<?php
namespace DpOsmParserTest\Model;

use DpOsmParser\Model\Node;
use DpOsmParser\Model\Way;
use DpOsmParser\Model\WayNode;
use DpPHPUnitExtensions\PHPUnit\TestCase;

/**
 * Class WayNodeTest
 *
 * @package DpOsmParserTest\Model
 */
class WayNodeTest extends TestCase {
	const SUT = 'DpOsmParser\Model\WayNode';
	/**
	 * @var \DpOsmParser\Model\WayNode
	 */
	protected $_wayNode;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_wayNode = new WayNode;
		$this->_emptyState = array('node' => null,'step' => null,'way' => null);
	}
	public function testInitialState()
	{
		$wayTag = clone $this->_wayNode;

		$this->assertNull($wayTag->getNode());
		$this->assertNull($wayTag->getStep());
		$this->assertNull($wayTag->getWay());
	}
	public function testSettersGetters()
	{
		$wayTag = clone $this->_wayNode;
		$way = new Way();
		$node = new Node();
		$step = 3;
		$wayTag->exchangeArray(array('node' => $node,
                                    'step' => $step,
									'way' => $way
		                      ) + $this->_emptyState);
		$this->assertSame($node,$wayTag->getNode());
		$this->assertSame($step,$wayTag->getStep());
		$this->assertSame($way,$wayTag->getWay());
	}
	public function testGetStateVars() {
		$wayTag = clone $this->_wayNode;
		$this->assertEquals(array('way','node','step'),$wayTag->getStateVars());
	}
}
