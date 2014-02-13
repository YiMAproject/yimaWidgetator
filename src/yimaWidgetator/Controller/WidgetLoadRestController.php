<?php
namespace yimaWidgetator\Controller;

use yimaWidgetator\View\Helper\WidgetAjaxy;
use yimaWidgetator\Widget\AbstractWidget;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Session\Container as SessionContainer;
use Zend\Json;

/**
 * Class WidgetLoadRestController
 *
 * @package yimaWidgetator\Controller
 */
class WidgetLoadRestController extends AbstractRestfulController
{
    const ERR_INVALID_REQUEST = 'err_invalid_request';

    const ERR_RENDER_WIDGET   = 'err_render_widget';

    public function getList()
    {
        $request   = $this->getRequest();

        // retrive data ...
        $data = $request->getQuery()->toArray();

        return $this->proccessData($data);
    }

    public function create($data)
    {
        return $this->proccessData($data);
    }

    protected function proccessData($data)
    {
        $exception = false;
        $message   = 'SUCCESS';
        $content   = '';
        $scripts   = '';
        $links     = '';

        // run widget action {
        set_error_handler(
            function ($error, $errmsg = '', $file = '', $line = 0) use (&$exception, &$message, &$content) {
                $exception = true;
                $message   = 'ERR_WIDGET_ACTION_CALL';
                $content   = $errmsg;
            }, E_ALL
        );
        try {
            $params = array();
            if(isset($data['params'])) {
                if (is_array($data['params'])) {
                    // ajaxq send object as array here
                    $params = $data['params'];
                } else {
                    $params = Json\Json::decode($data['params']);
                }
            }

            // Validating requested data {
            if (!isset($data['widget'])) {
                $exception = true;
                $message   = self::ERR_INVALID_REQUEST;
            }

            if (!isset($params['request_token'])) {
                $exception = true;
                $message   = self::ERR_INVALID_REQUEST;
            } else {
                // validate token
                $token = $params['request_token'];

                $sesCont = new SessionContainer(WidgetAjaxy::SESSION_KEY);
                if (!$sesCont->offsetGet($token)) {
                    // invalid token
                    $exception = true;
                    $message   = self::ERR_INVALID_REQUEST;
                }
            }

            // ... }

            $renderer    = $this->getServiceLocator()->get('ViewRenderer');

            // reset container to have only widget script
            $headScripts = $renderer->headScript();
            $headScripts->deleteContainer();
            $headLinks   = $renderer->headLink();
            $headLinks  ->deleteContainer();

            if (!$exception) {
                // get widget

                /** @var $widget AbstractWidget */
                $widget  = $this->widget($data['widget']);
                $content = $widget->render();
            }

            // get scripts back
            $scripts = $headScripts->toString();
            $links   = $headLinks->toString();
        }
        catch (\Exception $e)
        {
            $exception = true;
            $message   = self::ERR_RENDER_WIDGET;
            $content   = $e->getMessage();
        }

        restore_error_handler();
        // ... }

        // set response
        $response  = $this->response;

        $response->setContent(Json\Json::encode(array(
                'exception' => $exception,
                'message'   => $message,
                'content'   => $content,
                'scripts'   => $scripts,
                'links'     => $links,
            )
        ));

        if ($exception) {
            $response->setStatusCode(417);
        }

        $header = new \Zend\Http\Header\ContentType();
        $header->value = 'Application/Json';
        $response->getHeaders()->addHeader($header);

        return $response;
    }
}