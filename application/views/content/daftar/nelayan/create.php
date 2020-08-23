<!-- Tambah Data-->
<div class="modal fade" id="modalTambahData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog shadow-nohover" role="document">
    <div class="modal-content">
      <form id="formTambah" class="forms-sample" action="<?=base_url().'daftar/nelayan/store'?>" method="POST">
      <input type="hidden" id="csrfTambah" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Data Nelayan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">Nama Pelaku Usaha</label>
              <input type="text" class="form-control col-md-9 formtab" id="tambah_nama" name="tambah_nama" value="<?php echo set_value('tambah_nama'); ?>">
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">NIK</label>
              <input type="text" class="form-control col-md-9 formtab numeric" id="tambah_nik" name="tambah_nik" onkeyup="" style="text-align: left;" value="<?php echo set_value('tambah_nik'); ?>">
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">Alamat</label>
              <input type="text" class="form-control col-md-9 formtab" id="tambah_alamat" name="tambah_alamat" value="<?php echo set_value('tambah_alamat'); ?>">
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">Kelompok Usaha</label>
              <select class="form-control col-md-9 select2 select2-tambah formtab" id="tambah_kelompok" name="tambah_kelompok" style="width: 75%">
                <option value="">-- Pilih Kelompok --</option>
                <?php foreach ($kelompoks as $key => $kelompok): ?>
                  <option value="<?= $kelompok['kelompok_key'] ?>"><?= $kelompok['kelompok_nama'] ?></option>
                <?php endforeach; ?>
              </select>
              <!-- <input type="text" class="form-control col-md-9" id="tambah_desa" name="tambah_desa" value="<?php //echo set_value('tambah_desa'); ?>"> -->
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">Kecamatan</label>
              <select class="form-control col-md-9 select2 select2-tambah formtab" id="tambah_kecamatan" name="tambah_kecamatan" style="width: 75%">
                <option value="">-- Pilih Kecamatan --</option>
                <?php foreach ($kecamatans as $key => $kecamatan): ?>
                  <option value="<?= $kecamatan['kecamatan_kode'] ?>"><?= $kecamatan['kecamatan_nama'] ?></option>
                <?php endforeach; ?>
              </select>
              <!-- <input type="text" class="form-control col-md-9" id="tambah_desa" name="tambah_desa" value="<?php //echo set_value('tambah_desa'); ?>"> -->
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">Desa</label>
              <select class="form-control col-md-9 select2 select2-tambah formtab" id="tambah_desa" name="tambah_desa" style="width: 75%">
                <option value="">-- Pilih Desa --</option>
              </select>
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3 numeric">Nomor KUSUKA</label>
              <input type="text" class="form-control col-md-9 formtab" id="tambah_kusuka" name="tambah_kusuka" onkeyup="" style="text-align: left;" value="<?php echo set_value('tambah_kusuka'); ?>">
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">Partisipasi Asuransi</label>
              <div class="form-check form-check-flat">
                <label class="form-check-label">
                  <input type="checkbox" class="form-check-input" id="tambah_asuransi" name="tambah_asuransi" value="1">
                <i class="input-helper"></i></label>
              </div>
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary shadow-nohover shadow-nohover" data-dismiss="modal">Kembali</button>
        <input type="submit" class="btn btn-primary modal-confirm formtab shadow-nohover shadow-nohover" value="Proses"></input>
      </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
// $('#tambah_nik').keyup(function(){
// });
//Proses Store Data
$( "#formTambah" ).on( "submit", function( event ) {
  event.preventDefault();
  processForm('Tambah','store');
});

$('#modalTambahData').on('shown.bs.modal', function() {
  $('#tambah_nama').focus();
})
</script>
