<style>
.content-wrapper{
  min-height: 800px !important; 
}

.blinker {
  animation: blink-animation 5s steps(10, start) infinite;
  -webkit-animation: blink-animation 2s steps(10, start) infinite;
  background-color: yellow;
  color:red;
}

@keyframes blink-animation {
  to {
    visibility: hidden;
  }
}
@-webkit-keyframes blink-animation {
  to {
    visibility: hidden;
  }
}

</style>
<div class="content-wrapper">
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
 <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ --> 
 <!-- Info 2 boxes -->
 <?php // echo var_dump($_SESSION) ?>
      <div class="row">
        <div class="col-md-12">
          <div class="box box-default">
            <!-- head -->
              <div class="box-header with-border">
                <h3 class="box-title">Data Overview From <?php echo $date_from; ?> To  <?php echo $date_to; ?></h3><br>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
              </div>
              <div class="box-body">
              <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h3><?php echo $pomain ?></h3>

                      <p>New Purchase Order</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-list"></i>
                    </div>
                    <a href="<?php echo $redirect_pomain ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-green">
                    <div class="inner">
                      <h3><?php echo $grmain ?><!-- <sup style="font-size: 20px">%</sup> --></h3>

                      <p>New Goods Received Notes</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-check"></i>
                    </div>
                    <?php if(in_array('VGR',$_SESSION['module_code']) )
                    { ?>
                    <a href="<?php echo $redirect_grmain ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>

                    <?php } else { ?>

                      <a href="<?php echo $redirect_grmain_download ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>

                   <?php } ?>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-yellow">
                    <div class="inner">
                      <h3><?php echo $grda ?></h3>

                      <p>GR Difference Advise</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-times"></i>
                    </div>
                    <a href="<?php echo $redirect_grda ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <!-- ./col -->
                <?php if(in_array('VRB',$_SESSION['module_code']) && $strb_valid_dashboard == '1') 
                {
                  ?>
                  
                  <div class="col-lg-3 col-xs-6">
                  <div class="small-box bg-purple">
                    <div class="inner">
                      <span class="blinker">
                      <h3><?php echo $strb ?></h3>
                      </span>
                      <p>Stock Return Batch Document</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-th-list"></i>
                    </div>
                      <a href="<?php echo $redirect_strb ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                  </div>
                </div>

              
                  <?php
                }
                ?>
        </div>
        <!-- ./col -->
      </div>
    </div>
  </div>
   <!-- row2 end -->
   <!-- row3 Start -->
      <div class="row">
        <!-- left panel -->
        <div class="col-md-6">
          <div class="box box-default">
            <!-- head -->
              <div class="box-header with-border">
                <h3 class="box-title">Latest announcement</h3><br>
                <div class="box-tools pull-right">
                  <a href="<?php echo site_url('dashboard/previous_announcement') ?>" class="btn btn-primary btn-xs">Read More <i class="fa fa-arrow-circle-right"></i></a>
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
              </div>
              <div class="box-body">
                 <!-- content --> 
                 <ul class="timeline timeline-inverse">
                  <!-- timeline time label -->
                  <li class="time-label">
                        <span class="bg-red">
                          <?php echo $announcement->row('docdate'); ?>
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-envelope bg-blue"></i>

                    <div class="timeline-item">
                      <h3 class="timeline-header"><?php echo $announcement->row('title'); ?></h3>

                      <div class="timeline-body">
                        <?php echo $announcement->row('content'); ?>
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                  </li>
                </ul>
              </div>
          </div>
        </div>
        <!-- right panel -->

      <div class="col-md-6">
          <div class="box box-default">
            
              <div class="box-header with-border">
                <h3 class="box-title">Quick Acknowledgements</h3><br>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
              </div>
              <div class="box-body">
                  <?php foreach($show_announcement_sidebar->result() as $row){ ?>
                    <a class="btn"  onclick="ack_modal()"
                    data-toggle="modal" data-target="#selected_modal"
                    data-announcement_guid="<?php echo $row->announcement_guid;?>"
                    data-title="<?php echo $row->title;?>"
                    data-content="<?php echo $row->content;?>"
                    data-pdf_status="<?php echo $row->pdf_status;?>"
                    data-user_guid="<?php echo $_SESSION['user_guid'] ?>"
                     ><?php echo $row->title ?></a> - <?php echo $row->acknowledged_at ?><br>
                  <?php } ?>
              </div>
          </div>
        </div> 
      </div>
   <!-- row2 end -->


 <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
