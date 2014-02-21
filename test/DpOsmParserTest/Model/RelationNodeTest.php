<?php
namespace DpOsmParserTest\Model;

use DpOsmParser\Model\Node;
use DpOsmParser\Model\Relation;
use DpOsmParser\Model\RelationNode;
use DpPHPUnitExtensions\PHPUnit\TestCase;

/**
 * Class RelationNodeTest
 *
 * @package DpOsmParserTest\Model
 */
class RelationNodeTest extends TestCase {
	const SUT = 'DpOsmParser\Model\RelationNode';
	/**
	 * @var \DpOsmParser\Model\RelationNode
	 */
	protected $_relationNode;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_relationNode = new RelationNode;
		$this->_emptyState = array('node' => null,'role' => null,'relation' => null);
	}
	public function testInitialState()
	{
		$relationTag = clone $this->_relationNode;

		$this->assertNull($relationTag->getNode());
		$this->assertNull($relationTag->getRole());
		$this->assertNull($relationTag->getRelation());
	}
	public function testSettersGetters()
	{
		$relationTag = clone $this->_relationNode;
		$relation = new Relation();
		$node = new Node();
		$role = 'capital';
		$relationTag->exchangeArray(array('node' => $node,
                                    'role' => $role,
									'relation' => $relation
		                      ) + $this->_emptyState);
		$this->assertSame($node,$relationTag->getNode());
		$this->assertSame($role,$relationTag->getRole());
		$this->assertSame($relation,$relationTag->getRelation());
	}
	public function testGetStateVars() {
		$relationTag = clone $this->_relationNode;
		$this->assertEquals(array('relation','node','role'),$relationTag->getStateVars());
	}
}
