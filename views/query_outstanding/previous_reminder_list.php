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

.loader_submit {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url("<?php echo base_url('assets/loading2.gif') ?>") center no-repeat #fff;
    /*background:   #fff;*/
}

.blinker {
  animation: blink-animation 5s steps(10, start) infinite;
  -webkit-animation: blink-animation 2s steps(10, start) infinite;
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
<div class="content-wrapper" style="min-height: 525px;">
<div class="container-fluid">
    <span class="pill_button">Reminder Date: 
        <span style="color:#8e78fa"><?php echo $get_previous_date; ?></span>
  </span>
<br>
<div class="loader_submit"></div>
  <div class="row">
    <div class="col-md-12">
      
    </div>
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">B2B Reminder  </h3> <br>
          <div class="box-tools pull-right">
          </div>
        </div>
          <div class="box-body">
              
            <table class="table table-bordered table-striped dataTable" id="reminder_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
              <thead style="white-space: nowrap;word-break: break-word !important">
                <tr>
                  <th>Code</th>
                  <th>Debtor Code</th>
                  <th>Supplier Name</th>
                  <th>Reg No</th>
                  
                  <th>Registration Invoice Date</th>
                  <th>Overdue Registration Fee</th> 
                  <th>Overdue Subscription</th>
                  <th>Total Overdue</th>

                  <th>Overdue Invoices Count</th>
                  <th>Overdue Invoice Date From</th>
                  <th>Overdue Invoice Date To</th>
                  <th>Overdue Invoice Due Date</th>

                  <th>Last Subscriptions Invoice Count</th>
                  <th>Last Invoice Date</th> 
                  <th>Last Invoice Due Date</th>
                  <th>Last Invoice Amt</th>

                  <th>Created At</th>
                  <th>Created By</th>
                  <th>Updated At</th>
                  <th>Updated By</th>
                  <th>Reminder Type</th>                          
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

    <div class="row">
    <div class="col-md-12">
    </div>
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">B2B Reminder By Retailer</h3><br>
          <div class="box-tools pull-right">
            <!-- <button id="import_excel" type="button" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-plus" aria-hidden="true" ></i> Import Excel</button> -->
          </div>
        </div>
          <div class="box-body">
              
            <table class="table table-bordered table-striped dataTable" id="reminder_retailer_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
              <thead style="white-space: nowrap;word-break: break-word !important">
                <tr>
                  <th>Debtor Code</th>
                  <th>Customer Name</th>
                  <th>Supplier Name</th>
                  <th>Reg No</th>
                  
                  <th>Registration Invoice Date</th>
                  <th>One Off Invoice Date</th>
                  <th>Registration & Add ON Amt</th>
                  <th>Subscription One OFF Amt</th>
                  <th>Training Amt</th>
                  <th>AdHoc Service Amt</th>
                  <th>Overdue Registration Fee</th> 
                  <th>Overdue Subscription</th>
                  <th>Total Overdue</th>

                  <th>Overdue Invoices Count</th>
                  <th>Overdue Invoice Date From</th>
                  <th>Overdue Invoice Date To</th>
                  <th>Overdue Invoice Due Date</th>

                  <th>Last Subscriptions Invoice Count</th>
                  <th>Last Invoice Date</th> 
                  <th>Last Invoice Due Date</th>
                  <th>Last Invoice Amt</th>

                  <th>Created At</th>
                  <th>Created By</th>
                  <th>Updated At</th>
                  <th>Updated By</th>
                  <th>Invoice Number</th>
                  <th>Extended</th>
                  <th>Reminder Type</th>
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
$(".loader_submit").hide(); 
  $('#reminder_table').DataTable({
    "columnDefs": [{ "orderable": false, "targets": 21 },
    { className: "alignright", targets: [5,6,7,15] },
    { className: "alignleft", targets: '_all' },
    { visible: false, targets: [20,21]}],
    "serverSide": true, 
    'processing'  : true,
    'paging'      : true,
    'lengthChange': true,
    'lengthMenu'  : [ [10, 25, 50, 100, 9999999], [10, 25, 50, 100, 'ALL'] ],
    'searching'   : true,
    'ordering'    : true,
    'order'       : [ [13 , 'desc'],[2 , 'asc']],
    'info'        : true,
    'autoWidth'   : false,
    "bPaginate": true, 
    "bFilter": true, 
    "sScrollY": "50vh", 
    "sScrollX": "100%", 
    "sScrollXInner": "100%", 
    "bScrollCollapse": true,
    "ajax": {
        "url": "<?php echo site_url('Query_outstanding/b2b_reminder_tb');?>",
        "type": "POST",
    },
    columns: [
      { data: "Code"},
      { data: "DebtorCode"},
      { data: "supplier_name"},
      { data: "reg_no"},
      { data: "Registration_Invoice_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Overdue_Registration_Fees", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //5
      { data: "Overdue_Subscriptions_Invoice_Amt", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //6
      { data: "Total_Overdue", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //7
      { data: "Overdue_Invoices_Count"},
      { data: "Overdue_Invoice_Date_From", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Overdue_Invoice_Date_To", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Overdue_Invoice_Due_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Last_Subscriptions_Invoice_Count"},  
      { data: "Last_Invoice_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Last_Due_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Last_Invoice_Amt", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //15
      { data: "created_at"},
      { data: "created_by"},
      { data: "updated_at", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00 00:00:00') || (data == 'null') || (data == null))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "updated_by"},
      { data: "reminder_type"},
      { data: "action", render: function(data, type, row){ 
        var element = '';

        

        return element;
      }},
    ],
    dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'Brtip',
        buttons: [

       { extend: 'excelHtml5',
         exportOptions: {columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15 ]} /*, footer: true */},

       { extend: 'csvHtml5',  
         exportOptions: {columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15 ]} /*, footer: true*/ },
              ],
    // "pagingType": "simple",
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      $(nRow).closest('tr').css({"cursor": "pointer"});
      $(nRow).attr('dockey', aData['dockey']);
      $(nRow).attr('DebtorCode', aData['DebtorCode']);
      //$(nRow).closest('tr').find('td:eq(0)').css('white-space', 'nowrap');
      //$(nRow).closest('tr').find('td:eq(4)').css('word-break', 'break-all');
    },
    "initComplete": function( settings, json ) {
      interval();
    }
  });//close datatable

  $(document).on('click', '#reminder_table tbody tr', function(event){
    
    var xstatus = $('#reminder_table').DataTable().rows().data().any();
    var debtor_code = $(this).attr('DebtorCode');

    if((xstatus == false) || (xstatus != true)){
      return;
    }

    if(event.target.tagName == "I" || event.target.tagName == "BUTTON" || event.target.tagName == "INPUT") {
      return;
    }

    if((debtor_code == '') || (debtor_code == null) || (debtor_code == 'null'))
    {
      alert('Invalid Debtor Code');
      return;
    }

    //child_table(debtor_code);

    $('input[aria-controls="reminder_retailer_table"]').val(debtor_code).keyup();

    var id = $(this).closest('table').attr('id');

    var table = $('#'+id).DataTable();

    table.rows('.active').nodes().to$().removeClass("active");

    $(this).closest('table').find('tr').removeClass("active");
    $(this).addClass('active');

  });//close mouse click

  $('#reminder_retailer_table').DataTable({
    "columnDefs": [{ "orderable": false, "targets": 28 },
    { className: "alignright", targets: [6,7,8,9,10,11,12,20] },
    { className: "alignleft", targets: '_all' }],
    "serverSide": true, 
    'processing'  : true,
    'paging'      : true,
    'lengthChange': true,
    'lengthMenu'  : [ [10, 25, 50, 100, 9999999], [10, 25, 50, 100, 'ALL'] ],
    'searching'   : true,
    'ordering'    : true,
    'order'       : [ [14 , 'desc'],[2 , 'asc']],
    'info'        : true,
    'autoWidth'   : false,
    "bPaginate": true, 
    "bFilter": true, 
    "sScrollY": "50vh", 
    "sScrollX": "100%", 
    "sScrollXInner": "100%", 
    "bScrollCollapse": true,
    "ajax": {
        "url": "<?php echo site_url('Query_outstanding/previous_b2b_reminder_retailer_tb');?>",
        "type": "POST",
    },
    columns: [
      { data: "DebtorCode"},
      { data: "acc_name"},
      { data: "supplier_name"},
      { data: "reg_no"},
      { data: "Registration_Invoice_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "One_Off_Invoice_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Registration_AddON_Invoice_Amt", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //5
      { data: "Subscription_OneOFF_Invoice_Amt", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //6
      { data: "Training_Invoice_Amt", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //7
      { data: "Ad_Hoc_Service_Invoice_Amt", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //8
      { data: "Overdue_Registration_Fees", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //9
      { data: "Overdue_Subscriptions_Invoice_Amt", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //10
      { data: "Total_Overdue", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //11
      { data: "Overdue_Invoices_Count"},
      { data: "Overdue_Invoice_Date_From", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Overdue_Invoice_Date_To", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Overdue_Invoice_Due_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Last_Subscriptions_Invoice_Count"},  
      { data: "Last_Invoice_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Last_Due_Date", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00') || (data == '1001-01-01'))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "Last_Invoice_Amt", render: $.fn.dataTable.render.number( ',', '.', 2,)}, //15
      { data: "created_at"},
      { data: "created_by"},
      { data: "updated_at", render: function(data, type, row){ 
        var element = '';

        if((data == '0000-00-00 00:00:00') || (data == 'null') || (data == null))
        {
          element += '';
        }
        else
        {
          element += data;
        }

        return element;
      }},
      { data: "updated_by"},
      { data: "invoice_number", render: function(data, type, row){ 
         var element = '';

         element += '<span class="cell_breakWord">'+data+'</span>';

         element += '<button id="view_last_gr" type="button" style="float:right;" title="REMOVE" class="btn btn-xs btn-warning"  supplier_guid="'+row['supplier_guid']+'" customer_guid="'+row['customer_guid']+'"><i class="fa fa-file"></i></button>';

         return element;
      }},
      { data: "extend_status"},
      { data: "reminder_type", render: function(data, type, row){ 
        var element = '';
        var element1 = row['variance_day'];
        var element2 = row['until_date'];
        var element3 = row['reg_block'];
        var element4 = row['one_off_block'];
        var element5 = '';

        if(element3 != '' && element4 != '')
        {
            if(element3 < element4)
            {
              element5 = element3;
            }
            else
            {
              element5 = element4;
            }
        }
        else if(element4 == '')
        {
          element5 = element3;
        }
        else
        {
          element5 = element4;
        }

        if(data == 'PROCESSING'  || data == 'Extended')
        {
          if(element1 != 0)
          {
            element += data+' '+element1+' DAY(S) until <br/>'+ element2;
          }
          else
          {
            element += data;
          }

        }
        else if(data == 'WARNING' )
        {
          element += data+'<br/> Block Date :<br/>'+element5;
        }
        else
        {
          element += data;
        }


        return element;
      }},
      { data: "action", render: function(data, type, row){ 
        var element = '';



        return element;
      }},
    ],
    dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'Brtip',
        buttons: [

       { extend: 'excelHtml5',
         exportOptions: {columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,19,20,21,22,23,24,25,26,27 ]} /*, footer: true */},

       { extend: 'csvHtml5',  
         exportOptions: {columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,19,20,21,22,23,24,25,26,27 ]} /*, footer: true*/ },
              ],
    // "pagingType": "simple",
    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
      //$(nRow).closest('tr').css({"cursor": "pointer"});
      $(nRow).attr('dockey', aData['dockey']);
      $(nRow).attr('DebtorCode', aData['DebtorCode']);
      //$(nRow).closest('tr').find('td:eq(0)').css('white-space', 'nowrap');
      //$(nRow).closest('tr').find('td:eq(4)').css('word-break', 'break-all');
    },
    "initComplete": function( settings, json ) {
      interval();
    }
  });//close datatable

})
</script>
