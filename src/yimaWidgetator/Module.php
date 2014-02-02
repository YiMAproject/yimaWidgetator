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
    public function init(ModuleManagerInterface $moduleManager)
    {
        /** @var $moduleManager \Zend\ModuleManager\ModuleManager */

        try {
            // yimaWidgetator need yimajQuery and it will be loaded.

            $moduleManager->loadModule('yimaJquery');
        }
        catch(\Zend\ModuleManager\Exception\RuntimeException $e) {
            if ($e->getMessage() == 'Module (yimaJquery) could not be initialized.') {
                throw new \Exception(
                    'yimaWidgetator Module need <a href="https://github.com/RayaMedia/yimaJquery.git">yimaJquery</a> module installed and enabled.'
                );
            }

            throw $e;
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
                'yimaWidgetator\WidgetLoader' => 'yimaWidgetator\Service\WidgetLoaderFactory',
            ),
            'aliases' => array (
                'WidgetLoader' => 'yimaWidgetator\WidgetLoader',
                'widget'       => 'widgetLoader',
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
                'ajaxyWidget'  => 'yimaWidgetator\View\Helper\AjaxyWidget',
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
