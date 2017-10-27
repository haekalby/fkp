/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

(function ($) {

    /**
     * 
     * @returns {undefined}
     */
    function initialize() {
        $('#configpress').on("blur", function(){
            $.ajax(aamLocal.ajaxurl, {
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'aam',
                    sub_action: 'ConfigPress.save',
                    _ajax_nonce: aamLocal.nonce,
                    config: $('#configpress').val()
                },
                error: function () {
                    aam.notification('danger', aam.__('Application error'));
                }
            });
        });
    }
    
    $(document).ready(function () {
        aam.addHook('init', function() {
            initialize();
        });
    });

})(jQuery);