<?php
return array(
	'yima_widgetator' => array(
        /*
         * This is configurable service manager config
         */
		'invokables' => array(
			// 'widgetName' => 'Widget\Class',
		),
	),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

	'controllers' => array(
		'invokables' => array(
            'yimaWidgetator\Controller\Rest'  => 'yimaWidgetator\Controller\WidgetLoadRestController'
		),
	),

    'router' => array(
        'routes' => array(
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
