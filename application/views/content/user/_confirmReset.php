<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="reset-password-form" action="<?= base_url()?>user/generatePassword/<?=$user->id?>" method="POST">
        <input type="hidden" id="id_user_reset" name="id_user_reset" value="<?=$user->id?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Reset Password Pengguna?</h5>
      </div>
      <div class="modal-body">
        Mereset Password Pengguna <b><?= $user->username ?></b> akan merubah password yang telah ada, menjadi password default.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-delete" data-dismiss="modal">Tidak</button>
        <input type="submit" class="btn btn-danger" value="Ya, Reset Password Pengguna"></input>
      </div>
    </form>
    </div>
  </div>
</div>

<script type="text/javascript">
//CSRF Token
$('#csrfTambah').attr('name','<?= $csrf['name'] ?>');
$('#csrfTambah').val('<?= $csrf['hash'] ?>');
$('#csrfEdit').attr('name','<?= $csrf['name'] ?>');
$('#csrfEdit').val('<?= $csrf['hash'] ?>');

$("#reset-password-form").on( "submit", function( event ) {
  event.preventDefault();
  $.get( "<?= base_url() ?>user/generatePassword", { id: $('#id_user_reset').val() } )
  .done(function( data ) {
    let obj = JSON.parse(data);
    $('#resetModal').modal('hide');
    $('#edit_password_default').val(obj.pass);
    $('.password_default').removeAttr('hidden');
    showNotif('primary', obj.message);
    updateCSRF(obj);
  });
});
</script>
