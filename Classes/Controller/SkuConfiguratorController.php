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
		if ( $this->currentPackage instanceof \S3b0\Ecompc\Domain\Model\Package ) {
			$this->currentPackage->setCurrent(TRUE);
			$this->view->assign('options', self::getPackageOptions($this->currentPackage, $this));
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
	 * Fetching options selectable, limited by active options
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package          $package
	 * @param array                                      $selectableOptions
	 * @param \S3b0\Ecompc\Controller\StandardController $controller
	 */
	public static function getSelectableOptions(\S3b0\Ecompc\Domain\Model\Package $package, array &$selectableOptions, \S3b0\Ecompc\Controller\StandardController $controller) {
		// Parse configurations
		$options = array();
		if ( $controller->selectableConfigurations ) {
			foreach ( $controller->selectableConfigurations as $configuration ) {
				if ( $configurationOptions = $configuration->getOptions() ) {
					/** @var \S3b0\Ecompc\Domain\Model\Option $configurationOption */
					foreach ( $configurationOptions as $configurationOption ) {
						if ( $configurationOption->getConfigurationPackage() === $package )
							$options[$configurationOption->getSorting()] = $configurationOption;
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

	public static function getConfigurationData(\S3b0\Ecompc\Controller\StandardController $controller, \S3b0\Ecompc\Domain\Model\Configuration $configuration, $returnArray = FALSE, $loggerUid = 0) {

	}

	/**
	 * Fetching selectable configurations
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|array|null $current actual setting
	 * @return void
	 */
	public function setSelectableConfigurations(&$current = NULL) {
		$current = $this->configurationRepository->findByTtContentUidApplyingSelectedOptions($this->cObj->getUid(), $this->selectedConfiguration['options']);
	}

	/**
	 * autoSetOptions
	 *
	 * @param array $configuration
	 * @return void
	 */
	protected function autoSetOptions(array &$configuration) {
		if ( $packages = $this->cObj->getEcompcPackagesFE() ) {
			/** @var \S3b0\Ecompc\Domain\Model\Package $package */
			foreach ( $packages as $package ) {
				if ( in_array($package->getUid(), $configuration['packages']) )
					continue;

				if ( $packageOptions = self::getPackageOptions($package, $this) ) {
					if ( count($packageOptions) === 1 ) {
						// Add option to NEW package
						$configuration['options'][$packageOptions->getFirst()->getSorting()] = $packageOptions->getFirst()->getUid();
						$configuration['packages'][$package->getUid()] = $package->getUid();
					}
				}
			}
		}
	}

}