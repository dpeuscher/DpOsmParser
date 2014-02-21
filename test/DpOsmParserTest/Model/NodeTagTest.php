<?php
namespace DpOsmParserTest\Model;

use DpOsmParser\Model\Node;
use DpOsmParser\Model\NodeTag;
use DpPHPUnitExtensions\PHPUnit\TestCase;

/**
 * Class NodeTagTest
 *
 * @package DpOsmParserTest\Model
 */
class NodeTagTest extends TestCase {
	const SUT = 'DpOsmParser\Model\NodeTag';
	/**
	 * @var \DpOsmParser\Model\NodeTag
	 */
	protected $_nodeTag;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_nodeTag = new NodeTag;
		$this->_emptyState = array('key' => null,'value' => null,'node' => null);
	}
	public function testInitialState()
	{
		$nodeTag = clone $this->_nodeTag;

		$this->assertNull($nodeTag->getKey());
		$this->assertNull($nodeTag->getValue());
		$this->assertNull($nodeTag->getNode());
	}
	public function testSettersGetters()
	{
		$nodeTag = clone $this->_nodeTag;
        $key = "ownKey";
        $value = "ownValue";
		$node = new Node();
		$nodeTag->exchangeArray(array('key' => $key,
                                    'value' => $value,
									'node' => $node
		                      ) + $this->_emptyState);
		$this->assertSame($key,$nodeTag->getKey());
		$this->assertSame($value,$nodeTag->getValue());
		$this->assertSame($node,$nodeTag->getNode());
	}
	public function testGetStateVars() {
		$nodeTag = clone $this->_nodeTag;
		$this->assertEquals(array('key','value','node'),$nodeTag->getStateVars());
	}
}
