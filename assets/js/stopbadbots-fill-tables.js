jQuery(document).ready(function($) {
  "use strict";
  //console.log('OK Fill Tables!!!');
  jQuery('#stopbadbots_import-dialog').slideDown();
  jQuery.ajax({
    url: ajaxurl,
    type: 'post',
    data: {
             action: 'stopbadbots_import_tables_callback',
             security: StopBadBotsmyAjaxFill.nonce 
    },
    success: function (data) {
        var containsSuccess = data.includes("success");
        if (containsSuccess) {
          // console.log(data);
          stopbadbots_hideImportDialog();    
        }
        else {
          console.log(data);
          alert('Fail to Import tables');
        }
    },
    error: function (errorThrown) {
        console.log(errorThrown);
        alert('Fail to Import tables');
    }
})
function stopbadbots_hideImportDialog() {
  jQuery('#stopbadbots_import-dialog').slideUp();
}
});
