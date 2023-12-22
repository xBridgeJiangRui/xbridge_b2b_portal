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

.modal-lg
{
  width: 80%;
}

#table1 th {
  text-align: left;
}

#edit_table th {
  text-align: left;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
    
/* Disable the default styling */
input[type="number"] {
  -moz-appearance: textfield;
}

.dropzone {
    border: 2px dashed #0087F7 !important;
    border-radius: 5px;
    background: white;
    position: relative;
}

#upload_file {
    opacity: 0;
    position: absolute;
    z-index: -1;
}

input[type=file] {
    display: block;
}

.vertical-center {
    margin: 0;
    position: absolute;
    top: 50%;
    left: 50%;
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    text-align: center;
    cursor: pointer;
}

#loading-screen {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  z-index: 9999;
}

.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.blinker {
  animation: blink-animation 5s steps(10, start) infinite;
  -webkit-animation: blink-animation 1s steps(3, start) infinite;
  background-color: yellow;
  font-weight: bold;
  font-size:32px;
  color:red;
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

.my-swal-header {
  font-size: 12px;
}

.my-swal-text {
  font-size: 18px;
  color: #333;
}

</style>

<div class="content-wrapper" style="min-height: 525px; text-align: justify;">
<div class="container-fluid">
<br>
  <div class="col-md-12">

    <a class="btn btn-app" href="<?php echo site_url('Archived_document') ?>" style="color:grey" title="All">
      <i class="fa fa-list"></i> Requested Doc 
    </a>

  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <!-- head -->
        <div class="box-header with-border">
          <h3 class="box-title">Add Document</h3><br>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <!-- head -->
        <!-- body -->
        <div class="box-body">
          <div class="col-md-12">
            <div class="row">

              <div class="col-md-1"><b>Request No</b></div>
              <div class="col-md-4">
                <input type="text" id="req_refno" name="req_refno" value="<?php echo isset($_GET['guid']) ? $req_refno : '' ?>" readonly class="form-control pull-right" placeholder="NEW[]">
              </div>

              <div class="clearfix"></div><br>

            </div>           

          </div>

          <!-- <div class="col-md-12">
            <div class="row">

              <div class="col-md-1"><b>Requested By</b></div>
              <div class="col-md-4">
                <input type="text" id="requested_by" name="requested_by" value="<?php echo $username; ?>" readonly class="form-control pull-right">
              </div>

              <div class="clearfix"></div><br>

            </div>           

          </div> -->

          <div class="col-md-12">
            <div class="row">
              
              <div class="col-md-1"><b>Doc Ref No</b></div>
              <div class="col-md-4">
                <input type="text" id="refno" name="refno" class="form-control pull-right">
              </div>

              <div class="clearfix"></div><br>

            </div>      
            
            <div class="row">
              <div class="col-md-2">
                <a class="btn btn-success" id="add_document">Add</a>
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
          <h3 class="box-title">Document Lists<span class="add_branch_list"></span></h3>
          <div class="box-tools pull-right">
            <button id="export_document" class="btn btn-xs btn-default"><i class="fa fa-file-excel-o"></i> Export</button>
            <button id="import_document" class="btn btn-xs btn-default"> <i class="fa fa-file-excel-o"></i> Import File</button>
            <button id="submit_request" class="btn btn-xs btn-success hidden"> <i class="fa fa-check"></i> Submit</button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="height: 650px; overflow-y: auto;" >

          <table id="table1" class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead style="white-space: nowrap;"> <!--style="white-space: nowrap;"-->
            <tr>
              <th>Action</th>
              <th>Document Ref No</th>
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

<div id="modalImportExcel" class="modal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="close_modalImportExcel1" name="close_modalImportExcel1" class="close">×</button>
        <h4 class="modal-title">Import File</h4>
      </div>

      <div class="modal-body table-responsive modal-control-size">
        <div id="myDropZone" class="dropzone" style="height:100px;">
          <center><label class="vertical-center" id="output" for="upload_file">Select a file to continue</label></center> 
        </div>
        <div class="row" style="padding-top:10px;">
          <form id="excel_file_form" method="POST">
            <div class="col-md-6"><label for="upload_file" class="btn btn-block btn-primary">Select File</label></div>
            <div class="col-md-6"><button type="button" class="btn btn-block btn-danger" id="reset_input">Reset</button></div>
            <input type="file" name="upload_file[]" id="upload_file" accept=".xls,.xlsx,.csv">
          </form>
        </div>
      </div>

      <div class="modal-footer">
        <p class="full-width">
          <span class="pull-right">
            <input type="button" id="close_modalImportExcel2" name="close_modalImportExcel2" class="btn btn-default" value="Close"> 
          </span>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit_modal" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="edit_form" action="" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <span class="modal-title"></span>
        </div>
        <div class="modal-body" style="display: inline-block;"></div>
        <div class="modal-footer">
          <p class="full-width">
            <span class="buttons pull-right">
              <input type="submit" value="Edit" class="" name="submit">
            </span>
          </p>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="price_confirmation" class="modal" role="dialog" data-keyboard="false" data-backdrop ="static">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">          
          <h3 class="modal-title">Document Pricing</h3>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div><h4>The estimated pricing for the requested document/service is as follows:</h4></div>
            <div style="margin-top: 5px; margin-bottom: 10px;">
              <center><span class="blinker" id="estimated_price"></span></center>
            </div>
            <div><h5><b>*While the estimated price is as shown above, please note that the final pricing may vary slightly.</b></h5></div></br>
            <div><h4>Once the "Confirm" button is clicked, our team will review your request and proceed to send you a detailed quotation.</div></h4>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btn_close_modal" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
          <button type="button" id="confirm_request" class="btn btn-success"><i class="fa fa-check"></i> Confirm</button>
        </div>
      </div>
  </div>
</div>

<div id="loader_div" class="se-pre-con hidden"></div>

<div id="loading-screen" class="hidden">
  <div class="loader"></div>
  <p style="color: #fff; font-size: 24px;">Please wait while we check the document...</p>
</div>

<script>
$(document).ready(function() {

  var guid = "<?php echo isset($_GET['guid']) ? $_GET['guid'] : ''; ?>";
  var req_refno = $('#req_refno').val();

  if(guid != ""){
    // $('#req_refno').val(guid);
    $('#submit_request').removeClass('hidden');
  }

  $(document).on('click', '#export_document', function(event){

    var req_guid = '<?php echo $_GET["guid"] ?>';
   
    window.location.href = "<?php echo site_url('Archived_document/export_excel') ?>"+"?guid="+req_guid;

  });

  $(document).on('click', '#import_document', function(event){
    $('#modalImportExcel').fadeIn();
  }); 

  $(document).on('click', '#close_modalImportExcel1, #close_modalImportExcel2', function(event){
    $('#modalImportExcel').fadeOut();
  }); 

  $(document).on('change', '#upload_file', function(e) {

    var fileName = e.target.files[0].name;

    if (fileName != '') {
        $('#submit_file').remove();

        $('#excel_file_form').append('<div class="col-md-12" ><button type="button" id="submit_file" class="btn btn-block btn-success" style="margin-top:10px;">Submit</button></div>');

        $('#output').html(fileName);

    } else {
        $('#output').html('No files selected');
        $('#submit_file').remove();
    }

  });

  $(document).on('click', '#reset_input', function() {

    $('#upload_file').val('');

    var file = $('#upload_file')[0].files[0];

    if (file === undefined) {
        $('#output').html('No files selected');
        $('#submit_file').remove();
    } else {
        var fileName = file.name;

        $('#submit_file').remove();

        $('#excel_file_form').append('<button type="button" class="btn btn-block btn-success" id="submit_file" style="margin-top:10px;">Submit</button>');

        $('#output').html(fileName);
    }
  });

  $(document).on('click', '#submit_file', function() {

    var req_refno = $('#req_refno').val();

    var formData = new FormData();
    formData.append('file', $('#upload_file')[0].files[0]);
    formData.append('req_refno', req_refno);

    $.ajax({
      url:"<?php echo site_url('Archived_document/import_excel');?>",
      method:"POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend : function()
      { 
        $('.btn').button('loading');
      },
      complete : function()
      { 
        $('.btn').button('reset');
      },
      success:function(data)
      {

        json = JSON.parse(data);
        $('#modalImportExcel').fadeOut();

        if (json.status == true) {

          Swal.fire(json.message,'','success');

          $('#req_refno').val(json.req_refno);
          $('#submit_request').removeClass('hidden');
          datatable(json.req_refno);

        }else{

          Swal.fire(json.message,'','error');

          return;

        }

      }
    });
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
                "zeroRecords": "<?php echo '<b>No Record Found. Please Add the Document.</b>'; ?>",
      },
      "pagingType": "simple_numbers",
  });
  $('.remove_padding_right').css({'text-align':'left'});
  $("div.remove_padding").css({"text-align":"left"});

  $(document).on('click','#add_document',function(){

    var req_refno = $('#req_refno').val();
    var refno = $('#refno').val();

    if(refno == '' || refno == null)
    {

      Swal.fire('Doc Ref No Cannot be Empty','','error');

      return;
    } 

    $.ajax({
      url:"<?php echo site_url('Archived_document/add_document') ?>",
      method:"POST",
      data:{req_refno:req_refno, refno:refno},
      beforeSend : function() {
          $('.btn').button('loading');
      },
      complete: function() {
          $('.btn').button('reset');
      },
      success:function(data){
        json = JSON.parse(data);

        if (json.status == true) {
                  
          $('#submit_request').removeClass('hidden');

        }else{

          Swal.fire(json.message,'','error');

          return false;

        }

        $('#req_refno').val(json.req_refno);
        datatable(json.req_refno);

      }
    });
  });

  datatable = function(req_refno = '')
  { 
    $.ajax({
      url : "<?php echo site_url('Archived_document/request_document_listing');?>",
      method: "POST",
      data:{req_refno:req_refno},
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

        $('#table1').DataTable({
          "columnDefs": [
            // { className: "aligncenter", targets: [0] },
            // { className: "alignleft", targets: [1,2] },
            { className: "alignleft", targets: '_all' },
            // { width: '5%', targets: [0,4,5,6,8,9] },
            // { width: '7%', targets: [1,7] },
            // { width: '10%', targets: [2] },
          ],
          'processing'  : true,
          'paging'      : true,
          'lengthChange': true,
          'lengthMenu'  : [ [9999999999999999], ["ALL"] ],
          'searching'   : true,
          'ordering'    : true,
          // 'order'       : [ [1 , 'asc'] ],
          'info'        : true,
          'autoWidth'   : false,
          "bPaginate": true, 
          "bFilter": true, 
          // "sScrollY": "40vh", 
          // "sScrollX": "100%", 
          "sScrollXInner": "100%", 
          "bScrollCollapse": true,
          data: json,
          columns: [
            {"data" : "action", render:function( data, type, row ){
              var element = '';

              element += '<button id="edit_document" style="margin-left:5px;" title="EDIT" class="btn btn-sm btn-info" guid="'+row['guid']+'" request_refno="'+row['request_refno']+'" customer_guid="'+row['customer_guid']+'" doc_refno="'+row['doc_refno']+'" requested_at="'+row['requested_at']+'" requested_by="'+row['requested_by']+'"><i class="fa fa-edit"></i></button>';
              
              element += '<button id="delete_document" style="margin-left:5px;" title="DELETE" class="btn btn-sm btn-danger" guid="'+row['guid']+'" doc_refno="'+row['doc_refno']+'" request_refno="'+row['request_refno']+'"><i class="fa fa-trash"></i></button>';
          
              return element;

            }},
            {"data": "doc_refno"},
          ],
          dom: "<'row'<'col-sm-4'l><'col-sm-8'f>>rtip",  
          buttons: [
            {
              extend: 'csv'
            }
          ],
          "language": {
            "lengthMenu": "Show _MENU_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "zeroRecords": "<?php echo '<b>No Record Found. Please Add the Document.</b>'; ?>",
          },
          "fnCreatedRow": function( nRow, aData, iDataIndex ) {
            $(nRow).closest('tr').css({"cursor": "pointer"});
          },
          "initComplete": function( settings, json ) {
            interval();
          }
        });  
      }
    });
  }

  datatable(req_refno);

  $(document).on('click', '#edit_document', function(){

    var modal = $("#edit_modal").modal();
    var guid = $(this).attr('guid');

    modal.find('.modal-title').html('<h3>Edit Document</h3>');

    modal.find('#edit_form').attr("action","<?php echo site_url('Archived_document/edit_document')?>");

    methodd = '';

    $.ajax({
      url: "<?php echo site_url('Archived_document/document_details')?>",
      type: 'post',
      dataType: 'html',
      async: false,
      data: {guid: guid},
      success: function(data) {
        methodd += data;
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });

    modal.find('.modal-body').html(methodd);

  });

  $(document).on('click','#delete_document',function(){

    var guid = $(this).attr('guid');
    var req_refno = $(this).attr('request_refno');
    var doc_refno = $(this).attr('doc_refno');

    Swal.fire({
      title: 'Are you sure?',
      text: 'You are about to remove Document Ref No ('+doc_refno+')',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, proceed',
      cancelButtonText: 'No, cancel'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url:"<?php echo site_url('Archived_document/delete_document') ?>",
          method:"POST",
          data:{guid:guid},
          beforeSend : function() {
              $('.btn').button('loading');
          },
          complete: function() {
              $('.btn').button('reset');
          },
          success:function(data){
            json = JSON.parse(data);

            if (json.status == true) {
                      
              $('#submit_request').removeClass('hidden');
              datatable(req_refno);

            }else{

              Swal.fire(json.message,'','error');

              return false;

            }

          }
        });

      } else if (result.dismiss === Swal.DismissReason.cancel) {

      }
    });
  });

  $(document).on('click','#submit_request',function(){

    var req_refno = $('#req_refno').val();

    if(req_refno == '' || req_refno == null)
    {

      Swal.fire('Missing Request No','','error');

      return;
    }

    Swal.fire({
      title: 'Submit this request document?',
      // text: 'Our team will review this request document after submit!',
      text: 'Our team will review your request and proceed to send you a detailed quotation.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, confirm!',
      cancelButtonText: 'No, cancel',
      customClass: {
        header: 'my-swal-header',
        content: 'my-swal-text'
      }
    }).then((result) => {

      if (result.value) {

        $.ajax({
          url:"<?php echo site_url('Archived_document/confirm_request') ?>",
          method:"POST",
          data:{req_refno:req_refno},
          beforeSend:function(){
            $('.btn').button('loading');  
          },
          complete: function() {
              $('.btn').button('reset');
          },
          success:function(data)
          {

            json = JSON.parse(data);
            console.log(json);
            if (json.status == 1) {

              // var pricing = json.estimated_price;
              // var numericValue = parseFloat(pricing);
              // var estimated_price = numericValue.toFixed(2)
                
              // $('#estimated_price').text('RM '+formatNumberWithCommas(estimated_price));
              // $("#price_confirmation").modal("show");

              Swal.fire(json.message,'','success');

              setTimeout(function(){
                window.location.href = '<?php echo site_url("Archived_document"); ?>';
              },500);

            }else{

              Swal.fire(json.message,'','error');

              $('.btn').button('reset');

            }

          },
        });

      } else if (result.dismiss === Swal.DismissReason.cancel) {
        return;
      }
    });
  });

});
</script>

<script text="text/javascript">

  function formatNumberWithCommas(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

</script>
