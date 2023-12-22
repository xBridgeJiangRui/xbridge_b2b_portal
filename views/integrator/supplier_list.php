<style type="text/css">
.content-wrapper{
  min-height: 1200px !important; 
}

.select2-container--default .select2-selection--multiple .select2-selection__choice
{
  background: #3c8dbc;
} 
</style>
<div class="content-wrapper" style="min-height: 525px; text-align: justify;">
<div class="container-fluid">
<br>

  <div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Filter By</h3><br>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2"><b>Integration Retailair Code</b></div>
                        <div class="col-md-4">
                            <select id="select_int_code" class="form-control select2" >
                                <option value="" selected="">-INTEGRATION CODE-</option>
                                <?php foreach ($get_config_settings as $row) { ?>
                                    <option value="<?php echo $row->integration_guid ?>">
                                      <?php echo $row->integration_name; ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="clearfix"></div><br>

                        <div class="col-md-2"><b>Debtor Code</b></div>
                        <div class="col-md-4">
                            <select id="select_acc_code" class="form-control select2" multiple="multiple">
                                <!-- <option value="" selected="">-STATUS-</option> -->
                                <?php foreach ($get_code_list->result() as $row) { ?>
                                    <option value="<?php echo $row->acc_code ?>">
                                      <?php echo $row->supplier_name; ?> || <?php echo $row->acc_code; ?>  </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-1"> 
                            <button id="location_all_dis" class="btn btn-danger" type="button" >X</button> 
                            <!-- <button id="location_all" class="btn btn-xs btn-info" type="button" style="float: right;margin-top:5px;margin-bottom:5px;">ALL</button>  -->
                        </div>

                        <div class="clearfix"></div><br>

                        <div class="col-md-2"><b>Period Code</b></div>
                        <div class="col-md-4">
                            <select id="select_period_code" class="form-control select2" multiple="mutliple">
                                <!-- <option value="" selected="">-PERIOD CODE-</option> -->
                                <?php foreach ($get_period_list->result() as $row) { ?>
                                    <option value="<?php echo $row->period_code ?>">
                                      <?php echo $row->period_code; ?> </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="clearfix"></div><br>

                        <div class="col-md-12">
                            <button id="search_data" class="btn btn-primary" ><i class="fa fa-search"></i> Search</button>
                            <button id="reset_data" class="btn btn-secondy"><i class="fa fa-repeat"></i> Reset</button>
                        </div>
                        <!-- </form> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>


  <div class="row">
    <div class="col-md-12 col-xs-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Integrator Supplier</h3>
          <div class="box-tools pull-right">
            <?php if(in_array('IAVA',$this->session->userdata('module_code')))
            {
            ?>

            <button type="button" class="btn btn-xs btn-warning" id="trigger_batch_btn"><i class="glyphicon glyphicon-send" aria-hidden="true"></i>&nbsp&nbspTrigger</button>

            <?php
            }
            ?>

          </div> <!--end pull right button -->
        </div>
        <!-- /.box-header -->
        <div class="box-body" >
          <div id="">
          
                  <table id="supplier_table" class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead style="white-space: nowrap;">
                    <tr>
                        <th>
                           <input type="checkbox" id="checkall_input_table" name="checkall_input_table" table_id="supplier_table">
                        </th> 
                        <th>Action</th>
                        <th>Supplier Name</th>
                        <th>Reg No</th>
                        <th>Name Reg</th>
                        <th>Active</th>
                        <th>Suspended</th>
                        <th>GST No.</th>
                        <th>Acc Code</th>
                        <th>Payment Term</th>
                        <th>Created At</th>
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

  <!-- INTEGRATION TABLE LOG -->
  <div class="row">
    <div class="col-md-6 col-xs-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Integrator Supplier Logs</h3>
          <div class="box-tools pull-right">

          </div> <!--end pull right button -->
        </div>
        <!-- /.box-header -->
        <div class="box-body" >
          <div id="">
          
                  <table id="supplier_int_table" class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead style="white-space: nowrap;">
                    <tr>
                        <th>Supplier Name</th>
                        <th>Reg No</th>
                        <th>Acc Code</th>
                        <th>Status</th>
                        <th>Created At</th>
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
</div>
<script>
var select_acc_code = '';
var select_period_code = '';
$(document).ready(function() {

    $(document).on('click','#search_data',function(){
        var select_acc_code = $('#select_acc_code').val();
        var select_period_code = $('#select_period_code').val();
        // alert(select_acc_code); die;
        main_table(select_acc_code,select_period_code);
    });

    $(document).on('click','#reset_data',function(){
        var select_acc_code = $('#select_acc_code').val('').trigger('change');
        var select_period_code = $('#select_period_code').val('').trigger('change');
        // alert(select_acc_code); die;
    });
    
    main_table = function(select_acc_code,select_period_code) {
        if ($.fn.DataTable.isDataTable('#supplier_table')) {
        $('#supplier_table').DataTable().destroy();
        }

        var table;

        table = $('#supplier_table').DataTable({
        "scrollX": true,
        "processing": true,
        "serverSide": true,
        "lengthMenu": [ [10, 25, 50, 9999999], [10, 25, 50, 'All'] ],
        "sScrollY": "50vh",
        "sScrollX": "100%", 
        "sScrollXInner": "100%", 
        "bScrollCollapse": true,
        "order": [
            // [6, "desc"]
        ],
        "columnDefs": [
            { "orderable": false, "targets": [0,1]},
        ],
        "ajax": {
            "url": "<?php echo site_url('Integrator_section/supplier_tb') ?>",
            "type": "POST",
            "data": function(data) {
              data.select_acc_code = select_acc_code
              data.select_period_code = select_period_code
            },
        },
        columns: [
            { "data": "supplier_guid" , render:function( data, type, row ){

                var element = '';
                var element1 = row['form_status'];

                <?php if(in_array('IAVA',$this->session->userdata('module_code')))
                {
                    ?>
                    if((element1 == '') || (element1 == 'null') || (element1 == null))
                    {
                        element += '<input type="checkbox" class="form-checkbox" name="trigger_check_box" id="trigger_check_box" supplier_guid ="'+row['supplier_guid']+'" supplier_name ="'+row['supplier_name']+'" reg_no ="'+row['reg_no']+'" name_reg ="'+row['name_reg']+'" isactive ="'+row['isactive']+'" suspended ="'+row['suspended']+'" gst_no ="'+row['gst_no']+'" acc_code ="'+row['acc_code']+'" payment_term ="'+row['payment_term']+'"/>';
                    }
                    <?php
                }
                ?>

                return element;

            }},
            { "data": "supplier_guid", render: function (data, type, row) {
                var buttons = '';

                buttons += '<button id="sync_btn" title="TRIGGER" class="btn btn-xs btn-warning" style="margin-right: 5px;" supplier_guid ="' +row['supplier_guid']+ '"><i class="glyphicon glyphicon-send"></i></button>';

                return buttons;
            }},
            { "data": "supplier_name" },
            { "data": "reg_no" },
            { "data": "name_reg" },
            { "data": "isactive",render: function (data) {return data == 1 ? 'Yes' : 'No';}},
            { "data": "suspended",render: function (data) {return data == 1 ? 'Yes' : 'No';}},
            { "data": "gst_no" },
            { "data": "acc_code" },
            { "data": "payment_term" },
            { "data": "created_at" }
        ],
        //dom: 'lBfrtip',
        dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'Brtip',
        // buttons: [
        //   'copy', 'csv', 'excel', 'pdf', 'print'
        // ]
        "fnCreatedRow": function( nRow, aData, iDataIndex ) 
        {
            $(nRow).closest('tr').css({"cursor": "pointer"});
        }
        });
    }
    main_table(select_acc_code,select_period_code);

    $(document).on('change','#checkall_input_table',function(){

        var id = $(this).attr('table_id');

        var table = $('#'+id).DataTable();

        if($(this).is(':checked'))
        {
            table.rows().nodes().to$().each(function(){

                $(this).find('td').find('#trigger_check_box').prop('checked',true)

            });//close small loop
        }
        else
        {
            table.rows().nodes().to$().each(function(){

                $(this).find('td').find('#trigger_check_box').prop('checked',false)

            });//close small loop
        }//close else

    });//close checkbox all set_group_table

    $(document).on('click', '#trigger_batch_btn', function(event){

        var details = [];
        var table = $('#supplier_table').DataTable();
        var i = 0;
        shoot_link = 0;
        table.rows().nodes().to$().each(function(){
            if($(this).find('td').find('#trigger_check_box').is(':checked'))
            {
                var supplier_guid = $(this).find('td').find('#trigger_check_box').attr('supplier_guid');
                var supplier_name = $(this).find('td').find('#trigger_check_box').attr('supplier_name');
                var reg_no = $(this).find('td').find('#trigger_check_box').attr('reg_no');
                var name_reg = $(this).find('td').find('#trigger_check_box').attr('name_reg');
                var isactive = $(this).find('td').find('#trigger_check_box').attr('isactive');
                var suspended = $(this).find('td').find('#trigger_check_box').attr('suspended');
                var gst_no = $(this).find('td').find('#trigger_check_box').attr('gst_no');
                var acc_code = $(this).find('td').find('#trigger_check_box').attr('acc_code');
                var payment_term = $(this).find('td').find('#trigger_check_box').attr('payment_term');

                if((supplier_guid == '')|| (supplier_guid == 'null')|| (supplier_guid == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid GUID.');
                }

                if((supplier_name == '')|| (supplier_name == 'null')|| (supplier_name == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Supplier Name.');
                }

                if((reg_no == '')|| (reg_no == 'null')|| (reg_no == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid REG NO.');
                }

                if((name_reg == '')|| (name_reg == 'null')|| (name_reg == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Name REG.');
                }

                if((isactive == '')|| (isactive == 'null')|| (isactive == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Active Status.');
                }

                if((suspended == '')|| (suspended == 'null')|| (suspended == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Suspended.');
                }

                if((acc_code == '')|| (acc_code == 'null')|| (acc_code == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Acc Code.');
                }

                i++;
            }
        });//close small loop
        //console.log(details);
        if(shoot_link >= 1)
        {
            return;
        }

        var modal = $("#medium-modal").modal();

        modal.find('.modal-title').html('Select Integration Settings');

        methodd = '';

        methodd +='<div class="col-md-12">';

        methodd += '<div class="form-group"><label>Integration Settings </label> <select class="form-control select2" name="batch_select_integration" id="batch_select_integration" > <option value=""> -SELECT DATA- </option><?php foreach($get_config_settings as $row) { ?> <option value="<?php echo $row->setting_guid?>"><?php echo addslashes($row->integration_name)?>  </option> <?php } ?></select> </div> ';
        
        methodd += '</div>';

        methodd_footer = '<p class="full-width"><span class="pull-right"><input type="button" id="submit_batch_btn" class="btn btn-success" value="Submit"> <input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

        modal.find('.modal-footer').html(methodd_footer);
        modal.find('.modal-body').html(methodd);

        setTimeout(function(){
            $('#select_supplier').select2();
        },300);

    });

    $(document).on('click', '#submit_batch_btn', function(event){
        var selection_integration_batch = $('#batch_select_integration').val();
        // alert(selection_integration_batch); die;
        var details = [];
        var table = $('#supplier_table').DataTable();
        var i = 0;
        shoot_link = 0;
        table.rows().nodes().to$().each(function(){
            if($(this).find('td').find('#trigger_check_box').is(':checked'))
            {
                var supplier_guid = $(this).find('td').find('#trigger_check_box').attr('supplier_guid');
                var supplier_name = $(this).find('td').find('#trigger_check_box').attr('supplier_name');
                var reg_no = $(this).find('td').find('#trigger_check_box').attr('reg_no');
                var name_reg = $(this).find('td').find('#trigger_check_box').attr('name_reg');
                var isactive = $(this).find('td').find('#trigger_check_box').attr('isactive');
                var suspended = $(this).find('td').find('#trigger_check_box').attr('suspended');
                var gst_no = $(this).find('td').find('#trigger_check_box').attr('gst_no');
                var acc_code = $(this).find('td').find('#trigger_check_box').attr('acc_code');
                var payment_term = $(this).find('td').find('#trigger_check_box').attr('payment_term');

                if((supplier_guid == '')|| (supplier_guid == 'null')|| (supplier_guid == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid GUID.');
                }

                if((supplier_name == '')|| (supplier_name == 'null')|| (supplier_name == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Supplier Name.');
                }

                if((reg_no == '')|| (reg_no == 'null')|| (reg_no == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid REG NO.');
                }

                if((name_reg == '')|| (name_reg == 'null')|| (name_reg == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Name REG.');
                }

                if((isactive == '')|| (isactive == 'null')|| (isactive == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Active Status.');
                }

                if((suspended == '')|| (suspended == 'null')|| (suspended == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Suspended.');
                }

                if((acc_code == '')|| (acc_code == 'null')|| (acc_code == null))
                {
                    shoot_link = shoot_link+1;
                    alert('OPPPS..Invalid Acc Code.');
                }

                details.push({'supplier_guid':supplier_guid,'supplier_name':supplier_name,'reg_no':reg_no,'name_reg':name_reg,'isactive':isactive,'suspended':suspended,'acc_code':acc_code,'payment_term':payment_term,'gst_no':gst_no});
                i++;
            }
        });//close small loop
        //console.log(details);
        if(details == '' || details == null || details == 'null')
        {
            shoot_link = shoot_link+1;
            alert('Please select checkbox to proceed TRIGGER.');
        }

        if(selection_integration_batch == '' || selection_integration_batch == null || selection_integration_batch == 'null')
        {
            shoot_link = shoot_link+1;
            alert('Please Select Trigger Settings.');
        }
        
        if(shoot_link == 0)
        {
            confirmation_modal('<b>'+i+' Row(s) Selected.</b><br> Are you sure want to SEND?');
            $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
                $.ajax({
                url:"<?php echo site_url('Integrator_section/integrator_trigger_batch') ?>",
                method:"POST",
                data:{details:details,selection_integration_batch:selection_integration_batch},
                beforeSend:function(){
                    $('.btn').button('loading');
                    swal.fire({
                        allowOutsideClick: false,
                        title: 'Processing...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        onOpen: function () {
                        swal.showLoading()
                        }
                    });
                },
                complete: function() {
                  setTimeout(function() {
                      Swal.close();
                  }, 300);
                },
                success:function(data)
                {
                    json = JSON.parse(data);

                    if(json.status == 'false')
                    {
                        $('.btn').button('reset');
                        $('#alertmodal').modal('hide');
                        alert(json.message.replace(/\\n/g,"\n"));
                    }
                    else
                    {
                        $('.btn').button('reset');
                        $('#alertmodal').modal('hide');
                        alert(json.message.replace(/\\n/g,"\n"));
                        setTimeout(function() {
                        location.reload();
                        }, 300); 
                    }
                
                }//close success
                });//close ajax 
            });//close document yes click
        }
    });//close mouse click

    $(document).on('click', '#sync_btn', function(event){
        var supplier_guid = $(this).attr('supplier_guid');
        var shoot_link = 0;

        var modal = $("#medium-modal").modal();

        modal.find('.modal-title').html('Select Integration Settings');

        methodd = '';

        methodd +='<div class="col-md-12">';

        methodd += '<div class="form-group"><input type="hidden" id="data_supplier_guid" name="data_supplier_guid" value="'+supplier_guid+'"></div>';

        methodd += '<div class="form-group"><label>Integration Settings </label> <select class="form-control select2" name="select_integration" id="select_integration" > <option value=""> -SELECT DATA- </option><?php foreach($get_config_settings as $row) { ?> <option value="<?php echo $row->setting_guid?>"><?php echo addslashes($row->integration_name)?>  </option> <?php } ?></select> </div> ';
        
        methodd += '</div>';

        methodd_footer = '<p class="full-width"><span class="pull-right"><input type="button" id="confirm_btn" class="btn btn-success" value="Submit"> <input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

        modal.find('.modal-footer').html(methodd_footer);
        modal.find('.modal-body').html(methodd);

        setTimeout(function(){
            $('#select_supplier').select2();
        },300);

    });

    $(document).on('click','#confirm_btn',function(){

        var data_supplier_guid = $('#data_supplier_guid').val();
        var select_integration = $('#select_integration').val();
        
        if((data_supplier_guid == '') || (data_supplier_guid == null) || (data_supplier_guid == 'null'))
        {
            alert('Invalid Process.');
            return;
        }

        if((select_integration == '') || (select_integration == null) || (select_integration == 'null'))
        {
            alert('Invalid Process.');
            return;
        }

        confirmation_modal('Are you sure want to Sync Supplier?');
        $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
        $.ajax({
            url:"<?php echo site_url('Integrator_section/integrator_trigger') ?>",
            method:"POST",
            data:{data_supplier_guid:data_supplier_guid,select_integration:select_integration},
            beforeSend:function(){
                $('.btn').button('loading');
                swal.fire({
                    allowOutsideClick: false,
                    title: 'Processing...',
                    showCancelButton: false,
                    showConfirmButton: false,
                    onOpen: function () {
                    swal.showLoading()
                    }
                });
            },
            complete: function() {
                  setTimeout(function() {
                      Swal.close();
                  }, 300);
            },
            success:function(data)
            {
                json = JSON.parse(data);
                if(json.status == 'false')
                {
                    $('.btn').button('reset');
                    $('#alertmodal').modal('hide');
                    // alert(json.msg);
                    alert(json.message.replace(/\\n/g,"\n"));
                }
                else
                {
                    $('.btn').button('reset');
                    $('#alertmodal').modal('hide');
                    $("#medium-modal").modal('hide');
                    // alert(json.msg);
                    alert(json.message.replace(/\\n/g,"\n"));
                    setTimeout(function() {
                        location.reload();
                    }, 300); 
                }
            
            }//close success
            
        });//close ajax 
        });//close document yes click
    });//close redirect

    $(document).on('click', '#location_all', function(){
        // alert();
        $("#select_acc_code option").prop('selected',true);
        $(".select2").select2();
        die;
    });//CLOSE ONCLICK  

    $(document).on('click', '#location_all_dis', function(){
        // alert();
        $("#select_acc_code option").prop('selected',false);
        $(".select2").select2();
        die;
    });//CLOSE ONCLICK  

    /** INTEGRATION TABLE LOG HERE  */
    $(document).on('change','#select_int_code',function(){
        var integration_guid = $(this).val();

        // alert(integration_guid); die;
        supplier_int_table_log(integration_guid);
    });

    $('#supplier_int_table').DataTable({
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
                    "zeroRecords": "<?php echo '<b>No Record Found. Please Select Integration Retailer Code to view data.</b>'; ?>",
        },
        "pagingType": "simple_numbers",
    });
    $('.remove_padding_right').css({'text-align':'left'});
    $("div.remove_padding").css({"text-align":"left"});

    supplier_int_table_log = function(integration_guid) {
        if ($.fn.DataTable.isDataTable('#supplier_int_table')) {
        $('#supplier_int_table').DataTable().destroy();
        }

        var table;

        table = $('#supplier_int_table').DataTable({
        "scrollX": true,
        "processing": true,
        "serverSide": true,
        "lengthMenu": [ [10, 25, 50, 9999999], [10, 25, 50, 'All'] ],
        "sScrollY": "50vh",
        "sScrollX": "100%", 
        "sScrollXInner": "100%", 
        "bScrollCollapse": true,
        "order": [
            [4, "desc"]
        ],
        "columnDefs": [
            { "orderable": false, "targets": []},
        ],
        "ajax": {
            "url": "<?php echo site_url('Integrator_section/supplier_integration_tb') ?>",
            "type": "POST",
            "data": function(data) {
              data.integration_guid = integration_guid
            },
        },
        columns: [
            { "data": "supplier_name" },
            { "data": "reg_no" },
            { "data": "acc_code" },
            { "data": "naming_status" },
            { "data": "created_at" }
        ],
        //dom: 'lBfrtip',
        dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'Brtip',
        // buttons: [
        //   'copy', 'csv', 'excel', 'pdf', 'print'
        // ]
        "fnCreatedRow": function( nRow, aData, iDataIndex ) 
        {
            $(nRow).closest('tr').css({"cursor": "pointer"});
        }
        });
    }
});
</script>
