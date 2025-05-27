jQuery(document).ready(function($) {
     // console.log('-------------fbrfa 24 ---------------');
    $deactivateSearch = $(".active");
    $deactivateSearch.on('click', function(evt) {
        billtempclass = evt.target.parentNode.className;
        // console.log(billtempclass);  // deactivate
        if (billtempclass != "deactivate") {
          return;
        }
        $deactivateLink = evt.target.href;
        if ($deactivateLink == '') {
            return;
        }
        // console.log($deactivateLink);
        //var prodclass = $("#prodclass").val();
        var url = new URL( $deactivateLink);
        // Obter os parâmetros da query string
        var params = new URLSearchParams(url.search);
        // Obter o valor do parâmetro 'plugin'
        var plugin = params.get('plugin');
        // Extrair o slug do plugin (parte antes do '/')
        var prodclass = plugin.split('/')[0];
        if (!$deactivateLink.includes(prodclass)) {
            // console.log('?');
            return; 
        }
        // console.log('!');
        evt.preventDefault($deactivateLink);
        $('#imagewaitfbl').hide();
        // console.log(prodclass);
        $billmodal = $('.' + prodclass + '-wrap-deactivate');
        $billmodal.prependTo($('#wpcontent')).slideDown();
        $('.' + prodclass + '-wrap-deactivate').prependTo($('#wpcontent')).slideDown();
        $('html, body').scrollTop(0);
        $('[class$="-wrap-deactivate"]').addClass('bill-minozzi-wrap-deactivate');
        // just deactivate
        $("." + prodclass + "-deactivate_lf").click(function() {
            $('#imagewaitfbl').show();
            if (!$(this).hasClass('disabled')) {
                $("." + prodclass + "-close-submit").addClass('disabled');
                $("." + prodclass + "-close-dialog").addClass('disabled');
                $("." + prodclass + "-deactivate").addClass('disabled');
                window.location.href = $deactivateLink;
            }
        });
        // cancell
        $("." + prodclass + "-close-dialog_lf").click(function(evt) {
            if (!$(this).hasClass('disabled')) {
                $('#imagewaitfbl').hide();
                // $billmodal = $('.' + prodclass + '-wrap-deactivate');
                $billmodal.slideUp();
            }
        });
  }); // end clicked Deactivated ...
}); // end jQuery 