<?php
/**
 * User: dpeuscher
 * Date: 03.04.13
 */

namespace DpOsmParser\Model;


use DpZFExtensions\Validator\IExchangeState;
use DpZFExtensions\Validator\TExchangeState;

/**
 * Class wayNode
 *
 * @package DpOsmParser\Model
 */
class WayNode implements IExchangeState {
	use TExchangeState;
	/**
	 * @var Way
	 */
	protected $_way;
	/**
	 * @var Node
	 */
	protected $_node;
	/**
	 * @var int
	 */
	protected $_step;

	/**
	 * @return \DpOsmParser\Model\Node
	 */
	public function getNode() {
		return $this->_node;
	}

	/**
	 * @return int
	 */
	public function getStep() {
		return $this->_step;
	}

	/**
	 * @return \DpOsmParser\Model\Way
	 */
	public function getWay() {
		return $this->_way;
	}
	/**
	 * @return array
	 */
	public function getStateVars() {
		return array('way','node','step');
	}
}