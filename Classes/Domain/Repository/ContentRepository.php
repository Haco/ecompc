<?php
namespace S3b0\Ecompc\Domain\Repository;


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
use TYPO3\CMS\Core\Cache\Backend\NullBackend;

/**
 * The repository for Contents (extending tt_content repo)
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class ContentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * Sets the default orderings
	 *
	 * @var array $defaultOrderings
	 */
	protected $defaultOrderings = array(
		'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
	);

	/**
	 * Set repository wide settings
	 */
	public function initializeObject() {
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QuerySettingsInterface');
		$querySettings->setRespectStoragePage(FALSE); // Disable storage pid
		$this->setDefaultQuerySettings($querySettings);
	}

	/**
	 * @param null    $uid
	 * @param boolean $respectSysLanguage
	 * @param boolean $respectStoragePage
	 *
	 * @return null|object
	 */
	public function findByUid($uid = NULL, $respectSysLanguage = FALSE, $respectStoragePage = FALSE) {
		if ( !(\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($uid) || \TYPO3\CMS\Core\Utility\MathUtility::convertToPositiveInteger($uid)) )
			return NULL;

		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage($respectStoragePage);
		$query->getQuerySettings()->setRespectSysLanguage($respectSysLanguage);

		return $query->matching($query->equals('uid', $uid))->execute()->getFirst();
	}

	/**
	 * @param array $types Plugin-Types
	 *
	 * @return boolean
	 */
	public function hasDuplicateContentElementsOfConfiguratorTypes($types = array('ecompc_configurator_dynamic', 'ecompc_configurator_sku')) {
		$query = $this->createQuery();

		return $query->matching(
			$query->logicalAnd(
				$query->equals('pid', $GLOBALS['TSFE']->id),
				$query->in('list_type', $types)
			)
		)->execute()->count() > 1;
	}

}