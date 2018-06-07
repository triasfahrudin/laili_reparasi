<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="robots" content="noindex, nofollow">
      <title>ADMIN</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
      <link href="<?php echo site_url('assets/manage/css/custom.css')?>" rel="stylesheet" id="bootstrap-css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" />
      <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/red/pace-theme-flash.css" /> -->
      <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/red/pace-theme-mac-osx.min.css" /> -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/red/pace-theme-loading-bar.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
      <style>
         .pac-card {
         margin: 10px 10px 0 0;
         border-radius: 2px 0 0 2px;
         box-sizing: border-box;
         -moz-box-sizing: border-box;
         outline: none;
         box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
         background-color: #fff;
         font-family: Roboto;
         }
         #pac-container {
         padding-bottom: 12px;
         margin-right: 12px;
         }
         .pac-controls {
         display: inline-block;
         padding: 5px 11px;
         }
         .pac-controls label {
         font-family: Roboto;
         font-size: 13px;
         font-weight: 300;
         }
         #pac-input {
         background-color: #fff;
         font-family: Roboto;
         font-size: 15px;
         font-weight: 300;
         margin-left: 12px;
         padding: 0 11px 0 13px;
         text-overflow: ellipsis;
         width: 400px;
         }
         #pac-input:focus {
         border-color: #4d90fe;
         }
         #title {
         color: #fff;
         background-color: #4d90fe;
         font-size: 25px;
         font-weight: 500;
         padding: 6px 12px;
         }
         #target {
         width: 345px;
         }
         .chat
         {
         list-style: none;
         margin: 0;
         padding: 0;
         }
         .chat li
         {
         margin-bottom: 10px;
         padding-bottom: 5px;
         border-bottom: 1px dotted #B3A9A9;
         }
         .chat li.left .chat-body
         {
         margin-left: 60px;
         }
         .chat li.right .chat-body
         {
         margin-right: 60px;
         }
         .chat li .chat-body p
         {
         margin: 0;
         color: #777777;
         }
         .panel .slidedown .glyphicon, .chat .glyphicon
         {
         margin-right: 5px;
         }
         .panel-body
         {
         overflow-y: scroll;
         height: 250px;
         }
         ::-webkit-scrollbar-track
         {
         -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
         background-color: #F5F5F5;
         }
         ::-webkit-scrollbar
         {
         width: 12px;
         background-color: #F5F5F5;
         }
         ::-webkit-scrollbar-thumb
         {
         -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
         background-color: #555;
         }
      </style>
      <?php
         if(isset($css_files)){
           foreach($css_files as $file): ?>
      <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
      <?php endforeach;
         }else{ ?>
      <link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" >
      <?php }; ?>
      <?php
         if(isset($js_files)){
           foreach($js_files as $file): ?>
      <script src="<?php echo $file; ?>"></script>
      <?php endforeach;
         }else{ ?>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
      <!-- <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script> -->
      <?php }; ?>
      <!-- <script src="//code.jquery.com/jquery-1.10.2.min.js"></script> -->
      <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Cookies.js/0.3.1/cookies.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
      <script type="text/javascript">
         $(document).ready(function(){
           <?php echo @$grocery_btn?>
         });
      </script>
   </head>
   <body>
      <nav class="navbar navbar-default navbar-static-top">
         <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
               <button type="button" class="navbar-toggle navbar-toggle-sidebar collapsed">
               MENU
               </button>
               <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               </button>
               <a class="navbar-brand" href="#">
               Administrator
               </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
               <!-- <form class="navbar-form navbar-left" method="GET" role="search">
                  <div class="form-group">
                     <input type="text" name="q" class="form-control" placeholder="Search">
                  </div>
                  <button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i></button>
                  </form> -->
               <ul class="nav navbar-nav navbar-right">
                  <!-- <li><a href="http://www.pingpong-labs.com" target="_blank">Visit Site</a></li> -->
                  <li class="dropdown ">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                     <?php echo strtoupper($this->session->userdata('user_nama_lengkap'))?>
                     <span class="caret"></span></a>
                     <ul class="dropdown-menu" role="menu">
                        <!-- <li class="dropdown-header">SETTINGS</li>
                           <li class=""><a href="#">Other Link</a></li>
                           <li class=""><a href="#">Other Link</a></li>
                           <li class=""><a href="#">Other Link</a></li>
                           <li class="divider"></li> -->
                        <li><a href="<?php echo site_url('admin/profile')?>">Profile</a></li>
                        <li><a href="<?php echo site_url('signout')?>">Logout</a></li>
                     </ul>
                  </li>
               </ul>
            </div>
            <!-- /.navbar-collapse -->
         </div>
         <!-- /.container-fluid -->
      </nav>
      <div class="container-fluid main-container">
         <div class="col-md-2 sidebar">
            <div class="row">
               <!-- uncomment code for absolute positioning tweek see top comment in css -->
               <div class="absolute-wrapper"> </div>
               <!-- Menu -->
               <div class="side-menu">
                  <nav class="navbar navbar-default" role="navigation">
                     <!-- Main Menu -->
                     <div class="side-menu-container">
                        <ul class="nav navbar-nav main-menu">
                           <li class="active"><a href="<?php echo site_url('admin/index')?>"><span class="glyphicon glyphicon-home"></span> Dashboard</a></li>
                           <li><a href="<?php echo site_url('admin/user')?>"><span class="glyphicon glyphicon-list-alt"></span> Admin</a></li>
                           <li><a href="<?php echo site_url('admin/kategori_jasa')?>"><span class="glyphicon glyphicon-list-alt"></span> Kategori Jasa</a></li>
                           <li><a href="<?php echo site_url('admin/pelanggan')?>"><span class="glyphicon glyphicon-list-alt"></span> Pelanggan</a></li>
                           <li><a href="<?php echo site_url('admin/penjual_jasa')?>"><span class="glyphicon glyphicon-list-alt"></span> Penjual Jasa</a></li>
                           <li><a href="<?php echo site_url('admin/transaksi')?>"><span class="glyphicon glyphicon-list-alt"></span> Transaksi</a></li>
                           <li><a href="<?php echo site_url('admin/web_settings')?>"><span class="glyphicon glyphicon-wrench"></span> Web Settings</a></li>
                        </ul>
                     </div>
                     <!-- /.navbar-collapse -->
                  </nav>
               </div>
            </div>
         </div>
         <div class="col-md-10 content">
            <?php echo $this->breadcrumbs->show();?>
            <?php if(isset($output)){ echo $output; }else{ include $page_name . ".php";} ?>
         </div>
         <footer class="pull-left footer">
            <p class="col-md-12">
            <hr class="divider">
            <!-- Copyright &COPY; 2015 <a href="http://www.pingpong-labs.com">Gravitano</a> -->
            </p>
         </footer>
      </div>
      <!-- Modal -->
      <div id="modalPercakapan" class="modal fade" role="dialog">
         <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <!--  <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Percakapan</h4>
               </div> -->
               <div class="modal-body">
                  <div class="container">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="panel panel-primary">
                              <div class="panel-heading" id="accordion">
                                 <span class="glyphicon glyphicon-comment"></span> Chat
                                 <!-- <div class="btn-group pull-right">
                                    <a type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                    <span class="glyphicon glyphicon-chevron-down"></span>
                                    </a>
                                 </div> -->
                              </div>
                              <div class="panel-collapse collapse in" id="collapseOne">
                                 <div class="panel-body">
                                    <ul class="chat">
                                       <li class="left clearfix">
                                          <span class="chat-img pull-left">
                                          <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                                          </span>
                                          <div class="chat-body clearfix">
                                             <div class="header">
                                                <strong class="primary-font">Jack Sparrow</strong> <small class="pull-right text-muted">
                                                <span class="glyphicon glyphicon-time"></span>12 mins ago</small>
                                             </div>
                                             <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                                dolor, quis ullamcorper ligula sodales.
                                             </p>
                                          </div>
                                       </li>
                                       <li class="right clearfix">
                                          <span class="chat-img pull-right">
                                          <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle" />
                                          </span>
                                          <div class="chat-body clearfix">
                                             <div class="header">
                                                <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>13 mins ago</small>
                                                <strong class="pull-right primary-font">Bhaumik Patel</strong>
                                             </div>
                                             <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                                dolor, quis ullamcorper ligula sodales.
                                             </p>
                                          </div>
                                       </li>
                                       <li class="left clearfix">
                                          <span class="chat-img pull-left">
                                          <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                                          </span>
                                          <div class="chat-body clearfix">
                                             <div class="header">
                                                <strong class="primary-font">Jack Sparrow</strong> <small class="pull-right text-muted">
                                                <span class="glyphicon glyphicon-time"></span>14 mins ago</small>
                                             </div>
                                             <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                                dolor, quis ullamcorper ligula sodales.
                                             </p>
                                          </div>
                                       </li>
                                       <li class="right clearfix">
                                          <span class="chat-img pull-right">
                                          <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle" />
                                          </span>
                                          <div class="chat-body clearfix">
                                             <div class="header">
                                                <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>15 mins ago</small>
                                                <strong class="pull-right primary-font">Bhaumik Patel</strong>
                                             </div>
                                             <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                                dolor, quis ullamcorper ligula sodales.
                                             </p>
                                          </div>
                                       </li>
                                    </ul>
                                 </div>
                                 <div class="panel-footer">
                                    <div class="input-group">
                                       <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." />
                                       <span class="input-group-btn">
                                       <button class="btn btn-warning btn-sm" id="btn-chat">
                                       Send</button>
                                       </span>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div> -->
            </div>
         </div>
      </div>

      <div id="modalDetailTransaksi" class="modal fade" role="dialog">
         <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Detail Transaksi</h4>
               </div>
               <div class="modal-body">
                  
               </div>
               <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div> -->
            </div>
         </div>
      </div>

      <style type="text/css">
         .modal-backdrop { z-index: -1;}
      </style>
      <script type="text/javascript">
         $(function () {
            $('.navbar-toggle-sidebar').click(function () {
              $('.navbar-nav').toggleClass('slide-in');
              $('.side-body').toggleClass('body-slide-in');
              $('#search').removeClass('in').addClass('collapse').slideUp(200);
            });
         
            $('#search-trigger').click(function () {
              $('.navbar-nav').removeClass('slide-in');
              $('.side-body').removeClass('body-slide-in');
              $('.search-input').focus();
            });
          });
         
          $(document).ready(function () {
         
            $(".select2").select2();
         
            var index = Cookies.get('active');
            $('.main-menu').find('li').removeClass('active');
            $(".main-menu").find('li').eq(index).addClass('active');
            $('.main-menu').on('click', 'li', function (e) {
                // e.preventDefault();
                $('.main-menu').find('li').removeClass('active');
                $(this).addClass('active');
                Cookies.set('active', $('.main-menu li').index(this));
            });
          });
         
          <?php if(has_alert()):
            foreach(has_alert() as $type => $message): ?>
            <?php if($type === 'alert-danger'){ ?>
              swal({
                  title: 'Ada kesalahan!',
                  text: '<?php echo $message; ?>',
                  type: 'error',
                  confirmButtonText: 'Ok'
              });
            <?php }else{ ?>
              swal({
                  title: 'Berhasil',
                  text: '<?php echo $message; ?>',
                  type: 'success',
                  confirmButtonText: 'Ok'
              });
           <?php } ?>
            <?php endforeach;
            endif; ?>
         
          // $(".fancybox").fancybox({
          //   openEffect: "elastic",
          //   closeEffect: "elastic"
          // });
         
         
         
          $(document).on("keypress", "form", function(event) { 
              return event.keyCode != 13;
          });

          function lihat_percakapan(transaksi_id){
            $('#modalPercakapan').modal();
          }

          function lihat_detail_transaksi(transaksi_id){
            $('#modalDetailTransaksi').modal();
          }
      </script>
   </body>
</html>