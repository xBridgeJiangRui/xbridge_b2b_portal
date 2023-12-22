<style>

.content-wrapper{
  min-height: 850px !important; 
}

.alignright {
  text-align: right;
}

.aligncenter{
  text-align: center;
}

.alignleft
{
  text-align: left;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice
{
    background: #3c8dbc;
}

.select2-container--default .select2-selection--multiple .select2-selection__rendered {
  display: inline-grid;
  white-space: nowrap;
  overflow-x: hidden;
  overflow-y: scroll;
  max-height: 250px;
}

.no-js #loader {
    display: none;
}

.js #loader {
    display: block;
    position: absolute;
    left: 100px;
    top: 0;
}

.se-pre-con {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url("<?php echo base_url('assets/loading2.gif') ?>") center no-repeat #fff;
    /*background:   #fff;*/
}

</style>

<div class="content-wrapper" style="min-height: 525px; text-align: justify;">
<div class="container-fluid">
<br>

  <div id="no_variance_message" class="alert alert-success text-center hidden" style="font-size: 18px">
    <?php echo 'No Variance Found, Please Proceed to Release'; ?>
    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
  </div>

  <div id="release_message" class="alert alert-success text-center hidden" style="font-size: 18px">
    <?php echo 'Please Proceed to Release'; ?>
    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
  </div>

  <div id="no_consign" class="alert alert-warning text-center hidden" style="font-size: 18px">
    <?php echo 'No consign to be Release'; ?>
    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
  </div>

<?php if ($_POST['email_status'] && $_POST['email_message']) { ?>
  <div class="alert <?php echo ($_POST['email_status'] == 1) ? 'alert-success' : 'alert-danger'; ?> text-center" style="font-size: 18px">
    <?php echo $_POST['email_message']; ?>
    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
  </div>
<?php } ?>

<?php if ($this->session->flashdata('message')) { ?>
  <div class="alert alert-success text-center" style="font-size: 18px">
    <?php echo $this->session->flashdata('message') <> '' ? $this->session->flashdata('message') : ''; ?>
    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
  </div>
<?php } ?>

<?php if ($this->session->flashdata('warning')) { ?>
  <div class="alert alert-danger text-center" style="font-size: 18px">
    <?php echo $this->session->flashdata('warning') <> '' ? $this->session->flashdata('warning') : ''; ?>
    <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
  </div>
<?php } ?>


<div class="col-md-12">
  <a class="btn btn-app" href="<?php echo site_url('Consignment/view_log') ?>">
    <i class="fa fa-file-text"></i> View Log
  </a>
  <a class="btn btn-app" style="color:#B8860B" href="<?php echo site_url('Consignment/check_email') ?>">
    <i class="fa fa-envelope"></i> Check Email
  </a>
  <a class="btn btn-app" style="color:#00008B" id="consign_half_btn">
    <i class="fa fa-refresh"></i> Process Half Consignment
  </a>
  <a class="btn btn-app hidden" style="color:#3C8DBC" id="release_button" title="Release Consignment">
    <i class="fa fa-paper-plane"></i> Release 
  </a>
