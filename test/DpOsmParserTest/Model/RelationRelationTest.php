<?php
namespace DpOsmParserTest\Model;

use DpOsmParser\Model\Relation;
use DpOsmParser\Model\RelationRelation;
use DpPHPUnitExtensions\PHPUnit\TestCase;

/**
 * Class RelationRelationTest
 *
 * @package DpOsmParserTest\Model
 */
class RelationRelationTest extends TestCase {
	const SUT = 'DpOsmParser\Model\RelationRelation';
	/**
	 * @var \DpOsmParser\Model\RelationRelation
	 */
	protected $_relationRelation;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_relationRelation = new RelationRelation;
		$this->_emptyState = array('relationParent' => null,'role' => null,'relationChild' => null);
	}
	public function testInitialState()
	{
		$relationTag = clone $this->_relationRelation;

		$this->assertNull($relationTag->getRelationParent());
		$this->assertNull($relationTag->getRole());
		$this->assertNull($relationTag->getRelationChild());
	}
	public function testSettersGetters()
	{
		$relationTag = clone $this->_relationRelation;
		$relation = new Relation();
		$relation2 = new Relation();
		$role = 'outer';
		$relationTag->exchangeArray(array('relationParent' => $relation,
                                    'role' => $role,
									'relationChild' => $relation2
		                      ) + $this->_emptyState);
		$this->assertSame($relation,$relationTag->getRelationParent());
		$this->assertSame($role,$relationTag->getRole());
		$this->assertSame($relation2,$relationTag->getRelationChild());
	}
	public function testGetStateVars() {
		$relationTag = clone $this->_relationRelation;
		$this->assertEquals(array('relationParent','relationChild','role'),$relationTag->getStateVars());
	}
}
