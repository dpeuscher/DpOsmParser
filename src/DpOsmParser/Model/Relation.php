<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Model;


use DpZFExtensions\Validator\Exception\InvalidStateException;
use DpDoctrineExtensions\Collection\TDecoreeCollection;
use DpOpenGis\Model\LineString;
use DpOpenGis\ModelInterface\IPolygonCollection;
use DpOpenGis\ModelInterface\IReversePointCollection;
use DpOsmParser\ModelInterface\IRelationNodeCollection;
use DpOsmParser\ModelInterface\IRelationRelationCollection;
use DpOsmParser\ModelInterface\IRelationTagCollection;
use DpOsmParser\ModelInterface\IRelationWayCollection;
use DpOpenGis\Model\MultiPolygon;
use DpOpenGis\Model\Polygon;
use DpZFExtensions\ServiceManager\TServiceLocator;
use DpZFExtensions\Validator\IExchangeState;
use DpZFExtensions\Validator\TExchangeState;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Exception;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class Relation
 *
 * @package DpOsmParser\Model
 */
class Relation implements ServiceLocatorAwareInterface,IExchangeState {
	use TDecoreeCollection,TServiceLocator,TExchangeState {
      TExchangeState::exchangeArray as defaultExchangeArray;
    }
	/**
	 * @var int
	 */
	protected $_relationId;
	/**
	 * @var int
	 */
	protected $_version;
	/**
	 * @var DateTime
	 */
	protected $_timestamp;
	/**
	 * @var int
	 */
	protected $_changeset;
	/**
	 * @var IRelationTagCollection
	 */
	protected $_tags;
	/**
	 * @var IRelationNodeCollection
	 */
	protected $_nodes;
	/**
	 * @var IRelationWayCollection
	 */
	protected $_ways;
	/**
	 * @var IRelationRelationCollection
	 */
	protected $_relations;
	/**
	 * @var MultiPolygon
	 */
	protected $_multiPolygon;
	/**
	 * @var bool
	 */
	protected $_multiPolygonGenerated = false;

	/**
	 * @return int
	 */
	public function getChangeset() {
		return $this->_changeset;
	}

	/**
	 * @return int
	 */
	public function getRelationId() {
		return $this->_relationId;
	}

	/**
	 * @return \DpOsmParser\ModelInterface\IRelationNodeCollection
	 */
	public function getNodes() {
		return $this->_getDecoreeCollection('_nodes','DpOsmParser\ModelInterface\IRelationNodeCollection');
	}

