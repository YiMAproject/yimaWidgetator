<?php
namespace yimaWidgetator\Controller\Plugin;

use yimaWidgetator\AbstractWidgetHelper;
use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;

class WidgetLoader extends AbstractWidgetHelper
    implements
    PluginInterface
{
    /**
     * @var null|Dispatchable
     */
    protected $controller;

    /**
     * Set the current controller instance
     *
     * @param  Dispatchable $controller
     * @return void
     */
    public function setController(Dispatchable $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get the current controller instance
     *
     * @return null|Dispatchable
     */
    public function getController()
    {
        return $this->controller;
    }
}
