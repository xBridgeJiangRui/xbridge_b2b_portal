<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="container-fluid">
        <br>

        <!-- Success Message -->
        <?php if ($this->session->userdata('message')) { ?>
            <div class="alert alert-success text-center" style="font-size: 18px">
                <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
            </div>
        <?php } ?>

        <!-- Warning Message -->
        <?php if ($this->session->userdata('warning')) { ?>
            <div class="alert alert-danger text-center" style="font-size: 18px">
                <?php echo $this->session->userdata('warning') <> '' ? $this->session->userdata('warning') : ''; ?>
                <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
            </div>
        <?php } ?>

        <!-- Filter by -->
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="box box-default">
                    <!-- Head -->
                    <div class="box-header with-border">
                        <h3 class="box-title">Filter By</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <!-- Head -->

                    <!-- Body -->
                    <div class="box-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2"><b>Customer</b></div>
                                    <div class="col-md-4">
                                        <select name="customer_guid" id="customer_guid" class="form-control">
                                            <?php
                                            foreach($acc->result() as $row)
                                            {
                                            ?>

                                                <option value="<?=$row->acc_guid;?>"><?=$row->acc_name;?></option>

                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="clearfix"></div><br>

                                    <div class="col-md-2"><b>Vendor Code</b></div>
                                        <div class="col-md-4">
                                            <select name="vendor_guid" id="vendor_guid" class="form-control select2">
                                            <option value="">None</option>
                                            </select>
                                        </div>

                                    <div class="clearfix"></div><br>

                                    <div class="col-md-2"><b>GR Ref No</b></div>
                                        <div class="col-md-4">
                                            <input id="gr_num" name="gr_num" type="text" autocomplete="off" class="form-control pull-right" spellcheck="false">
                                        </div>

                                    <div class="clearfix"></div><br>

                                    <div class="col-md-2"><b>GR Status</b></div>
                                        <div class="col-md-4">
                                            <select name="gr_status" id="gr_status" class="form-control">
                                            <?php
                                            foreach($gr_status->result() as $row)
                                            {
                                            ?>

                                                <option value="<?=$row->code;?>"><?=$row->reason;?></option>

                                            <?php
                                            }
                                            ?>
                                            </select>                

                                        </div>

                                    <div class="clearfix"></div><br>

                                    <div class="col-md-2"><b>GR Date Range<br>(YYYY-MM-DD)</b></div>
                                        <div class="col-md-4">
                                            <input required id="daterange" name="daterange" type="datetime" class="form-control pull-right" id="reservationtime" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-danger"  onclick="date_clear()">Clear</a>
                                        </div>
                                    
                                    <div class="clearfix"></div><br>

                                    <div class="col-md-2"><b>Filter by Period Code<br>(YYYY-MM)</b></div>
                                        <div class="col-md-4">
                                            <select name="period_code" id="period_code" class="form-control">
                                            <option value="">None</option>

                                            <?php
                                            foreach($period_code->result() as $row)
                                            {
                                            ?>

                                                <option value="<?= $row->period_code;?>"><?= $row->period_code;?></option>
                                            <?php
                                            }
                                            ?>


                                            </select> 
                                        </div>
                                        
                                        <div class="clearfix"></div><br>

                                    <div class="col-md-12">
                                        <button type="button" id="search" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                        <a href="<?php echo site_url('Troubleshoot_grn');?>" class="btn btn-default" ><i class="fa fa-repeat"></i> Reset</a>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- Body -->

                </div>
            </div>
        </div>
        <!-- filter by -->

        <!-- Data Table -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><b>Good Received Note</b></h3> &nbsp;
                <span id="parameter_span"></span>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>

            <div class="box-body">
                <br>
                 <div class="col-md-12"  style="overflow-x:auto"> 
                 <table id="gr_new_table" class="table table-bordered table-hover" >
                    <thead>
                        <tr>
                            <!-- Column Headers -->
                            <th>GR Refno</th>
                            <th>GRDA</th>
                            <th>Outlet</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>GR Date</th>
                            <th>Inv No</th>
                            <th>E-Inv No</th>
                            <th>Amount</th>
                            <th>Tax</th>
                            <th>Total Include Tax</th>
                            <th>Status</th>
                            <th>Action</th>
                            <!-- End Column Headers -->
                        </tr>
                    </thead>
                </table>
                 </div>
            </div>
        </div>
        
    </div>
</div>

<script>  
 $(document).ready(function(){ 
    
    gr_new_table = function()
    {
      if ( $.fn.DataTable.isDataTable('#gr_new_table') ) {
        $('#gr_new_table').DataTable().destroy();
      }

      var customer_guid = $('#customer_guid').val();
      var vendor_guid = $('#vendor_guid').val();
      var gr_num = $('#gr_num').val();
      var gr_status = $('#gr_status').val();
      var daterange = $('#daterange').val();
      var period_code = $('#period_code').val();

      span_button_para = '';

      if(customer_guid != '' && customer_guid != null)
      { 
        var display_customer = $('#customer_guid option:selected').text();

        span_button_para += '<span class="pill_button"> '+display_customer+' </span>';
      }

      if(vendor_guid != '' && vendor_guid != null)
      { 
        var display_vendor = $('#vendor_guid option:selected').text()

        span_button_para += '<span class="pill_button">Vendor Code :'+display_vendor+'</span>';
      }

      if(gr_num != '' && gr_num != null)
      {
        span_button_para += '<span class="pill_button"> '+gr_num+' </span>';
      }

      if (gr_status != null) 
      {
        if (gr_status === 'geinv') 
        {
        span_button_para += '<span class="pill_button">New - Viewed - Printed</span>';
        } 
        else if (gr_status === '') 
        {
        span_button_para += '<span class="pill_button">New</span>';
        } 
        else 
        {
        span_button_para += '<span class="pill_button">' + gr_status + '</span>';
        }
      }

      if(daterange != '' && daterange != null)
      {
        span_button_para += '<span class="pill_button">GR Date Range :'+daterange+'</span>';
      }

      if(period_code != '' && period_code != null)
      {
        span_button_para += '<span class="pill_button">Period Code :'+period_code+'</span>';
      }

      $('#parameter_span').html('');

      $('#parameter_span').html(span_button_para);

      var table;

      table = $('#gr_new_table').DataTable({
                "columnDefs": [ {"targets": [4] ,"orderable": false}],
                "serverSide": true, 
                'processing'  : true,
                'paging'      : true,
                'lengthChange': true,
                'lengthMenu'  : [ [10, 25, 50, 9999999999999999], [10, 25, 50, "ALL"] ],
                'searching'   : true,
                'ordering'    : true,
                'order'       : [ [2 , 'desc'] ],
                'info'        : true,
                'autoWidth'   : false,
                "bPaginate": true, 
                "bFilter": true, 
                "ajax": {
                    "url": "<?php echo site_url('Troubleshoot_grn/gr_new_table');?>",
                    "type": "POST",
                    data : {
                            customer_guid:customer_guid,
                            vendor_guid:vendor_guid,
                            gr_num:gr_num,
                            gr_status:gr_status,
                            daterange:daterange,
                            period_code:period_code   
                    },
                    complete:function()
                    {
                    },
                },

                columns: [
                {"data":"RefNo"},
                {"data":"grda_refno"},
                {"data":"Location"},
                {"data":"Code"},
                {"data":"Name"},
                {"data":"GRDate"},
                {"data":"InvNo"},
                {"data":"einvno"},
                {"data":"Total"},
                {"data":"gst_tax_sum"},
                {"data":"total_include_tax"},
                {"data":"status"},
                {"data":"filename" ,render: function ( data, type, row ) {
                
                element = '<span style="display:flex;">';

                element += '<button style="float:left" id="open_modal_troubleshoot_gr" filename="'+data+'" class="btn btn-sm btn-info" role="button"><i class="glyphicon glyphicon-eye-open"></i></button>';

                element += '<button style="float:left;margin-left:5px;" id="open_modal_troubleshoot_gr_useraction" RefNo="'+row['RefNo']+'"" class="btn btn-sm btn-warning" role="button"><i class="fa fa-bars"></i></button>';

                element == '</div>';

                return element;

              }},
             ],

                "buttons": [
                {
                    extend: 'excelHtml5',
                    exportOptions: { orthogonal: 'export' }
                },

                ],

                dom:'<"row"<"col-sm-2" l><"col-sm-4" B><"col-sm-6" f>>rtip',

                "fnCreatedRow": function( nRow, aData, iDataIndex ) {

                },
                "initComplete": function( settings, json ) {
                setTimeout(function(){
                    interval();
                    $('.btn').button('reset');
                },300);
                }
      });//close datatable

    }//close gr_new_table

    $('#gr_new_table').DataTable();

    $(document).on('click','#open_modal_troubleshoot_gr',function(){

    var filename = $(this).attr('filename');

    var modal = $("#large-modal").modal();

    modal.find('.modal-title').html('GR');

    methodd = '';

    methodd +='<div class="col-md-12">';

    methodd += '<embed src="'+filename+'" width="100%" height="500px" style="border: none;">';


    methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-right"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    });//close click button modal

    $(document).on('click','#open_modal_troubleshoot_gr_useraction',function(){

    var modal = $("#medium-modal").modal();

    modal.find('.modal-title').html('Details');

    methodd = '';

    methodd +='<div class="col-md-12">';

    methodd += '<table id="user_movements_table_details" class="table table-bordered table-hover"><thead><tr><th>User ID</th><th>Value</th><th>Action</th><th>Created At</th><th>Type</th></tr></thead></table>';


    methodd += '</div>';

    methodd_footer = '<p class="full-width"><span class="pull-right"><input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

    modal.find('.modal-footer').html(methodd_footer);
    modal.find('.modal-body').html(methodd);

    var customer_guid = $('#customer_guid').val();
    var RefNo = $(this).attr('RefNo');

    $.ajax({
            url : "<?php echo site_url('Troubleshoot_grn/user_movements_table'); ?>",
            type: "POST",
            data:{customer_guid:customer_guid,RefNo:RefNo},
            beforeSend : function() {
                $('.btn').button('loading');
            },
            complete: function() {
                $('.btn').button('reset');
            },
            success : function(data){
            
            json = JSON.parse(data);

            if ($.fn.DataTable.isDataTable('#user_movements_table_details')) {
                $('#user_movements_table_details').DataTable().destroy();
            }

            $('#user_movements_table_details').DataTable({
                // "columnDefs": [{ "orderable": false, "targets": 0 }],
                'processing'  : true,
                'paging'      : true,
                'lengthChange': true,
                'lengthMenu'  : [ [10, 25, 50, -1], [10, 25, 50, "ALL"] ],
                'searching'   : true,
                'ordering'    : true,
                'order'       : [ [3 , 'desc'] ],
                'info'        : true,
                'autoWidth'   : false,
                "bPaginate": true, 
                "bFilter": false, 
                "sScrollY": "30vh", 
                "sScrollX": "100%", 
                "sScrollXInner": "100%", 
                "bScrollCollapse": true,
                "language": {
                    "zeroRecords": "No movements.",
                    "infoEmpty": "",
                },
                data: json['movements'],
                columns: [   
                        {"data": "user_id"},
                        {"data": "value",render: function ( data, type, row ) {
                        
                            return '<span class="label label-default" style="font-size:14px;">'+data+'</span>';
                        }},
                        {"data": "action"},
                        {"data": "c_date"},
                        {"data": "type"}
                        ],                       
                dom: '<"row" <"col-sm-6"l><"col-sm-6" f> >rt  <"row" <"col-sm-6"i><"col-sm-6" p> >',
                "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                },
                "initComplete": function( settings, json ) {
                    setTimeout(function() {
                    interval();
                    }, 300);
                }
            });//close datatable
            }//close success
        });//close ajax
    });//close click button modal


    $(document).on('change','#customer_guid',function(){

    var customer_guid = $('#customer_guid').val();

    $.ajax({
                url:"<?php echo site_url('Troubleshoot_grn/vendor_code_dropdown');?>",
                method:"POST",
                data:{customer_guid:customer_guid},
                beforeSend:function(){
                $('.btn').button('loading');
                $('#vendor_guid').prepend($('<option></option>').html('LOADING...'));
                },
                complete:function(){
                },
                success:function(data)
                {
                json = JSON.parse(data);

                var set_supplier = '';

                Object.keys(json['set_supplier']).forEach(function(key) {

                    set_supplier += '<option value="'+json['set_supplier'][key]['supplier_guid']+'">';

                    set_supplier += json['set_supplier'][key]['supplier_name'];

                    set_supplier += '</option>';

                });

                $('#vendor_guid').html(set_supplier);
                $('.btn').button('reset');

                }//close success
            });//close ajax

    });

    $('#customer_guid').trigger('change');

    $(document).on('click','#search',function(){

    var customer_guid = $('#customer_guid').val();
    var vendor_guid = $('#vendor_guid').val();

    if(customer_guid == '' || customer_guid == null)
    {
    alert('Please select a customer to proceed.');
    return;
    }

    if(vendor_guid == '' || vendor_guid == null)
    {
    alert('Vendor Code must have value.');
    return;
    }

    // alert("Happy Holiday by Teng Chee Ming");die;

    gr_new_table();
    });
    
    

 });  
 </script>  

<script>

    $(function() {
    $('input[name="daterange"]').daterangepicker({
        locale: {
        format: 'YYYY-MM-DD'
        },
    });
    $(this).find('[name="daterange"]').val("");
    });

</script>
 

<script type="text/javascript">

    function date_clear()
    {
        $(function() {
            $(this).find('[name="daterange"]').val("");
        });
    }


</script>


