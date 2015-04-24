<?php
namespace yimaWidgetator\Service;

/**
 * Interface InitializeFeatureInterface
 * Classes implement this feature have called init() method after
 * service created by pluginManager(WidgetManager)
 *
 */
interface iInitableWidgetFeature
{
    /**
     * Initialize class object
     *
     * @return string
     */
    function init();
}
