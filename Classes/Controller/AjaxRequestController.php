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
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function initializeAction() {
		global $TYPO3_CONF_VARS;
		/** !!! IMPORTANT TO MAKE JSON WORK !!! */
		$TYPO3_CONF_VARS['FE']['debug'] = '0';
		/** define type used! */
		if ( $this->request->hasArgument('cObj') ) {
			$this->cObj = $this->contentRepository->findByUid($this->request->getArgument('cObj'));
		} else {
			$this->throwStatus(404, \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('404.no_cObj', $this->extensionName));
		}
		self::setStoragePid($this);
		$this->getFeSession()->setStorageKey('ext-' . $this->request->getControllerExtensionKey());
		\S3b0\Ecompc\Setup::setPriceHandling($this);
		// Add cObj-uid to configurationSessionStorageKey to make it unique in sessionStorage
		$this->configurationSessionStorageKey .= $this->cObj->getPid();
		// Get current configuration (Array: options=array(options)|packages=array(package => option(s)))
		$this->selectedConfiguration = $this->getFeSession()->get($this->configurationSessionStorageKey) ?: [
			'options' => [ ],
			'packages' => [ ]
		];
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
		 * selcps -> indicator for active packages used at JS calculation of progress
		 */
		$variablesToRender = [ 'controller', 'currentPackage', 'packages', 'options', 'hint', 'progress', 'showResult', 'configurationData', 'pricingEnabled', 'pricing', 'requestLink' ];
		if ( \TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext()->isDevelopment() ) {
			$variablesToRender[] = 'debug';
		}
		$this->view->setVariablesToRender($variablesToRender);
		// parent::initializeView();
		$this->view->assignMultiple([
			'controller' => $this->request->getControllerName(),
			'pricingEnabled' => $this->isPricingEnabled()
		]);
	}

	/**
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 */
	public function initializeGetOptionHintAction() {
		if ( $this->request->hasArgument('option') && !$this->request->getArgument('option') instanceof \S3b0\Ecompc\Domain\Model\Option ) {
			$this->request->setArgument(
				'option',
				\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($this->request->getArgument('option')) ? $this->optionRepository->findByUid($this->request->getArgument('option')) : NULL
			);
		}
	}

	/**
	 * action getOptionHint
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @return void
	 */
	public function getOptionHintAction(\S3b0\Ecompc\Domain\Model\Option $option = NULL) {
		$this->view->assign('hint', $this->configurationManager->getContentObject()->parseFunc($option->getHintText(), [ ], '< lib.parseFunc_RTE'));
	}

	/**
	 * action setOption
	 *
	 * @param int $option Option uid
	 * @param int $unset  set/Unset indicator
	 * @param int $vpn    Visible Packages Number
	 *
	 * @return void
	 */
	public function setOptionAction($option, $unset = 0, $vpn = 0) {
		/** @var \S3b0\Ecompc\Domain\Model\Option $option */
		$option = $this->optionRepository->findByUid($option);
		parent::setOptionAction($option, $unset);

		$this->forward('index');
	}

	/**
	 * action resetPackage
	 *
	 * @param int $package
	 *
	 * @deprecated May apply on multiple selects, once fully supported
	 * @return void
	 */
	public function resetPackageAction($package = 0) {
		$this->selectedConfiguration['options'] = array_diff((array) $this->selectedConfiguration['options'], $this->optionRepository->getPackageOptionUidList($this->packageRepository->findByUid($package)));
		unset($this->selectedConfiguration['packages'][$package]);
		$this->getFeSession()->store($this->configurationSessionStorageKey, $this->selectedConfiguration);

		$this->view->assignMultiple([
			'package' => $package,
			'cfgp' => $this->getUpdatedConfigurationPrice(),
			'selcps' => count((array) $this->selectedConfiguration['packages'])
		]);
	}

	/**
	 * @param float $floatToFormat
	 * @param bool  $signed
	 *
	 * @return string
	 */
	protected function formatCurrency($floatToFormat = 0.0, $signed = FALSE) {
		$output = number_format($floatToFormat, 2, $this->getCurrency()->isNumberSeparatorsInUSFormat() ? '.' : ',', $this->getCurrency()->isNumberSeparatorsInUSFormat() ? ',' : '.');
		// Add algebraic sign if positive
		if ( $floatToFormat > 0 && $signed ) {
			$output = '+' . $output;
		} elseif ( number_format($floatToFormat, 2) == 0.00 && $signed ) {
			$output = '+' . $output;
			/*return ExtbaseUtility\LocalizationUtility::translate('price.inclusive', 'ecompc');*/
		}
		if ( $this->getCurrency()->getSymbol() !== '' ) {
			$currencySeparator = $this->getCurrency()->isWhitespaceBetweenCurrencyAndValue()  ?: ' ';
			if ( $this->getCurrency()->isSymbolPrepended() ) {
				$output = $this->getCurrency()->getSymbol() . $currencySeparator . $output;
			} else {
				$output .= $currencySeparator . $this->getCurrency()->getSymbol();
			}
		}
		return $output;
	}

	/**
	 * renderTemplateView
	 * render a view in default template (TemplateView), no JSONView
	 *
	 * @param string $controllerActionName
	 * @param array  $arguments
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
		$view->assignMultiple([
			'action' => $controllerActionName ?: $this->request->getControllerActionName(), // current action
			'pid' => $GLOBALS['TSFE']->id,
			'cObj' => $this->cObj->getUid()
		]);
		if ( $this->isPricingEnabled() ) {
			$view->assignMultiple([
				'pricingEnabled' => $this->isPricingEnabled(), // checks whether price labels are displayed or not!
				'currency' => $this->getCurrency(), // fetch currency TS
				'pricing' => $this->getConfigurationPrice() // current configuration price
			]);
		}
		/** Assign Action specific templateContainerVariables committed as first method argument [ func_get_arg(0) ] */
		$view->assignMultiple($arguments);
		return preg_replace('/[\t\n\r]/i', '', $view->render($controllerActionName ?: $this->request->getControllerActionName()));
	}

	/**
	 * @param \S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController
	 */
	protected static function setStoragePid(\S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController) {
		$ajaxRequestController::setRepositoryStoragePidSettings($ajaxRequestController->packageRepository, $ajaxRequestController);
		$ajaxRequestController::setRepositoryStoragePidSettings($ajaxRequestController->optionRepository, $ajaxRequestController);
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\Repository $repository
	 * @param \S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 */
	protected static function setRepositoryStoragePidSettings(\TYPO3\CMS\Extbase\Persistence\Repository $repository, \S3b0\Ecompc\Controller\AjaxRequestController $ajaxRequestController) {
		// Set Query settings
		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
		$querySettings = $ajaxRequestController->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QuerySettingsInterface');
		$querySettings->setRespectStoragePage($ajaxRequestController->request->hasArgument('storagePid') || $ajaxRequestController->cObj->getStoragePidArray());
		if ( $ajaxRequestController->request->hasArgument('storagePid') ) {
			$querySettings->setStoragePageIds([ $ajaxRequestController->request->getArgument('storagePid') ]);
		} elseif ( $ajaxRequestController->cObj->getStoragePidArray() ) {
			$querySettings->setStoragePageIds($ajaxRequestController->cObj->getStoragePidArray());
		}
		$repository->setDefaultQuerySettings($querySettings);
	}

}