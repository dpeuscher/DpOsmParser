<?php
/**
 * User: dpeuscher
 * Date: 16.04.13
 */

namespace DpOsmParser\Collection;

use DpDoctrineExtensions\Collection\AForceTypeCollection;
use DpOsmParser\ModelInterface\IRelationRelationCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Class RelationRelationCollection
 *
 * @package DpOsmParser\Collection
 */
class RelationRelationCollection extends AForceTypeCollection implements IRelationRelationCollection {
    protected $_entityType = 'DpOsmParser\Model\RelationRelation';
	public function add($newElement) {
		foreach ($this->toArray() as $element)
			if ($element->getRelationParent() === $newElement->getRelationParent() &&
				$element->getRelationChild() === $newElement->getRelationChild() &&
				$element->getRole() === $newElement->getRole())
				return;
		parent::add($newElement);
	}
}
