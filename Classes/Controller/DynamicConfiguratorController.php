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
 * DynamicConfiguratorController
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class DynamicConfiguratorController extends \S3b0\Ecompc\Controller\StandardController implements \S3b0\Ecompc\GenericConfiguratorControllerInterface {

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
			$this->currentPackage = $package;
			if ( !$package instanceof \S3b0\Ecompc\Domain\Model\Package ) {
				$configurationData = self::getConfigurationData($this->cObj->getEcompcConfigurations()->toArray()[0], $this);
				$this->view->assignMultiple(array(
					'configurationLabel' => $configurationData[0],
					'configurationData' => $configurationData[1],
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
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array|null
	 */
	public static function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package, \S3b0\Ecompc\Controller\StandardController $controller) {
		$packageOptions = array();
		// Fetch selectable options for current package
		self::getSelectableOptions($package, $packageOptions, $controller);
		if ( count($packageOptions) === 0 )
			return NULL;

		// Include pricing for enabled users!
		if ( $controller->pricingEnabled ) {
			$controller->initializeOptions($package);
		}

		return $packageOptions;
	}

	/**
	 * Fetching options selectable
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package          $package
	 * @param array                                      $selectableOptions
	 * @param \S3b0\Ecompc\Controller\StandardController $controller
	 */
	public static function getSelectableOptions(\S3b0\Ecompc\Domain\Model\Package $package, array &$selectableOptions, \S3b0\Ecompc\Controller\StandardController $controller) {
		$options = $controller->optionRepository->findByConfigurationPackage($package); // Set basic settings

		// Run dependency check
		$selectableOptions = array();
		/** @var \S3b0\Ecompc\Domain\Model\Option $option */
		foreach ( $options as $option ) {
			$option->setInConflictWithSelectedOptions(FALSE);
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
	public static function getConfigurationData(\S3b0\Ecompc\Domain\Model\Configuration $configuration, \S3b0\Ecompc\Controller\StandardController $controller) {
		if ( $controller->pricingEnabled ) {
			/** @var \TYPO3\CMS\Fluid\ViewHelpers\S3b0\Financial\CurrencyViewHelper $currencyVH */
			$currencyVH = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\ViewHelpers\\S3b0\\Financial\\CurrencyViewHelper');
		}

		$configurationLabel = $configuration->getFrontendLabel();
		$configurationCode = new \ArrayObject();
		if ( $configuration->hasConfigurationCodePrefix() ) {
			$configurationCode->append(array(
				'Prefix',
				$configuration->getConfigurationCodePrefix(),
				'pkg' => FALSE
			));
		}

		/** @var \S3b0\Ecompc\Domain\Model\Package $package */
		foreach ( $controller->cObj->getEcompcPackages() as $package ) {
			/** NO multipleSelect allowed for dynamic configurators, accordingly skip 'em */
			if ( $package->isMultipleSelect() ) {
				continue;
			}
			/**  */
			if ( !$package->isVisibleInFrontend() ) {
				$configurationCode->append(array(
					$package->getDefaultOption()->getFrontendLabel(),
					$package->getDefaultOption()->getConfigurationCodeSegment(),
					'pkg' => $package->getDefaultOption()->getConfigurationPackage()->getFrontendLabel(),
					TRUE
				));
			} elseif ( $option = $controller->optionRepository->findOptionsByUidList($controller->selectedConfiguration['options'], $package, TRUE) ) {
				/** @var \S3b0\Ecompc\Domain\Model\Option $option */
				$configurationCode->append(array(
					$option->getFrontendLabel(),
					$option->getConfigurationCodeSegment(),
					'pkg' => $option->getConfigurationPackage()->getFrontendLabel(),
					'pkgUid' => $option->getConfigurationPackage()->getUid(),
					'pricing' => !$controller->pricingEnabled ?: $currencyVH->render(
						$controller->currency,
						$option->getPricing($controller->currency),
						2,
						TRUE,
						FALSE,
						$controller->settings['usFormat']
					)
				));
			}
		}

		if ( $configuration->hasConfigurationCodeSuffix() ) {
			$configurationCode->append(array(
				'Suffix',
				$configuration->getConfigurationCodeSuffix(),
				'pkg' => FALSE
			));
		}

		return array(
			$configurationLabel,
			$configurationCode
		);
	}

	/**
	 * Fetching selectable configurations
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|array|null $current actual setting
	 * @return void
	 */
	public function setSelectableConfigurations(&$current = NULL) {
		$current = $current ?: $this->configurationRepository->findByTtContentUid($this->cObj->getUid());
	}

}