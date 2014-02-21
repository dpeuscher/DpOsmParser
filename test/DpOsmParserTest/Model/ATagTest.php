<?php
namespace DpOsmParserTest\Model\ATagTest;

use DpOsmParser\Model\ATag;

/**
 * Class Tag
 *
 * @package DpOsmParserTest\Model\ATagTest
 */
class Tag extends ATag {}

namespace DpOsmParserTest\Model;

use DpOsmParserTest\Model\ATagTest\Tag;
use DpPHPUnitExtensions\PHPUnit\TestCase;

/**
 * Class ATagTest
 *
 * @package DpOsmParserTest\Model
 */
class ATagTest extends TestCase {
	const SUT = 'DpOsmParserTest\Model\ATagTest\Tag';
	/**
	 * @var \DpOsmParserTest\Model\ATagTest\Tag
	 */
	protected $_aTag;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_aTag = new Tag;
		$this->_emptyState = array('key' => null,'value' => null);
	}
	public function testInitialState()
	{
		$aTag = clone $this->_aTag;

		$this->assertNull($aTag->getKey());
		$this->assertNull($aTag->getValue());
	}
	public function testSettersGetters()
	{
		$aTag = clone $this->_aTag;
        $key = "ownKey";
        $value = "ownValue";
		$aTag->exchangeArray(array('key' => $key,
                                    'value' => $value
		                      ) + $this->_emptyState);
		$this->assertSame($key,$aTag->getKey());
		$this->assertSame($value,$aTag->getValue());
	}
	public function testGetStateVars() {
		$aTag = clone $this->_aTag;
		$this->assertEquals(array('key','value'),$aTag->getStateVars());
	}
}
