<!-- Edit Nelayan -->
<div class="modal fade" id="modalEditData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="formEdit" class="forms-sample" action="<?=base_url().'daftar/nelayan/update'?>" method="POST">
      <input type="hidden" id="id" name="id"/>
      <input type="hidden" id="csrfEdit" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Data Pelaku Usaha Perikanan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group row ">
              <label for="recipient-name" class="col-form-label col-md-3">Nama Pelaku Usaha</label>
              <input type="text" class="form-control col-md-9 formtab" id="edit_nama" name="edit_nama" value="<?php echo set_value('tambah_nama'); ?>">
              <div class="col-md-3"></div>
              <div id="error" class="-error col-md-9"></div>
            </div>
            <div class="form-group row ">
              <label for="recipient-name" class="col-form-label col-md-3">NIK</label>
              <input type="text" class="form-control col-md-9 formtab" id="edit_nik" name="edit_nik" value="<?php echo set_value('tambah_nik'); ?>">
              <div class="col-md-3"></div>
              <div id="error" class="-error col-md-9"></div>
            </div>
            <div class="form-group row ">
              <label for="recipient-name" class="col-form-label col-md-3">Alamat</label>
              <input type="text" class="form-control col-md-9 formtab" id="edit_alamat" name="edit_alamat" value="<?php echo set_value('tambah_alamat'); ?>">
              <div class="col-md-3"></div>
              <div id="error" class="-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">Kelompok Usaha</label>
              <select class="form-control col-md-9 select2 select2-edit formtab" id="edit_kelompok" name="edit_kelompok" style="width: 75%">
                <option value="">-- Pilih Kelompok --</option>
                <?php foreach ($kelompoks as $key => $kelompok): ?>
                  <option value="<?= $kelompok['kelompok_key'] ?>"><?= $kelompok['kelompok_nama'] ?></option>
                <?php endforeach; ?>
              </select>
              <!-- <input type="text" class="form-control col-md-9" id="tambah_desa" name="tambah_desa" value="<?php //echo set_value('tambah_desa'); ?>"> -->
              <div class="col-md-3"></div>
              <div id="error" class="form-modal-error col-md-9"></div>
            </div>
            <div class="form-group row ">
              <label for="recipient-name" class="col-form-label col-md-3">Kecamatan</label>
              <select class="form-control col-md-9 select2 select2-edit formtab" id="edit_kecamatan" name="edit_kecamatan" style="width: 75%">
                <option value="">-- Pilih Kecamatan --</option>
                <?php foreach ($kecamatans as $key => $kecamatan): ?>
                  <option value="<?= $kecamatan['kecamatan_kode'] ?>"><?= $kecamatan['kecamatan_nama'] ?></option>
                <?php endforeach; ?>
              </select>
              <!-- <input type="text" class="form-control col-md-9" id="tambah_desa" name="tambah_desa" value="<?php //echo set_value('tambah_desa'); ?>"> -->
              <div class="col-md-3"></div>
              <div id="error" class="-error col-md-9"></div>
            </div>
            <div class="form-group row ">
              <label for="recipient-name" class="col-form-label col-md-3">Desa</label>
              <select class="form-control col-md-9 select2 select2-edit formtab" id="edit_desa" name="edit_desa" style="width: 75%">
                <option value="">-- Pilih Desa --</option>
              </select>
              <div class="col-md-3"></div>
              <div id="error" class="-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3 numeric">Nomor KUSUKA</label>
              <input type="text" class="form-control col-md-9 formtab" id="tambah_kusuka" name="tambah_kusuka" onkeyup="" style="text-align: left;" value="<?php echo set_value('tambah_kusuka'); ?>">
              <div class="col-md-3"></div>
              <div id="error" class="-error col-md-9"></div>
            </div>
            <div class="form-group row">
              <label for="recipient-name" class="col-form-label col-md-3">Partisipasi Asuransi</label>
              <div class="form-check form-check-flat">
                <label class="form-check-label">
                  <input type="checkbox" class="form-check-input" id="tambah_asuransi" name="tambah_asuransi">
                <i class="input-helper"></i></label>
              </div>
              <div class="col-md-3"></div>
              <div id="error" class="-error col-md-9"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary shadow-nohover" data-dismiss="modal">Kembali</button>
        <input type="submit" class="btn btn-primary modal-confirm formtab shadow-nohover" value="Proses"></input>
      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
$( "#formEdit" ).on( "submit", function( event ) {
  event.preventDefault();
  processForm('Edit','update');
});

function editForm(idVal){
  $.get( "<?php echo base_url()?>daftar/nelayan/edit", { id: idVal } )
  .done(function( data ) {
    let obj = JSON.parse(data);
    $('#modalEditData').modal('show');
    $('#id').val(obj.nelayan_key);
    $('#edit_nama').val(obj.nelayan_nama);
    $('#edit_nik').val(obj.nelayan_nik);
    $('#edit_alamat').val(obj.nelayan_alamat);
    $('#edit_kelompok').val(obj.nelayan_kelompok_key);
    $('#edit_kelompok').trigger('change');
    $('#edit_kecamatan').val(obj.nelayan_kecamatan);
    $('#edit_kecamatan').trigger('change');

    $.get( "<?php echo base_url()?>daftar/nelayan/getDesa", { kecamatan: obj.nelayan_kecamatan } )
    .done(function( data2 ) {
      let obj2 = JSON.parse(data2);
      var $dropdown = $("#edit_desa");
      $('#edit_desa').empty()
        .append("<option value=''>-- Pilih Desa --</option>");
      $.each(obj2, function() {
          $dropdown.append($("<option />").val(this.desa_kode).text(this.desa_nama));
      });

      $('#edit_desa').val(obj.nelayan_desa);
      $('#edit_desa').trigger('change');
    });
  });
}
</script>
