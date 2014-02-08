<?php
namespace yimaWidgetator\Service;

use Zend\ServiceManager;

/**
 * Class WidgetLoaderFactory
 *
 * @package yimaWidgetator\Service
 */
class WidgetManagerFactory implements ServiceManager\FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return WidgetManager
     */
    public function createService(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $config = array();
        $config = $serviceLocator->get('config');

        // yima_widgetator config must be a serviceManager config, exp. invokables, factories
        $config = (isset($config['yima_widgetator'])) ? $config['yima_widgetator'] : array();
        $config = new ServiceManager\Config($config); // to configure WidgetManager by configureServiceManager

        $widgetManager = new WidgetManager($config);

        /**
         * ServiceLocator Injected into WidgetManager because of instanceof serviceLocatorInterface
         */

        return $widgetManager;
    }
}
