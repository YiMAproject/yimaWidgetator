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
        'initializers' => array (
            // DB: Using Global db Adapter on each services Implemented AdapterAwareInterface
            function ($instance, $sl) {
                if ($instance instanceof \Zend\Db\Adapter\AdapterAwareInterface) {
                    $sm = $sl->getServiceLocator();
                    $instance->setDbAdapter(
                        $sm->get('Zend\Db\Adapter\Adapter')
                    );
                }
            }
        ),
	),

    /**
     * Libraries that used in Ajax Loading of widgets.
     * @see \yimaJquery\View\Helper\WidgetAjaxy
     */
    'static_uri_helper' => array(
        'Yima.Widgetator.JS.Jquery.Ajaxq' => '$basepath/yima-widgetator/js/jquery.ajaxq.min.js',
        'Yima.Widgetator.JS.Jquery.Json'  => '$basepath/yima-widgetator/js/jquery.json.min.js',
    ),

    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__.'/../www',
            ),
        ),
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
