<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class RelationWayFactory
 *
 * @package DpOsmParser\Factory
 */
class RelationWayFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'RelationWay' => 'DpOsmParser\Model\RelationWay',
		'DpOsmParser\Model\RelationWay' => 'DpOsmParser\Model\RelationWay',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\RelationWay';

}