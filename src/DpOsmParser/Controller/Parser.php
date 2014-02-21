<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Controller;

use Doctrine\DBAL\Types\Type;
use DpOsmParser\Model\Node;
use DpOsmParser\Model\Relation;
use DpOsmParser\Model\Way;
use DpOsmParser\ModelInterface\IRelationNodeCollection;
use DpZFExtensions\ServiceManager\TServiceLocator;
use Doctrine\Common\Collections\Collection;
use DpProfiler\Profiler;
use PDOException;
use Resque;
use Zend\Console\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class Parser
 *
 * @package DpOsmParser\Controller
 */
class Parser extends AbstractActionController implements ServiceLocatorAwareInterface {
	use TServiceLocator;

	/**
	 * @var resource
	 */
	protected $link;
	/**
	 * @var resource
	 */
	protected $xml;
	/**
	 * @var string
	 */
	protected $filename;
	/**
	 * @var object
	 */
	protected $current;
	/**
	 * @var integer
	 */
	protected $nodeStep;
	/**
	 * @var array
	 */
	protected $limit = array('node','way','relation');
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	/**
	 * @var array
	 */
	protected $memberRelationCache = array();
	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function _getEntityManager() {
		if (null === $this->em)
			$this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		return $this->em;
	}

	/**
	 * @param array $limit
	 */
	protected function _limit(array $limit) {
		$this->limit = $limit;
	}

	public function _truncate() {
		$conn = $this->_getEntityManager()->getConnection();
		if (in_array('relation',$this->limit,true)) {
			$conn->exec("TRUNCATE TABLE relation;");
			$conn->exec("TRUNCATE TABLE relationMemberNode;");
			$conn->exec("TRUNCATE TABLE relationMemberRelation;");
			$conn->exec("TRUNCATE TABLE relationMemberWay;");
			$conn->exec("TRUNCATE TABLE relationTag;");
		}
		if (in_array('way',$this->limit,true)) {
			$conn->exec("TRUNCATE TABLE way;");
			$conn->exec("TRUNCATE TABLE wayNode;");
			$conn->exec("TRUNCATE TABLE wayTag;");
		}
		if (in_array('node',$this->limit,true)) {
			$conn->exec("TRUNCATE TABLE node;");
			$conn->exec("TRUNCATE TABLE nodeTag;");
		}
	}

	public function importAction() {
		$request = $this->getRequest();

		// Make sure that we are running in a console and the user has not tricked our
		// application into running this action from a public web server.
		if (!$request instanceof Request)
			throw new \RuntimeException('You can only use this action from a console!');

		$filename = $request->getParam('osmFile');

		$limit = array('node','way','relation');
		if (!$request->getParam('aAll')) {
			if ($request->getParam('skipNodes')) unset($limit[0]);
			if ($request->getParam('skipWays')) unset($limit[1]);
			if ($request->getParam('skipRelations')) unset($limit[2]);
		}
		$this->_limit($limit);
		print("Updating these entities: ".implode(',',$limit).". Truncating ".($request->getParam('noTruncate')?'not.'."\n":'first:'));

		if (!$request->getParam('noTruncate')) {
			$this->_truncate();
			print(" done\n");
		}

		print("Import started...\n\n");

		$this->xml = xml_parser_create();
		xml_set_element_handler($this->xml,array($this,'start'),array($this,'end'));

		$this->filename = $filename;
		$file = fopen($this->filename,'r');
		while (($tmp = fgets($file)) !== false)
			xml_parse($this->xml,$tmp);
		fclose($file);

		if (!empty($this->memberRelationCache)) {
			trigger_error('Orphaned RelationRelations: '.var_export($this->memberRelationCache,true),E_USER_WARNING);
			$export = array();
			array_walk($this->memberRelationCache,function ($value,$key) use (&$export) {
				foreach ($value as $array)
					$export[] = '('.$key.','.$array['role'].','.$array['id'].')'."\n";
			});
			print('Full export: '.implode($export));
		}

		/** @var Profiler $profiler */
		$profiler = $this->getServiceLocator()->get('DpProfiler\Profiler');
		$profiler->trackPrintTime();
	}

