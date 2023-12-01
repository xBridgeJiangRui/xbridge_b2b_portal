<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="container-fluid">
    <br>
    <?php
    if ($this->session->userdata('message')) {
    ?>
      <div class="alert alert-success text-center" style="font-size: 18px">
        <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
      </div>
    <?php
    }
    ?>

    <?php
    if ($this->session->userdata('warning')) {
    ?>
      <div class="alert alert-danger text-center" style="font-size: 18px">
        <?php echo $this->session->userdata('warning') <> '' ? $this->session->userdata('warning') : ''; ?>
        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
      </div>
    <?php
    }
    ?>

    <div class="col-md-12">
      <?php if ($this->session->flashdata('noconnection')) : ?>
        <strong>
          <center><?php echo $this->session->flashdata('noconnection'); ?></center>
        </strong>
      <?php endif; ?>
    </div>


    <div class="col-md-12">

      <a class="btn btn-app" href="<?php echo site_url($_SESSION['frommodule']) ?>">
        <i class="fa fa-search"></i> Browse
      </a>
      <a class="btn btn-app" href="<?php echo site_url('login_c/location') ?>">
        <i class="fa fa-bank"></i> Outlet
      </a>

    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header with-border">

            <h3 class="box-title"><?php echo $title;
                                  echo ' <br>Ref No : ';
                                  echo $check_status->row('refno');
                                  echo ' - ';
                                  echo ucfirst($check_status->row('status'));  ?></h3><br>

            <!-- <?php echo $title_accno ?> -->
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button> -->
            </div>
          </div>
          <?php // echo $file_path; 
          ?>
          <div class="box-body">
            <div class="col-md-12">
              <div class="col-md-12" style="overflow-x:auto;overflow-y:auto">
                <div class="box-body" id="acc_concepts">
                  <div id="accconceptCheck">
                    <embed id="embed" height="750px" width="100%" src="<?= $request_link; ?>"></embed>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php  // echo var_dump($_SESSION); 
    ?>
  </div>
</div>