<tr>
  <td>
    <div class="input-group input-group-icon" style="width:150px;">
      <select class="form-control select2 kode-item'+x+'" id="tambah_tipe'+x+'" name="tambah_tipe[]" style="width: 75%">
        <option value="">-- Pilih Tipe --</option>
      </select>
    </div>
  </td>
  <td>
    <input class="kode-item'+x+' form-control" name="kode_item[]">
    <input type="hidden" class="nama-item'+x+'" name="nama_item[]">
  </td>
  <td>
    <input type="text"  class="form-control nama-item'+x+'">
  </td>
  <td>
    <input type="text"  class="form-control nama-item'+x+'">
  </td>
  <td>
    <input type="text" name="tgl_expired[]"  class="form-control tgl_expired">
  </td>
  <td>
    <input type="text" name="harga[]" class="form-control mask_price'+x+'" required>
  </td>
  <td>
    <input type="text" name="satuan_besar[]" size="3" class="form-control satuan-besar'+x+'">
  </td>
  <td>
    <a href="javascript:void(0);" class="mb-xs mt-xs mr-xs btn btn-danger deleterow">
      <i class="fa fa-trash-o"></i>
    </a>
  </td>
</tr>
