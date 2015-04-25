<?php
namespace yimaWidgetator\Widget\Interfaces\Feature;

/**
 * Interface InitializeFeatureInterface
 * Classes implement this feature have called init() method after
 * service created by pluginManager(WidgetManager)
 *
 */
interface iInitableWidgetFeature
{
    /**
     * Initialize object on widget manager -
     * instance creation
     *
     * @return string
     */
    function init();
}
