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
	 * Initializes the controller before invoking an action method.
	 *
	 * Override this method to solve tasks which all actions have in
	 * common.
	 *
	 * @return void
	 * @api
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function initializeAction() {
		if ( CoreUtility\GeneralUtility::_GP('log') && CoreUtility\MathUtility::canBeInterpretedAsInteger(CoreUtility\GeneralUtility::_GP('log')) && ($log = $this->loggerRepository->findByUid(CoreUtility\MathUtility::convertToPositiveInteger(CoreUtility\GeneralUtility::_GP('log')))) ) {
			$this->redirectToPage(NULL, array(CoreUtility\GeneralUtility::camelCaseToLowerCaseUnderscored('Tx' . $this->request->getControllerExtensionName() . $this->request->getPluginName()) => array('action' => 'show', 'logger' => $log)), FALSE, FALSE);
		} elseif ( CoreUtility\GeneralUtility::_GP('log') ) {
			$this->throwStatus(404);
		}
		if ( $this->request->getControllerActionName() != 'show' && !($GLOBALS['TSFE']->fe_user->groupData['uid'] && in_array($this->settings['resolverUserGroup'], $GLOBALS['TSFE']->fe_user->groupData['uid'])) ) {
			$this->redirect('show');
		}
	}

	/**
	 * Initializes the view before invoking an action method.
	 *
	 * Override this method to solve assign variables common for all actions
	 * or prepare the view in another way before the action is called.
	 *
	 * @return void
	 * @api
	 */
	public function initializeView() {
		$this->view->assign('dateFormat', $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] . ' ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']);
	}

	/**
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 */
	public function initializeShowAction() {
		if ( $this->request->hasArgument('logger') && !$this->request->getArgument('logger') instanceof \S3b0\Ecompc\Domain\Model\Logger && CoreUtility\MathUtility::canBeInterpretedAsInteger($this->request->getArgument('logger')) ) {
			$this->request->setArgument(
				'logger',
				$this->loggerRepository->findByUid(CoreUtility\MathUtility::convertToPositiveInteger($this->request->getArgument('logger')))
			);
		}
	}

	/**
	 * action show
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Logger $logger
	 * @return void
	 */
	public function showAction(\S3b0\Ecompc\Domain\Model\Logger $logger = NULL) {
		if ( !$logger instanceof \S3b0\Ecompc\Domain\Model\Logger ) {
			$this->throwStatus(404, 'Log not found!');
		}
		/** @var \S3b0\Ecompc\Domain\Model\Configuration $configuration */
		$configuration = $logger->getConfiguration();
		$configurationArray = $logger->getSelectedConfiguration();
		if ( $configuration->getOptions()->count() ) {
			$options = $logger->getConfiguration()->getOptions();
		} else {
			$options = $this->optionRepository->findOptionsByUidList($configurationArray['options']);
		}

		$this->view->assign('log', $logger);
		$this->view->assign('options', $options);
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$this->view->assign('logs', $this->loggerRepository->findAll());
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