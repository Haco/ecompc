<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 07.07.14
 * Time: 08:31
 */

	$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ecompc') . 'Classes/';

	return array(
		'S3b0\Ecompc\User\TCAMod\ModifyTCA' => $extensionClassesPath . 'User/TCAMod/ModifyTCA.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\ExplodeViewHelper' => $extensionClassesPath . 'ViewHelpers/S3b0/ExplodeViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\NumViewHelper' => $extensionClassesPath . 'ViewHelpers/S3b0/NumViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\CObjUidViewHelper' => $extensionClassesPath . 'ViewHelpers/S3b0/CObjUidViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\CalculationViewHelper' => $extensionClassesPath . 'ViewHelpers/S3b0/CalculationViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\Financial\CurrencyViewHelper' => $extensionClassesPath . 'ViewHelpers/S3b0/Financial/CurrencyViewHelper.php'
	);

?>