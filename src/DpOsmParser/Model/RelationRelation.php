<?php
/**
 * User: dpeuscher
 * Date: 03.04.13
 */

namespace DpOsmParser\Model;


use DpZFExtensions\Validator\IExchangeState;
use DpZFExtensions\Validator\TExchangeState;

/**
 * Class RelationRelation
 *
 * @package DpOsmParser\Model
 */
class RelationRelation implements IExchangeState {
	use TExchangeState;
	/**
	 * @var Relation
	 */
	protected $_relationParent;
	/**
	 * @var Relation
	 */
	protected $_relationChild;
	/**
	 * @var string
	 */
	protected $_role;

	/**
	 * @return \DpOsmParser\Model\Relation
	 */
	public function getRelationChild() {
		return $this->_relationChild;
	}

	/**
	 * @return \DpOsmParser\Model\Relation
	 */
	public function getRelationParent() {
		return $this->_relationParent;
	}

	/**
	 * @return string
	 */
	public function getRole() {
		return $this->_role;
	}

	/**
	 * @return array
	 */
	public function getStateVars() {
		return array('relationParent','relationChild','role');
	}
}