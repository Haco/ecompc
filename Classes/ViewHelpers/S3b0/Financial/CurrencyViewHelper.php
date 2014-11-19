<?php
namespace TYPO3\CMS\Fluid\ViewHelpers\S3b0\Financial;

/**                                                                       *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Formats a given float to a currency representation.
 *
 * = Examples =
 *
 * <code title="Defaults">
 * <f:format.currency>123.456</f:format.currency>
 * </code>
 * <output>
 * 123,46
 * </output>
 *
 * <code title="All parameters">
 * <f:format.currency currencySign="$" decimalSeparator="." thousandsSeparator="," prependCurrency="TRUE" separateCurrency="FALSE" decimals="2">54321</f:format.currency>
 * </code>
 * <output>
 * $54,321.00
 * </output>
 *
 * <code title="Inline notation">
 * {someNumber -> f:format.currency(thousandsSeparator: ',', currencySign: '€')}
 * </code>
 * <output>
 * 54,321,00 €
 * (depending on the value of {someNumber})
 * </output>
 *
 * @api
 */
class CurrencyViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Function render
	 *
	 * @param \S3b0\Ecompc\Domain\Model\Currency $currency       The currency object
	 * @param mixed                              $floatToFormat  (optional) The float, if any; If not set, renderChildren() will set the value
	 * @param integer                            $decimals       (optional) Set decimals places.
	 * @param boolean                            $signed         (optional) Select if algebraic sign should be added
	 * @param boolean                            $zeroLabel      (optional) If set a text like 'incl.' will be added instead of zero values
	 * @param boolean                            $usFormat       (optional) Indicator for US formats
	 *
	 * @return string the formatted amount.
	 * @api
	 */
	public function render(\S3b0\Ecompc\Domain\Model\Currency $currency, $floatToFormat = NULL, $decimals = 2, $signed = TRUE, $zeroLabel = FALSE, $usFormat = FALSE) {
		$decimalSeparator = $currency->isNumberSeparatorsInUSFormat() || $usFormat ? '.' : ',';
		$thousandsSeparator = $currency->isNumberSeparatorsInUSFormat() || $usFormat ? ',' : '.';
		$floatToFormat = $floatToFormat !== NULL ? $floatToFormat : $this->renderChildren();
		if ( empty($floatToFormat) ) {
			$floatToFormat = 0.0;
		} else {
			$floatToFormat = floatval($floatToFormat);
		}
		$output = number_format($floatToFormat, $decimals, $decimalSeparator, $thousandsSeparator);
		// Add algebraic sign if positive
		if ( $floatToFormat >= 0 && $signed ) {
			$output = '+' . $output;
		} elseif ( number_format($floatToFormat, 2) == 0.00 && $zeroLabel ) {
			return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('price.inclusive', 'ecompc');
		}

		if ( $currency->getSymbol() !== '' ) {
			$currencySeparator = $currency->isWhitespaceBetweenCurrencyAndValue() ? ' ' : '';
			if ( $currency->isSymbolPrepended() === TRUE ) {
				$output = $currency->getSymbol() . $currencySeparator . $output;
			} else {
				$output = $output . $currencySeparator . $currency->getSymbol();
			}
		}
		return $output;
	}

}