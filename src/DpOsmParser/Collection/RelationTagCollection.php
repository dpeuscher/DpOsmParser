<?php
/**
 * User: dpeuscher
 * Date: 16.04.13
 */

namespace DpOsmParser\Collection;

use DpDoctrineExtensions\Collection\AForceTypeCollection;
use DpOsmParser\ModelInterface\IRelationTagCollection;

/**
 * Class RelationTagCollection
 *
 * @package DpOsmParser\Collection
 */
class RelationTagCollection extends AForceTypeCollection implements IRelationTagCollection {
	/**
	 * @var string
	 */
	protected $_entityType = 'DpOsmParser\Model\RelationTag';
}