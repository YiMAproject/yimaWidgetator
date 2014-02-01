<?php
namespace yimaWidgetator\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    	//echo $locale->date()->diff(time() - (20 * 60 * 60));

        $ezWidget = $this->widget('ezWidget');
        $ezWidget = $ezWidget->callMethod();

        $ezBorder = $this->widgetLoader()->get('ezBorder');
        $ezBorder->addNested($ezWidget);
        $ezBorder = $ezBorder->border();

        $this->layout()->addChild($ezBorder,'widgets');

    	// Get the "layout" view model and inject a sidebar
    	/*
    	$layout = $this->layout();
    	$sidebarView = new ViewModel();
    	$sidebarView->setTemplate('content/sidebar');
    	$layout->addChild($sidebarView, 'sidebar');
    	*/

    	// changing template, also can be used on viewScripts
    	//$this->layout('404');
    	// or
    	//$layout = $this->layout();
    	//$layout->setTemplate('article/layout');
    }
}