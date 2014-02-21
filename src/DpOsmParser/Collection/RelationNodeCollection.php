<?php
/**
 * User: dpeuscher
 * Date: 16.04.13
 */

namespace DpOsmParser\Collection;

use DpDoctrineExtensions\Collection\AForceTypeCollection;
use DpOsmParser\ModelInterface\IRelationNodeCollection;

/**
 * Class RelationNodeCollection
 *
 * @package DpOsmParser\Collection
 */
class RelationNodeCollection extends AForceTypeCollection implements IRelationNodeCollection {
    protected $_entityType = 'DpOsmParser\Model\RelationNode';
}