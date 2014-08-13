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
class AjaxRequestController extends StandardController {

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\\CMS\\Extbase\\Mvc\\View\\JsonView';

	/**
	 * action selectPackageOptions
	 *
	 * @param  integer $configurationPackage
	 * @return void
	 */
	public function selectPackageOptionsAction($configurationPackage = 0) {
		$this->setRepositoryStoragePidSettings($this->packageRepository);
		$configurationPackage = $this->packageRepository->findAll();
		$this->view->assign('value', array(
			'success' => TRUE,
			'pack' => $configurationPackage,
			'session' => $this->feSession->get('currency'),
			'debug' => $this->selectPackageOptionsRenderAction($configurationPackage->getFirst())
		));
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 *
	 * @return mixed
	 */
	public function selectPackageOptionsRenderAction(\S3b0\Ecompc\Domain\Model\Package $package) {
		/** @var \TYPO3\CMS\Fluid\View\TemplateView $view */
		$view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\TemplateView');
		$templateCompiler = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\Core\\Compiler\\TemplateCompiler');
		$view->injectTemplateCompiler($templateCompiler);
		$view->setControllerContext($this->controllerContext);
		$view->initializeView();
		$view->assign('package', $package);
		return $view->render($this->request->getControllerActionName());
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
		$querySettings->setRespectStoragePage($this->request->hasArgument('storagePid'));
		if ($this->request->hasArgument('storagePid')) {
			$querySettings->setStoragePageIds(array($this->request->getArgument('storagePid')));
		}
		$repository->setDefaultQuerySettings($querySettings);
	}

}