</div>
</div>
<style>
  .modal { overflow: auto !important; }
  </style>
<?php if($show_panel == '1') { ?>
<script type="text/javascript">
   $( document ).ready(function() {
    var mandatory = "<?php echo $mandatory;?>";
    // // if(mandatory == 1)
    // // {
    //   $('#auto_modal').attr({
    //     "data-backdrop":"static",
    //     "data-keyboard":"false"
    //   });

    // // }
    //  $('#auto_modal').modal('show');

    
      // $.ajax({
      //       url:"<?= site_url('Query_outstanding/get_outstanding');?>",
      //       method:"POST",
      //       dataType:'JSON',
      //       beforeSend:function(){
      //         // $('.btn').button('loading');
      //         $('.btn').prop('disabled',true);
      //       },
      //       success:function(data)
      //       {   
      //         var modal = $('#new-medium-modal').modal();

      //         modal.find(".modal-title").html('Reminders');              
      //         // console.log(data.string);
      //         modal.find(".modal-body").html(data.string); 
      //         $('.btn').prop('disabled',false);
      //       }//close succcess
      //     });//close ajax




    //$(document).on('click','#new-medium-modal-close',function(){
<?php foreach($check_announcement_acknowledgement->result() as $index => $row) 
{
?>
var mandatory = "<?php echo $row->mandatory;?>";
        if(mandatory == 1)
        {
          
          $('#auto_modal<?php echo $index;?>').attr({
            "data-backdrop":"static",
            "data-keyboard":"false"
          });

          // var modal = $('#auto_modal').modal();
          var modal = $('#auto_modal'+<?php echo $index;?>).modal();

          // modal.find('a').prop('href','login_c/logout');

        }
        else
        {
          //$('#auto_modal<?php echo $index;?>').removeAttr("data-backdrop");
          //$('#auto_modal<?php echo $index;?>').removeAttr("data-keyboard");


          var modal = $('#auto_modal'+<?php echo $index;?>).modal();
          // var modal = $('#auto_modal').modal();

          // modal.find('a').removeAttr("href");
          // modal.find('a').attr('data-dismiss','modal');

        }//close else
<?php
}
?>        
    //});

});
   
</script>
<?php } ?>

<script type="text/javascript">
    function checkFileExist(urlToFile) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
     
    if (xhr.status == "404") {
        return false;
    } else {
        return true;
    }
}

   function ack_modal()
  {
    $('#selected_modal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var acc_guid = "<?php echo $session_guid; ?>"; 
    //alert('123'); die;
      var modal = $(this);
      if(button.data('pdf_status') == 1)
      { 
        modal.find('[name="announcement_guid"]').val(button.data('announcement_guid'));
        modal.find('[name="user_guid"]').val(button.data('user_guid'));
        modal.find('[name="content"]').val(button.data('content'));
        modal.find('[name="title"]').val(button.data('title'));
        var guid = button.data('announcement_guid'); 

        var virtual_path = '';
        var content_name = button.data('content');
        //alert(virtual_path); 
        var cut_pdf = content_name.split('-+0+-');
        //alert(virtual_path);
        var file_name_url = ''; 
        Object.keys(cut_pdf).forEach(function(key) {
          
          // var result = checkFileExist("https://file.xbridge.my/b2b-pdf/ann_doc/"+acc_guid+"/"+guid+"/"+cut_pdf[key]+".pdf");
          // if (result == true) 
          // {
            
            virtual_path = "https://file.xbridge.my/b2b-pdf/ann_doc/"+acc_guid+"/"+guid+"/";
          // }
          // else
          // {
          //   virtual_path = "<?php echo $virtual_path.'/acceptance_form/'; ?>";
          // }

          file_name_url += '<embed src="'+virtual_path+cut_pdf[key]+'.pdf?time='+new Date().getTime()+'" class="selected_modal" width="100%" height="500px" style="border: none;"/>';

        });
         //alert(file_name_url);

        modal.find('.acceptance_title').html(button.data('title'));
        modal.find('#selected_modal_embed').html(file_name_url);
      }
      else
      {
        modal.find('[name="announcement_guid"]').val(button.data('announcement_guid'));
        modal.find('[name="user_guid"]').val(button.data('user_guid'));
        modal.find('[name="content"]').val(button.data('content'));
        modal.find('#selected_modal_embed').html('<div class="col-md-9"><textarea name="content" rows="10" cols="30" class="form-control" disabled>'+button.data('content')+'</textarea></div>');
        modal.find('.acceptance_title').html(button.data('title'));
      }
    });
  }
</script>
