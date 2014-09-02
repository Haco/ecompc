<?php
namespace S3b0\Ecompc\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Sebastian Iffland <sebastian.iffland@ecom-ex.com>, ecom instruments GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * AjaxRequestController
 *
 * @todo remove indexAction and corresponding Templates if not used!
 * @package S3b0
 * @subpackage Ecompc
 */
class AjaxRequestController extends \S3b0\Ecompc\Controller\StandardController {

	/** @var \TYPO3\CMS\Extbase\Mvc\View\JsonView $view */
	protected $view;

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\\CMS\\Extbase\\Mvc\\View\\JsonView';

	public function initializeAction() {
		if ($this->request->hasArgument('cObj')) {
			$this->cObj = $this->contentRepository->findByUid($this->request->getArgument('cObj'));
		} else {
			$this->throwStatus(404, \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('404.no_cObj', $this->extensionName));
		}
		self::updateStorage($this);
		parent::initializeAction();
	}

	/**
	 * @todo remove dev log before GoingLive!
	 * initialize view
	 */
	public function initializeView() {
		$this->view->setVariablesToRender(array('dev', 'priceLabels', 'action', 'currency', 'pricing', 'cObj', 'pid', 'content', 'debug', 'selectedCPkg'));
		/** @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication $beUserAuth */
		$beUserAuth = $this->objectManager->get('TYPO3\\CMS\\Core\\Authentication\\BackendUserAuthentication');
		$beUserAuth->start();
		$this->view->assign('dev', $beUserAuth->isAdmin());
		parent::initializeView();
	}

	/**
	 * action index
	 */
	public function indexAction() {
		$templateVariableContainer = array();
		$process = 0;
		// Fetch packages
		if ($packages = $this->cObj->getEcompcPackages()) {
			$visiblePackages = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
			$isActive = TRUE;
			/** @var \S3b0\Ecompc\Domain\Model\Package $package */
			foreach ($packages as $package) {
				if ($package->isVisibleInFrontend()) {
					$package->setActive($isActive);
					$visiblePackages->attach($package);
					$isActive = array_key_exists($package->getUid(), $this->selectedConfiguration['packages']);
				}
			}
			// Get process state update (ratio of selected to visible packages) => float from 0 to 1 (*100 = %)
			$process = count((array) $this->selectedConfiguration['packages']) / $visiblePackages->count();
		}

		if ($process === 1)
			$templateVariableContainer['configurationResult'] = $this->getConfigurationResult(); // Get configuration code | SKU

		$templateVariableContainer['packages'] = $packages;
		$templateVariableContainer['process'] = $process;

		$this->view->assign('content', $this->renderTemplateView($templateVariableContainer));
	}

	/**
	 * action selectPackageOptions
	 *
	 * @param  integer $configurationPackage
	 * @return void
	 */
	public function selectPackageOptionsAction($configurationPackage) {
		/** @var \S3b0\Ecompc\Domain\Model\Package $package */
		$package = $this->packageRepository->findByUid($configurationPackage);
		$this->view->assign('content', $this->renderTemplateView(array('package' => $package, 'options' => $this->getPackageOptions($package))));
		$this->view->assign('selectedCPkg', count((array) $this->selectedConfiguration['packages']));
	}

	/**
	 * action setOption
	 *
	 * @param integer $option
	 * @param integer $unset
	 * @param integer $redirect
	 * @return void
	 */
	public function setOptionAction($option, $unset = 0, $redirect = 0) {
		/** @var \S3b0\Ecompc\Domain\Model\Option $option */
		$option = $this->optionRepository->findByUid($option);
		parent::setOptionAction($option, $unset);

		// @todo check options set, if finished fetch result!
		$this->view->assign('content', '');
		$this->selectedConfiguration = $this->feSession->get($this->configurationSessionStorageKey);
		$this->view->assign('selectedCPkg', count((array) $this->selectedConfiguration['packages']));
		#list($actionName, $controllerName, $extensionName, $arguments) = $redirect[$unset ?: $redirectAction]; // Set params for $this->redirect() method
		#$this->redirect($actionName, $controllerName, $extensionName, $arguments);
	}

	/**
	 * @return mixed
	 */
	public function renderTemplateView() {
		/** @var \TYPO3\CMS\Fluid\View\TemplateView $view */
		$view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\TemplateView');
		/** @var \TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler $templateCompiler */
		$templateCompiler = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\Core\\Compiler\\TemplateCompiler');
		$view->injectTemplateCompiler($templateCompiler);
		$view->setControllerContext($this->controllerContext);
		/** imitate initializeView() from \S3b0\Ecompc\Controller\StandardController and assign global templateContainerVariables */
		$view->assignMultiple(array(
			'priceLabels' => $this->showPriceLabels, // checks whether price labels are displayed or not!
			'action' => $this->request->getControllerActionName(), // current action
			'instructions' => $this->cObj->getBodytext(), // short instructions for user
			'currency' => $this->currency, // fetch currency TS
			'pricing' => $this->selectedConfigurationPrice, // current configuration price
			'cObj' => $this->cObj->getUid(),
			'pid' => $GLOBALS['TSFE']->id
		));
		/** Assign Action specific templateContainerVariables committed as first method argument [ func_get_arg(0) ] */
		$view->assignMultiple(func_get_arg(0));
		return $view->render($this->request->getControllerActionName());
	}

	public static function updateStorage(\S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController) {
		$ajaxRequestController->setRepositoryStoragePidSettings($ajaxRequestController->packageRepository);
		$ajaxRequestController->setRepositoryStoragePidSettings($ajaxRequestController->optionRepository);
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\Repository $repository
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 */
	public function setRepositoryStoragePidSettings(\TYPO3\CMS\Extbase\Persistence\Repository $repository) {
		// Set Query settings
		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QuerySettingsInterface');
		$querySettings->setRespectStoragePage($this->request->hasArgument('storagePid') || $this->cObj->getStoragePidArray());
		if ($this->request->hasArgument('storagePid')) {
			$querySettings->setStoragePageIds(array($this->request->getArgument('storagePid')));
		} elseif ($this->cObj->getStoragePidArray()) {
			$querySettings->setStoragePageIds($this->cObj->getStoragePidArray());
		}
		$repository->setDefaultQuerySettings($querySettings);
	}

}