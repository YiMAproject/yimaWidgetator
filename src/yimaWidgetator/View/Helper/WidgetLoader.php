<?php
namespace yimaWidgetator\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use yimaWidgetator\Exception;

/**
 * @category   yimaWidgetator
 */
class WidgetLoader extends AbstractHelper
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
    	if ($this->widgetManager == null) {
            $serviceLocator      = $this->getServiceLocator();
            $this->widgetManager = $serviceLocator->get('yimaWidgetator\WidgetManager');
    	}

    	return $this->widgetManager;
    }
    

    /**
     * Set the main service locator so factories can have access to it to pull deps
     *
     * @param ServiceLocatorInterface $serviceLocator
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
     * @return mixed
     */
    public function getServiceLocator()
    {
    	$parentLocator = $this->serviceLocator->getServiceLocator();
        
        return $parentLocator; 
    }
}
