<?php $this->load->view("layout/header");?>
<div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper auth-page">
        <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
          <div class="row w-100">
            <div class="col-lg-4 mx-auto">
              <!-- <span>Sistem Informasi Pelaku Perikanan Sumenep</span> -->
              <div class="auto-form-wrapper">
                <?php if(isset($error)) { echo $error; }; ?>
                <form method="POST" action="<?php echo base_url() ?>login">
                  <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>">
                  <div class="form-group">
                    <label class="label">Username</label>
                    <div class="input-group">
                      <input id="username" type="text" class="form-control" style="text-transform: uppercase;" name="username" required autofocus>
                      <?php echo form_error('username'); ?>
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="mdi mdi-check-circle-outline"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="label">Password</label>
                    <div class="input-group">
                      <input id="password" type="password" class="form-control" name="password" required>
                      <?php echo form_error('password'); ?>
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <i class="mdi mdi-check-circle-outline"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <button class="btn btn-primary submit-btn btn-block">Login</button>
                  </div>
                  <div id="error" style="margin-top: 10px"></div>
                </form>
              </div>
              
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <!-- <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('vendors/js/vendor.bundle.addons.js') }}"></script> -->
    <!-- endinject -->
    <!-- inject:js -->
    <!-- <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/misc.js') }}"></script> -->
    <!-- endinject -->
