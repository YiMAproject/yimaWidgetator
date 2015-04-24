<?php
namespace yimaWidgetator\View\Helper;

use Zend\Json;
use Zend\Session\Container as SessionContainer;
use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

/**
 * Ajax load for widgets
 *
 */
class WidgetAjaxy implements HelperInterface
{
    const SESSION_KEY = 'Widget_Ajaxy_Helper_Session_Key';

    /**
     * View object instance
     *
     * @var Renderer
     */
    protected $view = null;

    /**
     * Determine base script attached ?!!
     *
     * @var bool
     */
    protected $isScriptAttached = false;

    /**
     * Loading widgets with ajax by generating needed jScript
     *
     * @param string|null  $cssSelector Css Selector
     * @param array        $options     Widgetator Options
     *
     * @return $this
     */
    public function __invoke($cssSelector = null, $options = [])
    {
        if ($cssSelector == null)
            // return this
            return $this;

        $defaults = [
            'method'   => 'render',
            'params'   => array(),
            // 'callback' => 'function(element, response){}',

            'request_token' => $this->generateToken(),
        ];

        $options = array_merge($defaults, $options);
        if (isset($options['callback'])){
            $callback = $options['callback'];
            unset($options['callback']);
        }

        // attach needed scripts
        if (!$this->isScriptsAttached())
            $this->attachScripts();

        // append widget loader script {
        $requestParams = Json\Json::encode($options);
        $jsFunc = ($callback)
            ? "widgetator($requestParams, $callback);"
            : "widgetator($requestParams);";

        $this->getView()->inlineScript()
            ->appendScript("
                (function($){
                    $(document).ready(function(){
                        $('$cssSelector').$jsFunc;
                    });
                })(jQuery);
            ");
        // ... }

        return $this;

    }

    /**
     * Store a unique key in session to validate rest calls
     *
     * @return string
     */
    public function generateToken()
    {
        $token   = md5(__CLASS__.uniqid());

        $sesCont = new SessionContainer(self::SESSION_KEY);
        $sesCont->$token = time();
        $sesCont->setExpirationSeconds(30, $token);

        return $token;
    }

    /**
     * Attach needed scripts to load widgets
     *
     * after attaching scripts, you can call:
     *  YimaWidgetLoader('$widget', $options, '$domElemID', $callBack);
     *  inside view and getting widgets
     *
     * @return $this
     */
    public function attachScripts()
    {
        if ($this->isScriptsAttached())
            return $this;

        $view = $this->getView();
        $view->inlineScript()
            // we can change target of js files with static_uri_helper config key
            ->appendFile($view->staticUri('Yima.Widgetator.JS.Jquery.Json'))
            ->appendFile($view->staticUri('Yima.Widgetator.JS.Jquery.Ajaxq'))
            ->appendFile($view->staticUri('Yima.Widgetator.JS.Jquery.WidgetAjaxy'))

            /*
            ->appendScript(
                str_replace(
                    '{{url}}',
                    $view->url('yimaWidgetator_restLoadWidget'),
                    file_get_contents(__DIR__.DS.'WidgetAjaxy.js')
                )
            )
            */
        ;

        $this->isScriptAttached = true;

        return $this;
    }

    /**
     * Is scripts attached ?
     *
     * @return bool
     */
    public function isScriptsAttached()
    {
        return (boolean) $this->isScriptAttached;
    }

    /**
     * Set the View object
     *
     * @param  Renderer $view
     *
     * @return $this
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the view object
     *
     * @return null|Renderer
     */
    public function getView()
    {
        return $this->view;
    }
}
