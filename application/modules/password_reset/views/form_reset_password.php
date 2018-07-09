<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Password Reset</title>
      <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
      <style>.form-gap {
         padding-top: 70px;
         }
      </style>
      <script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
      <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
      <script type="text/javascript"></script>
   </head>
   <body style="">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
      <div class="form-gap"></div>
      <div class="container">
         <div class="row">
            <div class="col-md-4 col-md-offset-4">
               <div class="panel panel-default">
                  <div class="panel-body">
                     <div class="text-center">
                        <h3><i class="fa fa-lock fa-4x"></i></h3>
                        <h2 class="text-center">Ubah Password Anda</h2>
                        <p>Masukkan password anda yang baru</p>
                        <div class="panel-body">
                           <form id="register-form" role="form" autocomplete="off" class="form" method="post" name="form-reset">
                             <div class="form-group">
                                <!-- <label for="new_pass">Password baru</label> -->
                                <input type="password" class="form-control" id="new_pass" name="new_pass" placeholder="Masukkan Password baru" required>
                             </div>
                             <div class="form-group">
                                <!-- <label for="repeat_pass">Password</label> -->
                                <input type="password" class="form-control" id="repeat_pass" name="repeat_pass" placeholder="Ulangi Password" required>
                             </div>
                            <button type="submit" class="btn btn-lg btn-danger btn-block" name="submit" value="reset">Ubah</button>

                            <!-- <input type="hidden" class="hide" name="token" id="token" value=""> -->
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script>
        $('#form-reset').submit(function(){
          var new_pass = $('#new_pass').val().trim();
          var pass_repeat = $('#repeat_pass').val().trim();

          if(new_pass == '' || repeat_pass == ''){
            alert('Password tidak boleh kosong');
            return false;
          }else{
            if(new_pass != pass_repeat){
              alert('password tidak sama!');
              return false;
            }
          }
        })
      </script>
   </body>
</html>
