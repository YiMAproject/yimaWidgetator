<?php
namespace yimaWidgetator\Interfaces;

use yimaWidgetator\Widget\Interfaces\WidgetInterface;

/**
 * This is only container that has a widgets sets
 *
 * ! it's good to store widgets as a string or array setter
 *   format insteadof real object
 *   all none objects widgets constructed and instanced on
 *   render listener.
 *
 */
interface iRegionBoxContainer
{
    /**
     * Add Widget To Container
     *
     * @param string                       $regionBox
     * @param string|array|WidgetInterface $widget
     * @param int                          $priority
     *
     * @return $this
     */
    function addWidget($regionBox, $widget, $priority = 10);

    /**
     * Add Widgets
     *
     * - ['region' => [
     *     $widgetSetter,
     *     ...
     *   ]]
     *
     * @param array $regWidgets
     *
     * @return $this
     */
    function addWidgets(array $regWidgets);

    /**
     * Get All Widgets In Region Box
     *
     * @param string $regionBox
     *
     * @return array
     */
    function getRegionWidgets($regionBox);

    /**
     * Get All Widgets
     *
     * @return array
     */
    function getWidgets();
}
