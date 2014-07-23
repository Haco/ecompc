<?php

namespace S3b0\Ecompc\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Sebastian Iffland <sebastian.iffland@ecom-ex.com>, ecom instruments GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \S3b0\Ecompc\Domain\Model\Option.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Sebastian Iffland <sebastian.iffland@ecom-ex.com>
 */
class OptionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \S3b0\Ecompc\Domain\Model\Option
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \S3b0\Ecompc\Domain\Model\Option();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getBackendLabelReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getBackendLabel()
		);
	}

	/**
	 * @test
	 */
	public function setBackendLabelForStringSetsBackendLabel() {
		$this->subject->setBackendLabel('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'backendLabel',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getFrontendLabelReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getFrontendLabel()
		);
	}

	/**
	 * @test
	 */
	public function setFrontendLabelForStringSetsFrontendLabel() {
		$this->subject->setFrontendLabel('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'frontendLabel',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getConfigurationCodeSegmentReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getConfigurationCodeSegment()
		);
	}

	/**
	 * @test
	 */
	public function setConfigurationCodeSegmentForStringSetsConfigurationCodeSegment() {
		$this->subject->setConfigurationCodeSegment('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'configurationCodeSegment',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getImageReturnsInitialValueForFileReference() {
		$this->assertEquals(
			NULL,
			$this->subject->getImage()
		);
	}

	/**
	 * @test
	 */
	public function setImageForFileReferenceSetsImage() {
		$fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
		$this->subject->setImage($fileReferenceFixture);

		$this->assertAttributeEquals(
			$fileReferenceFixture,
			'image',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getHintTextReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getHintText()
		);
	}

	/**
	 * @test
	 */
	public function setHintTextForStringSetsHintText() {
		$this->subject->setHintText('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'hintText',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPriceReturnsInitialValueForFloat() {
		$this->assertSame(
			0.0,
			$this->subject->getPrice()
		);
	}

	/**
	 * @test
	 */
	public function setPriceForFloatSetsPrice() {
		$this->subject->setPrice(3.14159265);

		$this->assertAttributeEquals(
			3.14159265,
			'price',
			$this->subject,
			'',
			0.000000001
		);
	}

	/**
	 * @test
	 */
	public function getPricePercentalReturnsInitialValueForFloat() {
		$this->assertSame(
			0.0,
			$this->subject->getPricePercental()
		);
	}

	/**
	 * @test
	 */
	public function setPricePercentalForFloatSetsPricePercental() {
		$this->subject->setPricePercental(3.14159265);

		$this->assertAttributeEquals(
			3.14159265,
			'pricePercental',
			$this->subject,
			'',
			0.000000001
		);
	}

	/**
	 * @test
	 */
	public function getConfigurationPackageReturnsInitialValueForPackage() {
		$this->assertEquals(
			NULL,
			$this->subject->getConfigurationPackage()
		);
	}

	/**
	 * @test
	 */
	public function setConfigurationPackageForPackageSetsConfigurationPackage() {
		$configurationPackageFixture = new \S3b0\Ecompc\Domain\Model\Package();
		$this->subject->setConfigurationPackage($configurationPackageFixture);

		$this->assertAttributeEquals(
			$configurationPackageFixture,
			'configurationPackage',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getDependencyReturnsInitialValueForDependency() {
		$this->assertEquals(
			NULL,
			$this->subject->getDependency()
		);
	}

	/**
	 * @test
	 */
	public function setDependencyForDependencySetsDependency() {
		$dependencyFixture = new \S3b0\Ecompc\Domain\Model\Dependency();
		$this->subject->setDependency($dependencyFixture);

		$this->assertAttributeEquals(
			$dependencyFixture,
			'dependency',
			$this->subject
		);
	}
}
