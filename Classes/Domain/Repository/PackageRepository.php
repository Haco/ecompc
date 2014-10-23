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
 * The repository for Packages
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class PackageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * @param array $uidList
	 *
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findPackagesByUidList(array $uidList) {
		if ( !count($uidList) )
			return NULL;

		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
		$db = $GLOBALS['TYPO3_DB'];
		$query = $this->createQuery();

		return $query->matching($query->in('uid', $db->cleanIntArray($uidList)))->execute();
	}

	/**
	 * @param array $uidList
	 *
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findVisiblePackagesByUidList(array $uidList) {
		if ( !count($uidList) )
			return NULL;

		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
		$db = $GLOBALS['TYPO3_DB'];
		$query = $this->createQuery();

		return $query->matching($query->logicalAnd($query->in('uid', $db->cleanIntArray($uidList)), $query->equals('visible_in_frontend', 1)))->execute();
	}

}