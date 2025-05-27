jQuery(document).ready(function ($) {
   // console.log('More...');
   //hhhh9();
  // install now
  $('#billimagewaitfbl').hide();
  jQuery('.bill-install-now-24').click(function(e) {
    e.preventDefault();
    jQuery('#bill-wrap-install-modal').show();
    jQuery('#loading-spinner').show();
    jQuery('.bill-install-now').hide();
    var main_slug = jQuery('#main_slug').val();
    // alert(main_slug);
    var clickedButtonId = this.id;
    var slug = clickedButtonId.substring(1);
    $('#billimagewaitfbl').show();
    $billmodal = $('#bill-wrap-install');
    $billmodal.prependTo($('#wpcontent')).slideDown();
    $('html, body').scrollTop(0);
    $("#billpluginslugModal").html(slug);
    var nonce = jQuery('#nonce_install').val();
    // console.log(nonce);
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'bill_install_plugin2',
            slug: slug,
            nonce: nonce
        },
        success: function(response) {
            // remove underline...
            slug = clickedButtonId.substring(1);
                var BILLCLASS = "ACTIVATED_" + slug.toUpperCase();
                var d = new Date();
                var DayInSeconds = 24 * 60 * 60; // 10 dias * 24 horas * 60 minutos * 60 segundos
                d.setTime(d.getTime() + (DayInSeconds * 1000)); // Convertendo para milissegundos
                var expires = "expires="+d.toUTCString();
                document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";
            if(response.trim() === 'OK') {
              // alert('Plugin '+slug+ ' Installed Successfully!!!');
              $('body').showToast('Plugin '+slug+ ' Installed Successfully! <br> Go To Plugins page and activate it!', 8000, 'ok');
              jQuery('.bill-install-now').show();
              jQuery('#loading-spinner').hide();
              $('#billimagewaitfbl').hide();
            }
           // window.location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Erro ao instalar o plugin:', error);
            alert('Error, please, try again later!');
        },
        complete: function() {
            slug = clickedButtonId.substring(1);
            jQuery('#loading-spinner').prop('disabled', true);
            jQuery('#loading-spinner').text('Installed');
            jQuery('#loading-spinner').hide();
            //alert('Plugin '+slug+ ' Installed Successfully!');
            setTimeout(function() {
              window.location.reload();
            }, 4000);
        }
    });
});
/*
    // console.log({id});
    //alert();
    if (id != "database-backup" &&  id != "bigdump-restore" &&  id != "easy-update-urls" &&  id != "s3cloud" &&  id != "toolsfors3" && id != "antihacker" && id != "toolstruthsocial" && id != "stopbadbots" && id != "wptools" && id != "recaptcha-for-all" && id != "wp-memory") {
      Return;
    }
    alert_msg = 'Plugin Installed Successively!\nGo to ';
    switch (id) {
      case "database-backup":
        alert_msg = alert_msg + "Dashboard => Menu => Tools => Database-Backup";
        break;
      case "bigdump-restore":
        alert_msg = alert_msg + "Dashboard => Menu => Tools => Bigdump Restore";
        break;
      case "easy-update-urls":
        alert_msg = alert_msg + "Dashboard => Menu => Tools => Easy Update Urls";
        break;
      case "s3cloud":
          alert_msg = alert_msg + "Dashboard => Menu => Tools => S3 Cloud";
          break;  
      case "toolsfors3":
          alert_msg = alert_msg + "Dashboard => Menu => Tools => Tools For S3";
          break;  
      case "wp-memory":
        alert_msg = alert_msg + "Dashboard => Menu => Tools => WP Memory";
        break;
      case "antihacker":
        alert_msg = alert_msg + "Dashboard => Anti Hacker";
        break;
      case "stopbadbots":
        alert_msg = alert_msg + "Dashboard => Stop Bad Bots";
        break;
      case "wptools":
        alert_msg = alert_msg + "Dashboard => WP Tools";
        break;
      case "recaptcha-for-all":
        alert_msg = alert_msg + "Dashboard => Tools => reCAPTCHA For All";
        break
      default:
        alert_msg = alert_msg + "Dashboard => Menu";
        break;
    }
*/  
});  // end jQuery  