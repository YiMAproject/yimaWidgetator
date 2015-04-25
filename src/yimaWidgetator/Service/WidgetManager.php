<?php
namespace yimaWidgetator\Service;

use yimaWidgetator\Widget\Interfaces\Feature\iInitableWidgetFeature;
use yimaWidgetator\Widget\Interfaces\ViewRendererPlugInterface;
use yimaWidgetator\Widget\Interfaces\WidgetInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Manager for loading widgets
 *
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
		$this->addInitializer([$this, 'injectWidgetDependencies'], false);
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
    public function get($name, $options = [], $usePeeringServiceManagers = false)
    {
    	$return = parent::get($name, $options, $usePeeringServiceManagers);

        // TODO Ensure that options was implemented for wdgets

        return $return;
    }

    /**
     * Validate the plugin
     *
     * Ensure we have a widget.
     *
     * @param  mixed $plugin
     *
     * @return true
     * @throws \Exception
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof WidgetInterface)
            return true;

        throw new \Exception(sprintf(
            'yimaWidgetator of type %s is invalid; must implement yimaWidgetator\Widget\WidgetInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }

    /**
     * Inject required dependencies into the widget.
     *
     * @param  WidgetInterface $widget
     * @param  ServiceLocatorInterface $serviceLocator
     *
     * @throws \Exception
     * @return void
     */
    public function injectWidgetDependencies(WidgetInterface $widget, ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceLocator \yimaWidgetator\Service\WidgetManager */
        $sm = $serviceLocator->getServiceLocator();
        if (!$sm)
            throw new \Exception('Service Manager can`t found.');

        /**
         * MVC Widget
         */
        if ($widget instanceof ViewRendererPlugInterface) {
            if (! $sm->has('ViewRenderer'))
                throw new \Exception('ViewRenderer service not found on Service Manager.');

            $widget->setView($sm->get('ViewRenderer'));
        }

        if ($widget instanceof iInitableWidgetFeature) {
            // widget initialize himself after all
            $widget->init();
        }

        /*if ($widget instanceof AbstractWidget) {
            // register widget in service locator with own unique id
            $sl = $this->getServiceLocator();
            $sl->setService($widget->getID(), $widget);
        }*/
    }
}
