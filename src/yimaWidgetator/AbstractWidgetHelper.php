<?php
namespace yimaWidgetator;

use yimaWidgetator\Service\RegionBoxContainer;
use yimaWidgetator\Service\WidgetManager;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractWidgetHelper
    implements
    ServiceLocatorAwareInterface
{
	/**
	 * @var WidgetManager
	 */
	protected $widgetManager;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Container for layout region boxes widgets
     * @var RegionBoxContainer
     */
    protected $rBoxContainer;

    /**
     * Invoke as a functor
     *
     * - if no arguments are given, grabs WidgetLoader
     *   Otherwise: attempts to get widget from WidgetLoader
     *
     * @param  null|string $widget
     * @param array        $options
     *
     * @throws \Exception
     * @return mixed
     */
    public function __invoke($widget = null, $options = [])
    {
        if (null === $widget)
            return $this;

        return $this->getWidgetManager()->get($widget, $options);
    }

    /**
     * Add Widget To Region Box Container
     *
     * @param $region
     * @param $widget
     * @param int $priority
     *
     * @return $this
     */
    function addWidget($region, $widget, $priority = 0)
    {
        if (!$this->rBoxContainer) {
            $sm = $this->getServiceLocator()->getServiceLocator();
            $rBoxContainer  = $sm->get('yimaWidgetator.Widgetizer.Container');

            $this->rBoxContainer = $rBoxContainer;
        }

        $this->rBoxContainer->addWidget($region, $widget, $priority);

        return $this;
    }

    /**
     * Get Widget Manager
     *
     * @throws \Exception
     * @return WidgetManager
     */
    function getWidgetManager()
    {
    	if (! $this->widgetManager) {
            $sm = $this->getServiceLocator()->getServiceLocator();
            $widgetManager  = $sm->get('yimaWidgetator.WidgetManager');

            if (!($widgetManager instanceof WidgetManager)
                || !($widgetManager instanceof AbstractPluginManager)
            )
                throw new \Exception(sprintf(
                    'WidgetManager must instance of WidgetManager or AbstractPluginManager, but "%s" given from \'yimaWidgetator.WidgetManager\'',
                    is_object($widgetManager) ? get_class($widgetManager) : gettype($widgetManager)
                ));

            $this->widgetManager = $widgetManager;
    	}

    	return $this->widgetManager;
    }

    // Implement ServiceLocator:

    /**
     * Set the main service locator so factories can have access to it to pull deps
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractPluginManager
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get the main plugin manager.
     * Useful for fetching dependencies from within factories.
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
    	return $this->serviceLocator;
    }
}
