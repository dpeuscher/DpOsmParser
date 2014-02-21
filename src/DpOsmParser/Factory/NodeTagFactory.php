<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class NodeTagFactory
 *
 * @package DpOsmParser\Factory
 */
class NodeTagFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'NodeTag' => 'DpOsmParser\Model\NodeTag',
		'DpOsmParser\Model\NodeTag' => 'DpOsmParser\Model\NodeTag',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\NodeTag';

}