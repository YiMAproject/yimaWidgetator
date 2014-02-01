<?php
namespace yimaWidgetator\Feature;

interface WidgetProviderInterface
{
    /**
     * Expected to return \Zend\ServiceManager\Config object or array to seed
     * such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getWidgetConfig();
}
