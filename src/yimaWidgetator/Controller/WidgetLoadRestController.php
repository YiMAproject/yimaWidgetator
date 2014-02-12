<?php
namespace yimaWidgetator\Controller;

use yimaWidgetator\Widget\AbstractWidget;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Json;

/**
 * Class WidgetLoadRestController
 *
 * @package yimaWidgetator\Controller
 */
class WidgetLoadRestController extends AbstractRestfulController
{
    /**
     * array(
     *  'exception' => false,
     *  'content'   => '',
     *  'message'   => '',
     * );
     */

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
        $response  = $this->response;

        $exception = false;
        $message   = 'SUCCESS';
        $content   = '';
        $scripts   = '';
        $links     = '';

        if (!isset($data['widget'])) {
            $exception = true;
            $message   = 'ERR_INVALID_REQUEST';
        }

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
                $widget->setFromArray($params);
                $content = $widget->render();
            }

            // get scripts back
            $scripts = $headScripts->toString();
            $links   = $headLinks->toString();
        }
        catch (\Exception $e)
        {
            $exception = true;
            $message   = 'ERR_WIDGET_ACTION_CALL';
            $content   = $e->getMessage();
        }

        restore_error_handler();
        // ... }

        // set response
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