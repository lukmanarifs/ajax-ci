<!-- Tambah Data-->
<div class="modal fade" id="modalTambahData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog shadow-nohover" role="document">
    <div class="modal-content">
    <form id="formTambah" class="forms-sample" action="#" method="POST">
        <input type="hidden" id="csrfTambah" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Tambah Data Pengguna</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="exampleInputEmail2" class="col-sm-4 col-form-label">Username</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control formtab" id="tambah_username" name="tambah_username"/>
                </div>
                <div class="col-md-4"></div>
                <div id="error" class="form-modal-error col-md-8"></div>
              </div>
              <div class="form-group row">
                <label for="exampleInputEmail2" class="col-sm-4 col-form-label">Nama Lengkap</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control formtab" id="tambah_fullname" name="tambah_fullname"/>
                </div>
                <div class="col-md-4"></div>
                <div id="error" class="form-modal-error col-md-8"></div>
              </div>
              <div class="form-group row">
                <label for="exampleInputEmail2" class="col-sm-4 col-form-label">Password</label>
                <div class="col-sm-8">
                  <input type="password" class="form-control formtab" id="tambah_password" name="tambah_password"/>
                </div>
                <div class="col-md-4"></div>
                <div id="error" class="form-modal-error col-md-8"></div>
              </div>
              <div class="form-group row">
                <label for="role" class="col-sm-4 col-form-label">Role</label>
                <div class="col-sm-8">
                  <select id="tambah_role" name="tambah_role" class="form-control formtab">
                    <?php foreach ($kategoris as $key => $kategori): ?>
                      <option value="<?= $kategori['kategori_id'] ?>"><?= $kategori['kategori_nama'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4"></div>
                <div id="error" class="form-modal-error col-md-8"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary shadow-nohover" data-dismiss="modal">Kembali</button>
                <input type="submit" class="btn btn-primary modal-confirm formtab shadow-nohover" value="Proses"></input>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
//Proses Store Data
$( "#formTambah" ).on( "submit", function( event ) {
  event.preventDefault();
  processForm('Tambah','store');
});

$('#modalTambahData').on('shown.bs.modal', function() {
  $('#tambah_username').focus();
})
</script>
