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
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class OptionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

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
		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $querySettings */
//		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QuerySettingsInterface');
//		$querySettings->setRespectStoragePage(FALSE); // Disable storage pid
//		$this->setDefaultQuerySettings($querySettings);
	}

	/**
	 * @param array   $list
	 * @param boolean $getFirst
	 *
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface|\S3b0\Ecompc\Domain\Model\Option
	 */
	public function findOptionsByUidList(array $list, $getFirst = FALSE) {
		if (!count($list))
			return NULL;

		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);
		$result = $query->matching(
			$query->in('uid', $list)
		)->execute();

		return $getFirst ? $result->getFirst() : $result;
	}

	public function findByConfigurationPackage(\S3b0\Ecompc\Domain\Model\Package $package) {
		$query = $this->createQuery();
		return $query->matching($query->equals('configuration_package', $package))->execute();
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 * @param integer                           $mode
	 *
	 * @return array|string
	 */
	public function getPackageOptionsUids(\S3b0\Ecompc\Domain\Model\Package $package, $mode = 1) {
		$query = $this->createQuery();

		$return = array();
		if ($result = $query->matching($query->equals('configuration_package', $package))->execute()) {
			foreach ($result as $row)
				$return[] = $row->getUid();
		}

		return $mode === 1 ? $return : implode(',', $return);
	}

}