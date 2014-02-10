<?php
return array(
    /**
     * Register Widgets in WidgetManager
     *
     * each widget must instance of WidgetInterface
     */
    'yima_widgetator' => array(
         // This is configurable service manager config
		'invokables' => array(
			# 'widgetName' => 'Widget\Class',
		),
	),

    /**
     * Libraries that used in Ajax Loading of widgets.
     * @see \yimaJquery\View\Helper\WidgetAjaxy
     */
    'static_uri_helper' => array(
        'Yima.Widgetator.JS.Jquery.Ajaxq' => '{{basepath}}/yima-widgetator/js/jquery.ajaxq.min.js',
        'Yima.Widgetator.JS.Jquery.Json'  => '{{basepath}}/yima-widgetator/js/jquery.json.min.js',
    ),

    /**
     * Rest Controller to loading widgets
     */
    'controllers' => array(
		'invokables' => array(
            'yimaWidgetator\Controller\Rest' => 'yimaWidgetator\Controller\WidgetLoadRestController'
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