	/**
	 * @param resource $parser
	 * @param string $name
	 * @param array $attr
	 */
	protected function start($parser,$name,$attr) {
		/** @var Profiler $profiler */
		$profiler = $this->getServiceLocator()->get('DpProfiler\Profiler');
		switch (strtolower($name)) {
			case 'osm': break;
			case 'node':
				if (!in_array('node',$this->limit,true))
					return;

				$profiler->track('nodeFactory',true);
				$this->current = $this->getServiceLocator()->get('DpOsmParser\Factory\NodeFactory')->create('Node',
				                                                  array(
						                                               'nodeId' => (int) $attr['ID'],
						                                               'lat' => (float) $attr['LAT'],
						                                               'lon' => (float) $attr['LON'],
						                                               'version' => (int) $attr['VERSION'],
						                                               'timestamp' => new \DateTime($attr['TIMESTAMP']),
						                                               'changeset' => (int) $attr['CHANGESET']
						                                          ));
				$profiler->track('nodeFactory',false);
				break;
			case 'way':
				if (!in_array('way',$this->limit,true))
					return;

				$profiler->track('wayFactory',true);
				$this->current = $this->getServiceLocator()->get('DpOsmParser\Factory\WayFactory')->create('Way',array(
	                                                                'wayId' => (int) $attr['ID'],
	                                                                'version' => (int) $attr['VERSION'],
	                                                                'timestamp' => new \DateTime($attr['TIMESTAMP']),
	                                                                'changeset' => (int) $attr['CHANGESET']
	                                                           ));
				$this->nodeStep = 0;
                $profiler->track('wayFactory',false);
				break;
			case 'relation':
				if (!in_array('relation',$this->limit,true))
					return;

                $profiler->track('relationFactory',true);
				$this->current = $this->getServiceLocator()->get('DpOsmParser\Factory\RelationFactory')->
					create('Relation',
				                                                  array(
		                                                               'relationId' => (int) $attr['ID'],
		                                                               'version' => (int) $attr['VERSION'],
		                                                               'timestamp' => new \DateTime($attr['TIMESTAMP']),
		                                                               'changeset' => (int) $attr['CHANGESET']
		                                                          ));
                $profiler->track('relationFactory',false);

				if (isset($this->memberRelationCache[$attr['ID']]) && is_array($this->memberRelationCache[$attr['ID']])) {
					foreach ($this->memberRelationCache[$attr['ID']] as $parentInfo) {
						$profiler->track('findRelation',true);
						/** @var Relation $parent */
						$parent = $this->_getEntityManager()->find('DpOsmParser\Model\Relation',$parentInfo['id']);
						$profiler->track('findRelation',false);
						$profiler->track('relationRelationFactory',true);
						$relationRelation = $this->getServiceLocator()->
							get('DpOsmParser\Factory\RelationRelationFactory')->create('RelationRelation',array(
                                                                               'relationParent' => $parent,
                                                                               'relationChild' => $this->current,
                                                                               'role' => $parentInfo['role']
                                                                          ));
						$profiler->track('relationRelationFactory',false);
						$parent->setServiceLocator($this->getServiceLocator());
						/** @var Relation $current */
						$current = $this->current;
						echo "Adding Member Relation [Backtrack]: ".$parent->getRelationId()." - ".$current->
							getRelationId()."\nClass: ".get_class($parent->getRelations())."\n";
						$parent->getRelations()->add($relationRelation);
					}
					unset($this->memberRelationCache[$attr['ID']]);
				}
				break;
			case 'tag':
				if (is_null($this->current))
					return;

				/** @var Collection $collection  */
				$collection = $this->current->getTags();
				$tagArray = array(
					'key' => $attr['K'],
					'value' => $attr['V'],
				);
				$tag = null;
				if ($this->current instanceof Node) {
                    $profiler->track('nodeTagFactory',true);
                    $tag = $this->getServiceLocator()->get('DpOsmParser\Factory\NodeTagFactory')->
	                    create('NodeTag',array('node' => $this->current) + $tagArray);
                    $profiler->track('nodeTagFactory',false);
                }
				if ($this->current instanceof Relation) {
                    $profiler->track('relationTagFactory',true);
					$tag = $this->getServiceLocator()->get('DpOsmParser\Factory\RelationTagFactory')->
						create('RelationTag',array('relation' => $this->current) +
						$tagArray);
                    $profiler->track('relationTagFactory',false);
                }
				if ($this->current instanceof Way) {
                    $profiler->track('wayTagFactory',true);
					$tag = $this->getServiceLocator()->get('DpOsmParser\Factory\WayTagFactory')->
						create('WayTag',array('way' => $this->current) + $tagArray);
                    $profiler->track('wayTagFactory',false);
                }
				$collection->add($tag);
				break;
			case 'nd':
				if (is_null($this->current))
					return;

				/** @var IRelationNodeCollection $collection  */
				$collection = $this->current->getWayNodes();
				$profiler->track('findNode',true);
				$node = $this->_getEntityManager()->find('DpOsmParser\Model\Node',$attr['REF']);
				$profiler->track('findNode',false);
				if (!is_null($node)) {
					$profiler->track('wayNodeFactory',true);
					$wayNode = $this->getServiceLocator()->get('DpOsmParser\Factory\WayNodeFactory')->
						create('WayNode',array(
					                                                            'way' => $this->current,
					                                                            'node' => $node,
					                                                            'step' => $this->nodeStep++
					                                                       ));
					$profiler->track('wayNodeFactory',false);
					$collection->add($wayNode);
				}
				else
					trigger_error('Could not find node: '.$attr['REF']);
				break;
			case 'member':
				if (is_null($this->current))
					return;

				/** @var Relation $current */
				$current = $this->current;
				switch ($attr['TYPE']) {
					case 'node':
						$collection = $current->getNodes();
                        $profiler->track('findNode',true);
                        $node = $this->_getEntityManager()->find('DpOsmParser\Model\Node',$attr['REF']);
                        $profiler->track('findNode',false);
						if (!is_null($node)) {
							$profiler->track('relationNodeFactory',true);
							$relationNode = $this->getServiceLocator()->get('DpOsmParser\Factory\RelationNodeFactory')->
								create('RelationNode',array(
							                                                                'relation' => $current,
							                                                                'node' => $node,
							                                                                'role' => $attr['ROLE']
							                                                           ));
							$profiler->track('relationNodeFactory',false);
							$collection->add($relationNode);
						}
						else
							trigger_error('Could not find node: '.$attr['REF'],E_USER_NOTICE);
						break;
					case 'way':
						$collection = $current->getWays();
                        $profiler->track('findWay',true);
                        $way = $this->_getEntityManager()->find('DpOsmParser\Model\Way',$attr['REF']);
                        $profiler->track('findWay',false);
						if (!is_null($way)) {
							$profiler->track('relationWayFactory',true);
							$relationWay = $this->getServiceLocator()->get('DpOsmParser\Factory\RelationWayFactory')->
								create('RelationWay',array(
			                                                                               'relation' => $current,
			                                                                               'way' => $way,
			                                                                               'role' => $attr['ROLE']
			                                                                          ));
							$profiler->track('relationWayFactory',false);
							$collection->add($relationWay);
						}
						else
							trigger_error('Could not find way: '.$attr['REF'],E_USER_NOTICE);
						break;
					case 'relation':
						$collection = $current->getRelations();
                        $profiler->track('findRelation',true);
                        $relation = $this->_getEntityManager()->find('DpOsmParser\Model\Relation',
                            $attr['REF']);
                        $profiler->track('findRelation',false);
						if (!is_null($relation)) {
							$profiler->track('relationRelationFactory',true);
							$relationRelation = $this->getServiceLocator()->
								get('DpOsmParser\Factory\RelationRelationFactory')->create('RelationRelation',array(
		                                                                             'relationParent' => $current,
		                                                                             'relationChild' => $relation,
		                                                                             'role' => $attr['ROLE']
		                                                                        ));
							$profiler->track('relationRelationFactory',false);
							echo "Adding Member Relation: ".$current->getRelationId()." - ".$relation->getRelationId()."\n";
							$collection->add($relationRelation);
						}
						else {
							if (!isset($this->memberRelationCache[$attr['REF']]) || !is_array($this->memberRelationCache[$attr['REF']]))
								$this->memberRelationCache[$attr['REF']] = array();
							$this->memberRelationCache[$attr['REF']][] = array('id' => $current->getRelationId(),'role' => $attr['ROLE']);
						}
						break;
				}
				break;
			default:
				echo "Unknown Tag: $name\n".var_export($attr,true)."\n";
		}
	}

