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
	protected $widgetLoader;
	
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
            return $this->getWidgetLoader();
        }
        
        if (! is_array($options)) {
        	throw new Exception\InvalidArgumentException(sprintf(
    			'Options must be an associated array of "config"=>value you enter %s',
    			gettype($options)
    		));
        }
        
        return $this->getWidgetLoader()->get($widget, $options);
    }
    
    public function getWidgetLoader()
    {
    	if (null !== $this->widgetLoader) {
    		return $this->widgetLoader;
    	}
    	
    	$controller = $this->getController();
    	if (! $controller instanceof ServiceLocatorAwareInterface) {
    		throw new \Exception('yimaWidgetator plugin requires a controller that implements ServiceLocatorAwareInterface');
    	}
    	
    	$serviceLocator     = $controller->getServiceLocator();
    	$this->widgetLoader = $serviceLocator->get('WidgetLoader');
    	 
    	return $this->widgetLoader;
    }

}
