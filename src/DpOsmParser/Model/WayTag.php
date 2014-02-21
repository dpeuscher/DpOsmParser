<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Model;

/**
 * Class WayTag
 *
 * @package DpOsmParser\Model
 */
class WayTag extends ATag {
	/**
	 * @var Way
	 */
	protected $_way;

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
		return array_merge(parent::getStateVars(),array('way'));
	}

}