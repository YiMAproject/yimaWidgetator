//<!--
(function($){
    /**
     * Load Widget By Making An Ajax Call
     *
     * @param options
     * @returns {$.fn} jQuery DOM Element Object
     */
    $.fn.widgetator = function (options)
    {
        var settings    = $.fn.widgetator.settings;

        var defaults = {
               widget: '',            // widget name
               method: 'render',      // call method from widget object
               params: {},            // parameters passed to method
             callback: function(element, response){}   // callback after loading widget
        };

        if ((!options.method || options.method == 'render') && !options.callback) {
            // use default callback on render if not present
            defaults.callback = function(element, response) {
                element.html(response.content);

                for (var i in response.links) {
                    if ($('head').find('link[href=\"' + response.links[i] + '\"]').length == 0) {
                        $('<link rel=\"'+response.links[i].rel+'\" type=\"'+response.links[i].type+'\" href=\"' + response.links[i].href + '\" />').appendTo('head');
                    }
                }

                for (var i in response.scripts) {
                    if (response.scripts[i] != null) {
                        if (response.scripts[i].source) {
                            $('<script type="'+response.scripts[i].type+'">'+response.scripts[i].source+'</script>').appendTo('body');
                        } else if (response.scripts[i].attributes.src) {
                            // this is a file
                            if ($('body').find('script[src=\"'+response.scripts[i].attributes.src+'\"]').length == 0) {
                                if ($('head').find('script[src=\"'+response.scripts[i].attributes.src+'\"]').length == 0) {
                                    $('<script type="'+response.scripts[i].type+'" src="'+response.scripts[i].attributes.src+'"></script>').appendTo('body');
                                }
                            }
                        }
                    }
                }
            }
        }

        var exOptions = $.extend(false, defaults, options);

        // request widget ajax call ... {
        var $this = this;

        $this.addClass(settings.loading_class);
        $.ajaxq('widgetator', {
            url     : settings.provider_url,
            type    : 'POST',
            data    : { widget: exOptions.widget, method:exOptions.method, params: exOptions.params },
            success : function (response) {
                response = $.evalJSON(response);
                $this.removeClass(settings.loading_class);

                // Run the callback
                if (exOptions.callback) {
                    exOptions.callback($this, response);
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
        provider_url: '{{url}}',
        loading_class: 'widgetator-loading'
    }

})(jQuery);
//-->