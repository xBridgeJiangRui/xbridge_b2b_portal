<style>
.content-wrapper{
  min-height: 850px !important; 
}

.alignright {
  text-align: right;
}

.alignleft
{
  text-align: left;
}
</style>
<div class="content-wrapper" style="min-height: 525px;">
<div class="container-fluid">
<br>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Reminder Duration Settings</h3><br>
          <div class="box-tools pull-right">
            <?php if(in_array('IAVA',$this->session->userdata('module_code')))
            {
            ?>

            <button id="create_duration" type="button" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus" aria-hidden="true" ></i> Create</button>

            <?php
            }
            ?>
            <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
          </div>
        </div>
          <div class="box-body">
            <table class="table table-bordered table-hover" id="reminder_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
              <thead style="white-space: nowrap;">
                <tr>
                  <th>Action</th>
                  <th>Retailer Name</th> 
                  <th>Supplier Name</th>
                  <th>Duration Day(s)</th>
                  <th>Created At</th>
                  <th>Created By</th>
                  <th>Updated At</th>
                  <th>Updated By</th>
                </tr>
              </thead>
              <tbody> 
              </tbody>

            </table>
          </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Set New Email Reminder</h3><br>
          <div class="box-tools pull-right">
            <?php if(in_array('IAVA',$this->session->userdata('module_code')))
            {
            ?>

            <button id="create_email_setting" type="button" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus" aria-hidden="true" ></i> Create</button>

            <?php
            }
            ?>
            <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
          </div>
        </div>
          <div class="box-body">
            <table class="table table-bordered table-hover" id="email_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
              <thead style="white-space: nowrap;">
                <tr>
                  <th>Action</th>
                  <th>Retailer Name</th> 
                  <th>Supplier Name</th>
                  <th>Email Address</th>
                  <th>Created At</th>
                  <th>Created By</th>
                </tr>
              </thead>
              <tbody> 
              </tbody>

            </table>
          </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Excluded Retailer Send Block</h3><br>
          <div class="box-tools pull-right">
            <?php if(in_array('IAVA',$this->session->userdata('module_code')))
            {
            ?>

            <button id="create_email_exclude" type="button" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus" aria-hidden="true" ></i> Create</button>

            <?php
            }
            ?>
            <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
          </div>
        </div>
          <div class="box-body">
            <table class="table table-bordered table-hover" id="exclude_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
              <thead style="white-space: nowrap;">
                <tr>
                  <th>Action</th>
                  <th>Retailer Name</th> 
                  <th>Supplier Name</th>
                  <th>Created At</th>
                  <th>Created By</th>
                </tr>
              </thead>
              <tbody> 
              </tbody>

            </table>
          </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Reminder Extend Settings</h3><br>
          <div class="box-tools pull-right">
            <?php if(in_array('IAVA',$this->session->userdata('module_code')))
            {
            ?>

            <button id="create_extend" type="button" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus" aria-hidden="true" ></i> Create</button>

            <?php
            }
            ?>
            <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
          </div>
        </div>
          <div class="box-body">
            <table class="table table-bordered table-hover" id="reminder_extend_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
              <thead style="white-space: nowrap;">
                <tr>
                  <th>Action</th>
                  <th>Retailer Name</th> 
                  <th>Supplier Name</th>
                  <th>Email Address</th>
                  <th>Created At</th>
                  <th>Created By</th>
                  <th>Active</th>
                </tr>
              </thead>
              <tbody> 
              </tbody>

            </table>
          </div>
      </div>
    </div>

  </div>
</div>
</div>

