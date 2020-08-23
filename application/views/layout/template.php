<?php $this->load->view("layout/header");?>

<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    <?php $this->load->view("layout/navbar");?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view("layout/sidebar");?>
      <?php $this->load->view($content);?>
      <?php $this->load->view("layout/footer");?>
