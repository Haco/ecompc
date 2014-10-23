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
use TYPO3\CMS\Core\Utility as CoreUtility;
use TYPO3\CMS\Extbase\Utility as ExtbaseUtility;

/**
 * DynamicConfiguratorController
 *
 * @package S3b0
 * @subpackage Ecompc
 */
class DynamicConfiguratorController extends \S3b0\Ecompc\Controller\StandardController implements \S3b0\Ecompc\GenericConfiguratorControllerInterface {

	/**
	 * action index
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package $package
	 *
	 * @return void
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function indexAction(\S3b0\Ecompc\Domain\Model\Package $package = NULL) {
		parent::indexAction($package);
		if ( $this->process === 1 ) {
			$this->currentPackage = $package;
			if ( !$package instanceof \S3b0\Ecompc\Domain\Model\Package ) {
				$configurationCode = self::getConfigurationCode($this, $this->cObj->getEcompcConfigurations()->toArray()[0], TRUE);
				$this->view->assign('configurationResult', $configurationCode[0]);
				$this->view->assign('configurationSummary', $configurationCode[1]);
			}
		}
		if ( $this->currentPackage instanceof \S3b0\Ecompc\Domain\Model\Package ) {
			$this->currentPackage->setCurrent(TRUE);
			$this->view->assignMultiple(array(
				'options' => self::getPackageOptions($this->currentPackage, $this),
				'currentPackage' => $this->currentPackage
			));
		}
	}

	/**
	 * @param \S3b0\Ecompc\Domain\Model\Package          $package
	 * @param \S3b0\Ecompc\Controller\StandardController $controller
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array|null
	 */
	public static function getPackageOptions(\S3b0\Ecompc\Domain\Model\Package $package, \S3b0\Ecompc\Controller\StandardController $controller) {
		$packageOptions = array();
		// Fetch selectable options for current package
		self::getSelectableOptions($package, $packageOptions, $controller);
		if ( count($packageOptions) === 0 )
			return NULL;

		// Include pricing for enabled users!
		if ( $controller->showPriceLabels ) {
			$controller->initializeOptions($package);
		}

		return $packageOptions;
	}

	/**
	 * Fetching options selectable
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Package          $package
	 * @param array                                      $selectableOptions
	 * @param \S3b0\Ecompc\Controller\StandardController $controller
	 */
	public static function getSelectableOptions(\S3b0\Ecompc\Domain\Model\Package $package, array &$selectableOptions, \S3b0\Ecompc\Controller\StandardController $controller) {
		$options = $controller->optionRepository->findByConfigurationPackage($package); // Set basic settings

		// Run dependency check
		$selectableOptions = array();
		/** @var \S3b0\Ecompc\Domain\Model\Option $option */
		foreach ( $options as $option ) {
			if ( $controller->checkOptionDependencies($option, $controller->selectedConfiguration) ) {
				if ( in_array($option->getUid(), $controller->selectedConfiguration['options']) )
					$option->setActive(TRUE);
				$selectableOptions[] = $option;
			}
		}
	}

