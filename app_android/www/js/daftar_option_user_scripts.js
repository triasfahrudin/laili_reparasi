/*jshint browser:true */
/*global $ */
(function() {
    "use strict";
    /*
      hook up event handlers 
    */
    function register_event_handlers() {
        /* button  .uib_w_3 */
        $(document).on("click", ".uib_w_3", function(evt) {
            /* your code goes here */
            window.location = "signup_pelanggan.html";
            return false;
        });
        /* button  .uib_w_4 */
        $(document).on("click", ".uib_w_4", function(evt) {
            /* your code goes here */
            window.location = "signup_penjual.html";
            return false;
        });
        $(document).on("click", ".uib_w_6", function(evt) {
            /* your code goes here */
            window.location = "index.html";
            return false;
        });
    }
    document.addEventListener("app.Ready", register_event_handlers, false);
})();