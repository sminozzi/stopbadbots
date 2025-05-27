jQuery(document).ready(function($) {
    "use strict";


        var forms = "#commentform";
        forms += ", .wpforms-form";
        // forms += ", #registerform";
        $(forms).on("submit", function() {
            $("<input>").attr("type", "text").attr("name", "stopbadbots_key").attr("value", '1').appendTo(forms);
            return true;
        });
        $(".wpcf7-submit").click(function() {
            $("<input>").attr("type", "hidden").attr("name", "stopbadbots_key").attr("value", '1').appendTo(".wpcf7-form");
        });
        $(".wpforms-form").click(function() {
            $("<input>").attr("type", "hidden").attr("name", "stopbadbots_wpforms").attr("value", '1').appendTo(".wpforms-form");
        });
});