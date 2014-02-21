<?php
namespace DpOsmParserTest\Model;

use Doctrine\Common\Collections\ArrayCollection;
use DpOpenGis\Collection\ReversePointCollection;
use DpOpenGis\Factory\LineStringFactory;
use DpOpenGis\Factory\MultiPolygonFactory;
use DpOpenGis\Factory\PointFactory;
use DpOpenGis\Factory\PolygonFactory;
use DpOpenGis\Model\LineString;
use DpOpenGis\Model\Point;
use DpOpenGis\Model\Polygon;
use DpOsmParser\Factory\NodeFactory;
use DpOsmParser\Factory\RelationFactory;
use DpOsmParser\Factory\RelationWayFactory;
use DpOsmParser\Factory\WayFactory;
use DpOsmParser\Factory\WayNodeFactory;
use DpOsmParser\Model\Node;
use DpOsmParser\Model\Relation;
use DpOsmParser\Model\RelationWay;
use DpOsmParser\Model\Way;
use DpPHPUnitExtensions\PHPUnit\TestCase;
use DpOsmParser\Model\WayNode;
use ReflectionClass;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class RelationTest
 *
 * @package DpOsmParserTest\Model
 */
class RelationTest extends TestCase {
	const SUT = 'DpOsmParser\Model\Relation';
	/**
	 * @var \DpOsmParser\Model\Relation
	 */
	protected $_relation;
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
		$this->_relation = new Relation();
		$this->_manager = new ServiceManager(new Config(array('invokables' => array(
			'DpOsmParser\Model\Node' => 'DpOsmParser\Model\Node',
			'DpOsmParser\Model\Way' => 'DpOsmParser\Model\Way',
			'DpOsmParser\Model\WayNode' => 'DpOsmParser\Model\WayNode',
			'DpOsmParser\Model\Relation' => 'DpOsmParser\Model\Relation',
			'DpOsmParser\Model\RelationNode' => 'DpOsmParser\Model\RelationNode',
			'DpOsmParser\Model\RelationWay' => 'DpOsmParser\Model\RelationWay',
			'DpOsmParser\Model\RelationRelation' => 'DpOsmParser\Model\RelationRelation',
			'DpOsmParser\ModelInterface\IWayNodeCollection' => 'DpOsmParser\Collection\WayNodeCollection',
			'DpOsmParser\ModelInterface\IWayTagCollection' => 'DpOsmParser\Collection\WayTagCollection',
			'DpOpenGis\Model\Point' => 'DpOpenGis\Model\Point',
			'DpOpenGis\Model\LineString' => 'DpOpenGis\Model\LineString',
            'DpOpenGis\Model\Polygon' => 'DpOpenGis\Model\Polygon',
            'DpOpenGis\Model\MultiPolygon' => 'DpOpenGis\Model\MultiPolygon',
            'DpOpenGis\ModelInterface\IPointCollection' => 'DpOpenGis\Collection\PointCollection',
            'DpOpenGis\ModelInterface\ILineStringCollection' => 'DpOpenGis\Collection\LineStringCollection',
            'DpOpenGis\ModelInterface\IPolygonCollection' => 'DpOpenGis\Collection\PolygonCollection',
            'DpOpenGis\Validator\Point' => 'DpOpenGis\Validator\Point',
            'DpOpenGis\Validator\LineString' => 'DpOpenGis\Validator\LineString',
            'DpOpenGis\Validator\Polygon' => 'DpOpenGis\Validator\Polygon',
            'DpOpenGis\Validator\MultiPolygon' => 'DpOpenGis\Validator\MultiPolygon'
		),
            'factories' => array(
			    'DpOsmParser\Factory\NodeFactory' => function (ServiceLocatorInterface $sm) {
				    NodeFactory::getInstance()->setServiceLocator($sm);
				    return WayFactory::getInstance();
			    },
			    'DpOsmParser\Factory\WayFactory' => function (ServiceLocatorInterface $sm) {
				    WayFactory::getInstance()->setServiceLocator($sm);
				    return WayFactory::getInstance();
			    },
		        'DpOsmParser\Factory\RelationFactory' => function (ServiceLocatorInterface $sm) {
			        RelationFactory::getInstance()->setServiceLocator($sm);
			        return RelationFactory::getInstance();
		        },
		        'DpOpenGis\Factory\PointFactory' => function (ServiceLocatorInterface $sm) {
			        PointFactory::getInstance()->setServiceLocator($sm);
			        return PointFactory::getInstance();
		        },
		        'DpOpenGis\Factory\LineStringFactory' => function (ServiceLocatorInterface $sm) {
			        LineStringFactory::getInstance()->setServiceLocator($sm);
			        return LineStringFactory::getInstance();
		        },
		        'DpOpenGis\Factory\PolygonFactory' => function (ServiceLocatorInterface $sm) {
			        PolygonFactory::getInstance()->setServiceLocator($sm);
					return PolygonFactory::getInstance();
				},
		        'DpOpenGis\Factory\MultiPolygonFactory' => function (ServiceLocatorInterface $sm) {
			        MultiPolygonFactory::getInstance()->setServiceLocator($sm);
			        return MultiPolygonFactory::getInstance();
		        },
		        'DpOpenGis\ModelInterface\IReversePointCollection' => function (ServiceLocatorInterface $sm) {
			        $cl = new ReversePointCollection();
			        $cl->setServiceLocator($sm);
			        return $cl;
		        },
            ))));
		$this->_relation->setServiceLocator($this->_manager);
		NodeFactory::getInstance()->setServiceLocator($this->_manager);
		WayNodeFactory::getInstance()->setServiceLocator($this->_manager);
		WayFactory::getInstance()->setServiceLocator($this->_manager);
		RelationFactory::getInstance()->setServiceLocator($this->_manager);
		RelationWayFactory::getInstance()->setServiceLocator($this->_manager);
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
		$relation = clone $this->_relation;

