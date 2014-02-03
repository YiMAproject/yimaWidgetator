<?php
namespace yimaWidgetator\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use yimaWidgetator\Exception;

/**
 * @category   yimaWidgetator
 */
class WidgetLoader extends AbstractPlugin
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
     * @param null|string $widget  Registered service in WidgetManager
     * @param array       $options Options for widget
     *
     * @return Model|Layout
     */
    public function __invoke($widget = null, $options = array())
    {
   	 	if (null === $widget) {
            return $this->getWidgetManager();
        }
        
        if (! is_array($options)) {
        	throw new Exception\InvalidArgumentException(
                sprintf(
    			    'Options must be an associated array of "config" => value you enter %s',
    			    gettype($options)
    		    )
            );
        }
        
        return $this->getWidgetManager()->get($widget, $options);
    }

    /**
     * Get Widget Manager
     *
     * @return array|object|WidgetManager
     *
     * @throws \Exception
     */
    public function getWidgetManager()
    {
    	if ($this->widgetManager == null) {
            $controller = $this->getController();
            if (! $controller instanceof ServiceLocatorAwareInterface) {
                throw new \Exception('yimaWidgetator plugin requires a controller that implements ServiceLocatorAwareInterface');
            }

            $serviceLocator      = $controller->getServiceLocator();
            $this->widgetManager = $serviceLocator->get('yimaWidgetator\WidgetManager');
    	}

    	return $this->widgetManager;
    }

}