</div>
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

              <div class="col-md-2"><b>Date From<br>(YYYY-MM-DD)</b></div>
              <div class="col-md-2">
                <input  id="date_from" name="date_from" type="datetime" value="" readonly class="form-control pull-right">
              </div>
              <div class="col-md-2"><b>Date To<br>(YYYY-MM-DD)</b></div>
              <div class="col-md-2">
                <input  id="date_to" name="date_to" type="datetime" class="form-control pull-right" readonly value="">
              </div>
              <div class="col-md-1">
                <a class="btn btn-danger" onclick="expiry_clear()">Clear</a>
              </div>

              <div class="clearfix"></div><br>

              
                <!-- </form> -->
            </div>

            <div class="row">

              <div class="col-md-2"><b>Retailer</b></div>
              <div class="col-md-8">

                <?php if(sizeof($retailer->result()) == 1){ ?>
                    <input type="text" value="<?php echo $retailer->row('acc_name'); ?>" readonly class="form-control pull-right">
                    <input  id="retailer" name="retailer" type="text" value="<?php echo $retailer->row('acc_guid'); ?>" hidden>
                <?php }else{ ?>
                  <select id="retailer" name="retailer" class="form-control select2" required>
                    <option value="">Please Select One Retailer</option> 
                    <?php foreach($retailer->result() as $row){ ?>
                      <option value="<?php echo $row->acc_guid ?>"> 
                      <?php echo $row->acc_name; ?></option>
                    <?php } ?>
                  </select>
                <?php } ?>
              </div>

              <div class="col-md-2">

              </div>

              <div class="clearfix"></div><br>

            </div>           

            <div class="row">
              <div class="col-md-2">
                <a class="btn btn-success" id="search_data">Submit</a>
                <!-- <a class="btn btn-info" id="release_button">Release</a> -->
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
          <h3 class="box-title"><i class="fa fa-folder-open-o"></i> HQ Variance <span class="add_branch_list"></span></h3>
          <div class="box-tools pull-right">
            <div class="box-tools pull-right">
              <button type="button" id="hq_variance_collapse" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
              <!-- <a class="btn btn-xs btn-warning" target="_blank" href="<?php echo site_url('Amend_doc/amend_sites');?>">
                <i class="fa fa-file"></i> Hide/Reset Doc
              </a> -->
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body" >

          <table id="table1" class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead style="white-space: nowrap;"> <!--style="white-space: nowrap;"-->
            <tr>
              <th style="width: 1px;" class="text-center"><input type="checkbox" checked="checked" onclick="$('input[name*=\'consign_checkbox\']').prop('checked', this.checked);" /></th>
              <th>Variance Amt</th>
              <th>Cost Amt</th>
              <th>Invoice Amt</th>
              <th>Period Code</th>
              <th>Code</th>
              <th>Supplier</th>
              <th>Missing B2B</th>
              <th>Total Row</th>
              <th>Total Posted</th>
              <th>Total Outlet</th>
              <th>Diff Outlet</th>
              <th>Trans Refno</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="email-recipient-layout"></div>

</div>
</div>

<div id="loader_div" class="se-pre-con hidden"></div>

