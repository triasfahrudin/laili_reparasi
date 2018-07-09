/*jshint browser:true */
/*global $ */
(function() {
    "use strict";
    /*
      hook up event handlers 
    */
    function register_event_handlers() {
        /* button  .uib_w_2 */
        $(document).on("click", ".uib_w_2", function(evt) {
            /* your code goes here */
            //menu order pending
            // return false;
            window.location = "penjual_order_pending.html";
        });
        /* button  .uib_w_3 */
        $(document).on("click", ".uib_w_3", function(evt) {
            /* your code goes here */
            //order dalam proses
            // return false;
            window.location = "penjual_order_proses.html";
        });
        /* button  .uib_w_4 */
        $(document).on("click", ".uib_w_4", function(evt) {
            /* your code goes here */
            //order selesai
            // return false;
            window.location = "penjual_order_selesai.html";
        });
        /* button  .uib_w_6 */
        $(document).on("click", ".uib_w_6", function(evt) {
            /* your code goes here */
            //dompetku
            window.location = "dompetku.html";
            // return false;
        });
        /* button  .uib_w_6 */
        $(document).on("click", ".uib_w_5", function(evt) {
            /* your code goes here */
            //profile
            window.location = "profile_penjual.html";
            // return false;
        });
    }
    document.addEventListener("app.Ready", register_event_handlers, false);
})();