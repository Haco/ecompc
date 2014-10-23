<?php
namespace S3b0\Ecompc\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Sebastian Iffland <sebastian.iffland@ecom-ex.com>, ecom instruments GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * DynamicConfiguratorAjaxRequestController
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class DynamicConfiguratorAjaxRequestController extends \S3b0\Ecompc\Controller\AjaxRequestController {

	/** @var \TYPO3\CMS\Extbase\Mvc\View\JsonView $view */
	protected $view;

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\\CMS\\Extbase\\Mvc\\View\\JsonView';

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 */
	public function indexAction(\S3b0\Ecompc\Domain\Model\Package $package = NULL) {
		$packages = $this->initializePackages(TRUE);
		if ($package instanceof \S3b0\Ecompc\Domain\Model\Package) {
			$this->currentPackage = $package;
		}
		if ($this->process === 1)
			$this->currentPackage = end($packages->toArray());
			$this->view->assign('configurationResult', $this->getConfigurationResult()); // Get configuration code | SKU
		if ($this->currentPackage instanceof \S3b0\Ecompc\Domain\Model\Package) {
			$this->view->assign('options', $this->getPackageOptions($this->currentPackage));
		}

		$this->currentPackage->setCurrent(TRUE);
		/** pre-parse hintText since not done by rendering process */
		$this->currentPackage->setHintText($this->configurationManager->getContentObject()->parseFunc($this->currentPackage->getHintText(), array(), '< lib.parseFunc_RTE'));
		$this->view->assignMultiple(array(
			'currentPackage' => $this->currentPackage,
			'packages' => $packages,
			'process' => $this->process,
			'debug' =>$this->currency
		));
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 *
	 * @return array
	 */
	public function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package) {
		$return = array();
		$configurationArray = $this->feSession->get($this->configurationSessionStorageKey);
		if ($options = \S3b0\Ecompc\Controller\DynamicConfiguratorController::getPackageOptions($package, $this)) {
			/** @var \S3b0\Ecompc\Domain\Model\Option $option */
			foreach ($options as $option) {
				$return[] = $option->getSummaryForJSONView($configurationArray['options'], $this->showPriceLabels, $this->currency);
			}
		}

		return $return;
	}

}