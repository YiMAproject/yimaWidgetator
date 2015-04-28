<?php
namespace yimaWidgetator\Listener;

use yimaWidgetator\View\Renderer\PhpRenderer;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\ViewEvent;

class WidgetizeViewStrategy implements
    ServiceLocatorAwareInterface, // Service manager injected manually on bootstrap
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

    static protected $resolved;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 100)
    {
        $this->listeners[] = $events->attach(
            ViewEvent::EVENT_RENDERER
            , [$this, 'onRenderRenderWidgets']
            , $priority
        );

        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), $priority);
    }

    /**
     * Render Defined Widgets into Layout Sections(area)
     *
     * @param ViewEvent $e MVC Event
     *
     * @return void|PhpRenderer
     */
    function onRenderRenderWidgets(ViewEvent $e)
    {
        $viewModel = $e->getModel();

        $return = false;

        $options = $viewModel->getOptions();
        if (!array_key_exists('has_parent', $options) && !self::$resolved) {
            $return = new PhpRenderer;
            $return->setServiceLocator($this->sm);

            self::$resolved = true;
        }

        return $return;
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results.
     *
     * @param ViewEvent $e
     * @return void
     */
    public function injectResponse(ViewEvent $e)
    {
        $result   = $e->getResult();
        $response = $e->getResponse();

        $response->setContent($result);
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
