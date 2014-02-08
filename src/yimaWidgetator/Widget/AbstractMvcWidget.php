<?php
namespace yimaWidgetator\Widget;

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
class AbstractMvcWidget extends AbstractWidget
    implements
    MvcWidgetInterface
{
    const PATH_PREFIX = 'widgets';

    const LAYOUT_DEF_NAME = 'default';

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
     * extra feature that we need to achive desire output.
     *
     * @return string
     */
    public function render()
    {
        return $this->getView()->render($this->getLayout());
    }

    /**
     * Set the View object
     *
     * @param  Renderer $view
     *
     * @return mixed
     */
    final public function setView(Renderer $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the View object
     *
     * @return Renderer
     */
    final public function getView()
    {
        return $this->view;
    }

    /**
     * Set view script to render by view renderer
     *
     * @param string|ModelInterface $nameOrModel The script/resource process, or a view model
     *
     * @return mixed
     */
    final public function setLayout($nameOrModel)
    {
        $this->layout = $nameOrModel;
    }

    /**
     * Get view script layout
     *
     * @return string|ModelInterface
     */
    final public function getLayout()
    {
        if ($this->layout === null) {
            $DS = (defined('DS')) ? constant('DS') : DIRECTORY_SEPARATOR; // from yima
            // derive default layout pathname
            $pathname = self::PATH_PREFIX.$DS.$this->deriveLayoutPathPrefix().$DS.$this->getLayoutName();

            $this->layout = $pathname;
        }

        return $this->layout;
    }

    /**
     * Get default layout name appended to layout pathname
     *
     * note: you have to implement this method if you want varoius
     *       template output
     *
     * @return string
     */
    public function getLayoutName()
    {
        return self::LAYOUT_DEF_NAME;
    }

    /**
     * Get layout path prefixed to module layout name
     *
     * @return string
     */
    final public function deriveLayoutPathPrefix()
    {
        $DS   = (defined('DS')) ? constant('DS') : DIRECTORY_SEPARATOR; // from yima

        $path = ($this->deriveModuleNamespace())
            ? $this->deriveModuleNamespace().$DS
            : '';
        $path .= $this->deriveWidgetName();

        return $this->inflectName($path);
    }
}
