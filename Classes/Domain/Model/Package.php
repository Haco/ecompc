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
 */
class Package extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

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
	 * prompt
	 *
	 * @var string
	 */
	protected $prompt = '';

	/**
	 * hintText
	 *
	 * @var string
	 */
	protected $hintText = '';

	/**
	 * image
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $image = NULL;

	/**
	 * visibleInFrontend
	 *
	 * @var boolean
	 */
	protected $visibleInFrontend = FALSE;

	/**
	 * multipleSelect
	 *
	 * @var boolean
	 */
	protected $multipleSelect = FALSE;

	/**
	 * defaultOption
	 *
	 * @var \S3b0\Ecompc\Domain\Model\Option
	 */
	protected $defaultOption = NULL;

	/**
	 * selectedOptions
	 *
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
	 * Returns the prompt
	 *
	 * @return string $prompt
	 */
	public function getPrompt() {
		return $this->prompt;
	}

	/**
	 * Sets the prompt
	 *
	 * @param string $prompt
	 * @return void
	 */
	public function setPrompt($prompt) {
		$this->prompt = $prompt;
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
	 * Returns the visibleInFrontend
	 *
	 * @return boolean $visibleInFrontend
	 */
	public function getVisibleInFrontend() {
		return $this->visibleInFrontend;
	}

	/**
	 * Sets the visibleInFrontend
	 *
	 * @param boolean $visibleInFrontend
	 * @return void
	 */
	public function setVisibleInFrontend($visibleInFrontend) {
		$this->visibleInFrontend = $visibleInFrontend;
	}

	/**
	 * Returns the boolean state of visibleInFrontend
	 *
	 * @return boolean
	 */
	public function isVisibleInFrontend() {
		return $this->visibleInFrontend;
	}

	/**
	 * Returns the multipleSelect
	 *
	 * @return boolean $multipleSelect
	 */
	public function getMultipleSelect() {
		return $this->multipleSelect;
	}

	/**
	 * Sets the multipleSelect
	 *
	 * @param boolean $multipleSelect
	 * @return void
	 */
	public function setMultipleSelect($multipleSelect) {
		$this->multipleSelect = $multipleSelect;
	}

	/**
	 * Returns the boolean state of multipleSelect
	 *
	 * @return boolean
	 */
	public function isMultipleSelect() {
		return $this->multipleSelect;
	}

	/**
	 * Returns the defaultOption
	 *
	 * @return \S3b0\Ecompc\Domain\Model\Option $defaultOption
	 */
	public function getDefaultOption() {
		return $this->defaultOption;
	}

	/**
	 * Sets the defaultOption
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $defaultOption
	 * @return void
	 */
	public function setDefaultOption(\S3b0\Ecompc\Domain\Model\Option $defaultOption) {
		$this->defaultOption = $defaultOption;
	}

	/**
	 * Adds a selectedOption
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $selectedOptions
	 * @return void
	 */
	public function addSelectedOption(\S3b0\Ecompc\Domain\Model\Option $selectedOptions) {
		$this->selectedOptions->attach($selectedOptions);
	}

	/**
	 * Removes a selectedOption
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Option $selectedOptionsToRemove The Option to be removed
	 * @return void
	 */
	public function removeSelectedOption(\S3b0\Ecompc\Domain\Model\Option $selectedOptionsToRemove) {
		$this->selectedOptions->detach($selectedOptionsToRemove);
	}

	/**
	 * Returns the selectedOption
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage $selectedOptions
	 */
	public function getSelectedOptions() {
		return $this->selectedOptions;
	}

	/**
	 * Sets the selectedOption
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option> $selectedOptions
	 * @return void
	 */
	public function setSelectedOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $selectedOptions = null) {
		$this->selectedOptions = $selectedOptions;
	}

}