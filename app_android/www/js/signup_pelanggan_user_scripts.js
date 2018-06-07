/*jshint browser:true */
/*global $ */(function()
{
 "use strict";
 /*
   hook up event handlers 
 */

 function isEmpty(str) {
   	return (!str || 0 === str.length);
 }

 function isBlank(str) {
    return (!str || /^\s*$/.test(str));
 }

 function register_event_handlers()
 {  

     /* button  Daftar */
    $(document).on("click", ".uib_w_5", function(evt)
    {
        /* your code goes here */ 

        var txtEmail = $('#email').val();
        var txtNama	 = $('#nama').val();	
        var txtTelp  = $('#telp').val();
        var pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
        
        $('#alert_div').hide();
        $('#loading_img').hide();

        if(!pattern.test(txtEmail)){
        	$('#danger_label').html('Email tidak valid')
	        $('#alert_div').show();
        }else if( isEmpty(txtNama) || isEmpty(txtTelp) ){
        	$('#danger_label').html('Nama atau Telp tidak boleh kosong!')
	        $('#alert_div').show();
        }else{
			$('#loading_img').show();

        	$.post(rootWebService + "/signup", {
	            email			: txtEmail,
	            nama	        : txtNama,
	            telp 			: txtTelp,
                jenis           : 'pelanggan'
	        }, 'json')
	        .done(function (data) {

	            if (data.status === 'email_udah_ada') {
	            	$('#danger_label').html('email ini sudah terdaftar!')
	                $('#alert_div').show();
	                $('#loading_img').hide();

	            } else if(data.status === 'berhasil') {
	                $('#success_div').show();
	                $('#loading_img').hide();
	            }

	        });	
        };

        
         return false;
    });
    
        /* button  .uib_w_6 */
    $(document).on("click", ".uib_w_6", function(evt)
    {
        /* your code goes here */ 
        window.location = "daftar_option.html";
         return false;
    });
    
    }
 document.addEventListener("app.Ready", register_event_handlers, false);
})();
