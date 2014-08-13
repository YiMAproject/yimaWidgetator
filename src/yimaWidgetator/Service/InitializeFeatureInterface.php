<?php
namespace yimaWidgetator\Service;

/**
 * Interface InitializeFeatureInterface
 * Classes implement this feature have called init() method after
 * service created by pluginManager(WidgetManager)
 *
 * @package yimaWidgetator\Service
 */
interface InitializeFeatureInterface
{
    /**
     * Initialize class object
     *
     * @return string
     */
    public function init();
}
