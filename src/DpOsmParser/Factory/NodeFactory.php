<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class NodeFactory
 *
 * @package DpOsmParser\Factory
 */
class NodeFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'Node' => 'DpOsmParser\Model\Node',
		'DpOsmParser\Model\Node' => 'DpOsmParser\Model\Node',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\Node';
}