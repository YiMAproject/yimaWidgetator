<?php
namespace yimaWidgetator\Service;

use yimaWidgetator\AbstractWidget;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Manager for loading widgets
 *
 * @category   yimaWidgetator
 */
class WidgetManager extends AbstractPluginManager 
{
    /**
     * We do not want arbitrary classes instantiated as widgets.
     *
     * note: if true will add service if class equal to service name exists
     *
     * @var bool
     */
    protected $autoAddInvokableClass = false;
    
    /**
     * Whether or not to share by default
     * Every yimaWidgetator must constructed, because of each widgets are unique
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * service manager, event manager, and plugin manager
     *
     * @param  null|ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        
        // Pushing to bottom of stack to ensure this is done last ------ V
        
		$this->addInitializer(array($this, 'injectWidgetDependencies'), false);

		// maa har widget ro ke id unique daarad ro mojadad zakhire mikonim be
		// in tartib bar asaase id dobaare ghaabele faraakhaanist
		$this->addInitializer(array($this, 'setWidgetAsService'), false);
    }
    
    /**
     * Validate the plugin
     *
     * Ensure we have a widget.
     *
     * @param  mixed $plugin
     *
     * @return true
     *
     * @throws \Exception
     */
    public function validatePlugin($plugin)
    {
    	if ($plugin instanceof AbstractWidget) {
    		return;
    	}
    
    	throw new \Exception(sprintf(
    			'yimaWidgetator of type %s is invalid; must implement yimaWidgetator\AbstractWidget',
    			(is_object($plugin) ? get_class($plugin) : gettype($plugin))
    	));
    }
    
    /**
     * Override: do not use peering service manager to retrieve widgets
     *
     * @param  string $name
     * @param  array  $options
     * @param  bool   $usePeeringServiceManagers
     *
     * @return mixed
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = false)
    {
    	return parent::get($name, $options, $usePeeringServiceManagers);
    }

    /**
     * Inject required dependencies into the widget.
     *
     * @param  AbstractWidget          $widget
     * @param  ServiceLocatorInterface $serviceLocator
     *
     * @return void
     */
    public function injectWidgetDependencies(AbstractWidget $widget, ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceLocator \yimaWidgetator\Service\WidgetManager */

        /*if ($widget instanceof ThatInterface) {
            // do something with .... $widget
        }*/
    }

    /**
     * Store widget as service in serviceLocator
     *
     * @param AbstractWidget          $widget
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    public function setWidgetAsService(AbstractWidget $widget, ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceLocator \yimaWidgetator\Service\WidgetManager */

        $uid = $widget->getID();
        $serviceLocator->setService($uid, $widget);

        return $this;
    }
}
