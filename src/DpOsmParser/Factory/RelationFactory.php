<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class RelationFactory
 *
 * @package DpOsmParser\Factory
 */
class RelationFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'Relation' => 'DpOsmParser\Model\Relation',
		'DpOsmParser\Model\Relation' => 'DpOsmParser\Model\Relation',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\Relation';

}