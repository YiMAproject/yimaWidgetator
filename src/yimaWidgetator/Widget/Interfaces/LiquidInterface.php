<?php
namespace yimaWidgetator\Widget\Interfaces;

/**
 * Interface LiquidInterface
 * : this is not final widget interface bu can used beside
 *   of other widgets interfaces
 *
 * @package yimaWidgetator\Widget\Interfaces
 */
interface LiquidInterface
{
    /**
     * Save widget data
     *
     * @return mixed
     */
    public function save();

    /**
     * Load widget saved data
     *
     * @return boolean false on no data
     */
    public function load();

    /**
     * Apply loaded data and Init widget
     *
     * @return mixed
     */
    public function apply();
}
