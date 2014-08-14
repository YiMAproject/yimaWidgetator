<?php
namespace yimaWidgetator\Widget;

use yimaWidgetator\Widget\Interfaces\MvcWidgetInterface;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

use Zend\View\Model\ViewModel;
use Zend\Filter;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Class AbstractMvcWidget
 *
 * @package yimaWidgetator\Widget
 */
class AbstractMvcWidget extends AbstractWidget implements
    MvcWidgetInterface
{
    use TraitMvcWidget
}
