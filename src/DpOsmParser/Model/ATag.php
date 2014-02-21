<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Model;

use DpZFExtensions\Validator\IExchangeState;
use DpZFExtensions\Validator\TExchangeState;

/**
 * Class Tag
 *
 * @package DpOsmParser\Model
 */
abstract class ATag implements IExchangeState {
	use TExchangeState;
	/**
	 * @var string
	 */
	protected $_key;

	/**
	 * @var string
	 */
	protected $_value;

	/**
	 * @return string
	 */
	public function getKey() {
		return $this->_key;
	}

	/**
	 * @return string
	 */
	public function getValue() {
		return $this->_value;
	}
	/**
	 * @return array
	 */
	public function getStateVars() {
		return array('key','value');
	}
}