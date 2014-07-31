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
 * Test case for class \S3b0\Ecompc\Domain\Model\Configuration.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Sebastian Iffland <sebastian.iffland@ecom-ex.com>
 */
class ConfigurationTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \S3b0\Ecompc\Domain\Model\Configuration
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \S3b0\Ecompc\Domain\Model\Configuration();
	}

	protected function tearDown() {
		unset($this->subject);
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
	public function getSkuReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getSku()
		);
	}

	/**
	 * @test
	 */
	public function setSkuForStringSetsSku() {
		$this->subject->setSku('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'sku',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getConfigurationCodeSuffixReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getConfigurationCodeSuffix()
		);
	}

	/**
	 * @test
	 */
	public function setConfigurationCodeSuffixForStringSetsConfigurationCodeSuffix() {
		$this->subject->setConfigurationCodeSuffix('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'configurationCodeSuffix',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getConfigurationCodePrefixReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getConfigurationCodePrefix()
		);
	}

	/**
	 * @test
	 */
	public function setConfigurationCodePrefixForStringSetsConfigurationCodePrefix() {
		$this->subject->setConfigurationCodePrefix('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'configurationCodePrefix',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getOptionsReturnsInitialValueForOption() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getOptions()
		);
	}

	/**
	 * @test
	 */
	public function setOptionsForObjectStorageContainingOptionSetsOptions() {
		$option = new \S3b0\Ecompc\Domain\Model\Option();
		$objectStorageHoldingExactlyOneOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneOptions->attach($option);
		$this->subject->setOptions($objectStorageHoldingExactlyOneOptions);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneOptions,
			'options',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addOptionToObjectStorageHoldingOptions() {
		$option = new \S3b0\Ecompc\Domain\Model\Option();
		$optionsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$optionsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($option));
		$this->inject($this->subject, 'options', $optionsObjectStorageMock);

		$this->subject->addOption($option);
	}

	/**
	 * @test
	 */
	public function removeOptionFromObjectStorageHoldingOptions() {
		$option = new \S3b0\Ecompc\Domain\Model\Option();
		$optionsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$optionsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($option));
		$this->inject($this->subject, 'options', $optionsObjectStorageMock);

		$this->subject->removeOption($option);

	}
}
