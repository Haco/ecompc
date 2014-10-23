<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 16.10.14
 * Time: 08:43
 */

namespace S3b0\Ecompc;


interface GenericConfiguratorControllerInterface {

	public static function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package, \S3b0\Ecompc\Controller\StandardController $controller);

	public static function getSelectableOptions(\S3b0\Ecompc\Domain\Model\Package $package, array &$selectableOptions, \S3b0\Ecompc\Controller\StandardController $controller);

	public static function getConfigurationCode(\S3b0\Ecompc\Controller\StandardController $controller, \S3b0\Ecompc\Domain\Model\Configuration $configuration, $returnArray = FALSE, $loggerUid = 0);

	public function setSelectableConfigurations(&$current = NULL);

}