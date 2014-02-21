<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Model;

/**
 * Class RelationTag
 *
 * @package DpOsmParser\Model
 */
class RelationTag extends ATag {
	/**
	 * @var Relation
	 */
	protected $_relation;

	/**
	 * @return \DpOsmParser\Model\Relation
	 */
	public function getRelation() {
		return $this->_relation;
	}

	/**
	 * @return array
	 */
	public function getStateVars() {
		return array_merge(parent::getStateVars(),array('relation'));
	}

}