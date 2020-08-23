<div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper auth-page">
        <div class="content-wrapper d-flex align-items-center auth register-bg-1 theme-one">
          <div class="row w-100">
            <div class="col-lg-4 mx-auto">
              <h2 class="text-center mb-4">Reset Password</h2>
              <div class="auto-form-wrapper">
                <form method="POST" action="<?= base_url() ?>login/updatePassword">
                  <input type="hidden" id="csrfTambah" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>">
                  <input type="hidden" id="id" name="id" value="<?= $id ?>">
                  <div class="form-group">
                    <label class="label">Password</label>
                    <div class="input-group">
                      <input id="password" type="password" class="form-control" name="password" required autofocus>
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="mdi mdi-check-circle-outline"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="label">Confirm Password</label>
                    <div class="input-group">
                      <input id="confirm_password" type="password" class="form-control" name="confirm_password" required>
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="mdi mdi-check-circle-outline"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <button class="btn btn-primary submit-btn btn-block">Simpan Password</button>
                    <a href="<?= base_url() ?>dashboard/logout" class="btn btn-default submit-btn btn-block" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                  </div>
                </form>
                <form id="logout-form" action="<?= base_url() ?>dashboard/logout" method="POST" style="display: none;">
                  <input type="hidden" id="csrfLogout" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>">
                </form>
              </div>
              <p class="footer-text text-center">copyright © 2019 Alton. All rights reserved.</p>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->

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
    <script src="<?php echo base_url()?>public/js/vendor.bundle.addons.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="<?php echo base_url()?>public/js/off-canvas.js"></script>
    <script src="<?php echo base_url()?>public/js/misc.js"></script>
    <script src="<?php echo base_url()?>public/js/bootstrap-notify.min.js"></script>
