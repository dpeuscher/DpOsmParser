<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class RelationNodeFactory
 *
 * @package DpOsmParser\Factory
 */
class RelationNodeFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'RelationNode' => 'DpOsmParser\Model\RelationNode',
		'DpOsmParser\Model\RelationNode' => 'DpOsmParser\Model\RelationNode',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\RelationNode';

}