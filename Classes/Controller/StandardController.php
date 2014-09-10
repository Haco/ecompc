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
 * StandardController
 *
 * @todo re-configuration of sku-based, releasing dependencies of chosen packages (mark incompatible!)
 * @package S3b0
 * @subpackage Ecompc
 */
class StandardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \S3b0\Ecompc\Domain\Model\Content|null
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
	 * Enables/Disables price labels
	 *
	 * @var boolean
	 */
	protected $showPriceLabels = FALSE;

	/**
	 * @var array
	 */
	protected $currency = array(
		'long' => 'Euro',
		'short' => 'EUR',
		'symbol' => '€',
		'region' => 'Europe',
		'llRef' => 'LLL:EXT:ecompc/Resources/Private/Language/locallang_db.xlf:currency.eur.region',
		'flagIcon' => 'EXT:ecompc/Resources/Public/Images/Flags/EUR.png',
		'prependCurrency' => 0,
		'exchange' => 1.0
	);

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
	 * loggerRepository
	 *
	 * @var \S3b0\Ecompc\Domain\Repository\LoggerRepository
	 * @inject
	 */
	protected $loggerRepository;

	/**
	 * frontendUserRepository
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	 * @inject
	 */
	protected $frontendUserRepository;

	/**
	 * feSession
	 *
	 * @var \S3b0\Ecompc\Domain\Session\FrontendSessionHandler
	 * @inject
	 */
	protected $feSession;

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
		// Fetch content object (tt_content)
		$this->cObj = $this->cObj ?: $this->contentRepository->findByUid($this->configurationManager->getContentObject()->data['uid']);
		if (!$this->cObj instanceof \S3b0\Ecompc\Domain\Model\Content)
			$this->throwStatus(404, \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('404.no_cObj', $this->extensionName));
		// Frontend-Session
		$this->feSession->setStorageKey($this->request->getControllerExtensionName() . $this->request->getPluginName());
		// Get Extension configuration (set @Extension Manager)
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ecompc']);
		// Get distributors frontend user groups (set @Extension Manager)
		$distFeUserGroups = CoreUtility\GeneralUtility::intExplode(',', $extConf['distFeUserGroup'], TRUE);
		// Set price flag (displays pricing if TRUE)
		$this->showPriceLabels = $this->settings['showPriceLabels'] ? count(array_intersect($distFeUserGroups, (array) $GLOBALS['TSFE']->fe_user->groupData['uid'])) : FALSE;
		// Add cObj-uid to configurationSessionStorageKey to make it unique in sessionStorage
		$this->configurationSessionStorageKey .= '-c' . $this->cObj->getUid();
		// Get current configuration (Array: options=array(options)|packages=array(package => option(s)))
		$this->selectedConfiguration = $this->feSession->get($this->configurationSessionStorageKey) ?: array(
			'options' => array(),
			'packages' => array()
		);
		if ($this->showPriceLabels) {
			// Fetch currency configuration from TS
			$this->currency = $this->settings['currency'][$this->feSession->get('currency')];
		}
		// Initialize Options
		$this->initializeOptions();
		// Set selectable configurations
		$this->setSelectableConfigurations($this->selectableConfigurations);
		// Get configuration price
		$this->selectedConfigurationPrice = $this->showPriceLabels ? $this->getConfigurationPrice() : array();
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
			'action' => $this->request->getControllerActionName(), // current action
			'instructions' => $this->cObj->getBodytext(), // short instructions for user
			'pid' => $GLOBALS['TSFE']->id,
			'cObj' => $this->cObj->getUid()
		));
		if ($this->showPriceLabels) {
			$this->view->assignMultiple(array(
				'showPriceLabels' => $this->showPriceLabels, // checks whether price labels are displayed or not!
				'currency' => $this->currency, // fetch currency TS
				'pricing' => $this->selectedConfigurationPrice // current configuration price
			));
		}
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
		if ($this->contentRepository->hasDuplicateContentElementsWithPlugin())
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.dbl_config_found', $this->extensionName));

		// Failure if no configuration has been found
		if (!$this->cObj->getEcompcConfigurations())
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.no_config_found', $this->extensionName) . ' [ttc:#' . $this->cObj->getUid() . '@pid:#' . $this->cObj->getPid() . ']');

		// Check for currency when distributor is logged in
		if ($this->showPriceLabels) {
			if (!$this->feSession->get('currency') && !$currency && $this->checkForCurrencies($this->settings) === TRUE) {
				$this->forward('selectRegion'); // Add redirect for region selection - influencing currency display
			} elseif (!$this->feSession->get('currency') && $currency && array_key_exists($currency, (array) $this->settings['currency'])) {
				$this->feSession->store('currency', $currency); // Store region selection
				$this->currency = $this->settings['currency'][$currency];
				$this->redirectToPage();
			}
		}

		$process = 0;
		// Fetch packages | Set (in)active states
		if ($packages = $this->cObj->getEcompcPackagesFE()) {
			$isActive = FALSE;
			$prev = NULL;
			/** @var \S3b0\Ecompc\Domain\Model\Package $package */
			foreach (array_reverse($packages->toArray()) as $package) {
				if (!$isActive && array_key_exists($package->getUid(), $this->selectedConfiguration['packages'])) {
					$isActive = TRUE;
					if ($prev instanceof \S3b0\Ecompc\Domain\Model\Package) {
						$prev->setActive(TRUE);
					}
				}
				$package->setActive($isActive);
				/** @var \S3b0\Ecompc\Domain\Model\Package $prev */
				$prev = $package;
			}
			if (!$isActive) {
				$package->setActive(TRUE);
			}
			// Get process state update (ratio of selected to visible packages) => float from 0 to 1 (*100 = %)
			$process = count($this->selectedConfiguration['packages']) / $packages->count();
		}

		if ($process === 1)
			$this->view->assign('configurationResult', $this->getConfigurationResult()); // Get configuration code | SKU

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
		$this->view->assign('package', $package);
		$this->view->assign('options', $this->getPackageOptions($package));
	}

	/**
	 * action setOption
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @param boolean                          $unset           Identifier to unset option!
	 * @param integer                          $redirectAction
	 * @return void
	 */
	public function setOptionAction(\S3b0\Ecompc\Domain\Model\Option $option, $unset = FALSE, $redirectAction = 0) {
		$configuration = $this->selectedConfiguration;

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
				$configuration['options'] = array_diff((array) $configuration['options'], $this->optionRepository->getPackageOptionsUids($option->getConfigurationPackage()));
				unset($configuration['packages'][$option->getConfigurationPackage()->getUid()]);
			}
			// Remove option from configuration array if 'unset'-flag is ON
			if ($unset) {
				$configuration['options'] = array_diff((array) $configuration['options'], [$option->getUid()]);
				if ($option->getConfigurationPackage()->isMultipleSelect()) {
					$configuration['packages'][$option->getConfigurationPackage()->getUid()] = array_diff((array) $configuration['packages'][$option->getConfigurationPackage()->getUid()], [$option->getUid()]);
					if (count($configuration['packages'][$option->getConfigurationPackage()->getUid()]) === 0)
						unset($configuration['packages'][$option->getConfigurationPackage()->getUid()]);
				}
			} else {
				// Add options to package selection if 'unset'-flag is OFF
				if (!in_array($option->getUid(), (array) $configuration['options'])) {
					$configuration['options'][] = $option->getUid();
					$configuration['packages'][$option->getConfigurationPackage()->getUid()][] = $option->getUid();
				}
			}
		} else {
			// Add options to NEW package if 'unset'-flag is OFF
			if (!$unset) {
				$configuration['options'][] = $option->getUid();
				$configuration['packages'][$option->getConfigurationPackage()->getUid()][] = $option->getUid();
			}
		}

		// Add or remove selected option to selection
		$unset ? $this->selectedOptions->detach($option) : $this->selectedOptions->attach($option);
		// Run the dependency check if other packages next to current got options chosen
		foreach ($this->selectedOptions as $selectedOption) {
			if (!$this->checkOptionDependencies($selectedOption, $configuration)) {
				$configuration['options'] = array_diff($configuration['options'], $option->getConfigurationPackage()->isMultipleSelect() ? $this->optionRepository->getPackageOptionsUids($selectedOption->getConfigurationPackage()) : [$selectedOption->getUid()]);
				unset($configuration['packages'][$selectedOption->getConfigurationPackage()->getUid()]);
			}
		}

