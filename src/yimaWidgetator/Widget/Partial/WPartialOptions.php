<?php
namespace yimaWidgetator\Widget\Partial;

use Poirot\Core\AbstractOptions;
use Poirot\Core\OpenOptions;
use Zend\View\Model\ViewModel;

class WPartialOptions extends OpenOptions
{
    /**
     * Path to viewScript or ViewModel Object
     *
     * @return string|ViewModel
     */
    function getModel()
    {
        return parent::__get('model');
    }

    /**
     * Path to viewScript or ViewModel Object
     *
     * @param string|ViewModel $model
     */
    function setModel($model)
    {
        parent::__set('model', $model);
    }
}
