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

class ExplodeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Splits a string into an array
	 *
	 * @param string  $subject String to explode
	 * @param string  $delimiter Char or string to split the string into pieces
	 * @param boolean $removeEmpty If TRUE empty items will be removed
	 *
	 * @return array Exploded parts
	 */
	public function render($subject = NULL, $delimiter = ',', $removeEmpty = TRUE) {
		if ( $subject === NULL ) {
			$subject = $this->renderChildren();
		}

		switch ( $delimiter ) {
			case '\n':
				$delimiter = "\n";
				break;
			case '\r':
				$delimiter = "\r";
				break;
			case '\r\n':
				$delimiter = "\r\n";
				break;
			case '\n\r':
				$delimiter = "\n\r";
				break;
			case '\t':
				$delimiter = "\t";
				break;
		}

		return \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode($delimiter, $subject, $removeEmpty);
	}
}

?>