<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class WayTagFactory
 *
 * @package DpOsmParser\Factory
 */
class WayTagFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'WayTag' => 'DpOsmParser\Model\WayTag',
		'DpOsmParser\Model\WayTag' => 'DpOsmParser\Model\WayTag',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\WayTag';

}