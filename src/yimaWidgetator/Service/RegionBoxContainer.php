<?php
namespace yimaWidgetator\Service;

use yimaWidgetator\Interfaces\iRegionBoxContainer;
use yimaWidgetator\Widget\Interfaces\WidgetInterface;
use Zend\Stdlib\PriorityQueue;

class RegionBoxContainer implements iRegionBoxContainer
{
    protected $__widgets = [
        # 'region' => PriorityQueue
    ];

    /**
     * Add Widgets
     *
     * [
     #   'region_box' => [
     #     $priority => 'WidgetName',
     #     $priority => [
     #       'widget' => 'WidgetName'
     #       'params' => [
     #          'with_construct_param' => 'param_value'
     #       ]
     #     ],
     #     $priority => $WidgetInstance,
     # ],
     *
     * @param array $regWidgets
     *
     * @return $this
     */
    function addWidgets(array $regWidgets)
    {
        foreach($regWidgets as $region => $widgets) {
            if (!is_array($widgets))
                continue;

            foreach($widgets as $priority => $w)
                $this->addWidget($region, $w, $priority);
        }

        return $this;
    }

    /**
     * Add Widget To Container
     *
     * @param string $regionBox
     * @param string|array|WidgetInterface $widget
     * @param int $priority
     *
     * @throws \Exception Invalid Widget Provided
     * @return $this
     */
    function addWidget($regionBox, $widget, $priority = 0)
    {
        $this->__validateWidget($widget);

        $regionBox = (string) $regionBox;
        if (!array_key_exists($regionBox, $this->__widgets))
            $this->__widgets[$regionBox] = new PriorityQueue();

        /** @var PriorityQueue $queue */
        $queue = $this->__widgets[$regionBox];
        $queue->insert($widget, $priority);

        return $this;
    }

    protected function __validateWidget($widget)
    {
        if (is_array($widget)) {
            /*
             * [
             *    'widget' => 'Namespace\To\ClassName' // 'widget_name', 'existClassName'
             #    'params' => [
             #       'with_construct_param' => 'param_value'
             #     ]
             #  ]
             */
            if (!isset($widget['widget']) && !is_string($widget['widget']))
                throw new \Exception('Widget Array must contains "widget" => "WidgetName|Class" ');
        }

        if (is_object($widget) && !$widget instanceof WidgetInterface)
            throw new \Exception(sprintf(
                'Widget must instance of WidgetInterface, but "%s" given.'
                , get_class($widget)
            ));
    }

    /**
     * Get All Widgets In Region Box
     *
     * @param string $regionBox
     *
     * @return array
     */
    function getRegionWidgets($regionBox)
    {
        if (!array_key_exists($regionBox, $this->__widgets))
            return [];

        $return = [];

        /** @var PriorityQueue $queue */
        $queue = $this->__widgets[$regionBox];
        foreach($queue as $w)
            $return[] = $w;

        return $return;
    }

    /**
     * Get All Widgets
     *
     * @return array
     */
    function getWidgets()
    {
        $return = [];
        foreach (array_keys($this->__widgets) as $region)
            $return[$region] = $this->getRegionWidgets($region);

        return $return;
    }
}
