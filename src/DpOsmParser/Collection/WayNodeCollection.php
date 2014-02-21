<?php
/**
 * User: dpeuscher
 * Date: 16.04.13
 */

namespace DpOsmParser\Collection;

use DpDoctrineExtensions\Collection\AForceTypeCollection;
use DpOsmParser\ModelInterface\IWayNodeCollection;

/**
 * Class WayNodeCollection
 *
 * @package DpOsmParser\Collection
 */
class WayNodeCollection extends AForceTypeCollection implements IWayNodeCollection {
    protected $_entityType = 'DpOsmParser\Model\WayNode';
}