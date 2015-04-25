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
        (isset($config['services'])) ? $config = $config['services'] : [];

        // ServiceLocator Will Injected into WidgetManager
        // because is instanceof serviceLocatorAwareInterface
        $smConfig      = new ServiceManager\Config($config);
        $widgetManager = new WidgetManager($smConfig);

        return $widgetManager;
    }
}
