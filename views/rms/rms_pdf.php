<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" >
<div class="container-fluid">
<br>
  <?php
  if($this->session->userdata('message'))
  {
    ?>
    <div class="alert alert-success text-center" style="font-size: 18px">
    <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
  <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
  </div>
    <?php
  }
  ?>

  <?php
  if($this->session->userdata('warning'))
  {
    ?>
    <div class="alert alert-danger text-center" style="font-size: 18px">
    <?php echo $this->session->userdata('warning') <> '' ? $this->session->userdata('warning') : ''; ?>
  <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
  </div>
    <?php
  }
  ?>

<div class="col-md-12">
<?php if($this->session->flashdata('noconnection')): ?>
<strong><center><?php echo $this->session->flashdata('noconnection'); ?></center></strong>
<?php endif; ?>
</div>


  <div class="col-md-12">
    <a class="btn btn-app" href="<?php echo site_url('panda_rms').'?code='.$doctype?>">
      <i class="fa fa-search"></i> Browse
    </a>
  </div>

  <?php foreach($doc_mapping as $doc){ ?>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo rawurldecode($title).' | '.$refno.' | '.$doc['file_refno'].' - '.$status;?></h3><br>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="col-md-12">
            <div class="col-md-12" style="overflow-x:auto;overflow-y:auto"> 

            <?php 
              $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
              if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'android')) {
            ?>
              <embed src="<?php echo site_url('Panda_rms/readfile').'?refno='.urlencode($doc['file_refno']).'&code='.$doctype.'&supcode='.$supcode; ?>" width="100%" height="500px" style="border: none;"/>

            <?php  } else { ?>

              <embed src="<?php echo site_url('Panda_rms/readfile').'?refno='.urlencode($doc['file_refno']).'&code='.$doctype.'&supcode='.$supcode; ?>" width="100%" height="500px" style="border: none;"/>

              If browser does not support PDFs. Please download the PDF to view it: <a href="<?php echo site_url('Panda_rms/read_64').'?refno='.$doc['file_refno'].'&code='.$doctype.'&supcode='.$supcode; ?>" download>Download PDF</a>

            <?php } ?>
 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>

</div>
</div>
