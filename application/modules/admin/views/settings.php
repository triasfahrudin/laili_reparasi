<script src="<?php echo site_url('assets/grocery_crud/texteditor/ckeditor/ckeditor.js')?>"></script>
<script src="<?php echo site_url('assets/grocery_crud/texteditor/ckeditor/adapters/jquery.js')?>"></script>
<script src="<?php echo site_url('assets/grocery_crud/js/jquery_plugins/config/jquery.ckeditor.config.js')?>"></script>

<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDy5ePPPOnm2Ix6_MU7SGsUX4QzrHfH1t4&sensor=false"></script>

<legend>Web Settings</legend>

<?php if($setting->num_rows() == 0){ ?>
   <div class="row"></div>
   <div class="alert alert-info">
      <strong>Data tidak ditemukan.</strong>
   </div>
   <?php }else{ ?>
   <table width="100%" class="table table-condensed">
      <thead>
         <tr>
            <th style="text-align: left">No</th>
            <th style="text-align: left">Title</th>
            <th style="text-align: left">Tipe</th>
            <th style="text-align: left">Value</th>
         </tr>
      </thead>
      <tbody>
         <?php $nomor = 0;?>
         <?php foreach($setting->result() as $r){ ?>
         <tr>
            <td><?php echo $nomor + 1?></td>
            <td><?php echo $r->title;?></td>
            <td><?php echo $r->tipe;?></td>
            <td>
            <?php if($r->tipe === 'big-text'){?>
              <textarea id="<?php echo $r->title;?>" class="update_me texteditor" style="width: 100%;height: 100%"><?php echo $r->value;?></textarea>
              <script>
                CKEDITOR.on('instanceCreated', function (e) {
                  e.editor.on('change', function (event) {
                    var this_val = CKEDITOR.instances['<?php echo $r->title;?>'].getData();//Value of Editor
                    var this_title = '<?php echo $r->title;?>';

                    $.post( "<?php echo base_url() . 'admin/web_settings/edt/';?>" + this_title,
                      { value: this_val }
                    );
                  });
                });

              </script>
            <?php }elseif($r->tipe === 'small-text'){ ?>
              <input id="<?php echo $r->title;?>" class="update_me form-control" style="width: 100%;height: 100%" type="text" value="<?php echo $r->value;?>">
            <?php }elseif($r->tipe === 'image'){ ?>
              <!-- <img style="margin-bottom: 10px" src="<?php echo base_url(); ?>timthumb?src=/uploads/<?php echo $r->value;?>&h=100&w=100&zc=0"> -->
              <a href="<?php echo base_url() . 'uploads/' . $r->value;?>" target="_blank">Lihat Gambar</a>
              <form action="<?php echo base_url() . 'admin/web_settings/upload/' . $r->title;?>" method="POST" enctype="multipart/form-data">
                <input class="upload" name="img" onchange="this.form.submit()" multiple="" type="file">
              </form>
            <?php }elseif ($r->tipe === 'map') { ?>
              <script>
                // global "map" variable
                var map_<?php echo $r->title?> = null;
                var marker_<?php echo $r->title?> = null;

                var infowindow_<?php echo $r->title?> = new google.maps.InfoWindow({size: new google.maps.Size(150,50)});

                // A function to create the marker and set up the event window function
                function createMarker_<?php echo $r->title?>(map,infowindow,latlng, name, html) {
                    var contentString = html;
                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        zIndex: Math.round(latlng.lat()*-100000)<<5
                    });

                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.setContent(contentString);
                        infowindow.open(map,marker);
                    });

                    google.maps.event.trigger(marker, 'click');
                    return marker;
                }


                function initialize_<?php echo $r->title?>() {
                    <?php if($r->value === ""){ ?>

                    var myOptions = {
                            zoom: 4,
                            center: new google.maps.LatLng(-0.08789059053082422, 113.6865234375),
                            mapTypeControl: true,
                            mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
                            navigationControl: true,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                    }

                    map_<?php echo $r->title?> = new google.maps.Map(document.getElementById("map_canvas_<?php echo $r->title?>"), myOptions);

                    google.maps.event.addListener(map_<?php echo $r->title?>, 'click', function() {
                          infowindow_<?php echo $r->title?>.close();
                    });

                    google.maps.event.addListener(map_<?php echo $r->title?>, 'click', function(event) {
                  	//call function to create marker
                      if (marker_<?php echo $r->title?>) {
                          marker_<?php echo $r->title?>.setMap(null);
                          marker_<?php echo $r->title?> = null;
                      }

            	        marker_<?php echo $r->title?> = createMarker_<?php echo $r->title?>(map_<?php echo $r->title?>,infowindow_<?php echo $r->title?>,event.latLng, "name", "<b>Location</b><br>"+event.latLng);
                       //alert(event.latLng.lat());
                      //$('#lat').val(event.latLng.lat());
                      //$('#lng').val(event.latLng.lng());
                        $.post( "<?php echo base_url() . 'admin/web_settings/edt/' . $r->title;?>" ,
                          { value: event.latLng.lat() + '|' + event.latLng.lng() }
                        );
                    });

                    <?php }else{ ?>

                      <?php $latlng = explode('|',$r->value);?>
                      var myLatLng = {lat: <?php echo $latlng[0]?>, lng: <?php echo $latlng[1]?>};
                      // create the map
                      var myOptions = {
                        zoom: 15,
                        center: new google.maps.LatLng(<?php echo $latlng[0]?>, <?php echo $latlng[1]?>),
                        mapTypeControl: true,
                        mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
                        navigationControl: true,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                      }

                      map_<?php echo $r->title?> = new google.maps.Map(document.getElementById("map_canvas_<?php echo $r->title?>"), myOptions);

                      var marker_<?php echo $r->title?> = new google.maps.Marker({
                          position: myLatLng,
                          map: map_<?php echo $r->title?>,
                          title: 'Hello World!'
                        });


                      google.maps.event.addListener(map_<?php echo $r->title?>, 'click', function() {
                        infowindow_<?php echo $r->title?>.close();
                      });

                      google.maps.event.addListener(map_<?php echo $r->title?>, 'click', function(event) {
                      	//call function to create marker
                          if (marker_<?php echo $r->title?>) {
                              marker_<?php echo $r->title?>.setMap(null);
                              marker_<?php echo $r->title?> = null;
                          }

                          marker_<?php echo $r->title?> = createMarker_<?php echo $r->title?>(map_<?php echo $r->title?>,infowindow_<?php echo $r->title?>,event.latLng, "name", "<b>Location</b><br>"+event.latLng);
                          //alert(event.latLng.lat());
                          //$('#lat').val(event.latLng.lat());
                          //$('#lng').val(event.latLng.lng());
                          $.post( "<?php echo base_url() . 'admin/web_settings/edt/' . $r->title;?>" ,
                            { value: event.latLng.lat() + '|' + event.latLng.lng() }
                          );
                  });


                  <?php } ?>
                }
                                //]]>

              window.onload = initialize_<?php echo $r->title?>;

              </script>
              <div id="map_canvas_<?php echo $r->title?>" style="width:100%; height:350px;"></div>

            <?php } ?>

            </td>
         </tr>
         <?php $nomor++;} ?>
      </tbody>
   </table>
   <?php } ?>


<script>
$('.update_me').keyup( function() {
  var this_title = $(this).attr('id');
  var this_val = $(this).val();

  $.post( "<?php echo base_url() . 'admin/web_settings/edt/';?>" + this_title,
    { value: this_val }
  );
});


</script>
