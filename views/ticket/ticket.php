<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<div class="container-fluid">
<br>
<script src="<?php echo base_url('asset/dist/js/Chart.js');?>"></script>

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

<?php if ( $_SESSION['user_group_name'] == 'SUPER_ADMIN') { ?>
  <div class="row">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo $all; ?></h3>
          <p>All Tickets</p>
        </div>
        <div class="icon">
          <i class="fa fa-ticket"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3><?php echo $Closed; ?></h3>

          <p>Solved</p>
        </div>
        <div class="icon">
          <i class="fa fa-check"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?php echo $In_Progress; ?></h3>

          <p>In-Progress</p>
        </div>
        <div class="icon">
          <i class="fa fa-clock-o"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-red">
        <div class="inner">
          <h3><?php echo $New; ?></h3>

          <p>New</p>
        </div>
        <div class="icon">
          <i class="fa fa-comment-o"></i>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

  <div class="row">
    <div class="col-md-12 col-xs-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Ticket Status</h3>
            
            <div class="dropdown" id="stress">
                  <select id="ticket_status_dropdown">
                        <option value="All">All</option>
                        <option value="New">New</option>
                        <option value="In-Progress">In-Progress</option>
                        <option value="Closed">Closed</option>
                   </select>
           </div>
          
          <div class="box-tools pull-right">
            <?php if ($_SESSION['user_group_name'] == 'SUPER_ADMIN') { ?>
            <button title="" id="search_message_details" type="button" class="btn btn-xs btn-primary"   
            ><i class="fa fa-search" aria-hidden="true"></i>Search
            </button>
            <?php } ?>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>

        <!-- /.box-header -->
          <div class="box-body">
          <div id="">
          
                  <table id="list" class="table table-bordered table-hover" width="100%" cellspacing="0" >
                    <thead>
                    <tr >
                        <th>Ticket Number</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Ticket Status</th>
                        <th>Closed at</th>
                        <th>Created at</th>
                        <th>Created by</th>
                        
                        <th>Code</th>
                        <th>Supplier Name</th>
                        <th>Retailer Name</th>
                        <th>Assigned</th>
                        <th>Seq</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                      
                    <tbody >
                     
                      <tr >
                        
                        <td>
                          
                        </td>
                      </tr>


   

                    </tbody>
         
                  </table>
             
              </div>  
        </div>

      </div>

    </div>
  </div>

<?php if ( $_SESSION['user_group_name'] == 'SUPER_ADMIN') { ?>
  <div class="row">
    <div class="col-md-6">
      <div class="box">
          <div class="box-header with-border">
              <h3 class="box-title">Ticket Category</h3>
              <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body" >
              <table id="ticket_table1" class="table table-hover" width="100%" cellspacing="0">
                  <thead>
                  <tr>
                      <th>Category</th>
                      <th>Count</th>
                  </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
          </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box">
          <div class="box-header with-border">
              <h3 class="box-title">Ticket Sub Category</h3>
              <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body" >
              <table id="ticket_table2" class="table table-hover" width="100%" cellspacing="0">
                  <thead>
                  <tr>
                      <th>Category</th>
                      <th>Sub Category</th>
                      <th>Count</th>
                  </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
          </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Ticket (Current Year) </h3>

            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div class="col-sm-12 graph_wrap">
              <canvas id="ticket_in_current_year" height="67.5"></canvas>        
            </div>

          </div>
          
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Ticket (Previous Year) </h3>

            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body">
            <div class="col-sm-12 graph_wrap">
              <canvas id="ticket_in_last_year" height="67.5"></canvas>        
            </div>

          </div>
          
      </div>
    </div>

  </div>
<?php } ?>

</div>
</div>

<div class="modal fade" id="search_modal" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Search Ticket Message <small id="search_count"></small></h4>
      </div>
      <div class="modal-body">
        <p>One fine body…</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>


<div class="modal fade" id="edit_ticket_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                <h3 class="modal-title">Ticket Edit</h3>
            </div>
            <div class="modal-body form">
                <form action="" method="" id="form" class="form-horizontal">
                      <div class="form-body">
                        <input type="hidden" name="ticket_guid" id="ticket_guid" value="">

                        <div class="form-group">
                        <label class="control-label col-md-3">Retailer Name<span style="color:red">*</span> </label>
                            <div class="col-md-9">
                                <select id="retainer_category" name="retainer_category" class="form-control">
                                <?php foreach($retailer_name->result() as $row) { ?>
                                    <option value=<?php echo $row->acc_guid; ?>><?php echo $row->acc_name ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                        <label class="control-label col-md-3">Supplier Name<span style="color:red">*</span> </label>
                            <div class="col-md-9">
                                <select id="supplier_category" name="supplier_category" class="form-control select2" style="width: 420px;">
                                <?php foreach($supplier_name->result() as $row) { ?>
                                    <option value=<?php echo $row->supplier_guid; ?>><?php echo $row->supplier_name ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                        <label class="control-label col-md-3">Category<span style="color:red">*</span> </label>
                            <div class="col-md-9">
                                <select id="ticket_category" name="ticket_category" class="form-control select2" style="width: 420px;">
                                <?php foreach($category->result() as $row) { ?>
                                    <option value=<?php echo $row->t_topic_guid; ?>><?php echo $row->name ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                        <label class="control-label col-md-3">Sub Category<span style="color:red">*</span> </label>
                            <div class="col-md-9">
                                <select id="ticket_sub_category" name="ticket_sub_category" class="form-control">
                                <?php foreach($sub_category->result() as $row) { ?>
                                    <option value=<?php echo $row->t_sub_topic_guid; ?>><?php echo $row->name ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>                        

                    </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" id="edit_ticket_save" class="btn btn-sm btn-primary">Save</button>
                      <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                  </div>
                </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  //for minimize and maximize box 

$(document).ready(function(){
  $(document).on('click','#edit_ticket',function(){
    var ticket_guid = $(this).attr('ticket_guid');
    var category_guid = $(this).attr('category_guid');
    var sub_category = $(this).attr('sub_category_guid');
    var supplier_name = $(this).attr('supplier_guid');
    var retailer_name = $(this).attr('retailer_guid');

    $('#ticket_guid').val(ticket_guid);
    $('#ticket_category').val(category_guid);
    $('#ticket_sub_category').val(sub_category);
    $('#retainer_category').val(retailer_name);
    // $('#supplier_category').val(supplier_name).trigger('change');
    $('#supplier_category').val(supplier_name);
    $('#supplier_category').select2();
    $('#ticket_category').select2();

    $('#ticket_category').change(function() {
      var selectedCategory = $(this).val();
      console.log('Selected Category:', selectedCategory);

      if (selectedCategory !== '') {
        $.ajax({
          url: "<?php echo site_url('Ticket/fetch_subtopic'); ?>",
          method: "POST",
          data: {
            type_val: selectedCategory
          },
          success: function (result) {
            var subtopicSelect = $('#ticket_sub_category');
            var json = JSON.parse(result);

            var subtopicOptions = '<option value="" disabled selected readonly>-Select-</option>';

            if (json.subtopic.length > 0) {
              $.each(json.subtopic, function (key, value) {
                subtopicOptions += '<option value="' + value.t_sub_topic_guid + '">' + value.name + '</option>';
              });
            } else {
              subtopicOptions += '<option value="" disabled>No sub-categories available</option>';
            }

            subtopicSelect.empty().html(subtopicOptions);     
          }
        });
      }

    });

    if (category_guid !== '') {
      $.ajax({
        url: "<?php echo site_url('Ticket/fetch_subtopic'); ?>",
        method: "POST",
        data: {
          type_val: category_guid
        },
        success: function (result) {
          var subtopicSelect = $('#ticket_sub_category');
          var json = JSON.parse(result);

          var subtopicOptions = '';

          if (json.subtopic.length > 0) {
            $.each(json.subtopic, function (key, value) {
              subtopicOptions += '<option value="' + value.t_sub_topic_guid + '">' + value.name + '</option>';
            });
          } else {
            subtopicOptions += '<option value="" disabled>No sub-categories available</option>';
          }

          subtopicSelect.empty().html(subtopicOptions);

          // Set the selected sub-category based on its actual value
          if (sub_category) {
            subtopicSelect.val(sub_category).trigger('change');
          }
          
        }
      });
    } else {
      // Clear the sub-category dropdown without adding the default "-Select-" option
      $('#ticket_sub_category').empty();
    }
  });

  $(document).on('click','#oldedit_ticket_save',function(){
    var ticket_guid = $('#ticket_guid').val();
    var category_guid = $('#ticket_category').val();
    var sub_category = $('#ticket_sub_category').val();
    var retailer_name = $('#retainer_category').val();
    var supplier_name = $('#supplier_category').val();
    // alert(ticket_guid+' - '+category_guid+' - '+sub_category);die;
    $.ajax({
          url:"<?php echo site_url('Ticket/edit_ticket'); ?>",
          method:"POST",
          data:{ticket_guid:ticket_guid,category_guid:category_guid,sub_category:sub_category,retailer_name:retailer_name,supplier_name:supplier_name},
          success:function(data){
            if(data > 0)
            {
              alert('Record Updated .');
              $('#edit_ticket_modal').modal('hide');
              location.reload();
            }
            else
            {
              alert('Record Not Update !');
            }
          }
    });

  });

  $(document).on('click', '#edit_ticket_save', function () {
    var subCategoryDropdown = $('#ticket_sub_category');
    var selectedSubCategory = subCategoryDropdown.val();
    
    if (selectedSubCategory == ''|| selectedSubCategory == null ) {
        alert('Please select a sub-category.');
        return; // Prevent form submission if no sub-category is selected
    }

    // If a sub-category is selected, proceed with saving the form
    var ticket_guid = $('#ticket_guid').val();
    var category_guid = $('#ticket_category').val();
    var sub_category = selectedSubCategory;
    var retailer_name = $('#retainer_category').val();
    var supplier_name = $('#supplier_category').val();

    $.ajax({
        url: "<?php echo site_url('Ticket/edit_ticket'); ?>",
        method: "POST",
        data: {
            ticket_guid: ticket_guid,
            category_guid: category_guid,
            sub_category: sub_category,
            retailer_name: retailer_name,
            supplier_name: supplier_name
        },
        success: function (data) {
            if (data > 0) {
                alert('Record Updated.');
                $('#edit_ticket_modal').modal('hide');
                $('#list').DataTable().ajax.reload();
            } else {
                alert('Record Not Updated!');
            }
        }
    });
  });

  tablelist = function(ticket_status_value='')
  { 

    if ( $.fn.DataTable.isDataTable('#list') ) {
      $('#list').DataTable().destroy();
    }
    // alert(ticket_status_value);
    var table_branch;

    table_branch = $('#list').DataTable({
      <?php if (in_array('OABYCUST',$this->session->userdata('module_code')) || in_array('OAT',$this->session->userdata('module_code'))) 
      { 
      ?>      
      "order": [[11,'desc'],[5 , 'desc']], 
      "columnDefs": [{ "visible": false, "targets": [7,11]  },{ "orderable": false, "targets": 12 }],
      <?php }
      else
      {
      ?>
      "order": [[11,'desc'],[5 , 'desc']], 
      "columnDefs": [{ "visible": false, "targets": [4,7,8,9,10,11,12]  }],         
      <?php          
      }
      ?>  
      "serverSide": true, 
      'processing'  : true,
      'paging'      : true,
      'lengthChange': true,
      'lengthMenu'  : [ [10, 25, 50, 100, 200, 9999999], [10, 25, 50, 100, 200, 'ALL'] ],
      'searching'   : true,
      'ordering'    : true,
      // 'order'       : [ [2 , 'desc'] ],
      'info'        : true,
      'autoWidth'   : false,
      "bPaginate": true, 
      "bFilter": true, 
      "sScrollY": "30vh", 
      "sScrollX": "100%", 
      "sScrollXInner": "100%", 
      "bScrollCollapse": true,
      "ajax": {
          "url" : "<?php echo site_url('Ticket/ticket_table'); ?>",
          "type": "POST",
          "data": {ticket_status_value:ticket_status_value},
          beforeSend:function(){
          },
          complete:function()
          { 
          },
      },
      //'fixedHeader' : false,
      columns: [
                { data: "ticket_number"},
                { data: "name"},
                { data: "sub_name"},
                { data: "ticket_status",render: function ( data, type, row ) {
                  if (data == 'New') { word = '<b style="color:red; ">'+data+'</b>' } else { word = data }
                  return word;
                }},
                { data: "closed_at"},
                { data: "created_at"},
                { data: "user_name"},
                
                { data: "supplier_group_name"},
                { data: "supplier_name"},
                { data: "acc_name"},
                { data: "assigned_name"},
                { data: "seq"},
                { data: "action"},
              ],
      dom: "<'row'<'col-sm-4'l>" + "<'col-sm-8'f>>" +'Brtip',
      buttons: [
        { extend: 'excelHtml5',
          exportOptions: {columns: [ 0,1,2,3,4,5,6,8,9,10]} /*, footer: true */},
                ],
      "pagingType": "simple_numbers",
      "fnCreatedRow": function( nRow, aData, iDataIndex ) {

        if(aData['messages_type'] == 'U')
        {   
            $(nRow).closest('tr').attr("id","highlight4");  
        }
          <?php if ( $_SESSION['user_group_name'] !== 'SUPER_ADMIN') { ?>
        if(aData['ticket_status'] == 'Closed')
        {   
            $(nRow).closest('tr').attr("id","highlight5");  
        }
        <?php } ?>

      // <?php if ( $_SESSION['user_group_name'] === 'SUPER_ADMIN') { ?>
      //  if(aData['ticket_status'] == 'Closed')
      // {   
      //     $(nRow).closest('tr').attr("id","highlight5");  
      // }
      // <?php } ?>

        $(nRow).attr('RefNo', aData['RefNo']);
      },
      "initComplete": function( settings, json ) {
        setTimeout(function(){
          // interval();
        },300);
        $('.btn').button('reset');
      }
    });//close datatable

    $('#list_filter').find('input').off('keyup.DT input.DT');
    $("div.remove_padding").css({"text-align":"left"});

    var searchDelay = null;
      
    $('#list_filter').find('input').on('keyup', function(e) {
        var search = $(this).val();
        if (e.keyCode == 13) {
            table_branch.search(search).draw();
            reset = 1;
        }//close keycode
    });//close keyup function

  }//close recreate_child_table

  tablelist();

  var searchDelay = null;
     
  $(document).on('keyup', '#search_message_input', function() {

    search_value = this.value;

    clearTimeout(searchDelay);
 
    searchDelay = setTimeout(function() {
        if (search_value != null) {
              
              $.ajax({
                        url:"<?php echo site_url('Ticket/search_message_result');?>",
                        method:"POST",
                        data:{search_value:search_value},

                        beforeSend : function() {
    
                        },
                        complete: function() {
      
                        },       
                        success:function(data)
                        { 

                            result = JSON.parse(data);
                            html = ''

                            for(i = 0; i < result['search_result'].length; i++)
                            {
                              

                              html += '<blockquote style="overflow: auto;"> <a title="'+result['search_result'][i].ticket_number+'" href="<?php echo site_url('Ticket/details?t_g=');?>'+result['search_result'][i].ticket_guid+'"><p>'+result['search_result'][i].ticket_number+'</p></a> <small>'+result['search_result'][i].messages+' - <cite title="Source Title">'+result['search_result'][i].created_at+'</cite></small> </blockquote> ';


                            }

                            
                            $('#search_message_result').html(html)


                            count_message = "Result: <b>"+result['search_count']+"</b>"

                            $('#search_count').html(count_message)


                        }//close succcess
                      });//close ajax


        }
    }, 1400);
  });//close delay

  $(document).on('change','#ticket_status_dropdown',function(){
    var ticket_status_value = $(this).val();

    tablelist(ticket_status_value);
  });

  <?php if ( $_SESSION['user_group_name'] == 'SUPER_ADMIN') { ?>
    $('#ticket_table1').DataTable({
      "columnDefs": [ ],
      "serverSide": true, 
      'processing'  : true,
      'paging'      : true,
      'lengthChange': true,
      'lengthMenu'  : [ [10, 25, 50, 999999], [10, 25, 50, 'ALL'] ],
      'searching'   : true,
      'ordering'    : true,
      'order'       : [ [1 , 'DESC'] ],
      'info'        : true,
      'autoWidth'   : false,
      "bPaginate": true, 
      "bFilter": true, 
      "sScrollY": "30vh", 
      "sScrollX": "100%", 
      "sScrollXInner": "100%", 
      "bScrollCollapse": true,
      "ajax": {
          "url": "<?php echo site_url('Ticket/category_tb');?>",
          "type": "POST",
      },
      columns: [

              { "data": "category" },
              { "data": "total_count" },

              ],
      dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>Brtip",
      // "pagingType": "simple",
      "fnCreatedRow": function( nRow, aData, iDataIndex ) {
        $(nRow).attr('guid', aData['guid']);
        
      },
      "initComplete": function( settings, json ) {
        interval();
      }
    });//close datatable

    $('#ticket_table2').DataTable({
      "columnDefs": [ ],
      "serverSide": true, 
      'processing'  : true,
      'paging'      : true,
      'lengthChange': true,
      'lengthMenu'  : [ [10, 25, 50, 999999], [10, 25, 50, 'ALL'] ],
      'searching'   : true,
      'ordering'    : true,
      'order'       : [ [2 , 'DESC'] ],
      'info'        : true,
      'autoWidth'   : false,
      "bPaginate": true, 
      "bFilter": true, 
      "sScrollY": "30vh", 
      "sScrollX": "100%", 
      "sScrollXInner": "100%", 
      "bScrollCollapse": true,
      "ajax": {
          "url": "<?php echo site_url('Ticket/sub_category_tb');?>",
          "type": "POST",
      },
      columns: [

              { "data": "category" },
              { "data": "sub_category" },
              { "data": "total_count" },

              ],
      dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>Brtip",
      // "pagingType": "simple",
      "fnCreatedRow": function( nRow, aData, iDataIndex ) {
        $(nRow).attr('guid', aData['guid']);
        
      },
      "initComplete": function( settings, json ) {
        interval();
      }
    });//close datatable
  <?php } ?>
})
</script>

<script type="text/javascript">
  
  $("#search_message_details").click(function(){

    modal = $('#search_modal').modal();

    html = ''

    html += '<input type="text" class="form-control" id="search_message_input" placeholder="Search Message"> ';

    html += '<span id="search_message_result"></span>';

    modal.find('.modal-body p').html(html)

    modal.find('#search_count').html('')
  });

</script>

<script type="text/javascript">

  <?php 

  $backgroundColor=array('rgba(255, 99, 132, 0.2)',
  'rgba(54, 162, 235, 0.2)',
  'rgba(255, 206, 86, 0.2)',
  'rgba(75, 192, 192, 0.2)',
  'rgba(153, 102, 255, 0.2)',
  'rgba(245, 123, 36, 0.2)',
  'rgba(0, 142, 37, 0.2)'); 

  //shuffle($backgroundColor);

  $borderColor=array('rgba(255,99,132,1)',
  'rgba(54, 162, 235, 1)',
  'rgba(255, 206, 86, 1)',
  'rgba(75, 192, 192, 1)',
  'rgba(153, 102, 255, 1)',
  'rgba(245, 123, 36, 1)',
  'rgba(0, 142, 37, 1)'); 

  //shuffle($borderColor);

  ?>

  data = {
    datasets: [{
        data: [

        <?php 

        foreach ($Topic as $key) {
          echo "'".$key->topic_count."',";
        }

         ?>

        ],

        backgroundColor:[<?php foreach ($backgroundColor as $key) {
          echo "'".$key."',";
        } ?>],

        borderColor:[<?php foreach ($borderColor as $key) {
          echo "'".$key."',";
        } ?>],
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
    <?php

    foreach ($Topic as $key) {
      echo "'".$key->name."',";
    }

    ?>
    ],
  };

</script>

<script type="text/javascript">
  // previous month
  var MONTHS = [

    <?php foreach ($last_12_month_ticket as $key) {
      echo " ' ". $key->period_month." ' , ";
    } ?>

  ];
  var config = {
    type: 'line',
    data: {
      labels: MONTHS,
      datasets: [{
        label: "Ticket Per Month",
        backgroundColor: 'rgba(255, 206, 86, 0.2)',
        borderColor: 'rgba(255, 206, 86, 1)',
        data: [
          <?php foreach ($last_12_month_ticket as $key) {
            echo $key->Count.',';
          } ?>
        ],
        fill: true,
      }]
    },
    options: {
      responsive: true,
      title: {
        display: true,
      },
      tooltips: {
        mode: 'index',
        intersect: false,
        callbacks: {
          label: function(tooltipItem, data) {
            var datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
            return datasetLabel + ': ' + value;
          }
        }
      },
      hover: {
        mode: 'nearest',
        intersect: true
      },
      scales: {
        xAxes: [{
          display: true,
          scaleLabel: {
            display: true,
            labelString: 'Month'
          }
        }],
        yAxes: [{
          display: true,
          scaleLabel: {
            display: true,
            labelString: 'Ticket'
          },
          ticks: {
          beginAtZero: true,
          userCallback: function(label, index, labels) {
              // when the floored value is the same as the value we have a whole number
              if (Math.floor(label) === label) {
                  return label;
              }
            }
          }
        }]
      }
    }
  };

  var ctx = document.getElementById("ticket_in_last_year").getContext("2d");
  window.myLine = new Chart(ctx, config);

  // current month
  var current_months = [
    <?php foreach ($current_month_ticket as $key) {
      echo " ' ". $key->period_month." ' , ";
    } ?>
  ];

  var current_config = {
    type: 'line',
    data: {
      labels: current_months,
      datasets: [{
        label: "Ticket Per Month",
        backgroundColor: 'rgba(255, 206, 86, 0.2)',
        borderColor: 'rgba(255, 206, 86, 1)',
        data: [
          <?php foreach ($current_month_ticket as $key) {
            echo $key->Count.',';
          } ?>
        ],
        fill: true,
      }]
    },
    options: {
      responsive: true,
      title: {
        display: true,
      },
      tooltips: {
        mode: 'index',
        intersect: false,
        callbacks: {
          label: function(tooltipItem, data) {
            var datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
            return datasetLabel + ': ' + value;
          }
        }
      },
      hover: {
        mode: 'nearest',
        intersect: true
      },
      scales: {
        xAxes: [{
          display: true,
          scaleLabel: {
            display: true,
            labelString: 'Month'
          }
        }],
        yAxes: [{
          display: true,
          scaleLabel: {
            display: true,
            labelString: 'Ticket'
          },
          ticks: {
          beginAtZero: true,
          userCallback: function(label, index, labels) {
              // when the floored value is the same as the value we have a whole number
              if (Math.floor(label) === label) {
                  return label;
              }
            }
          }
        }]
      }
    }
  };

  var current_ctx = document.getElementById("ticket_in_current_year").getContext("2d");
  window.myLine = new Chart(current_ctx, current_config);

</script>
