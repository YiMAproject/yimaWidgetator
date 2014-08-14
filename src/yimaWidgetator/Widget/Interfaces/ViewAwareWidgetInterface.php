<?php
namespace yimaWidgetator\Widget\Interfaces;

use Zend\View\Renderer\RendererInterface as Renderer;

/**
 * Interface ViewAwareWidgetInterface
 *
 * @package yimaWidgetator\Widget
 */
interface ViewAwareWidgetInterface extends WidgetInterface
{
    /**
     * Set the View object
     *
     * @param  Renderer $view
     *
     * @return mixed
     */
    public function setView(Renderer $view);

    /**
     * Get the View object
     *
     * @return Renderer
     */
    public function getView();
}
