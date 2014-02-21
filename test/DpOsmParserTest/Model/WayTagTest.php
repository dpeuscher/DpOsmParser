<?php
namespace DpOsmParserTest\Model;

use DpOsmParser\Model\Way;
use DpOsmParser\Model\WayTag;
use DpPHPUnitExtensions\PHPUnit\TestCase;

/**
 * Class WayTagTest
 *
 * @package DpOsmParserTest\Model
 */
class WayTagTest extends TestCase {
	const SUT = 'DpOsmParser\Model\WayTag';
	/**
	 * @var \DpOsmParser\Model\WayTag
	 */
	protected $_wayTag;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_wayTag = new WayTag;
		$this->_emptyState = array('key' => null,'value' => null,'way' => null);
	}
	public function testInitialState()
	{
		$wayTag = clone $this->_wayTag;

		$this->assertNull($wayTag->getKey());
		$this->assertNull($wayTag->getValue());
		$this->assertNull($wayTag->getWay());
	}
	public function testSettersGetters()
	{
		$wayTag = clone $this->_wayTag;
        $key = "ownKey";
        $value = "ownValue";
		$way = new Way();
		$wayTag->exchangeArray(array('key' => $key,
                                    'value' => $value,
									'way' => $way
		                      ) + $this->_emptyState);
		$this->assertSame($key,$wayTag->getKey());
		$this->assertSame($value,$wayTag->getValue());
		$this->assertSame($way,$wayTag->getWay());
	}
	public function testGetStateVars() {
		$wayTag = clone $this->_wayTag;
		$this->assertEquals(array('key','value','way'),$wayTag->getStateVars());
	}
}