	/**
	 * set configuration code
	 *
	 * @param  \S3b0\Ecompc\Controller\StandardController $controller
	 * @param  \S3b0\Ecompc\Domain\Model\Configuration    $configuration
	 * @param  boolean                                    $returnArray
	 * @param  integer                                    $loggerUid
	 *
	 * @return string
	 */
	public static function getConfigurationCode(\S3b0\Ecompc\Controller\StandardController $controller, \S3b0\Ecompc\Domain\Model\Configuration $configuration, $returnArray = FALSE, $loggerUid = 0) {
		$configurationCodeWrapper = ($configuration->hasConfigurationCodePrefix() ? '<span class="ecompc-syntax-help" title="' . ExtbaseUtility\LocalizationUtility::translate('csh.configCodePrefix', $controller->extensionName) . '">' . $configuration->getConfigurationCodePrefix() . '</span>' : '') . '%s' . ($configuration->hasConfigurationCodeSuffix() ? '<span class="ecompc-syntax-help" title="' . ExtbaseUtility\LocalizationUtility::translate('csh.configCodeSuffix', $controller->extensionName) . '">' . $configuration->getConfigurationCodeSuffix() . '</span>' : '');
		$configurationCodePlainTextWrapper = $configuration->getConfigurationCodePrefix() . '%s' . $configuration->getConfigurationCodeSuffix();
		$configurationCodeSegmentWrapper = '<span class="ecompc-syntax-help" title="%1$s">%2$s</span>';
		/*$summaryPlainWrapper = '%1$s: %2$s' . PHP_EOL;*/
		$summaryHTMLTableWrapper = '<table>%s</table>';
		$summaryHTMLTableRowWrapper = '<tr><td><b>%1$s:</b></td><td>%2$s</td></tr>';

		$code = '';
		$plain = '';
		/*$summaryPlain = '';*/
		$summaryHTML = '';

		/** @var \S3b0\Ecompc\Domain\Model\Package $package */
		foreach ( $controller->cObj->getEcompcPackages() as $package ) {
			if ( !$package->isVisibleInFrontend() ) {
				$code .= sprintf($configurationCodeSegmentWrapper, $package->getFrontendLabel(), $package->getDefaultOption()->getConfigurationCodeSegment());
				$plain .= $package->getDefaultOption()->getConfigurationCodeSegment();
				/*$summaryPlain .= sprintf($summaryPlainWrapper, $package->getFrontendLabel(), $package->getDefaultOption()->getFrontendLabel() . ($this->request->getControllerName() === 'DynamicConfiguratorAjaxRequest' ? ' [' . $package->getDefaultOption()->getConfigurationCodeSegment() . ']' : ''))*/;
				$summaryHTML .= sprintf($summaryHTMLTableRowWrapper, $package->getFrontendLabel(), $package->getDefaultOption()->getFrontendLabel() . ($controller->request->getControllerName() === 'DynamicConfiguratorAjaxRequest' ? ' [' . $package->getDefaultOption()->getConfigurationCodeSegment() . ']' : ''));
			} elseif ( $option = $controller->optionRepository->findOptionsByUidList($controller->selectedConfiguration['options'], $package, TRUE) ) {
				/** @var \S3b0\Ecompc\Domain\Model\Option $option */
				$code .= sprintf($configurationCodeSegmentWrapper, $option->getConfigurationPackage()->getFrontendLabel(), $option->getConfigurationCodeSegment());
				$plain .= $option->getConfigurationCodeSegment();
				/*$summaryPlain .= sprintf($summaryPlainWrapper, $package->getFrontendLabel(), $option->getFrontendLabel() . ($option->hasConfigurationCodeSegment() ? ' [' . $option->getConfigurationCodeSegment() . ']' : ''));*/
				$summaryHTML .= sprintf($summaryHTMLTableRowWrapper, $package->getFrontendLabel(), $option->getFrontendLabel() . ($option->hasConfigurationCodeSegment() ? ' [' . $option->getConfigurationCodeSegment() . ']' : ''));
			}
		}

		return $returnArray ? array(
			sprintf($configurationCodeWrapper, $code),
			sprintf($summaryHTMLTableWrapper, $summaryHTML),
			sprintf(
				$controller->settings['requestForm']['additionalParamsQueryString'],
				sprintf($configurationCodePlainTextWrapper, $plain),
				$configuration->getFrontendLabel(),
				$loggerUid
			)
		) : sprintf($configurationCodeWrapper, $code);
	}

	/**
	 * Fetching selectable configurations
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult|array|null $current actual setting
	 * @return void
	 */
	public function setSelectableConfigurations(&$current = NULL) {
		$current = $current ?: $this->configurationRepository->findByTtContentUid($this->cObj->getUid());
	}

}