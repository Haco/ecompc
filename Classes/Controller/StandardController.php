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
	 * @var \S3b0\Ecompc\Domain\Model\Package|null
	 */
	protected $currentPackage = NULL;

	/**
	 * @var float Progress indicator for progress bar and display
	 */
	protected $progress = 0.0;

	/**
	 * @var array
	 */
	protected $selectedConfiguration = array();

	/**
	 * @var array (base price, configuration price, config price BEFORE breakpoint, config price AT breakpoint, config price AFTER breakpoint)
	 */
	protected $selectedConfigurationPrice = array(0.0, 0.0, 0.0, 0.0, 0.0);

	/**
	 * @var string
	 */
	protected $configurationSessionStorageKey = 'configurator-';

	/**
	 * @var boolean
	 */
	protected $pricingEnabled = FALSE;

	/**
	 * @var \S3b0\Ecompc\Domain\Model\Currency
	 */
	protected $currency = NULL;

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
	 * currencyRepository
	 *
	 * @var \S3b0\Ecompc\Domain\Repository\CurrencyRepository
	 * @inject
	 */
	protected $currencyRepository;

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
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function initializeAction() {
		// Fetch content object (tt_content)
		$this->cObj = $this->cObj ?: $this->contentRepository->findByUid($this->configurationManager->getContentObject()->data['uid']);
		if ( !$this->cObj instanceof \S3b0\Ecompc\Domain\Model\Content )
			$this->throwStatus(404, \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('404.no_cObj', $this->extensionName));
		// Frontend-Session
		$this->getFeSession()->setStorageKey('ext-' . $this->request->getControllerExtensionKey());
		\S3b0\Ecompc\Utility\Div::setPriceHandling($this);
		// Add cObj-pid to configurationSessionStorageKey to make it unique in sessionStorage
		$this->configurationSessionStorageKey .= $this->cObj->getPid();
		// Get current configuration (Array: options=array(options)|packages=array(package => option(s)))
		$this->selectedConfiguration = $this->getFeSession()->get($this->configurationSessionStorageKey) ?: array(
			'options' => array(),
			'packages' => array()
		);
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
			'pid' => $GLOBALS['TSFE']->id,
			'cObj' => $this->cObj->_getProperty('_localizedUid'),
			'sys_language_uid' => (int) $GLOBALS['TSFE']->sys_language_content
		));
		if ( $this->isPricingEnabled() ) {
			$this->view->assignMultiple(array(
				'pricingEnabled' => $this->isPricingEnabled(), // checks whether price labels are displayed or not!
				'currency' => $this->getCurrency(), // fetch currency TS
				'pricing' => $this->selectedConfigurationPrice // current configuration price
			));
		}
	}

	/**
	 * Initializes the controller before invoking the indexAction method.
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function initializeIndexAction() {
		/**
		 * Set environment variables on non Ajax actions
		 */
		if ( substr($this->request->getControllerName(), -11) !== 'AjaxRequest' )
			\S3b0\Ecompc\Utility\Div::setEnvironment(CoreUtility\GeneralUtility::getApplicationContext()->isDevelopment());
		/**
		 * Check for currency when distributor is logged in
		 */
		if ( $this->isPricingEnabled() && !$this->getCurrency() instanceof \S3b0\Ecompc\Domain\Model\Currency ) {
			$currency = CoreUtility\GeneralUtility::_GP('currency');
			if ( !$this->getFeSession()->get('currency') && !$currency ) {
				$this->forward('selectRegion', 'Standard'); // Add redirect for region selection - influencing currency display
			} elseif ( !$this->getFeSession()->get('currency') && $currency && ($record = $this->getCurrencyRepository()->findByUid($currency)) ) {
				$this->getFeSession()->store('currency', $currency); // Store region selection
				$this->setCurrency($record);
				$this->redirectToPage();
			}
		}
		// Get configuration price
		$this->selectedConfigurationPrice = $this->isPricingEnabled() ? $this->getConfigurationPrice() : array(0.0, 0.0, 0.0, 0.0, 0.0);
		/**
		 * Set required parameter if uid is transmitted only (AJAX requests)
		 */
		if ( $this->request->hasArgument('package') && !$this->request->getArgument('package') instanceof \S3b0\Ecompc\Domain\Model\Package ) {
			$this->request->setArgument(
				'package',
				CoreUtility\MathUtility::canBeInterpretedAsInteger($this->request->getArgument('package')) ? $this->packageRepository->findByUid($this->request->getArgument('package')) : NULL
			);
		}
	}

	/**
	 * action index
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 *
	 * @return void
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function indexAction(\S3b0\Ecompc\Domain\Model\Package $package = NULL) {
		/**
		 * Avoid duplicate cObjects containing current plugin
		 */
		if ( $this->contentRepository->hasDuplicateContentElementsOfConfiguratorTypes() )
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.dbl_config_found', $this->extensionName));

		/**
		 * Failure if no configuration has been found
		 */
		if ( !$this->cObj->getEcompcConfigurations() )
			$this->throwStatus(404, ExtbaseUtility\LocalizationUtility::translate('404.no_config_found', $this->extensionName) . ' [ttc:#' . $this->cObj->_getProperty('_localizedUid') . '@pid:#' . $this->cObj->getPid() . ']');

		$this->currentPackage = $package;
		$packages = $this->initializePackages(TRUE);

		$this->view->assignMultiple(array(
			'instructions' => $this->cObj->getBodytext(),
			'packages' => $packages,
			'progress' => $this->progress
		));
	}

	/**
	 * action setOption
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @param boolean                          $unset           Identifier to unset option!
	 *
	 * @return void
	 */
	public function setOptionAction(\S3b0\Ecompc\Domain\Model\Option $option, $unset = FALSE) {
		$configuration = $this->selectedConfiguration;

		// Modify (options of) package that already EXISTS
		if ( in_array($option->getConfigurationPackage()->getUid(), $configuration['packages']) ) {
			// For packages NOT supporting multipleSelect unset current selection
			if ( !$option->getConfigurationPackage()->isMultipleSelect() ) {
				// Remove all options + package from configuration array
				$configuration['options'] = array_diff($configuration['options'], $this->optionRepository->getPackageOptionUidList($option->getConfigurationPackage()));
				$configuration['packages'] = array_diff($configuration['packages'], array($option->getConfigurationPackage()->getUid()));
			}
			// Remove single option from configuration array if 'unset'-flag is ON
			if ( $unset ) {
				$configuration['options'] = array_diff($configuration['options'], array($option->getUid()));
				if ( !count(array_intersect($configuration['options'], $this->optionRepository->getPackageOptionUidList($option->getConfigurationPackage()))) ) {
					unset($configuration['packages'][$option->getConfigurationPackage()->getUid()]);
				}
			} else {
				// Add options to package selection if 'unset'-flag is OFF
				$configuration['options'][$option->getSorting()] = $option->getUid();
				$configuration['packages'][$option->getConfigurationPackage()->getUid()] = $option->getConfigurationPackage()->getUid();
			}
		} else {
			// Add options to NEW package if 'unset'-flag is OFF
			if ( !$unset ) {
				$configuration['options'][$option->getSorting()] = $option->getUid();
				$configuration['packages'][$option->getConfigurationPackage()->getUid()] = $option->getConfigurationPackage()->getUid();
			}
		}

		// Add or remove active option to selection
		$unset ? (($key = array_search($option->getUid(), $configuration['options'])) !== FALSE ? $configuration['options'][$key] : '') : array_push($configuration['options'], $option->getUid());
		// Kill duplicates
		$configuration['options'] = array_unique($configuration['options']);
		$configuration['packages'] = array_unique($configuration['packages']);
		// Run the dependency check if other packages next to current got options chosen
		/** @var integer $uid */
		foreach ( (array) $configuration['options'] as $uid ) {
			/** @var \S3b0\Ecompc\Domain\Model\Option $selectedOption */
			$selectedOption = $this->optionRepository->findByUid($uid);
			if ( !$this->checkOptionDependencies($selectedOption, $configuration) ) {
				$configuration['options'] = array_diff($configuration['options'], $option->getConfigurationPackage()->isMultipleSelect() ? $this->optionRepository->getPackageOptionUidList($selectedOption->getConfigurationPackage()) : array($selectedOption->getUid()));
				$configuration['packages'] = array_diff($configuration['packages'], array($selectedOption->getConfigurationPackage()->getUid()));
			}
		}

#		// Prepared autoFill. Still some issues, especially regarding traversing as of dependency checks!
#		if ( $this->settings['auto_set'] )
#			$this->autoSetOptions($configuration);
		$this->getFeSession()->store($this->configurationSessionStorageKey, $configuration); // Store configuration in fe_session_data
	}

	/**
	 * action reset
	 * Resets the configurator!
	 *
	 * @return void
	 */
	public function resetAction() {
		$this->getFeSession()->store($this->configurationSessionStorageKey, array());
		$this->redirectToPage();
	}

	/**
	 * action request
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
	 * @return void
	 */
	public function requestAction() {
		/** @var \S3b0\Ecompc\Domain\Model\Logger $logger */
		$logger = $this->objectManager->get('S3b0\\Ecompc\\Domain\\Model\\Logger');
		$logger->setSelectedConfiguration($this->selectedConfiguration)
			->setIp(CoreUtility\GeneralUtility::getIndpEnv('REMOTE_ADDR'), $this->settings['log']['ipParts'])
			->setConfiguration($this->cObj->getEcompcConfigurations()->toArray()[0]);
		if ( $GLOBALS['TSFE']->loginUser ) {
			$logger->setFeUser($this->frontendUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']));
			if ( $this->isPricingEnabled() ) {
				$pricing = $this->getConfigurationPrice();
				$logger->setCurrency($this->getCurrency())->setPrice($pricing[1]);
			}
		}

		// Write to DB and persist to obtain a uid for current log
		$this->loggerRepository->add($logger);
		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
		$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$persistenceManager->add($logger);
		$persistenceManager->persistAll();
		/**
		 * @var array $data
		 * @todo fit to new return value for writing correct values into db!
		 */
		$data = $this->getConfigurationData($this->cObj->getEcompcConfigurations()->toArray()[0], $this);
#		$this->getFeSession()->store($this->configurationSessionStorageKey, array()); // Unset configuration to avoid multiple submit provided by back button!
		$code = '';
		foreach ( $data[1] as $codeSegmentData ) {
			$code .= $codeSegmentData[1];
		}
		$addParams = sprintf($this->settings['requestForm']['additionalParamsQueryString'], $code, $data[0], $logger->getUid());

		// Build link & redirect
		$linkConfiguration = array(
			'returnLast' => 'url',
			'parameter' => $this->settings['requestForm']['pid'],
			'additionalParams' => $addParams . '&L=' . $GLOBALS['TSFE']->sys_language_content,
			'useCacheHash' => FALSE,
			'addQueryString' => TRUE,
			'addQueryString.' => array(
				'method' => 'GET,POST',
				'exclude' => 'tx_ecompc_configurator'
			)
		);

		$this->redirectToUri($this->configurationManager->getContentObject()->typoLink('', $linkConfiguration), 0, 301);
	}

	/**
	 * action selectRegion
	 *
	 * @return void
	 */
	public function selectRegionAction() {
		if ( $this->getFeSession()->get('currency') )
			$this->redirectToPage();

		$this->view->assign('currencies', $this->getCurrencyRepository()->findAll());
	}

	/**********************************
	 ******** NON-ACTION METHODS ******
	 **********************************/

	/**
	 * Redirects to current page without any params or CacheHash values!
	 *
	 * @param integer $pid
	 * @param array   $arguments
	 * @param boolean $useCacheHash
	 * @param boolean $addQueryString
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function redirectToPage($pid = NULL, $arguments = array(), $useCacheHash = FALSE, $addQueryString = TRUE) {
		if ( !$this->request instanceof \TYPO3\CMS\Extbase\Mvc\Web\Request ) {
			throw new \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException('redirect() only supports web requests.', 1220539734);
		}

		$arguments['L'] = $GLOBALS['TSFE']->sys_language_content;
		if ( CoreUtility\GeneralUtility::getIndpEnv('TYPO3_SSL') ) {
			$this->uriBuilder->setAbsoluteUriScheme('https');
		}
		$uri = $this->uriBuilder
			->reset()
			->setTargetPageUid($pid)
			->setUseCacheHash($useCacheHash)
			->setArguments($arguments)
			->setCreateAbsoluteUri(TRUE)
			->setAddQueryString($addQueryString)
			->setArgumentsToBeExcludedFromQueryString(array(
				'tx_' . $this->request->getControllerExtensionKey() . '_' . $this->request->getPluginName()
			));

		if ( CoreUtility\GeneralUtility::getIndpEnv('TYPO3_SSL') ) {
			$this->uriBuilder->setAbsoluteUriScheme('https');
		}

		$this->redirectToUri($uri->build(), 0, 301);
	}

	/**
	 * Checks an option against its dependencies. Returns TRUE if option IS NOT prohibited!
	 *
	 * @param  \S3b0\Ecompc\Domain\Model\Option $option
	 * @param  array                            $configuration
	 *
	 * @return boolean
	 */
	protected function checkOptionDependencies(\S3b0\Ecompc\Domain\Model\Option $option, array $configuration) {
		$check = TRUE;
		if ( !$option->getDependency() )
			return $check;

		if ( $dependency = $option->getDependency() ) {
			if ( $packages = $option->getDependency()->getPackages() ) {
				$checkOptionAgainstDependencies = array();
				/** @var \S3b0\Ecompc\Domain\Model\Package $package */
				foreach ( $packages as $package ) {
					if ( !in_array($package->getUid(), $configuration['packages']) )
						continue;
					if ( $selectedPackageOptions = $this->optionRepository->findOptionsByUidList($configuration['options'], $package) ) {
						/** @var \S3b0\Ecompc\Domain\Model\Option $selected */
						foreach ( $selectedPackageOptions as $selected ) {
							// @modes {0 : explicit deny, 1 : explicit allow}
							$checkOptionAgainstDependencies[] = $dependency->getMode() === 0 ? !$dependency->getPackageOptions($package)->contains($selected) : $dependency->getPackageOptions($package)->contains($selected);
						}
					}
				}
				$check = !in_array(FALSE, $checkOptionAgainstDependencies);
			}
		}

		return $check;
	}

	/**
	 * Calculates the price for configurations already during configuration process
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $breakPoint
	 * @param boolean                           $render
	 * @param integer                           $tellMeWhatToRender Array pointer to enable rendering of several values calculated [0-4]
	 *
	 * @return array
	 */
	protected function getConfigurationPrice(\S3b0\Ecompc\Domain\Model\Package $breakPoint = NULL, $render = FALSE, $tellMeWhatToRender = 1) {
		/**
		 * Fetch parent on localizations
		 *
		 * @var \S3b0\Ecompc\Domain\Model\Content $cObj
		 */
		$cObj = $this->cObj->_hasProperty('_languageUid') && $this->cObj->_getProperty('_languageUid') ? $this->contentRepository->findByUid($this->cObj->getUid()) : $this->cObj;
		$basePrice = $cObj->getPrice($this->getCurrency());
		if ( !$basePrice ) return array(0.0, 0.0);

		// Get configuration price
		$configPrice = $basePrice;
		$configPriceBeforeBreakpoint = 0.0;
		$configPriceAtBreakpoint = 0.0;
		$configPriceAfterBreakpoint = 0.0;
		$breakPointReached = FALSE;
		if ( count($this->selectedConfiguration['options']) && ($packages = $this->cObj->getEcompcPackages()) ) {
			/** @var \S3b0\Ecompc\Domain\Model\Package $package */
			foreach ( $packages as $package ) {
				if ( $breakPoint && $package === $breakPoint ) {
					$configPriceBeforeBreakpoint = $configPrice;
				}
				if ( $options = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['options'], $package) ) {
					/** @var \S3b0\Ecompc\Domain\Model\Option $option */
					foreach ( $options as $option ) {
						$configPrice += $option->getPricing($this->getCurrency(), $configPrice);
						if ( substr($this->request->getControllerName(), 0, 7) === 'Dynamic' ) break;
					}
				}
				if ( $breakPoint && $breakPointReached ) {
					$configPriceAfterBreakpoint = $configPrice;
				}
				if ( $breakPoint && $package === $breakPoint ) {
					$configPriceAtBreakpoint = $configPrice;
					$breakPointReached = TRUE;
				}
			}
			if ( $breakPoint && $breakPointReached && $configPriceAfterBreakpoint === 0.0 ) {
				$configPriceAfterBreakpoint = $configPrice;
			}
		}

		$returnArray = array($basePrice, $configPrice, $configPriceBeforeBreakpoint, $configPriceAtBreakpoint, $configPriceAfterBreakpoint);

		if ( $render && $this->isPricingEnabled() && ($tellMeWhatToRender >= 0 && $tellMeWhatToRender <= 4) ) {
			/** @var \TYPO3\CMS\Fluid\ViewHelpers\S3b0\Financial\CurrencyViewHelper $currencyVH */
			$currencyVH = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\ViewHelpers\\S3b0\\Financial\\CurrencyViewHelper');
			return $currencyVH->render(
				$this->getCurrency(),
				$returnArray[$tellMeWhatToRender],
				2,
				FALSE
			);
		}

		return $returnArray;
	}

	/**
	 * Initialize Options
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 */
	protected function initializeOptions(\S3b0\Ecompc\Domain\Model\Package $package = NULL) {
		if ( $package instanceof \S3b0\Ecompc\Domain\Model\Package ) {
			$options = $this->optionRepository->findByConfigurationPackage($package);
		} elseif ( $this->request->hasArgument('package') && ($this->request->getArgument('package') instanceof \S3b0\Ecompc\Domain\Model\Package || CoreUtility\MathUtility::canBeInterpretedAsInteger($this->request->getArgument('package'))) ) {
			/** @var \S3b0\Ecompc\Domain\Model\Package $package */
			$package = $this->request->getArgument('package') instanceof \S3b0\Ecompc\Domain\Model\Package ? $this->request->getArgument('package') : $this->packageRepository->findByUid(CoreUtility\MathUtility::convertToPositiveInteger($this->request->getArgument('package')));
			$options = $this->optionRepository->findByConfigurationPackage($package);
		} else {
			$options = $this->optionRepository->findAll();
		}
		$activeOptions = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['options'], $package);
		if ( $options instanceof \TYPO3\CMS\Extbase\Persistence\QueryResultInterface ) {
			$parsedPackages = array();
			/** @var \S3b0\Ecompc\Domain\Model\Option $option */
			foreach ( $options as $option ) {
				/** @var \S3b0\Ecompc\Domain\Model\Package $package */
				$package = $option->getConfigurationPackage();
				$optionIsActive = in_array($option->getUid(), $this->selectedConfiguration['options']);
				/**************************************************
				 * Process pricing | Set corresponding properties *
				 **************************************************/
				if ( $this->isPricingEnabled() && $this->cObj->getEcompcPricing() ) {
					/*****************************************************************************************
					 * Calculate PERCENT price [working on packages WITHOUT multipleSelect() flag set ONLY!] *
					 *****************************************************************************************/
					if ( $package->isPercentPricing() && !$package->isMultipleSelect() ) {
						$currentConfigurationPrice = $this->getConfigurationPrice()[1];
						$configurationPriceExcludingCurrentOption = $package->hasActiveOptions() ? floatval($currentConfigurationPrice / ($this->optionRepository->findOptionsByUidList($this->selectedConfiguration['options'], $package, TRUE)->getPricePercental() + 1)) : 0.0;
						$optionPrice = floatval($currentConfigurationPrice - $configurationPriceExcludingCurrentOption);
						if ( in_array($package->getUid(), $this->selectedConfiguration['packages']) && !in_array($option->getUid(), $this->selectedConfiguration['options']) ) {
							$option->setUnitPrice($optionIsActive ? $optionPrice : floatval($configurationPriceExcludingCurrentOption * $option->getPricePercental()));
							$option->setPriceOutput($optionIsActive ? $optionPrice : floatval($configurationPriceExcludingCurrentOption * $option->getPricePercental() - $optionPrice));
						} else {
							$option->setUnitPrice(in_array($option->getUid(), $this->selectedConfiguration['options']) ? $optionPrice : $option->getPricing($this->getCurrency(), $currentConfigurationPrice));
							$option->setPriceOutput(in_array($option->getUid(), $this->selectedConfiguration['options']) ? 0.00 : $option->getPricing($this->getCurrency(), $currentConfigurationPrice));
						}
					/***************************
					 * Calculate STATIC prices *
					 ***************************/
					} else {
						$option->setUnitPrice($option->getPricing($this->getCurrency()));
						$priceOutput = $package->isMultipleSelect() || !$activeOptions ? $option->getUnitPrice($this->getCurrency()) : $option->getPricing($this->getCurrency()) - $activeOptions->getFirst()->getPricing($this->getCurrency());
						$option->setPriceOutput($priceOutput);
					}
				}
				/*******************************************
				 * process SKU based configurator SPECIALS *
				 *******************************************/
				// TODO processing
				/*
				if ( $dryRun && substr($this->request->getControllerName(), 0, 15) === 'SkuConfigurator' && $package->hasActiveOptions() && $this->selectedConfiguration['options'] ) {
					$activeOptions = $this->optionRepository->findOptionsByUidList($this->selectedConfiguration['options']);
					if ( $package->getActiveOptions()->contains($option) ) {

					}
				}
				*/
				$parsedPackages[] = $package->getUid();
			}
		}
	}

	/**
	 * @param boolean $return
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage|null|void
	 */
	public function initializePackages($return = FALSE) {
		$packages = NULL;

		// Fetch packages | Set (in)active states
		if ( $packages = $this->cObj->getEcompcPackages() ) {
			$isActive = FALSE; // Set package state
			$prev = NULL;      // Set previous package (next as of array_reverse)
			$cycle = 1;        // Count loop cycles
			$invisible = 0;
			/** @var \S3b0\Ecompc\Domain\Model\Package $package */
			foreach ( array_reverse($packages->toArray()) as $package ) {
				/**
				 * Handle with 'invisible' packages
				 */
				if ( !$package->isVisibleInFrontend() ) {
					if ( $package->getDefaultOption() instanceof \S3b0\Ecompc\Domain\Model\Option ) {
						$this->selectedConfiguration['packages'][$package->getUid()] = $package->getUid();
						$this->selectedConfiguration['options'][$package->getDefaultOption()->getSorting()] = $package->getDefaultOption()->getUid();
						$package->addActiveOption($package->getDefaultOption());
					} else {
						$packages->detach($package);
					}
					$invisible++;
					$cycle++;
					continue;
				}
				/**
				 * Check for active packages
				 */
				if ( in_array($package->getUid(), $this->selectedConfiguration['packages']) ) {
					$package->setActiveOptions(array_intersect($this->selectedConfiguration['options'], $this->optionRepository->getPackageOptionUidList($package)));
					if ( $this->isPricingEnabled() )
						$package->setPriceOutput($this->optionRepository->findOptionsByUidList($this->selectedConfiguration['options'], $package), $this->getCurrency());
					if ( !$isActive ) {
						$isActive = TRUE;
						if ( $prev instanceof \S3b0\Ecompc\Domain\Model\Package ) {
							$prev->setActive(TRUE);
							$this->currentPackage = $this->currentPackage ?: $prev;
						}
					}
				}
				// Initially use first package
				if ( $cycle === $packages->count() && !$this->currentPackage instanceof \S3b0\Ecompc\Domain\Model\Package ) {
					$isActive = TRUE;
					$this->currentPackage = $this->currentPackage ?: $package;
				}
				$package->setActive($isActive);
				// Fallback to first package wherein no option is active
				if ( $package->isActive() && !$package->hasActiveOptions() ) {
					if ( !$this->currentPackage instanceof \S3b0\Ecompc\Domain\Model\Package ) {
						$this->currentPackage = $package;
					} elseif ( $this->currentPackage instanceof \S3b0\Ecompc\Domain\Model\Package && $this->currentPackage !== $package ) {
						$this->currentPackage = $package;
					}
				}
				/** @var \S3b0\Ecompc\Domain\Model\Package $prev */
				$prev = $package;
				$cycle++;
			}
			if ( !$isActive ) {
				$package->setActive(TRUE);
			}
			// Get progress state update (ratio of active to visible packages) => float from 0 to 1 (*100 = %)
			$this->progress = ( count($this->selectedConfiguration['packages']) - $invisible ) / ( $packages->count() - $invisible );
		}

		return $return ? $packages : NULL;
	}

	/**
	 * @return array
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @param array $settings
	 */
	public function setSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @return boolean
	 */
	public function isPricingEnabled() {
		return $this->pricingEnabled;
	}

	/**
	 * @param boolean $showPriceLabels
	 */
	public function setPricingEnabled($showPriceLabels) {
		$this->pricingEnabled = $showPriceLabels;
	}

	/**
	 * @return \S3b0\Ecompc\Domain\Model\Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Currency $currency
	 */
	public function setCurrency(\S3b0\Ecompc\Domain\Model\Currency $currency = NULL) {
		$this->currency = $currency;
	}

	/**
	 * @return \S3b0\Ecompc\Domain\Repository\CurrencyRepository
	 */
	public function getCurrencyRepository() {
		return $this->currencyRepository;
	}

	/**
	 * @return \S3b0\Ecompc\Domain\Session\FrontendSessionHandler
	 */
	public function getFeSession() {
		return $this->feSession;
	}

}