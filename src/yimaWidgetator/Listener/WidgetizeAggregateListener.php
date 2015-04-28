<?php
namespace yimaWidgetator\Listener;

use yimaWidgetator\Service\RegionBoxContainer;
use yimaWidgetator\Service\WidgetManager;
use yimaWidgetator\Widget\Interfaces\WidgetInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\RendererInterface;
use Zend\View\ViewEvent;

class WidgetizeAggregateListener implements
    ServiceManagerAwareInterface, // Service manager injected manually on bootstrap
    ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            ViewEvent::EVENT_RENDERER_POST
            , array($this, 'onRenderRenderWidgets')
        );
    }

    /**
     * Render Defined Widgets into Layout Sections(area)
     *
     * @param ViewEvent $e MVC Event
     *
     * @return void
     */
    function onRenderRenderWidgets(ViewEvent $e)
    {
        $viewModel = $e->getModel();
        if (! $viewModel->terminate())
            // Only base themes has a widgets rendered into
            return;

        /** @var RegionBoxContainer $rBoxContainer */
        $rBoxContainer = $this->sm->get('yimaWidgetator.Widgetizer.Container');
        foreach ($rBoxContainer->getWidgets() as $region => $wdgs) {
            // Render Widget
            $this->__renderWidgets($region, $wdgs, $viewModel);
        }
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
        k(__FUNCTION__);

        foreach($widgets as $widget) {
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
                $options = $wArg['params'];
            }

            $instance = $widgetManager->get($widget, $options);

            return $instance;
        }

    /**
     * Get View Renderer
     *
     * @return RendererInterface
     */
    protected function __getViewRenderer()
    {
        $view = $this->sm->get('ViewRenderer');

        return $view;
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }
}
