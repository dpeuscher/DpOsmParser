<?php
namespace DpOsmParser;
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
	'controllers' => array(
		'invokables' => array(
			'DpOsmParser\Controller\Parser' => 'DpOsmParser\Controller\Parser'
		),
	),
	'console' => array(
	    'router' => array(
	        'routes' => array(
		        'osm-import' => array(
	                'options' => array(
	                    'route'    => 'osm import [--verbose|-v]:aVerbose [--all|-a]:aAll [--skip-nodes|-n]:skipNodes '.
		                    '[--skip-ways|-w]:skipWays [--skip-relations|-r]:skipRelations '.
		                    '[--no-truncate|-t]:noTruncate <osmFile>',
	                    'defaults' => array(
	                        'controller' => 'DpOsmParser\Controller\Parser',
	                        'action'     => 'import',
	                    ),
	                ),
	            ),
		        'osm-generate-multi-polygon' => array(
			        'options' => array(
				        'route'    => 'osm generate [--one-process-per-relation|--ppr]:processPerRelation '.
						'[--prefix=] [--only=] [--instances=] [--limit=] [--offset=]',
				        'defaults' => array(
					        'controller' => 'DpOsmParser\Controller\Parser',
					        'action'     => 'generateMultiPolygon',
				        ),
			        ),
		        ),
	        ),
	    ),
	),
	'doctrine' => array(
                'driver' => array(
	                __NAMESPACE__ . '_driver' => array(
		                'class' => 'Doctrine\ORM\Mapping\Driver\YamlDriver',
		                'cache' => 'array',
		                'paths' => array(getcwd()."/config/yaml")
	                ),
	                'orm_default' => array(
		                'drivers' => array(
			                __NAMESPACE__ . '\Model' => __NAMESPACE__ . '_driver',
		                )
	                ),
                )
	)
);
