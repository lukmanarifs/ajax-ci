<!-- partial:../../partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <div class="nav-link">
        <div class="user-wrapper">
          <div class="profile-image">
            <img src="<?php echo base_url()?>public/images/avatar.png" alt="profile image">
          </div>
          <div class="text-wrapper">
            <p class="profile-name"><?= $this->session->userdata('user_username')?></p>
            <div>
              <small class="designation text-muted"><?= $this->session->userdata('user_kategori')?></small>
              <span class="status-indicator online"></span>
            </div>
          </div>
        </div>
      </div>
    </li>

    <li class="nav-item">
    <?php if ($page == 'nelayan' || $page == 'user'): ?>
      <a class="nav-link" data-toggle="collapse" href="#menu-daftar" aria-expanded="true" aria-controls="ui-basic">
    <?php else: ?>
      <a class="nav-link" data-toggle="collapse" href="#menu-daftar" aria-expanded="false" aria-controls="ui-basic">
    <?php endif; ?>
        <i class="menu-icon mdi mdi-content-copy"></i>
        <span class="menu-title">Daftar</span>
        <i class="menu-arrow"></i>
      </a>
  <?php if ($page == 'nelayan' || $page == 'user'): ?>
    <div class="collapse show" id="menu-daftar">
  <?php else: ?>
    <div class="collapse" id="menu-daftar">
  <?php endif; ?>
      <ul class="nav flex-column sub-menu">
        <li class="nav-item">
          <a class="nav-link <?= $page == 'nelayan' ? 'active' : '' ?>" href="<?php echo base_url()?>daftar/nelayan">Pelaku Usaha</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $page == 'user' ? 'active' : '' ?>" href="<?php echo base_url()?>user">Pengguna</a>
        </li>
      </ul>
    </div>
    </li>
  </ul>
</nav>
<!-- partial -->
