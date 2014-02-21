<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class RelationTagFactory
 *
 * @package DpOsmParser\Factory
 */
class RelationTagFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'RelationTag' => 'DpOsmParser\Model\RelationTag',
		'DpOsmParser\Model\RelationTag' => 'DpOsmParser\Model\RelationTag',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\RelationTag';

}