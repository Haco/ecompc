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
		'TYPO3\CMS\Fluid\ViewHelpers\CObjUidViewHelper' => $extensionClassesPath . 'ViewHelpers/CObjUidViewHelper.php',
		'TYPO3\CMS\Fluid\ViewHelpers\Financial\CurrencyViewHelper' => $extensionClassesPath . 'ViewHelpers/Financial/CurrencyViewHelper.php'
	);

?>