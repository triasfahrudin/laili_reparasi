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
                        <h2 class="text-center">Lupa Password?</h2>
                        <p>Anda bisa mereset password anda disini.</p>
                        <div class="panel-body">
                           <form id="register-form" role="form" autocomplete="off" class="form" method="post">
                              <div class="form-group">
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                    <input id="email" name="email" placeholder="Alamat email" class="form-control" type="email">
                                 </div>
                              </div>
                              <div class="form-group">
                                 <input name="recover-submit" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                              </div>
                              <input type="hidden" class="hide" name="token" id="token" value="">
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>
