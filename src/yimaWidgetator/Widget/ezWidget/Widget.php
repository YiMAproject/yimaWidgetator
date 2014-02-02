<?php
namespace yimaWidgetator\Widget\ezWidget;

use yimaWidgetator\AbstractWidget;

class Widget extends AbstractWidget
{
	public function callMethodAction()
	{
		return array(
			'viewParam'	=> 'Parameter Value for use in view'
		);
	}
}
