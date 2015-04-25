<?php
namespace yimaWidgetator\Widget;

use Poirot\Core\Interfaces\iPoirotOptions;
use Poirot\Core\OpenOptions;
use yimaWidgetator\Widget\Interfaces\Feature\iInitableWidgetFeature;
use Zend\Filter;
use Poirot\Core\Interfaces\OptionsProviderInterface;
use yimaWidgetator\Widget\Interfaces\WidgetInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractWidget
    implements
    WidgetInterface,
    OptionsProviderInterface,
    iInitableWidgetFeature,
    ServiceLocatorAwareInterface // to get serviceManager and other registered widgets from within
{
    /**
     * @var string Unique ID of widget
     */
    private $ID;

    /**
     * @var iPoirotOptions
     */
    protected $options;

    /**
     * FilterInterface/inflector used to normalize names for use as template identifiers
     *
     * @var mixed
     */
    protected $inflector;

    /**
     * @var ServiceLocatorInterface|\yimaWidgetator\Service\WidgetManager
     */
    protected $serviceLocator;

    /**
     * Initialize object on widget manager -
     * instance creation
     *
     * @return string
     */
    function init()
    {
        // implement this if you want some feature after
        // object creation with widget manager
        // usually happen when you need all dependencies and initializers
        // done on object by widget manager.
        // exp. ServiceManager Injected Into Object ...
    }

    /**
     * Render widget as string output
     *
     * @return string
     */
    abstract public function render();

    /**
     * To String Magic Method
     * : flush widgets content over echo(output func.)
     *
     * ! by php: __toString must no thrown an exception
     *
     * @return string
     */
    function __toString()
    {
        try {
            $return = $this->render();
        }
        catch (\Exception $e) {
            $return = $e->getMessage();
        }

        return $return;
    }

    /**
     * @return iPoirotOptions
     */
    function options()
    {
        if (!$this->options)
            $this->options = self::optionsIns();

        return $this->options;
    }

    /**
     * Get An Bare Options Instance
     *
     * ! you can use any options object specific related
     *   to widget. exp. setBgColor('black')
     *
     * ! it used on easy access to options instance
     *   before constructing class
     *   [php]
     *      $opt = Filesystem::optionsIns();
     *      $opt->setSomeOption('value');
     *
     *      $class = new Filesystem($opt);
     *   [/php]
     *
     * @return iPoirotOptions
     */
    static function optionsIns()
    {
        return new OpenOptions();
    }

    /**
     * Set Widget ID
     *
     * @param string $id Widget ID
     *
     * @return $this
     */
    function setUid($id)
    {
        $this->ID = $id;

        return $this;
    }

    /**
     * Get Widget ID
     *
     * @return string
     */
    function getUid()
    {
        if (!$this->ID)
            $this->ID = $this->generateID();

        return $this->ID;
    }

    /**
     * Return Unique ID for each widget
     *
     * note: usage on every where that you need unique call of each widget
     *       exp. use on jscripts on each widget id
     *
     * @return string
     */
    final function generateID()
    {
        $uniqStr = function($length) {
            $char = "abcdefghijklmnopqrstuvwxyz0123456789";
            $char = str_shuffle($char);
            for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
                $rand .= $char{mt_rand(0, $l)};
            }

            return $rand;
        };

        if ($this->ID == null) {
            $class     = get_called_class();
            $module    = $this->deriveModuleNamespace($class);
            $widget    = $this->deriveWidgetName($class);

            $this->ID  = (($module != '') ? $this->inflectName($module).'_' : '')
                .$this->inflectName($widget).'_'.$uniqStr(5);
        }

        return $this->ID;
    }

	/**
	 * Inflect a name to a normalized value
	 *
	 * @param  string $name
     *
	 * @return string
	 */
	protected function inflectName($name)
	{
		if (! $this->inflector) {
			$this->inflector = new Filter\Word\CamelCaseToDash();
		}

		return strtolower($this->inflector->filter($name));
	}
	
	/**
	 * Determine the top-level namespace of the object
     *
     * note: TopLevel\Namespaces\To\Object
	 *
	 * @return string
	 */
	protected function deriveModuleNamespace()
	{
        $widget = get_class($this);

		if (!strstr($widget, '\\'))
			return '';

		$module = substr($widget, 0, strpos($widget, '\\'));

		return $module;
	}
	
	/**
	 * Determine the name of the widget
     *
     * TODO maybe unpredicted behave happen for none defined directory structured widgets
	 *
	 * @return string
	 */
	protected function deriveWidgetName()
	{
        $widget = get_class($this);

		if (strstr($widget, '\\')) {
			$widget = substr($widget, strpos($widget, '\\') + 1);
			$widget = substr($widget, 0, strrpos($widget, '\\'));
		}
		
		if (strstr($widget, '\\')) {
			$widget = substr($widget,strpos($widget, '\\')+1);
		}

		return $widget;
	}

    // implement methods ----------------------------------------------------------------
	
	/**
	 * Set serviceManager instance
     *
     * Attention: in very first time set WidgetManager and after get method
     *            from initializers, and before that not accessible.
	 *
	 * @param  ServiceLocatorInterface $serviceLocator
     *
	 * @return void
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
        /** @var $serviceLocator \yimaWidgetator\Service\WidgetManager */
        $this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * Retrieve WidgetManager instance
     *
     * Attention: this will set from WidgetManager and after get method
     *            from initializers, and before that not accessible.
	 *
	 * @return \yimaWidgetator\Service\WidgetManager
	 */
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
}
