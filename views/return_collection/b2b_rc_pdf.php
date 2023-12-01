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
       <a class="btn btn-app" href="<?php echo site_url('b2b_strb/strb_list') ?>">
          <i class="fa fa-search"></i> Browse
        </a>
        <a class="btn btn-app" href="<?php echo site_url('login_c/location')?>">
          <i class="fa fa-bank"></i> Outlet
        </a>
        <?php 
        if($uploaded_image == '1')
        {
          ?>
          <a class="btn btn-app" id="view_image" style="float:right";>
          <i class="fa fa-file-image-o"></i> Image
          </a>
          <?php
        }
        ?>
        <?php if(in_array('VRB', $_SESSION['module_code'])) { 
        if($get_current_status == '0' && $set_disabled == '0' ) { ?>
                <button title="Confirm"  onclick="confirm_modal2('<?php echo site_url('B2b_strb/confirm'); ?>?refno=<?php echo $_REQUEST['refno'] ?>&customer_guid=<?php echo $_SESSION['customer_guid'] ?>&loc=<?php echo $_REQUEST['loc'] ?>')" 
                type="button" class="btn btn-app" style="color:#008D4C"  data-toggle="modal" data-target="#confirm_gr" data-name="<?php echo $_REQUEST['refno'] ?>"
                <?php if(isset($_REQUEST['edit'])) { echo 'disabled'; } ?>
                >
                                <i class="fa fa-check"></i>Accept
                </button>
                >
        <?php } } ?>
  </div>

  <div class="row">
      <div class="col-md-12">
          <div class="box box-default  ">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo 'Item Detail'; ?></h3> 
               
              <div class="box-tools pull-right " style="display: inline-flex;">
              <?php if($hide_button == '0') { ?>
              <button class="btn btn-xs btn-success" onclick="$('#formSBNC').submit()"   ><i class="glyphicon glyphicon-floppy-saved"></i>  Submit </button><br>
            <?php } ?>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
      <div class="box-body">
        <center><div class="col-12"><h4><b style="color: red">Please liaise with RETAILER if disagree with quantity and cost of the item to be returned</b></h4></div></center>
            <div class="col-md-12"  style="overflow-x:auto;overflow-y:auto"> 
                 <div style="overflow-x:auto;">
                    <table id="smstable" class="tablesorter table table-striped table-bordered table-hover"> 
                        <thead>
                          <tr>
                            <th>No.</th>
                            <th>Itemcode/<br>Barcode</th> 
                            <th>Description</th>
                            <th>PS / Ctn Qty</th>
                            <th>Quantity</th>
                            <th>Cost</th>
                            <th>Amount</th>
                            <th>Reason</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php 
                        foreach($check_child as $row => $value)
                        {
                          ?>
                           
                            <tr>
                              <td><?php echo $value['line']?>
                                <input type="hidden" name="line[]" value="<?php echo $value['line'] ?>">
                              </td>
                              <td><?php echo $value['itemcode']?>/<br><?php echo $value['barcode']?>
                                <input type="hidden" name="itemcode[]" value="<?php echo $value['itemcode'] ?>">
                                <input type="hidden" name="barcode[]" value="<?php echo $value['barcode'] ?>">
                              </td>
                             
                              <td><?php echo $value['description']?>
                                <input type="hidden" name="description[]" value="<?php echo $value['description'] ?>">
                              </td>
                              <td><?php echo $value['packsize']; ?>
                                <input type="hidden" name="packsize[]" value="<?php echo $value['packsize'] ?>">
                              </td>
                             
                              <td><?php echo $value['qty'];echo ' '; echo $value['um'];?>
                                <input type="hidden" name="qty[]" value="<?php echo $value['qty'] ?>">
                                <input type="hidden" name="um[]" value="<?php echo $value['um'] ?>">
                              </td>
                             
                              <td style="text-align:right"><?php echo number_format($value['input_cost'],2); ?>
                                <input type="hidden" name="input_cost[]" value="<?php echo $value['input_cost'] ?>"> 
                              </td>
                              <td style="text-align:right">
                                <?php echo number_format($value['qty']*$value['input_cost'],2); ?>
                              </td>
                              <td><?php echo $value['reason']; ?>
                                <input type="hidden" name="reason[]" value="<?php echo $value['reason'] ?>">
                              </td>
                            </tr>
                          <?php
                        }    
                        ?>
                          <input type='hidden' name='h_refno' id="h_refno"   value="<?php echo $_REQUEST['refno'] ?>"> 
                          <input type='hidden' name='location' id="location"   value="<?php echo $_REQUEST['loc'] ?>"> 
                        </tbody>
                      </table>
                    </div> 
                </div>
            </div>
          </div>
        </div>
      </div>  

  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><?php  echo $title; ?></h3><br>
            <!-- <?php echo $title_accno ?> -->
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button> -->
          </div>
        </div>
      <div class="box-body">
        <div class="col-md-12">
            <div class="col-md-12"  style="overflow-x:auto"> 
                <div class="col-md-12"  style="overflow-x:auto;overflow-y:auto"> 
                    <embed id="embed" height="750px" width="100%" src="<?= $request_link; ?>"></embed>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
