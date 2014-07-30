<?php
	namespace S3b0\Ecompc;

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
	 * Add news extension to the wizard in page module
	 * @author     Sebastian Iffland <sebastian.iffland@ecom-ex.com>, ecom instruments GmbH
	 * @package    S3b0
	 * @subpackage Ecompc
	 */
	class Wizicon {

		const KEY = 'ecompc';

		/**
		 * Processing the wizard items array
		 *
		 * @param array $wizardItems The wizard items
		 * @return array array with wizard items
		 */
		public function proc($wizardItems) {
			$wizardItems['plugins_tx_' . self::KEY] = array(
				'icon'			=> \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath(self::KEY) . 'ext_icon.gif',
				'title'			=> $GLOBALS['LANG']->sL('LLL:EXT:' . self::KEY . '/Resources/Private/Language/locallang_db.xml:pi.title'),
				'description'	=> $GLOBALS['LANG']->sL('LLL:EXT:' . self::KEY . '/Resources/Private/Language/locallang_db.xml:pi.wiz.description'),
				'params'		=> '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=ecompc_configurator'
			);

			return $wizardItems;
		}
	}

	if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ecompc/Resources/Private/PHP/class.ecompc_wizicon.php']) {
		include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ecompc/Resources/Private/PHP/class.ecompc_wizicon.php']);
	}

?>