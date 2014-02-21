<?php
/**
 * User: dpeuscher
 * Date: 02.04.13
 */

namespace DpOsmParser\Factory;


use DpZFExtensions\ServiceManager\AbstractModelFactory;

/**
 * Class WayFactory
 *
 * @package DpOsmParser\Factory
 */
class WayFactory extends AbstractModelFactory {
	/**
	 * @var AbstractModelFactory
	 */
	protected static $_instance;
	/**
	 * @var array
	 */
	protected $_buildInModels = array(
		'Way' => 'DpOsmParser\Model\Way',
		'DpOsmParser\Model\Way' => 'DpOsmParser\Model\Way',
	);
	/**
	 * @var string
	 */
	protected $_modelInterface = 'DpOsmParser\Model\Way';

}