<?php
namespace yimaWidgetator\Widget;

use Poirot\Dataset\Entity;
use yimaWidgetator\Widget\Interfaces\WidgetInterface;
use Zend\Filter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractMvcWidget
 *
 * @package yimaWidgetator\Widget
 */
abstract class AbstractWidget
    implements
    WidgetInterface,
    ServiceLocatorAwareInterface // to get serviceManager and other registered widgets from within
{
    /**
     * @var string Unique ID of widget
     */
    private $ID;

    /**
     * Entity To Store Widget Options
     * : with this we can retrieve data later
     *
     * @var Entity
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
     * Render widget as string output
     *
     * @return string
     */
    abstract public function render();

    /**
     * To String Magic Method
     * : flush widgets content over echo(output func.)
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Call Setter methods from array
     * : variable_name => setVariableName()
     *
     * @return $this
     */
    public function setFromArray(array $options)
    {
        foreach ($options as $key => $val) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (!method_exists($this, $setter)) {
                continue;
            }

            $this->{$setter}($val);
        }

        return $this;
    }

    /**
     * Get Options Entity Object
     *
     * @return Entity
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->options = new Entity();
        }

        return $this->options;
    }

    /**
     * Set Widget ID
     *
     * @param string $id Widget ID
     *
     * @return $this
     */
    public function setUid($id)
    {
        $this->ID = $id;

        return $this;
    }

    /**
     * Get Widget ID
     *
     * @return string
     */
    public function getUid()
    {
        if (!$this->ID) {
            $this->ID = $this->generateID();
        }

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
    final public function generateID()
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
	 * Determine the top-level namespace of the controller
	 *
	 * @return string
	 */
	protected function deriveModuleNamespace()
	{
        $widget = get_class($this);

		if (!strstr($widget, '\\')) {

			return '';
		}

		$module = substr($widget, 0, strpos($widget, '\\'));

		return $module;
	}
	
	/**
	 * Determine the name of the widget
	 *
	 * Strip the namespace, and the suffix "yimaWidgetator" if present.
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
