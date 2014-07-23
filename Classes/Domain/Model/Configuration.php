<?php
namespace S3b0\Ecompc\Domain\Model;


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
 * Configuration
 */
class Configuration extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * frontendLabel
	 *
	 * @var string
	 */
	protected $frontendLabel = '';

	/**
	 * sku
	 *
	 * @var string
	 */
	protected $sku = '';

	/**
	 * configurationCodeSuffix
	 *
	 * @var string
	 */
	protected $configurationCodeSuffix = '';

	/**
	 * configurationCodePrefix
	 *
	 * @var string
	 */
	protected $configurationCodePrefix = '';

	/**
	 * price
	 *
	 * @var float
	 */
	protected $price = 0.0;

	/**
	 * priceList
	 *
	 * @var string
	 */
	protected $priceList = '';

	/**
	 * Configuration Options (used for article number based configurators)
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option>
	 */
	protected $options = NULL;

	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->options = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Returns the frontendLabel
	 *
	 * @return string $frontendLabel
	 */
	public function getFrontendLabel() {
		return $this->frontendLabel;
	}

	/**
	 * Sets the frontendLabel
	 *
	 * @param string $frontendLabel
	 * @return void
	 */
	public function setFrontendLabel($frontendLabel) {
		$this->frontendLabel = $frontendLabel;
	}

	/**
	 * Returns the sku
	 *
	 * @return string $sku
	 */
	public function getSku() {
		return $this->sku;
	}

	/**
	 * Sets the sku
	 *
	 * @param string $sku
	 * @return void
	 */
	public function setSku($sku) {
		$this->sku = $sku;
	}

	/**
	 * Returns the configurationCodeSuffix
	 *
	 * @return string $configurationCodeSuffix
	 */
	public function getConfigurationCodeSuffix() {
		return $this->configurationCodeSuffix;
	}

	/**
	 * Sets the configurationCodeSuffix
	 *
	 * @param string $configurationCodeSuffix
	 * @return void
	 */
	public function setConfigurationCodeSuffix($configurationCodeSuffix) {
		$this->configurationCodeSuffix = $configurationCodeSuffix;
	}

	/**
	 * Returns the configurationCodePrefix
	 *
	 * @return string $configurationCodePrefix
	 */
	public function getConfigurationCodePrefix() {
		return $this->configurationCodePrefix;
	}

	/**
	 * Sets the configurationCodePrefix
	 *
	 * @param string $configurationCodePrefix
	 * @return void
	 */
	public function setConfigurationCodePrefix($configurationCodePrefix) {
		$this->configurationCodePrefix = $configurationCodePrefix;
	}

	/**
	 * Returns the price
	 *
	 * @return float $price
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * Sets the price
	 *
	 * @param float $price
	 * @return void
	 */
	public function setPrice($price) {
		$this->price = $price;
	}

	/**
	 * Returns the priceList
	 *
	 * @return string $priceList
	 */
	public function getPriceList() {
		$convArray = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($this->priceList);
		return $convArray['data']['sDEF']['lDEF'];
	}

	/**
	 * Sets the priceList
	 *
	 * @param string $priceList
	 * @return void
	 */
	public function setPriceList($priceList) {
		$this->priceList = $priceList;
	}

	/**
	 * Adds a Option
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 * @return void
	 */
	public function addOption(\S3b0\Ecompc\Domain\Model\Option $option) {
		$this->options->attach($option);
	}

	/**
	 * Removes a Option
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $optionToRemove The Option to be removed
	 * @return void
	 */
	public function removeOption(\S3b0\Ecompc\Domain\Model\Option $optionToRemove) {
		$this->options->detach($optionToRemove);
	}

	/**
	 * Returns the options
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option> $options
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * Sets the options
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option> $options
	 * @return void
	 */
	public function setOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $options) {
		$this->options = $options;
	}

}