	/**
	 * @param $relations
	 * @param $inners
	 * @param $outers
	 */
	protected function _aggregateRelations($relations,&$inners,&$outers) {
		/** @var IRelationRelationCollection $relations */
		foreach ($relations as $relationRelation) {
			/** @var RelationRelation $relationRelation */
			$aggregate = false;
			$use = false;
			foreach ($relationRelation->getRelationChild()->getTags() as $relationTag) {
				/** @var RelationTag $relationTag */
				if ($relationTag->getKey() == 'type') {
					if (strtolower($relationTag->getValue()) == 'multilinestring') {
						echo "Aggregate: Use wayMembers of relation ".$relationRelation->getRelationChild()->getRelationId()." in ".$this->getRelationId()." as multilinestring\n";
						$use = true;
					}
					elseif (strtolower($relationTag->getValue()) == 'multipolygon') {
						echo "Aggregate: Use wayMembers of relation ".$relationRelation->getRelationChild()->getRelationId()." in ".$this->getRelationId()." as multipolygon\n";
						$aggregate = true;
					}
					else {
						echo "Aggregate: Not able to aggregate ".$relationRelation->getRelationChild()->getRelationId()." in ".$this->getRelationId()." [no strategy for type ".$relationTag->getValue()."]\n";
						continue 2;
					}
				}
			}
			if ($aggregate)
				$this->_aggregateRelations($relationRelation->getRelationChild()->getRelations(),$inners,$outers);
			if ($use) {
				if ($relationRelation->getRole() == 'inner')
					foreach ($relationRelation->getRelationChild()->getWays() as $relationWay) {
						/** @var RelationWay $relationWay */
						echo "Aggregate: Use way ".$relationWay->getWay()->getWayId()." of relation ".$relationRelation->getRelationChild()->getRelationId()." in ".$this->getRelationId()." as ".$relationRelation->getRole()."\n";
						/** @var RelationWay $relationWay */
						$inners[] = array('parent' => $relationWay->getWay()->getLineString(),'children' => array());
				}
				elseif ($relationRelation->getRole() == 'outer')
					foreach ($relationRelation->getRelationChild()->getWays() as $relationWay) {
						echo "Aggregate: Use way ".$relationWay->getWay()->getWayId()." of relation ".$relationRelation->getRelationChild()->getRelationId()." in ".$this->getRelationId()." as ".$relationRelation->getRole()."\n";
						/** @var RelationWay $relationWay */
						$outers[] = array('parent' => $relationWay->getWay()->getLineString(),'children' => array());
					}
				else
					foreach ($relationRelation->getRelationChild()->getWays() as $relationWay) {
						echo "Aggregate: Use way ".$relationWay->getWay()->getWayId()." of relation ".$relationRelation->getRelationChild()->getRelationId()." in ".$this->getRelationId()." as ".$relationRelation->getRole()."\n";
						/** @var RelationWay $relationWay */
						if ($relationWay->getRole() == 'inner')
                                                	$inners[] = array('parent' => $relationWay->getWay()->getLineString(),'children' => array());
						elseif ($relationWay->getRole() == 'outer')
                                                	$outers[] = array('parent' => $relationWay->getWay()->getLineString(),'children' => array());
					}
			}
		}
	}

	public function generateMultiPolygon($force = false) {
		if ((!$this->_multiPolygonGenerated || $force) && $this->getServiceLocator()->has('DpOpenGis\Factory\MultiPolygonFactory')
			&& !is_null($this->getWays()) && (!$this->getWays()->isEmpty() || !$this->getRelations()->isEmpty())) {
			echo "Generating MultiPolygon inModel [RelationId:".$this->getRelationId()."]\n";
			$inners = array();
			$outers = array();
			foreach ($this->getWays() as $relationWay) {
				/** @var RelationWay $relationWay */
				if ($relationWay->getRole() == 'inner')
					$inners[] = array('parent' => $relationWay->getWay()->getLineString(),'children' => array());
				elseif ($relationWay->getRole() == 'outer')
					$outers[] = array('parent' => $relationWay->getWay()->getLineString(),'children' => array());
			}
			$this->_aggregateRelations($this->getRelations(),$inners,$outers);
			$inners = $this->_connectNonRings($inners);
			$outers = $this->_connectNonRings($outers);
			foreach ($inners as &$innerRef) {
				/** @var LineString $innerParent */
				$innerParent = $innerRef['parent'];
				foreach ($outers as $outer) {
					/** @var LineString $outerParent */
					$outerParent = $outer['parent'];
					try {
						if ($innerParent->Contains($outerParent))
							$innerRef['children'][] = $outer;
					}
					catch (Exception $e) {
						trigger_error('Could not add polygon because of exception: '.$e,E_USER_NOTICE);
						print('Full exception-data: '.$e."\n");
					}
				}
			}
			foreach ($outers as &$outerRef) {
				/** @var LineString $outerParent */
				$outerParent = $outerRef['parent'];
				foreach ($inners as $inner) {
					/** @var LineString $innerParent */
					$innerParent = $inner['parent'];
					try {
						if ($outerParent->Contains($innerParent))
							$outerRef['children'][] = $inner;
					}
					catch (Exception $e) {
						trigger_error('Could not add polygon because of exception: '.$e,E_USER_NOTICE);
						print('Full exception-data: '.$e."\n");
					}
				}
			}
			$outers = $this->_removeDuplicates($outers);
			/** @var IPolygonCollection $polygons */
			$polygons = clone $this->getServiceLocator()->get('DpOpenGis\ModelInterface\IPolygonCollection');
			foreach ($outers as $outer) {
				/** @var Collection $inners */
				$inners = clone $this->getServiceLocator()->get('DpOpenGis\ModelInterface\ILineStringCollection');
				foreach ($outer['children'] as $inner)
					$inners->add($inner['parent']);
				try {
					/** @var Polygon $polygon */
					$polygon = $this->getServiceLocator()->get('DpOpenGis\Factory\PolygonFactory')->
						create('Polygon',array('outer' => $outer['parent'],'inners' => $inners));
					$polygons->add($polygon);
				} catch (InvalidStateException $e) {
					trigger_error('Could not add polygon because of an invalid state: '.$e,E_USER_NOTICE);
					print('Full exception-data: '.$e."\n");
				}
				catch (Exception $e) {
					trigger_error('Could not add polygon because of exception: '.$e,E_USER_NOTICE);
					print('Full exception-data: '.$e."\n");
				}
			}
			try {
				$this->_multiPolygon = $this->getServiceLocator()->get('DpOpenGis\Factory\MultiPolygonFactory')->
					create('MultiPolygon',array('polygons' => $polygons));
				if (!is_null($this->_multiPolygon))
					$this->_multiPolygonGenerated = true;
			} catch (InvalidStateException $e) {
				trigger_error('Could not add multiPolygon for relation ('.$this->getRelationId().') because of an invalid state: '.$e,E_USER_WARNING);
				print('Full exception-data: '.$e."\n");
				$this->_multiPolygon = null;
				$this->_multiPolygonGenerated = false;
			}
		}
		//else
			//echo "Could not generate MultiPolygon inModel [RelationId:".$this->getRelationId()."] Cause: ".var_export(!$this->_multiPolygonGenerated,true).','.var_export($force,true).','.var_export($this->getServiceLocator()->has('DpOpenGis\Factory\MultiPolygonFactory'),true).','.var_export(!is_null($this->getWays()),true).','.var_export(!$this->getWays()->isEmpty(),true).",".var_export(!$this->getRelations()->isEmpty(),true)."\n";
	}

