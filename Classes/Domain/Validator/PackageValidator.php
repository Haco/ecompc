<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 19.11.14
 * Time: 15:01
 */

namespace S3b0\Ecompc\Domain\Validator;

/**
 * Class PackageValidator
 */
class PackageValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	public function isValid($value) {
		return $value instanceof \S3b0\Ecompc\Domain\Model\Package;
	}

}