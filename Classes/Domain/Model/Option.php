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
 * Option
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class Option extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * backendLabel
	 *
	 * @var string
	 */
	protected $backendLabel = '';

	/**
	 * frontendLabel
	 *
	 * @var string
	 */
	protected $frontendLabel = '';

	/**
	 * configurationCodeSegment
	 *
	 * @var string
	 */
	protected $configurationCodeSegment = '';

	/**
	 * image
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $image = NULL;

	/**
	 * hintText
	 *
	 * @var string
	 */
	protected $hintText = '';

	/**
	 * price
	 *
	 * @var float
	 */
	protected $price = 0.0;

	/**
	 * pricePercental
	 *
	 * @var float
	 */
	protected $pricePercental = 0.0;

	/**
	 * priceList
	 *
	 * @var string
	 */
	protected $priceList = '';

	/**
	 * priceOutput
	 *
	 * @var float
	 */
	protected $priceOutput = 0.0;
	/**
	 * Corresponding Package
	 *
	 * @var \S3b0\Ecompc\Domain\Model\Package
	 */
	protected $configurationPackage = NULL;

	/**
	 * Set Dependencies
	 *
	 * @var \S3b0\Ecompc\Domain\Model\Dependency
	 */
	protected $dependency = NULL;

	/**
	 * selected
	 *
	 * @var boolean
	 */
	protected $selected = FALSE;

	/**
	 * persistent
	 *
	 * @var boolean
	 */
	protected $persistent = FALSE;

	/**
	 * Returns the backendLabel
	 *
	 * @return string $backendLabel
	 */
	public function getBackendLabel() {
		return $this->backendLabel;
	}

	/**
	 * Sets the backendLabel
	 *
	 * @param string $backendLabel
	 * @return void
	 */
	public function setBackendLabel($backendLabel) {
		$this->backendLabel = $backendLabel;
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
	 * Returns the configurationCodeSegment
	 *
	 * @return string $configurationCodeSegment
	 */
	public function getConfigurationCodeSegment() {
		return $this->configurationCodeSegment;
	}

	/**
	 * Sets the configurationCodeSegment
	 *
	 * @param string $configurationCodeSegment
	 * @return void
	 */
	public function setConfigurationCodeSegment($configurationCodeSegment) {
		$this->configurationCodeSegment = $configurationCodeSegment;
	}

	/**
	 * Returns the image
	 *
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * Sets the image
	 *
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
	 * @return void
	 */
	public function setImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image) {
		$this->image = $image;
	}

	/**
	 * Returns the hintText
	 *
	 * @return string $hintText
	 */
	public function getHintText() {
		return $this->hintText;
	}

	/**
	 * Sets the hintText
	 *
	 * @param string $hintText
	 * @return void
	 */
	public function setHintText($hintText) {
		$this->hintText = $hintText;
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
	 * Returns the pricePercental
	 *
	 * @return float $pricePercental
	 */
	public function getPricePercental() {
		return $this->pricePercental / 100;
	}

	/**
	 * Sets the pricePercental
	 *
	 * @param float $pricePercental
	 * @return void
	 */
	public function setPricePercental($pricePercental) {
		$this->pricePercental = $pricePercental;
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
	 * Returns the priceOutput
	 *
	 * @return float $priceOutput
	 */
	public function getPriceOutput() {
		return $this->priceOutput;
	}

	/**
	 * Sets the priceOutput
	 *
	 * @param float $priceOutput
	 * @return void
	 */
	public function setPriceOutput($priceOutput) {
		$this->priceOutput = $priceOutput;
	}

	/**
	 * Returns the configurationPackage
	 *
	 * @return \S3b0\Ecompc\Domain\Model\Package $configurationPackage
	 */
	public function getConfigurationPackage() {
		return $this->configurationPackage;
	}

	/**
	 * Sets the configurationPackage
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $configurationPackage
	 * @return void
	 */
	public function setConfigurationPackage(\S3b0\Ecompc\Domain\Model\Package $configurationPackage) {
		$this->configurationPackage = $configurationPackage;
	}

	/**
	 * Returns the dependency
	 *
	 * @return \S3b0\Ecompc\Domain\Model\Dependency $dependency
	 */
	public function getDependency() {
		return $this->dependency;
	}

	/**
	 * Sets the dependency
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Dependency $dependency
	 * @return void
	 */
	public function setDependency(\S3b0\Ecompc\Domain\Model\Dependency $dependency) {
		$this->dependency = $dependency;
	}

	/**
	 * Returns the selected
	 *
	 * @return boolean $selected
	 */
	public function getSelected() {
		return $this->selected;
	}

	/**
	 * Sets the selected
	 *
	 * @param boolean $selected
	 * @return void
	 */
	public function setSelected($selected) {
		$this->selected = $selected;
	}

	/**
	 * Returns the boolean state of selected
	 *
	 * @return boolean
	 */
	public function isSelected() {
		return $this->selected;
	}

	/**
	 * Returns the persistent
	 *
	 * @return boolean $persistent
	 */
	public function getPersistent() {
		return $this->persistent;
	}

	/**
	 * Sets the persistent
	 *
	 * @param boolean $persistent
	 * @return void
	 */
	public function setPersistent($persistent) {
		$this->persistent = $persistent;
	}

	/**
	 * Returns the boolean state of persistent
	 *
	 * @return boolean
	 */
	public function isPersistent() {
		return $this->persistent;
	}

	/**
	 * @param string $currency
	 * @param float  $exchange
	 *
	 * @return float
	 */
	public function getPriceInCurrency($currency = 'default', $exchange = 0.00) {
		if ($currency === 'default')
			return $this->getPrice();

		$priceList = $this->getPriceList();
		$price = strlen($currency) === 3 && array_key_exists($currency, $priceList) ? floatval($priceList[$currency]['vDEF']) : 0.00;

		return $price ?: $this->getPrice() ? $this->getPrice() * $exchange : 0.00;
	}

}