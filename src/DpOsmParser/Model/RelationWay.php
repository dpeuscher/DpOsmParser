<?php
/**
 * User: dpeuscher
 * Date: 03.04.13
 */

namespace DpOsmParser\Model;


use DpZFExtensions\Validator\IExchangeState;
use DpZFExtensions\Validator\TExchangeState;

/**
 * Class RelationWay
 *
 * @package DpOsmParser\Model
 */
class RelationWay implements IExchangeState {
	use TExchangeState;
	/**
	 * @var Relation
	 */
	protected $_relation;
	/**
	 * @var Way
	 */
	protected $_way;
	/**
	 * @var string
	 */
	protected $_role;

	/**
	 * @return \DpOsmParser\Model\Relation
	 */
	public function getRelation() {
		return $this->_relation;
	}

	/**
	 * @return string
	 */
	public function getRole() {
		return $this->_role;
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
		return array('relation','way','role');
	}
}