<?php
return [
    /**
     * Register Widgets in WidgetManager
     *
     * each widget must instance of WidgetInterface
     */
    'yima_widgetator' => [
        // This is configurable service manager config
        'services' => [
            'invokables' => [
                # Default Widgets:
                'partial' => 'yimaWidgetator\Widget\Partial\Widget',
            ],
            'initializers' => [
                // DB: Using Global db Adapter on each services Implemented AdapterAwareInterface
                function ($instance, $sl) {
                    if ($instance instanceof \Zend\Db\Adapter\AdapterAwareInterface) {
                        $sm = $sl->getServiceLocator();
                        $instance->setDbAdapter(
                            $sm->get('Zend\Db\Adapter\Adapter')
                        );
                    }
                }
            ],
        ],
        'widgets' => [
            /** @see RegionBoxContainer */
            'region_box' => [
                # // $priority default is start with 0
                # $priority => 'WidgetName',
                # $priority => [
                #    'widget' => 'WidgetName'
                #    'params' => [
                #       'with_construct_param' => 'param_value'
                #     ]
                # ],
            ],
        ],
	],

    'view_manager' => [
        'strategies' => [
            # render widgets into layout viewModel
            'Yima.Widgetator.ViewStrategy',
        ],
    ],
    'service_manager' => [
        'invokables' => [
            'Yima.Widgetator.ViewStrategy' => 'yimaWidgetator\Listener\WidgetizeViewStrategy'
        ],
    ],

    /**
     * Libraries that used in Ajax Loading of widgets.
     * @see \yimaJquery\View\Helper\WidgetAjaxy
     */
    'statics.uri' => [
        'paths' => [
            'Yima.Widgetator.JS.Jquery.Ajaxq'        => '$basepath/yima-widgetator/js/jquery.ajaxq.min.js',
            'Yima.Widgetator.JS.Jquery.Json'         => '$basepath/yima-widgetator/js/jquery.json.min.js',
            'Yima.Widgetator.JS.Jquery.WidgetAjaxy'  => '$basepath/yima-widgetator/js/jquery.widget_ajaxy.js',
        ],
    ],

    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__.'/../www',
            ],
            'map' => array(
                'yima-widgetator/js/jquery.widget_ajaxy.js' => __DIR__ . '/../www/widget_ajaxy.js.php',
            ),
        ],
    ],

    /**
     * Rest Controller to loading widgets
     */
    'controllers' => [
		'invokables' => [
            'yimaWidgetator\Controller\Rest' => 'yimaWidgetator\Controller\WidgetLoadRestController'
		],
	],

    'router' => [
        'routes' => [
            'yimaWidgetator_restLoadWidget' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/yimaWidgetator/loadWidget',
                    'defaults' => [
                        'controller' => 'yimaWidgetator\Controller\Rest',
                    ],
                ],
            ],
        ],
    ],
];
