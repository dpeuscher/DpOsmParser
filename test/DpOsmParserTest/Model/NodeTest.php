<?php
namespace DpOsmParserTest\Model;

use DpOpenGis\Factory\PointFactory;
use DpOsmParser\Model\Node;
use DpPHPUnitExtensions\PHPUnit\TestCase;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class NodeTest
 *
 * @package DpOsmParserTest\Model
 */
class NodeTest extends TestCase {
	const SUT = 'DpOsmParser\Model\Node';
	/**
	 * @var \DpOsmParser\Model\Node
	 */
	protected $_node;
	/**
	 * @var array
	 */
	protected $_emptyState;
	public function setUp() {
		parent::setUp();
		$this->_node = new Node();
		$manager = new ServiceManager(new Config(array(
		   'invokables' => array(
				'DpOpenGis\Model\Point' => 'DpOpenGis\Model\Point',
				'DpOpenGis\Validator\Point' => 'DpOpenGis\Validator\Point'),
		   'factories' => array(
		       'DpOpenGis\Factory\PointFactory' => function (ServiceLocatorInterface $sm) {
		           PointFactory::getInstance()->setServiceLocator($sm);
		           return PointFactory::getInstance();
		       }
		   ))));
		$this->_node->setServiceLocator($manager);
		$this->_emptyState = array(
			'nodeId' => null,
			'lat' => null,
			'lon' => null,
			'version' => null,
			'timestamp' => null,
			'changeset' => null,
			'tags' => null,
			'point' => null);
	}
	public function testInitialState()
	{
		$node = clone $this->_node;

		$this->assertNull($node->getNodeId());
		$this->assertNull($node->getLat());
		$this->assertNull($node->getLon());
		$this->assertNull($node->getVersion());
		$this->assertNull($node->getTimestamp());
		$this->assertNull($node->getChangeset());
		$this->assertNull($node->getTags());
		$this->assertNull($node->getPoint());
	}
	public function testInitViaService()
	{
		$node = clone $this->_node;
		$collection = $this->getMock('DpOsmParser\ModelInterface\INodeTagCollection');
        /** @var \Zend\ServiceManager\ServiceLocatorInterface $serviceManager */
		$serviceManager = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
		$serviceManager->expects($this->atLeastOnce())->method('has')->
			with('DpOsmParser\ModelInterface\INodeTagCollection')->will($this->returnValue(true));
		$serviceManager->expects($this->atLeastOnce())->method('get')->
			with('DpOsmParser\ModelInterface\INodeTagCollection')->will($this->returnValue($collection));
		$node->setServiceLocator($serviceManager);
		$this->assertEquals($collection,$node->getTags());
		$this->assertInstanceOf('DpOsmParser\ModelInterface\INodeTagCollection',$node->getTags());
	}
	public function testSettersGetters()
	{
		$node = clone $this->_node;
        $nodeId = 123;
        $lat = 8.55;
		$lon = 2.1;
		$version = 44;
		$timestamp = new \DateTime();
		$changeset = 123;
		$node->exchangeArray(array(
			                     'nodeId' => $nodeId,
			                     'lat' => $lat,
			                     'lon' => $lon,
			                     'version' => $version,
			                     'timestamp'  => $timestamp,
			                     'changeset' => $changeset,
		                      ) +
			                     $this->_emptyState);
		$this->assertSame($nodeId,$node->getNodeId());
		$this->assertSame($lat,$node->getLat());
		$this->assertSame($lon,$node->getLon());
		$this->assertSame($version,$node->getVersion());
		$this->assertEquals($timestamp,$node->getTimestamp());
		$this->assertSame($changeset,$node->getChangeset());
		$this->assertSame($lat,$node->getPoint()->getLat());
		$this->assertSame($lon,$node->getPoint()->getLon());
	}
	public function testGetStateVars() {
		$node = clone $this->_node;
		$this->assertEquals(array('nodeId','lat','lon','version','timestamp','changeset','tags','point'),
		                    $node->getStateVars());
	}
}
