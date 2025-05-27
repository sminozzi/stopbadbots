jQuery(document).ready(function () {


// Cria um elemento <style>
var styleElement = jQuery('<style>');

// Define as regras de estilo
var customCSS = '.stopbadbots-custom-loading-message { background: yellow; color: blue; font-size: 18px; padding:20px; height:100px !important; /* outros estilos desejados */ }';

// Adiciona as regras de estilo ao elemento <style>
styleElement.text(customCSS);

// Insere o elemento <style> no <head> do documento
jQuery('head').append(styleElement);




    var table199 = jQuery('#dataTableVisitorsSBB').DataTable({
        processing: true,
        "language": { 
            processing: '<span class="stopbadbots-custom-loading-message">Please wait...</span>',
            search: "Filter",
            /*
            columns: {
                // Aqui você pode definir os rótulos personalizados para cada coluna
                // Use a chave do índice da coluna (baseado em zero) e forneça o texto desejado
                0: "Page Visited"
              }
              */
        },
        "serverSide": true,
        "order": [[0, "desc"]],
        "columnDefs": [
            {
                "targets": 0, // -1
                "data": null,
                "defaultContent": "<button>Whitelist IP</button>"
            },
            {
                "targets": 1, // -1
                "data": null,
                "defaultContent": "<button>Blacklist IP</button>"
            },
            {
                "targets": 3,
                "createdCell": function (td, cellData, rowData, row, col) {
                    /* console.log(cellData); */
                    if (cellData == 'OK') {
                        jQuery(td).css("background-color", "#A9DFBF");
                    }
                    if (cellData == 'Denied') {
                        jQuery(td).css("background-color", "#F5B7B1");
                    }
                    if (cellData == 'Masked' ) {
                        jQuery(td).css("background-color", "#FFFF00");
                    }
                },
            },
            { targets: 2, title: 'Time' },
            { targets: 4, title: 'IP Address' },
            { targets: 5, title: 'Block Reason' },
            { targets: 9, title: 'Page Visited' },

        ],
        "ajax": {
            "url": datatablesajax.url + '?action=stopbadbots_get_ajax_data',
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Unexpected error. Please, try again later.");
            }
        },
        dataType: "json",
        contentType: "application/json",
    });

    jQuery('#reloadButton').on('click', function() {
        table199.ajax.reload(null, false);
      });



    // jQuery("#dataTableVisitorsSBB tbody").on('click', 'tr', function (event) {
        jQuery("#dataTableVisitorsSBB tbody").on('click', 'button', function (e) {
            if (jQuery(this)[0].tagName == "BUTTON") {
                var $row = table199.row(jQuery(this).closest('tr')); // .data();
                var rowIdx = table199.row(jQuery(this).closest('tr')).index();
                $ip = $row.cell(rowIdx, 4).data();

                var conteudo_button = jQuery(this).text(); // Obtém o texto dentro do botão

    
                var stopbadbots_nonce_value = jQuery('#stopbadbots_view_visits').val();
    
                if( conteudo_button == 'Blacklist IP'){
                    /* console.log('click black'); */
                    jQuery("#dialog-confirm-black").dialog({
                        resizable: false,
                        height: "auto",
                        width: 400,
                        modal: true,
                        buttons: [
                            {
                            id: "add-to-blacklist-btn",
                            text: "Add to Blacklist",
                            click: function() {
                                //var $ip = "example-ip"; // Defina o valor do IP aqui
                                jQuery.ajax({
                                url: ajaxurl,
                                type: "POST", 
                                data: {
                                    'action': 'stopbadbots_add_blacklist',
                                    'ip': $ip,
                                    'stopbadbots_nonce_table' :  stopbadbots_nonce_value
                                },
                                success: function(data) {
                                    alert('IP included on Blacklist Table ' + data);
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    alert('IP inclusion fail ' + textStatus);
                                }
                                });
                                jQuery(this).dialog("close");
                            }
                            },
                            {
                            text: "Cancel",
                            click: function() {
                                jQuery(this).dialog("close");
                            }
                            }
                        ]
                        });
                     if(jQuery("#modal-body-black2").length > 0) {
                        jQuery("#modal-body-black2").html('Available in the Pro version');
                        jQuery("#add-to-blacklist-btn").hide();
                     }
                     else{
                        jQuery("#modal-body-black").html('Add IP: ' + $ip + ' to Blacklist?');
                     }
                } // Blacklist
                else {


                    jQuery("#dialog-confirm").dialog({
                        resizable: false,
                        height: "auto",
                        width: 400,
                        modal: true,
                        buttons: [
                        {
                            id: "add-to-whitelist-btn",
                            text: "Add to Whitelist",
                            click: function() {   
                                // var $ip = "example-ip"; // Defina o valor do IP aqui                  
                                // console.log($ip);
                                jQuery.ajax({
                                    url: ajaxurl,
                                    type: "POST", 
                                    data: {
                                        'action': 'stopbadbots_add_whitelist',
                                        'ip': $ip,
                                        'stopbadbots_nonce_table' :  stopbadbots_nonce_value
                                    },
                                    success: function (data) {
                                        // var jsonData = JSON.parse(data);
                                        alert('IP included on Whitelist Table ' + data);
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        // console.log(errorThrown);
                                        alert('IP inclusion fail ' + textStatus);
                                        // alert('error'+errorThrown+' '+textStatus);
                                    }
                                });
                            jQuery(this).dialog("close");
                        }
                        },
                        {
                        text: "Cancel",
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                        }
                    ]
                    }); // confirm whitelist
                    if(jQuery("#modal-body2").length > 0) {
                        jQuery("#modal-body2").html('Available in the Pro version');
                        jQuery("#add-to-whitelist-btn").hide();
                     }
                     else{
                        jQuery("#modal-body").html('Add IP: ' + $ip + ' to Whitelist?');
                     }
                } // end whitelist
            } // clicked button?
            }); // clicked Button 
    // }); // clicked tr
}); // jQuery