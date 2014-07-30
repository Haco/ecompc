<?php
namespace TYPO3\CMS\Fluid\ViewHelpers\S3b0;


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

class CObjUidViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;
	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface An instance of the Configuration Manager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}
	/**
	 * Set uid of the content element
	 *
	 * @return int $uid The uid of the content element
	 */
	public function render() {
// fallback
		$uid = uniqid();
		if ($this->templateVariableContainer->exists("contentObjectData")) {
// this works for templates but not for partials
			$contentObjectData = $this->templateVariableContainer->get("contentObjectData");
			$uid = $contentObjectData['uid'];
		} else {
// this should work in every circumstance
			$uid = $this->configurationManager->getContentObject()->data['uid'];
		}
		return $uid;
	}

}

?>