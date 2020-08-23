<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 d-flex align-items grid-margin">
        <div class="row flex-grow">
          <div class="col-6">
            <div class="card shadow-nohover">
              <div class="card-body">
                <h4 class="card-title"><strong>TABEL PELAKU USAHA</strong></h4>
                <p class="card-description">
                  Jumlah Pelaku Usaha Perikanan yang terdaftar
                </p>
                <div class="chart-container">
                  <div class="bar-chart-container">
                    <canvas id="bar-chart"></canvas>
                  </div>
                </div>
                <!-- <a href="<?php //echo base_url()?>charts/NelayanChart" class="btn btn-primary shadow-nohover">Lebih detail</a> -->
              </div>
            </div>
          </div>
          <div class="col-6 stretch-card">
            <div class="card shadow-nohover">
              <div class="card-body">
                <h4 class="card-title"><strong>TABEL BUDIDAYA</strong></h4>
                  <p class="card-description">
                    Luas lahan budidaya
                  </p>
                    <div class="chart-container">
                      <div class="bar-chart-container">
                        <canvas id="bar-chart1"></canvas>
                      </div>
                    </div>
                  <!-- <a href="<?php //echo base_url()?>charts/BudidayaChart" class="btn btn-primary shadow-nohover">Lebih detail</a> -->
              </div>
            </div>
          </div>
        </div>
      </div>
     </div>

      <div class="row">
      <div class="col-md-12 d-flex align-items grid-margin">
        <div class="row flex-grow">
          <div class="col-6">
            <div class="card shadow-nohover">
              <div class="card-body">
                <h4 class="card-title"><strong>TABEL TANGKAP</strong></h4>
                <p class="card-description">
                  Jumlah hasil tangkap
                </p>
                <div class="chart-container">
                  <div class="bar-chart-container">
                    <canvas id="bar-chart-tangkap"></canvas>
                  </div>
                </div>
                <!-- <a href="<?php //echo base_url()?>charts/tangkapchart" class="btn btn-primary shadow-nohover">Lebih detail</a> -->
              </div>
            </div>
          </div>
          <div class="col-6 stretch-card">
            <div class="card shadow-nohover">
              <div class="card-body">
                <h4 class="card-title"><strong>TABEL LAHSAR</strong></h4>
                <p class="card-description">
                  Hasil pengolahan dan pemasaran
                </p>
                <div class="chart-container">
                  <div class="bar-chart-container">
                    <canvas id="bar-chart-lahsar"></canvas>
                  </div>
                </div>
                <!-- <a href="<?php //echo base_url()?>charts/lahsarchart" class="btn btn-primary shadow-nohover">Lebih detail</a> -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="shadow col-md-6 grid-margin stretch-card">
        <div class="card shadow-nohover">
          <div class="card-body">
            <h4 class="card-title"><strong>TABEL GARAM</strong></h4>
            <p class="card-description">
              Hasil produksi Garam Rakyat per Bulan
              </p>
                <div class="chart-container">
                  <div class="bar-chart-container">
                    <!-- <canvas id="bar-chart-garam"></canvas> -->
                  </div>
                </div>
                <p class="mt-3 card-description mt-8 text-justify">

              </p>
          </div>
        </div>
      </div>
      <div class="col-md-6 grid-margin stretch-card">
        <div class="card shadow-nohover">
          <div class="card-body">
            <div class="jumbotron">
                <h1 class="display-5">Selamat datang,</br> <?= $this->session->userdata('user_fullname')?> !</h1>
                <p class="lead">Anda memiliki akses sebagai <?= $this->session->userdata('user_role')?>.</p>
                <hr class="my-3">
                <p></p>
                <p class="lead">
                  <a class="btn btn-primary btn-lg shadow-nohover" href="<?php echo base_url()?>index.php/dashboard/logout" role="button">Logout</a>
                </p>
              </div>
            </div>
        </div>
      </div>
      </div>
      </div>

  </div>
</div>
