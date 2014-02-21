<?php
namespace DpOsmParserTest\Model;

use DpOsmParser\Model\Way;
use DpOsmParser\Model\Relation;
use DpOsmParser\Model\RelationWay;
use DpPHPUnitExtensions\PHPUnit\TestCase;

/**
 * Class RelationWayTest
 *
 * @package DpOsmParserTest\Model
 */
class RelationWayTest extends TestCase {
	const SUT = 'DpOsmParser\Model\RelationWay';
	/**
	 * @var \DpOsmParser\Model\RelationWay
	 */
	protected $_relationWay;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_relationWay = new RelationWay;
		$this->_emptyState = array('way' => null,'role' => null,'relation' => null);
	}
	public function testInitialState()
	{
		$relationTag = clone $this->_relationWay;

		$this->assertNull($relationTag->getWay());
		$this->assertNull($relationTag->getRole());
		$this->assertNull($relationTag->getRelation());
	}
	public function testSettersGetters()
	{
		$relationTag = clone $this->_relationWay;
		$relation = new Relation();
		$way = new Way();
		$role = 'outer';
		$relationTag->exchangeArray(array('way' => $way,
                                    'role' => $role,
									'relation' => $relation
		                      ) + $this->_emptyState);
		$this->assertSame($way,$relationTag->getWay());
		$this->assertSame($role,$relationTag->getRole());
		$this->assertSame($relation,$relationTag->getRelation());
	}
	public function testGetStateVars() {
		$relationTag = clone $this->_relationWay;
		$this->assertEquals(array('relation','way','role'),$relationTag->getStateVars());
	}
}
