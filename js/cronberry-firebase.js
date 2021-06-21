
;
(function (jQuery) {

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    var tokencr = '';

    if (window.sessionStorage.getItem('sentToServer') == undefined) {
        setCookie('sentToServer', '', 100);
    }
    messaging.getToken().then((currentToken) => {
        if (currentToken) {
            if (window.sessionStorage.getItem('sentToServer') != currentToken) {
                setCookie('sentToServer', currentToken, 100);
                window.sessionStorage.setItem('sentToServer', currentToken);
                sendTokenToServer(currentToken);
            }
        } else {
            console.log('No Instance ID token available. Request permission to generate one.');
        }
    }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
    });

    function setCookie(name, value, days) {
        var d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + d.toGMTString();
        document.cookie = name + "=" + value + "; expires=" + expires + "; path=/";
    }

    function getCookie(name) {
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        } else {
            begin += 2;
            var end = document.cookie.indexOf(";", begin);
            if (end == -1) {
                end = dc.length;
            }
        }
        // because unescape has been deprecated, replaced with decodeURI
        //return unescape(dc.substring(begin + prefix.length, end));
        return decodeURI(dc.substring(begin + prefix.length, end));
    }



    "use strict";

    function sendTokenToServer(currentToken) {
        setTokenSentToServer(currentToken);
    }

    function setTokenSentToServer(token) {
        console.log('seding');
        jQuery.post({
            type: "POST",
            url: "/wp-json/module/cronberryIntegration/firebase",
            dataType: "json",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            success: function (data) {
                console.log("token " + token + " sent to cronberry.");
            },
            error: function () {
                console.log("error occured while sending token to cronberry.");
            }
        });
    }

})(jQuery);