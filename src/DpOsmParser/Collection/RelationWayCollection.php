<?php
/**
 * User: dpeuscher
 * Date: 16.04.13
 */

namespace DpOsmParser\Collection;

use DpDoctrineExtensions\Collection\AForceTypeCollection;
use DpOsmParser\ModelInterface\IRelationWayCollection;

/**
 * Class RelationWayCollection
 *
 * @package DpOsmParser\Collection
 */
class RelationWayCollection extends AForceTypeCollection implements IRelationWayCollection {
    protected $_entityType = 'DpOsmParser\Model\RelationWay';
}