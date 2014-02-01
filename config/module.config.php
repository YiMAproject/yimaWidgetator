<?php
return array(
	'yimaWidgetator' => array(
		'invokables' => array(
			'Navigation\Menu' => 'yimaWidgetator\Widget\NavigationMenu\Widget',

			'ezWidget' => 'yimaWidgetator\Widget\ezWidget\Widget',
			'ezBorder' => 'yimaWidgetator\Widget\ezBorder\Widget',
		),
	),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

	'controllers' => array(
		'invokables' => array(
			'yimaWidgetator\Controller\Index' => 'yimaWidgetator\Controller\IndexController',
            'yimaWidgetator\Controller\Rest'  => 'yimaWidgetator\Controller\WidgetLoadRestController'
		),
	),

    'router' => array(
        'routes' => array(
            'yimaWidgetator_demo' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/yimaWidgetator/demo',
                    'defaults' => array(
                        'controller' => 'yimaWidgetator\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),

            'yimaWidgetator_restLoadWidget' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/yimaWidgetator/loadWidget',
                    'defaults' => array(
                        'controller' => 'yimaWidgetator\Controller\Rest',
                    ),
                ),
            ),
        ),
    ),
	
);
