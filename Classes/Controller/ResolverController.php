<?php
namespace S3b0\Ecompc\Controller;

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
use TYPO3\CMS\Core\Utility as CoreUtility;
use TYPO3\CMS\Extbase\Utility as ExtbaseUtility;

/**
 * ResolverController
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class ResolverController extends \S3b0\Ecompc\Controller\StandardController {

	/**
	 * action index
	 *
	 * @param  \S3b0\Ecompc\Domain\Model\Logger $logger
	 * @return void
	 */
	public function indexAction(\S3b0\Ecompc\Domain\Model\Logger $logger = NULL) {
		if (!$logger instanceof \S3b0\Ecompc\Domain\Model\Logger && CoreUtility\MathUtility::canBeInterpretedAsInteger($logger)) {
			$logger = $this->loggerRepository->findByUid(CoreUtility\MathUtility::convertToPositiveInteger($logger));
		}
		if (!$logger instanceof \S3b0\Ecompc\Domain\Model\Logger) {
			$this->throwStatus(404, 'f41l');
		}

		$configuration = $logger->getConfiguration();
		$configurationArray = $logger->getSelectedConfiguration();
		if ($configuration->getOptions()->count()) {
			$options = $logger->getConfiguration()->getOptions();
		} else {
			$options = $this->optionRepository->findOptionsByUidList($configurationArray['options']);
		}

		$this->view->assign('log', $logger);
		$this->view->assign('options', $options);
	}

	/**
	 * action showUserInformation
	 *
	 * @param  \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $frontendUser
	 * @return void
	 */
	public function showUserInformationAction(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $frontendUser) {
		$this->view->assign('feUser', $frontendUser);
	}

}