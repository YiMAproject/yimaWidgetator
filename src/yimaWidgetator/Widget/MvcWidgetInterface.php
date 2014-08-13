<?php
namespace yimaWidgetator\Widget;

use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

/**
 * Interface MvcWidgetInterface
 *
 * @package yimaWidgetator\Widget
 */
interface MvcWidgetInterface extends WidgetInterface
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

    /**
     * Set view script to render by view renderer
     *
     * @param string|ModelInterface $nameOrModel The script/resource process, or a view model
     *
     * @return mixed
     */
    public function setLayout($nameOrModel);

    /**
     * Get view script layout
     *
     * @return string|ModelInterface
     */
    public function getLayout();
}
