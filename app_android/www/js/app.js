/*
 * Please see the included README.md file for license terms and conditions.
 */
// This file is a suggested starting place for your code.
// It is completely optional and not required.
// Note the reference that includes it in the index.html file.
/*jslint browser:true, devel:true, white:true, vars:true */
/*global $:false, intel:false app:false, dev:false, cordova:false */
// This file contains your event handlers, the center of your application.
// NOTE: see app.initEvents() in init-app.js for event handler initialization code.
// function myEventHandler() {
//     "use strict" ;
// // ...event handler code here...
// }
// ...additional event handlers here...
 var rootWebService = "https://laili-reparasi.000webhostapp.com/webservice";
//var rootWebService = "http://localhost/laili_reparasi/webservice";
//var rootWebService = "http://192.168.127.1/laili_reparasi/webservice";//aku
// var rootWebService = "http://192.168.113.1/laili_reparasi/webservice";//laili



//======================onesignal============================
//https://documentation.onesignal.com/docs/intel-xdk-setup
// Add to index.js or the first page that loads with your app.
// For Intel XDK and please add this to your app.js.
document.addEventListener('deviceready', function () {
    window.plugins.OneSignal
     .startInit("2295b7de-4c54-445c-91fc-a1b910d4734f")
    .handleNotificationOpened(function (jsonData) {
        console.log('Did I receive a notification: ' + JSON.stringify(jsonData));
    })
    .endInit();

    window.plugins.OneSignal
        .getIds(function (ids) {
            window.localStorage.setItem("player_id", ids.userId);
            window.localStorage.setItem("token_id", ids.pushToken);
        });

    // cordova.plugins.notification.local.schedule({
    //     title: 'My first notification',
    //     text: 'Thats pretty easy...',
    //     foreground: true
    // });

    /*
    console.log("Device Ready");    
    var push = PushNotification.init({ "android": {"senderID": "MY SENDER ID"},"ios": {"alert": "true", "badge": "true", "sound": "true"}, "windows": {} } );

    push.on('registration', function(data) {    
        console.log(data.registrationId);    
        // $("#gcm_id").val(data.registrationId);
        window.localStorage.setItem("player_id", data.registrationId);
    });    

    push.on('notification', function(data) {    
        console.log(data.message);    
        // alert(data.title+" Message: " +data.message);    
        // data.title,    
        // data.count,    
        // data.sound,    
        // data.image,    
        // data.additionalData    
    });   

    push.on('error', function(e) {
        console.log(e.message);    
    });
    */ 

}, false);


//===================/onesignal=================================
function cekLogin_index() {
    if (localStorage.getItem('status_login') !== null) {
        if (localStorage.getItem('user_level') === 'pelanggan') {
            window.location = 'menu_pelanggan.html';
        } else if (localStorage.getItem('user_level') === 'penjual') {
            window.location = 'menu_penjual.html';
        } else {
            window.location = 'index.html';
        }
    }
}

function cekLogin() {
    if (localStorage.getItem('status_login') === null) {
        window.location = 'index.html';
    }
}

function signout() {
    localStorage.clear();
    window.location = "index.html";
}
/*jshint browser:true */
/*global $ */
(function() {
    "use strict";
    /*
      hook up event handlers
    */
    function register_event_handlers() {
        /* button  #back */
        $(document).on("click", "#back", function(evt) {
            /* your code goes here */
            window.history.back();
        });
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