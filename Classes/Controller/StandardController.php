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
use TYPO3\CMS\Core\Utility as CoreUtility;
use TYPO3\CMS\Extbase\Utility as ExtbaseUtility;

/**
 * PackageController
 *
 * @todo re-configuration by releasing few dependencies… we´ll see
 * @package S3b0
 * @subpackage Ecompc
 */
class StandardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	CONST ttc_field_type = 'tx_ecompc_type';
	CONST ttc_field_packages = 'tx_ecompc_packages';
	CONST ttc_field_configurations = 'tx_ecompc_configurations';

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer|null
	 */
	protected $cObj = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage|null
	 */
	protected $selectedOptions = NULL;

	/**
	 * @var array
	 */
	protected $selectedConfiguration = array();

	/**
	 * @var array
	 */
	protected $selectedConfigurationPrice = array();

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|array|null
	 */
	protected $selectableConfigurations = NULL;

	/**
	 * @var string
	 */
	protected $configurationSessionStorageKey = 'ecompc-current-configuration';

	/**
	 * @var boolean
	 */
	protected $showPricing = FALSE;

	/**
	 * @var array
	 */
	protected $currencySetup = array();

	/**
	 * configurationRepository
	 *
	 * @var \S3b0\Ecompc\Domain\Repository\ConfigurationRepository
	 * @inject
	 */
	protected $configurationRepository;

	/**
	 * packageRepository
	 *
	 * @var \S3b0\Ecompc\Domain\Repository\PackageRepository
	 * @inject
	 */
	protected $packageRepository;

	/**
	 * optionRepository
	 *
	 * @var \S3b0\Ecompc\Domain\Repository\OptionRepository
	 * @inject
	 */
	protected $optionRepository;

	/**
	 * contentRepository
	 *
	 * @var \S3b0\Ecompc\Domain\Repository\ContentRepository
	 * @inject
	 */
	protected $contentRepository;

	/**
	 * @var \S3b0\Ecompc\Domain\Session\FrontendSessionHandler
	 * @inject
	 */
	protected $feSession;

	/**
	 * Constructs the controller.
	 */
	public function __construct() {
		parent::__construct();
		$this->selectedOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

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
		// Get Extension configuration (set @Extension Manager)
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ecompc']);
		// Get distributors frontend user groups (set @Extension Manager)
		$distFeUserGroups = CoreUtility\GeneralUtility::intExplode(',', $extConf['distFeUserGroup'], TRUE);
		// Set price flag (displays pricing if true)
		$this->showPricing = (is_array($GLOBALS['TSFE']->fe_user->groupData['uid'])) && count(array_intersect($distFeUserGroups, $GLOBALS['TSFE']->fe_user->groupData['uid'])) ?: FALSE;
		// Fetch content object (tt_content)
		$this->cObj = $this->configurationManager->getContentObject();
		// Frontend-Session
		$this->feSession->setStorageKey($this->request->getControllerExtensionName() . $this->request->getPluginName());
		// Add cObj-uid to configurationSessionStorageKey to make it unique in sessionStorage
		$this->configurationSessionStorageKey .= '-c' . $this->cObj->data['uid'];
		// Get current configuration (Array: keys=package|values=option)
		$this->selectedConfiguration = $this->feSession->get($this->configurationSessionStorageKey) ?: array();
		// Get selected options
		$this->setSelectedOptions($this->selectedOptions, $this->selectedConfiguration);
		// Set selectable configurations
		$this->setSelectableConfigurations($this->selectableConfigurations);
		// Fetch currency configuration from TS
		$this->currencySetup = $this->settings['currency'][$this->feSession->get('currency')];
		// Get configuration price
		$this->selectedConfigurationPrice = $this->showPricing ? $this->getConfigurationPrice() : array();
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
		$this->view->assignMultiple(array(
			'sp' => $this->showPricing, // checks whether prices are displayed or not!
			'action' => $this->request->getControllerActionName(), // current action
			'instructions' => $this->cObj->getFieldVal('bodytext'), // short instructions for user
			'currencySetup' => $this->currencySetup, // fetch currency TS
			'pricing' => $this->selectedConfigurationPrice // current configuration price
		));
	}

	/**
	 * action index
	 *
	 * @param  string $currency
	 * @return void
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function indexAction($currency = NULL) {
		// Avoid duplicate cObjects containing current plugin
		if ($this->contentRepository->findDuplicates()->count() > 1)
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.dbl_config_found', $this->extensionName));

		// Failure if no configuration has been found
		if (!CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal(self::ttc_field_configurations)))
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.no_config_found', $this->extensionName) . ' [ttc:#' . $this->cObj->getFieldVal('uid') . '@pid:#' . $this->cObj->getFieldVal('pid') . ']');

		// Check for currency when distributor is logged in
		if ($this->showPricing) {
			if (!$this->feSession->get('currency') && !$currency && $this->checkForCurrencies($this->settings) === TRUE) {
				$this->redirect('selectRegion'); // Add redirect for region selection - influencing currency display
			} elseif (!$this->feSession->get('currency') && $currency && array_key_exists($currency, (array) $this->settings['currency'])) {
				$this->feSession->store('currency', $currency); // Store region selection
				$this->currencySetup = $this->settings['currency'][$currency];
				$this->view->assign('currencySetup', $this->currencySetup);
			}
		}

		$process = 0;
		// Fetch packages
		if ($packages = $this->contentRepository->findByUid($this->cObj->data['uid'])->getEcompcPackages()) {
			$visiblePackages = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
			$isActive = TRUE;
			foreach ($packages as $package) {
				if ($package->isVisibleInFrontend()) {
					$package->setActive($isActive);
					$visiblePackages->attach($package);
					$isActive = array_key_exists($package->getUid(), $this->selectedConfiguration['packages']);
				}
				$selectedOptions = array_key_exists($package->getUid(), $this->selectedConfiguration['packages']) ? $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['packages'][$package->getUid()]) : NULL;
				$package->setSelectedOptions($selectedOptions);
			}
			// Get process state update (ratio of selected to visible packages) => float from 0 to 1 (*100 = %)
			$process = count((array) $this->selectedConfiguration['packages']) / $visiblePackages->count();
		}

		if ($process === 1)
			$this->view->assign('configurationResult', $this->getConfigurationResult()); // Get configuration code | SKU

		$this->view->assign('packages', $packages);
		$this->view->assign('process', $process);
	}

	/**
	 * action selectPackageOptions
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @return void
	 */
	public function selectPackageOptionsAction(\S3b0\Ecompc\Domain\Model\Package $package) {
		$this->view->assign('package', $package);
		$this->view->assign('options', $this->getPackageOptions($package));
	}

	/**
	 * action setOption
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @param int                              $unset           Identifier to unset option!
	 * @param int                              $redirectAction
	 * @return void
	 */
	public function setOptionAction(\S3b0\Ecompc\Domain\Model\Option $option, $unset = 0, $redirectAction = 0) {
		$configuration = $this->selectedConfiguration;
		$redirect = array(
			array('index', NULL, NULL, array()),
			array('selectPackageOptions', NULL, NULL, array('package' => $option->getConfigurationPackage()))
		);

		// Modify (options of) package that already EXISTS
		if (array_key_exists($option->getConfigurationPackage()->getUid(), (array) $configuration['packages'])) {
			// For packages NOT supporting multipleSelect unset current selection
			if (!$option->getConfigurationPackage()->isMultipleSelect()) {
				// Remove other (package) options from selectedOptions
				if ($packageOptions = $this->optionRepository->findByConfigurationPackage($option->getConfigurationPackage())) {
					foreach ($packageOptions as $singlePackageOption) {
						if ($this->selectedOptions->contains($singlePackageOption)) $this->selectedOptions->detach($singlePackageOption);
					}
				}
				// Remove all options + package from configuration array
				$configuration['options'] = array_diff((array) $configuration['options'], $this->optionRepository->getPackageOptionsUidList($option->getConfigurationPackage()));
				unset($configuration['packages'][$option->getConfigurationPackage()->getUid()]);
			}
			// Remove option from configuration array
			if ($unset) {
				$configuration['options'] = array_diff((array) $configuration['options'], [$option->getUid()]);
				if ($option->getConfigurationPackage()->isMultipleSelect()) {
					$configuration['packages'][$option->getConfigurationPackage()->getUid()] = array_diff((array) $configuration['packages'][$option->getConfigurationPackage()->getUid()], [$option->getUid()]);
					if (count($configuration['packages'][$option->getConfigurationPackage()->getUid()]) === 0)
						unset($configuration['packages'][$option->getConfigurationPackage()->getUid()]);
				}
			} else {
				// Add options to package selection
				if (!in_array($option->getUid(), (array) $configuration['options'])) {
					$configuration['options'][] = $option->getUid();
					$configuration['packages'][$option->getConfigurationPackage()->getUid()][] = $option->getUid();
				}
			}
		} else {
			if (!$unset) {
				// Add options to NEW package
				$configuration['options'][] = $option->getUid();
				$configuration['packages'][$option->getConfigurationPackage()->getUid()][] = $option->getUid();
			}
		}

		// Add selected option to selection
		$unset ? $this->selectedOptions->detach($option) : $this->selectedOptions->attach($option);
		// Run the dependency check if other packages next to current got options chosen
		foreach ($this->selectedOptions as $selectedOption) {
			if (!$this->checkOptionDependencies($selectedOption, $configuration)) {
				// if ($this->selectedOptions->contains($selectedOption)) $this->selectedOptions->detach($selectedOption); // not necessary in this place since ObjectStorage will be refilled after redirect leaving $selectedOption as of session configuration. Just listed to show regular complete process.
				$configuration['options'] = array_diff($configuration['options'], $option->getConfigurationPackage()->isMultipleSelect() ? $this->optionRepository->getPackageOptionsUidList($selectedOption->getConfigurationPackage()) : [$selectedOption->getUid()]);
				unset($configuration['packages'][$selectedOption->getConfigurationPackage()->getUid()]);
			}
		}

#		// Prepared autoFill. Still some issues, especially regarding traversing as of dependency checks!
#		if ($this->settings['auto_set'])
#			$this->autoSetOptions($configuration);
		$this->feSession->store($this->configurationSessionStorageKey, $configuration); // Store configuration in fe_session_data
		list($actionName, $controllerName, $extensionName, $arguments) = $redirect[$unset ?: $redirectAction]; // Set params for $this->redirect() method
		$this->redirect($actionName, $controllerName, $extensionName, $arguments);
	}

	/**
	 * action reset
	 * Resets the configuration!
	 *
	 * @return void
	 */
	public function resetAction() {
		$this->feSession->store($this->configurationSessionStorageKey, array());
		$this->redirect('index');
	}

	/**
	 * action selectRegion
	 *
	 * @return void
	 */
	public function selectRegionAction() {
		$this->view->assign('currencies', $this->checkForCurrencies($this->settings, TRUE));
	}

	/**********************************
	 ******** NON-ACTION METHODS ******
	 **********************************/

	/**
	 * Checking currencies set by TS
	 *
	 * @param array   $settings
	 * @param boolean $returnFiltered
	 *
	 * @internal
	 * @return boolean|array
	 */
	final public static function checkForCurrencies(array $settings, $returnFiltered = FALSE) {
		if (!array_key_exists('currency', $settings))
			return FALSE;

		$requiredFieldsPattern = array(
			'short' => 1,
			'symbol' => 1,
			'region' => 1,
			'flagIcon' => 1
		);

		$processCurrencies = $returnCurrencies = $settings['currency'];
		foreach ($processCurrencies as $k => $currency) {
			// Add key for identification to currencies.
			$returnCurrencies[$k]['key'] = $k;
			// Drop elements not matching required fields
			$required = array_intersect_key($currency, $requiredFieldsPattern);
			if (count($required) !== count($requiredFieldsPattern) || $k === 'default') {
				unset($processCurrencies[$k]);
				if ($k !== 'default')
					unset($returnCurrencies[$k]);
				continue;
			}
		}

		return $returnFiltered && count($returnCurrencies) ? $returnCurrencies : boolval(count($processCurrencies));
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array|null
	 */
	public function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package) {
		// Fetch selectable options for current package
		$this->getSelectableOptions($package, $processOptions);
		if (!$processOptions)
			return NULL;

		// Fetch selected options for current package
		$selectedOptions = NULL;
		if (array_key_exists($package->getUid(), (array) $this->selectedConfiguration['packages'])) {
			$selectedOptions = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['packages'][$package->getUid()]);
		}

		// Include pricing for enabled users!
		if ($this->showPricing) {
			foreach ($processOptions as $option) {
				if ($package->isPercentPricing()) {
					// Calculate percental pricing [ONLY working on packages WITHOUT multipleSelect() flag set]
					if (array_key_exists($package->getUid(), (array) $this->selectedConfiguration['packages']) && !($selectedOptions && in_array($option, $selectedOptions->toArray()))) {
						$selectedOption = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['packages'][$package->getUid()])->getFirst();
						$configurationPrice = floatval($this->selectedConfigurationPrice[1] / ($selectedOption->getPricePercental() + 1));
						$selectedOptionPrice = floatval($this->selectedConfigurationPrice[1] - $configurationPrice);
						$option->setPriceOutput(floatval($configurationPrice * $option->getPricePercental() - $selectedOptionPrice));
					} else {
						$option->setPriceOutput($selectedOptions && in_array($option, $selectedOptions->toArray()) ? 0.00 : floatval($this->selectedConfigurationPrice[1] * $option->getPricePercental()));
					}
				} else {
					$priceOutput = $package->isMultipleSelect() || !$selectedOptions ? $option->getPriceInCurrency($this->feSession->get('currency'), floatval($this->currencySetup['exchange'])) : $option->getPriceInCurrency($this->feSession->get('currency')) - $selectedOptions->getFirst()->getPriceInCurrency($this->feSession->get('currency'), floatval($this->currencySetup['exchange']));
					$option->setPriceOutput($selectedOptions && in_array($option, $selectedOptions->toArray()) ? 0.00 : $priceOutput);
				}
			}
		}

		return $processOptions;
	}

	/**
	 * Fetching options selectable which may be either all package options in case of dynamic configurations or limited by selected options at SKU-based configurations
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @param array                             $result
	 */
	protected function getSelectableOptions(\S3b0\Ecompc\Domain\Model\Package $package, array &$result) {
		$selectableOptions = $this->optionRepository->findByConfigurationPackage($package)->toArray(); // Set basic settings

		// Parse configurations, if any | @case SKU (default)
		if (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal('tx_ecompc_type')) !== 1 && $this->selectableConfigurations) {
			$selectableOptions = array();
			foreach ($this->selectableConfigurations as $configuration) {
				if ($configurationOptions = $configuration->getOptions()) {
					foreach ($configurationOptions as $configurationOption) {
						if ($configurationOption->getConfigurationPackage() === $package)
							$selectableOptions[] = $configurationOption;
					}
				}
			}
			$selectableOptions = array_unique($selectableOptions);
		}

		// Run dependency check
		$result = array();
		foreach ($selectableOptions as $option) {
			if ($this->checkOptionDependencies($option, $this->selectedConfiguration)) {
				if ($this->selectedOptions && in_array($option, $this->selectedOptions->toArray()))
					$option->setSelected(TRUE);
				$result[] = $option;
			}
		}
	}

	/**
	 * Checks an option against its dependencies. Returns true if option IS NOT prohibited!
	 *
	 * @param  \S3b0\Ecompc\Domain\Model\Option $option
	 * @param  array                            $configuration
	 * @return boolean
	 */
	public function checkOptionDependencies(\S3b0\Ecompc\Domain\Model\Option $option, array $configuration) {
		if (!$option->getDependency()) return TRUE;
		$check = TRUE;

		if ($dependency = $option->getDependency()) {
			if ($packages = $option->getDependency()->getPackages()) {
				$checkAgainstArray = array();
				foreach ($packages as $package) {
					if ($selectedOptions = $this->optionRepository->findOptionsByUidList($configuration['packages'][$package->getUid()])) {
						foreach ($selectedOptions as $selectedOption) {
							// @modes {0 : explicit deny, 1 : explicit allow}
							$checkAgainstArray[] = $dependency->getMode() === 0 ? !$dependency->getPackageOptions($package)->contains($selectedOption) : $dependency->getPackageOptions($package)->contains($selectedOption);
						}
					}
				}
				$check = !in_array(FALSE, $checkAgainstArray);
			}
		}

		return $check;
	}

	/**
	 * autoSetOptions
	 *
	 * @param array $configuration
	 * @return void
	 */
	public function autoSetOptions(array &$configuration) {
		if ($packages = $this->contentRepository->findByUid($this->cObj->data['uid'])->getEcompcPackages()) {
			foreach ($packages as $package) {
				if (array_key_exists($package->getUid(), (array) $configuration['packages']))
					continue;

				if ($packageOptions = $this->getPackageOptions($package)) {
					if (count($packageOptions) === 1) {
						// Add option to NEW package
						$configuration['options'][] = $packageOptions[0]->getUid();
						$configuration['packages'][$package->getUid()][] = $packageOptions[0]->getUid();
						$this->selectedOptions->attach($packageOptions[0]);
					}
				}
			}
		}
	}

	/**
	 * Fetching selectable configurations
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|array|null $current actual setting
	 * @return void
	 */
	protected function setSelectableConfigurations(&$current = NULL) {
		$current = $current ?: $this->configurationRepository->findByTtContentUid($this->cObj->getFieldVal('uid'));

		// Overwrite for SKU-based configurations
		if (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal(self::ttc_field_type)) !== 1) {
			$current = $this->configurationRepository->findByTtContentUidApplyingSelectedOptions($this->cObj->getFieldVal('uid'), $this->selectedOptions);
		}
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage
	 * @param array $selectedConfiguration
	 * @return void
	 */
	protected function setSelectedOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage &$objectStorage, array $selectedConfiguration) {
		if (count($selectedConfiguration['options'])) {
			$objectStorage->removeAll($objectStorage);
			if ($options = $this->optionRepository->findOptionsByUidList($selectedConfiguration['options'])) {
				foreach ($options as $option)
					$objectStorage->attach($option);
			}
		}
	}

	/**
	 * Calculates the price for configurations already during configuration process
	 *
	 * @return array
	 */
	public function getConfigurationPrice() {
		$cObj = $this->contentRepository->findByUid($this->cObj->data['uid']);

		$base = $cObj->getEcompcBasePrice();
		if ($this->feSession->get('currency') && $this->feSession->get('currency') !== 'default') {
			$priceList = $cObj->getEcompcBasePriceList();
			$base = floatval($priceList[$this->feSession->get('currency')]['vDEF']);
			if (!$base && $this->currencySetup['exchange']) {
				$base = $cObj->getEcompcBasePrice() * floatval($this->currencySetup['exchange']);
			}
		}

		// Get configuration price
		$config = $base;
		if ($this->selectedOptions) {
			foreach ($this->selectedOptions as $option) {
				if ($option->getConfigurationPackage()->isPercentPricing()) {
					$price = floatval($config * $option->getPricePercental());
				} else {
					$price = $option->getPriceInCurrency($this->feSession->get('currency'), floatval($this->currencySetup['exchange']));
				}
				$config += $price;
			}
		}

		return array($base, $config);
	}

	/**
	 * @return string
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function getConfigurationResult() {
		switch (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal(self::ttc_field_type))) {
			// In case of dynamic configurations get first configuration record
			case 1:
				return $this->buildConfigurationCode($this->selectableConfigurations->getFirst());
				break;
			// Otherwise fetch all configuration records
			default:
				if ($this->selectableConfigurations->count() !== 1) {
					$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.no_unique_sku_found', $this->extensionName));
				} else {
					$this->view->assignMultiple(array(
						'requestFormAdditionalParams' => json_decode(
							sprintf(
								$this->settings['requestForm']['addParam'],
								$this->selectableConfigurations->getFirst()->getSku(),
								$this->selectableConfigurations->getFirst()->getFrontendLabel()
							),
							TRUE
						),
						'configurationResultLabel' => $this->selectableConfigurations->getFirst()->getFrontendLabel()
					));
					return $this->selectableConfigurations->getFirst()->getSku();
				}
		}
	}

	/**
	 * set configuration code
	 *
	 * @param  \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 * @return string
	 */
	public function buildConfigurationCode(\S3b0\Ecompc\Domain\Model\Configuration $configuration) {
		$wrapper = $configuration->getConfigurationCodePrefix() . '%s' . $configuration->getConfigurationCodeSuffix();
		$segmentWrapper = '<span class="ecompc-syntax-help" title="%1$s">%2$s</span>';

		$code = '';
		$plain = '';

		foreach ($this->contentRepository->findByUid($this->cObj->data['uid'])->getEcompcPackages() as $package) {
			if (!$package->isVisibleInFrontend()) {
				$code .= sprintf($segmentWrapper, $package->getFrontendLabel(), $package->getDefaultOption()->getConfigurationCodeSegment());
				$plain .= $package->getDefaultOption()->getConfigurationCodeSegment();
			} elseif ($options = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['packages'][$package->getUid()])) {
				foreach ($options as $option) {
					$code .= sprintf($segmentWrapper, $option->getConfigurationPackage()->getFrontendLabel(), $option->getConfigurationCodeSegment());
					$plain .= $option->getConfigurationCodeSegment();
				}
			}
		}

		$this->view->assign('requestFormAdditionalParams', json_decode(
			sprintf(
				$this->settings['requestForm']['addParam'],
				sprintf($wrapper, $plain),
				$configuration->getFrontendLabel()
			),
			TRUE
		));

		return sprintf($wrapper, $code);
	}

}