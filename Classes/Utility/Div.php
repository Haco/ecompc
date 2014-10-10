<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 22.09.14
 * Time: 07:06
 */

namespace S3b0\Ecompc\Utility;


class Div extends \S3b0\Ecompc\Controller\StandardController {

	public static function setEnvironment($isDevelopment = FALSE) {
		if ($isDevelopment) {
			$GLOBALS['TYPO3_CONF_VARS']['BE']['debug'] = 1;
			$GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] = 1;
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] = '*';
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['displayErrors'] = '1';
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] = 'file';
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['sqlDebug'] = '1';
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLogLevel'] = '0';
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['exceptionalErrors'] = '28674';
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['clearCacheSystem'] = '1';
		}
	}

	/**
	 * @param \S3b0\Ecompc\Controller\StandardController $controller
	 */
	public static function setPriceHandling(\S3b0\Ecompc\Controller\StandardController $controller) {
		// Get distributors frontend user groups (set @Extension Manager)
		if ($GLOBALS['TSFE']->loginUser) {
			// Set price flag (displays pricing if TRUE)
			$controller->showPriceLabels = $controller->settings['showPriceLabels'] ? \TYPO3\CMS\Core\Utility\GeneralUtility::inList($GLOBALS['TSFE']->fe_user->user['usergroup'], $controller->settings['distFeUserGroup']) : FALSE;
		}
		if ($controller->showPriceLabels) {
			// Fetch currency configuration from TS
			$controller->currency = $controller->currencyRepository->findByUid($controller->feSession->get('currency'));
		}
	}

}