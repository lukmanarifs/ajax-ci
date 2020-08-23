<div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-12 stretch-card">
                  <div class="card shadow-nohover">
                  <div class="card-header">Form Data Pelaku Usaha Perikanan</div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-6">
                          <!-- <h4 class="card-title">Form Data Nelayan</h4> -->
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">

                        </div>
                        <div class="col-lg-6">
                          <a href="#"  data-toggle="modal" data-target="#modalTambahData" class="btn btn-raised btn-primary m-t-15 waves-effect float-right shadow-nohover" style="margin-top: -5px; margin-left: 5px;">Tambah</a>
                        </div>
                      </div>
                      <div style="margin-top: 20px;" class="table-responsive">
                        <table  class="table table-hover" id="perikanan_table" class="display">
                          <thead>
                              <tr>
                                  <th></th>
                                  <th>Nama RTP</th>
                                  <th>NIK</th>
                                  <th>Alamat</th>
                                  <th>Desa</th>
                                  <th>Kecamatan</th>
                                  <th>Kelompok</th>
                                  <th>Pilihan</th>
                              </tr>
                          </thead>
                      </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <div id="modal-confirm-delete"></div>

          <?php $this->load->view('content/daftar/nelayan/create')?>
          <?php $this->load->view('content/daftar/nelayan/edit')?>

<script type="text/javascript">
  $(document).ready(function() {
    showTable();
    //Select2 Settings
    $('.select2-tambah').select2({
      dropdownParent: $("#modalTambahData"),
      theme: 'bootstrap'
    });
    $('.select2-edit').select2({
      dropdownParent: $("#modalEditData"),
      theme: 'bootstrap'
    });
    $('.select2').on('select2:close',function () {
        $(this).focus();
      }
    );
    $('.select2').on('select2:select', function (e) {
      let id = $(this).attr('id');
      let selectVal = $(this).val();
      let action = id.split('_')[0];
      if (id == 'tambah_kecamatan' || id == 'edit_kecamatan') {
        $.get( "<?php echo base_url()?>daftar/nelayan/getDesa", { kecamatan: selectVal } )
        .done(function( data ) {
          let obj = JSON.parse(data);
          var $dropdown = $("#"+action+"_desa");
          $("#"+action+"_desa").empty()
            .append("<option value=''>-- Pilih Desa --</option>");
          $.each(obj, function() {
              $dropdown.append($("<option />").val(this.desa_kode).text(this.desa_nama));
          });
        });
      }
      $(this).removeClass('is-invalid');
      $(this).parents('.form-group').find('#error').html(" ");
    });
  });


  function showConfirmation(data_id){
    $.ajax({
       type: 'POST',
       url: '<?php echo base_url()?>daftar/nelayan/delete',
       dataType: 'html',
       // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
       data: {id:data_id, csrf_token:$('#csrfTambah').val() },

       success: function (data) {
              console.log(data);
              $('#modal-confirm-delete').html(data);
              $('#exampleModal').modal('show');
       },
       error: function (data) {
            console.log(data);
       }
    });
  }
  function showTable(){
    var oTable = $('#perikanan_table').DataTable({
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false,
        "processing": true,
        "scrollX": true,
        "ajax": "<?php echo base_url()?>daftar/nelayan/ajaxList",
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
        "order" : [[0, 'DESC']],
        "columnDefs" : [
          { "sortable": false, "targets": [ 5 ] },
          { "className": "text-center", "targets": [1, 5 ] },
          { "targets": [ 0 ], "visible": false, "searchable": false},
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

  function processForm(action,func){
    $.ajax({
       type: 'POST',
       url: '<?= base_url()?>daftar/nelayan/'+func,
       dataType: 'html',
       // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
       data: $( '#form'+action ).serialize(),

       success: function (data) {
         let obj = JSON.parse(data);
         updateCSRF(obj);
         if (obj['status'] == 'success') {
           $('#modal'+action+'Data').modal('hide');
           // $('body').removeClass('modal-open');
           $('.modal-backdrop').remove();
           $('#perikanan_table').DataTable().clear().destroy();
           showTable();
           showNotif('primary', obj['message']);
           $('#form'+action).trigger("reset");

         }else if(obj['status'] == 'failed'){
           showNotif('danger', obj['message']);

         }else{
           $.each(obj, function(key, value) {
             if (value != "") {
               $('#' + key).addClass('is-invalid');
               $('#' + key).parents('.form-group').find('#error').html(value);
             }
           });
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
  function updateCSRF(obj){
    //CSRF Token
    $('#csrfTambah').attr('name',obj['csrf']['name']);
    $('#csrfTambah').val(obj['csrf']['hash']);
    $('#csrfEdit').attr('name',obj['csrf']['name']);
    $('#csrfEdit').val(obj['csrf']['hash']);
  }
</script>
