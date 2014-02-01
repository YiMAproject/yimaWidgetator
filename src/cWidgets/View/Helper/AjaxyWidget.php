<?php
namespace yimaWidgetator\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use yimaWidgetator\Exception;
use Zend\Json;

/**
 * @category   yimaWidgetator
 */
class AjaxyWidget extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * Detemine base script attached ?!!
     *
     * @var bool
     */
    protected $isScriptAttached = false;

    /**
     * Invoke as a functor
     *
     * @param  null|string $template
     * @return Model|Layout
     */
    public function __invoke($widget, $action, $domElemID = null, $params = array() )
    {
        $view = $this->getView();

        if (! $this->isScriptAttached) {
            // attach needed scripts
            $view->headScript()
                ->prependFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js')

                ->appendFile($view->basePath().'/cacoon/js/jquery.json.min.js')
                ->appendFile($view->basePath().'/cacoon/js/jquery.ajaxq.min.js')

                ->appendFile($view->basePath().'/cacoon/js/Candoo.namespace.js')

                ->captureStart();
                $script = file_get_contents(__DIR__.DS.'AjaxyWidget.js');

                $url = $view->url('yimaWidgetator_restLoadWidget');
                $script = str_replace('{{url}}', $url, $script);

                echo $script;

                $view->headScript()->captureEnd();

            $this->isScriptAttached = true;
        }

        $params = Json\Json::encode($params);
        $view->headScript()->captureStart();
        echo "
        $(document).ready(function(){
            Candoo.Core.Widget.Loader.getAction('$widget', '$action', '$domElemID', $params);
        });
        ";
        $view->headScript()->captureEnd();
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
