<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class WayNodeFactory
 *
 * @package DpOsmParser\Factory
 */
class WayNodeFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'WayNode' => 'DpOsmParser\Model\WayNode',
		'DpOsmParser\Model\WayNode' => 'DpOsmParser\Model\WayNode',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\WayNode';

}