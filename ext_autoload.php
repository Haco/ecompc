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
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\CObjUidViewHelper' => $extensionClassesPath . 'ViewHelpers/S3b0/CObjUidViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\S3b0\Financial\CurrencyViewHelper' => $extensionClassesPath . 'ViewHelpers/S3b0/Financial/CurrencyViewHelper.php'
	);

?>