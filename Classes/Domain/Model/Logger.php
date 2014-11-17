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
	protected $tstamp;

	/**
	 * @var string
	 */
	protected $configurationCode = '';

	/**
	 * @var string
	 */
	protected $selectedConfiguration = '';

	/**
	 * @var \S3b0\Ecompc\Domain\Model\Configuration
	 */
	protected $configuration = NULL;

	/**
	 * @var \S3b0\Ecompc\Domain\Model\Currency
	 */
	protected $currency = NULL;

	/**
	 * @var float
	 */
	protected $price = 0.0;

	/**
	 * @var string
	 */
	protected $ip = '127.0.0.1';

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
	 */
	protected $feUser = NULL;

	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
		$this->setPid(0);
		$this->setSesId($GLOBALS['TSFE']->fe_user->id)->setTstamp(time());
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
	 * @return string
	 */
	public function getSesId() {
		return $this->sesId;
	}

	/**
	 * @param string $sesId
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setSesId($sesId) {
		$this->sesId = $sesId;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * @param integer $tstamp
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setTstamp($tstamp) {
		$this->tstamp = $tstamp;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getConfigurationCode() {
		return $this->configurationCode;
	}

	/**
	 * @param string $configurationCode
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setConfigurationCode($configurationCode) {
		$this->configurationCode = $configurationCode;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSelectedConfiguration() {
		return unserialize($this->selectedConfiguration);
	}

	/**
	 * @param array $selectedConfiguration
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setSelectedConfiguration(array $selectedConfiguration) {
		$this->selectedConfiguration = serialize($selectedConfiguration);
		return $this;
	}

	/**
	 * @return \S3b0\Ecompc\Domain\Model\Configuration
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Configuration $configuration
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setConfiguration(\S3b0\Ecompc\Domain\Model\Configuration $configuration = NULL) {
		$this->configuration = $configuration;
		return $this;
	}

	/**
	 * @return \S3b0\Ecompc\Domain\Model\Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Currency $currency
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setCurrency(\S3b0\Ecompc\Domain\Model\Currency $currency = NULL) {
		$this->currency = $currency;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getPrice() {
		return number_format($this->price, 2);
	}

	/**
	 * @param float $price
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setPrice($price = 0.0) {
		$this->price = $price;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIp() {
		return $this->ip;
	}

	/**
	 * @param string $ip
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setIp($ip, $parts = 4) {
		$tokens = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode('.', $ip, TRUE, 4);

		$ipParts = array_slice($tokens, 0, $parts);
		$ipParts = array_pad($ipParts, 4, '*');

		$this->ip = implode('.', $ipParts);
		return $this;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
	 */
	public function getFeUser() {
		return $this->feUser;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser
	 * @return \S3b0\Ecompc\Domain\Model\Logger
	 */
	public function setFeUser(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser = NULL) {
		$this->feUser = $feUser;
		return $this;
	}

}