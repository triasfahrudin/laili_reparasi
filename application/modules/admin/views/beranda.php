<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">Data</h3>
  </div>
  <div class="panel-body">
    <div class="col-lg-3" style="text-align: center;">
      <a href="<?php echo site_url('admin/pelanggan')?>" class="thumbnail" style="color:rgba(247, 194, 6, 0.83);">
        <i class="fa fa-file-o" style="font-size: 80px; margin: 10px 0"></i><br>
        <h4 class="">(<label id="jml_pelanggan">0</label>) Pelanggan</h4>
      </a>
    </div>

    <div class="col-lg-3" style="text-align: center;">
      <a href="<?php echo site_url('admin/penjual-jasa')?>" class="thumbnail text-warning"">
        <i class="fa fa-file-o" style="font-size: 80px; margin: 10px 0"></i><br>
        <h4 class="">(<label id="jml_penjual_jasa">0</label>) Penjual Jasa</h4>
      </a>      
    </div>

    <div class="col-lg-3" style="text-align: center;">
      <a href="<?php echo site_url('admin/transaksi')?>" class="thumbnail text-primary"">
        <i class="fa fa-file-o" style="font-size: 80px; margin: 10px 0"></i><br>
        <h4 class="">(<label id="jml_transaksi">0</label>) Transaksi</h4>
      </a>
    </div>

  </div>
</div>



<script>
  (function worker() {
    $.ajax({
      url: '<?php echo site_url('admin/statistik')?>',
      method : "POST",
      data : { status : "semua"},
      success: function(data) {
        $('#jml_pelanggan').html(data.jml_pelanggan);
        $('#jml_penjual_jasa').html(data.jml_penjual_jasa);
        $('#jml_transaksi').html(data.jml_transaksi);
        
      },
      complete: function() {
        setTimeout(worker, 5000);
      }
    });
  })();
</script>
