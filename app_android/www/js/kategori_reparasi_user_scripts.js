/*jshint browser:true */
/*global $ */
(function() {
    "use strict";
    /*
      hook up event handlers 
    */
    function register_event_handlers() {
        /* button  #btn_home */
        $(document).on("click", "#btn_home", function(evt) {
            /* your code goes here */
            if (localStorage.getItem('user_level') === 'pelanggan') {
                window.location = 'menu_pelanggan.html';
            } else {
                window.location = 'menu_penjual.html';
            }
            return false;
        });
    }
    document.addEventListener("app.Ready", register_event_handlers, false);
})();