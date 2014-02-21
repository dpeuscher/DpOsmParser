<?php
/**
 * User: dpeuscher
 * Date: 16.04.13
 */

namespace DpOsmParser\Collection;

use DpDoctrineExtensions\Collection\AForceTypeCollection;
use DpOsmParser\ModelInterface\IWayTagCollection;

/**
 * Class WayTagCollection
 *
 * @package DpOsmParser\Collection
 */
class WayTagCollection extends AForceTypeCollection implements IWayTagCollection {
    protected $_entityType = 'DpOsmParser\Model\WayTag';
}