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
<div class="content-wrapper" style="min-height: 525px; text-align: justify;">
<div class="container-fluid">
<br>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <!-- head -->
        <div class="box-header with-border">
          <h3 class="box-title">Filter By</h3><br>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <!-- head -->
        <!-- body -->
        <div class="box-body">
          <div class="col-md-12">
            <div class="row">

              <div id="table_option_filter">
                <div class="col-md-2"><b>Log Table Name</b></div>
                <div class="col-md-4">
                  <select class="form-control select2" name="table_option" id="table_option">
                    <option value="">-Select-</option>
                    <?php foreach($get_table as $row)
                    {
                      ?>
                      <option value="<?php echo $row->guid?>"><?php echo $row->log_table?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <div class="clearfix"></div><br>
              </div>

              <div id="retailer_option_filter">
                <div class="col-md-2"><b>Retailer Name</b><span id="retailer_filter" style="color:red;font-size:25px;"></span></div>
                <div class="col-md-4">
                  <select class="form-control select2" name="retailer_option" id="retailer_option">
                    <option value="">-Select-</option>
                    <?php foreach($get_acc as $row)
                    {
                      ?>
                      <option value="<?php echo $row->acc_guid?>"><?php echo $row->acc_name?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <div class="clearfix"></div><br>
              </div>

              <div id="supplier_option_filter">
                <div class="col-md-2"><b>Supplier Name</b><span id="supplier_filter" style="color:red;font-size:25px;"></span></div>
                <div class="col-md-4">
                  <select class="form-control select2" name="supplier_option" id="supplier_option">
                    <option value="">-Select-</option>
                    <?php foreach($get_supplier as $row)
                    {
                      ?>
                      <option value="<?php echo $row->supplier_guid?>"><?php echo $row->supplier_name?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <div class="clearfix"></div><br>
              </div>

              <div id="user_option_filter">
                <div class="col-md-2"><b>User Name</b><span id="user_filter" style="color:red;font-size:25px;"></span></div>
                <div class="col-md-4">
                  <select class="form-control select2" name="user_option" id="user_option">
                    <option value="">-Select User Name-</option>
                  </select>
                </div>
                <div class="clearfix"></div><br>
              </div>

              <div id="date_range_filter">
                <div class="col-md-2"><b>Date From</b><span id="date_filter" style="color:red;font-size:25px;"></span></div>
                  <div class="col-md-2">
                      <input id="date_from" name="date_from" type="text" class="form-control pull-right datepicker" autocomplete="off">
                  </div>
                  <div class="col-md-2"><b>Date To</b><span id="date_filter" style="color:red;font-size:25px;"></span></div>
                  <div class="col-md-2">
                      <input id="date_to" name="date_to" type="text" class="form-control pull-right datepicker" autocomplete="off">
                  </div>
                  <div class="col-md-2">
                      <a class="btn btn-danger" id="reset_date">Clear</a>
                  </div>
                <div class="clearfix"></div><br>
              
                <span id="append_filter"></span>
              </div>

              <div id="period_range_filter">
                <div class="col-md-2"><b>Period Code From</b><span id="period_filter" style="color:red;font-size:25px;"></span></div>
                <div class="col-md-2">
                <select class="form-control select2" name="period_code_from" id="period_code_from">
                  <option value="">-Select-</option>
                  <?php foreach($get_period as $row)
                  {
                    ?>
                    <option value="<?php echo $row->period_code?>"><?php echo $row->period_code?></option>
                    <?php
                  }
                  ?>
                </select>
                </div>
                <div class="col-md-2"><b>Period Code To</b><span id="period_filter" style="color:red;font-size:25px;"></span></div>
                <div class="col-md-2">
                <select class="form-control select2" name="period_code_to" id="period_code_to">
                  <option value="">-Select-</option>
                  <?php foreach($get_period as $row)
                  {
                    ?>
                    <option value="<?php echo $row->period_code?>"><?php echo $row->period_code?></option>
                    <?php
                  }
                  ?>
                </select>
                </div>
                <div class="col-md-2">
                  <a class="btn btn-danger" id="reset_period_range">Clear</a>
                </div>
                <div class="clearfix"></div><br>
              </div>

              <div id="period_code_filter">
                <div class="col-md-2"><b>Period Code</b></div>
                <div class="col-md-4">
                <select class="form-control select2" name="period_code" id="period_code">
                  <option value="">-Select-</option>
                  <?php foreach($get_period as $row)
                  {
                    ?>
                    <option value="<?php echo $row->period_code?>"><?php echo $row->period_code?></option>
                    <?php
                  }
                  ?>
                </select>
                </div>
                <div class="clearfix"></div><br>
              </div>

              <div class="col-md-2"><b>Jasper Report Module</b></div>
              <div class="col-md-4">
                <span id="append_jasper_menu" style="font-size:16px;cursor:pointer;">
                  
                </span>
              </div>
              <div class="clearfix"></div><br>

              <div class="col-md-12">
                <button type="button" id="search_data" class="btn btn-primary" ><i class="fa fa-search"></i> Search </button>
                <button type="button" id="reset_data" class="btn btn-danger" ><i class="fa fa-refresh"></i> Reset </button>
              </div>

            </div>
          </div>
        </div>
        <!-- body -->
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 col-xs-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Log Table </h3>
          <div class="box-tools pull-right">

          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body" >
          <button id="download_excel" onClick="downloadExcel()" hidden>Excel</button></br></br>
          <span id="append_table"></span>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="preview-jasper">
    <div class="panel panel-default" id="copyright-wrap">                     
      <div class="panel-heading">Report
         /** <button type="button" class="close" onclick="close_report()"> 
          <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> **/
      </div>
      <div class="panel-body">
        <div class="embed-responsive embed-responsive-16by9" id="jasper" >
          /**<iframe class="embed-responsive-item" src="https://jasper.xbridge.my:59090/jasperserver/flow.html?_flowId=viewReportFlow&_flowId=viewReportFlow&decorate=no&j_username=panda_b2b&j_password=b2b@adnap&db_b2b=b2b_summary&db_lite=lite_b2b&db_apps=lite_b2b_apps&ParentFolderUri=/reports/B2B_MGMT_Report/PO_Action&reportUnit=/reports/B2B_MGMT_Report/PO_Action/PO_Action_Details_Report" ></iframe> **/
          <iframe class="embed-responsive-item" src="https://jasper.xbridge.my:59090/jasperserver/flow.html?_flowId=viewReportFlow&_flowId=viewReportFlow&decorate=no&j_username=panda_b2b&j_password=b2b@adnap&db_b2b=b2b_summary&db_lite=lite_b2b&db_apps=lite_b2b_apps&ParentFolderUri=/reports/B2B_MGMT_Report/PO_Action&reportUnit=/reports/B2B_MGMT_Report/PO_Action/PO_Action_Summary_Report"></iframe>
        </div>
      </div>
    </div>
  </div> -->

</div>
</div>

<script type="text/javascript">

  function downloadExcel() {

    document.getElementById("download_excel").disabled = true;

    table_option = $('#table_option').val();
    retailer_option = $('#retailer_option').val();
    supplier_option = $('#supplier_option').val();
    user_option = $('#user_option').val();
    date_from = $('#date_from').val();
    date_to = $('#date_to').val();
    period_code_from = $('#period_code_from').val();
    period_code_to = $('#period_code_to').val();
    period_code = $('#period_code').val();

    parameter = '?table_option='+table_option+'&retailer_option='+retailer_option+'&supplier_option='+supplier_option+'&user_option='+user_option+'&date_from='+date_from+'&date_to='+date_to+'&period_code_from='+period_code_from+'&period_code_to='+period_code_to+'&period_code='+period_code+'&download_excel=true';

    window.location.href = "<?php echo site_url('Portal_logs/run_log_table'); ?>" + parameter;

    setTimeout(function() {
      document.getElementById("download_excel").disabled = false;
    }, 5000);

  }

</script>

<script>
$(document).ready(function() {

  var db_b2b = 'b2b_summary';
  var db_lite = 'lite_b2b';
  var db_apps = 'lite_b2b_apps';

  // alert(db_b2b); die;
  $.ajax({
    type: "get",        
    url: "https://api.xbridge.my/rest_client/index.php/report_hub?web_module=xbridge_b2b&db_b2b="+db_b2b+"&db_lite="+db_lite+"&db_apps="+db_apps,
    //shoot url to rest client                           
    beforeSend : function() {
      $('#jasper_module_btn').button('loading');
    },
    complete: function() {
      $('#jasper_module_btn').button('reset');
    },                    
    success: function(data) 
    {             
      //$('#report_menu_modal').modal('show');
      $("#append_jasper_menu").html(data);
      console.log(data);
      
    }
  });

  $('.datepicker').datepicker({
    forceParse: false,
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd'
  });

  $(document).on('change','#table_option',function(){
    
    check_table_option = $('#table_option').val();
    hide_unused_filter(check_table_option);
    
    $.ajax({
      url: "<?php echo site_url('Portal_logs/get_filter_data'); ?>",
      method: "POST",
      data: {
        table_option: check_table_option
      },
      success: function (data) {
        var json = JSON.parse(data);

        retailer_filter = json.query[0]['check_retailer'];
        supplier_filter = json.query[0]['check_supplier'];
        user_filter = json.query[0]['check_user'];
        date_filter = json.query[0]['check_date'];
        period_filter = json.query[0]['check_period'];

        if(retailer_filter == '1')
        {
          $('#retailer_filter').html('*');
        }
        else
        {
          $('#retailer_filter').html('');
        }

        if(supplier_filter == '1')
        {
          $('#supplier_filter').html('*');
        }
        else
        {
          $('#supplier_filter').html('');
        }

        if(user_filter == '1')
        {
          $('#user_filter').html('*');
        }
        else
        {
          $('#user_filter').html('');
        }

        if(date_filter == '1')
        {
          $('#date_filter').html('*');
        }
        else
        {
          $('#date_filter').html('');
        }

        if(period_filter == '1')
        {
          $('#period_filter').html('*');
        }
        else
        {
          $('#period_filter').html('');
        }

      }
    });

  });//close search button

  $(document).on('click','#reset_data',function(){
    
    $('#table_option').val('').trigger('change');
    $('#retailer_option').val('').trigger('change');
    $('#supplier_option').val('').trigger('change');
    $('#user_option').val('').trigger('change');
    $('#date_from').val('').trigger('change');
    $('#date_to').val('').trigger('change');
    $('#period_code_from').val('').trigger('change');
    $('#period_code_to').val('').trigger('change');
    $('#period_code').val('').trigger('change');

  });//close search button

  $(document).on('click','#reset_date',function(){
    
    $('#date_from').val('').trigger('change');
    $('#date_to').val('').trigger('change');

  });

  $(document).on('click','#reset_period_range',function(){
    
    $('#period_code_from').val('').trigger('change');
    $('#period_code_to').val('').trigger('change');

  });

  $(document).on('change','#supplier_option',function(){
    
    supplier_data = $('#supplier_option').val();
    retailer_data =  $('#retailer_option').val();

    if(supplier_data != '' && retailer_data != '')
    {
      $.ajax({
        url : "<?php echo site_url('Portal_logs/fetch_user'); ?>",
        method:"POST",
        data:{supplier_data:supplier_data,retailer_data:retailer_data},
        success:function(result)
        {
          json = JSON.parse(result); 

          vendor = '';

          Object.keys(json['vendor']).forEach(function(key) {

            vendor += '<option value="'+json['vendor'][key]['user_guid']+'">'+json['vendor'][key]['user_id']+' - '+json['vendor'][key]['user_name']+'</option>';

          });
          $('#user_option').select2().html(vendor);
        }
      });
    }
    else
    {
      $('#user_option').select2().html('<option value="" disabled>Please select the supplier & retailer</option>');
    }

    // alert(retailer_data);die;

  });//close user filter button
  
  $(document).on('click','#search_data',function(){

    table_option = $('#table_option').val();
    retailer_option = $('#retailer_option').val();
    supplier_option = $('#supplier_option').val();
    user_option = $('#user_option').val();
    date_from = $('#date_from').val();
    date_to = $('#date_to').val();
    period_code_from = $('#period_code_from').val();
    period_code_to = $('#period_code_to').val();
    period_code = $('#period_code').val();
    
    if((table_option == '') || (table_option == 'null') || (table_option == null))
    {
      alert('Opps, Please Select Log for Searching...');
      return;
    }

    if(table_option == '528DA5CE16D411EDBBD7B2C55218ACED') // supplier movement
    {
      if(retailer_option == '' && user_option == '' )
      {
        alert('Please Select more filter Retailer or User. Due to Too large data.');
        return;
      }

      if(date_from == '' && date_to == '')
      {
        alert('Please Select more Date From and Date To. Due to Too large data.');
        return;
      }

      if(date_from > date_to)
      {
        alert('Date From cannot bigger than Date To.');
        return;
      }
    }

    if(table_option == '10598C1C648711EDB862000D3AA2838A' || table_option == '067F6ECA654E11EDA237000D3AA2838A' || table_option == '10598C1C648711EDB862000D3AA2838B') // supplier movement
    {
      if(retailer_option == '')
      {
        alert('Please Select Retailer.');
        return;
      }

      if(period_code == '')
      {
        alert('Please Select period code.');
        return;
      }
    }

    if(table_option == 'C3091AA9A6B611ED8D8C000D3AA232B1' ) // email transaction
    {
      if(date_from == '' && date_to == '')
      {
        alert('Please Select more Date From and Date To. Due to Too large data.');
        return;
      }

      if(date_from > date_to)
      {
        alert('Date From cannot bigger than Date To.');
        return;
      }
    }

    if(table_option == 'G28DA61316D411EDBBD7B2C55218A001' || table_option == 'G28DA61316D411EDBBD7B2C55218A002') // Consignment E-Invoice Report && Monthly Doc Count Report
    {

      if(retailer_option == '')
      {
        alert('Please Select Retailer.');
        return;
      }

      if(period_code_from == '' || period_code_to == '')
      {
        alert('Please Select Period Range.');
        return;
      }

      if(period_code_from > period_code_to)
      {
        alert('Period Code From cannot bigger than Period Code To.');
        return;
      }
    }

    if(table_option == 'G28DA61316D411EDBBD7B2C55218A003' || table_option == 'G28DA61316D411EDBBD7B2C55218A004' || table_option == 'G28DA61316D411EDBBD7B2C55218A005' || table_option == 'G28DA61316D411EDBBD7B2C55218A006') // Monthly GRN E-Invoice Report && STRB vs PRDN - Summary By Day, Summary By Outlets, Summary By Outlets & Days Diff
    {

      if(retailer_option == '')
      {
        alert('Please Select Retailer.');
        return;
      }

      if(period_code == '')
      {
        alert('Please Select period code.');
        return;
      }
    }

    document.getElementById("download_excel").removeAttribute("hidden");

    log_table(table_option,retailer_option,supplier_option,user_option,date_from,date_to,period_code,period_code_from,period_code_to);

  });//close search button

  log_table = function(table_option,retailer_option,supplier_option,user_option,date_from,date_to,period_code,period_code_from,period_code_to) {
    $.ajax({
      url: "<?php echo site_url('Portal_logs/run_log_table'); ?>",
      method: "POST",
      data:{table_option:table_option,retailer_option:retailer_option,supplier_option:supplier_option,user_option:user_option,date_from:date_from,date_to:date_to,period_code:period_code,period_code_from:period_code_from,period_code_to:period_code_to},
      beforeSend: function() {
        swal.fire({
          allowOutsideClick: false,
          title: 'Processing...',
          showCancelButton: false,
          showConfirmButton: false,
          onOpen: function () {
          swal.showLoading()
          }
        });
        $('.btn').button('loading');
      },
      complete: function() {
        $('.btn').button('reset');
        $(".loader").hide();

        if(json.para == 'True')
        {
          setTimeout(function() {
              Swal.close();
          }, 300);
        }
      },
      success: function(data) {
        json = JSON.parse(data);

        if(json.para == 'True')
        {
          methodd = '';

          column_var = [];

          methodd += '<table id="report_summary" class="table table-bordered table-striped dataTable" width="100%" cellspacing="0">';

          methodd += '<thead style="white-space: nowrap;">';

          Object.keys(json['csv_header']).forEach(function(key) {

              methodd += '<th>'+json['csv_header'][key]+'</th>';
              column_var.push({data:json['csv_header'][key]});
          });

          methodd += '</thead>';

          methodd += '<tbody></tbody>';

          methodd += '</table>';

          methodd += '</div>';

          $('#append_table').html(methodd);

          if ($.fn.DataTable.isDataTable('#report_summary')) {
            $('#report_summary').DataTable().clear().destroy()
          }

          $('#report_summary').DataTable({
            "columnDefs": [],
            //"serverSide": true, 
            'processing': true,
            'paging': true,
            'lengthChange': true,
            'lengthMenu': [
              [10, 25, 50, 9999999999],
              [10, 25, 50, 'ALL']
            ],
            'searching': true,
            'ordering': true,
            'order': [
              [0, 'asc']
            ],
            'info': true,
            'autoWidth': false,
            "bPaginate": true,
            "bFilter": true,
            "sScrollY": "60vh",
            "sScrollX": "100%",
            "sScrollXInner": "100%",
            "bScrollCollapse": true,
            data: json['sql'],
            columns: column_var,
            //dom: "lBfrtip",
            dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>rtip",
            buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                columns: ':visible'
                }
            },
            ],
            "language": {
              "lengthMenu": "Show _MENU_",
              "infoEmpty": "No records available",
              "infoFiltered": "(filtered from _MAX_ total records)",

            },
            // "pagingType": "simple",
            "fnCreatedRow": function(nRow, aData, iDataIndex) {},
            "initComplete": function(settings, json) {
              interval();
            }
          }); //close datatable
          $("#ttable_wrapper .row .col-sm-12").css("overflow", "auto");
          $("#ttable_wrapper .row .col-sm-12").css("max-height", "310px");

        }
        else
        {
          Swal.fire({
                title: "No Data Record", 
                text: '', 
                type: "error",
                allowOutsideClick: false,
                showConfirmButton: true,
            })
        }
      }
    }); //close ajax
  }
 
});
</script>

