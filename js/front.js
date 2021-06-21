;
(function(jQuery) {
    "use strict";

    function getInappData() {
        jQuery.post({
            type: "POST",
            url: "/wp-json/module/cronberryIntegration/inapp",
            dataType: "json",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            success: function(data) {
                jQuery("#inappbody").html(data.quickview_html);
                var modal = document.getElementById("exampleModal");
                modal.style.display = "block";

                // Get the button that opens the modal
                var btn = document.getElementById("inappbutton");

                // Get the <span> element that closes the modal
                var span = document.getElementById("exampleModalClose");

                // When the user clicks on the button, open the modal
                btn.onclick = function() {
                    modal.style.display = "block";
                }

                // When the user clicks on <span> (x), close the modal
                span.onclick = function() {
                    modal.style.display = "none";
                }

                // When the user clicks anywhere outside of the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            },
            error: function() {
                console.log("error occured while sending token to cronberry.");
            }
        });
    }

    console.log('getting data for in app notification')
    jQuery('#inappbutton').click(function() {
        if (jQuery("#inappbody").html() == "") {
            getInappData();
        } else {
            var modal = document.getElementById("exampleModal");
            modal.style.display = "block";
        }
    });

})(jQuery);