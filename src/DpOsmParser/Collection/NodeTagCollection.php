<?php
/**
 * User: dpeuscher
 * Date: 16.04.13
 */

namespace DpOsmParser\Collection;

use DpDoctrineExtensions\Collection\AForceTypeCollection;
use DpOsmParser\ModelInterface\INodeTagCollection;

/**
 * Class NodeTagCollection
 *
 * @package DpOsmParser\Collection
 */
class NodeTagCollection extends AForceTypeCollection implements INodeTagCollection {
    protected $_entityType = 'DpOsmParser\Model\NodeTag';
}