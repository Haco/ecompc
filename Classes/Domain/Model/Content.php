<?php
namespace S3b0\Ecompc\Domain\Model;


/***************************************************************
 * Copyright notice
 *
 * (c) 2012 Klaus Heuer <klaus.heuer@t3-developer.com>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Content
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class Content extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * @var string
	 */
	protected $bodytext;

	/**
	 * @var int
	 */
	protected $ecompcType = 0;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Package>
	 */
	protected $ecompcPackages = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration>
	 */
	protected $ecompcConfigurations = NULL;

	/**
	 * ecompcBasePrice
	 *
	 * @var float
	 */
	protected $ecompcBasePrice = 0.0;

	/**
	 * ecompcBasePriceList
	 *
	 * @var string
	 */
	protected $ecompcBasePriceList = '';

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
		$this->ecompcPackages = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->ecompcConfigurations = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * @return string $bodytext
	 */
	public function getBodytext() {
		return $this->bodytext;
	}

	/**
	 * @return int $ecompcType
	 */
	public function getEcompcType() {
		return $this->ecompcType;
	}

	/**
	 * @param int $ecompcType
	 * @return void
	 */
	public function setEcompcType($ecompcType) {
		$this->ecompcType = $ecompcType;
	}

	/**
	 * Adds a Package
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @return void
	 */
	public function addEcompcPackage(\S3b0\Ecompc\Domain\Model\Package $package) {
		$this->ecompcPackages->attach($package);
	}

	/**
	 * Removes a Package
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @return void
	 */
	public function removeEcompcPackage(\S3b0\Ecompc\Domain\Model\Package $package) {
		$this->ecompcPackages->detach($package);
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Package> $ecompcPackages
	 */
	public function getEcompcPackages() {
		return $this->ecompcPackages;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Package> $ecompcPackages
	 * @return void
	 */
	public function setEcompcPackages($ecompcPackages) {
		$this->ecompcPackages = $ecompcPackages;
	}

	/**
	 * Adds a Configuration
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 * @return void
	 */
	public function addEcompcConfiguration(\S3b0\Ecompc\Domain\Model\Configuration $configuration) {
		$this->ecompcConfigurations->attach($configuration);
	}

	/**
	 * Removes a Configuration
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 * @return void
	 */
	public function removeEcompcConfiguration(\S3b0\Ecompc\Domain\Model\Configuration $configuration) {
		$this->ecompcConfigurations->detach($configuration);
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration> $ecompcConfigurations
	 */
	public function getEcompcConfigurations() {
		return $this->ecompcConfigurations;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration> $ecompcConfigurations
	 */
	public function setEcompcConfigurations($ecompcConfigurations) {
		$this->ecompcConfigurations = $ecompcConfigurations;
	}

	/**
	 * Returns the ecompcBasePrice
	 *
	 * @return float $ecompcBasePrice
	 */
	public function getEcompcBasePrice() {
		return $this->ecompcBasePrice;
	}

	/**
	 * Sets the ecompcBasePrice
	 *
	 * @param float $ecompcBasePrice
	 * @return void
	 */
	public function setEcompcBasePrice($ecompcBasePrice) {
		$this->ecompcBasePrice = $ecompcBasePrice;
	}

	/**
	 * Returns the ecompcBasePriceList
	 *
	 * @return string $ecompcBasePriceList
	 */
	public function getEcompcBasePriceList() {
		$convArray = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($this->ecompcBasePriceList);
		return $convArray['data']['sDEF']['lDEF'];
	}

	/**
	 * Sets the ecompcBasePriceList
	 *
	 * @param string $ecompcBasePriceList
	 * @return void
	 */
	public function setEcompcBasePriceList($ecompcBasePriceList) {
		$this->ecompcBasePriceList = $ecompcBasePriceList;
	}

	public function isDynamicEcomProductConfigurator() {
		return $this->getEcompcType() === 1;
	}

}