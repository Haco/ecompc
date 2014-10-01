<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 22.09.14
 * Time: 07:06
 */

namespace S3b0\Ecompc\Utility;


class Div extends \S3b0\Ecompc\Controller\StandardController {

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
			$controller->currency = $controller->settings['currency'][$controller->feSession->get('currency')];
		}
	}

}