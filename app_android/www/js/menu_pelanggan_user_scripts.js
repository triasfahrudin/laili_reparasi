/*jshint browser:true */
/*global $ */
(function() {
    "use strict";
    /*
      hook up event handlers 
    */
    function register_event_handlers() {
        /* button  #btn_lokasi_terdekat */
        $(document).on("click", "#btn_lokasi_terdekat", function(evt) {
            /* your code goes here */
            window.location = 'lokasi_terdekat.html';
        });
        /* button  .uib_w_3 */
        $(document).on("click", ".uib_w_3", function(evt) {
            /* your code goes here */
            window.location = 'kategori_reparasi.html';
        });
        /* button  .uib_w_4 */
        $(document).on("click", ".uib_w_4", function(evt) {
            /* your code goes here */
//            return false;
            window.location = "pelanggan_transaksi.html";
        });
        /* button  PENGATURAN */
    $(document).on("click", ".uib_w_7", function(evt)
    {
        /* your code goes here */ 
         return false;
    });
    
        /* button  PROFIL */
    $(document).on("click", ".uib_w_9", function(evt)
    {
        /* your code goes here */ 
         return false;
    });
    
        /* button  .uib_w_10 */
    $(document).on("click", ".uib_w_10", function(evt)
    {
        /* your code goes here */ 
         window.location = "profile_pelanggan.html";
//         return false;
    });
    
    }
    document.addEventListener("app.Ready", register_event_handlers, false);
})();