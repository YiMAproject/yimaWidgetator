//<!--
$(document).ready(function ()
{
    YimaWidgetLoader = function (widget, params, cssSelector, callback) {
        $(cssSelector).addClass('loading').html('Loading');

        $.ajaxq('widget', {
            url    : '{{url}}',
            type   : 'POST',
            data   : { widget: widget, params: params },
            success: function (response) {
                response = $.evalJSON(response);
                $(cssSelector).removeClass('loading').html(response.content);

                /*for (var i in response.links) {
                    if ($('head').find('link[href=\"' + response.links[i] + '\"]').length == 0) {
                       $('<link rel=\"stylesheet\" type=\"text/css\" href=\"' + response.links[i] + '\" />').appendTo('head');
                    }
                }*/

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

                // Run the callback
                if (callback) {
                    callback(response);
                }
            },
            error : function (response) {
                //response = $.evalJSON(response);
                //console.log(response);
                //response = $.evalJSON(response.responseText);
                $(cssSelector).removeClass('loading').html(response.responseText);
            }
        });
    };
});
//-->