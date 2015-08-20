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
 * Class Currency
 * @package S3b0\Ecompc\Domain\Model
 */
class Currency extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * @var int
	 */
	protected $sorting = 0;

	/**
	 * @var string
	 */
	protected $label = '';

	/**
	 * @var string
	 */
	protected $iso4217 = '';

	/**
	 * @var string
	 */
	protected $symbol = '';

	/**
	 * @var string
	 */
	protected $region = '';

	/**
	 * @var string
	 */
	protected $localLang = '';

	/**
	 * @var float
	 */
	protected $exchange = 0.0;

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|NULL
	 */
	protected $flag = NULL;

	/**
	 * @var int
	 */
	protected $settings = 0;

	/**
	 * @var bool
	 */
	protected $defaultCurrency = FALSE;

	/**
	 * @var bool
	 */
	protected $symbolPrepended = FALSE;

	/**
	 * @var bool
	 */
	protected $whitespaceBetweenCurrencyAndValue = FALSE;

	/**
	 * @var bool
	 */
	protected $numberSeparatorsInUSFormat = FALSE;

	/**
	 * @return int
	 */
	public function getSorting() {
		return $this->sorting;
	}

	/**
	 * @param int $sorting
	 */
	public function setSorting($sorting) {
		$this->sorting = $sorting;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getIso4217() {
		return $this->iso4217;
	}

	/**
	 * @param string $iso4217
	 */
	public function setIso4217($iso4217) {
		$this->iso4217 = $iso4217;
	}

	/**
	 * @return string
	 */
	public function getSymbol() {
		return $this->symbol;
	}

	/**
	 * @param string $symbol
	 */
	public function setSymbol($symbol) {
		$this->symbol = $symbol;
	}

	/**
	 * @return string
	 */
	public function getRegion() {
		return $this->region;
	}

	/**
	 * @param string $region
	 */
	public function setRegion($region) {
		$this->region = $region;
	}

	/**
	 * @return string
	 */
	public function getLocalLang() {
		return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($this->localLang, 'ecompc');
	}

	/**
	 * @param string $localLang
	 */
	public function setLocalLang($localLang) {
		$this->localLang = $localLang;
	}

	/**
	 * @return float
	 */
	public function getExchange() {
		return $this->exchange;
	}

	/**
	 * @param float $exchange
	 */
	public function setExchange($exchange) {
		$this->exchange = $exchange;
	}

	/**
	 * @return NULL|\TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	public function getFlag() {
		return $this->flag;
	}

	/**
	 * @param NULL|\TYPO3\CMS\Extbase\Domain\Model\FileReference $flag
	 */
	public function setFlag(\TYPO3\CMS\Extbase\Domain\Model\FileReference $flag) {
		$this->flag = $flag;
	}

	/**
	 * @return int
	 */
	public function getSettings() {
		return (int) $this->settings;
	}

	/**
	 * @param int $settings
	 */
	public function setSettings($settings) {
		$this->settings = $settings;
	}

	/**
	 * @return bool
	 */
	public function isDefaultCurrency() {
		return ($this->settings & \S3b0\Ecompc\Setup::BIT_CURRENCY_IS_DEFAULT) == \S3b0\Ecompc\Setup::BIT_CURRENCY_IS_DEFAULT;
	}

	/**
	 * @param bool $defaultCurrency
	 */
	public function setDefaultCurrency($defaultCurrency) {
		$this->defaultCurrency = $defaultCurrency;
	}

	/**
	 * @return bool
	 */
	public function isSymbolPrepended() {
		return ($this->settings & \S3b0\Ecompc\Setup::BIT_CURRENCY_PREPEND_SYMBOL) == \S3b0\Ecompc\Setup::BIT_CURRENCY_PREPEND_SYMBOL;
	}

	/**
	 * @param bool $prependSymbol
	 */
	public function setSymbolPrepended($prependSymbol) {
		$this->symbolPrepended = $prependSymbol;
	}

	/**
	 * @return bool
	 */
	public function isWhitespaceBetweenCurrencyAndValue() {
		return ($this->settings & \S3b0\Ecompc\Setup::BIT_CURRENCY_ADD_WHITEPACE_BETWEEN_CURRENCY_AND_VALUE) == \S3b0\Ecompc\Setup::BIT_CURRENCY_ADD_WHITEPACE_BETWEEN_CURRENCY_AND_VALUE;
	}

	/**
	 * @param bool $whitespaceBetweenCurrencyAndValue
	 */
	public function setWhitespaceBetweenCurrencyAndValue($whitespaceBetweenCurrencyAndValue) {
		$this->whitespaceBetweenCurrencyAndValue = $whitespaceBetweenCurrencyAndValue;
	}

	/**
	 * @return bool
	 */
	public function isNumberSeparatorsInUSFormat() {
		return $this->settings & 8;
	}

	/**
	 * @param bool $numberSeparatorsInUSFormat
	 */
	public function setNumberSeparatorsInUSFormat($numberSeparatorsInUSFormat) {
		$this->numberSeparatorsInUSFormat = $numberSeparatorsInUSFormat;
	}

}