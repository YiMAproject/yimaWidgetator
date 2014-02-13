<?php
namespace yimaWidgetator\View\Helper;

use Zend\Json;
use Zend\Session\Container as SessionContainer;

/**
 * Ajax load widgets helper
 *
 * @package yimaWidgetator\View\Helper
 */
class WidgetAjaxy extends WidgetLoader
{
    const SESSION_KEY = 'Widget_Ajaxy_Helper_Session_Key';

    /**
     * Detemine base script attached ?!!
     *
     * @var bool
     */
    protected $isScriptAttached = false;

    /**
     * Loading widgets by generating needed jScript
     *
     * @param null|string $widget    Widget registered service name, null will return this class
     * @param array       $params    Options set into widget
     * @param null|string $domElemID DomElementID to put widget content into
     * @param null|string $callBack  Callback after successfully widget loaded,
     *                               this call back get response from widget load controller
     *
     * @return $this|mixed
     */
    public function __invoke($widget = null, array $params = array(), $domElemID = null, $callBack = null)
    {
        if ($widget == null) {
            // return this
            return $this;
        }

        // attach needed scripts
        if (!$this->isScriptsAttached()) {
            $this->attachScripts();
        }

        // store a unique key in session to validate rest calls {
        $token    = md5($widget.serialize($params).uniqid());

        $sesCont = new SessionContainer(self::SESSION_KEY);
        $sesCont->$token = time();
        $sesCont->setExpirationSeconds(30, $token);

        $params  = array_merge($params, array('request_token' => $token));
        // ... }

        // append widget loader script {
        $params   = Json\Json::encode($params);
        $callBack = ($callBack) ?: 'null';
        $this->getView()->jQuery()
            ->appendScript("
                $(document).ready(function(){
                    YimaWidgetLoader('$widget', $params, '$domElemID', $callBack);
                });
            ");
        // ... }

        return $this;
    }

    /**
     * Attach needed scripts to load widgets
     *
     * after attaching scripts, you can call:
     *  YimaWidgetLoader('$widget', $params, '$domElemID', $callBack);
     *  inside view and getting widgets
     *
     * @return $this
     */
    public function attachScripts()
    {
        if ($this->isScriptsAttached()) {

            return $this;
        }

        $view = $this->getView();
        $view->jQuery()
            ->enable()
            // we can change target of js files with static_uri_helper config key
            ->appendFile($view->staticUri('Yima.Widgetator.JS.Jquery.Json'))
            ->appendFile($view->staticUri('Yima.Widgetator.JS.Jquery.Ajaxq'))

            ->appendScript(
                str_replace(
                    '{{url}}',
                    $view->url('yimaWidgetator_restLoadWidget'),
                    file_get_contents(__DIR__.DS.'WidgetAjaxy.js')
                )
            );

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
}
