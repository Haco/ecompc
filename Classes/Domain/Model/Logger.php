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
 * Logger
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class Logger extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * @var string
	 */
	protected $sesId = '';

	/**
	 * @var integer
	 */
	protected $crdate;

	/**
	 * @var integer
	 */
	protected $tstamp;

	/**
	 * @var \S3b0\Ecompc\Domain\Model\Content
	 */
	protected $cObj;

	/**
	 * @var array
	 */
	protected $selectedConfiguration = array(
		'options' => array(),
		'packages' => array()
	);

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option>
	 */
	protected $options = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration>
	 */
	protected $configurations = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration>
	 */
	protected $availableConfigurations = NULL;

	/**
	 * @var boolean
	 */
	protected $priceEnabled = FALSE;

	/**
	 * @var string
	 */
	protected $currency = 'default';

	/**
	 * @var array
	 */
	protected $currencySetup = array();

	/**
	 * @var float
	 */
	protected $priceBasic = 0.0;

	/**
	 * @var float
	 */
	protected $priceConfiguration = 0.0;

	/**
	 * @var string
	 */
	protected $settings = '';

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
		$this->configurations = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->availableConfigurations = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * @param Content $cObj
	 * @param array   $settings
	 */
	public function initialize(\S3b0\Ecompc\Domain\Model\Content $cObj, array $settings) {
		$this->setPid(0);
		if ($GLOBALS['TSFE']->fe_user->user['uid']) {
			$this->setSesId($GLOBALS['TSFE']->fe_user->id);
		}
		$this->setCObj($cObj);
		$this->setSettings($settings);
		$this->setConfigurations($cObj->getEcompcConfigurations());
		$this->setAvailableConfigurations($this->getConfigurations());
		$this->setPriceEnabled();
		$this->setCrdate(time());
	}

	/**
	 * @return string
	 */
	public function getSesId() {
		return $this->sesId;
	}

	/**
	 * @param string $sesId
	 */
	public function setSesId($sesId) {
		$this->sesId = $sesId;
	}

	/**
	 * @return integer
	 */
	public function getCrdate() {
		return $this->crdate;
	}

	/**
	 * @param integer $crdate
	 */
	public function setCrdate($crdate) {
		$this->crdate = $crdate;
		$this->setTstamp($crdate);
	}

	/**
	 * @return integer
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * @param integer $tstamp
	 */
	public function setTstamp($tstamp) {
		$this->tstamp = $tstamp;
	}

	/**
	 * @return Content
	 */
	public function getCObj() {
		return $this->cObj;
	}

	/**
	 * @param Content $cObj
	 */
	public function setCObj($cObj) {
		$this->cObj = $cObj;
	}

	/**
	 * @return array
	 */
	public function getSelectedConfiguration() {
		return $this->selectedConfiguration;
	}

	/**
	 * @param array $selectedConfiguration
	 */
	public function setSelectedConfiguration(array $selectedConfiguration) {
		$this->selectedConfiguration = $selectedConfiguration;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 */
	public function addOption(\S3b0\Ecompc\Domain\Model\Option $option) {
		if ($this->options === NULL)
			$this->options = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

		$option->setSelected(TRUE);
		$option->getConfigurationPackage()->addSelectedOption($option);
		$this->options->attach($option);
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Option $option
	 */
	public function removeOption(\S3b0\Ecompc\Domain\Model\Option $option){
		$option->setSelected(FALSE);
		$option->getConfigurationPackage()->removeSelectedOption($option);
		$this->options->detach($option);
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option>
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Option> $options
	 */
	public function setOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $options = NULL) {
		$this->options = $options;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 */
	public function addConfiguration(\S3b0\Ecompc\Domain\Model\Configuration $configuration) {
		if ($this->configurations === NULL)
			$this->configurations = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

		$this->configurations->attach($configuration);
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 */
	public function removeConfiguration(\S3b0\Ecompc\Domain\Model\Configuration $configuration){
		$this->configurations->detach($configuration);
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration>
	 */
	public function getConfigurations() {
		return $this->configurations;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration> $configurations
	 */
	public function setConfigurations(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $configurations = NULL) {
		/**
		 * Make sure that only one configuration entity is used in case of dynamic ones.
		 * This is just FORBIDDEN, furthermore an error will be thrown by the controller in case of misconfiguration!
		 *
		 * If you feel funny or bored, please feel free to kick this limitation and have a lot of fun in rewriting.
		 */
		if ($this->cObj->isDynamicEcomProductConfigurator() && $configurations instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && $configurations->count() > 1) {
			$configurations->rewind();
			$this->addConfiguration($configurations->current());
		} else {
			$this->configurations = $configurations;
		}
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 */
	public function addAvailableConfiguration(\S3b0\Ecompc\Domain\Model\Configuration $configuration) {
		if ($this->availableConfigurations === NULL)
			$this->availableConfigurations = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

		$this->availableConfigurations->attach($configuration);
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 */
	public function removeAvailableConfiguration(\S3b0\Ecompc\Domain\Model\Configuration $configuration){
		$this->availableConfigurations->detach($configuration);
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration>
	 */
	public function getAvailableConfigurations() {
		return $this->availableConfigurations;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\Ecompc\Domain\Model\Configuration> $availableConfigurations
	 */
	public function setAvailableConfigurations(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $availableConfigurations = NULL) {
		$this->availableConfigurations = $availableConfigurations;
	}

	/**
	 * @return boolean
	 */
	public function isPriceEnabled() {
		return $this->priceEnabled;
	}

	/**
	 * @return void
	 */
	public function setPriceEnabled() {
		if ($this->settings['enablePricing']) {
			// Get Extension configuration (set @Extension Manager)
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ecompc']);
			// Get distributors frontend user groups (set @Extension Manager)
			$distFeUserGroups = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $extConf['distFeUserGroup'], TRUE);
			// Set price flag (displays pricing if TRUE)
			$this->priceEnabled = is_array($GLOBALS['TSFE']->fe_user->groupData['uid']) && count(array_intersect($distFeUserGroups, $GLOBALS['TSFE']->fe_user->groupData['uid']));
		}
	}

	/**
	 * @return float
	 */
	public function getPriceBasic() {
		return $this->priceBasic;
	}

	/**
	 * @param float $priceBasic
	 */
	public function setPriceBasic($priceBasic) {
		$this->priceBasic = $priceBasic;
		$this->setPriceConfiguration($priceBasic);
	}

	/**
	 * @return float
	 */
	public function getPriceConfiguration() {
		return $this->priceConfiguration;
	}

	/**
	 * @param float $priceConfiguration
	 */
	public function setPriceConfiguration($priceConfiguration) {
		$this->priceConfiguration = $priceConfiguration;
	}

	/**
	 * @param float $summand
	 */
	public function addOnPriceConfiguration($summand) {
		$this->priceConfiguration += floatval($summand);
	}

	/**
	 * @param float $subtrahend
	 */
	public function substractFromPriceConfiguration($subtrahend) {
		$this->priceConfiguration -= floatval($subtrahend);
	}

	/**
	 * @param float $multiplier
	 */
	public function multiplyPriceConfigurationBy($multiplier){
		$this->priceConfiguration *= floatval($multiplier);
	}

	/**
	 * @param float $divisor
	 */
	public function dividePriceConfigurationBy($divisor) {
		$this->priceConfiguration /= floatval($divisor);
	}

	/**
	 * @return string
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @param array $settings
	 */
	public function setSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @return string
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @param string $currency
	 */
	public function setCurrency($currency) {
		$this->currency = $currency;
		$this->setCurrencySetup();
		$price = $this->getCObj()->getPriceInCurrency($currency, $this->currencySetup['exchange']);
		$this->setPriceBasic($price);
	}

	/**
	 * @return array
	 */
	public function getCurrencySetup() {
		return $this->currencySetup;
	}

	/**
	 * @return void
	 */
	public function setCurrencySetup() {
		$this->currencySetup = $this->settings['currency'][$this->currency];
	}

}