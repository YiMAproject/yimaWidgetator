<?php
namespace yimaWidgetator\Widget\Interfaces;

use Zend\View\Model\ModelInterface;

interface WidgetMvcInterface extends ViewRendererPlugInterface
{
    /**
     * Set view script to render by view renderer
     *
     * @param string|ModelInterface $nameOrModel The script/resource process, or a view model
     *
     * @return mixed
     */
    public function setLayout($nameOrModel);

    /**
     * Get view script layout
     *
     * @return string|ModelInterface
     */
    public function getLayout();
}
