Widgetator Layout Module
==============

*this module is part of Yima Application Framework*

See it in action
------------

#### What is widget

Widget is a class that implement minimal interface ```WidgetInterface```

```php
interface WidgetInterface
{
    /**
     * Render widget as string output
     *
     * @return string
     */
    public function render();
}
```
#### Widget plugin manager

Widgets stored in widget plugin manager (serviceLocator for widgets).
stored with serviceManager key ```yimaWidgetator.WidgetManager``` and build as factory service.

#### Define widget(s) from merged config

```php
return array(
    /**
     * Register Widgets in WidgetManager
     *
     * each widget must instance of WidgetInterface
     */
    'yima_widgetator' => array(
         // This is configurable service manager config
		'invokables' => array(
			# 'widgetName' => 'Widget\Class',
		),
	),
```

#### How to Get Widgets From ?

In Controller with controller helper:

```php
$this->layout()->side_bar = $this->widget('widgetName')->render();

```

In View Script:

```html
<div class="container">
    <p><?php echo $this->widget('widgetName')->render();?></p>
</div>

```

#### Load Widgets With Ajax Call

ajax widget loading need some js resources you can find in ```www``` folder of module,
you can put file anywhere you want and edit ```config\module.config.php```.


```php
return array(
    /**
     * Libraries that used in Ajax Loading of widgets.
     * @see \yimaJquery\View\Helper\WidgetAjaxy
     */
    'static_uri_helper' => array(
        'Yima.Widgetator.JS.Jquery.Ajaxq' => '$basepath/js/yima-widgetator/jquery.ajaxq.min.js',
        'Yima.Widgetator.JS.Jquery.Json'  => '$basepath/js/yima-widgetator/jquery.json.min.js',
    ),
```
for more info around ```static_uri_helper``` see [yimaStaticUriHelper](https://github.com/RayaMedia/yimaStaticUriHelper)

In View Script:

```html
<div class="container">
    <p id="container_id">
        <?php
        echo $this->widgetAjaxy('widgetName',   // widget name
            array('option' => 'value'),         // options
            'container_id',                     // id of dom element
            'function callback(response)'       // callback after loading widget
            );
        ?>
    </p>
</div>

```

*I`m working to improve ajax loading of widgets.*


#### And Finally

We have some Abstract class for widgets called: AbstractWidget and AbstractMvcWidget. (take a look)


Todo
-----------

Example Widgets and AbstractClasses will be added.


Installation
-----------

#### Requirement

* [yimaStaticUriHelper](https://github.com/RayaMedia/yimaStaticUriHelper)

* [yimaJquery](https://github.com/RayaMedia/yimaJquery)


Composer installation:

require ```rayamedia/yima-widgetator``` in your ```composer.json```

Or clone to modules folder

Enable module in application config


## Support ##
To report bugs or request features, please visit the [Issue Tracker](https://github.com/RayaMedia/yimaWidgetator/issues).

*Please feel free to contribute with new issues, requests and code fixes or new features.*
