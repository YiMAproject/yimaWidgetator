<?php
namespace yimaWidgetator\Service;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractWidgetHelper
 *
 * @package yimaWidgetator\Service
 */
class AbstractWidgetHelper
    implements ServiceLocatorAwareInterface
{
	/**
	 * @var WidgetManager
	 */
	protected $widgetManager;
	
    /**
     * Invoke as a functor
     *
     * If no arguments are given, grabs WidgetLoader
     * Otherwise, attempts to get widget from WidgetLoader
     *
     * @param  null|string $template
     * @return Model|Layout
     */
    public function __invoke($widget = null, $options = array() )
    {
        if (null === $widget) {
            return $this->getWidgetManager();
        }
        
        if (! is_array($options)) {
        	throw new Exception\InvalidArgumentException(sprintf(
    			'Options must be an associated array of "config"=>value you enter %s',
    			gettype($options)
    		));
        }
        
        return $this->getWidgetManager()->get($widget, $options);
    }

    /**
     * Get Widget Manager
     *
     * @return WidgetManager
     */
    protected function getWidgetManager()
    {
    	if (! $this->widgetManager) {
            $serviceManager = $this->getServiceLocator()->getServiceLocator();
            $widgetManager  = $serviceManager->get('yimaWidgetator\WidgetManager');

            if (!($widgetManager instanceof WidgetManager) || !($widgetManager instanceof AbstractPluginManager)) {
                throw new \Exception(
                    sprintf(
                        'WidgetManager must instance of WidgetManager or AbstractPluginManager, but "%s" given from \'yimaWidgetator\WidgetManager\'',
                        is_object($widgetManager) ? get_class($widgetManager) : gettype($widgetManager)
                    )
                );
            }

            $this->widgetManager = $widgetManager;
    	}

    	return $this->widgetManager;
    }
    

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
     * Get the main plugin manager. Useful for fetching dependencies from within factories.
     *
     * @return
     */
    public function getServiceLocator()
    {
    	return $this->serviceLocator;
    }
}
