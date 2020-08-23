<!-- content-wrapper ends -->
<!-- partial:../../partials/_footer.html -->
<footer class="footer">
  <div class="container-fluid clearfix">
    <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 2019
      <a href="#" target="_blank">Dinas Perikanan Kab. Sumenep</a>. All rights reserved.</span>
    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Developed by .
      <!-- <i class="mdi mdi-heart text-danger"></i>  -->
      <img class="logo-footer" src="<?php echo base_url()?>public/images/altonlogo.svg" alt="logo" />
      <a href="https://www.alt-on.net" target="_blank">Alton</a>
    </span>
  </div>
</footer>
<!-- partial -->
</div>
<!-- main-panel ends -->
</div>
<!-- page-body-wrapper ends -->
</div>
<script type="text/javascript">
<?php if ($this->session->flashdata('danger')){?>
    $(function() {
        $.notify({
          message: '<?php echo $this->session->flashdata('danger');?>'
        },{
          type: 'danger',
          offset: {
            x: 25,
            y: 75
          }
        });
      });
    <?php } ?>
  <?php if ($this->session->flashdata('message')){?>
    $(function() {
        $.notify({
          message: '<?php echo $this->session->flashdata('message');?>'
        },{
          type: 'primary',
          offset: {
            x: 25,
            y: 75
          }
        });
      });
    <?php } ?>
  <?php if ($this->session->flashdata('warning')){?>
    $(function() {
        $.notify({
          message: '<?php echo $this->session->flashdata('warning');?>'
        },{
          type: 'warning',
          offset: {
            x: 25,
            y: 75
          }
        });
      });
    <?php } ?>
</script>
<!-- container-scroller -->
<!-- <script src="<?php //echo base_url()?>public/js/jquery.keyfilter.js"></script> -->
<script src="<?php echo base_url()?>public/js/popper.min.js"></script>
<!-- <script src="<?php echo base_url()?>public/js/bootstrap.js"></script> -->
<script src="<?php echo base_url()?>public/vendors/datatables/datatables.min.js"></script>
<script src="<?php echo base_url()?>public/js/select2.js"></script>
<script src="<?php echo base_url()?>public/js/jquery.price.format.1.7.min.js"></script>
<!-- plugins:js -->
<script src="<?php echo base_url()?>public/js/vendor.bundle.addons.js"></script>
<!-- endinject -->
<!-- Plugin js for this page-->
<!-- End plugin js for this page-->
<!-- inject:js -->
<script src="<?php echo base_url()?>public/js/off-canvas.js"></script>
<script src="<?php echo base_url()?>public/js/misc.js"></script>
<script src="<?php echo base_url()?>public/js/custom.js"></script>
<script src="<?php echo base_url()?>public/js/bootstrap-notify.min.js"></script>
<script src="<?php echo base_url()?>public/js/Chart.js"></script>
<script src="<?php echo base_url()?>public/js/chartjs-plugin-datalabels.js"></script>
<!-- <script src="<?php echo base_url()?>public/js/bootstrap-select.min.js"></script> -->
<!-- chart -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> -->

<!-- endinject -->
<!-- Custom js for this page-->
<!-- End custom js for this page-->
</body>


</html>
