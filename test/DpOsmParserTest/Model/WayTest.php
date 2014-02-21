<?php
namespace DpOsmParserTest\Model;

use Doctrine\Common\Collections\ArrayCollection;
use DpOpenGis\Factory\LineStringFactory;
use DpOpenGis\Factory\PointFactory;
use DpOpenGis\Model\Point;
use DpOsmParser\Factory\NodeFactory;
use DpOsmParser\Factory\WayFactory;
use DpOsmParser\Factory\WayNodeFactory;
use DpOsmParser\Model\Node;
use DpOsmParser\Model\Way;
use DpPHPUnitExtensions\PHPUnit\TestCase;
use DpOsmParser\Model\WayNode;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class WayTest
 *
 * @package DpOsmParserTest\Model
 */
class WayTest extends TestCase {
	const SUT = 'DpOsmParser\Model\Way';
	/**
	 * @var \DpOsmParser\Model\Way
	 */
	protected $_way;
	/**
	 * @var array
	 */
	protected $_emptyState;
	/**
	 * @var ServiceManager
	 */
	protected $_manager;
	public function setUp() {
		parent::setUp();
		$this->_way = new Way();
		$this->_manager = new ServiceManager(new Config(array('invokables' => array(
				'DpOsmParser\Model\Node' => 'DpOsmParser\Model\Node',
				'DpOsmParser\Model\Way' => 'DpOsmParser\Model\Way',
				'DpOsmParser\Model\WayNode' => 'DpOsmParser\Model\WayNode',
				'DpOpenGis\Model\Point' => 'DpOpenGis\Model\Point',
			    'DpOpenGis\Model\LineString' => 'DpOpenGis\Model\LineString',
			    'DpOpenGis\ModelInterface\IPointCollection' => 'DpOpenGis\Collection\PointCollection',
			    'DpOpenGis\Validator\Point' => 'DpOpenGis\Validator\Point',
				'DpOpenGis\Validator\LineString' => 'DpOpenGis\Validator\LineString',
				'DpOpenGis\Validator\Polygon' => 'DpOpenGis\Validator\Polygon',
				'DpOpenGis\Validator\MultiPolygon' => 'DpOpenGis\Validator\MultiPolygon'
			),
            'factories' => array(
		        'DpOpenGis\Factory\PointFactory' => function (ServiceLocatorInterface $sm) {
		            PointFactory::getInstance()->setServiceLocator($sm);
		            return PointFactory::getInstance();
		        },
		        'DpOpenGis\Factory\WayFactory' => function (ServiceLocatorInterface $sm) {
			        WayFactory::getInstance()->setServiceLocator($sm);
			        return WayFactory::getInstance();
		        },
		        'DpOpenGis\Factory\LineStringFactory' => function (ServiceLocatorInterface $sm) {
			        LineStringFactory::getInstance()->setServiceLocator($sm);
			        return LineStringFactory::getInstance();
		        }
		    ))));
		$this->_way->setServiceLocator($this->_manager);
		NodeFactory::getInstance()->setServiceLocator($this->_manager);
        WayNodeFactory::getInstance()->setServiceLocator($this->_manager);
        WayFactory::getInstance()->setServiceLocator($this->_manager);
		$this->_emptyState = array(
			'wayId' => null,
			'lat' => null,
			'lon' => null,
			'version' => null,
			'timestamp' => null,
			'changeset' => null,
			'tags' => null,
			'wayNodes' => null,
			'lineString' => null);
	}
	public function testInitialState()
	{
		$way = clone $this->_way;

		$this->assertNull($way->getWayId());
		$this->assertNull($way->getVersion());
		$this->assertNull($way->getTimestamp());
		$this->assertNull($way->getChangeset());
		$this->assertNull($way->getTags());
		$this->assertNull($way->getWayNodes());
		$this->assertNull($way->getLineString());
	}
	public function testInitViaService()
	{
		$way = clone $this->_way;
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IWayTagCollection',
		                            'DpOsmParser\Collection\WayTagCollection');
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IWayNodeCollection',
		                            'DpOsmParser\Collection\WayNodeCollection');
		$this->assertInstanceOf('DpOsmParser\ModelInterface\IWayTagCollection',$way->getTags());
		$this->assertInstanceOf('DpOsmParser\ModelInterface\IWayNodeCollection',$way->getWayNodes());
	}
	public function testSettersGetters()
	{
		$way = clone $this->_way;
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IWayTagCollection',
		                                   'DpOsmParser\Collection\WayTagCollection');
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IWayNodeCollection',
		                                   'DpOsmParser\Collection\WayNodeCollection');
        $wayId = 123;
		$version = 44;
		$timestamp = new \DateTime();
		$changeset = 123;
		$points = new ArrayCollection();
		$node1Lat = 8.55;
		$node1Lon = 2.1;
		$node2Lat = 1.55;
		$node2Lon = 3.1;
		$node3Lat = 7.55;
		$node3Lon = 6.1;
		$nodes = array();
		$wayNodeArray = array('way' => $way,'step' => 0);
		$nodes[0] = WayNodeFactory::getInstance()->create('WayNode',$wayNodeArray + array(
			'node' => NodeFactory::getInstance()->create('Node',array('lon' => $node1Lon,'lat' => $node1Lat))));
		$nodes[1] = WayNodeFactory::getInstance()->create('WayNode',$wayNodeArray + array(
			'node' => NodeFactory::getInstance()->create('Node',array('lon' => $node2Lon,'lat' => $node2Lat))));
		$nodes[2] = WayNodeFactory::getInstance()->create('WayNode',$wayNodeArray + array(
			'node' => NodeFactory::getInstance()->create('Node',array('lon' => $node3Lon,'lat' => $node3Lat))));
		$nodes[3] = WayNodeFactory::getInstance()->create('WayNode',$wayNodeArray + array(
			'node' => NodeFactory::getInstance()->create('Node',array('lon' => $node1Lon,'lat' => $node1Lat))));
		$points->add($nodes[0]);
		$points->add($nodes[1]);
		$points->add($nodes[2]);
		$points->add($nodes[3]);
		$way->exchangeArray(array(
			                     'wayId' => $wayId,
			                     'version' => $version,
			                     'timestamp'  => $timestamp,
			                     'changeset' => $changeset,
								 'wayNodes' => $points
		                      ) +
			                     $this->_emptyState);
		$this->assertSame($wayId,$way->getWayId());
		$this->assertSame($version,$way->getVersion());
		$this->assertEquals($timestamp,$way->getTimestamp());
		$this->assertSame($changeset,$way->getChangeset());
		foreach ($way->getWayNodes() as $nr => $wayNode)
			/** @var WayNode $wayNode */
			$this->assertSame($nodes[$nr]->getNode(),$wayNode->getNode());
		foreach ($way->getLineString()->getPoints() as $nr => $point)
			/** @var Point $point */
			$this->assertEquals($point,$points->get($nr)->getNode()->getPoint());
	}
	public function testGetStateVars() {
		$node = clone $this->_way;
		$this->assertEquals(array('wayId','version','timestamp','changeset','tags','wayNodes','lineString'),
		                    $node->getStateVars());
	}
}
