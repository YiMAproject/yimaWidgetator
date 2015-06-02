<?php
namespace yimaWidgetator\View\Renderer;

use yimaWidgetator\Service\RegionBoxContainer;
use yimaWidgetator\Service\WidgetManager;
use yimaWidgetator\Widget\Interfaces\WidgetInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\PhpRenderer as BaseRenderer;

class PhpRenderer extends BaseRenderer
    implements
    ServiceLocatorAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $sm;

    public function render($nameOrModel, $values = null)
    {
        /** @var RegionBoxContainer $rBoxContainer */
        $rBoxContainer = $this->sm->get('yimaWidgetator.Widgetizer.Container');
        foreach ($rBoxContainer->getWidgets() as $region => $wdgs) {
            // Render Widget
            $this->__renderWidgets($region, $wdgs, $nameOrModel);
        }

        $return = $this->sm->get('ViewRenderer')
            ->render($nameOrModel, $values);

        return $return;
    }

    /**
     * Render Widget From Container Result
     *
     * @param array          $widgets    Container Widget Entity
     * @param ModelInterface $viewModel  View Model
     *
     * @return bool
     */
    protected function __renderWidgets($region, array $widgets, ModelInterface $viewModel)
    {
        foreach($widgets as $widget) {
            if (!$widget instanceof WidgetInterface)
                $widget = $this->___attainWidgetInstance($widget);

            // Render Widget
            $content = $widget->render();

            // TODO maybe we want to add filter or event on rendering widget contents
            $viewModel->{$region} .= $content;
        }
    }

    /**
     * @param mixed $widget
     *
     * @return WidgetInterface
     */
    protected function ___attainWidgetInstance($widget)
    {
        /** @var $widgetManager WidgetManager */
        $widgetManager = $this->sm->get('yimaWidgetator.WidgetManager');

        $options = [];
        if (is_array($widget)) {
            $wArg    = $widget;
            $widget  = $wArg['widget'];
            $options = (isset($wArg['params'])) ? $wArg['params'] : $options;
        }

        $instance = $widgetManager->get($widget, $options);

        return $instance;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->sm;
    }
}
 