</div>
<?php //  echo var_dump($_SESSION); ?>
</div>
</div>
<script type="text/javascript">
  function confirm_modal2(confirm_url)
  {
    $('#confirm_gr').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) 

    var modal = $(this)
    modal.find('.modal_detail').text('Confirm Collection ' + button.data('name') + '?')
    document.getElementById('url_confirm').setAttribute("href" , confirm_url );
    });
  }

</script>
<script>
$(document).ready(function () {    
  setTimeout(function(){
    $('#large-modal').attr({"data-backdrop":"static","data-keyboard":"false"}); //to remove clicking outside modal for closing
  },300);

  $(document).on('click','#view_image',function(){
    var refno = "<?php echo $_REQUEST['refno'];?>";
    var period_code = "<?php echo $_REQUEST['pc'];?>";
    var image_type = 'STRB'

	  //alert(image_type); die;
    if((refno == '') || (refno == null) || (refno == 'null'))
    {
      alert('Invalid Get STRB RefNo.');
      return;
    }

    if((period_code == '') || (period_code == null) || (period_code == 'null'))
    {
      alert('Invalid Get Period Code.');
      return;
    }

    if((image_type == '') || (image_type == null) || (image_type == 'null'))
    {
      alert('Invalid Get Image Details.');
      return;
    }

    $.ajax({
        url:"<?php echo site_url('B2b_strb/strb_view_image') ?>",
        method:"POST",
        data:{refno:refno,period_code:period_code,image_type:image_type},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            var url = json.file_path_list;
            var name = url.toString().substring(url.lastIndexOf('/') + 1);
            
            var modal = $("#large-modal").modal();

            modal.find('.modal-title').html( 'STRB RefNo : <b>' + refno + '</b>');

            methodd = '';

            //methodd +='<div class="col-md-12">';

            //methodd += '<input type="hidden" class="form-control input-sm" id="supplier_guid" value="'+supplier_guid+'" readonly/>';

            Object.keys(url).forEach(function(key) {

              var before_name = url[key].toString().split('?')[0];
              var after_name = before_name.toString().split("/").slice(-1)[0];
              
              methodd += '<div class="col-md-12"><label>'+after_name+'</label>';

              methodd += '<input style="float:right;" type="button" id="show'+key+'" class="btn btn-primary view_image_btn" value="Show" path_url="'+url[key]+'" key="'+key+'">';

              methodd += '<input style="float:right;margin-right:5px;" type="button" id="show'+key+'" class="btn btn-warning dl_image_btn" value="Download" path_url="'+url[key]+'" key="'+key+'">';

              methodd += '</div>';

              methodd += '<div class="clearfix"></div><br>';

              methodd += '<div class="col-md-12"><span id="image'+key+'"></span></div>';

            });

            //methodd += '</div>';

            methodd_footer = '<p class="full-width"><span class="pull-right"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

            modal.find('.modal-footer').html(methodd_footer);
            modal.find('.modal-body').html(methodd);

            setTimeout(function(){

              $(document).off('click', '.view_image_btn').on('click', '.view_image_btn', function(){
                var path_url = $(this).attr('path_url');
                var key = $(this).attr('key');
                var value = $(this).val();
                $(this).hide();
                
                if(value == 'Show') 
                {
                  //$(this).attr('value','Hide');
                  //$(this).attr('class','btn btn-danger view_image_btn');
                  $('#image'+key+'').html('<embed src="'+path_url+'" width="100%" height="800px" style="border: none;" toolbar="0" id="image_view'+key+'"/>');
                  //alert('show image'); die;
                }
                
                // if(value == 'Hide') 
                // {
                //   $(this).attr('value','Show');
                //   $(this).attr('class','btn btn-primary view_image_btn');
                //   $('#image'+key+'').html('');
                //   //alert('show image'); die;
                // }
              });//close modal create

              $(document).off('click', '.dl_image_btn').on('click', '.dl_image_btn', function(){
                var path_url = $(this).attr('path_url');
                var value = $(this).val();
                
                if(value == 'Download') 
                {
                  var form = document.createElement('a');
                  form.href = path_url;
                  form.download = path_url;
                  document.body.appendChild(form);
                  form.click();
                  alert('Download Successful'); 
                  $(this).attr('value','Download Complete');
                  $(this).prop('disabled', true);
                } 
              });//close modal create
            },300);
          }//close else
        }//close success
      });//close ajax 
  });//close image process
});
</script>