#		// Prepared autoFill. Still some issues, especially regarding traversing as of dependency checks!
#		if ($this->settings['auto_set'])
#			$this->autoSetOptions($configuration);
		$this->feSession->store($this->configurationSessionStorageKey, $configuration); // Store configuration in fe_session_data

		// Redirections (StandardController ONLY!)
		if ($this->request->getControllerName() === 'Standard') {
			$redirect = array(
				array('index', NULL, NULL, array()),
				array('selectPackageOptions', NULL, NULL, array('package' => $option->getConfigurationPackage()))
			);
			list($actionName, $controllerName, $extensionName, $arguments) = $redirect[$unset ?: $redirectAction]; // Set params for $this->redirect() method
			$this->redirect($actionName, $controllerName, $extensionName, $arguments);
		}
	}

	/**
	 * action resetPackage
	 * Resets the package configuration!
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @return void
	 */
	public function resetPackageAction(\S3b0\Ecompc\Domain\Model\Package $package) {
		$this->selectedConfiguration['options'] = array_diff($this->selectedConfiguration['options'], $this->optionRepository->getPackageOptionsUids($package));
		unset($this->selectedConfiguration['packages'][$package->getUid()]);

		$this->feSession->store($this->configurationSessionStorageKey, $this->selectedConfiguration);
		$this->redirect('selectPackageOptions', NULL, NULL, array('package' => $package));
	}

	/**
	 * action reset
	 * Resets the configurator!
	 *
	 * @return void
	 */
	public function resetAction() {
		$this->feSession->store($this->configurationSessionStorageKey, array());
		$this->redirectToPage();
	}

	public function requestAction() {
		/** @var \S3b0\Ecompc\Domain\Model\Logger $logger */
		$logger = $this->objectManager->get('S3b0\\Ecompc\\Domain\\Model\\Logger');
		$pricing = $this->getConfigurationPrice();
		$logger->setSelectedConfiguration($this->selectedConfiguration)
			->setCurrency($this->currency['long'])
			->setPrice($pricing[1])
			->setIp(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'), $this->settings['log']['ipParts'])
			->setConfiguration($this->selectableConfigurations->getFirst());
		if ($GLOBALS['TSFE']->loginUser) {
			$logger->setFeUser($this->frontendUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']));
		}

		$this->loggerRepository->add($logger);
		$result = $this->getConfigurationResult(TRUE);
		$this->feSession->store($this->configurationSessionStorageKey, array()); // Unset configuration to avoid multiple submit provided by back button!
		$this->redirectToPage($this->settings['requestForm']['pid'], $result[2]); //target="{settings.requestForm.target}" additionalParams="{requestFormAdditionalParams}"
	}

	/**
	 * action selectRegion
	 *
	 * @return void
	 */
	public function selectRegionAction() {
		if ($this->feSession->get('currency'))
			$this->redirectToPage();

		$this->view->assign('currencies', $this->checkForCurrencies($this->settings, TRUE));
	}

	/**********************************
	 ******** NON-ACTION METHODS ******
	 **********************************/

	/**
	 * Redirects to current page without any params or CacheHash values!
	 *
	 * @param integer $pid
	 * @param array   $arguments
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function redirectToPage($pid = NULL, $arguments = array(), $useCachHash = FALSE) {
		if (!$this->request instanceof \TYPO3\CMS\Extbase\Mvc\Web\Request) {
			throw new \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException('redirect() only supports web requests.', 1220539734);
		}

		if (\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL')) {
			$this->uriBuilder->setAbsoluteUriScheme('https');
		}
		$uri = $this->uriBuilder
			->reset()
			->setCreateAbsoluteUri(TRUE)
			->setArguments((array) $arguments)
			->setTargetPageUid($pid)
			->setUseCacheHash($useCachHash)
			->setAddQueryString(TRUE);

		$this->redirectToUri($uri->buildFrontendUri());
	}

	/**
	 * Checking currencies set by TS
	 *
	 * @param array   $settings
	 * @param boolean $returnFiltered
	 *
	 * @internal
	 * @return boolean|array
	 */
	final protected static function checkForCurrencies(array $settings, $returnFiltered = FALSE) {
		if (!array_key_exists('currency', $settings))
			return FALSE;

		$requiredFieldsPattern = array(
			'short' => 1,
			'symbol' => 1,
			'region' => 1,
			'flagIcon' => 1
		);

		$processCurrencies = $returnCurrencies = $settings['currency'];
		foreach ($processCurrencies as $typoScriptKey => $currency) {
			// Add key for identification to currencies.
			$returnCurrencies[$typoScriptKey]['key'] = $typoScriptKey;
			// Drop elements not matching required fields
			$required = array_intersect_key($currency, $requiredFieldsPattern);
			if (count($required) !== count($requiredFieldsPattern) || $typoScriptKey === 'def') {
				unset($processCurrencies[$typoScriptKey]);
				if ($typoScriptKey !== 'def')
					unset($returnCurrencies[$typoScriptKey]);
				continue;
			}
		}

		return $returnFiltered && count($returnCurrencies) ? $returnCurrencies : (bool) count($processCurrencies);
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array|null
	 */
	protected function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package) {
		$processOptions = array();
		// Fetch selectable options for current package
		$this->getSelectableOptions($package, $processOptions);
		if (!$processOptions)
			return NULL;

		// Fetch selected options for current package only
		$selectedOptions = NULL;
		if (array_key_exists($package->getUid(), $this->selectedConfiguration['packages'])) {
			$selectedOptions = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['packages'][$package->getUid()]);
		}

		// Include pricing for enabled users!
		if ($this->showPriceLabels) {
			$this->initializeOptions($selectedOptions, FALSE);
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
		$selectableOptions = $this->optionRepository->findByConfigurationPackage($package); // Set basic settings

		// Parse configurations, if any | @case SKU (default)
		if ($this->cObj->isStaticEcomProductConfigurator() && $this->selectableConfigurations) {
			$selectableOptions = array();
			foreach ($this->selectableConfigurations as $configuration) {
				if ($configurationOptions = $configuration->getOptions()) {
					/** @var \S3b0\Ecompc\Domain\Model\Option $configurationOption */
					foreach ($configurationOptions as $configurationOption) {
						if ($configurationOption->getConfigurationPackage() === $package)
							$selectableOptions[$configurationOption->getSorting()] = $configurationOption;
					}
				}
			}
			$selectableOptions = array_unique($selectableOptions);
			ksort($selectableOptions);
		}

		// Run dependency check
		$result = array();
		/** @var \S3b0\Ecompc\Domain\Model\Option $option */
		foreach ($selectableOptions as $option) {
			if ($this->checkOptionDependencies($option, $this->selectedConfiguration)) {
				if ($this->selectedOptions && in_array($option, $this->selectedOptions->toArray()))
					$option->setSelected(TRUE);
				$result[] = $option;
			}
		}
	}

	/**
	 * Checks an option against its dependencies. Returns TRUE if option IS NOT prohibited!
	 *
	 * @param  \S3b0\Ecompc\Domain\Model\Option $option
	 * @param  array                            $configuration
	 * @return boolean
	 */
	protected function checkOptionDependencies(\S3b0\Ecompc\Domain\Model\Option $option, array $configuration) {
		if (!$option->getDependency()) return TRUE;
		$check = TRUE;

		if ($dependency = $option->getDependency()) {
			if ($packages = $option->getDependency()->getPackages()) {
				$checkAgainstArray = array();
				foreach ($packages as $package) {
					if (!is_array($configuration['packages'][$package->getUid()]) || count((array) $configuration['packages'][$package->getUid()]) === 0) {
						continue;
					}
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
	protected function autoSetOptions(array &$configuration) {
		if ($packages = $this->cObj->getEcompcPackagesFE()) {
			/** @var \S3b0\Ecompc\Domain\Model\Package $package */
			foreach ($packages as $package) {
				if (array_key_exists($package->getUid(), (array) $configuration['packages']))
					continue;

				if ($packageOptions = $this->getPackageOptions($package)) {
					if (count($packageOptions) === 1) {
						// Add option to NEW package
						$configuration['options'][] = $packageOptions->getFirst()->getUid();
						$configuration['packages'][$package->getUid()][] = $packageOptions->getFirst()->getUid();
						$this->selectedOptions->attach($packageOptions->getFirst());
						$this->selectedPackages->attach($package);
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
		$current = $current ?: $this->configurationRepository->findByTtContentUid($this->cObj->getUid());

		// Overwrite for SKU-based configurations
		if (!$this->cObj->isDynamicEcomProductConfigurator()) {
			$current = $this->configurationRepository->findByTtContentUidApplyingSelectedOptions($this->cObj->getUid(), $this->selectedOptions);
		}
	}

	/**
	 * @param boolean $returnContentAndSkipAssignToView
	 *
	 * @return mixed
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	protected function getConfigurationResult($returnContentAndSkipAssignToView = FALSE) {
		// In case of dynamic configurations get first configuration record...
		if ($this->cObj->isDynamicEcomProductConfigurator()) {
			return $this->getConfigurationCode($this->selectableConfigurations->getFirst(), $returnContentAndSkipAssignToView);
		}

		// ...otherwise check for suitable configurations and return result in case of one remaining, of not throw 404 Error
		if ($this->selectableConfigurations->count() !== 1) {
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.no_unique_sku_found', $this->extensionName));
		} else {
			return $this->getConfigurationCode($this->selectableConfigurations->getFirst(), $returnContentAndSkipAssignToView);
		}
	}

	/**
	 * set configuration code
	 *
	 * @param  \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 * @param  boolean                                 $returnContentAndSkipAssignToView
	 * @return string
	 */
	protected function getConfigurationCode(\S3b0\Ecompc\Domain\Model\Configuration $configuration, $returnContentAndSkipAssignToView = FALSE) {
		$ccWrapper = $this->cObj->isDynamicEcomProductConfigurator() && $configuration->getConfigurationCodePrefix() ? '<span class="ecompc-syntax-help" title="' . ExtbaseUtility\LocalizationUtility::translate('csh.configCodePrefix', $this->extensionName) . '">' . $configuration->getConfigurationCodePrefix() . '</span>' : '';
		$ccWrapper .= '%s';
		$ccWrapper .= $this->cObj->isDynamicEcomProductConfigurator() && $configuration->getConfigurationCodeSuffix() ? '<span class="ecompc-syntax-help" title="' . ExtbaseUtility\LocalizationUtility::translate('csh.configCodeSuffix', $this->extensionName) . '">' . $configuration->getConfigurationCodeSuffix() . '</span>' : '';
		$ccPlainWrapper = $this->cObj->isDynamicEcomProductConfigurator() ? $configuration->getConfigurationCodePrefix() . '%s' . $configuration->getConfigurationCodeSuffix() : $ccWrapper;
		$ccSegmentWrapper = '<span class="ecompc-syntax-help" title="%1$s">%2$s</span>';
		$summaryPlainWrapper = '%1$s: %2$s\n';
		$summaryHMTLTableWrapper = '<table>%s</table>';
		$summaryHTMLTableRowWrapper = '<tr><td><b>%1$s:</b></td><td>%2$s</td></tr>';

		$code = '';
		$plain = '';
		$summaryPlain = '';
		$summaryHTML = '';

		foreach ($this->cObj->getEcompcPackages() as $package) {
			if (!$package->isVisibleInFrontend()) {
				$code .= sprintf($ccSegmentWrapper, $package->getFrontendLabel(), $package->getDefaultOption()->getConfigurationCodeSegment());
				$plain .= $package->getDefaultOption()->getConfigurationCodeSegment();
				$summaryPlain .= sprintf($summaryPlainWrapper, $package->getFrontendLabel(), $package->getDefaultOption()->getFrontendLabel() . ($this->cObj->isStaticEcomProductConfigurator() ? '' : ' [' . $package->getDefaultOption()->getConfigurationCodeSegment() . ']'));
				$summaryHTML .= sprintf($summaryHTMLTableRowWrapper, $package->getFrontendLabel(), $package->getDefaultOption()->getFrontendLabel() . ($this->cObj->isStaticEcomProductConfigurator() ? '' : ' [' . $package->getDefaultOption()->getConfigurationCodeSegment() . ']'));
			} elseif ($options = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['packages'][$package->getUid()])) {
				$optionsList = array();
				foreach ($options as $option) {
					$code .= sprintf($ccSegmentWrapper, $option->getConfigurationPackage()->getFrontendLabel(), $option->getConfigurationCodeSegment());
					$plain .= $option->getConfigurationCodeSegment();
					$optionsList[] = $option->getFrontendLabel() . ($this->cObj->isStaticEcomProductConfigurator() ? '' : ' [' . $option->getConfigurationCodeSegment() . ']');
				}
				$summaryPlain .= sprintf($summaryPlainWrapper, $package->getFrontendLabel(), implode(PHP_EOL, $optionsList));
				$summaryHTML .= sprintf($summaryHTMLTableRowWrapper, $package->getFrontendLabel(), implode('<br />', $optionsList));
			}
		}

		// OVERWRITE CONFIG CODE @SKU-BASED CONFIGURATORS
		if ($this->cObj->isStaticEcomProductConfigurator()) {
			$plain = $code = $configuration->getSku();
		}

		$this->view->assignMultiple(array(
			'configurationSummary' => sprintf($summaryHMTLTableWrapper, $summaryHTML),
			'requestFormAdditionalParams' => json_decode(
				sprintf(
					$this->settings['requestForm']['additionalParams'],
					\TYPO3\CMS\Core\Utility\GeneralUtility::rawUrlEncodeJS(sprintf($ccPlainWrapper, $plain)),
					\TYPO3\CMS\Core\Utility\GeneralUtility::rawUrlEncodeJS($configuration->getFrontendLabel()),
					$summaryPlain
				),
				TRUE
			)
		));

		return $returnContentAndSkipAssignToView ? array(
			sprintf($ccWrapper, $code), sprintf($summaryHMTLTableWrapper, $summaryHTML), json_decode(
				sprintf(
					$this->settings['requestForm']['additionalParams'],
					\TYPO3\CMS\Core\Utility\GeneralUtility::rawUrlEncodeJS(sprintf($ccPlainWrapper, $plain)),
					\TYPO3\CMS\Core\Utility\GeneralUtility::rawUrlEncodeJS($configuration->getFrontendLabel()),
					$summaryPlain
				),
				TRUE
			)
		) : sprintf($ccWrapper, $code);
	}

	/**
	 * Calculates the price for configurations already during configuration process
	 *
	 * @return array
	 */
	protected function getConfigurationPrice() {
		$base = $this->cObj->getEcompcBasePriceInDefaultCurrency();
		if (!$base) return array(0.0, 0.0);
		if ($this->currency['short'] !== 'EUR') {
			$priceList = $this->cObj->getEcompcBasePriceInForeignCurrencies();
			$base = floatval($priceList[$this->currency['short']]['vDEF']);
			if (!$base && $this->currency['exchange'])
				$base = $this->cObj->getEcompcBasePriceInDefaultCurrency() * floatval($this->currency['exchange']);
		}

		// Get configuration price
		$config = $base;
		if ($this->selectedOptions) {
			/** @var \S3b0\Ecompc\Domain\Model\Option $option */
			foreach ($this->selectedOptions as $option)
				$config += $option->getConfigurationPackage()->isPercentPricing() ? floatval($config * $option->getPricePercental()) : $option->getPriceInCurrency($this->currency['short'], floatval($this->currency['exchange']));
		}

		return array($base, $config);
	}

	/**
	 * Initialize Options
	 *
	 * @param null|\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $selectedOptions
	 * @param boolean $firstRun indicates to handle as it was the initial run
	 */
	protected function initializeOptions($selectedOptions = NULL, $firstRun = TRUE) {
		/** Initialize Storage Objects */
		if ($firstRun) {
			$this->selectedOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		}
		// If argument package is set, fetch corresponding options only to improve performance
		$options = $this->request->hasArgument('package') ? $this->optionRepository->findByConfigurationPackage($this->request->getArgument('package')) : $this->optionRepository->findAll();
		if ($options) {
			$parsedPackages = array();
			/** @var \S3b0\Ecompc\Domain\Model\Option $option */
			foreach ($options as $option) {
				/** @var \S3b0\Ecompc\Domain\Model\Package $package */
				$package = $option->getConfigurationPackage();
				$package->setSelected(array_key_exists($package->getUid(), $this->selectedConfiguration['packages']));
				$optionIsActive = in_array($option->getUid(), $this->selectedConfiguration['options']);
				/*******************************************************
				 * Set selected options & Add to corresponding package *
				 *******************************************************/
				if ($firstRun && in_array($option->getUid(), $this->selectedConfiguration['options'])) {
					$package->addSelectedOption($option);
					$this->selectedOptions->attach($option);
				}
				/**************************************************
				 * Process pricing | Set corresponding properties *
				 **************************************************/
				if ($this->showPriceLabels && $this->cObj->getEcompcBasePriceInDefaultCurrency()) {
					if (!in_array($package->getUid(), $parsedPackages)) {
						$package->setPriceOutput(0.0); // Reset package price information
					}
					/*****************************************************************************************
					 * Calculate PERCENT price [working on packages WITHOUT multipleSelect() flag set ONLY!] *
					 *****************************************************************************************/
					if ($package->isPercentPricing() && !$package->isMultipleSelect()) {
						$currentConfigurationPrice = end($this->getConfigurationPrice());
						$configurationPrice = $package->isSelected() ? floatval($currentConfigurationPrice / ($this->optionRepository->findOptionsByUidList($this->selectedConfiguration['packages'][$package->getUid()], 1)->getPricePercental() + 1)) : 0.0;
						$selectedOptionPrice = floatval($currentConfigurationPrice - $configurationPrice);
						if (array_key_exists($package->getUid(), $this->selectedConfiguration['packages']) && !($selectedOptions instanceof \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult && in_array($option, $selectedOptions->toArray()))) {
							$option->setUnitPrice($optionIsActive ? $selectedOptionPrice : floatval($configurationPrice * $option->getPricePercental()));
							$option->setPriceOutput($optionIsActive ? $selectedOptionPrice : floatval($configurationPrice * $option->getPricePercental() - $selectedOptionPrice));
						} else {
							$option->setUnitPrice($selectedOptions instanceof \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult && in_array($option, $selectedOptions->toArray()) ? $selectedOptionPrice : floatval($currentConfigurationPrice * $option->getPricePercental()));
							$option->setPriceOutput($selectedOptions && in_array($option, $selectedOptions->toArray()) ? 0.00 : floatval($currentConfigurationPrice * $option->getPricePercental()));
						}
					/***************************
					 * Calculate STATIC prices *
					 ***************************/
					} else {
						$option->setUnitPrice($option->getPriceInCurrency($this->currency['short'], floatval($this->currency['exchange'])));
						$priceOutput = $package->isMultipleSelect() || !$selectedOptions ? $option->getPriceInCurrency($this->currency['short'], floatval($this->currency['exchange'])) : $option->getPriceInCurrency($this->currency['short']) - $selectedOptions->getFirst()->getPriceInCurrency($this->currency['short'], floatval($this->currency['exchange']));
						$option->setPriceOutput($priceOutput);
					}
					/*****************************************************
					 * Update Package price information & Add to storage *
					 *****************************************************/
					if ($optionIsActive) {
						$package->sumPriceOutput($option->getUnitPrice());
					}
				}
				/*******************************************
				 * process SKU based configurator SPECIALS *
				 *******************************************/
// TODO processing
				/*
				if ($firstRun && $this->cObj->isStaticEcomProductConfigurator() && $package->isSelected() && $this->selectedConfiguration['options']) {
					$selectedOptions = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['options']);
					if ($package->getSelectedOptions()->contains($option)) {

					}
				}
				*/
				$parsedPackages[] = $package->getUid();
			}
		}
	}

	/**
	 * @param float   $floatToFormat
	 * @param boolean $signed
	 *
	 * @return string
	 */
	protected function formatCurrency($floatToFormat = 0.0, $signed = FALSE) {
		$output = number_format($floatToFormat, 2, $this->currency['decimalSeparator'] ?: ',', $this->currency['thousandsSeparator'] ?: '.');
		// Add algebraic sign if positive
		if ($floatToFormat > 0 && $signed) {
			$output = '+' . $output;
		} elseif (number_format($floatToFormat, 2) == 0.00 && $signed) {
			$output = '+' . $output;
			//return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('price.inclusive', 'ecompc');
		}
		if ($this->currency['symbol'] !== '') {
			$currencySeparator = $this->currency['separateCurrency'] ?: ' ';
			if ($this->currency['prependCurrency']) {
				$output = $this->currency['symbol'] . $currencySeparator . $output;
			} else {
				$output .= $currencySeparator . $this->currency['symbol'];
			}
		}
		return $output;
	}

}