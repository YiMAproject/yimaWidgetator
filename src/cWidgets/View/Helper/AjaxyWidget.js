$(document).ready(function () {

    'Candoo.Core.Widget.Loader'.namespace();

    /**
     * This class required plugins:
     * - ajaxq
     * - jquery.json
     */
    Candoo.Core.Widget.Loader = function () {
    };

    /**
     *
     * @return void
     */
    Candoo.Core.Widget.Loader.getAction = function (widget, action, containerId, params, callback) {
        $('#' + containerId).addClass('loading').html('Loading');

        $.ajaxq('widget', {
            url: '{{url}}',
            type: 'POST',
            data: { widget: widget, action: action, params: params },
            success: function (response) {
                response = $.evalJSON(response);
                $('#' + containerId).removeClass('loading').html(response.content);

                if (response.scripts != null ) {
                    $(response.scripts).prependTo('body');
                }

                if (response.links != null ) {
                    $(response.links).appendTo('head');
                }

                 /*
                 for (var i in response.css) {
                 if ($('head').find('link[href=\"' + response.css[i] + '\"]').length == 0) {
                 $('<link rel=\"stylesheet\" type=\"text/css\" href=\"' + response.css[i] + '\" />').appendTo('head');
                 }
                 }

                 if (response.javascript[i].script != null) {
                 $('
                 <script type=\"text/javascript\">' + response.javascript[i].script + '</scrip>').prependTo('body');
                 }
                 }
                 */

                // Run the callback
                if (callback) {
                    callback(response);
                }
            },
            error : function (response) {
                //response = $.evalJSON(response);
                //console.log(response);
                //response = $.evalJSON(response.responseText);
                $('#' + containerId).removeClass('loading').html(response.responseText);
            }

        });
    };

});