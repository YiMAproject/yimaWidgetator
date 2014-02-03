<?php
namespace yimaWidgetator;

use Zend\View\Model\ViewModel;
use Zend\Http\Request as HttpRequest;
use Zend\StdLib\AbstractOptions;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractWidget extends AbstractOptions implements 
	ServiceLocatorAwareInterface
{
	const NO_VIEW_MODEL = 'NoViewModelToRender';
	
	protected $serviceLocator;
	
	/**
     * FilterInterface/inflector used to normalize names for use as template identifiers
     *
     * @var mixed
     */
    protected $inflector;
    
    /**
     * Default template generated for each action
     * 
     * @var string
     */
    protected $defaultTemplate;
    
    /**
     * Akharin methodAction i ke sedaa zade shode
     * 
     * @var string
     */
    protected $lastActionCall;
    
    /**
     * Use for generating ID
     * 
     */
    protected static $counter;
    
    protected $uid;

    protected $idpref;
    
    /**
     * Listi az widget haaye digar ke daroone in widget gharaar migirand
     * 
     * @var array
     */
    protected $nested = array();
    
    /**
     * Jaigaah e pish farzi ke widget haaye digar hengaame nest
     * shodan dar in motaghaier dar view gharaar migirand
     */
    protected $defaultCapture = 'content';
    
    // class internal cache to avoid from repeating codes
    // protected static $addedPathStacks = array();
    
    protected $noViewModel = array();
	
    /**
     * Return UniqID for widgets
     * 
     */
    final public function getID()
    {
        self::$counter++;
        $class     = get_class($this);
        $module    = $this->deriveModuleNamespace($class);
        $widget    = $this->deriveWidgetName($class);

        $uid = (($module != '') ? $this->inflectName($module) : '')
               .'_'.$this->inflectName($widget).'_'.self::$counter;

        $this->uid = $this->idpref.$uid;

    	return $this->uid;
    }

    /**
     * Bazi vaght haa ehtiaj ast ke id haa be nahve digar generate
     * shavand masalan hengaami ke yek widget be soorat e ajax load mishavad
     * yek prefix be aanhaa ezaafe mikonim ke baa aanhaaii ke az tarigh e barnaame
     * bargozaari shode and id e yeksaan nadaashte baashad.
     *
     * @param $prefix
     */
    final public function setIdPrefix($prefix)
    {
        $this->idpref = (string) $prefix.'_';
    }
    
	public function __call($action, $params)
	{
		//reset to defaults
		$this->setNoViewModel(false,$action);
		
		$defaultTemplate = $this->getDefaultTemplate();
		
		$method = $this->getMethodFromAction($action);
		if (method_exists($this,$method)) 
		{
			if (is_array($params) && !empty($params)) {
				if (is_array($params[0])) {
					$this->setFromArray($params[0]);
					unset($params[0]);
				} elseif (is_string($params[0])) {
					$this->setDefaultTemplate($params[0]);
				}
			}
			
			if (empty($params)) {
				$params = array();
			}
			
			$this->lastActionCall = $action;
			$result = call_user_func_array(array($this, $method),$params); 
		}
		else 
		{
			throw new Exception\NotFoundMethodException(sprintf(
					'Method %s not found in (%s) class ',
					$method,get_class($this)
			));
		}
		
		if ($this->getNoViewModel($action) || $result == self::NO_VIEW_MODEL)
		{
			// back to previous settings before call
			$this->setDefaultTemplate($defaultTemplate);
			
			return ($result == self::NO_VIEW_MODEL) ? null : $result;
		}
		
		// set viewModel --------------
		if ($result == null) {
			$result = array();
		}
		
		if (is_object($result)) {
			if ($result instanceof ViewModel) {
				$this->prepareTemplate($result);
				
				return $result;
			}
			
			throw new Exception\InvalidResultException(sprintf(
				'Result object returned from methodAction (%s) must implemented of Zend\View\Model\ViewModel',
				$method
			));
				
		} else if (is_string($result)) {
			$result = array('content' => $result);
		} else if (!is_array($result)) {
			throw new Exception\InvalidResultException(sprintf(
					'Result object returned from methodAction (%s) must implemented of Zend\View\Model\ViewModel, String or Array',
					$method
			));
		}
		
		// build ViewModel
		$model = $this->buildViewModel($result);
		
		// back to previous settings before call
		$this->setDefaultTemplate($defaultTemplate);
		
		return $model;
	}

    /**
     * Transform an "action" token into a method name
     *
     * @param  string $action
     * @return string
     */
    protected function getMethodFromAction($action)
    {
        $method  = str_replace(array('.', '-', '_'), ' ', $action);
        $method  = ucwords($method);
        $method  = str_replace(' ', '', $method);
        $method  = lcfirst($method);
        $method .= 'Action';

        return $method;
    }
	
	/**
	 * Dar soorati ke khorooji e widget gharaar nist ViewModel baashad
	 */
	protected function setNoViewModel($bool = true, $action = null)
	{
		if ($action == null) {
			$action = ($this->lastActionCall) ? ($this->lastActionCall) : 'default';
		}
		
		$this->noViewModel[$action] = (boolean) $bool; 
	}
	
	protected function getNoViewModel($action = null)
	{
		if ($action == null) {
			$action = ($this->lastActionCall) ? ($this->lastActionCall) : 'default';
		}
	
		return $this->noViewModel[$action];
	}
	
	protected function buildViewModel($result)
	{
		$model = new ViewModel($result);
	
		// set template
		$this->prepareTemplate($model);
	
		// pass widget uid into ViewModel
		$model->setVariable('uid',$this->getID());
	
		// add Nested Childs to Model
		$this->injectChildsToViewModel($model);
	
		return $model;
	}

    protected function prepareTemplate(ViewModel $model)
    {
        /* agar template be in soorat bood *anyname* in be onvaane name
         * template dar nazar gerefte mishavad, pas baayad prefix haa ham
         * be aan ezaafe shavad
         * 		namespace/...../widget-name     /action/anyname
         * exp. c-widget/widget/navigation-menu/create/default
         *
         * dar gheire in soorat hamaan meghdaar e template dar nazar gerefte mishavad
         * va prefix haa be aan ezaafe nemishavad
         */
        if('' != $template = $model->getTemplate() ) {
            if($this->deriveModuleNamespace($template) == '') {
                $model->setTemplate('');
                $this->setDefaultTemplate($template);

                $this->prepareTemplate($model);
            }

            return;
        }

        // register module default widget's place to ViewResolver
        //$this->registerViewResolverPathStack();

        $widgetClass  = get_class($this);

        $module = $this->deriveModuleNamespace($widgetClass);
        $widget = $this->deriveWidgetName($widgetClass);

        $template   = $this->inflectName($module);
        if (!empty($template)) {
            $template .= '/';
        }
        $template  .= 'widget/'.$this->inflectName($widget);
        $template  .= '/'.$this->inflectName($this->lastActionCall);
        $template  .= '/'.$this->inflectName(
            $this->getDefaultTemplate()
        );

        $model->setTemplate($template);
    }

    /*
     *  In ghesmat ro be ohdeie module migozaarim
     *
	protected function registerViewResolverPathStack()
	{
		$moduleName = $this->deriveModuleNamespace(get_class($this));
		$inflectedModuleName = $this->inflectName($moduleName);
	
		if (isset(self::$addedPathStacks[$inflectedModuleName])) {
			// we added this previous
			return;
		}
	
		$moduleClass = $moduleName.'\Module';
		if (method_exists($moduleClass,'getDir')) {
			$path = call_user_func(array($moduleClass,'getDir'));
		} else {
			$file = new SplFileInfo(APP_DIR_CORE.DS.$moduleName);
				
			$path = APP_DIR_MODULES.DS.$moduleName;
			if ($file->isReadable()) {
				$path = APP_DIR_CORE.DS.$moduleName;
			}
		}
		$path .= DS.'view';
	
		# set module view dir to end of stack,
		# agar samte template va yaa har jaaie digar template e alternate
		# peidaa nashavad, dar aakhar folder e module va view raa jaigozin mikonim
		#
		# note: $viewResolver->getPaths() yek object Zend\Stdlib\SplStack ast ke
		# 		 hengaame itterate az aakharin ozv shoroo mikonad, baraaie hamin
		# 		 dar injaa path e module/view ro be aval ezaafe kardam
		#
		$serviceLocator = $this->getServiceLocator();
		$viewResolver   = $serviceLocator->get('ViewTemplatePathStack');
	
		$paths = $viewResolver->getPaths()->toArray();
		$paths = array_reverse($paths);
	
		array_unshift($paths,$path);
		
		$viewResolver->setPaths($paths);
	
		self::$addedPathStacks[$inflectedModuleName] = true;
	}
    */
	
	public function addNested($widgetName, $widgetAction = null, $params = null, $captureTo = null)
	{
		// addNested(array('ezWidget','callMethod'),array(),'content');
		if (is_array($widgetName)) {
			$captureTo = (empty($params) || !is_string($params)) ? $captureTo : $params;
			$params    = $widgetAction;
			list($widgetName,$widgetAction) = $widgetName;
		} 
		elseif (is_object($widgetName)) {
			if (! ($widgetName instanceof ViewModel || $widgetName instanceof AbstractWidget) ) {
				throw new Exception\InvalidArgumentException(sprintf(
					'%s::%s You pas Object as a argument #1 that must be instance of ViewModel or AbstractWidget, %s given.',
					get_class($this), __FUNCTION__, get_class($widgetName) 
				));
			}
			// addNested(ViewModel ,'content');
			if ($widgetName instanceof ViewModel) {
				$captureTo = ($widgetAction === null || !is_string($widgetAction)) ? $captureTo : $widgetAction;
			} 
		}
		
		array_push($this->nested,array(
			'class'  	 => $widgetName,
			'method' 	 => $widgetAction,
			'params' 	 => $params,
			'capture_to' => $captureTo)
		);
		
		return $this;
	}
	
	protected function injectChildsToViewModel(ViewModel $viewModel)
	{
		$widgetLoader = $this->getServiceLocator();

		foreach ($this->nested as $nest) {
			$widget = $nest['class'];
			if (is_string($widget)) {
				$widget = $widgetLoader->get($widget); 
			}
			
			if (! is_object($widget) && (! ($widget instanceof ViewModel) || ! ($widget instanceof AbstractWidget)  ) ) {
				throw new Exception\InvalidArgumentException(sprintf(
						'You pas nested (%s) Object that not instance of ViewModel or AbstractWidget',
						get_class($widget)
				));
			}
			
			$captureTo = (null !== $nest['capture_to']) ? $nest['capture_to'] : $this->getCaptureTo();

			
			// abstract widget :
			// get viewModel from abstract widget and add as child to root model
			if ($widget instanceof AbstractWidget) {
				$method = $nest['method'];
				$params = $nest['params'];
				
				$wModel = $widget->$method($params);
			} else {
				$wModel = $widget;
			}
			
			$viewModel->addChild($wModel,$captureTo,true);
		}
	}
	
	public function getCaptureTo()
	{
		return $this->defaultCapture;
	}
	
	public function getDefaultTemplate()
	{
		if ( empty($this->defaultTemplate) ) {
			$this->defaultTemplate = 'default'; 
		}
		
		return $this->defaultTemplate;
	}
	
	public function setDefaultTemplate($template)
	{
		$this->defaultTemplate = $template; 
		return $this;
	}
	
	/**
	 * Inflect a name to a normalized value
	 *
	 * @param  string $name
	 * @return string
	 */
	protected function inflectName($name)
	{
		if (!$this->inflector) {
			$this->inflector = new CamelCaseToDashFilter();
		}
		$name = $this->inflector->filter($name);
		return strtolower($name);
	}
	
	/**
	 * Determine the top-level namespace of the controller
	 *
	 * @param  string $controller
	 * @return string
	 */
	protected function deriveModuleNamespace($widget)
	{
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
	 * @param  string $controller
	 * @return string
	 */
	protected function deriveWidgetName($widget)
	{
		if (strstr($widget, '\\')) {
			$widget = substr($widget, strpos($widget, '\\') + 1);
			$widget = substr($widget, 0, strrpos($widget, '\\'));
		}
		
		if (strstr($widget, '\\')) {
			$widget = substr($widget,strpos($widget, '\\')+1);
		}
		
		return $widget;
	}
	
	/**
	 * Set serviceManager instance
	 *
	 * @param  ServiceLocatorInterface $serviceLocator
	 * @return void
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
		
		return $this;
	}
	
	/**
	 * Retrieve serviceManager instance
	 * 
	 * !!! NOTE: hatman tavajoh daashte baashid ke serviceLocator pas az tolid e instance
	 * 			 va tavasote initializer haa be system ezaafe mishavad
	 * 			 dar construct va tavasote option haaye aan ghaabele dastresi nist
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
	
}
