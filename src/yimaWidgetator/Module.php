<?php
namespace yimaWidgetator;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

/**
 * Class Module
 *
 * @package yimaWidgetator
 */
class Module implements
    InitProviderInterface,
    ServiceProviderInterface,
    ControllerPluginProviderInterface,
    ViewHelperProviderInterface,
    ConfigProviderInterface,
    AutoLoaderProviderInterface
{
    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     *
     * @return void
     */
    public function init(ModuleManagerInterface $moduleModuleManager)
    {
        /** @var $moduleModuleManager \Zend\ModuleManager\ModuleManager */
        $moduleModuleManager->loadModule('yimaJquery');

        //$moduleManager->loadModule('yimaStaticUriHelper');
        if (! $moduleModuleManager->getModule('yimaStaticUriHelper')) {
            // yimaStaticUriHelper needed and not loaded.
            // loadModule in default zf2 can't load more than one module
            throw new \Exception(
                'Module "yimaStaticUriHelper" not loaded, by zf2 module manager we can`t load this module automatically.'
                .'please enable this module and put before "yimaWidgetator".'
            );
        }
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
	public function getServiceConfig()
	{
        return array(
            'factories' => array(
                'yimaWidgetator.WidgetManager' => 'yimaWidgetator\Service\WidgetManagerFactory',
            ),
        );
	}

    /**
     * Controller helper services
     *
     * @return array|\Zend\ServiceManager\Config
     */
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

    /**
     * View helper services
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
	{
		return array(
			'invokables' => array (
				'widgetLoader' => 'yimaWidgetator\View\Helper\WidgetLoader',
                'widgetAjaxy'  => 'yimaWidgetator\View\Helper\WidgetAjaxy',
			),
			'aliases' => array (
				'widget' => 'widgetLoader',
			),
		);
	}

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__,
				),
			),
		);
	}
}
