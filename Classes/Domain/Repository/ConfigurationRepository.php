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
 */
class ConfigurationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * Set repository wide settings
	 */
	public function initializeObject() {
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QuerySettingsInterface');
		$querySettings->setRespectStoragePage(FALSE); // Disable storage pid
		$this->setDefaultQuerySettings($querySettings);
	}

	/**
	 * @param array $list
	 *
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByUidList(array $list) {
		if (!sizeof($list))
			return NULL;

		$query = $this->createQuery();
		return $query->matching(
			$query->in('uid', $list)
		)->execute();
	}

}