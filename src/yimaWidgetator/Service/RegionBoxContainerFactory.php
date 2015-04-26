<?php
namespace yimaWidgetator\Service;

use Zend\ServiceManager;

class RegionBoxContainerFactory implements ServiceManager\FactoryInterface
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
        $config = (isset($config['widgets'])) ? $config['widgets'] : [];

        $widgetContainer = new RegionBoxContainer;
        $widgetContainer->addWidgets($config);

        return $widgetContainer;
    }
}