		$this->assertNull($relation->getRelationId());
		$this->assertNull($relation->getVersion());
		$this->assertNull($relation->getTimestamp());
		$this->assertNull($relation->getChangeset());
		$this->assertNull($relation->getTags());
		$this->assertNull($relation->getRelations());
		$this->assertNull($relation->getNodes());
		$this->assertNull($relation->getWays());
		$this->assertNull($relation->getMultiPolygon());
	}
	public function testInitViaService()
	{
		$relation = clone $this->_relation;
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationTagCollection',
		                                   'Doctrine\Common\Collections\ArrayCollection');
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationNodeCollection',
		                                   'Doctrine\Common\Collections\ArrayCollection');
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationWayCollection',
		                                   'Doctrine\Common\Collections\ArrayCollection');
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationRelationCollection',
		                                   'Doctrine\Common\Collections\ArrayCollection');
		$this->assertInstanceOf('Doctrine\Common\Collections\Collection',$relation->getTags());
		$this->assertInstanceOf('Doctrine\Common\Collections\Collection',$relation->getNodes());
		$this->assertInstanceOf('Doctrine\Common\Collections\Collection',$relation->getWays());
		$this->assertInstanceOf('Doctrine\Common\Collections\Collection',$relation->getRelations());
	}
	public function testSettersGetters()
	{
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationTagCollection',
		                                   'DpOsmParser\Collection\RelationTagCollection');
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationNodeCollection',
		                                   'DpOsmParser\Collection\RelationNodeCollection');
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationWayCollection',
		                                   'DpOsmParser\Collection\RelationWayCollection');
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationRelationCollection',
		                                   'DpOsmParser\Collection\RelationRelationCollection');
		$relation = clone $this->_relation;
		$relation2 = new Relation();
		$relation2->setServiceLocator($this->_manager);
		$expectedPoints = array(
			array(
				array(
					array(0.0, 1.0),
					array(1.0, 1.0),
					array(1.0, 0.0),
					array(0.0, 0.0),
					array(0.0, 1.0)
				),
				array(
					array(0.0, 0.5),
					array(0.5, 0.0),
					array(0.0, 0.0),
					array(0.0, 0.5)
				),
			),
			array(
				array(
					array(1.0, 2.0),
					array(2.0, 2.0),
					array(3.0, 2.0),
					array(3.0, 1.0),
					array(2.0, 1.0),
					array(1.0, 1.0),
					array(1.0, 2.0)
				),
				array(
					array(1.0, 1.6),
					array(1.6, 1.0),
					array(1.0, 1.0),
					array(1.0, 1.6)
				),
			));
		$points = $expectedPoints;
		$points[1][0] = array(
			array(2.0, 1.0),
			array(1.0, 1.0),
			array(1.0, 2.0)

		);
		$points[2] = array(array(
			                   array(1.0, 2.0),
			                   array(2.0, 2.0),
			                   array(3.0, 2.0),
		                   ));
		$points[3] = array(array(
			                   array(2.0, 1.0),
			                   array(3.0, 1.0),
			                   array(3.0, 2.0),
		                   ));

		$points2 = $points;
		$points2[4] = array(array(
			                   array(4.0, 5.0),
			                   array(4.0, 4.0),
			                   array(5.0, 4.0),
		                   ));

		$relationId = 123;
		$version = 44;
		$timestamp = new \DateTime();
		$changeset = 123;
		$ways = $relation->getWays();
		$ways2 = $relation2->getWays();
		foreach ($points2 as $nr => $polygon) {
			$second = false;
			if (isset($points[$nr]))
				$second = true;
			foreach ($polygon as $nr2 => $line) {
				/** @var Way $way */
				if ($second) {
					$way = WayFactory::getInstance()->create('Way',array());
					$relationWay = RelationWayFactory::getInstance()->create('RelationWay',
					                                                         array('relation' => $relation,
					                                                               'way' => $way,
					                                                               'role' => ($nr2 == 0?'outer':'inner')));
					$ways[] = $relationWay;
					$wayNodes = $way->getWayNodes();
					$wayNodeArray = array('way' => $way);
				}
				$way2 = WayFactory::getInstance()->create('Way',array());
				$relationWay2 = RelationWayFactory::getInstance()->create('RelationWay',array('relation' => $relation2,
				                                                                             'way' => $way2,
				                                                                             'role' => ($nr2 == 0?'outer':'inner')));
				$ways2[] = $relationWay2;
				$wayNodes2 = $way2->getWayNodes();
				$wayNodeArray2 = array('way' => $way2);

				$step = 0;
				foreach ($line as $point) {
					if ($second)
						$wayNodes[] = WayNodeFactory::getInstance()->create('WayNode',$wayNodeArray + array(
							'node' => NodeFactory::getInstance()->create('Node',
							                                             array('lon' => $point[0],'lat' => $point[1])),
							'step' => $step));
					$wayNodes2[] = WayNodeFactory::getInstance()->create('WayNode',$wayNodeArray2 + array(
						'node' => NodeFactory::getInstance()->create('Node',
						                                             array('lon' => $point[0],'lat' => $point[1])),
						'step' => $step++));
				}
			}
		}
		$relation->exchangeArray(array(
			                         'relationId' => $relationId,
			                         'version' => $version,
			                         'timestamp'  => $timestamp,
			                         'changeset' => $changeset,
			                         'ways' => $ways
		                         ) +
			                         $this->_emptyState);
		$relation2->exchangeArray(array(
			                         'relationId' => $relationId,
			                         'version' => $version,
			                         'timestamp'  => $timestamp,
			                         'changeset' => $changeset,
			                         'ways' => $ways2
		                         ) +
			                         $this->_emptyState);
		$this->assertSame($relationId,$relation->getRelationId());
		$this->assertSame($version,$relation->getVersion());
		$this->assertEquals($timestamp,$relation->getTimestamp());
		$this->assertSame($changeset,$relation->getChangeset());

		$expectedOuters = array();
		$expectedInners = array();
		foreach ($points as $poly)
			foreach ($poly as $nr => $lin)
				if ($nr == 0)
					$expectedOuters[] = $lin;
				else
					$expectedInners[] = $lin;

		$outers = array();
		$inners = array();
		foreach ($relation->getWays() as $relationWay) {
			/** @var RelationWay $relationWay */
			$lin = array();
			foreach ($relationWay->getWay()->getWayNodes() as $wayNode)
				/** @var WayNode $wayNode */
				$lin[] = array($wayNode->getNode()->getPoint()->getLon(), $wayNode->getNode()->getPoint()->getLat());
			if ($relationWay->getRole() == 'outer')
				$outers[] = $lin;
			else
				$inners[] = $lin;
		}
		$this->assertEquals($expectedOuters,$outers);
		$this->assertEquals($expectedInners,$inners);
		foreach ($relation->getMultiPolygon()->getPolygons() as $nr => $polygon) {
			/** @var Polygon $polygon */
			$stack = array();
			$prev = null;
			$foundBegin = false;
			$checkStack = array();
			foreach ($polygon->getOuter()->getPoints() as $point) {
				if ($prev == $point)
					continue;
				/** @var Point $point */
				if (!$foundBegin && !($point->getLon() == $expectedPoints[$nr][0][0][0] &&
					$point->getLat() == $expectedPoints[$nr][0][0][1]))
					array_push($stack,$point);
				else {
					$foundBegin = true;
					$checkStack[] = $point;
				}
				$prev = $point;
			}
			foreach ($stack as $recoverPoint) {
				if ($prev == $recoverPoint)
					continue;
				$checkStack[] = $recoverPoint;
				$prev = $recoverPoint;
			}
			$this->assertNotEmpty($checkStack);
			foreach ($checkStack as $nr2 => $point) {
				$this->assertSame($point->getLon(),$expectedPoints[$nr][0][$nr2][0]);
				$this->assertSame($point->getLat(),$expectedPoints[$nr][0][$nr2][1]);
			}

			foreach ($polygon->getInners() as $nr2 => $lineString) {
				/** @var LineString $lineString */

				$stack = array();
				$prev = null;
				$foundBegin = false;
				$checkStack = array();
				foreach ($lineString->getPoints() as $point) {
					if ($prev == $point)
						continue;
					/** @var Point $point */
					if (!$foundBegin && !($point->getLon() == $expectedPoints[$nr][$nr2+1][0][0] &&
						$point->getLat() == $expectedPoints[$nr][$nr2+1][0][1]))
						array_push($stack,$point);
					else {
						$foundBegin = true;
						$checkStack[] = $point;
					}
					$prev = $point;
				}
				foreach ($stack as $recoverPoint) {
					if ($prev == $recoverPoint)
						continue;
					$checkStack[] = $recoverPoint;
					$prev = $recoverPoint;
				}
				$this->assertNotEmpty($checkStack);
				foreach ($checkStack as $nr3 => $point) {
					$this->assertSame($point->getLon(),$expectedPoints[$nr][$nr2+1][$nr3][0]);
					$this->assertSame($point->getLat(),$expectedPoints[$nr][$nr2+1][$nr3][1]);
				}
			}
		}
		$this->setExpectedException('PHPUnit_Framework_Error_Warning');
		$relation2->getMultiPolygon();
	}
	public function testGetStateVars() {
		$relation = clone $this->_relation;
		$this->assertEquals(array('relationId','version','timestamp','changeset','tags','nodes','ways','relations',
		                          'multiPolygon'), $relation->getStateVars());
	}
	public function testDecorator() {
		$relation = clone $this->_relation;
		$newManager = clone $this->_manager;
		$this->_manager->setInvokableClass('DpOsmParser\ModelInterface\IRelationRelationCollection',
		                                   'Doctrine\Common\Collections\ArrayCollection');
		$originalCollection = $relation->getRelations();
		$this->assertEquals('Doctrine\Common\Collections\ArrayCollection',get_class($originalCollection));

		$relation->setServiceLocator($newManager);
		$newManager->setInvokableClass('DpOsmParser\ModelInterface\IRelationRelationCollection',
		                                         'DpOsmParser\Collection\RelationRelationCollection');
		$this->assertEquals('DpOsmParser\Collection\RelationRelationCollection',get_class($relation->getRelations()));

		$class = new ReflectionClass('DpOsmParser\Collection\RelationRelationCollection');
		$property = $class->getProperty('_decoree');
		$property->setAccessible(true);
		$this->assertEquals($originalCollection,$property->getValue($relation->getRelations()));
	}
}
