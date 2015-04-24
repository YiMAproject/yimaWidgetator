<?php
namespace yimaWidgetator\View\Helper;

use yimaWidgetator\Service\AbstractWidgetHelper;
use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

class WidgetLoader extends AbstractWidgetHelper
    implements HelperInterface
{
    /**
     * View object instance
     *
     * @var Renderer
     */
    protected $view = null;

    /**
     * Set the View object
     *
     * @param  Renderer $view
     *
     * @return $this
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the view object
     *
     * @return null|Renderer
     */
    public function getView()
    {
        return $this->view;
    }
}