<script>
$(document).ready(function() {

  $( ".target" ).change(function() {
    alert( "Handler for .change() called." );
  });

  $('#table1').DataTable({
    "columnDefs": [{"targets": '_all' ,"orderable": false}],
      'order': [],
      "sScrollY": "30vh", 
        "sScrollX": "100%", 
        "sScrollXInner": "100%", 
        "bScrollCollapse": true,
       dom: "<'row'<'col-sm-2 remove_padding_right 'l > <'col-sm-10' f>  " + "<'col-sm-1' <'toolbar_list'>>>" +'rt<"row" <".col-md-4 remove_padding" i>  <".col-md-8" p> >',
        "language": {
                "lengthMenu": "Display _MENU_",
                "infoEmpty": "No records available",
                "infoFiltered": "(filtered from _MAX_ total records)",
                "info":           "Show _START_ - _END_ of _TOTAL_ entry",
                "zeroRecords": "<?php echo '<b>No Record Found. Please Select filtering to view data.</b>'; ?>",
      },
      "pagingType": "simple_numbers",
  });
  $('.remove_padding_right').css({'text-align':'left'});
  $("div.remove_padding").css({"text-align":"left"});

  var today = '<?php echo date('Y-m-d', strtotime(date('Y-m-01') . " - 1 month"));?>';

  $(function() {
    $('input[name="date_from"]').daterangepicker({
        locale: {
        format: 'YYYY-MM-DD'
        },
        maxDate: new Date(),
        startDate: today,
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        
    },function(start) {

        // alert(moment(start, 'DD-MM-YYYY').add(31, 'days'));
        qenddate = moment(start, 'DD-MM-YYYY').add(30, 'days');
        enddate = moment(start, 'DD-MM-YYYY').endOf('month');
        // var maxDate = start.addDays(5);
        // alert(maxDate);

            $('input[name="date_to"]').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            "minDate": start,
            "maxDate": qenddate,
            startDate: enddate,
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: true,
            });
        });
    $(this).find('[name="date_from"]').val(today);
  });

  $(function() {
    mend = '<?php echo date('Y-m-t', strtotime(date('Y-m-d') . " - 30 days"));?>';
    $('input[name="date_to"]').daterangepicker({
        locale: {
        format: 'YYYY-MM-DD'
        },
        "minDate": today,
        "maxDate": mend,
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
    });
    $(this).find('[name="date_to"]').val(mend);
  });

  $(document).on('click','#release_button',function(){

    var count_checkbox = $('#consign_checkbox:checked').length;

    if(count_checkbox < 1){
      Swal.fire({
        title: 'Please select at least one(1) supplier', 
        text: '', 
        type: "error",
        allowOutsideClick: true,
        showConfirmButton: true,
      });

      return;
    }

    // Swal.fire({
    //   title: "Are you sure?",
    //   text: "You will release " + count_check + " numbers of invoices!",
    //   icon: "warning",
    //   buttons: [
    //     'No, cancel it!',
    //     'Yes, I am sure!'
    //   ],
    //   dangerMode: true,
    // }).then(function(isConfirm) {
    //   if (isConfirm) {
    //     swal({
    //       title: 'Success!',
    //       text: count_check + 'invoices has been released!',
    //       icon: 'success'
    //     }).then(function() {
    //       form.submit();
    //     });
    //   } else {
    //     swal("Cancelled", "You may submit again to proceed", "error");
    //     return;
    //   }
    // });

    Swal.fire({
      title: 'Are you sure?',
      text: "You will release " + count_checkbox + " numbers of invoices!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, release it!',
      cancelButtonText: 'No, cancel',
    }).then((result) => {

      if (result.value) {

        $("#no_variance_message").addClass('hidden');
        $("#release_message").addClass('hidden');
        $("#no_consign").addClass('hidden');

        var date_start = $('#date_from').val();
        var date_end = $('#date_to').val();
        var retailer = $('#retailer').val();
        var selectedCheckboxes = $('input[name="consign_checkbox[]"]:checked');
        var selectedCheckboxValues = [];
        
        selectedCheckboxes.each(function() {
          selectedCheckboxValues.push($(this).val());
        });

        if(date_start == '' || date_start == null)
        {
          alert('Please Choose Start Date');
          return;
        }

        if(date_end == '' || date_end == null)
        {
          alert('Please Choose End Date');
          return;
        }

        if(date_end < date_start)
        {
          alert('Date End cannot smaller than date start.');
          return;
        }

        if(retailer == '' || retailer == null)
        {
          alert('Please Choose Retailer');
          return;
        }  

        $.ajax({
          url : "<?php echo site_url('Consignment/check_hq_variance');?>",
          method: "POST",
          data:{date_start:date_start,date_end:date_end,retailer:retailer,selectedCheckboxes:selectedCheckboxValues,doublecheck:true},
          beforeSend : function() {
              $('.btn').button('loading');
          },
          complete: function() {
              $('.btn').button('reset');
          },
          success : function(data)
          {  
            json = JSON.parse(data);
            
            //nabil hardcode
            // if(retailer == ''){
            if(json['query_data'].length == 0){
              Swal.fire({
                title: 'Cannot proceed, no result from HQ. Kindly re-start again the process', 
                text: '', 
                type: "error",
                allowOutsideClick: true,
                showConfirmButton: true,
              });
            }else{

              $.ajax({
                url : "<?php echo site_url('Consignment/run_process');?>",
                method: "POST",
                data:{date_start:date_start,date_end:date_end,retailer:retailer,selectedCheckboxes:selectedCheckboxValues},
                beforeSend : function() {
                    $('.btn').button('loading');
                    $('#loader_div').removeClass('hidden');
                },
                complete: function() {
                    $('.btn').button('reset');
                },
                success : function(data)
                {  
                  $(".se-pre-con").fadeOut("slow");
                  // json = JSON.parse(data);
                  console.log(data);
                  
                  if(json['status'] == 0){
                    Swal.fire({
                      title: json['message'], 
                      text: '', 
                      type: "error",
                      allowOutsideClick: true,
                      showConfirmButton: true,
                    });
                  }else{
                    get_layout_email_recipient(date_start,date_end,retailer);

                    if (!$("#hq_variance_collapse").parents('.box').hasClass('collapsed-box')) {
                      $("#hq_variance_collapse").click();
                    }

                    Swal.fire(
                      'Release!',
                      count_checkbox + ' numbers of invoices has been released.',
                      'success'
                    );
                  }
                  
                }
              });

            }
          }
        });

      } else if (result.dismiss === Swal.DismissReason.cancel) {
        // User clicked the "No, cancel" button or pressed Esc
        Swal.fire(
          'Cancelled',
          'You may submit again to proceed',
          'error'
        )

        return;
      }
    });
  });

  $(document).on('click','#search_data',function(){

    $('#release_message').addClass('hidden');
    $('#no_consign').addClass('hidden');
    $('#email-recipient-layout').addClass('hidden');

    if ($("#hq_variance_collapse").parents('.box').hasClass('collapsed-box')) {
      $("#hq_variance_collapse").click();
    }

    // if ($(this).parents('.box').hasClass('collapsed-box')) {
    //   console.log('Box has been collapsed');
    // } else {
    //   console.log('Box has not been collapsed');
    // }

    var date_start = $('#date_from').val();
    var date_end = $('#date_to').val();
    var retailer = $('#retailer').val();

    if(date_start == '' || date_start == null)
    {
      alert('Please Choose Start Date');
      return;
    }

    if(date_end == '' || date_end == null)
    {
      alert('Please Choose End Date');
      return;
    }

    if(date_end < date_start)
    {
      alert('Date End cannot smaller than date start.');
      return;
    }

    if(retailer == '' || retailer == null)
    {
      alert('Please Choose Retailer');
      return;
    }   

    datatable(date_start,date_end,retailer);

  });//close search button

  datatable = function(date_start,date_end,retailer)
  { 
    $.ajax({
      url : "<?php echo site_url('Consignment/check_hq_variance');?>",
      method: "POST",
      data:{date_start:date_start,date_end:date_end,retailer:retailer},
      beforeSend : function() {
          $('.btn').button('loading');
      },
      complete: function() {
          $('.btn').button('reset');
      },
      success : function(data)
      {  
        json = JSON.parse(data);
        if ($.fn.DataTable.isDataTable('#table1')) {
            $('#table1').DataTable().destroy();
        }

        if(json['query_data'] != '' && json['query_data'] != '[]'){
          $('#release_button').removeClass('hidden');
          $('#release_message').removeClass('hidden');
        }else{
          $('#no_consign').removeClass('hidden');
        }

        $('#table1').DataTable({
        "columnDefs": [
        { className: "aligncenter", targets: [] },
        { className: "alignright", targets: [0,1,2,6,7] },
        { className: "alignleft", targets: '_all' },
        ],
        'pageLength'  : 9999999999999999,
        'processing'  : true,
        'paging'      : true,
        'lengthChange': true,
        'lengthMenu'  : [ [9999999999999999], ["ALL"] ],
        'searching'   : true,
        'ordering'    : true,
        // 'order'       : [ [2 , 'desc'] ],
        'info'        : true,
        'autoWidth'   : false,
        "bPaginate": true, 
        "bFilter": true, 
        "sScrollY": "60vh", 
        "sScrollX": "100%", 
        "sScrollXInner": "100%", 
        "bScrollCollapse": true,
        "aoColumnDefs": [
        {
          "bSortable": false,
          "aTargets": [0]
        }
        ],
          data: json['query_data'],
          columns: [
                    {"data": "" ,render:function( data, type, row ){
                      var element = '';
                      var period_code = row['periodcode'];
                      var code = row['CODE'];
                      var chkb_value = period_code + '|' + code;

                      if(row['trans_guid'] != '' && row['missing_b2b'] == ''){
                        element += '<input type="checkbox" class="data-check" id="consign_checkbox" name="consign_checkbox[]" value="' + chkb_value + '" checked="checked">';
                      }else{
                        element += '';
                      }

                      return element;

                      }
                    },
                    {"data" : "var_amount"},
                    {"data" : "cost_amt"},
                    {"data" : "inv_amt"},
                    {"data" : "periodcode"},
                    {"data" : "CODE"},
                    {"data" : "supplier"},
                    {"data" : "missing_b2b"},
                    {"data" : "total_row"},
                    {"data" : "total_posted"},
                    {"data" : "total_outlet"},
                    {"data" : "diff_outlet"},
                    {"data" : "trans_guid"},
                   ],
          dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>Brtip", 
          buttons: [
                {
                    extend: 'csv'
                }
          ],
          "language": {
            "lengthMenu": "Show _MENU_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "zeroRecords": "<?php echo '<b>No Variance Found.</b>'; ?>",
          },
         "fnCreatedRow": function( nRow, aData, iDataIndex ) {
            $(nRow).closest('tr').css({"cursor": "pointer"});

            if(aData['trans_guid'] == '' || aData['missing_b2b'] != ''){
              $(nRow).addClass('bg-danger');
            }
            // $(nRow).attr('refno_val', aData['refno_val']);
          // $(nRow).attr('status', aData['status']);
        },
        "initComplete": function( settings, json ) {
          interval();
        }
        });//close datatable
      }//close success
    });//close ajax
  }//close proposed batch table

  // half consign process start here
  $(document).on('click','#consign_half_btn',function(){

    var modal = $("#large-modal").modal();

    modal.find('.modal-title').html('Process Consignment Half / Week');

    methodd = '';

    // methodd +='<div class="col-md-12">';

    methodd += '<div class="col-md-6"><label>Consignment Type </label> <select class="form-control select2 fetch_half_selection" name="select_consign_type" id="select_consign_type" > <option value=""> -SELECT DATA- </option> <option value="half month"> Half Month </option> <option value="weekly"> Weekly </option> <option value="monthly"> Monthly </option> <option value="reflow"> Reflow - No Need Send Email </option> </select>';

    methodd += '<label>Retailer Name </label> <select class="form-control fetch_half_selection" name="select_retailer" id="select_retailer" > <option value=""> -SELECT DATA- </option><?php foreach($retailer->result() as $row) { ?> <option value="<?php echo $row->acc_guid?>"><?php echo addslashes($row->acc_name)?>  </option> <?php } ?></select> </div>';

    methodd += '<div class="col-md-6"><label>Date From </label><div class="input-group date"><div class="input-group-addon"> <i class="fa fa-calendar"></i> </div><input name="half_date_from" id="half_date_from" type="text" class="datepicker form-control input-sm fetch_half_selection" autocomplete="off" ></div>';

    methodd += '<label>Date To </label><div class="input-group date"><div class="input-group-addon"> <i class="fa fa-calendar"></i> </div><input name="half_date_to" id="half_date_to" type="text" class="datepicker form-control input-sm fetch_half_selection" autocomplete="off" ></div></div>';

    methodd += '<div class="col-md-12"><div class="form-group"><label>Supplier Name </label> <button id="dis_select_supplier_option" class="btn btn-xs btn-danger" type="button" style="float: right;margin-left:5px;margin-top:5px;margin-bottom:5px;" >X</button> <button id="select_supplier_option" class="btn btn-xs btn-info" type="button" style="float: right;margin-top:5px;margin-bottom:5px;">ALL</button> <select class="form-control" name="select_supplier" id="select_supplier" multiple="multiple" > </select> </div> </div>';

    methodd +='<div class="col-md-12"> <div class="form-group"> <table id="half_consign_tb" class="table table-bordered table-striped" width="100%" cellspacing="0"> <thead style="white-space: nowrap;"><tr> <th>Action</th> <th>Operation</th> <th>Date To/From</th> <th>Trans-GUID</th> <th>Code/Location</th> <th>Supcus Name</th> <th>Amount</th> <th>Total Incl Tax</th> <th>Date Trans</th> <th>Created At/By</th> <th>Updated At/By</th> <th>Approved At/By</th> <th>Company Info</th> </tr> </thead> <tbody> </tbody></table> <table id="footer" class="table" width="100%"> <tr> <th style="width:200px;">Sum Total Include Amount</th> <th class="sum_total_include_amt alignleft"></th> </tr> </table> </div> </div>';

    // methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-right"><input type="button" id="half_process_btn" class="btn btn-success" value="Process"> <input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    setTimeout(function(){
      $('#select_consign_type').select2();
      $('#select_retailer').select2();
      $('#select_supplier').select2();

      $('.datepicker').datepicker({
        forceParse: false,
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd'
      });

      $('#half_date_from').change(function(){
        var half_date_from_val = $('#half_date_from').val();
        var select_consign_type = $('#select_consign_type').val();
        //alert(half_date_from_val); die;
        if(half_date_from_val != '' && select_consign_type != '')
        {
          var from_someDate = new Date(half_date_from_val);
          var from_dd = from_someDate.getDate();
          var from_mm = from_someDate.getMonth();
          var from_y = from_someDate.getFullYear();  

          if(select_consign_type == 'half month')
          {
            var add_day = 14;
          }
          else if(select_consign_type == 'weekly')
          {
            var add_day = 6;
          }
          else if(select_consign_type == 'monthly')
          {
            var lastDay = new Date(from_y, from_mm + 1, 0).getDate();
            var add_day = lastDay;
          }
          else
          {
            var add_day = 0;
          }

          var sum_from_dd = from_dd + add_day;

          if(sum_from_dd >= 30)
          {
            var from_c = new Date(from_y , from_mm + 1 , 0);
          }
          else
          {
            var from_c = new Date(from_y , from_mm , from_dd + add_day);
          }
          
          var from_cnewDate = new Date(from_c);

          var from_result = from_cnewDate.toLocaleDateString("fr-CA", { // you can use undefined as first argument
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
          });

          $('#half_date_to').val(from_result);
          $('#half_date_to').datepicker("setDate", from_result );
        }
      });//close selection

      $('.fetch_half_selection').on('change', function() {
        var half_date_from = $('#half_date_from').val();
        var half_date_to = $('#half_date_to').val();
        var select_retailer = $('#select_retailer').val();
        
        if(half_date_from != '' && half_date_to != '' && select_retailer != '')
        {
          $.ajax({
            url : "<?php echo site_url('Consignment/fetch_supplier_data'); ?>",
            method:"POST",
            data:{half_date_from:half_date_from,half_date_to:half_date_to,select_retailer:select_retailer},
            success:function(data)
            {
              json = JSON.parse(data); 

              selection = '';

              Object.keys(json['fetch_supplier']).forEach(function(key) {
                selection += '<option value="'+json['fetch_supplier'][key]['supcus_code']+'">'+json['fetch_supplier'][key]['supcus_name']+' || '+json['fetch_supplier'][key]['supcus_code']+' || '+json['fetch_supplier'][key]['sum_total']+' </option>';
              });
              
              $('#select_supplier').select2().html(selection);

              if ($.fn.DataTable.isDataTable('#half_consign_tb')) {
                $('#half_consign_tb').DataTable().destroy();
              }

              $('#half_consign_tb').DataTable({
                "columnDefs": [
                  { "orderable": false, "targets": 0 },
                 { className: "alignright", targets: [6,7] },
                // { className: "alignright", targets: '_all' }
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
                "sScrollY": "60vh", 
                "sScrollX": "100%", 
                "sScrollXInner": "100%", 
                "bScrollCollapse": true,
                  data: json['query_table_data'],
                  columns: [
                    { "data": "locgroup", render:function( data, type, row ){
                      var element = '';


                      return element;
                    }},
                    { "data": "operation"},
                    { "data": "date_from", render:function( data, type, row ){
                      var element = '';

                      element += row['date_from'] + '<br>'+ row['date_to'];

                      return element;
                    }},
                    { "data": "trans_guid"},
                    { "data": "supcus_code", render:function( data, type, row ){
                      var element = '';

                      element += row['supcus_code']+ '<br>'+ row['locgroup'];

                      return element;
                    }},
                    { "data": "supcus_name"},
                    { "data": "amount"},
                    { "data": "total_inc_tax"},
                    { "data": "date_trans"},
                    { "data": "created_at", render:function( data, type, row ){
                      var element = '';

                      element += row['created_at']+ '<br>'+ row['created_by'];

                      return element;
                    }},
                    { "data": "updated_at", render:function( data, type, row ){
                      var element = '';

                      element += row['updated_at']+ '<br>'+ row['updated_by'];

                      return element;
                    }},
                    { "data": "approved_at", render:function( data, type, row ){
                      var element = '';

                      element += row['approved_at']+ '<br>'+ row['approved_by'];

                      return element;
                    }},
                    { "data": "company_name", render:function( data, type, row ){
                      var element = '';

                      element += row['company_name']+ '<br>'+ row['company_id'];

                      return element;
                    }},
                  ],
                  dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>Brtip", 
                "language": {
                    "lengthMenu": "Show _MENU_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "zeroRecords": "<span ><?php echo '<b>No Record Found.</b>'; ?></span>",
                  }, 
                "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                    $(nRow).closest('tr').css({"cursor": "pointer"});
                    // $(nRow).attr('poex_guid', aData['poex_guid']);
                    // $(nRow).find('td:eq(1)').css({"background-color":"#ffb84d","color":"black"});
                    // $(nRow).find('td:eq(2)').css({"background-color":"#ffb84d","color":"black"});
                    // $(nRow).find('td:eq(3)').css({"background-color":"#ffb84d","color":"black"});
                    // $(nRow).find('td:eq(4)').css({"background-color":"#ffb84d","color":"black"});
                    // $(nRow).find('td:eq(5)').css({"background-color":"#ffb84d","color":"black"});
                    // $(nRow).find('td:eq(6)').css({"background-color":"#ffb84d","color":"black"});
                },
                "initComplete": function( settings, json ) {
                  interval();
                },
              });//close datatable
            }
          });
        }
        else
        {
          $('#select_supplier').html('');
          $('#half_consign_tb').DataTable().clear().draw();
        }
        
      });

      $('#select_supplier').on('change', function() {
        var half_date_from = $('#half_date_from').val();
        var half_date_to = $('#half_date_to').val();
        var select_retailer = $('#select_retailer').val();
        var select_supplier = $('#select_supplier').val();
        
        if(half_date_from != '' && half_date_to != '' && select_retailer != '' && (select_supplier != '' || select_supplier != 'null' || select_supplier != null))
        {
          $.ajax({
            url : "<?php echo site_url('Consignment/fetch_sales_data'); ?>",
            method:"POST",
            data:{half_date_from:half_date_from,half_date_to:half_date_to,select_retailer:select_retailer,select_supplier:select_supplier},
            success:function(data)
            {
              json = JSON.parse(data); 

              if ($.fn.DataTable.isDataTable('#half_consign_tb')) {
                $('#half_consign_tb').DataTable().destroy();
              }

              $('#half_consign_tb').DataTable({
                "columnDefs": [
                  { "orderable": false, "targets": 0 },
                 { className: "alignright", targets: [6,7] },
                // { className: "alignright", targets: '_all' }
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
                "sScrollY": "60vh", 
                "sScrollX": "100%", 
                "sScrollXInner": "100%", 
                "bScrollCollapse": true,
                  data: json['query_table_data'],
                  columns: [
                    { "data": "locgroup", render:function( data, type, row ){
                      var element = '';


                      return element;
                    }},
                    { "data": "operation"},
                    { "data": "date_from", render:function( data, type, row ){
                      var element = '';

                      element += row['date_from'] + '<br>'+ row['date_to'];

                      return element;
                    }},
                    { "data": "trans_guid"},
                    { "data": "supcus_code", render:function( data, type, row ){
                      var element = '';

                      element += row['supcus_code']+ '<br>'+ row['locgroup'];

                      return element;
                    }},
                    { "data": "supcus_name"},
                    { "data": "amount"},
                    { "data": "total_inc_tax"},
                    { "data": "date_trans"},
                    { "data": "created_at", render:function( data, type, row ){
                      var element = '';

                      element += row['created_at']+ '<br>'+ row['created_by'];

                      return element;
                    }},
                    { "data": "updated_at", render:function( data, type, row ){
                      var element = '';

                      element += row['updated_at']+ '<br>'+ row['updated_by'];

                      return element;
                    }},
                    { "data": "approved_at", render:function( data, type, row ){
                      var element = '';

                      element += row['approved_at']+ '<br>'+ row['approved_by'];

                      return element;
                    }},
                    { "data": "company_name", render:function( data, type, row ){
                      var element = '';

                      element += row['company_name']+ '<br>'+ row['company_id'];

                      return element;
                    }},
                  ],
                  dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>rtip", 
                "language": {
                    "lengthMenu": "Show _MENU_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "zeroRecords": "<span ><?php echo '<b>No Record Found.</b>'; ?></span>",
                  }, 
                "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                    $(nRow).closest('tr').css({"cursor": "pointer"});
                    // $(nRow).attr('poex_guid', aData['poex_guid']);
                    // $(nRow).find('td:eq(1)').css({"background-color":"#ffb84d","color":"black"});
                },
                "initComplete": function( settings, json ) {
                  interval();
                },
                "footerCallback": function ( row, data, start, end, display ) {
                  var api = this.api(), data;

                  var data = $.fn.dataTable.render.number( '\,', '.', 2, 'RM' ).display;

                  // Remove the formatting to get integer data for summation
                  var intVal = function ( i ) {
                      return typeof i === 'string' ?
                          i.replace(/[\$,]/g, '')*1 :
                          typeof i === 'number' ?
                              i : 0;
                  };
                  // Total over all pages
                  total = api
                      .column( 7 )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );
                      
                  // Total over this page
                  pageTotal = api
                      .column( 7, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );

                  // Update footer
                  $( '.sum_total_include_amt' ).html(
                      /*''+(pageTotal).toFixed(2) +' <hr>'+ (total).toFixed(2)+''*/
                      (total).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")+''
                  );
                }
              });//close datatable
            }
          });
        }
        else
        {
          $('#half_consign_tb').DataTable().clear().draw();
        }
        
      });

      $('#half_consign_tb').DataTable({
        "columnDefs": [{"targets": '_all' ,"orderable": false}],
          'order': [],
          "sScrollY": "30vh", 
            "sScrollX": "100%", 
            "sScrollXInner": "100%", 
            "bScrollCollapse": true,
          dom: "<'row'<'col-sm-2 remove_padding_right 'l > <'col-sm-10' f>  " + "<'col-sm-1' <'toolbar_list'>>>" +'rt<"row" <".col-md-4 remove_padding" i>  <".col-md-8" p> >',
            "language": {
                    "lengthMenu": "Display _MENU_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "info":           "Show _START_ - _END_ of _TOTAL_ entry",
                    "zeroRecords": "<?php echo '<b>No Record Found. Please Select Supplier Name to view data.</b>'; ?>",
          },
          "pagingType": "simple_numbers",
      });
      $('.remove_padding_right').css({'text-align':'left'});
      $("div.remove_padding").css({"text-align":"left"});
    },300);
  });

  $(document).on('click', '#select_supplier_option', function(){
    $("#select_supplier option").prop('selected',true).trigger('change');
    var selectedOptions = $('#select_supplier').val();
    $("#select_supplier").select2();
  });//CLOSE ONCLICK  

  $(document).on('click', '#dis_select_supplier_option', function(){
    $("#select_supplier option").prop('selected',false);
    var selectedOptions = $('#select_supplier').val();
    $("#select_supplier").select2();
    $('#half_consign_tb').DataTable().clear().draw();
  });//CLOSE ONCLICK 

  $(document).on('click','#half_process_btn',function(){
    var half_date_from = $('#half_date_from').val();
    var half_date_to = $('#half_date_to').val();
    var select_retailer = $('#select_retailer').val();
    var select_supplier = $('#select_supplier').val();
    var select_consign_type = $('#select_consign_type').val();
    
    // alert(half_date_to); die;
    if(select_consign_type == '' || select_consign_type == null || select_consign_type == 'null')
    {
      alert('Invalid Process. ERROR CODE 00');
      return;
    }

    if((half_date_from == '') || (half_date_from == null) || (half_date_from == 'null'))
    {
      alert('Invalid Process. ERROR CODE 01');
      return;
    }

    if((half_date_to == '') || (half_date_to == null) || (half_date_to == 'null'))
    {
      alert('Invalid Process. ERROR CODE 02');
      return;
    }

    if(half_date_to < half_date_from) 
    {
      alert('Date From cannot be smaller than Date To.');
      return;
    }

    if((select_retailer == '') || (select_retailer == null) || (select_retailer == 'null'))
    {
      alert('Invalid Process. ERROR CODE 03');
      return;
    }

    if((select_supplier == '') || (select_supplier == null) || (select_supplier == 'null'))
    {
      alert('Please Select Supplier.');
      return;
    }

    confirmation_modal('Are you sure want to Proceed Cosnignment.');
    $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
      $.ajax({
        url:"<?php echo site_url('Consignment/half_run_process') ?>",
        method:"POST",
        data:{half_date_from:half_date_from,half_date_to:half_date_to,select_retailer:select_retailer,select_supplier:select_supplier,select_consign_type:select_consign_type},
        beforeSend:function(){
          $('.btn').button('loading');
        },
        success:function(result)
        {
          json = JSON.parse(result);
          if(json.status == 'false')
          {
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            alert(json.message);
          }
          else
          {
            $('.btn').button('reset');
            $('#alertmodal').modal('hide');
            $("#medium-modal").modal('hide');
            alert(json.message);
            location.reload();
          }
        }//close success
      });//close ajax 
    });//close document yes click
  });//close redirect
  
  // end half consign process here

});
</script>

<script type="text/javascript">
  
  function expiry_clear()
  {
    $(function() {
        $(this).find('[name="date_from"]').val("");
        $(this).find('[name="date_to"]').val("");
    });
  }

  function get_layout_email_recipient(date_start,date_end,retailer){

    $.ajax({
      url : "<?php echo site_url('Consignment/consignment_email_statement');?>",
      dataType: 'html',
      method: "POST",
      data:{date_start:date_start,date_end:date_end,retailer:retailer},
      beforeSend : function() {
        $('.btn').button('loading');
      },
      complete: function() {
        $('.btn').button('reset');
      },
      success: function(html) {         
        $('#email-recipient-layout').html(html);
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });

    $('#email-recipient-layout').removeClass('hidden');
  }

</script>