<?php
namespace yimaWidgetator;

use Zend\ModuleManager\ModuleManagerInterface;

class Module
{
    /**
     * Determine yimaWidgetator run as a serviceListener Configurable ??
     *
     * @var bool
     */
    public static $isServiceListener = false;

    public function init(ModuleManagerInterface $moduleManager)
    {
        // cWidget need cjQuery and it will be loaded
        $moduleManager->loadModule('cjQuery');

        // Check that yimaWidgetator used as listener option or not {
        $sm = $moduleManager->getEvent()->getParam('ServiceManager');

        $appConf = $sm->get('ApplicationConfig');
        if (isset($appConf['service_listener_options']) && $sm->has('yimaWidgetator\WidgetLoader')) {
            foreach ($appConf['service_listener_options'] as $srLis ) {
                if (@ $srLis['service_manager'] == 'yimaWidgetator\WidgetLoader')
                    if (@ $srLis['config_key'] == 'yimaWidgetator')
                        self::$isServiceListener = true;
            }
        }
        // ... }
    }

	public function getServiceConfig()
	{
        $return = array(
            'aliases' => array (
                'WidgetLoader' => 'yimaWidgetator\WidgetLoader',
                'widget'       => 'widgetLoader',
            ),
        );

        if (! self::$isServiceListener) {
            // define widgetLoader pluginManager as a service
            $return = array_merge($return, array(
                'factories' => array(
                    'yimaWidgetator\WidgetLoader' => 'yimaWidgetator\Service\WidgetLoaderFactory',
                ),
            ));
        }

		return $return;
	}

	public function getControllerPluginConfig()
	{
		return array(
			'invokables' => array (
				'widgetLoader' => 'yimaWidgetator\Controller\Plugin\WidgetLoader',
			),
			'aliases' => array (
				'widget' => 'widgetLoader', 
			),
		);
	}

	public function getViewHelperConfig()
	{
		return array(
			'invokables' => array (
				'widgetLoader' => 'yimaWidgetator\View\Helper\WidgetLoader',
                'ajaxyWidget'  => 'yimaWidgetator\View\Helper\AjaxyWidget',
			),
			'aliases' => array (
				'widget' => 'widgetLoader',
			),
		);
	}

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
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
