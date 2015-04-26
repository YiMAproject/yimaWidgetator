<?php
namespace yimaWidgetator\Widget\Partial;

use yimaWidgetator\Widget\AbstractWidgetMvc;
use Zend\View\Model\ModelInterface;

class Widget extends AbstractWidgetMvc
{
    /**
     * Set view script to render by view renderer
     *
     * @param string|ModelInterface $nameOrModel The script/resource process, or a view model
     *
     * @return mixed
     */
    function setLayout($nameOrModel)
    {
        $this->options()->setModel($nameOrModel);
    }

    /**
     * Get view script layout
     *
     * @return string|ModelInterface
     */
    function getLayout()
    {
        $this->options()->getModel();
    }

    /**
     * @return WPartialOptions
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
     * @return WPartialOptions
     */
    static function optionsIns()
    {
        return new WPartialOptions;
    }
}
