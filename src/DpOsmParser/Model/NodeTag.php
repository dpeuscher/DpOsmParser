<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Model;

/**
 * Class NodeTag
 *
 * @package DpOsmParser\Model
 */
class NodeTag extends ATag {
	/**
	 * @var Node
	 */
	protected $_node;
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
		return array_merge(parent::getStateVars(),array('node'));
	}
}