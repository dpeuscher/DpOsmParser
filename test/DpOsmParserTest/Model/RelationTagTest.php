<?php
namespace DpOsmParserTest\Model;

use DpOsmParser\Model\Relation;
use DpOsmParser\Model\RelationTag;
use DpPHPUnitExtensions\PHPUnit\TestCase;

/**
 * Class RelationTagTest
 *
 * @package DpOsmParserTest\Model
 */
class RelationTagTest extends TestCase {
	const SUT = 'DpOsmParser\Model\RelationTag';
	/**
	 * @var \DpOsmParser\Model\RelationTag
	 */
	protected $_relationTag;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_relationTag = new RelationTag;
		$this->_emptyState = array('key' => null,'value' => null,'relation' => null);
	}
	public function testInitialState()
	{
		$relationTag = clone $this->_relationTag;

		$this->assertNull($relationTag->getKey());
		$this->assertNull($relationTag->getValue());
		$this->assertNull($relationTag->getRelation());
	}
	public function testSettersGetters()
	{
		$relationTag = clone $this->_relationTag;
        $key = "ownKey";
        $value = "ownValue";
		$relation = new Relation();
		$relationTag->exchangeArray(array('key' => $key,
                                    'value' => $value,
									'relation' => $relation
		                      ) + $this->_emptyState);
		$this->assertSame($key,$relationTag->getKey());
		$this->assertSame($value,$relationTag->getValue());
		$this->assertSame($relation,$relationTag->getRelation());
	}
	public function testGetStateVars() {
		$relationTag = clone $this->_relationTag;
		$this->assertEquals(array('key','value','relation'),$relationTag->getStateVars());
	}
}
