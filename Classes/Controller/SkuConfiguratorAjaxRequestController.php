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
 * SkuConfiguratorAjaxRequestController
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class SkuConfiguratorAjaxRequestController extends \S3b0\Ecompc\Controller\AjaxRequestController {

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 */
	public function indexAction(\S3b0\Ecompc\Domain\Model\Package $package = NULL) {
		$packages = $this->initializePackages(TRUE);
		if ( $package instanceof \S3b0\Ecompc\Domain\Model\Package ) {
			$this->currentPackage = $package;
		}
		if ( $this->progress === 1 ) {
			$this->currentPackage = $package;
			if ( !$package instanceof \S3b0\Ecompc\Domain\Model\Package ) {
				$matchingConfiguration = $this->configurationRepository->findByTtContentUidApplyingSelectedOptions($this->cObj->getUid(), $this->selectedConfiguration['options'])->getFirst();
				$this->view->assignMultiple(array(
					'configurationData' => \S3b0\Ecompc\Controller\SkuConfiguratorController::getConfigurationData($matchingConfiguration, $this), // Get configuration code
					'showResult' => TRUE,
					'requestLink' => $this->uriBuilder
						->setArguments(array(
							'tx_' . $this->request->getControllerExtensionKey() . '_configurator_sku' => array(
								'configuration' => $matchingConfiguration,
								'action' => 'request',
								'controller' => 'SkuConfigurator'
							)
						))
						->setUseCacheHash(FALSE)
						->setCreateAbsoluteUri(TRUE)
						->build()
				));
			}
		}
		if ( $this->currentPackage instanceof \S3b0\Ecompc\Domain\Model\Package ) {
			$this->currentPackage->setCurrent(TRUE);
			/** pre-parse hintText since not done by rendering process */
			$this->currentPackage->setHintText($this->configurationManager->getContentObject()->parseFunc($this->currentPackage->getHintText(), array(), '< lib.parseFunc_RTE'));
			$this->view->assignMultiple(array(
				'options' => $this->getPackageOptions($this->currentPackage),
				'currentPackage' => $this->currentPackage
			));
		}

		$this->view->assignMultiple(array(
			'packages' => $packages,
			'progress' => $this->progress
		));

		if ( $this->isPricingEnabled() ) {
			$this->view->assign('pricing', $this->getConfigurationPrice(NULL, TRUE));
		}
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @param boolean                           $availableOnly
	 * @param boolean                           $includePricing
	 *
	 * @return array
	 */
	public function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package, $availableOnly = FALSE, $includePricing = TRUE) {
		$return = array();
		$configurationArray = $this->getFeSession()->get($this->configurationSessionStorageKey) ?: array(
			'options' => array(),
			'packages' => array()
		);
		$pricing = $this->getConfigurationPrice($package);
		if ( $options = \S3b0\Ecompc\Controller\SkuConfiguratorController::getPackageOptions($package, $this, $availableOnly, $includePricing) ) {
			/** @var \S3b0\Ecompc\Domain\Model\Option $option */
			foreach ( $options as $option ) {
				if ( $option->getConfigurationPackage()->isPercentPricing() ) {
					$return[] = $option->getSummaryForJSONView($configurationArray['options'], $this->isPricingEnabled(), $this->getCurrency(), $pricing, $this->settings['usFormat']);
				} else {
					$return[] = $option->getSummaryForJSONView($configurationArray['options'], $this->isPricingEnabled(), $this->getCurrency(), $pricing, $this->settings['usFormat']);
				}
			}
		}

		return $return;
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

				if ( $packageOptions = self::getPackageOptions($package, TRUE, FALSE) ) {
					if ( count($packageOptions) === 1 ) {
						// Add option to NEW package
						$configuration['options'][$packageOptions[0]['sorting']] = $packageOptions[0]['uid'];
						$configuration['packages'][$package->getUid()] = $package->getUid();
					}
				}
			}
		}
	}

}