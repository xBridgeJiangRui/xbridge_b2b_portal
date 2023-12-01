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

.cell_breakWord{
  word-break: break-all;
  max-width: 1px;
}

</style>
<div class="content-wrapper" style="min-height: 525px;">
<div class="container-fluid">
<br>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Extend Logs</h3><br>
          <div class="box-tools pull-right">
            <?php if(in_array('IAVA',$this->session->userdata('module_code')))
            {
            ?>

            <!-- <button id="create_duration" type="button" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus" aria-hidden="true" ></i> Create</button> -->

            <?php
            }
            ?>
            <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
          </div>
        </div>
          <div class="box-body">
            <table class="table table-bordered table-hover" id="log_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
              <thead style="white-space: nowrap;">
                <tr>
                  <!-- <th>Action</th> -->
                  <th>Retailer Name</th> 
                  <th>Supplier Name</th>
                  <th>User ID</th>
                  <th>Current Invoice Number</th>
                  <th>Current Doc Key</th>
                  <th>Extended Doc Key</th>
                  <!-- <th>Status</th> -->
                  <th>Created At</th>
                  <th>Created By</th>
                  <th>Extend Until</th>
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
          <h3 class="box-title">Reminder Status Logs</h3><br>
          <div class="box-tools pull-right">
            <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
          </div>
        </div>
          <div class="box-body">
            <table class="table table-bordered table-hover" id="reminder_status_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
              <thead style="white-space: nowrap;">
                <tr>
                  <th>Retailer Name</th> 
                  <th>Supplier Name</th>
                  <th>Old Variance</th>
                  <th>New Variance</th>
                  <th>Created At</th>
                  <th>Created By</th>
                  <th>Action</th>
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
  $('#log_table').DataTable({
    "columnDefs": [
    // {"targets": 0 ,"orderable": false},
    { "width": "20%", "targets": 4 }
    ],
    "serverSide": true, 
    'processing'  : true,
    'paging'      : true,
    'lengthChange': true,
    'lengthMenu'  : [ [10, 25, 50, 9999999], [10, 25, 50, 'All'] ],
    'searching'   : true,
    'ordering'    : true, 
    'order'       : [  [6 , 'desc'] ],
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
        "url": "<?php echo site_url('Query_outstanding/extend_log_tb');?>",
        "type": "POST",
    },
    columns: [
            // { "data" : "guid" ,render:function( data, type, row ){

            //   var element = '';

            //   <?php if($_SESSION['user_group_name'] == "SUPER_ADMIN" || $_SESSION['user_group_name'] == 'CUSTOMER_ADMIN_TESTING_USE')
            //   {
            //     ?>
                  
            //     element += '<button id="edit_duration" type="button"  title="EDIT" class="btn btn-sm btn-info" guid="'+row['guid']+'" customer_guid="'+row['customer_guid']+'" supplier_guid="'+row['supplier_guid']+'" day_limit="'+row['day_limit']+'" ><i class="fa fa-edit"></i></button>';

            //     element += '<button id="delete_button" type="button" style="margin-left:5px;" title="DELETE" class="btn btn-sm btn-danger" guid="'+row['guid']+'" ><i class="fa fa-trash"></i></button>';

            //     <?php
            //   }
            //   ?>

            //   return element;
            // }},
            { "data" : "acc_name" },
            { "data" : "supplier_name" },
            { "data" : "user_id" },
            { data: "invoice_number", render: function(data, type, row){ 
              var element = '';

              element += '<span class="cell_breakWord">'+data+'</span>';

              return element;
            }},
            { "data" : "current_dockey" },
            { "data" : "dockey" },
            { "data" : "created_at" },
            { "data" : "created_by" },
            { "data" : "extend_until_at" },
            
          ],
    dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'Brtip',
    buttons: [
      { extend: 'excelHtml5'},

        ],
    // "pagingType": "simple",
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      //$(nRow).attr('final_amount', aData['final_amount']);
      // if(aData['current_dockey'] != aData['dockey'] )
      // {
      //   $(nRow).find('td:eq(3)').css({"background-color":"#97f792","color":"black"});
      // }
      // else if (aData['current_dockey']  == aData['dockey'] )
      // {
      //   $(nRow).find('td:eq(3)').css({"background-color":"#4287f5","color":"black"});
      // }
      // else
      // {
      //   $(nRow).find('td:eq(3)').css({"background-color":"#fce512","color":"black"});
      // }
    },
    "initComplete": function( settings, json ) {
      setTimeout(function(){
        interval();
      },300);
    }
  });//close datatable

  $('#reminder_status_table').DataTable({
    "columnDefs": [
    {"targets": 6 ,"orderable": false},
    // { "width": "20%", "targets": 4 }
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
        "url": "<?php echo site_url('Query_outstanding/reminder_status_log_tb');?>",
        "type": "POST",
    },
    columns: [
            { "data" : "acc_name" },
            { "data" : "supplier_name" },
            { "data" : "old_variance" },
            { "data" : "new_variance" },
            { "data" : "created_at" },
            { "data" : "created_by" },
            { "data" : "guid" ,render:function( data, type, row ){

              var element = '';

              <?php if($_SESSION['user_group_name'] == "SUPER_ADMIN" || $_SESSION['user_group_name'] == 'CUSTOMER_ADMIN_TESTING_USE')
              {
                ?>
                  
                element += '<button id="view_invoice_btn" type="button"  title="EDIT" class="btn btn-xs btn-warning" guid="'+row['guid']+'" ><i class="fa fa-file"></i> Invoice Number</button>';


                <?php
              }
              ?>

              return element;
            }},
            
          ],
    dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'Brtip',
    buttons: [
      { extend: 'excelHtml5'},
    ],
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      //$(nRow).attr('final_amount', aData['final_amount']);
    },
    "initComplete": function( settings, json ) {
      setTimeout(function(){
        interval();
      },300);
    }
  });//close datatable

  $(document).on('click','#view_invoice_btn',function(){
    var guid = $(this).attr('guid');

    if(guid == '' || guid == null || guid == 'null')
    {
      alert('Please contact handsome developer.');
      return;
    }

    $.ajax({
      url:"<?php echo site_url('Query_outstanding/view_invoice_log') ?>",
      method:"POST",
      data:{guid:guid},
      beforeSend:function(){
        $('.btn').button('loading');
      },
      success:function(data)
      {
        json = JSON.parse(data);

        var modal = $("#medium-modal").modal();

        modal.find('.modal-title').html('Invoice Number');

        methodd = '';

        methodd +='<div class="row"> <div class="col-md-12"> <div class="box "> <div class="box-body"> <table id="vc_table" class="table table-bordered table-hover " width="100%" cellspacing="0"> <thead style="white-space: nowrap;"> <tr> <th>Invoice Number</th> </tr> </thead> </table> </div> </div> </div> </div>';

        methodd_footer = '<p class="full-width"><span class="pull-right"> <input name="close_btn" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

        modal.find('.modal-footer').html(methodd_footer);
        modal.find('.modal-body').html(methodd);
      
        setTimeout(function(){
          if ($.fn.DataTable.isDataTable('#vc_table')) {
              $('#vc_table').DataTable().destroy();
          }

          $('#vc_table').DataTable({
            "columnDefs": [
            ],
            'processing'  : true,
            'paging'      : true,
            'lengthChange': true,
            'lengthMenu'  : [ [10, 25, 50, 9999999999999999], [10, 25, 50, "ALL"] ],
            'searching'   : true,
            'ordering'    : true,
            'order'       : [ [0 , 'asc'] ],
            'info'        : true,
            'autoWidth'   : true,
            "bPaginate": true, 
            "bFilter": true, 
            "sScrollY": "50vh", 
            "sScrollX": "100%", 
            "sScrollXInner": "100%", 
            "bScrollCollapse": true,
              data: json['data'],
              columns: [
                { "data": "invoice_number"},
                ],
              dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>rtip", 
            "language": {
              "lengthMenu": "Show _MENU_",
              "infoEmpty": "No records available",
              "infoFiltered": "(filtered from _MAX_ total records)",
              "zeroRecords": "<span><?php echo '<b>No Record Found.</b>'; ?></span>",
            }, 
            "fnCreatedRow": function( nRow, aData, iDataIndex ) {

              // $(nRow).attr('status', aData['status']);
            },
            "initComplete": function( settings, json ) {
              setTimeout(function(){
                interval();
              },300);
            }
          });//close datatable
        },300);
        
        $('.btn').button('reset');
      }//close success
    });//close ajax 
  });

});
</script>

