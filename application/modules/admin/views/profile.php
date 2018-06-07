<div class="panel panel-default">
   <div class="panel-heading">
      <h3 class="panel-title">
         Form Profile
      </h3>
   </div>
   <div class="panel-body">
     <?php echo form_open('admin/profile','');?>

       <div class="form-group">
         <label>Nama lengkap</label>
         <input type="text" class="form-control" placeholder="Nama lengkap" name="nama_lengkap" required value="<?php echo $p->nama_lengkap?>">
         <!-- <span class="help-block">* Kosongkan jika tidak ingin merubah password</span> -->
       </div>

       <div class="form-group">
         <label>Email</label>
         <input type="email" class="form-control" placeholder="Email" name="email" required value="<?php echo $p->email?>">
         <!-- <span class="help-block">* Kosongkan jika tidak ingin merubah password</span> -->
       </div>


       <div class="form-group">
         <label>Password Lama</label>
         <input type="password" class="form-control" placeholder="Password Lama" id="pass_lama" name="pass_lama">
         <span class="help-block">* Kosongkan jika tidak ingin merubah password</span>
       </div>

       <div class="form-group" id="div_pass_baru">
         <label>Password Baru</label>
         <input type="password" class="form-control" placeholder="Password Baru" id="pass_baru" name="pass_baru">
       </div>

       <div class="form-group" id="div_pass_ulangi">
         <label>Ulangi</label>
         <input type="password" class="form-control" placeholder="Ulangi Password" id="pass_ulangi" name="pass_ulangi">
       </div>

       <button type="submit" class="btn btn-default">Submit</button>
     <!-- </form> -->
     <?php echo form_close();?>
   </div>
</div>

<script>
    $('#div_pass_baru , #div_pass_ulangi').hide();

    $( "#pass_lama").keyup(function() {
      if($(this).val() != ''){
        $('#div_pass_baru , #div_pass_ulangi').show();
      }else{
        $('#div_pass_baru , #div_pass_ulangi').hide();
      }
    });
</script>
