<?php
// Set the content type to Javascript
header("Content-type: text/javascript");

// Disallow caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>

// <script type="text/javascript">
(function($){
    /**
     * Load Widget By Making An Ajax Call
     *
     * @param options
     * @param callback Callback after loading widget
     *
     * @returns {$.fn} jQuery DOM Element Object
     */
    $.fn.widgetator = function (options, callback)
    {
        var settings    = $.fn.widgetator.settings;

        var defaults = {
               widget: '',            // widget name
               method: 'render',      // call method from widget object
               params: {}            // parameters passed to method
        };

        if ((!options.method || options.method == 'render') && !callback) {
            // use default callback on render if not present
            callback = $.fn.widgetator.defaultCallback;
        }

        var exOptions = $.extend(false, defaults, options);

        // request widget ajax call ... {
        var $this = this;

        $this.addClass(settings.loading_class);
        $.ajaxq('widgetator', {
            url     : settings.provider_url,
            type    : 'POST',
            data    : exOptions,
            success : function (response) {
                response = $.evalJSON(response);
                $this.removeClass(settings.loading_class);

                if (callback) {
                    // Run the callback
                    callback($this, response);
                }
            },
            error   : function (response) {
                $this.removeClass(settings.loading_class).html(response.responseText);
            }
        });
        // ... }

        return this;
    }

    $.fn.widgetator.settings = {
        provider_url: '<?php echo $this->sm->get('ViewHelperManager')->get('url')->__invoke('yimaWidgetator_restLoadWidget'); ?>',
        loading_class: 'widgetator-loading'
    };

    $.fn.widgetator.defaultCallback = function (element, response) {
        element.html(response.result.content);

        for (var i in response.result.links) {
            if ($('head').find('link[href=\"' + response.result.links[i] + '\"]').length == 0) {
                $('<link rel=\"'+response.result.links[i].rel+'\" type=\"'+response.result.links[i].type+'\" href=\"' + response.result.links[i].href + '\" />').appendTo('head');
            }
        }

        for (var i in response.result.scripts) {
            if (response.result.scripts[i] != null) {
                if (response.result.scripts[i].source) {
                    $('<script type="'+response.result.scripts[i].type+'">'+response.result.scripts[i].source+'</script>').appendTo('body');
                } else if (response.result.scripts[i].attributes.src) {
                    // this is a file
                    if ($('body').find('script[src=\"'+response.result.scripts[i].attributes.src+'\"]').length == 0) {
                        if ($('head').find('script[src=\"'+response.result.scripts[i].attributes.src+'\"]').length == 0) {
                            $('<script type="'+response.result.scripts[i].type+'" src="'+response.result.scripts[i].attributes.src+'"></script>').appendTo('body');
                        }
                    }
                }
            }
        }
    }
})(jQuery);
// </script>