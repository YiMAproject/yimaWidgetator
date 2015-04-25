<?php
namespace yimaWidgetator\Widget;

use Zend\Filter;
use yimaWidgetator\Widget\Interfaces\WidgetMvcInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface as Renderer;

class AbstractWidgetMvc extends AbstractWidget
    implements
    WidgetMvcInterface
{
    public static $PATH_PREFIX     = 'widgets';
    public static $LAYOUT_DEF_NAME = 'default';

    /**
     * @var ServiceLocatorInterface|\yimaWidgetator\Service\WidgetManager
     */
    protected $serviceLocator;

    /**
     * @var Renderer
     */
    protected $view;

    /**
     * @var string|ViewModel View script layout path or Model to render
     */
    protected $layout;

    /**
     * Render widget as string output
     *
     * This is minimum implementation!!
     * always we need to implement this method to use options and
     * extra feature that we need to achieve desire output.
     *
     * @return string
     */
    function render()
    {
        return $this->getView()->render($this->getLayout(), $this->options()->toArray());
    }

    /**
     * Set view script to render by view renderer
     *
     * @param string|ModelInterface $nameOrModel The script/resource process, or a view model
     *
     * @return mixed
     */
    final function setLayout($nameOrModel)
    {
        $this->layout = $nameOrModel;
    }

    /**
     * Get view script layout
     *
     * @return string|ModelInterface
     */
    final function getLayout()
    {
        if ($this->layout === null) {
            $DS = (defined('DS')) ? constant('DS') : DIRECTORY_SEPARATOR;
            // derive default layout pathname
            $pathname =
                self::$PATH_PREFIX               # widgets
                .$DS
                .$this->deriveLayoutPathPrefix() # namespace_widget\widget_name\
                .$DS
                .$this->getLayoutName();

            $this->layout = $pathname;
        }

        return $this->layout;
    }

    /**
     * Get default layout name appended to layout pathname
     *
     * note: you have to implement this method if you want various
     *       template output
     *
     * @return string
     */
    function getLayoutName()
    {
        return self::$LAYOUT_DEF_NAME;
    }

    /**
     * Set the View object
     *
     * @param  Renderer $view
     *
     * @return mixed
     */
    final function setView(Renderer $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the View object
     *
     * @return Renderer
     */
    final function getView()
    {
        return $this->view;
    }

    /**
     * Get layout path prefixed to module layout name
     *
     * note: WidgetNamespace\WidgetName
     *       inflected:
     *       widget_namespace\widget_name
     *
     * @return string
     */
    final function deriveLayoutPathPrefix()
    {
        $moduleNamespace = $this->deriveModuleNamespace();
        $path  = ($moduleNamespace) ? $moduleNamespace .'/' : '';
        $path .= $this->deriveWidgetName();

        // in some cases widget name contains \ from class namespace
        $path  = str_replace('\\', '/', $this->inflectName($path));

        return $path;
    }
}
