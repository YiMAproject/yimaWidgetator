<?php
namespace yimaWidgetator\Listener;

use yimaWidgetator\Service\WidgetManager;
use yimaWidgetator\Widget\Interfaces\WidgetInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface;

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
            MvcEvent::EVENT_RENDER
            // TODO this event must change to something most closest but before render
            , array($this, 'onRenderRenderWidgets')
            , -9000
        );
    }

    /**
     * Render Defined Widgets into Layout Sections(area)
     *
     * @param MvcEvent $e MVC Event
     *
     * @return void
     */
    function onRenderRenderWidgets(MvcEvent $e)
    {
        $result = $e->getResult();
        if ($result instanceof Response)
            return;

        $viewModel = $e->getViewModel();
        /*if (! $viewModel instanceof ThemeDefaultInterface) {
            return false;
        }*/

        $widgetContainer = $this->sm->get('yimaWidgetator.Widgetizer.Container');
        $result = $widgetContainer->find();
        foreach ($result as $wdg) {
            // Render Widget
            $this->__renderWidget($wdg, $viewModel);
        }
    }

    /**
     * Render Widget From Container Result
     *
     * @param mixed          $wdg       Container Widget Entity
     * @param ModelInterface $viewModel View Model
     *
     * @return bool
     */
    protected function __renderWidget($wdg, ModelInterface $viewModel)
    {
        /** @var $widgetManager WidgetManager */
        $widgetManager = $this->sm->get('yimaWidgetator.WidgetManager');

        /** @var $widgetModel WidgetModelInterface */
        $widgetModel = $this->sm->get('Widgetizer.Model.Widget');
        /** @var $w Widget */
        $w = $widgetModel->getWidgetByUid( $r->get(CWE::WIDGET_UID) );
        if (!$w || !$widgetManager->has($w->get(Widget::WIDGET))) {
            // we don't have a widget with this name registered.
            // ...
            return false;
        }

        // get widget from widgetManager by Widget Name Field
        /** @var $widget WidgetInterface */
        $widget = $widgetManager->get($w->get(Widget::WIDGET));
        if (method_exists($widget, 'setFromArray')) {
            // load prop. entities into widget
            $widget->setFromArray($w->getArrayCopy());
        }

        $template_area = $r->get(CWE::TEMPLATE_AREA);
        if (ShareRegistery::isManagementAllowed()) {
            // Decorate widgets with ui management partial template
            $view = $this->__getViewRenderer();
            $widgetViewModel = new ViewModel(array('widget' => $widget));
            $widgetViewModel->setTemplate('partial/builderfront/surround-widgets-decorator');
            $content = $view->render($widgetViewModel);
        } else {
            // Render Widget
            $content = $widget->render();
        }

        $viewModel->{$template_area} .= $content;
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
