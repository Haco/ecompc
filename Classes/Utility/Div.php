<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 22.09.14
 * Time: 07:06
 */

namespace S3b0\Ecompc\Utility;


class Div extends \S3b0\Ecompc\Controller\StandardController {

	const BIT_CURRENCY_IS_DEFAULT = 1;
	const BIT_CURRENCY_PREPEND_SYMBOL = 2;
	const BIT_CURRENCY_ADD_WHITEPACE_BETWEEN_CURRENCY_AND_VALUE = 4;
	const BIT_CURRENCY_NUMBER_SEPARATORS_IN_US_FORMAT = 8;
	const CONFIGURATOR_DYN_SIGNATURE = 'ecompc_configurator_dynamic';
	const CONFIGURATOR_SKU_SIGNATURE = 'ecompc_configurator_sku';

	public static function setEnvironment($isDevelopment = FALSE) {
		if ( $isDevelopment ) {
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
		if ( $GLOBALS['TSFE']->loginUser ) {
			// Set price flag (displays pricing if TRUE)
			$controller->showPriceLabels = $controller->settings['viewHeader'] && $controller->settings['showPriceLabels'] ? \TYPO3\CMS\Core\Utility\GeneralUtility::inList($GLOBALS['TSFE']->fe_user->user['usergroup'], $controller->settings['distFeUserGroup']) : FALSE;
		}
		if ( $controller->showPriceLabels ) {
			// Fetch currency configuration from TS
			$controller->currency = $controller->currencyRepository->findByUid($controller->feSession->get('currency'));
		}
	}

}