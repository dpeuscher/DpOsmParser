<?php
/**
 * User: dpeuscher
 * Date: 03.04.13
 */

namespace DpOsmParser\Model;


use DpZFExtensions\Validator\IExchangeState;
use DpZFExtensions\Validator\TExchangeState;

/**
 * Class RelationNode
 *
 * @package DpOsmParser\Model
 */
class RelationNode implements IExchangeState {
	use TExchangeState;
	/**
	 * @var Relation
	 */
	protected $_relation;
	/**
	 * @var Node
	 */
	protected $_node;
	/**
	 * @var string
	 */
	protected $_role;

	/**
	 * @return \DpOsmParser\Model\Node
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
	 * @return \DpOsmParser\Model\Node
	 */
	public function getNode() {
		return $this->_node;
	}
	/**
	 * @return array
	 */
	public function getStateVars() {
		return array('relation','node','role');
	}
}