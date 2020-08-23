<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="destroy-form" action="<?= base_url()?>user/destroy/<?=$user->id?>" method="POST">
        <input type="hidden" id="csrf_hapus" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Hapus Pengguna?</h5>
      </div>
      <div class="modal-body">
        Menghapus Data Pengguna <b></b> akan menghilangkan data tersebut secara permanen dari Aplikasi.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-delete" data-dismiss="modal">Tidak, Tetap Simpan</button>
        <input type="submit" class="btn btn-danger" value="Ya, Hapus Data Pengguna"></input>
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
</script>
