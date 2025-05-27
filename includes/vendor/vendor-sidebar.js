/*
 * @ Author: Bill Minozzi
 * @ Copyright: 2021 www.BillMinozzi.com
 * @ Modified time: 2021-29-11 09:17:42
 * */
jQuery(document).ready(function ($) {
    // console.log('vendor-wpt-sidebar');
    $("#bill-vendor-button-ok-wpm").click(function () {
        // console.log("Learn More");
        window.location.replace("http://wptoolsplugin.com/premium//");
    });
    if ($('#bill-banner-2').length) {
        $("#bill-banner-2").get(0).play();
    }
});