	/**
	 * @return \DpOpenGis\Model\MultiPolygon
	 */
	public function getMultiPolygon() {
		if (!$this->_multiPolygonGenerated)
			$this->generateMultiPolygon();
		return $this->_multiPolygon;
	}

	/**
	 * @return \DpOsmParser\ModelInterface\IRelationRelationCollection
	 */
	public function getRelations() {
		return $this->_getDecoreeCollection('_relations','DpOsmParser\ModelInterface\IRelationRelationCollection');
	}

	/**
	 * @return \DpOsmParser\ModelInterface\IRelationTagCollection
	 */
	public function getTags() {
		return $this->_getDecoreeCollection('_tags','DpOsmParser\ModelInterface\IRelationTagCollection');
	}

	/**
	 * @return \DateTime
	 */
	public function getTimestamp() {
		return $this->_timestamp;
	}

	/**
	 * @return int
	 */
	public function getVersion() {
		return $this->_version;
	}

	/**
	 * @return \DpOsmParser\ModelInterface\IRelationWayCollection
	 */
	public function getWays() {
		return $this->_getDecoreeCollection('_ways','DpOsmParser\ModelInterface\IRelationWayCollection');
	}

	/**
	 * @param array $polygons
	 * @param array $found
	 * @return array
	 */
	protected function _removeDuplicates(array $polygons,array &$found = array()) {
		foreach ($polygons as $polygonArray) {
			$this->_removeDuplicates($polygonArray['children'],$found);
			foreach ($polygonArray['children'] as $nr => &$childPolygonArray)
				if (in_array($childPolygonArray['parent'],$found,true))
					unset($polygonArray['children'][$nr]);
		}
		return $polygons;
	}

