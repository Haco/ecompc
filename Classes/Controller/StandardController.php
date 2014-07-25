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
 * @package S3b0
 * @subpackage Ecompc
 */
class StandardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var null
	 */
	protected $cObj = null;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	protected $selectedOptions = null;

	/**
	 * @var array
	 */
	protected $selectedConfiguration = array();

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|array|null
	 */
	protected $selectableConfigurations = null;

	protected $configurationSessionStorageKey = 'ecompc-current-configuration';
	protected $showPricing = false;

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
	 * Frontend- or Backend-Session
	 * The type of session is set automatically in initializeAction().
	 *
	 * @var \S3b0\Ecompc\Domain\Session\SessionHandler
	 */
	protected $session;

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
		$distFeUserGroups = CoreUtility\GeneralUtility::intExplode(',', $extConf['distFeUserGroup'], true);
		// Set price flag (displays pricing if true)
		$this->showPricing = ($GLOBALS['TSFE']->fe_user->groupData['uid'] instanceof \ArrayAccess || is_array($GLOBALS['TSFE']->fe_user->groupData['uid'])) && count(array_intersect($distFeUserGroups, $GLOBALS['TSFE']->fe_user->groupData['uid'])) ?: false;

		// Fetch content object (tt_content)
		$this->cObj = $this->configurationManager->getContentObject();

		// Frontend-Session
		$this->feSession->setStorageKey($this->request->getControllerExtensionName() . $this->request->getPluginName());
		// Add cObj-uid to configurationSessionStorageKey to make it unique in sessionStorage
		$this->configurationSessionStorageKey .= $this->cObj->data['uid'];
		// Get current configuration (Array: keys=package|values=option)
		$this->selectedConfiguration = $this->feSession->get($this->configurationSessionStorageKey) ?: array();
		// Get selected options
		$this->setSelectedOptions($this->selectedOptions, $this->selectedConfiguration);
		// Set selectable configurations
		$this->setSelectableConfigurations($this->selectableConfigurations);
	}

	/**
	 * Initializes the view before invoking an action method.
	 *
	 * Override this method to solve assign variables common for all actions
	 * or prepare the view in another way before the action is called.
	 *
	 * @todo check templates/partials for used variables/filter unused
	 * @return void
	 * @api
	 */
	public function initializeView() {
		$this->view->assignMultiple(array(
			'sp' => $this->showPricing, // checks whether prices are displayed or not!
			'action' => $this->request->getControllerActionName(), // current action
			'instructions' => $this->cObj->getFieldVal('bodytext'), // short instructions for user
			'currencySetup' => $this->settings['currency'][$this->feSession->get('currency')], // fetch currency TS
			'pricing' => $this->showPricing ? $this->getConfigurationPrice() : null // current configuration price
		));
	}

	/**
	 * action index
	 *
	 * @todo stepwise configuration!
	 * @param  string $currency
	 * @return void
	 */
	public function indexAction($currency = null) {
		// Avoid duplicate cObjects containing current plugin
		if ($this->contentRepository->findDuplicates()->count() > 1)
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.dbl_config_found', $this->extensionName));

		// Failure if no configuration has been found
		if (!CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal('tx_ecompc_configurations')))
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.no_config_found', $this->extensionName) . ' [ttc:#' . $this->cObj->getFieldVal('uid') . '@pid:#' . $this->cObj->getFieldVal('pid') . ']');

		// Check for currency when distributor is logged in
		if ($this->showPricing) {
			if (!$this->feSession->get('currency') && !$currency && $this->checkForCurrencies($this->settings) === true) {
				$this->redirect('selectRegion'); // Add redirect for region selection - influencing currency display
			} elseif (!$this->feSession->get('currency') && $currency && array_key_exists($currency, (array) $this->settings['currency'])) {
				$this->feSession->store('currency', $currency); // Store region selection
				$this->initializeView(); // Recall initializeView() to set currency (session var) depending values/arrays
			}
		}

		// Fetch packages
		$list = CoreUtility\GeneralUtility::intExplode(',', $this->cObj->getFieldVal('tx_ecompc_packages'), TRUE);
		$packages = $this->packageRepository->findByUidList($list);
		if ($packages) {
			$setActive = true;
			foreach ($packages as $package) {
				if ($package->isVisibleInFrontend()) {
					$package->setActive($setActive);
					$setActive = array_key_exists($package->getUid(), $this->selectedConfiguration['packages']);
				}
				$selectedOptions = array_key_exists($package->getUid(), $this->selectedConfiguration['packages']) ? $this->optionRepository->findByUidList($this->selectedConfiguration['packages'][$package->getUid()]) : null;
				$package->setSelectedOptions($selectedOptions);
			}
		}

		// Get process / config code
		$printable = $this->packageRepository->findPrintableByUidList($list);
		$process = count((array) $this->selectedConfiguration['packages']) / count($printable);
		if ($process === 1) {
			$this->view->assign('configurationResult', $this->getConfigurationResult());
		}

		$this->view->assignMultiple(array(
			'packages' => $printable,
			'process' => $process
		));
	}

	/**
	 * action selectPackageOptions
	 *
	 * @todo add note for dependent options if no dependency is set!
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @return void
	 */
	public function selectPackageOptionsAction(\S3b0\Ecompc\Domain\Model\Package $package) {
		$this->view->assignMultiple(array(
			'package' => $package,
			'options' => $this->getPackageOptions($package)
		));
	}

	/**
	 * action setOption
	 *
	 * @todo  preselect packages if only one option possible!
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @param int                              $redirectAction
	 * @return void
	 */
	public function setOptionAction(\S3b0\Ecompc\Domain\Model\Option $option, $redirectAction = 0) {
		$configuration = $this->selectedConfiguration;
		$redirect = array(
			array('index', null, null, array()),
			array('selectPackageOptions', null, null, array('package' => $option->getConfigurationPackage()))
		);

		// Modify (options of) package that already EXISTS
		if (array_key_exists($option->getConfigurationPackage()->getUid(), (array) $configuration['packages'])) {
			// For those packages NOT supporting multipleSelect mode unset current corresponding selection
			if (!$option->getConfigurationPackage()->isMultipleSelect()) {
				// Remove other (package) options from selectedOptions
				if ($packageOptions = $this->optionRepository->findByConfigurationPackage($option->getConfigurationPackage())) {
					foreach ($packageOptions as $singlePackageOption) {
						if ($this->selectedOptions->contains($singlePackageOption)) $this->selectedOptions->detach($singlePackageOption);
					}
				}
				// Remove other (package) options + remove package from configuration array
				$configuration['options'] = array_diff($configuration['options'], $this->optionRepository->getPackageOptionsUidList($option->getConfigurationPackage()));
				unset($configuration['packages'][$option->getConfigurationPackage()->getUid()]);
			}
			// Add options to package selection
			if (!in_array($option->getUid(), (array) $configuration['options'])) {
				$configuration['options'][] = $option->getUid();
				$configuration['packages'][$option->getConfigurationPackage()->getUid()][] = $option->getUid();
			}
		} else {
			// Add options to NEW package
			$configuration['options'][] = $option->getUid();
			$configuration['packages'][$option->getConfigurationPackage()->getUid()][] = $option->getUid();
		}

		// Add selected option to selection
		$this->selectedOptions->attach($option);
		// Run the dependency check if other packages next to current got options chosen
		foreach ($this->selectedOptions as $selectedOption) {
			if (!$this->checkOptionDependencies($selectedOption, $configuration)) {
				// if ($this->selectedOptions->contains($selectedOption)) $this->selectedOptions->detach($selectedOption); // not necessary in this place since ObjectStorage will be refilled after redirect leaving $selectedOption as of session configuration. Just listed to show regular complete process.
				$configuration['options'] = array_diff($configuration['options'], $option->getConfigurationPackage()->isMultipleSelect() ? $this->optionRepository->getPackageOptionsUidList($selectedOption->getConfigurationPackage()) : [$selectedOption->getUid()]);
				unset($configuration['packages'][$selectedOption->getConfigurationPackage()->getUid()]);
			}
		}

		$this->feSession->store($this->configurationSessionStorageKey, $configuration); // Store configuration in fe_session_data
		list($actionName, $controllerName, $extensionName, $arguments) = $redirect[$redirectAction]; // Set params for $this->redirect() method
		$this->redirect($actionName, $controllerName, $extensionName, $arguments);
	}

	/**
	 * action unsetOption
	 *
	 * @todo implement in templates and finish!
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @param int                              $redirectAction
	 * @return void
	 */
	public function unsetOptionAction(\S3b0\Ecompc\Domain\Model\Option $option, $redirectAction = 0) {
		$configuration = $this->selectedConfiguration;
		$redirect = array(
			array('index', null, null, array()),
			array('selectPackageOptions', null, null, array('package' => $option->getConfigurationPackage()))
		);

		// Remove other (package) options from selectedOptions
		if ($packageOptions = $this->optionRepository->findByConfigurationPackage($option->getConfigurationPackage())) {
			foreach ($packageOptions as $singlePackageOption) {
				if ($this->selectedOptions->contains($singlePackageOption)) $this->selectedOptions->detach($singlePackageOption);
			}
		}
		// Remove other (package) options + remove package from configuration array
		$configuration['options'] = array_diff($configuration['options'], $this->optionRepository->getPackageOptionsUidList($option->getConfigurationPackage()));
		unset($configuration['packages'][$option->getConfigurationPackage()->getUid()]);

		// Add selected option to selection
		$this->selectedOptions->detach($option);
		// Run the dependency check if other packages next to current got options chosen
		foreach ($this->selectedOptions as $selectedOption) {
			if (!$this->checkOptionDependencies($selectedOption, $configuration)) {
				// if ($this->selectedOptions->contains($selectedOption)) $this->selectedOptions->detach($selectedOption); // not necessary in this place since ObjectStorage will be refilled after redirect leaving $selectedOption as of session configuration. Just listed to show regular complete process.
				$configuration['options'] = array_diff($configuration['options'], $option->getConfigurationPackage()->isMultipleSelect() ? $this->optionRepository->getPackageOptionsUidList($selectedOption->getConfigurationPackage()) : [$selectedOption->getUid()]);
				unset($configuration['packages'][$selectedOption->getConfigurationPackage()->getUid()]);
			}
		}

		$this->feSession->store($this->configurationSessionStorageKey, $configuration); // Store configuration in fe_session_data
		list($actionName, $controllerName, $extensionName, $arguments) = $redirect[$redirectAction]; // Set params for $this->redirect() method
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
		$this->view->assign('currencies', $this->checkForCurrencies($this->settings, true));
	}

	/**********************************
	 ******* NON-ACTION FUNCTIONS *****
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
	final static public function checkForCurrencies(array $settings, $returnFiltered = false) {
		if (!array_key_exists('currency', $settings))
			return false;

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
			return null;

		// Fetch selected options for current package
		$selectedOptions = null;
		if (array_key_exists($package->getUid(), (array) $this->selectedConfiguration['packages'])) {
			$selectedOptions = $this->optionRepository->findByUidList($this->selectedConfiguration['packages'][$package->getUid()]);
		}

		// Include pricing for enabled users!
		if ($this->showPricing) {
			foreach ($processOptions as $option) {
				$localizedPrice = $package->isMultipleSelect() || !$selectedOptions ? $option->getPrice() : floatval($option->getPrice() - $selectedOptions->getFirst()->getPrice());
				if ($this->feSession->get('currency') && $this->feSession->get('currency') !== 'default') {
					$priceList = $option->getPriceList();
					$selectedPriceList =  !$selectedOptions ?: $selectedOptions->getFirst()->getPriceList(); // if !$package->isMultipleSelect() we need this for calculating price difference!
					$localizedPrice = $package->isMultipleSelect() || !$selectedOptions ? floatval($priceList[$this->feSession->get('currency')]['vDEF']) : floatval(floatval($priceList[$this->feSession->get('currency')]['vDEF'] - $selectedPriceList[$this->feSession->get('currency')]['vDEF']));
				}
				$option->setLocalizedPrice($selectedOptions && in_array($option, $selectedOptions->toArray()) ? 0.00 : $localizedPrice);
			}
		}

		return $processOptions;
	}

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
					$option->setSelected(true);
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
		if (!$option->getDependency()) return true;
		$check = true;

		if ($dependency = $option->getDependency()) {
			if ($packages = $option->getDependency()->getPackages()) {
				$checkAgainstArray = array();
				foreach ($packages as $package) {
					if ($selectedOptions = $this->optionRepository->findByUidList($configuration['packages'][$package->getUid()])) {
						foreach ($selectedOptions as $selectedOption) {
							// @modes {0 : explicit deny, 1 : explicit allow}
							$checkAgainstArray[] = $dependency->getMode() === 0 ? !$dependency->getPackageOptions($package)->contains($selectedOption) : $dependency->getPackageOptions($package)->contains($selectedOption);
						}
					}
				}
				$check = !in_array(false, $checkAgainstArray);
			}
		}

		return $check;
	}

	/**
	 * Fetching selectable configurations
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|array|null $current actual setting
	 * @return void
	 */
	protected function setSelectableConfigurations(&$current = null) {
		$current = $current ?: $this->configurationRepository->findByTtContentUid($this->cObj->getFieldVal('uid'));

		// Overwrite for SKU-based configurations
		if (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal('tx_ecompc_type')) !== 1) {
			$current = $this->configurationRepository->findByTtContentUidExcludingSelectedOptions($this->cObj->getFieldVal('uid'), $this->selectedOptions);
		}
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage
	 * @param array $selectedConfiguration
	 * @return void
	 */
	public function setSelectedOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage &$objectStorage, array $selectedConfiguration) {
		if (count($selectedConfiguration['options'])) {
			$objectStorage->removeAll($objectStorage);
			if ($options = $this->optionRepository->findByUidList($selectedConfiguration['options'])) {
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
		$base = $config = 0.00;

		if (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal('tx_ecompc_type')) === 1 && $this->selectableConfigurations) {
			$base = $this->selectableConfigurations->getFirst()->getPrice();
			if ($this->feSession->get('currency') && $this->feSession->get('currency') !== 'default') {
				$priceList = $this->selectableConfigurations->getFirst()->getPriceList();
				$base = floatval($priceList[$this->feSession->get('currency')]['vDEF']);
			}

			// Get configuration price
			$config = $base;
			if ($this->selectedOptions) {
				foreach ($this->selectedOptions as $option) {
					$price = $option->getPrice();
					if ($this->feSession->get('currency') && $this->feSession->get('currency') !== 'default') {
						$priceList = $option->getPriceList();
						$price = floatval($priceList[$this->feSession->get('currency')]['vDEF']);
					}
					$config += $price;
				}
			}
		}

		return array($base, $config);
	}

	public function getConfigurationResult() {
		switch (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal('tx_ecompc_type'))) {
			// In case of dynamic configurations get first configuration record
			case 1:
				return $this->buildConfigurationCode($this->selectableConfigurations->getFirst());
				break;
			// Otherwise fetch all configuration records @todo fetch SKU
			default:
				if ($this->selectableConfigurations->count() !== 1) {
					$this->throwStatus(404, NULL, 'Something went terribly wrong! Please contact your system administrator for assistance.');
				} else {
					return $this->selectableConfigurations->getFirst()->getSku();
				}
		}
	}

	public function buildConfigurationCode(\S3b0\Ecompc\Domain\Model\Configuration $configuration) {
		$wrapper = $configuration->getConfigurationCodePrefix() . '%s' . $configuration->getConfigurationCodeSuffix();

		$code = '';
		/**
		 * @param \S3b0\Ecompc\Domain\Model\Package $package
		 */
		foreach ($this->packageRepository->findByUidList(CoreUtility\GeneralUtility::intExplode(',', $this->cObj->getFieldVal('tx_ecompc_packages'), true)) as $package) {
			if (!$package->isVisibleInFrontend()) {
				$code .= '<span class="ecompc-syntax-help">' . $package->getDefaultOption()->getConfigurationCodeSegment() . '</span>';
				continue;
			} elseif ($options = $this->optionRepository->findByUidList($this->selectedConfiguration['packages'][$package->getUid()])) {
				foreach ($options as $option) {
					$code .= '<span class="ecompc-syntax-help" title="' . $option->getFrontendLabel() . '">' . $option->getConfigurationCodeSegment() . '</span>';
				}
			}
		}

		return sprintf($wrapper, $code);
	}

}