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
 * Dependency
 */
class Dependency extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * mode
	 *
	 * @var integer
	 */
	protected $mode = 0;

	/**
	 * packages
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Package>
	 */
	protected $packages = NULL;

	/**
	 * Options depending
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
		$this->packages = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Returns the mode
	 *
	 * @return integer $mode
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * Sets the mode
	 *
	 * @param integer $mode
	 * @return void
	 */
	public function setMode($mode) {
		$this->mode = $mode;
	}

	/**
	 * Adds a Package
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @return void
	 */
	public function addPackage(\S3b0\Ecompc\Domain\Model\Package $package) {
		$this->packages->attach($package);
	}

	/**
	 * Removes a Package
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $packageToRemove The Option to be removed
	 * @return void
	 */
	public function removePackage(\S3b0\Ecompc\Domain\Model\Package $packageToRemove) {
		$this->packages->detach($packageToRemove);
	}

	/**
	 * Returns the packages
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Package> $options
	 */
	public function getPackages() {
		return $this->packages;
	}

	/**
	 * Sets the packages
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Package> $packages
	 * @return void
	 */
	public function setPackages(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $packages) {
		$this->packages = $packages;
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

	/**
	 * Returns the options
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option> $options
	 */
	public function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package) {
		if (!$this->getOptions()->count()) return $this->getOptions();

		$temp = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

		foreach ($this->getOptions() as $option) {
			if ($option->getConfigurationPackage() === $package) {
				$temp->attach($option);
			}
		}

		return $temp;
	}

}