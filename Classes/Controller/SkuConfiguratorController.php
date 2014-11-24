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
 * SkuConfiguratorController
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class SkuConfiguratorController extends \S3b0\Ecompc\Controller\StandardController implements \S3b0\Ecompc\GenericConfiguratorControllerInterface {

	public function initializeRequestAction() {
		$this->logConfiguration = $this->configurationRepository->findByTtContentUidApplyingSelectedOptions($this->cObj->getUid(), $this->selectedConfiguration['options'])->getFirst();
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
		parent::indexAction($package);
		if ( $this->progress === 1 ) {
			if ( $this->pricingEnabled ) {
				$this->initializeOptions();
			}
			$this->currentPackage = $package;
			if ( !$package instanceof \S3b0\Ecompc\Domain\Model\Package ) {
				$matchingConfiguration = $this->configurationRepository->findByTtContentUidApplyingSelectedOptions($this->cObj->getUid(), $this->selectedConfiguration['options'])->getFirst();
				$configurationData = self::getConfigurationData($matchingConfiguration, $this);
				$this->view->assignMultiple(array(
					'configurationLabel' => $configurationData[0],
					'configurationData' => $configurationData[2],
					'configurationCode' => $configurationData[1]
				));
			}
		}
		if ( $this->currentPackage instanceof \S3b0\Ecompc\Domain\Model\Package ) {
			$this->currentPackage->setCurrent(TRUE);
			$this->view->assignMultiple(array(
				'options' => self::getPackageOptions($this->currentPackage, $this),
				'currentPackage' => $this->currentPackage
			));
		}
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package          $package
	 * @param \S3b0\Ecompc\Controller\StandardController $controller
	 * @param boolean                                    $availableOnly
	 * @param boolean                                    $includePricing
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array|null
	 */
	public static function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package, \S3b0\Ecompc\Controller\StandardController $controller, $availableOnly = FALSE, $includePricing = TRUE) {
		$packageOptions = array();
		// Fetch selectable options for current package
		self::getSelectableOptions($package, $packageOptions, $controller, $availableOnly);
		if ( count($packageOptions) === 0 )
			return NULL;

		// Include pricing for enabled users!
		if ( $includePricing && $controller->pricingEnabled ) {
			$controller->initializeOptions($package);
		}

		return $packageOptions;
	}

	/**
	 * Fetching options selectable, limited by active options
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package          $package
	 * @param array                                      $selectableOptions
	 * @param \S3b0\Ecompc\Controller\StandardController $controller
	 * @param boolean                                    $availableOnly
	 */
	public static function getSelectableOptions(\S3b0\Ecompc\Domain\Model\Package $package, array &$selectableOptions, \S3b0\Ecompc\Controller\StandardController $controller, $availableOnly = FALSE) {
		// Parse configurations
		$options = array();
		$packageOptions = $controller->optionRepository->findByConfigurationPackage($package);

		if ( $packageOptions && ($selectableConfigurations = self::getSelectableConfigurations($controller)) ) {
			/** @var \S3b0\Ecompc\Domain\Model\Configuration $configuration */
			foreach ( $selectableConfigurations as $configuration ) {
				/** @var \S3b0\Ecompc\Domain\Model\Option $option */
				foreach ( $packageOptions as $option ) {
					if ( $configuration->getOptions()->contains($option) ) {
						if ( !in_array($option, $options) ) {
							$option->setInConflictWithSelectedOptions(self::checkOptionForConflicts($option, $packageOptions, $controller), $controller->currentPackage);
							if ( $availableOnly && $option->isInConflictWithSelectedOptions() )
								continue;
							$options[$option->getSorting()] = $option;
						}
					}
				}
			}
			$options = array_unique($options);
			ksort($options);
		}

		// Run dependency check
		$selectableOptions = array();
		/** @var \S3b0\Ecompc\Domain\Model\Option $option */
		foreach ( $options as $option ) {
			if ( $controller->checkOptionDependencies($option, $controller->selectedConfiguration) ) {
				if ( in_array($option->getUid(), $controller->selectedConfiguration['options']) )
					$option->setActive(TRUE);
				$selectableOptions[] = $option;
			}
		}
	}

	/**
	 * function getConfigurationData
	 *
	 * @param  \S3b0\Ecompc\Domain\Model\Configuration    $configuration
	 * @param  \S3b0\Ecompc\Controller\StandardController $controller
	 *
	 * @return string
	 */
	public static function getConfigurationData(\S3b0\Ecompc\Domain\Model\Configuration $configuration = NULL, \S3b0\Ecompc\Controller\StandardController $controller) {
		$options = new \ArrayObject();

		if ( !$configuration instanceof \S3b0\Ecompc\Domain\Model\Configuration ) {
			return $options;
		}

		if ( $controller->pricingEnabled ) {
			/** @var \TYPO3\CMS\Fluid\ViewHelpers\S3b0\Financial\CurrencyViewHelper $currencyVH */
			$currencyVH = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\ViewHelpers\\S3b0\\Financial\\CurrencyViewHelper');
		}

		$price = !$controller->pricingEnabled ?: $controller->cObj->getPrice($controller->currency);
		/** @var \S3b0\Ecompc\Domain\Model\Package $package */
		foreach ( $controller->cObj->getEcompcPackagesFE() as $package ) {
			/** NO multipleSelect allowed for dynamic configurators, accordingly skip 'em */
			if ( $package->isMultipleSelect() ) {
				if ( $packageOptions = $controller->optionRepository->findOptionsByUidList($controller->selectedConfiguration['options'], $package) ) {
					$labelsAndSegments = array();
					$pricing = 0.0;
					/** @var \S3b0\Ecompc\Domain\Model\Option $option */
					foreach ( $packageOptions as $option ) {
						$labelsAndSegments['labels'][] = $option->getFrontendLabel();
						$labelsAndSegments['segments'][] = $option->getConfigurationCodeSegment();
						$pricing += $option->getPricing($controller->currency);
					}
					$options->append(array(
						implode(', ', $labelsAndSegments['labels']),
						implode(', ', $labelsAndSegments['segments']),
						'pkg' => $option->getConfigurationPackage()->getFrontendLabel(),
						'pkgUid' => $option->getConfigurationPackage()->getUid(),
						'pricing' => !$controller->pricingEnabled ?: $currencyVH->render(
							$controller->currency,
							$pricing,
							2,
							TRUE,
							FALSE,
							$controller->settings['usFormat']
						)
					));
					$price += $pricing;
				}
			} elseif ( $option = $controller->optionRepository->findOptionsByUidList($controller->selectedConfiguration['options'], $package, TRUE) ) {
				/** @var \S3b0\Ecompc\Domain\Model\Option $option */
				$options->append(array(
					$option->getFrontendLabel(),
					$option->getConfigurationCodeSegment(),
					'pkg' => $option->getConfigurationPackage()->getFrontendLabel(),
					'pkgUid' => $option->getConfigurationPackage()->getUid(),
					'pricing' => !$controller->pricingEnabled ?: $currencyVH->render(
						$controller->currency,
						$option->getPricing($controller->currency, $price),
						2,
						TRUE,
						FALSE,
						$controller->settings['usFormat']
					)
				));
				$price += $option->getPricing($controller->currency, $price);
			}
		}

		return array(
			$configuration->getFrontendLabel(),
			$configuration->getSku(),
			$options
		);
	}

	public static function checkOptionForConflicts(\S3b0\Ecompc\Domain\Model\Option $option, \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $packageOptions, \S3b0\Ecompc\Controller\StandardController $controller) {
		$excludeUidList = array();
		/** In case of first package skip this step */
		if ( count($controller->selectedConfiguration['packages']) > 1 ) {
			/**
			 * Exclude same packages options
			 * @var \S3b0\Ecompc\Domain\Model\Option $packageOption
			 */
			foreach ( $packageOptions as $packageOption ) {
				$excludeUidList[] = $packageOption->getUid();
			}
		}
		return $controller->configurationRepository->checkOptionForConflictsTtContentUidApplyingSelectedOptions($option, $excludeUidList, $controller->cObj->getUid(), $controller->selectedConfiguration['options']);
	}

	/**
	 * Fetching selectable configurations
	 *
	 * @param \S3b0\Ecompc\Controller\StandardController                    $controller
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|array|null $current actual setting
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function getSelectableConfigurations(\S3b0\Ecompc\Controller\StandardController $controller, &$current = NULL) {
		return $current ?: $controller->configurationRepository->findByTtContentUid($controller->cObj->getUid());
	}

}