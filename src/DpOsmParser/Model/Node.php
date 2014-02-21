<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Model;

use DpDoctrineExtensions\Collection\TDecoreeCollection;
use DpOsmParser\ModelInterface\INodeTagCollection;
use DpOpenGis\Model\Point;
use DpZFExtensions\ServiceManager\TServiceLocator;
use DpZFExtensions\Validator\IExchangeState;
use DpZFExtensions\Validator\TExchangeState;
use DateTime;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class Node
 *
 * @package DpOsmParser\Model
 */
class Node implements ServiceLocatorAwareInterface,IExchangeState {
	use TDecoreeCollection,TServiceLocator,TExchangeState {
        TExchangeState::exchangeArray as defaultExchangeArray;
    }
	/**
	 * @var int
	 */
	protected $_nodeId;
	/**
	 * @var float
	 */
	protected $_lat;
	/**
	 * @var float
	 */
	protected $_lon;
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
	 * @var INodeTagCollection
	 */
	protected $_tags;
	/**
	 * @var Point
	 */
	protected $_point;
	/**
	 * @var bool
	 */
	protected $_pointGenerated = false;

	/**
	 * @return int
	 */
	public function getChangeset() {
		return $this->_changeset;
	}

	/**
	 * @return int
	 */
	public function getNodeId() {
		return $this->_nodeId;
	}

	/**
	 * @return float
	 */
	public function getLat() {
		return $this->_lat;
	}

	/**
	 * @return float
	 */
	public function getLon() {
		return $this->_lon;
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

	public function generatePoint() {
		if (!$this->_pointGenerated && $this->getServiceLocator()->has('DpOpenGis\Factory\PointFactory'))
			if (!is_null($this->getLon()) && !is_null($this->getLat())) {
				$this->_point = $this->getServiceLocator()->get('DpOpenGis\Factory\PointFactory')->create('Point',array(
                                                                                               'lon' => $this->getLon(),
                                                                                               'lat' => $this->getLat())
				);
				$this->_pointGenerated = true;
			}
	}
	/**
	 * @return \DpOpenGis\Model\Point
	 */
	public function getPoint() {
		if (!$this->_pointGenerated)
			$this->generatePoint();
		return $this->_point;
	}

	/**
	 * @return \DpOsmParser\ModelInterface\INodeTagCollection
	 */
	public function getTags() {
		return $this->_getDecoreeCollection('_tags','DpOsmParser\ModelInterface\INodeTagCollection');
	}

    /**
     * @param array $state
     */
    public function exchangeArray(array $state) {
        $this->defaultExchangeArray($state);
	    $this->_pointGenerated = false;
    }
	/**
	 * @return array of all fields that represent the state
	 */
	public function getStateVars() {
		return array('nodeId','lat','lon','version','timestamp','changeset','tags','point');
	}
}