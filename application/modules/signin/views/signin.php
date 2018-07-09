<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <meta name="robots" value="noindex,nofollow" />
    <title>Sign In Page</title>
    <link rel="stylesheet" href="<?php echo site_url('assets/signin/css/style.css')?>">
    <!--Google Font - Work Sans-->
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,700' rel='stylesheet' type='text/css'>
    <!-- <script src='https://www.google.com/recaptcha/api.js'></script> -->

  </head>
  <body>
    <div class="container" style="margin-top:40px">
      <div class="profile profile--close profile--open">
        <button class="profile__avatar" id="toggleProfile">
          <img src="<?php echo site_url('assets/signin/images/signin_avatar.png')?>" alt="Avatar" />
        </button>

        <!-- <form action="" method="post"> -->
        <?php echo form_open();?>

          <div class="profile__form">
            <div class="profile__fields">
              <div class="field">
                <input name="email" type="email" id="fieldUser" class="input" placeholder="Email" required pattern=.*\S.* />
                <!-- <label for="fieldUser" class="label">Email</label> -->
              </div>
              <div class="field">
                <input name="password" type="password" id="fieldPassword" class="input" placeholder="Password" required pattern=.*\S.* />
                <!-- <label for="fieldPassword" class="label">Password</label> -->
              </div>


              <?php //echo $this->recaptcha->render();?>

              <div class="profile__footer">
                <button class="btn" type="submit">Login</button>
                <!-- <button class="btn" type="submit" style="float:right">Lupa password ?</button> -->
                <label for="fieldPassword" class="label" style="position: inherit;padding-left:10px"><a href="<?php echo site_url('password-reset')?>">Lupa password ?</a></label>

              </div>
            </div>
           </div>
        <!-- </form> -->
        <?php echo form_close();?>

      </div>
    </div>
  </body>
  <script src="<?php echo site_url('assets/signin/js/index.js')?>"></script>
</html>
