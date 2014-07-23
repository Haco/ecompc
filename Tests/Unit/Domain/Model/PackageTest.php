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
 * Test case for class \S3b0\Ecompc\Domain\Model\Package.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Sebastian Iffland <sebastian.iffland@ecom-ex.com>
 */
class PackageTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \S3b0\Ecompc\Domain\Model\Package
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \S3b0\Ecompc\Domain\Model\Package();
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
	public function getPromptReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getPrompt()
		);
	}

	/**
	 * @test
	 */
	public function setPromptForStringSetsPrompt() {
		$this->subject->setPrompt('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'prompt',
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
	public function getVisibleInFrontendReturnsInitialValueForBoolean() {
		$this->assertSame(
			FALSE,
			$this->subject->getVisibleInFrontend()
		);
	}

	/**
	 * @test
	 */
	public function setVisibleInFrontendForBooleanSetsVisibleInFrontend() {
		$this->subject->setVisibleInFrontend(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'visibleInFrontend',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getMultipleSelectReturnsInitialValueForBoolean() {
		$this->assertSame(
			FALSE,
			$this->subject->getMultipleSelect()
		);
	}

	/**
	 * @test
	 */
	public function setMultipleSelectForBooleanSetsMultipleSelect() {
		$this->subject->setMultipleSelect(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'multipleSelect',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getDefaultOptionReturnsInitialValueForOption() {
		$this->assertEquals(
			NULL,
			$this->subject->getDefaultOption()
		);
	}

	/**
	 * @test
	 */
	public function setDefaultOptionForOptionSetsDefaultOption() {
		$defaultOptionFixture = new \S3b0\Ecompc\Domain\Model\Option();
		$this->subject->setDefaultOption($defaultOptionFixture);

		$this->assertAttributeEquals(
			$defaultOptionFixture,
			'defaultOption',
			$this->subject
		);
	}
}
