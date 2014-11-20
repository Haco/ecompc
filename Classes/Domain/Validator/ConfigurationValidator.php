<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 20.11.14
 * Time: 07:21
 */

namespace S3b0\Ecompc\Domain\Validator;


class ConfigurationValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	public function isValid($value) {
		return $value instanceof \S3b0\Ecompc\Domain\Model\Configuration;
	}

}