<script>
$(document).ready(function () {    
  $('#reminder_table').DataTable({
    "columnDefs": [
    {"targets": 0 ,"orderable": false},
    ],
    "serverSide": true, 
    'processing'  : true,
    'paging'      : true,
    'lengthChange': true,
    'lengthMenu'  : [ [10, 25, 50, 9999999], [10, 25, 50, 'All'] ],
    'searching'   : true,
    'ordering'    : true, 
    'order'       : [  [1 , 'asc'] ],
    'info'        : true,
    'autoWidth'   : false,
    "bPaginate": true, 
    "bFilter": true, 
    "fixedColumns": true,
    "sScrollY": "50vh", 
    "sScrollX": "100%", 
    "sScrollXInner": "100%", 
    "bScrollCollapse": true,
    "ajax": {
        "url": "<?php echo site_url('Query_outstanding/duration_tb');?>",
        "type": "POST",
    },
    columns: [
            { "data" : "guid" ,render:function( data, type, row ){

              var element = '';

              <?php if($_SESSION['user_group_name'] == "SUPER_ADMIN" || $_SESSION['user_group_name'] == 'CUSTOMER_ADMIN_TESTING_USE')
              {
                ?>
                  
                element += '<button id="edit_duration" type="button"  title="EDIT" class="btn btn-sm btn-info" guid="'+row['guid']+'" customer_guid="'+row['customer_guid']+'" supplier_guid="'+row['supplier_guid']+'" day_limit="'+row['day_limit']+'" ><i class="fa fa-edit"></i></button>';

                element += '<button id="delete_button" type="button" style="margin-left:5px;" title="DELETE" class="btn btn-sm btn-danger" guid="'+row['guid']+'" ><i class="fa fa-trash"></i></button>';

                <?php
              }
              ?>

              return element;
            }},
            { "data" : "acc_name" },
            { "data" : "supplier_name" },
            { "data" : "day_limit" },
            { "data" : "created_at" },
            { "data" : "created_by" },
            { "data" : "updated_at" },
            { "data" : "updated_by" },
          ],
    dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'rtip',
    // buttons: [
    //   { extend: 'excelHtml5',
    //     exportOptions: {columns: [1,2,3,4,5,6]}},

    //   { extend: 'csvHtml5',  
    //     exportOptions: {columns: [1,2,3,4,5,6]}},
    //     ],
    // "pagingType": "simple",
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      //$(nRow).attr('final_amount', aData['final_amount']);
      
    },
    "initComplete": function( settings, json ) {
      setTimeout(function(){
        interval();
      },300);
    }
  });//close datatable

  $(document).on('click','#create_duration',function(){

    var modal = $("#medium-modal").modal();

    modal.find('.modal-title').html('Create New Setting');

    methodd = '';

    methodd += '<div class="col-md-12">';

    methodd += '<div class="col-md-12"><label>Retailer Name</label> <select class="form-control select2 get_code" name="new_retailer" id="new_retailer"> <option value="" disabled selected>-Select Retailer Name-</option>  <?php foreach($get_acc as $row) { ?> <option value="<?php echo $row->acc_guid?>"><?php echo addslashes($row->acc_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Supplier Name</label> <select class="form-control select2 get_code" name="new_supplier" id="new_supplier"> <option value="" disabled selected>-Select Supplier Name-</option>  <?php foreach($get_supplier as $row) { ?> <option value="<?php echo $row->supplier_guid?>"><?php echo addslashes($row->supplier_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Duration Day(s)</label> <input type="number" class="form-control input-sm" id="add_day" name="add_day" autocomplete="off"/> </div>';

    methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-left"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"></span><span class="pull-right"><button type="button" id="submit_button" class="btn btn-primary"> Create </button></span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    setTimeout(function(){
      $('.select2').select2();
    },300);

  });//close modal create

  $(document).on('click','#submit_button',function(){
    var new_retailer = $('#new_retailer').val();
    var new_supplier = $('#new_supplier').val();
    var add_day = $('#add_day').val();

    if((new_retailer == '') || (new_retailer == null) || (new_retailer == 'null'))
    {
      alert('Invalid Retailer Name.');
      return;
    }

    if((new_supplier == '') || (new_supplier == null) || (new_supplier == 'null'))
    {
      alert('Invalid Supplier Name.');
      return;
    }

    if((add_day == '') || (add_day == null) || (add_day == 'null'))
    {
      alert('Invalid Day Duration.');
      return;
    }

    confirmation_modal('Are you sure to proceed Create?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/add_duration') ?>",
        method:"POST",
        data:{new_retailer:new_retailer,new_supplier:new_supplier,add_day:add_day},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation

  });//close submit process

  $(document).on('click','#edit_duration',function(){
    var guid = $(this).attr('guid');
    var customer_guid = $(this).attr('customer_guid');
    var supplier_guid = $(this).attr('supplier_guid');
    var day_limit = $(this).attr('day_limit');

    var modal = $("#medium-modal").modal();

    modal.find('.modal-title').html('Edit Duration Setting');

    methodd = '';

    methodd += '<div class="col-md-12">';

    methodd += '<div class="col-md-12"><input type="hidden" class="form-control input-sm" id="guid" name="guid" value="'+guid+'" readonly/> </div>';

    methodd += '<div class="col-md-12"><label>Retailer Name</label> <select class="form-control select2 get_code" name="edit_retailer" id="edit_retailer"> <option value="" disabled selected>-Select Retailer Name-</option>  <?php foreach($get_acc as $row) { ?> <option value="<?php echo $row->acc_guid?>"><?php echo addslashes($row->acc_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Supplier Name</label> <select class="form-control select2 get_code" name="edit_supplier" id="edit_supplier"> <option value="" disabled selected>-Select Supplier Name-</option>  <?php foreach($get_supplier as $row) { ?> <option value="<?php echo $row->supplier_guid?>"><?php echo addslashes($row->supplier_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Duration Day(s)</label> <input type="number" class="form-control input-sm" id="edit_day" name="edit_day" autocomplete="off"/> </div>';

    methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-left"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"></span><span class="pull-right"><button type="button" id="update_button" class="btn btn-primary"> Update </button></span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    setTimeout(function(){
      $('#edit_retailer').val(customer_guid);
      $('#edit_supplier').val(supplier_guid);
      $('#edit_day').val(day_limit);
      $('.select2').select2();
    },300);

  });//close modal edit

  $(document).on('click','#update_button',function(){
    var guid = $('#guid').val();
    var edit_retailer = $('#edit_retailer').val();
    var edit_supplier = $('#edit_supplier').val();
    var edit_day = $('#edit_day').val();

    if((edit_retailer == '') || (edit_retailer == null) || (edit_retailer == 'null'))
    {
      alert('Invalid Retailer Name.');
      return;
    }

    if((edit_supplier == '') || (edit_supplier == null) || (edit_supplier == 'null'))
    {
      alert('Invalid Supplier Name.');
      return;
    }

    if((edit_day == '') || (edit_day == null) || (edit_day == 'null'))
    {
      alert('Invalid Day Duration.');
      return;
    }

    confirmation_modal('Are you sure to proceed Update?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/update_duration') ?>",
        method:"POST",
        data:{guid:guid,edit_retailer:edit_retailer,edit_supplier:edit_supplier,edit_day:edit_day},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation
  });//close update process

  $(document).on('click','#delete_button',function(){
    var guid = $(this).attr('guid');

    if((guid == '') || (guid == null) || (guid == 'null'))
    {
      alert('Invalid GUID. Contact Handsome Developer');
      return;
    }

    confirmation_modal('Are you sure to proceed Delete?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/delete_duration') ?>",
        method:"POST",
        data:{guid:guid},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation
  });//close update process

  // email setting table
  $('#email_table').DataTable({
    "columnDefs": [
    {"targets": 0 ,"orderable": false},
    ],
    "serverSide": true, 
    'processing'  : true,
    'paging'      : true,
    'lengthChange': true,
    'lengthMenu'  : [ [10, 25, 50, 9999999], [10, 25, 50, 'All'] ],
    'searching'   : true,
    'ordering'    : true, 
    'order'       : [  [1 , 'asc'] ],
    'info'        : true,
    'autoWidth'   : false,
    "bPaginate": true, 
    "bFilter": true, 
    "fixedColumns": true,
    "sScrollY": "50vh", 
    "sScrollX": "100%", 
    "sScrollXInner": "100%", 
    "bScrollCollapse": true,
    "ajax": {
        "url": "<?php echo site_url('Query_outstanding/email_setting_tb');?>",
        "type": "POST",
    },
    columns: [
            { "data" : "supplier_guid" ,render:function( data, type, row ){

              var element = '';

              <?php if($_SESSION['user_group_name'] == "SUPER_ADMIN" || $_SESSION['user_group_name'] == 'CUSTOMER_ADMIN_TESTING_USE')
              {
                ?>
  
                element += '<button id="delete_email_button" type="button" style="margin-left:5px;" title="DELETE" class="btn btn-sm btn-danger" customer_guid="'+row['customer_guid']+'" supplier_guid="'+row['supplier_guid']+'"><i class="fa fa-trash"></i></button>';

                <?php
              }
              ?>

              return element;
            }},
            { "data" : "acc_name" },
            { "data" : "supplier_name" },
            { "data" : "new_email" },
            { "data" : "created_at" },
            { "data" : "created_by" },
          ],
    dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'rtip',
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      //$(nRow).attr('final_amount', aData['final_amount']);
    },
    "initComplete": function( settings, json ) {
      setTimeout(function(){
        interval();
      },300);
    }
  });//close datatable

  $(document).on('click','#delete_email_button',function(){
    var customer_guid = $(this).attr('customer_guid');
    var supplier_guid = $(this).attr('supplier_guid');

    if((customer_guid == '') || (customer_guid == null) || (customer_guid == 'null'))
    {
      alert('Invalid GUID. Contact Handsome Developer');
      return;
    }

    if((supplier_guid == '') || (supplier_guid == null) || (supplier_guid == 'null'))
    {
      alert('Invalid GUID. Contact Handsome Developer');
      return;
    }
    //alert('Opps Please Call handsome Jiang Rui'); die;
    confirmation_modal('Are you sure to proceed Delete?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/delete_email_reminder') ?>",
        method:"POST",
        data:{customer_guid:customer_guid,supplier_guid:supplier_guid},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation
  });//close update process

  $(document).on('click','#create_email_setting',function(){

    var modal = $("#medium-modal").modal();

    modal.find('.modal-title').html('Create New Email Reminder');

    methodd = '';

    methodd += '<div class="col-md-12">';

    methodd += '<div class="col-md-12"><label>Retailer Name</label> <select class="form-control select2" name="email_new_retailer" id="email_new_retailer"> <option value="" disabled selected>-Select Retailer Name-</option>  <?php foreach($get_acc as $row) { ?> <option value="<?php echo $row->acc_guid?>"><?php echo addslashes($row->acc_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Supplier Name</label> <select class="form-control select2" name="email_new_supplier" id="email_new_supplier"> <option value="" disabled selected>-Select Supplier Name-</option>  <?php foreach($get_supplier as $row) { ?> <option value="<?php echo $row->supplier_guid?>"><?php echo addslashes($row->supplier_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>New Email</label> <input type="text" class="form-control input-sm" id="add_email" name="add_email" autocomplete="off"/> </div>';

    methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-left"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"></span><span class="pull-right"><button type="button" id="email_submit_button" class="btn btn-primary"> Create </button></span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    setTimeout(function(){
      $('.select2').select2();
    },300);

  });//close modal create

  $(document).on('click','#email_submit_button',function(){
    var email_new_retailer = $('#email_new_retailer').val();
    var email_new_supplier = $('#email_new_supplier').val();
    var add_email = $('#add_email').val();

    if((email_new_retailer == '') || (email_new_retailer == null) || (email_new_retailer == 'null'))
    {
      alert('Invalid Retailer Name.');
      return;
    }

    if((email_new_supplier == '') || (email_new_supplier == null) || (email_new_supplier == 'null'))
    {
      alert('Invalid Supplier Name.');
      return;
    }

    if((add_email == '') || (add_email == null) || (add_email == 'null'))
    {
      alert('Please input email address.');
      return;
    }

    //alert('Opps Please Call handsome Jiang Rui'); die;
    confirmation_modal('Are you sure to proceed Create?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/add_email') ?>",
        method:"POST",
        data:{email_new_retailer:email_new_retailer,email_new_supplier:email_new_supplier,add_email:add_email},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation

  });//close submit process

  // retailer exclude table
  $('#exclude_table').DataTable({
    "columnDefs": [
    {"targets": 0 ,"orderable": false},
    ],
    "serverSide": true, 
    'processing'  : true,
    'paging'      : true,
    'lengthChange': true,
    'lengthMenu'  : [ [10, 25, 50, 9999999], [10, 25, 50, 'All'] ],
    'searching'   : true,
    'ordering'    : true, 
    'order'       : [  [1 , 'asc'] ],
    'info'        : true,
    'autoWidth'   : false,
    "bPaginate": true, 
    "bFilter": true, 
    "fixedColumns": true,
    "sScrollY": "50vh", 
    "sScrollX": "100%", 
    "sScrollXInner": "100%", 
    "bScrollCollapse": true,
    "ajax": {
        "url": "<?php echo site_url('Query_outstanding/exclude_tb');?>",
        "type": "POST",
    },
    columns: [
            { "data" : "supplier_guid" ,render:function( data, type, row ){

              var element = '';

              <?php if($_SESSION['user_group_name'] == "SUPER_ADMIN" || $_SESSION['user_group_name'] == 'CUSTOMER_ADMIN_TESTING_USE')
              {
                ?>

                element += '<button id="delete_exclude_button" type="button" style="margin-left:5px;" title="DELETE" class="btn btn-sm btn-danger" customer_guid="'+row['customer_guid']+'" supplier_guid="'+row['supplier_guid']+'"><i class="fa fa-trash"></i></button>';

                <?php
              }
              ?>

              return element;
            }},
            { "data" : "acc_name" },
            { "data" : "supplier_name" },
            { "data" : "created_at" },
            { "data" : "created_by" },
          ],
    dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'rtip',
    // buttons: [
    //   { extend: 'excelHtml5',
    //     exportOptions: {columns: [1,2,3,4,5,6]}},

    //   { extend: 'csvHtml5',  
    //     exportOptions: {columns: [1,2,3,4,5,6]}},
    //     ],
    // "pagingType": "simple",
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      //$(nRow).attr('final_amount', aData['final_amount']);
      
    },
    "initComplete": function( settings, json ) {
      setTimeout(function(){
        interval();
      },300);
    }
  });//close datatable

    // reminder extend settings table
    $('#reminder_extend_table').DataTable({
    "columnDefs": [
    {"targets": 0 ,"orderable": false},
    ],
    "serverSide": true, 
    'processing'  : true,
    'paging'      : true,
    'lengthChange': true,
    'lengthMenu'  : [ [10, 25, 50, 9999999], [10, 25, 50, 'All'] ],
    'searching'   : true,
    'ordering'    : true, 
    'order'       : [  [4 , 'desc'] ],
    'info'        : true,
    'autoWidth'   : false,
    "bPaginate": true, 
    "bFilter": true, 
    "fixedColumns": true,
    "sScrollY": "50vh", 
    "sScrollX": "100%", 
    "sScrollXInner": "100%", 
    "bScrollCollapse": true,
    "ajax": {
        "url": "<?php echo site_url('Query_outstanding/extend_s');?>",
        "type": "POST",
    },
    columns: [
            { "data" : "guid" ,render:function( data, type, row ){

              var element = '';

              <?php if($_SESSION['user_group_name'] == "SUPER_ADMIN" || $_SESSION['user_group_name'] == 'CUSTOMER_ADMIN_TESTING_USE')
              {
                ?>
                  
                element += '<button id="edit_extend_b" type="button"  title="EDIT" class="btn btn-sm btn-info" guid="'+row['guid']+'" customer_guid="'+row['customer_guid']+'" supplier_guid = "'+row ['supplier_guid']+'" user_guid ="'+row['user_guid']+'" ><i class="fa fa-edit"></i></button>';

                element += '<button id="delete_extend_b" type="button" style="margin-left:5px;" title="DELETE" class="btn btn-sm btn-danger" guid="'+row['guid']+'" ><i class="fa fa-trash"></i></button>';

                <?php
              }
              ?>

              return element;
            }},
            { "data" : "acc_name" },
            { "data" : "supplier_name" },
            { "data" : "user_id" },
            { "data" : "created_at" },
            { "data" : "created_by" },
            { "data" : "isactive", render: function(data, type, row){ 
                var element = '';

                if(data == '1')
                {
                    element = 'Yes';
                }
                else
                {
                    element = 'No';
                }

                return element;
            }},
          ],
          dom: "<'row'<'col-sm-2'l><'col-sm-4'><'col-sm-6'f>>Brtip",
          buttons: [
              'excel'
          ],

    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      
    },
    "initComplete": function( settings, json ) {
      setTimeout(function(){
        interval();
      },300);
    }
  });//close datatable

  $(document).on('click','#create_extend',function(){

    var modal = $("#medium-modal").modal();

    modal.find('.modal-title').html('Create New');

    methodd = '';

    methodd += '<div class="col-md-12">';

    methodd += '<div class="col-md-12"><label>Retailer Name<span style="color:red">*</span></label> <select class="form-control select2 get_code1" name="new_retailer" id="new_retailer"> <option value="" disabled selected>-Select Retailer Name-</option>  <?php foreach($get_acc as $row) { ?> <option value="<?php echo $row->acc_guid?>"><?php echo addslashes($row->acc_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Supplier Name<span style="color:red">*</span></label> <select class="form-control select2 get_code1" name="new_supplier" id="new_supplier"> <option value="" disabled selected>-Select Supplier Name-</option>  <?php foreach($get_supplier as $row) { ?> <option value="<?php echo $row->supplier_guid?>"><?php echo addslashes($row->supplier_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Email<span style="color:red">*</span></label> <select class="form-control select2" name="email" id="email" required multiple> </select> </div>';

    methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-left"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"></span><span class="pull-right"><button type="button" id="submit_button1" class="btn btn-primary"> Create </button></span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    setTimeout(function(){
      $('#new_retailer').select2();
      $('#new_supplier').select2();
      $('#email').select2();
    },300);


    $('.get_code1').change(function() {
        
        var selectedretailer = $('#new_retailer').val();
        var selectedsupplier = $('#new_supplier').val();

        // Checking if all the required fields have values
        if (selectedretailer && selectedsupplier) {
            // AJAX request to fetch data
            $.ajax({
                url: "<?php echo site_url('Query_outstanding/fetch_user_id'); ?>",
                method: "POST",
                data: {
                    type_val: selectedretailer,
                    type_val1: selectedsupplier
                },
                success: function (result) {
                    // Handling the data returned from the AJAX request
                    var emailSelect = $('#email');
                    var json = JSON.parse(result);

                    var emailOptions = '';

                    // Populating the email dropdown with the retrieved data
                    if (json.email.length > 0) {
                        $.each(json.email, function (key, value) {
                            emailOptions += '<option value="' + value.user_guid + '">' + value.user_id + '</option>';
                        });
                    } else {
                        emailOptions += '<option value="" disabled>No data available</option>';
                    }

                    emailSelect.empty().html(emailOptions);     
                }
            });
        } else {
            // Resetting the email dropdown to the default option when the required fields are not fully selected
            var emailSelect = $('#email');
            var emailOptions = '';
            emailSelect.empty().html(emailOptions);
        }
      });

    });//close modal create

    $(document).on('click','#submit_button1',function(){
    var new_retailer = $('#new_retailer').val();
    var new_supplier = $('#new_supplier').val();
    var email = $('#email').val();

    if((new_retailer == '') || (new_retailer == null) || (new_retailer == 'null'))
    {
      alert('Please Choose Retailer Name.');
      return;
    }

    if((new_supplier == '') || (new_supplier == null) || (new_supplier == 'null'))
    {
      alert('Please Choose Supplier Name.');
      return;
    }

    if((email == '') || (email == null) || (email == 'null'))
    {
      alert('Please Choose Email ID.');
      return;
    }

    confirmation_modal('Are you sure to proceed Create?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/add_new_setting') ?>",
        method:"POST",
        data:{new_retailer:new_retailer,new_supplier:new_supplier,email:email},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation
  });//close submit process

    //modal for edit button in reminder extend settings
    $(document).on('click','#edit_extend_b',function(){
    var guid = $(this).attr('guid');
    var customer_guid = $(this).attr('customer_guid');
    var supplier_guid = $(this).attr('supplier_guid');
    var user_guid = $(this).attr('user_guid');

    var modal = $("#medium-modal").modal();

    modal.find('.modal-title').html('Edit');

    methodd = '';

    methodd += '<div class="col-md-12">';

    methodd += '<div class="col-md-12"><input type="hidden" class="form-control input-sm" id="guid" name="guid" value="'+guid+'" readonly/> </div>';

    methodd += '<div class="col-md-12"><label>Retailer Name<span style="color:red">*</span></label> <select class="form-control select2 get_code1" name="edit_retailer" id="edit_retailer"> <option value="" disabled selected>-Select Retailer Name-</option>  <?php foreach($get_acc as $row) { ?> <option value="<?php echo $row->acc_guid?>"><?php echo addslashes($row->acc_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Supplier Name<span style="color:red">*</span></label> <select class="form-control select2 get_code1" name="edit_supplier" id="edit_supplier"> <option value="" disabled selected>-Select Supplier Name-</option>  <?php foreach($get_supplier as $row) { ?> <option value="<?php echo $row->supplier_guid?>"><?php echo addslashes($row->supplier_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Email<span style="color:red">*</span></label> <select class="form-control select2" name="email" id="email" required> </select> </div>';

    methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-left"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"></span><span class="pull-right"><button type="button" id="update_extend_button" class="btn btn-primary"> Update </button></span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    setTimeout(function(){
      $('#edit_retailer').val(customer_guid).trigger('change').select2();
      $('#edit_supplier').val(supplier_guid).trigger('change').select2();
      $('#email').val(user_guid).trigger('change').select2();
    },300);

    $('.get_code1').change(function() {
      
      var selectedretailer = $('#edit_retailer').val();
      var selectedsupplier = $('#edit_supplier').val();

      // Checking if all the required fields have values
      if (selectedretailer && selectedsupplier) {
          // AJAX request to fetch data
          $.ajax({
              url: "<?php echo site_url('Query_outstanding/fetch_user_id'); ?>",
              method: "POST",
              data: {
                  type_val: selectedretailer,
                  type_val1: selectedsupplier
              },
              success: function (result) {
                  // Handling the data returned from the AJAX request
                  var emailSelect = $('#email');
                  var json = JSON.parse(result);

                  var emailOptions = '';

                  // Populating the email dropdown with the retrieved data
                  if (json.email.length > 0) {
                      $.each(json.email, function (key, value) {
                          emailOptions += '<option value="' + value.user_guid + '">' + value.user_id + '</option>';
                      });
                  } else {
                      emailOptions += '<option value="" disabled>No data available</option>';
                  }

                  emailSelect.empty().html(emailOptions);
                  
                  if (user_guid) {
                    emailSelect.val(user_guid).trigger('change');
                  }
              }
          });
      } 
    });


  });//close modal edit

  $(document).on('click','#update_extend_button',function(){
    var guid = $('#guid').val();
    var edit_retailer = $('#edit_retailer').val();
    var edit_supplier = $('#edit_supplier').val();
    var edit_email = $('#email').val();

    if((edit_retailer == '') || (edit_retailer == null) || (edit_retailer == 'null'))
    {
      alert('Invalid Retailer Name.');
      return;
    }

    if((edit_supplier == '') || (edit_supplier == null) || (edit_supplier == 'null'))
    {
      alert('Invalid Supplier Name.');
      return;
    }

    if((edit_email == '') || (edit_email == null) || (edit_email == 'null'))
    {
      alert('Invalid Email ID.');
      return;
    }

    confirmation_modal('Are you sure to proceed Update?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/update_extend') ?>",
        method:"POST",
        data:{guid:guid,edit_retailer:edit_retailer,edit_supplier:edit_supplier,edit_email:edit_email},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation
  });//close update process

    //modal for delete button in reminder extend settings
    $(document).on('click','#delete_extend_b',function(){
    var guid = $(this).attr('guid');

    if((guid == '') || (guid == null) || (guid == 'null'))
    {
      alert('Invalid GUID. Please Contact Handsome Developer');
      return;
    }

    confirmation_modal('Are you sure to proceed Delete?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/delete_extend') ?>",
        method:"POST",
        data:{guid:guid},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation
  });//close update process

  $(document).on('click','#delete_exclude_button',function(){
    var customer_guid = $(this).attr('customer_guid');
    var supplier_guid = $(this).attr('supplier_guid');

    if((customer_guid == '') || (customer_guid == null) || (customer_guid == 'null'))
    {
      alert('Invalid GUID. Contact Handsome Developer');
      return;
    }

    if((supplier_guid == '') || (supplier_guid == null) || (supplier_guid == 'null'))
    {
      alert('Invalid GUID. Contact Handsome Developer');
      return;
    }
    //alert('Opps Please Call handsome Jiang Rui'); die;
    confirmation_modal('Are you sure to proceed Delete?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/delete_exclude_reminder') ?>",
        method:"POST",
        data:{customer_guid:customer_guid,supplier_guid:supplier_guid},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation
  });//close update process

  $(document).on('click','#create_email_exclude',function(){

    var modal = $("#medium-modal").modal();

    modal.find('.modal-title').html('Create New Email Reminder');

    methodd = '';

    methodd += '<div class="col-md-12">';

    methodd += '<div class="col-md-12"><label>Retailer Name</label> <select class="form-control select2" name="exclude_new_retailer" id="exclude_new_retailer"> <option value="" disabled selected>-Select Retailer Name-</option>  <?php foreach($get_acc as $row) { ?> <option value="<?php echo $row->acc_guid?>"><?php echo addslashes($row->acc_name) ?></option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-12"><label>Supplier Name</label> <select class="form-control select2" name="exclude_new_supplier" id="exclude_new_supplier"> <option value="" disabled selected>-Select Supplier Name-</option>  <?php foreach($get_supplier as $row) { ?> <option value="<?php echo $row->supplier_guid?>"><?php echo addslashes($row->supplier_name) ?></option> <?php } ?></select> </div>';

    methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-left"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"></span><span class="pull-right"><button type="button" id="exclude_submit_button" class="btn btn-primary"> Create </button></span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    setTimeout(function(){
      $('.select2').select2();
    },300);

  });//close modal create

  $(document).on('click','#exclude_submit_button',function(){
    var exclude_new_retailer = $('#exclude_new_retailer').val();
    var exclude_new_supplier = $('#exclude_new_supplier').val();

    if((exclude_new_retailer == '') || (exclude_new_retailer == null) || (exclude_new_retailer == 'null'))
    {
      alert('Invalid Retailer Name.');
      return;
    }

    if((exclude_new_supplier == '') || (exclude_new_supplier == null) || (exclude_new_supplier == 'null'))
    {
      alert('Invalid Supplier Name.');
      return;
    }

    //alert('Opps Please Call handsome Jiang Rui'); die;
    confirmation_modal('Are you sure to proceed Create?');

    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Query_outstanding/add_exclude') ?>",
        method:"POST",
        data:{exclude_new_retailer:exclude_new_retailer,exclude_new_supplier:exclude_new_supplier},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(data)
        {
          json = JSON.parse(data);
          if (json.para1 == 'false') {
            $('#alertmodal').modal('hide');
            alert(json.msg);
            $('.btn').button('reset');
          }else{
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.msg);
            location.reload();
          }//close else
        }//close success
      });//close ajax 
    });//close confirmation

  });//close submit process


});
</script>

