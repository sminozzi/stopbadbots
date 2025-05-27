jQuery(document).ready(function($) {
    //      30/04/2024  11/06/24
    // console.log('bi');
    // Dismiss     
    jQuery('.bill-dismiss-one-hour').click(function(e) {
        e.preventDefault();
       const adminUrl = document.querySelector('.bill-dismiss-one-hour').getAttribute('data-admin-url');

       var main_slug = $('#main_slug').val();
       // console.log(main_slug);
    

       var currentUrl = window.location.href;
       var adminIndex = currentUrl.indexOf('/wp-admin/');


        if (adminIndex !== -1) {
            var dashboardUrl = currentUrl.substring(0, adminIndex + 10); // "/wp-admin/" tem 10 caracteres
        }
        else {
            var dashboardUrl  = '';
        }

        if(adminUrl !== null){
            if (adminUrl && adminUrl.includes("/undefined")) {
                adminUrl = adminUrl.replace("/undefined", "");
            }
        }
        var nonce = jQuery('#nonce').val();

        //console.log(nonce);
        //alert();

        jQuery.ajax({
                url: ajaxurl,
                type: 'POST', // Defina o tipo de solicitação como POST
                data: {
                    action: 'bill_dismiss_pre_checkup_handler',
                    nonce: nonce // Adicione o nonce aos dados que você está enviando
                },
            success: function(response) {
                $('.bill-installation-msg').remove();

                // console_log(response);
  
 
                const currentPageURL = window.location.href;
                if (currentPageURL.includes('page=bill_pre-checkup')) {

                    window.location.href = adminUrl; 
                }
                else {
                    window.location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao Finalizar:', error);
                console.error('Erro ao Finalizar:', status);
                console.error('Erro ao Finalizar:', xhr);
                alert('Error, please, try again later!');
            },
        });
    });
    // Finished
    jQuery('.bill-install-finished').click(function(e) {
        e.preventDefault();
        var adminUrl = $('#data-admin-url-finished').val();


        var nonce = jQuery('#nonce').val();
        var main_slug = jQuery('#main_slug').val();



        var currentUrl = window.location.href;
        var adminIndex = currentUrl.indexOf('/wp-admin/');



        if (adminIndex !== -1) {
            var dashboardUrl = currentUrl.substring(0, adminIndex + 10); // "/wp-admin/" tem 10 caracteres
        }
        else {
            var dashboardUrl  = '';
        }



        
        $.ajax({
            url: ajaxurl, // Substitua com a URL correta para sua ação AJAX
            type: 'POST', // Defina o tipo de solicitação como POST
            data: {
                action: 'bill_finished_pre_checkup_handler',
                nonce: nonce 
            },
            success: function(response) {
                alert('Thank you for installing!');




                const currentPageURL = window.location.href;
                if (currentPageURL.includes('page=bill_pre-checkup')) {
                    window.location.href = adminUrl; 
                }
                else {
                    window.location.reload();
                }




                window.location.href = adminUrl;




            },
            error: function(xhr, status, error) {
                console.error('Erro ao Finalizar:', error);
                console.error('Erro ao Finalizar:', status);
                console.error('Erro ao Finalizar:', xhr);
                alert('Error, please, try again later!');
            },
        });
    });       
    // Install wp Memory
    jQuery('.bill-install-plugin-now').click(function(e) {
        e.preventDefault();
        // Mostrar o botão de espera e esconder o botão "Install WPmemory Free"
        jQuery('#loading-spinner').show();
        jQuery('#bill-install-wpmemory').hide();
        var nonce = jQuery('#nonce').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bill_install_plugin2',
                slug: 'wp-memory',
                nonce: nonce
            },
            success: function(response) {
                var main_slug = jQuery('#main_slug').val();
                var slug = 'wp-memory';
                //sendSecondRequest(response,slug,main_slug);
                //console.log(response);
                if(response.trim() === 'OK') {
                    alert('Plugin WPmemory Installed Successfully.\nGo To Plugins page and activate it!');
                }
                 // create cookie...
                //var BILLCLASS = "ACTIVATED_" + slug;
                var BILLCLASS = "ACTIVATED_" + slug.toUpperCase();
                var d = new Date();
                var DayInSeconds = 24 * 60 * 60; // 10 dias * 24 horas * 60 minutos * 60 segundos
                d.setTime(d.getTime() + (DayInSeconds * 1000)); // Convertendo para milissegundos
                var expires = "expires="+d.toUTCString();
                document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";
            },
            error: function(xhr, status, error) {
                console.error('Erro ao instalar o plugin:', error);
                alert('An error occurred while installing the plugin. Please try again later.');
            },
            complete: function() {
                jQuery('#loading-spinner').prop('disabled', true);
                jQuery('#loading-spinner').text('Installed');
            }
        });
    });
    // install plugin wptools
    jQuery('.bill-install-wpt-plugin-now').click(function(e) {
        e.preventDefault();
        jQuery('#loading-spinner').show();
        jQuery('#bill-install-wptools').hide();
        var nonce = jQuery('#nonce').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bill_install_plugin2',
                slug: 'wptools',
                nonce: nonce
            },
            success: function(response) {
                // console.log(response);
                var main_slug = jQuery('#main_slug').val();
                var slug = 'wptools';
                //sendSecondRequest(response,slug,main_slug);
                if(response.trim() === 'OK') {
                    alert('Plugin WPtools Installed Successfully.\nGo To Plugins page and activate it! ');
                }
                // create cookie...
                //var BILLCLASS = "ACTIVATED_" + slug;
                var BILLCLASS = "ACTIVATED_" + slug.toUpperCase();
                var d = new Date();
                var DayInSeconds = 24 * 60 * 60; // 10 dias * 24 horas * 60 minutos * 60 segundos
                d.setTime(d.getTime() + (DayInSeconds * 1000)); // Convertendo para milissegundos
                var expires = "expires="+d.toUTCString();
                document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";
            },
            error: function(xhr, status, error) {
                console.error('Erro ao instalar o plugin:', error);
                alert('Ocorreu um erro ao instalar o plugin. Por favor, tente novamente mais tarde.');
            },
            complete: function() {
                jQuery('#loading-spinner').prop('disabled', true);
                jQuery('#loading-spinner').text('Installed');
            }
        });
    });
    // bill-install-now
    jQuery('.bill-install-now').click(function(e) {
        e.preventDefault();
        // bill-wrap-install
        jQuery('#bill-wrap-install-modal').show();
        jQuery('#loading-spinner').show();
        jQuery('.bill-install-now').hide();
        var main_slug = jQuery('#main_slug').val();
        var clickedButtonId = this.id;
        var slug = clickedButtonId.substring(1);
      $('#billimagewaitfbl').show();
       $billmodal = $('#bill-wrap-install');
       //console.log($billmodal);
       $billmodal.prependTo($('#wpcontent')).slideDown();
       $('html, body').scrollTop(0);
       $("#billpluginslugModal").html(slug);
       var nonce = jQuery('#nonce').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bill_install_plugin',
                slug: slug,
                nonce: nonce
            },
            success: function(response) {
                slug = clickedButtonId.substring(1);
                //sendSecondRequest(response,clickedButtonId, main_slug);
                //console.log(response);
                if(response.trim() === 'OK') {
                    alert('Plugin '+slug+ ' Installed Successfully.');
                    jQuery('.bill-install-now').show();
                }
                var BILLCLASS = "ACTIVATED_" + slug.toUpperCase();
                var d = new Date();
                var DayInSeconds = 24 * 60 * 60; // 10 dias * 24 horas * 60 minutos * 60 segundos
                d.setTime(d.getTime() + (DayInSeconds * 1000)); // Convertendo para milissegundos
                var expires = "expires="+d.toUTCString();
                document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";
            },
            error: function(xhr, status, error) {
                console.error('Erro ao instalar o plugin:', error);
                alert('An error occurred while installing the plugin. Please try again later.');
            },
            complete: function() {
                slug = clickedButtonId.substring(1);
                jQuery('#loading-spinner').prop('disabled', true);
                jQuery('#loading-spinner').text('Installed');
                alert('Plugin '+slug+ ' Installed Successfully.');
                window.location.reload();
            }
        });
    });
});
