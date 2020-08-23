<div class="main-panel">
          <div class="content-wrapper">
            <div class="row purchace-popup">
            </div>
            <div class="row">
              <div class="col-12 stretch-card">
                  <div class="card shadow-nohover">
                  <div class="card-header">Form Data Pengguna</div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-6">
                          <!-- <h4 class="card-title">Form Data Pengguna</h4> -->
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                        </div>
                        <div class="col-lg-6">
                        <a href="#"  data-toggle="modal" data-target="#modalTambahData" class="btn btn-raised btn-primary m-t-15 waves-effect float-right shadow-nohover" style="margin-top: -5px; margin-left: 5px;">Tambah</a>
                    </div>
                    </div>
                      <!-- <img src="{{ Storage::url($kc1600->gambar) }}" title="kc1600"> -->
                      <!-- <div id="table-data"></div> -->
                      <div class="row">
                        <div class="col-lg-12">
                          <div style="margin-top: 20px;">
                            <table  class="table table-hover" id="perikanan_table" class="display">
                              <thead>
                                  <tr>
                                      <th>Username</th>
                                      <th>Fullname</th>
                                      <th>Role</th>
                                      <th>Action</th>
                                  </tr>
                              </thead>
                          </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php $this->load->view('content/user/create')?>
            <?php $this->load->view('content/user/edit')?>

          <!-- content-wrapper ends -->
<!-- <div id="modal-kc1600"></div>-->
<div id="modal-confirm-delete"></div>

<script type="text/javascript">
  $(document).ready(function() {
    showTable();
  });

  function showTable(){
    var oTable = $('#perikanan_table').DataTable({
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false,
        "processing": true,
        "ajax": "<?php echo base_url()?>user/ajaxList",
        "language": {
            "lengthMenu": "Menampilkan _MENU_ Data Per Kolom",
            "sSearch": "Cari: ",
            "sProcessing": "Dalam Proses...",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan Kolom _PAGE_ Dari _PAGES_",
            "infoEmpty": "Tidak ada Data",
            "infoFiltered": "(Disaring dari _MAX_ total Data)",
            "oPaginate": {
                "sFirst": "Awal",
                "sLast": "Akhir",
                "sNext": ">>",
                "sPrevious": "<<"
            }
        },
        "columnDefs" : [
          { "sortable": false, "targets": [1 ] },
          { "bSearchable": false, "aTargets": [ 1 ] },
          { "className": "text-center", "targets": [ 1, 2 ] }
        ]
      });

      $("#perikanan_table_filter input")
      .css('text-transform', 'uppercase')
      .focus();

      $('#perikanan_table_filter input').unbind();
      $('#perikanan_table_filter input').bind('keyup', function(e) {
          if(e.keyCode == 13) {
           oTable.search(this.value).draw();
       }
      });

      $('#perikanan_table').tooltip({selector: '[data-toggle="tooltip"]'});
  }

  function show_confirmation(data_id){

    $.ajax({
       type: 'POST',
       url: '<?= base_url() ?>user/delete',
       dataType: 'html',
       // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
       data: {id:data_id, csrf_token:$('#csrfTambah').val() },

       success: function (data) {
              $('#modal-confirm-delete').html(data);
              $('#exampleModal').modal('show');
       },
       error: function (data) {
            console.log(data);
       }
    });
  }

  function editForm(idVal){
    $.get( "<?= base_url() ?>user/edit", { id: idVal } )
    .done(function( data ) {
      let obj = JSON.parse(data);
      $('#modalEditData').modal('show');
      $('#id').val(obj.id);
      $('#edit_username').val(obj.username);
      $('#edit_fullname').val(obj.fullname);
      $('#edit_role').val(obj.kategori);
    });
  }

  function processForm(action,func){
    $.ajax({
       type: 'POST',
       url: '<?= base_url()?>user/'+func,
       dataType: 'html',
       // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
       data: $( '#form'+action ).serialize(),

       success: function (data) {
         let obj = JSON.parse(data);
         if (obj['status'] == 'success') {
           $('#modal'+action+'Data').modal('hide');
           // $('body').removeClass('modal-open');
           $('.modal-backdrop').remove();
           $('#perikanan_table').DataTable().clear().destroy();
           showTable();
           showNotif('primary', obj['message']);
           $('#form'+action).trigger("reset");
           updateCSRF(obj);

         }else if(obj['status'] == 'failed'){
           updateCSRF(obj);
           showNotif('danger', obj['message']);

         }else{

           $.each(obj, function(key, value) {
             if (value != "") {
               $('#' + key).addClass('is-invalid');
               $('#' + key).parents('.form-group').find('#error').html(value);
             }
           });
           updateCSRF(obj);
         }
       },
       error: function (data) {
            console.log(data);
       }
    });

    $('#form'+action+' input').on('keyup', function () {
        $(this).removeClass('is-invalid');
        $(this).parents('.form-group').find('#error').html(" ");
    });
  }


</script>
