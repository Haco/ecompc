<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 14.10.14
 * Time: 10:12
 */

namespace S3b0\Ecompc\Utility;

/**
 * Class BitHandler
 *
 * @package S3b0\Ecompc\Utility
 */
class BitHandler {

	/**
	 * @var integer
	 */
	protected $bits = 0;

	/**
	 * Gets the bits
	 *
	 * @return integer
	 */
	public function getBits() {
		return $this->bits;
	}

	/**
	 * Sets the bits
	 *
	 * @param $bits
	 */
	public function setBits($bits) {
		$this->bits = $bits;
	}

	/**
	 * Resets the bits property
	 *
	 * @return $this For method chaining
	 */
	public function reset() {
		$this->setBits(0);
		return $this;
	}

	/**
	 * Checks if flag (bit) is set
	 *
	 * @param  integer $bit The Bit to be checked against
	 * @return boolean
	 */
	public function isBitSet($bit) {
		return (($this->bits & $bit) == $bit);
	}

	/**
	 * @param integer $bit The Bit to be set
	 */
	public function setSingleBit($bit = 0) {
		$this->bits |= $bit;
	}

	/**
	 * @param integer $bit The Bit to be unset
	 */
	public function unsetSingleBit($bit = 0) {
		$this->bits &= ~$bit;
	}
}