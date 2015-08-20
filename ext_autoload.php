<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 07.07.14
 * Time: 08:31
 */

	$extClassPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ecompc') . 'Classes/';

	return [
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\CalculationViewHelper'        => $extClassPath . 'ViewHelpers/S3b0/CalculationViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\CObjUidViewHelper'            => $extClassPath . 'ViewHelpers/S3b0/CObjUidViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\ExplodeViewHelper'            => $extClassPath . 'ViewHelpers/S3b0/ExplodeViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\GetFileNameViewHelper'        => $extClassPath . 'ViewHelpers/S3b0/GetFileNameViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\NumViewHelper'                => $extClassPath . 'ViewHelpers/S3b0/NumViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\SubstringViewHelper'          => $extClassPath . 'ViewHelpers/S3b0/SubstringViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\Financial\CurrencyViewHelper' => $extClassPath . 'ViewHelpers/S3b0/Financial/CurrencyViewHelper.php'
	];

?>