	/**
	 * @param array $lineStrings
	 * @return array
	 */
	protected function _connectNonRings(array $lineStrings) {
		$fullLines = array();
		$openLines1 = array();
		$openLines2 = array();
		foreach ($lineStrings as $lineStringArray) {
			/** @var LineString $lineString */
			$lineString = $lineStringArray['parent'];
			if ($lineString->IsRing())
				$fullLines[] = $lineStringArray;
			else {
				$startPoint = $lineString->StartPoint();
				$endPoint = $lineString->EndPoint();
				/** @var IReversePointCollection $points */
				$points = clone $this->getServiceLocator()->get('DpOpenGis\ModelInterface\IReversePointCollection');
				$points->setOriginalPointCollection($lineString->getPoints());
				$reverseLineString = $this->getServiceLocator()->get('DpOpenGis\Factory\LineStringFactory')->
					create('LineString',array('points' => $points));
				if (!isset($openLines1[$startPoint->getLon().','.$startPoint->getLat()]))
					$target1 = &$openLines1;
				elseif (!isset($openLines2[$startPoint->getLon().','.$startPoint->getLat()]))
					$target1 = &$openLines2;
				else {
					trigger_error('Way with StartPoint '.$startPoint->getLon().','.$startPoint->getLat().
						              ' already has a connector.',E_USER_WARNING);
					continue;
				}
				if (!isset($openLines1[$endPoint->getLon().','.$endPoint->getLat()]))
					$target2 = &$openLines1;
				elseif (!isset($openLines2[$endPoint->getLon().','.$endPoint->getLat()]))
					$target2 = &$openLines2;
				else {
					trigger_error('Way with EndPoint '.$endPoint->getLon().','.$endPoint->getLat().
						              ' already has a connector.',E_USER_WARNING);
					continue;
				}
				$target1[$startPoint->getLon().','.$startPoint->getLat()] = array($lineString,$reverseLineString);
				$target2[$endPoint->getLon().','.$endPoint->getLat()] = array($reverseLineString,$lineString);
			}
		}
		while (($testLines = array_shift($openLines1)) || ($testLines = array_shift($openLines2))) {
			list($testLine,$testLineReverse) = $testLines;
			/** @var LineString $testLine */
			$endPoint = $testLine->EndPoint();
			$startPoint = $testLine->StartPoint();
			/** @var LineString $secondLine */
			if (isset($openLines1[$endPoint->getLon().','.$endPoint->getLat()])
				&& $testLineReverse !== $openLines1[$endPoint->getLon().','.$endPoint->getLat()][0]) {
				list($secondLine,$secondLineReverse) = $openLines1[$endPoint->getLon().','.$endPoint->getLat()];
				unset($openLines1[$endPoint->getLon().','.$endPoint->getLat()]);
				unset($openLines2[$endPoint->getLon().','.$endPoint->getLat()]);
			}
			elseif (isset($openLines2[$endPoint->getLon().','.$endPoint->getLat()])
				&& $testLineReverse !== $openLines2[$endPoint->getLon().','.$endPoint->getLat()][0]) {
				list($secondLine,$secondLineReverse) = $openLines2[$endPoint->getLon().','.$endPoint->getLat()];
				unset($openLines2[$endPoint->getLon().','.$endPoint->getLat()]);
				unset($openLines1[$endPoint->getLon().','.$endPoint->getLat()]);
			}
			else {
				trigger_error('Could not find way that connects to ('.$endPoint->getLon().','.$endPoint->getLat().') '.
					              '[RelationId:'.$this->_relationId.']',E_USER_WARNING);
				continue;
			}

			$secondEnd = $secondLine->EndPoint();
			if (isset($openLines1[$secondEnd->getLon().','.$secondEnd->getLat()]) &&
				$openLines1[$secondEnd->getLon().','.$secondEnd->getLat()][0] === $secondLineReverse)
				unset($openLines1[$secondEnd->getLon().','.$secondEnd->getLat()]);
			elseif (isset($openLines2[$secondEnd->getLon().','.$secondEnd->getLat()]) &&
				$openLines2[$secondEnd->getLon().','.$secondEnd->getLat()][0] === $secondLineReverse)
				unset($openLines2[$secondEnd->getLon().','.$secondEnd->getLat()]);
			else
				trigger_error('Could not find second way as connector (Should not happen)',E_USER_WARNING);

			$points = clone $this->getServiceLocator()->get('DpOpenGis\ModelInterface\IPointCollection');
			foreach ($testLine->getPoints() as $point)
				$points->add($point);
			foreach ($secondLine->getPoints() as $nr => $point)
				if ($nr > 0)
					$points->add($point);
			$lineString = $this->getServiceLocator()->get('DpOpenGis\Factory\LineStringFactory')->create('LineString',
				array('points' => $points));
			if ($lineString->IsRing())
				$fullLines[] = array('parent' => $lineString,'children' => array());
			else {
				$points = clone $this->getServiceLocator()->get('DpOpenGis\ModelInterface\IReversePointCollection');
				$points->setOriginalPointCollection($lineString->getPoints());
				$reverseLineString = $this->getServiceLocator()->get('DpOpenGis\Factory\LineStringFactory')->
					create('LineString',array('points' => $points));

				if (!isset($openLines1[$startPoint->getLon().','.$startPoint->getLat()]))
					$openLines1[$startPoint->getLon().','.$startPoint->getLat()] =
						array($lineString,$reverseLineString);
				elseif (!isset($openLines2[$startPoint->getLon().','.$startPoint->getLat()]))
					$openLines2[$startPoint->getLon().','.$startPoint->getLat()] =
						array($lineString,$reverseLineString);
				else {
					trigger_error('Aggregated way with StartPoint '.$startPoint->getLon().','.$startPoint->getLat().
						              ' already has a connector.',E_USER_WARNING);
					continue;
				}

				if (!isset($openLines1[$secondEnd->getLon().','.$secondEnd->getLat()]))
					$openLines1[$secondEnd->getLon().','.$secondEnd->getLat()] = array($reverseLineString,$lineString);
				elseif (!isset($openLines2[$secondEnd->getLon().','.$secondEnd->getLat()]))
					$openLines2[$secondEnd->getLon().','.$secondEnd->getLat()] = array($reverseLineString,$lineString);
				else {
					trigger_error('Aggregated way with EndPoint '.$secondEnd->getLon().','.$secondEnd->getLat().
						              ' already has a connector.',E_USER_WARNING);
					continue;
				}
			}
		}
		return $fullLines;
	}
	/**
     * @param array $state
     */
    public function exchangeArray(array $state) {
        $this->defaultExchangeArray($state);
	    $this->_multiPolygonGenerated = false;
	    if (is_null($this->_nodes) &&
		    $this->getServiceLocator()->has('DpOsmParser\ModelInterface\IRelationNodeCollection'))
		    $this->_nodes = clone
		    $this->getServiceLocator()->get('DpOsmParser\ModelInterface\IRelationNodeCollection');
	    if (is_null($this->_relations) &&
		    $this->getServiceLocator()->has('DpOsmParser\ModelInterface\IRelationRelationCollection'))
		    $this->_relations = clone
		    $this->getServiceLocator()->get('DpOsmParser\ModelInterface\IRelationRelationCollection');
	    if (is_null($this->_tags) &&
		    $this->getServiceLocator()->has('DpOsmParser\ModelInterface\IRelationTagCollection'))
		    $this->_tags = clone
		    $this->getServiceLocator()->get('DpOsmParser\ModelInterface\IRelationTagCollection');
	    if (is_null($this->_ways) &&
		    $this->getServiceLocator()->has('DpOsmParser\ModelInterface\IRelationWayCollection'))
		    $this->_ways = clone
		    $this->getServiceLocator()->get('DpOsmParser\ModelInterface\IRelationWayCollection');
    }

    /**
	 * @return array
	 */
	public function getStateVars() {
		return array('relationId','version','timestamp','changeset','tags','nodes','ways','relations','multiPolygon');
	}

}
