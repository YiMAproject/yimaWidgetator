<?php
namespace yimaWidgetator;

use yimaWidgetator\Listener\WidgetizeAggregateListener;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package yimaWidgetator
 */
class Module implements
    InitProviderInterface,
    BootstrapListenerInterface,
    ServiceProviderInterface,
    ControllerPluginProviderInterface,
    ViewHelperProviderInterface,
    ConfigProviderInterface,
    AutoLoaderProviderInterface
{
    /**
     * Initialize workflow
     *
     * @param ModuleManagerInterface $moduleModuleManager
     * @internal param ModuleManagerInterface $manager
     *
     * @throws \Exception
     * @return void
     */
    public function init(ModuleManagerInterface $moduleModuleManager)
    {
        /** @var $moduleModuleManager \Zend\ModuleManager\ModuleManager */
        $moduleModuleManager->loadModule('yimaStaticUriHelper');
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface|MvcEvent $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        // --- Attach default Listeners  ---------------------------------==================
        $sm           = $e->getApplication()->getServiceManager();
        $listenerAggr = new WidgetizeAggregateListener();
        $listenerAggr->setServiceManager($sm);

        $e->getApplication()
            ->getEventManager()->attach($listenerAggr)
        ;
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
	public function getServiceConfig()
	{
        return [
            'factories' => [
                'yimaWidgetator.WidgetManager'        => 'yimaWidgetator\Service\WidgetManagerFactory',
                'yimaWidgetator.Widgetizer.Container' => 'yimaWidgetator\Service\RegionBoxContainerFactory',
            ],
        ];
	}

    /**
     * Controller helper services
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getControllerPluginConfig()
	{
		return [
			'invokables' => [
				'widgetLoader' => 'yimaWidgetator\Controller\Plugin\WidgetLoader',
			],
			'aliases' => [
				'widget' => 'widgetLoader', 
			],
		];
	}

    /**
     * View helper services
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getViewHelperConfig()
	{
		return [
			'invokables' => [
				'widgetLoader' => 'yimaWidgetator\View\Helper\WidgetLoader',
                'widgetAjaxy'  => 'yimaWidgetator\View\Helper\WidgetAjaxy',
			],
			'aliases' => [
				'widget' => 'widgetLoader',
			],
		];
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
		return [
			'Zend\Loader\StandardAutoloader' => [
				'namespaces' => [
					__NAMESPACE__ => __DIR__,
				],
			],
		];
	}
}
