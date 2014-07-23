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
 */
class StandardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	protected $cObj = null;
	protected $selectedItems = array();
	protected $configuration = array();
	protected $availableConfigurations = null;
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

		// Get current configuration (Array: keys=package|values=option)
		$this->configuration = $this->feSession->get('cfg');
		// Get current configuration (\TYPO3\Extbase\Persistence\QueryResultInterface)
		if (is_array($this->configuration)) {
			$this->selectedItems = array();
			foreach ($this->configuration as $package) {
				$this->selectedItems = array_merge($this->selectedItems, $this->optionRepository->findByUidList($package)->toArray());
			}
		}
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
			'currencySetup' => $this->settings['currency'][$this->feSession->get('currency')], // fetch currency TS
			'configurator' => array( // configurator settings
				'type' => $this->cObj->getFieldVal('tx_ecompc_type'), // configuration type
				'configurations' => $this->getConfigurations(), // configurations
			),
			'currentConfiguration' => $this->configuration, // current configuration
			'pricing' => $this->showPricing ? $this->getConfigurationPrice() : null // current configuration price
		));
	}

	/**
	 * action index
	 *
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
			} elseif (!$this->feSession->get('currency') && $currency && array_key_exists($currency, $this->settings['currency'])) {
				$this->feSession->store('currency', $currency); // Store region selection
				$this->initializeView(); // Recall initializeView() to set currency (session var) depending values/arrays
			}
		}

		// Fetch packages @todo Extend Option-Model marking selectedItem
		$list = CoreUtility\GeneralUtility::intExplode(',', $this->cObj->getFieldVal('tx_ecompc_packages'), TRUE);
		$packages = $this->packageRepository->findByUidList($list);
		if ($packages) {
			foreach ($packages as $package) {
				$selectedOptions = array_key_exists($package->getUid(), $this->configuration) ? $this->optionRepository->findByUidList($this->configuration[$package->getUid()]) : null;
				$package->setSelectedOptions($selectedOptions);
			}
		}

		// Get process / config code
		$printable = $this->packageRepository->findPrintableByUidList($list);
		$process = count((array) $this->configuration) / count($printable);
		if ($process === 1) {
			$this->view->assign('configurationResult', $this->getConfigurationResult());
		}

		$this->view->assignMultiple(array(
			'packages' => $packages,
			'process' => $process
		));
	}

	/**
	 * action selectPackageOptions
	 *
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
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @param int                              $redirectAction
	 * @return void
	 */
	public function setOptionAction(\S3b0\Ecompc\Domain\Model\Option $option, $redirectAction = 0) {
		$configuration = $this->configuration;
		$redirect = array(
			array('index', null, null, array()),
			array('selectPackageOptions', null, null, array('package' => $option->getConfigurationPackage()))
		);

		if (array_key_exists($option->getConfigurationPackage()->getUid(), $configuration)) {
			// For those packages NOT supporting multipleSelect mode unset their current selection, if any
			if (!$option->getConfigurationPackage()->isMultipleSelect())
				unset($configuration[$option->getConfigurationPackage()->getUid()]);
			// Add option to current configuration before dependency check, if not present already!
			if (!in_array($option->getUid(), $configuration[$option->getConfigurationPackage()->getUid()]))
				$configuration[$option->getConfigurationPackage()->getUid()][] = $option->getUid();
		} else {
			// Add option to current configuration before dependency check
			$configuration[$option->getConfigurationPackage()->getUid()][] = $option->getUid();
		}

		// Run the dependency check if other packages next to current got options chosen
		if (count($configuration) > 1) {
			foreach ($configuration as $packageOptions) {
				if ($selectedOptions = $this->optionRepository->findByUidList($packageOptions)) {
					foreach ($selectedOptions as $selectedOption) {
						if (!$this->checkOptionDependencies($selectedOption, $configuration)) {
							if ($option->getConfigurationPackage()->isMultipleSelect())
								unset($configuration[$selectedOption->getConfigurationPackage()->getUid()][$selectedOption->getUid()]);
							else
								unset($configuration[$selectedOption->getConfigurationPackage()->getUid()]);
						}
					}
				}
			}
		}

		$this->feSession->store('cfg', $configuration);
		list($actionName, $controllerName, $extensionName, $arguments) = $redirect[$redirectAction];
		$this->redirect($actionName, $controllerName, $extensionName, $arguments);
	}

	/**
	 * action unsetOption
	 *
	 * @todo implement in localconf/templates / finish!
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @param int                              $redirectAction
	 * @return void
	 */
	public function unsetOptionAction(\S3b0\Ecompc\Domain\Model\Option $option, $redirectAction = 0) {
		$redirect = array(
			'index',
			'selectPackageOptions'
		);
		$configuration = $this->configuration;
		// For those packages NOT supporting multipleSelect mode unset their current selection, if any
		if (array_key_exists($option->getConfigurationPackage()->getUid(), $configuration) && !$option->getConfigurationPackage()->isMultipleSelect()) {
			unset($configuration[$option->getConfigurationPackage()->getUid()]);
		}

		// Add option to current configuration before dependency check, if not present already!
		if (!(array_key_exists($option->getConfigurationPackage()->getUid(), $configuration) && in_array($option->getUid(), $configuration[$option->getConfigurationPackage()->getUid()]))) {
			$configuration[$option->getConfigurationPackage()->getUid()][] = $option->getUid();
		}
		// Run the dependency check if multiple packages have been set
		if (count($configuration) - 1) {
			foreach ($configuration as $packageUid => $options) {
				foreach ($options as $optionUid) {
					if (!$this->checkOptionDependencies($this->optionRepository->findByUid($optionUid))) {
						unset($configuration[$packageUid][$optionUid]);
					}
				}
			}
		}

		$this->feSession->store('cfg', $configuration);
		$this->redirect($redirect[$redirectAction], $this->request->getControllerName(), $this->extensionName, array('package' => $option->getConfigurationPackage()));
	}

	/**
	 * action reset
	 * Resets the configuration!
	 *
	 * @return void
	 */
	final public function resetAction() {
		$this->feSession->store('cfg', null);
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
	final static private function checkForCurrencies(array $settings, $returnFiltered = false) {
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
	 * Fetching configurations linked to current cObj
	 *
	 * @return array|null
	 */
	protected function getConfigurations() {
		// Avoid double initialization (e.g. when re-calling initializeView() another time)
		if ($this->availableConfigurations !== null) {
			return $this->availableConfigurations;
		}

		switch (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal('tx_ecompc_type'))) {
			// In case of dynamic configurations get first configuration record
			case 1:
				if ($temp = $this->configurationRepository->findOneByTtContentUid($this->cObj->getFieldVal('uid')))
					$this->availableConfigurations[] = $temp;
				break;
			// Otherwise fetch all configuration records
			default:
				if ($temp = $this->configurationRepository->findByTtContentUid($this->cObj->getFieldVal('uid')))
					$this->availableConfigurations = $temp->toArray();
		}

		return $this->availableConfigurations;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array|null
	 */
	public function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package) {
		$options = $this->optionRepository->findByConfigurationPackage($package);
		if ($options === null)
			return null;

		// Fetch selected options for current package
		$selectedOptions = array();
		if (is_array($this->configuration) && array_key_exists($package->getUid(), $this->configuration)) {
			$selectedOptions = $this->optionRepository->findByUidList($this->configuration[$package->getUid()]);
		}

		// process dependencies
		$processOptions = array();
		foreach ($options as $option) {
			if ($this->checkOptionDependencies($option, $this->configuration)) {
				$processOptions[] = $option;
			}
		}

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
					$selectedOptions = $this->optionRepository->findByUidList($configuration[$package->getUid()]);
					foreach ($selectedOptions as $selectedOption) {
						switch ($dependency->getMode()) {
							case 0:
								$checkAgainstArray[] = !$dependency->getPackageOptions($package)->contains($selectedOption);
								break;
							default:
								$checkAgainstArray[] = $dependency->getPackageOptions($package)->contains($selectedOption);
						}
					}
				}
				$check = !in_array(false, $checkAgainstArray);
			}
		}

		return $check;
	}

	/**
	 * Calculates the price for configurations already during configuration process
	 *
	 * @todo new calculation according to array model of configuration session var
	 * @return array
	 */
	public function getConfigurationPrice() {
		$base = 0;

		if (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal('tx_ecompc_type')) === 1 && ($this->availableConfigurations instanceof \ArrayAccess || is_array($this->availableConfigurations))) {
			$base = $this->availableConfigurations[0]->getPrice();
			if ($this->feSession->get('currency') && $this->feSession->get('currency') !== 'default') {
				$priceList = $this->availableConfigurations[0]->getPriceList();
				$base = $priceList[$this->feSession->get('currency')]['vDEF'];
			}
		}

		$config = $base;
		if ($selected = $this->feSession->get('cfg')) {
	#		foreach ($selected as $selectedItem) {
	#			$option = $this->optionRepository->findByUid($selectedItem);
	#			$price = $option->getPrice();
	#			if ($this->feSession->get('currency') && $this->feSession->get('currency') !== 'default') {
	#				$priceList = $option->getPriceList();
	#				$price = $priceList[$this->feSession->get('currency')]['vDEF'];
	#			}
	#			$config += $price;
	#		}

		}

		return array($base, $config);
	}

	public function getConfigurationResult() {
		switch (CoreUtility\MathUtility::convertToPositiveInteger($this->cObj->getFieldVal('tx_ecompc_type'))) {
			// In case of dynamic configurations get first configuration record
			case 1:
				return $this->buildConfigurationCode($this->availableConfigurations[0]);
				break;
			// Otherwise fetch all configuration records
			default:
				if ($temp = $this->configurationRepository->findByTtContentUid($this->cObj->getFieldVal('uid')))
					$this->availableConfigurations = $temp->toArray();
		}

		return 'abc';
	}

	public function buildConfigurationCode(\S3b0\Ecompc\Domain\Model\Configuration $configuration) {
		$wrapper = $configuration->getConfigurationCodePrefix() . '%s' . $configuration->getConfigurationCodeSuffix();

		$code = '';
		/**
		 * @param \S3b0\Ecompc\Domain\Model\Package $package
		 */
		foreach ($this->packageRepository->findByUidList(CoreUtility\GeneralUtility::intExplode(',', $this->cObj->getFieldVal('tx_ecompc_packages'), TRUE)) as $package) {
			$option = $package->isVisibleInFrontend() ? $this->optionRepository->findByUid(reset($this->configuration[$package->getUid()])) : $package->getDefaultOption();
			$code .= $option->getConfigurationCodeSegment();
		}

		return sprintf($wrapper, $code);
	}

}