	/**
	 * @param resource $parser
	 * @param string $name
	 */
	protected function end($parser,$name) {
		/** @var Profiler $profiler */
		$profiler = $this->getServiceLocator()->get('DpProfiler\Profiler');
		if (in_array(strtolower($name),array('tag','nd','member','osm'),true))
			return;
		elseif (in_array(strtolower($name),array('node','way','relation'))) {
			if (!in_array(strtolower($name),$this->limit,true))
				return;
			if ($this->current instanceof Relation)
				echo "Persisting Relation ".$this->current->getRelationId()."\n";
			$profiler->track('EntityManager->Persist',true);
			$this->_getEntityManager()->persist($this->current);
			$profiler->track('EntityManager->Persist',false);
			if (rand(0,(strtolower($name) == 'node'?1000:(strtolower($name) == 'way'?50:10))) == 1) {
				$profiler->track('EntityManager->Flush',true);
				$this->_getEntityManager()->flush();
				$profiler->track('EntityManager->Flush',false);
				$profiler->track('EntityManager->Clear',true);
				$this->_getEntityManager()->clear();
				$profiler->track('EntityManager->Clear',false);
				echo $profiler->trackPrintTime();
			}
			unset($this->current);
		}
		else
			echo "Unknown Tag: ".strtolower($name)."\n";
	}
	public function generateMultiPolygonAction() {
		$request = $this->getRequest();
		if (!$request instanceof Request)
			throw new \RuntimeException('You can only use this action from a console!');

		if ($request->getParam('processPerRelation'))
			$ppr = $request->getParam('processPerRelation');
		else
			$ppr = false;

		if ((is_string($request->getParam('instances')) || is_int($request->getParam('instances'))) &&
			((int) $request->getParam('instances')) > 1)
			$instances = (int) $request->getParam('instances');
		else
			$instances = 1;

		if ((is_string($request->getParam('limit')) || is_int($request->getParam('limit'))) &&
			((int) $request->getParam('limit')) >= 1)
			$limit = (int) $request->getParam('limit');
		else
			$limit = null;
		if ((is_string($request->getParam('offset')) || is_int($request->getParam('offset'))) &&
			((int) $request->getParam('offset')) >= 1)
			$offset = (int) $request->getParam('offset');
		else
			$offset = 0;

		$where = array();
		if (is_string($request->getParam('only')) && $request->getParam('only') !== '') {
			$only = str_getcsv($request->getParam('only'),',');
			foreach ($only as $onlyOne)
				$where[] = 'r._relationId = \''.$onlyOne.'\'';
		}
		elseif (is_string($request->getParam('prefix')) && $request->getParam('prefix') !== '') {
			$prefixes = str_getcsv($request->getParam('prefix'),',');
			foreach ($prefixes as $prefix)
				$where[] = 'r._relationId LIKE \''.$prefix.'%\'';
		}

		if ($ppr) {
			$query = $this->_getEntityManager()->createQuery('
				SELECT r._relationId
				FROM DpOsmParser\Model\Relation r '.
				(!empty($where)?'WHERE '.implode(' OR ',$where):''));
			if (!is_null($limit)) {
				$query->setMaxResults($limit);
				$query->setFirstResult($offset);
			}
			$relationIds = $query->getScalarResult();
			foreach ($relationIds as $relationId) {
				$relationId = $relationId['_relationId'];
				$args = array("command" => 'osm generate --only='.$relationId);
				Resque::enqueue("generateMultiPolygon", "Application", $args);
			}
		}
		elseif ($instances > 1) {
			$count = $this->_getEntityManager()->createQuery('
				SELECT COUNT(r._relationId) FROM DpOsmParser\Model\Relation r '.
				(!empty($where)?'WHERE '.implode(' OR ',$where):''))->getSingleScalarResult();
			$partitionSize = floor($count/$instances);
			$rest = $count-($instances*floor($count/$instances));
			for ($i = 0;$i < $instances;$i++) {
				$args = array("command" => 'osm generate --prefix='.$request->getParam('prefix').
					' --only='.$request->getParam('only').' --instances=1'.
					' --offset='.floor($i*$partitionSize).' --limit='.($partitionSize+($i+1 == $instances?$rest:0)));
				Resque::enqueue("generateMultiPolygon", "Application", $args);
			}
		}
		else {
			$query = $this->_getEntityManager()->createQuery('
				SELECT r
				FROM DpOsmParser\Model\Relation r '.
				(!empty($where)?'WHERE '.implode(' OR ',$where):''));
			if (!is_null($limit)) {
				$query->setMaxResults($limit);
				$query->setFirstResult($offset);
			}
			$result = $query->getResult();
			foreach ($result as $relation) {
				/** @var Relation $relation */
				$relation->setServiceLocator($this->getServiceLocator());
				$relation->generateMultiPolygon();
				if (rand(0,10) == 1)
					$this->_getEntityManager()->flush();
			}
			$this->_getEntityManager()->flush();
			exit;
		}
	}
}
