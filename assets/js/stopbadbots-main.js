jQuery(document).ready(function($) {
    "use strict";

    //console.log('OK11!')

    $(document).on('click', '#stopbadbots_an2 .notice-dismiss', function( event ) {
        //alert('1');
        //console.log('OK112222!')

        jQuery.ajax({
            url: ajaxurl,
            data: {
                     action : 'stopbadbots_dismissible_notice2',
            },
            success: function (data) {
                // This outputs the result of the ajax request
                //console.log('OK');
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });



    });


});
