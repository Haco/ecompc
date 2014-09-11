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

	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * Override this method to solve tasks which all actions have in
	 * common.
	 *
	 * @return void
	 * @api
	 */
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
	 * Initializes the view before invoking an action method.
	 *
	 * Override this method to solve assign variables common for all actions
	 * or prepare the view in another way before the action is called.
	 *
	 * @return void
	 * @api
	 */
	public function initializeView() {
		/**
		 * cfgp   -> configuration price
		 * selcps -> indicator for selected packages used at JS calculation of progress
		 */
		$this->view->setVariablesToRender(array(
			'action', 'pid', 'L', 'cObj', 'showPriceLabels', 'currency', 'pricing', 'cfgp', 'cfgres', 'content', 'package', 'packagesLinksInnerHTML', 'selcps', 'proceed'
		));
		parent::initializeView();
		$this->view->assign('L', (int) $GLOBALS['TSFE']->sys_language_content);
	}

	/**
	 * action updatePackages
	 */
	public function updatePackagesAction() {
		$packageLinks = array();
		// Fetch packages
		if ($packages = $this->cObj->getEcompcPackagesFE()) {
			$isActive = FALSE;
			$prev = NULL;
			/** @var \S3b0\Ecompc\Domain\Model\Package $package */
			foreach (array_reverse($packages->toArray()) as $package) {
				if (!$isActive && array_key_exists($package->getUid(), $this->selectedConfiguration['packages'])) {
					$isActive = TRUE;
					if ($prev instanceof \S3b0\Ecompc\Domain\Model\Package) {
						$prev->setActive(TRUE);
						$packageLinks[$prev->getUid()] = array(
							$prev->isActive(),
							(bool) $prev->getSelectedOptions()->count(),
							$this->renderTemplateView('GetPackageLinkInnerHTML', array('package' => $prev))
						);
					}
				}
				$package->setActive($isActive);
				$packageLinks[$package->getUid()] = array(
					$package->isActive(),
					(bool) $package->getSelectedOptions()->count(),
					$this->renderTemplateView('GetPackageLinkInnerHTML', array('package' => $package))
				);
				/** @var \S3b0\Ecompc\Domain\Model\Package $prev */
				$prev = $package;
			}
		}

		$this->view->assignMultiple(array(
			'packagesLinksInnerHTML' => $packageLinks,
			'selcps' => count((array) $this->selectedConfiguration['packages']),
		));
		if (count((array) $this->selectedConfiguration['packages']) == $this->cObj->getEcompcPackagesFE()->count()) {
			$result = $this->getConfigurationResult(TRUE);
			$this->view->assign('cfgres', $this->renderTemplateView(
				'getResult',
				array(
					'configurationResult' => $result[0],
					'configurationSummary' => $result[1],
					'requestFormAdditionalParams' => $result[2]
				)
			));
		}
	}

	/**
	 * action GetPackageLinkInnerHTML
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @return void
	 */
	public function GetPackageLinkInnerHTMLAction(\S3b0\Ecompc\Domain\Model\Package $package) {
		$this->view->assign('package', $package);
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
		$this->view->assignMultiple(array(
			'content' => $this->renderTemplateView('', array('package' => $package, 'options' => $this->getPackageOptions($package), 'sys_language_uid' => $GLOBALS['TSFE']->sys_language_content)),
			'selcps' => count((array) $this->selectedConfiguration['packages'])
		));
	}

	/**
	 * action setOption
	 *
	 * @param integer $opt Option uid
	 * @param integer $uns set/Unset indicator
	 * @param integer $vpn Visible Packages Number
	 * @return void
	 */
	public function setOptionAction($opt, $uns = 0, $vpn = 0) {
		/** @var \S3b0\Ecompc\Domain\Model\Option $option */
		$option = $this->optionRepository->findByUid($opt);
		parent::setOptionAction($option, $uns);

		// Redirect; depends on multipleSelect or unset flag
		if ($option->getConfigurationPackage()->isMultipleSelect() || $uns) {
			$this->view->assignMultiple(array(
				'proceed' => 'selectPackageOptions',
				'package' => $option->getConfigurationPackage()->getUid()
			));
		} else {
			$this->view->assign('proceed', 'index');
		}
		// Fetch updated configuration
		$this->selectedConfiguration = $this->feSession->get($this->configurationSessionStorageKey);
		$this->view->assignMultiple(array(
			'cfgp' => $this->getUpdatedConfigurationPrice(),
			'selcps' => count((array) $this->selectedConfiguration['packages'])
		));
	}

	/**
	 * action resetPackage
	 *
	 * @param int $package
	 * @return void
	 */
	public function resetPackageAction($package = 0) {
		$this->selectedConfiguration['options'] = array_diff((array) $this->selectedConfiguration['options'], $this->optionRepository->getPackageOptionsUids($this->packageRepository->findByUid($package)));
		unset($this->selectedConfiguration['packages'][$package]);
		$this->feSession->store($this->configurationSessionStorageKey, $this->selectedConfiguration);

		$this->view->assignMultiple(array(
			'package' => $package,
			'cfgp' => $this->getUpdatedConfigurationPrice(),
			'selcps' => count((array) $this->selectedConfiguration['packages'])
		));
	}

	/**
	 * @return string
	 */
	protected function getUpdatedConfigurationPrice() {
		$this->initializeOptions();
		$floatToFormat = array_pop($this->getConfigurationPrice());
		if (empty($floatToFormat)) {
			$floatToFormat = 0.0;
		} else {
			$floatToFormat = floatval($floatToFormat);
		}
		return $this->formatCurrency($floatToFormat);
	}

	/**
	 * @param string                                     $controllerActionName
	 * @param array                                      $arguments
	 *
	 * @return string
	 */
	protected function renderTemplateView($controllerActionName = '', array $arguments) {
		/** @var \TYPO3\CMS\Fluid\View\TemplateView $view */
		$view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\TemplateView');
		/** @var \TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler $templateCompiler */
		$templateCompiler = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\Core\\Compiler\\TemplateCompiler');
		$view->injectTemplateCompiler($templateCompiler);
		$view->setControllerContext($this->controllerContext);
		/** imitate initializeView() from \S3b0\Ecompc\Controller\StandardController and assign global templateContainerVariables */
		$view->assignMultiple(array(
			'action' => $controllerActionName ?: $this->request->getControllerActionName(), // current action
			'instructions' => $this->cObj->getBodytext(), // short instructions for user
			'pid' => $GLOBALS['TSFE']->id,
			'cObj' => $this->cObj->getUid()
		));
		if ($this->showPriceLabels) {
			$view->assignMultiple(array(
				'showPriceLabels' => $this->showPriceLabels, // checks whether price labels are displayed or not!
				'currency' => $this->currency, // fetch currency TS
				'pricing' => $this->selectedConfigurationPrice // current configuration price
			));
		}
		/** Assign Action specific templateContainerVariables committed as first method argument [ func_get_arg(0) ] */
		$view->assignMultiple($arguments);
		return preg_replace('/[\t\n\r]/i', '', $view->render($controllerActionName ?: $this->request->getControllerActionName()));
	}

	/**
	 * @param \S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController
	 */
	protected static function updateStorage(\S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController) {
		$ajaxRequestController::setRepositoryStoragePidSettings($ajaxRequestController->packageRepository, $ajaxRequestController);
		$ajaxRequestController::setRepositoryStoragePidSettings($ajaxRequestController->optionRepository, $ajaxRequestController);
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\Repository $repository
	 * @param \S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 */
	private static function setRepositoryStoragePidSettings(\TYPO3\CMS\Extbase\Persistence\Repository $repository, \S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController) {
		// Set Query settings
		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
		$querySettings = $ajaxRequestController->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QuerySettingsInterface');
		$querySettings->setRespectStoragePage($ajaxRequestController->request->hasArgument('storagePid') || $ajaxRequestController->cObj->getStoragePidArray());
		if ($ajaxRequestController->request->hasArgument('storagePid')) {
			$querySettings->setStoragePageIds(array($ajaxRequestController->request->getArgument('storagePid')));
		} elseif ($ajaxRequestController->cObj->getStoragePidArray()) {
			$querySettings->setStoragePageIds($ajaxRequestController->cObj->getStoragePidArray());
		}
		$repository->setDefaultQuerySettings($querySettings);
	}

}