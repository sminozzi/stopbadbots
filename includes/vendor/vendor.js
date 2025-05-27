/*
 * @ Author: Bill Minozzi
 * @ Copyright: 2021 www.BillMinozzi.com
 * @ Modified time: 2021-29-11 09:17:42
 * */

jQuery(document).ready(function ($) {
    

    // $(".spinner").addClass("is-active");
    $(".spinner").hide();

/*
   // var ah_dismiss = stopbadbots_getCookie("sbb_dismiss");

   if (sbb_dismiss !== undefined){ 
    //  console.log("Found cookie " + sbb_dismiss);
    }
*/   
    

    // console.log('vendor-sbb');


    $("#stopbadbots-vendor-ok").click();
    $("#TB_title").hide();

    if (!$("#TB_window").is(':visible')) {
        $("#stopbadbots-vendor-ok").click();
        // console.log('auto click');
    }


    $("*").click(function (ev) {


      //  ev.preventDefault();

        //  alert('2');
        // console.log('click');
        // console.log(ev.target.id);
         //$(this).attr("class");
        // console.log($(this).attr("class"));





        if (ev.target.id == "bill-vendor-button-ok-sbb") {
         //    console.log("Learn More");
            window.location.replace("http://stopbadbots.com/premium/");
        }


        if (ev.target.id == "bill-vendor-button-again-sbb") {
           //  console.log("watch again");
           // $("#bill-banner-sbb").get(0).play();
            $("#bill-banner-sbb").get(0).play().catch(function () {
                // console.log("Fail to Play.");
                self.parent.tb_remove();
                $('#TB_window').fadeOut();
                $("#TB_closeWindowButton").click();
            });

        }

        if ( ev.target.id == "bill-vendor-button-dismiss-sbb" || $(this).attr("class") == "tb-close-icon"  ) {
            // event.preventDefault()
             $("#bill-banner-sbb").hide();
            /*  $("#bill-banner-sbb").html("Please, wait...") */
             
             $("#stopbadbots-wait").show();
             $("#stopbadbots-wait").addClass("is-active");

             console.log('clicked Dimiss !!!!!!');
             stopbadbots_setCookie('sbb_dismiss', '1', '1');

             $("#bill-vendor-button-dismiss-sbb").hide();
             $("#bill-vendor-button-again-sbb").hide();
             $("#bill-vendor-button-ok-sbb").hide();

             $(".spinner").addClass("is-active");
             $(".spinner").show();
            jQuery.ajax({
                method: 'post',
                url: ajaxurl,
                data: {
                    action: "stopbadbots_go_pro_hide2"
                },
                success: function (data) {
                    console.log('OK-dismissed!!!');
                    setTimeout(myFunction, 3000);
                    // return data;
                    
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('error' + errorThrown + ' ' + textStatus);
                }
            });
            //console.log("fechar");

            // setTimeout(myFunction, 3000);
            function myFunction() {
                self.parent.tb_remove();
                $('#TB_window').fadeOut();
                $("#TB_closeWindowButton").click();
            }

        }

    }); // click


    if ($('#bill-banner-sbb').length) {
        //  $("#bill-banner-sbb").get(0).play();
        $("#bill-banner-sbb").get(0).play().catch(function () {
            // console.log("Fail to Play.");
            self.parent.tb_remove();
            $('#TB_window').fadeOut();
            $("#TB_closeWindowButton").click();
        });
    }

    var altura = $("#TB_window").height();


    $("#TB_window").height(260);

    /*var altura = $("#TB_window").height();
    console.log(altura);
    */

    /* $("#TB_window").width(550); */
    $("#TB_window").addClass("bill_TB_window");

/*
    setTimeout(loadAfterTime, 5000)



    function loadAfterTime() { 
    // code you need to execute goes here. 
       $("#TB_window").css({
        height: "320px !important"
      });


    var altura2 = $("#TB_window").height();

       console.log(altura2);
       console.log('Hi2');
    }
*/

function stopbadbots_setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }
  
  function stopbadbots_getCookie(cookieName) {
    let cookie = {};
    document.cookie.split(';').forEach(function(el) {
      let [key,value] = el.split('=');
      cookie[key.trim()] = value;
    })
    return cookie[cookieName];
  }

});