<script type="text/javascript">

  function hide_unused_filter(table_option = ''){
    // $( "p" ).addClass( "myClass yourClass" );
    // $( "p" ).removeClass( "myClass yourClass" );

    // $("#retailer_option_filter").addClass("hidden");

    $("#table_option_filter").removeClass("hidden");
    $("#retailer_option_filter").removeClass("hidden");
    $("#supplier_option_filter").removeClass("hidden");
    $("#user_option_filter").removeClass("hidden");
    $("#date_range_filter").removeClass("hidden");
    $("#period_range_filter").removeClass("hidden");
    $("#period_code_filter").removeClass("hidden");

    // alert(table_option);

    if(table_option == 'G28DA61316D411EDBBD7B2C55218A001' || table_option == 'G28DA61316D411EDBBD7B2C55218A002'){
      $("#supplier_option_filter").addClass("hidden");
      $("#user_option_filter").addClass("hidden");
      $("#date_range_filter").addClass("hidden");
      $("#period_code_filter").addClass("hidden");
    }else if(table_option == 'G28DA61316D411EDBBD7B2C55218A003' || table_option == 'G28DA61316D411EDBBD7B2C55218A004' || table_option == 'G28DA61316D411EDBBD7B2C55218A005' || table_option == 'G28DA61316D411EDBBD7B2C55218A006'){
      $("#supplier_option_filter").addClass("hidden");
      $("#user_option_filter").addClass("hidden");
      $("#date_range_filter").addClass("hidden");
      $("#period_range_filter").addClass("hidden");
    }

  }
  
</script>
