<?php
namespace yimaWidgetator\Service;

use Zend\ServiceManager;

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
        $config = $serviceLocator->get('config');

        $config = (isset($config['yima_widgetator'])) ? $config['yima_widgetator'] : [];
        $config = new ServiceManager\Config($config);

        // ServiceLocator Will Injected into WidgetManager
        // because is instanceof serviceLocatorAwareInterface
        $widgetManager = new WidgetManager($config);

        return $widgetManager;
    }
}
