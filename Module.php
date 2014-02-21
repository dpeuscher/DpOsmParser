<?php

namespace DpOsmParser;

use DpOsmParser\Factory\NodeFactory;
use DpOsmParser\Factory\NodeTagFactory;
use DpOsmParser\Factory\RelationFactory;
use DpOsmParser\Factory\RelationNodeFactory;
use DpOsmParser\Factory\RelationRelationFactory;
use DpOsmParser\Factory\RelationTagFactory;
use DpOsmParser\Factory\RelationWayFactory;
use DpOsmParser\Factory\WayFactory;
use DpOsmParser\Factory\WayNodeFactory;
use DpOsmParser\Factory\WayTagFactory;
use Zend\Console\Adapter\AbstractAdapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
	    return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
    	return array(
		        'invokables' => array(
			        'DpOsmParser\Model\Node' => 'DpOsmParser\Model\Node',
			        'DpOsmParser\Model\NodeTag' => 'DpOsmParser\Model\NodeTag',
			        'DpOsmParser\Model\Relation' => 'DpOsmParser\Model\Relation',
			        'DpOsmParser\Model\RelationNode' => 'DpOsmParser\Model\RelationNode',
			        'DpOsmParser\Model\RelationRelation' => 'DpOsmParser\Model\RelationRelation',
			        'DpOsmParser\Model\RelationTag' => 'DpOsmParser\Model\RelationTag',
			        'DpOsmParser\Model\RelationWay' => 'DpOsmParser\Model\RelationWay',
			        'DpOsmParser\Model\Way' => 'DpOsmParser\Model\Way',
			        'DpOsmParser\Model\WayNode' => 'DpOsmParser\Model\WayNode',
			        'DpOsmParser\Model\WayTag' => 'DpOsmParser\Model\WayTag',
			        'DpOsmParser\ModelInterface\INodeTagCollection' => 'DpOsmParser\Collection\NodeTagCollection',
			        'DpOsmParser\ModelInterface\IRelationNodeCollection' => 'DpOsmParser\Collection\RelationNodeCollection',
			        'DpOsmParser\ModelInterface\IRelationRelationCollection' => 'DpOsmParser\Collection\RelationRelationCollection',
			        'DpOsmParser\ModelInterface\IRelationTagCollection' => 'DpOsmParser\Collection\RelationTagCollection',
			        'DpOsmParser\ModelInterface\IRelationWayCollection' => 'DpOsmParser\Collection\RelationWayCollection',
			        'DpOsmParser\ModelInterface\IWayNodeCollection' => 'DpOsmParser\Collection\WayNodeCollection',
			        'DpOsmParser\ModelInterface\IWayTagCollection' => 'DpOsmParser\Collection\WayTagCollection',
			        'DpOsmParser\Controller\Parser' => 'DpOsmParser\Controller\Parser',
		        ),
		        'factories' => array(
			        'DpOsmParser\Factory\NodeFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = NodeFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\NodeTagFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = NodeTagFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\RelationFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = RelationFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\RelationNodeFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = RelationNodeFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\RelationRelationFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = RelationRelationFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\RelationTagFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = RelationTagFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\RelationWayFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = RelationWayFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\WayFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = WayFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\WayNodeFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = WayNodeFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
			        'DpOsmParser\Factory\WayTagFactory' => function (ServiceLocatorInterface $sm) {
				        $factory = WayTagFactory::getInstance();
				        $factory->setServiceLocator($sm);
				        return $factory;
			        },
		        ),
		        'initializers' => array(
			        function($instance, $serviceManager) {
				        if ($instance instanceof ServiceLocatorAwareInterface) {
					        $instance->setServiceLocator($serviceManager);
				        }
			        }
		        )
			);
    }
	public function getConsoleUsage(AbstractAdapter $console)
	{
		return array(
			// Describe available commands
			'osm import [--verbose|-v] [--skip-nodes|-n] [--skip-ways|-w] [--skip-relations|-r] [--no-truncate|-t] <'.
				'osmFile>' => 'Import osmFile into db',

			// Describe expected parameters
			array('osmFile','location of osmFile'),
			array('--verbose|-v','(optional) turn on verbose mode'),
			array('--skip-nodes|-n','(optional) skip updating nodes'),
			array('--skip-ways|-w','(optional) skip updating ways'),
			array('--skip-relations|-r','(optional) skip updating relations'),
			array('--no-truncate|-t','(optional) don\'t truncate tables to update first [skipped won\'t be truncated '.
				'at all]'),

			'osm generate [--one-process-per-relation|--ppr] [--prefix=] [--only=] [--instances=] [--limit= [--offset=]]' =>
				'Generate MultiPolygons of imported relations',
			array('--one-process-per-relation=|--ppr','(optional) if given, every relation is processed in an extra process'.
				'generated. You can give many prefixes comma separated'),
			array('--prefix=','(optional) if given, only relations which relationIds start with prefixes are '.
				'generated. You can give many prefixes comma separated'),
			array('--only=','(optional) if given, only relations with relationIds in comma separated list are '.
				'generated'),
			array('--instances=','(optional) if given, generation will be splitted up in x different processes '.
				'number of processors is appreciated'),
			array('--limit=','(optional) if given, only that number of relations are processed'),
			array('--offset=','(optional) if given, processing skips [offset] relations'),
		);
	}
	public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
