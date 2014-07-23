<?php
namespace S3b0\Ecompc\Tests\Unit\Controller;
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
 * Test case for class S3b0\Ecompc\Controller\PackageController.
 *
 * @author Sebastian Iffland <sebastian.iffland@ecom-ex.com>
 */
class PackageControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \S3b0\Ecompc\Controller\PackageController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('S3b0\\Ecompc\\Controller\\PackageController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllPackagesFromRepositoryAndAssignsThemToView() {

		$allPackages = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$packageRepository = $this->getMock('S3b0\\Ecompc\\Domain\\Repository\\PackageRepository', array('findAll'), array(), '', FALSE);
		$packageRepository->expects($this->once())->method('findAll')->will($this->returnValue($allPackages));
		$this->inject($this->subject, 'packageRepository', $packageRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('packages', $allPackages);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenPackageToView() {
		$package = new \S3b0\Ecompc\Domain\Model\Package();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('package', $package);

		$this->subject->showAction($package);
	}
}
