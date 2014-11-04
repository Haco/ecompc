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

/**
 * The repository for Configurations
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class ConfigurationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * Set repository wide settings
	 */
	public function initializeObject() {
		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QuerySettingsInterface');
		$querySettings->setRespectStoragePage(FALSE); // Disable storage pid
		$this->setDefaultQuerySettings($querySettings);
	}

	/**
	 * @param array $uidList
	 *
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByUidList(array $uidList) {
		if ( !count($uidList) )
			return NULL;

		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
		$db = $GLOBALS['TYPO3_DB'];
		$query = $this->createQuery();

		return $query->matching($query->in('uid', $db->cleanIntArray($uidList)))->execute();
	}

	public function findByTtContentUid($uid) {
		if ( !(\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($uid) || \TYPO3\CMS\Core\Utility\MathUtility::convertToPositiveInteger($uid)) )
			return NULL;

		$query = $this->createQuery();

		return $query->matching($query->equals('tt_content_uid', $uid))->execute();
	}

	/**
	 * Returns configurations containing latest options selected! @use for SKU-based configurators
	 *
	 * @param int   $uid
	 * @param array $selectedOptions
	 *
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByTtContentUidApplyingSelectedOptions($uid = 0, array $selectedOptions) {
		if ( !(\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($uid) || \TYPO3\CMS\Core\Utility\MathUtility::convertToPositiveInteger($uid)) )
			return NULL;

		$query = $this->createQuery();
		if ( count($selectedOptions) ) {
			$logicalAndConstraint = array(
				$query->equals('tt_content_uid', $uid)
			);
			foreach ( $selectedOptions as $optionUid ) {
				$logicalAndConstraint[] = $query->contains('options', $optionUid);
			}

			return $query->matching($query->logicalAnd($logicalAndConstraint))->execute();
		} else {
			return $this->findByTtContentUid($uid);
		}
	}

}