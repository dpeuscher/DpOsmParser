<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class RelationRelationFactory
 *
 * @package DpOsmParser\Factory
 */
class RelationRelationFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'RelationRelation' => 'DpOsmParser\Model\RelationRelation',
		'DpOsmParser\Model\RelationRelation' => 'DpOsmParser\Model\RelationRelation',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\RelationRelation';

}