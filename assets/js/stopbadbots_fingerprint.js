jQuery(document).ready(function ($) {
    console.log('Fingerprint works !!!');

    var getTouchSupport = function () {
        var maxTouchPoints = 0;
        var touchEvent;
        if (typeof navigator.maxTouchPoints !== 'undefined') {
            maxTouchPoints = navigator.maxTouchPoints;
        } else if (typeof navigator.msMaxTouchPoints !== 'undefined') {
            maxTouchPoints = navigator.msMaxTouchPoints;
        }
        try {
            document.createEvent('TouchEvent');
            touchEvent = true;
        } catch (_) {
            touchEvent = false;
        }
        var touchStart = 'ontouchstart' in window;
        return [maxTouchPoints, touchEvent, touchStart];
    };

    var touchSupportKey = getTouchSupport();

    var userAgent = navigator.userAgent.toLowerCase();
    var platform = navigator.platform.toLowerCase();
    var os;
    if (userAgent.indexOf('windows phone') >= 0) {
        os = 'Windows Phone';
    } else if (userAgent.indexOf('windows') >= 0 || userAgent.indexOf('win16') >= 0 || userAgent.indexOf('win32') >= 0 || userAgent.indexOf('win64') >= 0 || userAgent.indexOf('win95') >= 0 || userAgent.indexOf('win98') >= 0 || userAgent.indexOf('winnt') >= 0 || userAgent.indexOf('wow64') >= 0) {
        os = 'Windows';
    } else if (userAgent.indexOf('android') >= 0) {
        os = 'Android';
    } else if (userAgent.indexOf('linux') >= 0 || userAgent.indexOf('cros') >= 0 || userAgent.indexOf('x11') >= 0) {
        os = 'Linux';
    } else if (userAgent.indexOf('iphone') >= 0 || userAgent.indexOf('ipad') >= 0 || userAgent.indexOf('ipod') >= 0 || userAgent.indexOf('crios') >= 0 || userAgent.indexOf('fxios') >= 0) {
        os = 'iOS';
    } else if (userAgent.indexOf('macintosh') >= 0 || userAgent.indexOf('mac_powerpc') >= 0) {
        os = 'Mac';
    } else {
        os = 'Other';
    }

    var x, t;
    try {
        if (window.Intl && window.Intl.DateTimeFormat) {
            x = new window.Intl.DateTimeFormat().resolvedOptions().timeZone;
        } else {
            x = 'undef';
        }
    } catch (err) {
        x = 'error';
    }
    try {
        var d = new Date();
        t = d.getTimezoneOffset();
    } catch (err) {
        t = 'error';
    }

    var javaEnabled = '0';
    var cookieEnabled = '0';

    try {
        if (navigator && navigator.userAgent) {
            // Verificar se o JavaScript está habilitado
            if (typeof window !== "undefined" && typeof window.navigator !== "undefined" && typeof window.navigator.userAgent !== "undefined") {
                // JavaScript está habilitado.
                javaEnabled = '1';
            }

            // Verificar se os cookies estão habilitados
            if (navigator.cookieEnabled) {
                // Cookies estão habilitados.
                cookieEnabled = '1';
            }
        }
    } catch (err) {
        // Handle errors silently
    }

    // Trimming the variables
    x = $.trim(x);
    t = $.trim(t.toString());
    var p = $.trim(platform);
    var o = $.trim(os);
    var ts = $.trim(touchSupportKey.toString());
    var u = $.trim(javaEnabled);
    var v = $.trim(cookieEnabled);

    var $fingerprint = '';
    if (x.length != 0) {
        $fingerprint = $fingerprint + '#' + x;
    }
    if (t.length != 0) {
        $fingerprint = $fingerprint + '#' + t;
    }
    if (p.length != 0) {
        $fingerprint = $fingerprint + '#' + p;
    }
    if (o.length != 0) {
        $fingerprint = $fingerprint + '#' + o;
    }
    if (ts.length != 0) {
        $fingerprint = $fingerprint + '#' + ts;
    }
    $fingerprint = $fingerprint + '#' + u;
    $fingerprint = $fingerprint + '#' + v;

    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'stopbadbots_grava_fingerprint',
            'fingerprint': $fingerprint
        },
        success: function (data) {
            // This outputs the result of the ajax request
            // console.log('2 '+$wsize);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });

    eraseCookie('stopbadbots_cookie');
    if (readCookie('stopbadbots_cookie') == null) {
        createCookie('stopbadbots_cookie', $fingerprint);
    }

    function createCookie(name, value, days) {
        var expires;
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        } else {
            expires = "";
        }
        document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
        console.log('Cookie created!');
    }

    function readCookie(name) {
        var nameEQ = escape(name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
        }
        return null;
    }

    function eraseCookie(name) {
        createCookie(name, "", -1);
    }
});
