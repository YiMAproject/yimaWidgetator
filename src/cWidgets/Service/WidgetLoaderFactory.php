<?php
namespace yimaWidgetator\Service;

use yimaWidgetator\WidgetManager;

use Zend\ServiceManager;

class WidgetLoaderFactory implements ServiceManager\FactoryInterface
{
    public function createService(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $config = array();
        if (! \yimaWidgetator\Module::$isServiceListener) {
            // in serviceListener usage config will passed automated into widgetLoader by listener
            $config = $serviceLocator->get('config');
            $config = (isset($config['yimaWidgetator'])) ? $config['yimaWidgetator'] : array();
        }

        $config = new ServiceManager\Config($config);

        $widgetLoader = new WidgetManager($config);
        $widgetLoader->setServiceLocator($serviceLocator);

        //$widgetLoader->addPeeringServiceManager($serviceLocator); disabled, seems useless!!!
        
        return $widgetLoader;
    }
}
