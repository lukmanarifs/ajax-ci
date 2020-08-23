<!-- Edit Nelayan -->
<div class="modal fade" id="modalEditData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="formEdit" class="forms-sample" action="#" method="POST">
        <input type="hidden" id="csrfEdit" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>">
      <input type="hidden" id="id" name="id"/>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Data Pengguna</h5>
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
                <input type="text" class="form-control formtab" id="edit_username" name="edit_username"/>
              </div>
              <div class="col-md-4"></div>
              <div id="error" class="form-modal-error col-md-8"></div>
            </div>
            <div class="form-group row">
              <label for="exampleInputEmail2" class="col-sm-4 col-form-label">Nama Lengkap</label>
              <div class="col-sm-8">
                <input type="text" class="form-control formtab" id="edit_fullname" name="edit_fullname"/>
              </div>
              <div class="col-md-4"></div>
              <div id="error" class="form-modal-error col-md-8"></div>
            </div>
            <div class="form-group row password_default" hidden>
              <label for="exampleInputEmail2" class="col-sm-4 col-form-label">Password Default</label>
              <div class="col-sm-8">
                <input type="text" class="form-control formtab" id="edit_password_default" name="edit_password_default" readonly/>
              </div>
              <div class="col-md-4"></div>
              <div id="error" class="form-modal-error col-md-8"></div>
            </div>
            <div class="form-group row">
              <label for="role" class="col-sm-4 col-form-label">Role</label>
              <div class="col-sm-8">
                <select id="edit_role" name="edit_role" class="form-control formtab">
                  <?php foreach ($kategoris as $key => $kategori): ?>
                    <option value="<?= $kategori['kategori_id'] ?>"><?= $kategori['kategori_nama'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4"></div>
              <div id="error" class="form-modal-error col-md-8"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
        <a href="#" class="btn btn-warning float-right" onclick="show_confirm_reset()">Reset Password</a>
        <input type="submit" class="btn btn-primary modal-confirm formtab" value="Proses"></input>
      </div>
      </form>
    </div>
  </div>
</div>
<div id="modal-confirm-reset"></div>

<script type="text/javascript">
$( "#formEdit" ).on( "submit", function( event ) {
  event.preventDefault();
  processForm('Edit','update');
});
$('#modalTambahData').on('shown.bs.modal', function() {
  $('#edit_username').focus();
})

function show_confirm_reset(){
  $.ajax({
     type: 'POST',
     url: '<?= base_url()?>user/confirmReset',
     dataType: 'html',
     // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
     data: {id:$('#id').val(), csrf_token:$('#csrfTambah').val()},

     success: function (data) {
            $('#modal-confirm-reset').html(data);
            $('#resetModal').modal('show');

     },
     error: function (data) {
          console.log(data);
     }
  });
}
</script>
