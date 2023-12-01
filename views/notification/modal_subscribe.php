<style>

</style>

<div class="content-wrapper" style="min-height: 525px;">
<div class="container-fluid">
<br>
    <div class="row">
        <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
            <h3 class="box-title">Notification Modal</h3><br>
            <div class="box-tools pull-right">

                <button id="create_btn_retailer" type="button" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus" aria-hidden="true" ></i> Create </button>
                
            </div>
            </div>
            <div class="box-body">
                
                <table class="table table-bordered table-striped dataTable" id="notification_table"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                <thead style="white-space: nowrap;word-break: break-word !important">
                    <tr>
                    <!-- <th>Action</th> -->
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Created By</th>
                    <th>Updated At</th>
                    <th>Updated By</th>
                    <!-- <th>Active</th> -->
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
        <div class="box box-default">
            <div class="box-header with-border">
            <h3 class="box-title">Notification Modal Subscribe</h3><br>
            <div class="box-tools pull-right">
                <span id='btn_append'> </span>
            </div>
            </div>
            <div class="box-body">
                
                <table class="table table-bordered table-striped dataTable" id="list_table_child"  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                <thead style="white-space: nowrap;word-break: break-word !important">
                    <tr>
                    <th>
                        <input type="checkbox" id="checkall_input_table" name="checkall_input_table" table_id="list_table_child">
                    </th> 
                    <!-- <th>Action</th> -->
                    <th>Retailer Name</th>
                    <th>Created At</th>
                    <th>Created By</th>
                    <th>Updated At</th>
                    <th>Updated By</th>
                    <!-- <th>Updated At</th>
                    <th>Updated By</th> -->
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
    // DataTable for child list
    $('#list_table_child').DataTable({
        // Disables sorting for all columns
        "columnDefs": [{"targets": '_all' ,"orderable": false}],
        'order': [],
        "sScrollY": "30vh", // Vertical scroll height
            "sScrollX": "100%", // Horizontal scroll length
            "sScrollXInner": "100%", // Inner scroll length
            "bScrollCollapse": true, // Enable table scrolling
        // DOM layout customization
        dom: "<'row'<'col-sm-2 remove_padding_right 'l > <'col-sm-10' f>  " + "<'col-sm-1' <'toolbar_list'>>>" +'rt<"row" <".col-md-4 remove_padding" i>  <".col-md-8" p> >',
        // Language settings for the DataTable
            "language": {
                    "lengthMenu": "Display _MENU_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "info":           "Show _START_ - _END_ of _TOTAL_ entry",
                    "zeroRecords": "<?php echo '<b>Please Select Notification Modal to view data.</b>'; ?>",
        },
        "pagingType": "simple_numbers",
    });

    // DataTable for main notification table
    $('#notification_table').DataTable({
        // Disable ordering for the first column
        "columnDefs": [
            // { "orderable": false, "targets": 0 },
        ],
        "serverSide": true, // Server-side processing mode
        'processing'  : true,
        'paging'      : true,
        'lengthChange': true,
        'lengthMenu'  : [ [10, 25, 50, 100, 9999999], [10, 25, 50, 100, 'ALL'] ],
        'searching'   : true,
        'ordering'    : true,
        'order'       : [ [1 , 'desc']],
        'info'        : true,
        'autoWidth'   : true,
        "bPaginate": true, 
        "bFilter": true, 
        "sScrollY": "50vh", 
        "sScrollX": "100%", 
        "sScrollXInner": "100%", 
        "bScrollCollapse": true,
        // Ajax configuration for data retrieval
        "ajax": {
            "url": "<?php echo site_url('Notification_modal/notification_list_tb');?>",
            "type": "POST",
        },
        // Columns configuration
        columns: [
            // Column data and custom rendering for the first column
            // { data: "seq", render: function(data, type, row){ 
            //     var element = '';
            //     // Creating buttons for edit and delete actions
            //     element += '<button id="edit_btn" style="margin-left:5px;" title="Edit" class="btn btn-xs btn-info" guid="'+row['seq']+'" notification_guid="'+row['notification_guid']+'" ><i class="fa fa-pencil"></i></button>';

            //     element += '<button id="delete_btn" style="margin-left:5px;" title="Edit" class="btn btn-xs btn-danger" guid="'+row['seq']+'" notification_guid="'+row['notification_guid']+'" ><i class="fa fa-trash"></i></button>';

            //     return element;
            // }},
            { data: "description"},
            { data: "created_at"},
            { data: "created_by"},
            { data: "updated_at"},
            { data: "updated_by"},
            // { data: "isactive", render: function(data, type, row){ 
            //     var element = '';

            //     if(data == '1')
            //     {
            //         element = 'Yes';
            //     }
            //     else
            //     {
            //         element = 'No';
            //     }

            //     return element;
            // }},
        ],
        // DOM layout customization for the main table
        dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'rtip',
        // Callback for row creation
        "fnCreatedRow": function( nRow, aData, iDataIndex ) {
            $(nRow).closest('tr').css({"cursor": "pointer"});
            $(nRow).attr('seq', aData['seq']);
            $(nRow).attr('notification_guid', aData['notification_guid']);
        },
        // Initialization complete callback
        "initComplete": function( settings, json ) {
        interval(); // Call some custom function 'interval'
        }
    });//close datatable

    // Event handling for edit button
    $(document).on('click','#edit_btn',function(){
        
        var notification_guid = $(this).attr('notification_guid');

        console.log(notification_guid);

        var modal = $("#medium-modal").modal();

        modal.find('.modal-title').html('Edit Notification Modal');

        methodd = '';

        methodd +='<div class="col-md-12">';

        methodd += '<div class="form-group"><label>Notification Description </label> <select class="form-control select2" name="edit_desc" id="edit_desc" > <option value=""> -SELECT DATA- </option><?php foreach($get_table as $row) { ?> <option value="<?php echo $row->notification_guid?>"><?php echo addslashes($row->description)?>  </option> <?php } ?></select> </div> ';
  
        methodd += '</div>';

        methodd_footer = '<p class="full-width"><span class="pull-right"><input type="button" id="edit_submit" class="btn btn-success" value="Update"> <input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

        modal.find('.modal-body').html(methodd);
        modal.find('.modal-footer').html(methodd_footer);
    
        setTimeout(function(){
            $('#edit_desc').val(notification_guid).trigger('change').select2();
        },300);
    });//CLOSE ONCLICK  

    // Event handling for update button
    $(document).on('click','#edit_submit',function(){

        confirmation_modal('Are you sure want to Update?');

        $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
            alert('Unsuccessful to update.');
            $('#confirmation_modal').modal('hide');
        });//close document yes click

    });//close 

    // Event handling for delete button
    $(document).on('click','#delete_btn',function(){

        var notification_guid = $(this).attr('notification_guid');

        confirmation_modal('Are you sure want to Remove Notification Modal?');
        $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
            alert('Unsuccessful to remove.');
            $('#alertmodal').modal('hide');
        });//close document yes click

    });//CLOSE ONCLICK  

    // Event handling for row click on the main table
    $(document).on('click', '#notification_table tbody tr', function(event){
        var xstatus = $('#notification_table').DataTable().rows().data().any();
        var notification_guid = $(this).attr('notification_guid');

        if((xstatus == false) || (xstatus != true)){
        return;
        }

        if(event.target.tagName == "I" || event.target.tagName == "BUTTON" || event.target.tagName == "INPUT") {
        return;
        }

        if((notification_guid == '') || (notification_guid == null) || (notification_guid == 'null'))
        {
            alert('Invalid Notification Modal');
            return;
        }

        child_table(notification_guid);
        $('#btn_append').html('<button id="create_child_btn" type="button" class="btn btn-xs btn-primary" v_main_guid="'+notification_guid+'" ><i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Create </button> <button id="delete_child_btn" type="button" class="btn btn-xs btn-danger" v_main_guid="'+notification_guid+'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete </button>');

        var id = $(this).closest('table').attr('id');

        var table = $('#'+id).DataTable();

        table.rows('.active').nodes().to$().removeClass("active");

        $(this).closest('table').find('tr').removeClass("active");
        $(this).addClass('active');

    });//close mouse click

    // Event handling for create child button
    $(document).on('click', '#create_child_btn', function(){

        var notification_guid = $(this).attr('v_main_guid');

        console.log(notification_guid);

        var modal = $("#medium-modal").modal();

        modal.find('.modal-title').html('Create Notification Modal Subscribe');

        methodd = '';

        methodd +='<div class="col-md-12">';

        methodd += '<div class="form-group"><label>Notification Description </label> <select class="form-control select2" name="notification_des" id="notification_des" disabled > <option value=""> -SELECT DATA- </option><?php foreach($get_table as $row) { ?> <option value="<?php echo $row->notification_guid?>"><?php echo addslashes($row->description)?>  </option> <?php } ?></select> </div> ';

        methodd += '<div class="form-group"><label>Retailer Name </label> <select class="form-control select2" name="add_retailer" id="add_retailer" > <option value=""> -SELECT RETAILER- </option><?php foreach($get_acc as $row) { ?> <option value="<?php echo $row->acc_guid?>"><?php echo addslashes($row->acc_name)?>  </option> <?php } ?></select> </div> ';
        
        methodd += '';
        
        methodd += '</div>';

        methodd_footer = '<p class="full-width"><span class="pull-right"><input type="button" id="create_child_submit" class="btn btn-success" value="Create"> <input name="sendsumbit" type="button" class="btn btn-default" data-dismiss="modal" value="Close"> </span></p>';

        modal.find('.modal-body').html(methodd);
        modal.find('.modal-footer').html(methodd_footer);

        setTimeout(function(){
            $('#notification_des').val(notification_guid).trigger('change').select2();
            $('#add_retailer').select2();
        },300);

    });//CLOSE ONCLICK 
    
    $(document).on('click','#create_child_submit',function(){

        var notification_des = $('#notification_des').val();
        var add_retailer = $('#add_retailer').val();

        if((add_retailer == '') || (add_retailer == null) || (add_retailer == 'null'))
        {
            alert('Please select Retailer.');
            return;
        }

        confirmation_modal('Are you sure want to Create?');
        $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
        $.ajax({
            url:"<?php echo site_url('Notification_modal/modal_subscribe_create') ?>",
            method:"POST",
            data:{notification_des:notification_des,add_retailer:add_retailer},
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
            success:function(data)
            {
                json = JSON.parse(data);
                if(json.para1 == 'false')
                {
                    $('.btn').button('reset');
                    $('#alertmodal').modal('hide');
                    Swal.fire({
                    title: json.msg, 
                    text: '', 
                    type: "error",
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    })
                }
                else
                {
                    $('.btn').button('reset');
                    $('#alertmodal').modal('hide');
                    $("#medium-modal").modal('hide');
                    Swal.fire({
                    title: json.msg, 
                    text: '', 
                    type: "success",
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    }).then(() => {
                        $('body').addClass('no-padding-right');
                        child_table(notification_des);
                    });
                }
            }//close success
        });//close ajax 
        });//close document yes click
    });//close 

    // Event handling for delete child button
    $(document).on('click', '#delete_child_btn', function(){
        var table = $('#list_table_child').DataTable();
        var main_guid = $(this).attr('v_main_guid');
        //alert(main_guid);die;
        var details = [];

        table.rows().nodes().to$().each(function(){
        if($(this).find('td').find('#flag_checkbox').is(':checked'))
        {
            d_guid = $(this).find('td').find('#flag_checkbox').attr('notification_subscribe_guid');

            //console.log(d_guid);die;
            
            if(d_guid == '' || d_guid == 'null' || d_guid == null)
            {
                alert('Invalid Process Error');
                return;
            }
    
            details.push({'d_guid':d_guid});
        }
        });//close small loop

        var count_selected = details.length;
        //alert(count_selected);die;

        if(details == '' || details == 'null' || details == null)
        {
            alert('At least one checkbox must be selected.');
            return;
        }

        confirmation_modal('Are you sure want to Remove Notification Modal Subscribe? <br> <b> Selected : '+count_selected+'</b>');
        $(document).off('click', '#confirmation_yes').on('click', '#confirmation_yes', function(){
        $.ajax({
            url:"<?php echo site_url('Notification_modal/remove_modal_subscribe') ?>",
            method:"POST",
            data:{details:details},
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
            success:function(data)
            {
                json = JSON.parse(data);
                if(json.para1 == 'false')
                {
                    $('.btn').button('reset');
                    $('#alertmodal').modal('hide');
                    Swal.fire({
                    title: json.msg, 
                    text: '', 
                    type: "error",
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    })
                }
                else
                {
                    $('.btn').button('reset');
                    $('#alertmodal').modal('hide');
                    $("#medium-modal").modal('hide');
                    Swal.fire({
                    title: json.msg, 
                    text: '', 
                    type: "success",
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    }).then(() => {
                        $('body').addClass('no-padding-right');
                        child_table(main_guid);
                    });
                }
            }//close success
            
        });//close ajax 
        });//close document yes click
    });//CLOSE ONCLICK  

    // Function to handle child table
    child_table = function(notification_guid)
    { 
        $.ajax({
            url : "<?php echo site_url('Notification_modal/notification_modal_sub');?>",
            method: "POST",
            data:{notification_guid:notification_guid},
            beforeSend : function() {
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
            success : function(data)
            {  
                json = JSON.parse(data);

                if ($.fn.DataTable.isDataTable('#list_table_child')) {
                    $('#list_table_child').DataTable().destroy();
                }

                $('#list_table_child').DataTable({
                    "columnDefs": [
                    {"targets": [0] ,"orderable": false},
                    ],
                    'processing'  : true,
                    'paging'      : true,
                    'lengthChange': true,
                    'lengthMenu'  : [ [10, 25, 50, 9999999], [10, 25, 50, 'All'] ],
                    'searching'   : true,
                    'ordering'    : true,
                    'order'       : [[2 , 'desc']],
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
                            { data: "notification_subscribe_guid", render: function(data, type, row){ 
                                var element = '';

                                element += '<input type="checkbox" id="flag_checkbox" class="form-checkbox" notification_subscribe_guid="'+row['notification_subscribe_guid']+'"/>';   

                                return element;
                            }},
                            { "data" : "acc_name" },
                            { "data" : "created_at" },
                            { "data" : "created_by" },
                            { "data" : "updated_at" },
                            { "data" : "updated_by" },
                            ],
                    dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>rtip",
                    "language": {
                        "lengthMenu": "Show _MENU_",
                        "infoEmpty": "No records available",
                        "infoFiltered": "(filtered from _MAX_ total records)",
                        "zeroRecords": "<span><?php echo '<b>No Record Found.</b>'; ?></span>",
                    }, 
                    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                    },
                    "initComplete": function( settings, json ) {
                        interval();
                    },
                });//close datatable
            }//close success
        });//close ajax
    }//close child table

    // Event handling for checkbox change
    $(document).on('change','#checkall_input_table',function(){

        var id = $(this).attr('table_id');

        var table = $('#'+id).DataTable();

        if($(this).is(':checked'))
        {
            table.rows().nodes().to$().each(function(){

                $(this).find('td').find('#flag_checkbox').prop('checked',true)

            });//close small loop
        }
        else
        {
            table.rows().nodes().to$().each(function(){

                $(this).find('td').find('#flag_checkbox').prop('checked',false)

            });//close small loop
        }//close else

    });//close checkbox

})
</script>