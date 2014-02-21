<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Model;


use ArrayObject;
use Doctrine\Common\Collections\ArrayCollection;
use DpDoctrineExtensions\Collection\TDecoreeCollection;
use DpOsmParser\ModelInterface\IRelationNodeCollection;
use DpOsmParser\ModelInterface\IWayNodeCollection;
use DpOsmParser\ModelInterface\IWayTagCollection;
use DpOpenGis\Factory\LineStringFactory;
use DpOpenGis\Model\LineString;
use DpZFExtensions\ServiceManager\TServiceLocator;
use DpZFExtensions\Validator\IExchangeState;
use DpZFExtensions\Validator\TExchangeState;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class Way
 *
 * @package DpOsmParser\Model
 */
class Way implements ServiceLocatorAwareInterface,IExchangeState {
	use TDecoreeCollection,TServiceLocator,TExchangeState {
        TExchangeState::exchangeArray as defaultExchangeArray;
    }
	/**
	 * @var int
	 */
	protected $_wayId;
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
	 * @var IWayNodeCollection
	 */
	protected $_wayNodes;
	/**
	 * @var IWayTagCollection
	 */
	protected $_tags;
	/**
	 * @var LineString
	 */
	protected $_lineString;
	/**
	 * @var bool
	 */
	protected $_lineStringGenerated = false;

	/**
	 * @return int
	 */
	public function getChangeset() {
		return $this->_changeset;
	}

	/**
	 * @return int
	 */
	public function getWayId() {
		return $this->_wayId;
	}

	public function generateLineString() {
		if (!$this->_lineStringGenerated && $this->getServiceLocator()->has('DpOpenGis\Factory\LineStringFactory') &&
			$this->getServiceLocator()->has('DpOpenGis\ModelInterface\IPointCollection') &&
			!is_null($this->getWayNodes()) && !($this->getWayNodes()->isEmpty()))
		{
			/** @var ArrayObject $points */
			$points = clone $this->getServiceLocator()->get('DpOpenGis\ModelInterface\IPointCollection');
			foreach ($this->getWayNodes() as $wayNode)
				/** @var WayNode $wayNode */
				$points[$wayNode->getStep()] = $wayNode->getNode()->getPoint();
			/** @var LineString $lineString */
			$lineString = $this->getServiceLocator()->get('DpOpenGis\Factory\LineStringFactory')->
				create('LineString',array('points' => $points));
			$this->_lineString = $lineString;
			$this->_lineStringGenerated = true;
		}
	}
	/**
	 * @return \DpOpenGis\Model\LineString
	 */
	public function getLineString() {
		if (!$this->_lineStringGenerated)
			$this->generateLineString();
		return $this->_lineString;
	}

	/**
	 * @return \DpOsmParser\ModelInterface\IRelationNodeCollection
	 */
	public function getWayNodes() {
		return $this->_getDecoreeCollection('_wayNodes','DpOsmParser\ModelInterface\IWayNodeCollection');
	}

	/**
	 * @return \DpOsmParser\ModelInterface\IWayTagCollection
	 */
	public function getTags() {
		return $this->_getDecoreeCollection('_tags','DpOsmParser\ModelInterface\IWayTagCollection');
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
     * @param array $state
     */
    public function exchangeArray(array $state) {
        $this->defaultExchangeArray($state);
	    $this->_lineStringGenerated = false;
	    if (is_null($this->_wayNodes) && $this->getServiceLocator()->
		    has('DpOsmParser\ModelInterface\IWayNodeCollection'))
		    $this->_wayNodes = clone
		    $this->getServiceLocator()->get('DpOsmParser\ModelInterface\IWayNodeCollection');
	    if (is_null($this->_tags) && $this->getServiceLocator()->has('DpOsmParser\ModelInterface\IWayTagCollection'))
		    $this->_tags = clone
		    $this->getServiceLocator()->get('DpOsmParser\ModelInterface\IWayTagCollection');
    }
	/**
	 * @return array
	 */
	public function getStateVars() {
		return array('wayId','version','timestamp','changeset','tags','wayNodes','lineString');
	}

}