$(function() {
  $('.numeric').inputFilter(function(value){
    return /^[0-9.]*$/i.test(value);
  });

  $('.alphanumeric').inputFilter(function(value){
    return /^[0-9a-zA-Z ]*$/i.test(value);
  });

  $('.alphanumericsym').inputFilter(function(value){
    return /^[0-9a-zA-Z() -]*$/i.test(value);
  });

  $('.alphanumericsym1').inputFilter(function(value){
    return /^[0-9a-zA-Z() -]*$/i.test(value);
  });

  $(".formtab").keydown(function (e) {
      if(e.which == 13 || e.which == 38 || e.which == 40 ) {
           e.preventDefault();
           if(($(this).attr("type") == "submit" || $(this).attr("type") == "button") && e.which == 13 ){
              $(this).click();
              return;
           }
           var n = $(".formtab").length;
           var nextIndex = $(".formtab").index(this) + 1;
           var prevIndex = $(".formtab").index(this) - 1;

           if(nextIndex < n && (e.which == 13 || e.which == 40)) {
              // $('.formtab')[nextIndex].focus();
              $('.formtab').select2('open');
           }
           if (prevIndex >= 0 && e.which == 38){
              $('.formtab')[prevIndex].focus();
           }
      }
  });

  $(document).on('show.bs.modal', '.modal', function (event) {
          var zIndex = 1040 + (10 * $('.modal:visible').length);
          $(this).css('z-index', zIndex);
          setTimeout(function() {
              $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
          }, 0);
  });
});

function htmlDecode(data){
    var txt=document.createElement('textarea');
    txt.innerHTML=data;
    return txt.value
}
function numberFormat(id){
  $('#'+id).priceFormat({prefix: '',
      centsSeparator: '',
      thousandsSeparator: '.',
      limit: 15,
      centsLimit: 0
    });
}
function commaFormat(id){
  $('#'+id).priceFormat({prefix: '',
      centsSeparator: ',',
      thousandsSeparator: '.',
      limit: 15,
      centsLimit: 2
    });
}
function priceFormat(id){
  $('#'+id).priceFormat({prefix: 'Rp ',
      centsSeparator: '',
      thousandsSeparator: '.',
      limit: 15,
      centsLimit: 0
    });
}

function autoCount(val1, val2, hasil, x ){
  let value1 = $('#'+val1+x).val().split('.').join('');
  value1 = value1.split(',').join('.');
  let value2 = $('#'+val2+x).val().split('.').join('');
  $('#'+hasil+x).val(value1 * value2);
  numberFormat(hasil+x);
}

function showNotif(status, message_val){
  $(function() {
      $.notify({
        message: message_val
      },{
        type: status,
        offset: {
          x: 25,
          y: 75
        },
        z_index: 2000,
      });
    });
}
function updateCSRF(obj){
  //CSRF Token
  $('#csrfTambah').attr('name',obj['csrf']['name']);
  $('#csrfTambah').val(obj['csrf']['hash']);
  $('#csrfEdit').attr('name',obj['csrf']['name']);
  $('#csrfEdit').val(obj['csrf']['hash']);
}

(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      }
    });
  };
}(jQuery));
