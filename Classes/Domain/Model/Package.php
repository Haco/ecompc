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
 * Package
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class Package extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * @var string
	 */
	protected $backendLabel = '';

	/**
	 * @var string
	 */
	protected $frontendLabel = '';

	/**
	 * @var string
	 */
	protected $prompt = '';

	/**
	 * @var string
	 */
	protected $hintText = '';

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $image = NULL;

	/**
	 * @var boolean
	 */
	protected $visibleInFrontend = FALSE;

	/**
	 * @var boolean
	 */
	protected $percentPricing = FALSE;

	/**
	 * @var boolean
	 */
	protected $multipleSelect = FALSE;

	/**
	 * @var boolean
	 */
	protected $active = FALSE;

	/**
	 * @var float
	 */
	protected $priceOutput = 0.0;

	/**
	 * @var \S3b0\Ecompc\Domain\Model\Option
	 */
	protected $defaultOption = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option>
	 */
	protected $selectedOptions = NULL;

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
		$this->selectedOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * @return string $backendLabel
	 */
	public function getBackendLabel() {
		return $this->backendLabel;
	}

	/**
	 * @param string $backendLabel
	 * @return void
	 */
	public function setBackendLabel($backendLabel) {
		$this->backendLabel = $backendLabel;
	}

	/**
	 * @return string $frontendLabel
	 */
	public function getFrontendLabel() {
		return $this->frontendLabel;
	}

	/**
	 * @param string $frontendLabel
	 * @return void
	 */
	public function setFrontendLabel($frontendLabel) {
		$this->frontendLabel = $frontendLabel;
	}

	/**
	 * @return string $prompt
	 */
	public function getPrompt() {
		return $this->prompt;
	}

	/**
	 * @param string $prompt
	 * @return void
	 */
	public function setPrompt($prompt) {
		$this->prompt = $prompt;
	}

	/**
	 * @return string $hintText
	 */
	public function getHintText() {
		return $this->hintText;
	}

	/**
	 * @param string $hintText
	 * @return void
	 */
	public function setHintText($hintText) {
		$this->hintText = $hintText;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
	 * @return void
	 */
	public function setImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image) {
		$this->image = $image;
	}

	/**
	 * @return boolean $visibleInFrontend
	 */
	public function getVisibleInFrontend() {
		return $this->visibleInFrontend;
	}

	/**
	 * @param boolean $visibleInFrontend
	 * @return void
	 */
	public function setVisibleInFrontend($visibleInFrontend) {
		$this->visibleInFrontend = $visibleInFrontend;
	}

	/**
	 * @return boolean
	 */
	public function isVisibleInFrontend() {
		return $this->visibleInFrontend;
	}

	/**
	 * @return boolean $percentPricing
	 */
	public function getPercentPricing() {
		return $this->isMultipleSelect() ? FALSE : $this->percentPricing;
	}

	/**
	 * @param boolean $percentPricing
	 * @return void
	 */
	public function setPercentPricing($percentPricing) {
		$this->percentPricing = $this->isMultipleSelect() ? FALSE : $percentPricing;
	}

	/**
	 * @return boolean
	 */
	public function isPercentPricing() {
		return $this->isMultipleSelect() ? FALSE : $this->percentPricing;
	}

	/**
	 * @return boolean $multipleSelect
	 */
	public function getMultipleSelect() {
		return $this->multipleSelect;
	}

	/**
	 * @param boolean $multipleSelect
	 * @return void
	 */
	public function setMultipleSelect($multipleSelect) {
		$this->multipleSelect = $multipleSelect;
	}

	/**
	 * @return boolean
	 */
	public function isMultipleSelect() {
		return $this->multipleSelect;
	}

	/**
	 * @return boolean $active
	 */
	public function getActive() {
		return $this->active;
	}

	/**
	 * @param boolean $active
	 * @return void
	 */
	public function setActive($active) {
		$this->active = $active;
	}

	/**
	 * @return boolean
	 */
	public function isActive() {
		return $this->active;
	}

	/**
	 * @return float
	 */
	public function getPriceOutput() {
		return $this->priceOutput;
	}

	/**
	 * @param float $priceOutput
	 * @return void
	 */
	public function setPriceOutput($priceOutput) {
		$this->priceOutput = $priceOutput;
	}

	/**
	 * @param integer|float $add
	 * @return void
	 */
	public function sumPriceOutput($add) {
		$this->priceOutput += floatval($add);
	}

	/**
	 * @return \S3b0\Ecompc\Domain\Model\Option $defaultOption
	 */
	public function getDefaultOption() {
		return $this->defaultOption;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Option $defaultOption
	 * @return void
	 */
	public function setDefaultOption(\S3b0\Ecompc\Domain\Model\Option $defaultOption) {
		$this->defaultOption = $defaultOption;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Option $selectedOption
	 * @return void
	 */
	public function addSelectedOption(\S3b0\Ecompc\Domain\Model\Option $selectedOption) {
		if ($this->selectedOptions === NULL) {
			$this->selectedOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		}

		$this->selectedOptions->attach($selectedOption);
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Option $selectedOptionToRemove
	 * @return void
	 */
	public function removeSelectedOption(\S3b0\Ecompc\Domain\Model\Option $selectedOptionToRemove) {
		$this->selectedOptions->detach($selectedOptionToRemove);
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage $selectedOptions
	 */
	public function getSelectedOptions() {
		if ($this->selectedOptions === NULL) {
			$this->selectedOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		}

		return $this->selectedOptions;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option> $selectedOptions
	 * @return void
	 */
	public function setSelectedOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $selectedOptions = null) {
		$this->selectedOptions = $selectedOptions;
	}

	/**
	 * @return boolean
	 */
	public function hasSelectedOptions() {
		return boolval($this->getSelectedOptions()->count());
	}

}