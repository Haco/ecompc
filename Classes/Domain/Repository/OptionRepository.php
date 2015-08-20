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
 * The repository for Options
 */
class OptionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * Sets the default orderings
	 *
	 * @var array $defaultOrderings
	 */
	protected $defaultOrderings = [
		'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
	];

	/**
	 * @param array                             $uidList
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @param bool                              $getFirst
	 *
	 * @return array|NULL|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface|\S3b0\Ecompc\Domain\Model\Option
	 */
	public function findOptionsByUidList(array $uidList, \S3b0\Ecompc\Domain\Model\Package $package = NULL, $getFirst = FALSE) {
		if ( !count($uidList) )
			return NULL;

		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
		$db = $GLOBALS['TYPO3_DB'];
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);
		$constraint = $package instanceof \S3b0\Ecompc\Domain\Model\Package ? $query->logicalAnd([
			$query->in('uid', $db->cleanIntArray($uidList)),
			$query->equals('configuration_package', $package)
		]) : $query->in('uid', $db->cleanIntArray($uidList));
		$result = $query->matching($constraint)->execute();

		return $result->count() ? ($getFirst ? $result->getFirst() : $result) : NULL;
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByConfigurationPackage(\S3b0\Ecompc\Domain\Model\Package $package) {
		$query = $this->createQuery();

		return $query->matching($query->equals('configuration_package', $package))->execute();
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @param int                               $mode
	 *
	 * @return array|string
	 */
	public function getPackageOptionUidList(\S3b0\Ecompc\Domain\Model\Package $package, $mode = 1) {
		$query = $this->createQuery();

		$return = [ ];
		/** @var array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface $result */
		if ( $result = $query->matching($query->equals('configuration_package', $package))->execute() ) {
			/** @var \S3b0\Ecompc\Domain\Model\Option $row */
			foreach ( $result as $row )
				$return[$row->getSorting()] = $row->getUid();
		}
		ksort($return);

		return $mode === 1 ? $return : implode(',', $return);
	}

}