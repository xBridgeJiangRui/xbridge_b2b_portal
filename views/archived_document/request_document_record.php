<style>
  .content-wrapper{
    min-height: 700px !important; 
  }

  .alignright {
    text-align: right;
  }

  .alignleft
  {
    text-align: left;
  }

  .aligncenter
  {
    text-align: center;
  }

  .no-select {
    -webkit-user-select: none; /* Safari */
    -moz-user-select: none; /* Firefox */
    -ms-user-select: none; /* Internet Explorer/Edge */
    user-select: none;
  }

  input[type="number"]::-webkit-inner-spin-button, input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
        
  input[type="number"] {
    -moz-appearance: textfield;
  }

  input[type="number"] {
    width: auto;
  }

  .my-swal-header {
    font-size: 12px;
  }

  .my-swal-text {
    font-size: 18px;
    color: #333;
  }

  .blinker {
    animation: blink-animation 5s steps(10, start) infinite;
    -webkit-animation: blink-animation 1s steps(3, start) infinite;
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

  .dataTables_wrapper .sorting,
  .dataTables_wrapper .sorting_asc,
  .dataTables_wrapper .sorting_desc {
    background-image: none !important;
    background-repeat: no-repeat !important;
    padding-right: 3px !important;
  }

  .sorting::after,
  .sorting_asc::after,
  .sorting_desc::after {
    display: none !important;
    content: none;
  }

  .dropzone {
    border: 2px dashed #0087F7 !important;
    border-radius: 5px;
    background: white;
    position: relative;
  }

  #upload_pdf {
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

  .loader-text:after {
    overflow: hidden;
    display: inline-block;
    vertical-align: bottom;
    -webkit-animation: ellipsis steps(4,end) 900ms infinite;      
    animation: ellipsis steps(4,end) 1500ms infinite;
    content: "\2026"; /* ascii code for the ellipsis character */
    width: 0px;
  }

  @keyframes ellipsis {
    to {
      width: 1.25em;    
    }
  }

  @-webkit-keyframes ellipsis {
    to {
      width: 1.25em;    
    }
  }

  #floatingButton {
    position: fixed;
    top: 100px; /* Adjust the top distance as needed */
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999; /* Ensure the button appears above other elements if necessary */
  }

  .spinner-border {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    vertical-align: text-bottom;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    -webkit-animation: spinner-border .75s linear infinite;
    animation: spinner-border .75s linear infinite;
  }

  @keyframes spinner-border {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

</style>

<div class="content-wrapper" style="min-height: 525px; text-align: justify;">
  <div class="container-fluid">
  <br>
    <?php if($live_date == null){ ?>
      <div class="alert alert-danger text-center" style="font-size: 18px">
        Live date is not being set. Kindly inform our support team about this issue.
        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-remove"></i></button><br>
      </div>
    <?php } ?>
    <div class="col-md-12">
      <a class="btn btn-app active" style="color:grey" id="btn_view_pending">
        <i class="fa fa-spinner"></i> Pending 
      </a>
      <a class="btn btn-app " style="color:grey" id="btn_view_complete">
        <i class="fa fa-check-square"></i> Submitted 
      </a>
      
      <a class="btn btn-app pull-right" id="request_btn_header" style="color:#000000" href="<?php echo site_url('Archived_document/request_document');?>">
        <i class="fa fa-plus-circle"></i> Request New
      </a>

      <!-- <a class="btn btn-app pull-right" id="btn_setup" style="color:#000000">
        <i class="fa fa-cog"></i> Setup
      </a> -->

      <?php if(isset($_GET['guid']) && $_SESSION['user_group_name'] == 'SUPER_ADMIN' && ($header_list[0]['status'] == 'SUBMITTED')){

        echo '<a class="btn btn-app pull-right" id="btn_submit_review" style="color:#000000">';
        echo    '<i class="fa fa-check"></i> Submit Review';
        echo '</a>';

        if($header_list[0]['missing_doc'] != 0 && $header_list[0]['ticket_created'] == null){

          echo '<a class="btn btn-app pull-right" id="btn_trigger_ticket" style="color:#000000">';
          echo    '<i class="fa fa-ticket"></i> Create Ticket';
          echo '</a>';

          echo '<a class="btn btn-app pull-right" id="btn_resync_document" style="color:#000000">';
          echo    '<i class="fa fa-refresh"></i> Re-check Doc';
          echo '</a>';
        }else{

          echo '<a class="btn btn-app pull-right hidden" id="btn_trigger_ticket" style="color:#000000">';
          echo    '<i class="fa fa-ticket"></i> Create Ticket';
          echo '</a>';

          echo '<a class="btn btn-app pull-right hidden" id="btn_resync_document" style="color:#000000">';
          echo    '<i class="fa fa-refresh"></i> Re-check Doc';
          echo '</a>';

        }
        
      } ?>

      <?php if(isset($_GET['guid']) && ($header_list[0]['status'] == 'REVIEWED')){

        echo '<a class="btn btn-app pull-right" id="btn_approve_request" style="color:green">';
        echo    '<i class="fa fa-check"></i> Approved';
        echo '</a>';

        echo '<a class="btn btn-app pull-right" id="btn_reject_request" style="color:red">';
        echo    '<i class="fa fa-times"></i> Rejected';
        echo '</a>';

      } ?>

      <?php if($header_list[0]['status'] == 'APPROVED'){
          echo '<a class="btn btn-app pull-right" id="btn_view_multiple" style="color:#000000">';
          echo    '<i class="fa fa-print"></i> Print';
          echo '</a>';
      } ?>

    </div>

    <div class="row class_pending_list">
      <div class="col-md-12 col-xs-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"> Pending Request Doc List <span class="pill_button"><?php echo $retailer; ?></span></h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body" >

            <table id="view_pending_list" class="table table-bordered table-hover" width="100%" cellspacing="0">
              <thead style="white-space: nowrap;">
              <tr>
                <th style="text-align: left;">No</th>
                <th style="text-align: left;">Request No</th>
                <th style="text-align: left;">Total Requested Doc</th>
                <th style="text-align: left;">Requested By</th>
                <th style="text-align: left;">Action</th> 
              </tr>
              </thead>
              <tbody>
                <?php $count = 1; ?>
                <?php foreach ($pending_list as $row){ ?>
                  <tr req_refno="<?php echo $row['request_refno'];?>">
                    <td><?php echo $count; ?></td>
                    <td><?php echo $row['request_refno'];?></td>
                    <td><?php echo $row['total_doc'];?></td>
                    <td><?php echo $row['requested_by'];?></br><?php echo $row['requested_at'];?></td>
                    <td>
                      <a class="btn btn-xs btn-primary" id="edit_btn" href="<?php echo site_url('Archived_document/request_document?guid=');?><?php echo $row['request_guid'];?>"><i class="fa fa-edit"></i> Edit</a>
                    </td>
                  </tr>
                  <?php $count++; ?>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row class_complete_list">
      <div class="col-md-12 col-xs-12">
        <div class="box" style="overflow-x: auto; white-space: nowrap;">
          <div class="box-header with-border">
            <h3 class="box-title"> Submitted Request Doc List <span class="pill_button"><?php echo $retailer; ?></span></h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body" >

            <table id="view_complete_list" class="table table-bordered table-hover" width="100%" cellspacing="0">
              <thead style="white-space: nowrap;">
              <tr>
                <th style="text-align: left;">No</th>
                <th style="text-align: left;">Request No</th>
                <th style="text-align: left;">Total Requested Doc</th>
                <!-- <th style="text-align: left;">Total Price</th> -->
                <th style="text-align: left;">Requested By</th>
                <th style="text-align: left;">Submitted By</th>
                <th style="text-align: left;">Reviewed By</th>
                <th style="text-align: left;">Approved / Rejected By</th>
                <th style="text-align: left;">Status</th>
                <th style="text-align: left;">Action</th> 
              </tr>
              </thead>
              <tbody>
                <?php $count = 1; ?>
                <?php foreach ($complete_list as $row){ ?>
                  <tr req_refno="<?php echo $row['request_refno'];?>">
                    <td><?php echo $count; ?></td>
                    <td><?php echo $row['request_refno'];?></td>
                    <td><?php echo $row['total_doc'];?></td>
                    <!-- <td><?php echo $_SESSION['user_group_name'] == 'SUPER_ADMIN' || $row['status'] == 'APPROVED' ? $row['total_price'] : '';?></td> -->
                    <td><?php echo $row['requested_by'];?></br><?php echo $row['requested_at'];?></td>
                    <td><?php echo $row['submitted_by'];?></br><?php echo $row['submitted_at'];?></td>
                    <td><?php echo $row['reviewed_by'];?></br><?php echo $row['reviewed_at'];?></td>
                    <td><?php echo ($row['status'] == 'REJECTED') ? $row['rejected_by'] : $row['approved_by'];?></br><?php echo ($row['status'] == 'REJECTED') ? $row['rejected_at'] : $row['approved_at'];?></td>
                    <td>
                      <?php if($row['status'] == 'APPROVED'){ ?>
                        <button type="button" class="btn btn-xs btn-success"> <?php echo ucfirst($row['status']) ?></button>
                      <?php }else if($row['status'] == 'REJECTED'){ ?>
                        <button type="button" class="btn btn-xs btn-danger"> <?php echo ucfirst($row['status']) ?></button>
                      <?php }else{ ?>
                        <button type="button" class="btn btn-xs btn-warning <?php echo ($_SESSION['user_group_name'] == 'SUPER_ADMIN' && $row['status'] == 'SUBMITTED') ? 'blinker' : ($_SESSION['user_group_name'] != 'SUPER_ADMIN' && $row['status'] == 'REVIEWED') ? 'blinker' : '' ?>"> <?php echo ucfirst($row['status']) ?></button>
                      <?php } ?>
                    </td>
                    <td>
                      <a class="btn btn-xs btn-primary" id="view_btn" href="<?php echo site_url('Archived_document?guid=');?><?php echo $row['request_guid'];?>"><i class="fa fa-eye"></i> View</a>
                    </td>
                  </tr>
                  <?php $count++; ?>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row class_complete_list_header hidden">
      <div class="col-md-12 col-xs-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"> Requested Document<span class="add_branch_list"></span></h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body" >

            <table id="view_complete_list_header" class="table table-bordered table-hover" width="100%" cellspacing="0">
              <thead style="white-space: nowrap;">
              <tr>
                <th style="text-align: left;">Request No</th>
                <th style="text-align: left;">Total Requested Doc</th>
                <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN' || $header_list[0]['status'] == 'REVIEWED' || $header_list[0]['status'] == 'APPROVED' || $header_list[0]['status'] == 'REJECTED'){ ?>
                  <th style="text-align: left;">Total Price</th>
                <?php } ?>
                <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN'){ ?>
                  <th style="text-align: left;">Total Unavailable Doc</th>
                  <th style="text-align: left;">Total Blocked Doc</th>
                  <th style="text-align: left;">Ticket Created</th>
                <?php } ?>
                <th style="text-align: left;">Requested Supplier</th>
                <th style="text-align: left;">Requested By</th>
                <th style="text-align: left;">Submitted By</th>
                <th style="text-align: left;">Reviewed By</th>
                <th style="text-align: left;">Approved / Rejected By</th>
                <th style="text-align: left;">Status</th>
              </tr>
              </thead>
              <tbody>
                <?php $count = 1; ?>
                <?php foreach ($header_list as $row){ ?>
                  <tr req_refno="<?php echo $row['request_refno'];?>">
                    <td><?php echo $row['request_refno'];?></td>
                    <td style="text-align: right;"><?php echo $row['total_doc'];?></td>
                    <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN' || $header_list[0]['status'] == 'REVIEWED' || $header_list[0]['status'] == 'APPROVED' || $header_list[0]['status'] == 'REJECTED'){ ?>
                      <td style="text-align: right;"><span id="sum_total_price"><?php echo $row['total_price'];?></span></td>
                    <?php } ?>
                    <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN'){ ?>
                      <td style="text-align: right;"><span id="total_missing_doc"><?php echo $row['missing_doc'];?></span></td>
                      <td style="text-align: right;"><?php echo $row['blocked_doc'];?></td>
                      <td style="text-align: left;"><span id="helpdesk_ticket"><?php echo $row['ticket_created'];?></span></td>
                    <?php } ?>
                    <td><?php echo $requested_supplier; ?></td>
                    <td><?php echo $row['requested_by'];?></br><?php echo $row['requested_at'];?></td>
                    <td><?php echo $row['submitted_by'];?></br><?php echo $row['submitted_at'];?></td>
                    <td><?php echo $row['reviewed_by'];?></br><?php echo $row['reviewed_at'];?></td>
                    <td><?php echo ($row['status'] == 'REJECTED') ? $row['rejected_by'] : $row['approved_by'];?></br><?php echo ($row['status'] == 'REJECTED') ? $row['rejected_at'] : $row['approved_at'];?></td>
                    <td>
                      <?php if($row['status'] == 'APPROVED'){ ?>
                        <button type="button" class="btn btn-xs btn-success"> <?php echo ucfirst($row['status']) ?></button>
                      <?php }else if($row['status'] == 'REJECTED'){ ?>
                        <button type="button" class="btn btn-xs btn-danger"> <?php echo ucfirst($row['status']) ?></button>
                      <?php }else{ ?>
                        <button type="button" class="btn btn-xs btn-warning"> <?php echo ucfirst($row['status']) ?></button>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php $count++; ?>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row class_complete_list_child hidden">
      <div class="col-md-12 col-xs-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"> Document Details<span class="add_branch_list"></span></h3>
          </div>
          <div class="box-body" >

            <table id="view_complete_list_child" class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead style="white-space: nowrap;">
                <tr>
                  <th style="text-align: left;">No</th>
                  <th style="text-align: left;">Document Ref No</th>
                  <th style="text-align: left;">Document Date</th>
                  <th style="text-align: left;">Supplier Code</th>
                  <th style="text-align: left;">Supplier Name</th>
                  <!-- <th style="text-align: left;">Amount</th> -->
                  <th style="text-align: left;">Document Type</th>
                  <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN'){ ?>
                    <th style="text-align: left;">Pricing Type</th>
                  <?php } ?>
                  <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN' || $header_list[0]['status'] == 'APPROVED'){ ?>
                    <th style="text-align: left;">Action <input type="checkbox" class="checkbox_input" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></th>
                  <?php } ?>
                </tr>
                </thead>
                <tbody>
                  <?php $count = 1; ?>
                  <?php foreach ($child_list as $row){ ?>

                    <?php if(!in_array($row['SCode'], $supplier) && $row['SCode'] != null && $_SESSION['user_group_name'] != 'SUPER_ADMIN'){ ?>
                      <tr guid="<?php echo $row['guid'];?>">
                        <td><?php echo $count; ?></td>
                        <td><?php echo $row['doc_refno'];?></td>
                        <td><?php echo $row['doc_date'];?></td>
                        <td><span style="color :red;">(BLOCKED)</span></td>
                        <td><span style="color :red;">(BLOCKED)</span></td>
                        <!-- <td style="text-align: right;">0.00</td> -->
                        <td><span style="color :red;">(BLOCKED)</span></td>
                        <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN' || $header_list[0]['status'] == 'APPROVED'){ ?>
                          <td><a class="btn btn-xs btn-danger" style="pointer-events: none; cursor: default;"><i class="fa fa-times"></i> Blocked</a></td>
                        <?php } ?>
                      </tr>
                    <?php }else{ ?>
                      <tr guid="<?php echo $row['guid'];?>">
                        <td><?php echo $count; ?></td>
                        <td><?php echo $row['doc_refno'];?></td>
                        <td><?php echo ($row['doc_date'] == '' || $row['doc_date'] == null) ? '<span style="color :red;">(NOT AVAILABLE)</span>' : $row['doc_date'];?></td>
                        <td><?php echo ($row['SCode'] == '' || $row['SCode'] == null || $live_date == null || $row['doc_date'] < $live_date) ? '<span style="color :red;">(NOT AVAILABLE)</span>' : $row['SCode'];?></td>
                        <td><?php echo ($row['SName'] == '' || $row['SName'] == null || $live_date == null || $row['doc_date'] < $live_date) ? '<span style="color :red;">(NOT AVAILABLE)</span>' : $row['SName'];?></td>
                        <!-- <td style="text-align: right;"><?php echo number_format($row['total'], 2, '.', ',');?></td> -->
                        <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN' && $header_list[0]['status'] == 'SUBMITTED' && ($row['pricing_type'] == '' || $row['doc_type'] == '' || ($row['json_report'] == null && $row['file_path'] == null)) && $live_date != null || $row['doc_date'] > $live_date){ ?>
                          <td>
                            <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN'){ ?>
                              <select id="doc_type" name="doc_type" class="form-control doc_type_option">
                              <option value="-">NOT FOUND</option>
                              <?php foreach($doc_type as $type){ ?>
                                <option value="<?php echo $type ?>" <?php echo (isset($_GET['guid']) && $row['doc_type'] == $type) ? 'selected' : '' ; ?>> 
                                  <?php echo $type; ?>
                                </option>
                              <?php } ?>
                              </select>
                            <?php }else{ ?>
                              <?php echo $row['doc_type']; ?>
                            <?php } ?>
                          </td>
                        <?php }else{ ?>
                          <td><span class="doc_type_option"><span style="color :red;">(NOT AVAILABLE)</span></span></td>
                        <?php } ?>
                        <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN'){ ?>
                          <td>

                            <?php if(!in_array($row['SCode'], $requested_supplier_array) && $row['SCode'] != null && $row['pricing_type'] == ''){ ?>
                              <select id="pricing_type" name="pricing_type" class="form-control pricing_type_option" <?php echo (isset($_GET['guid']) && $row['status'] != 'SUBMITTED') || $row['file_path'] != null ? 'disabled' : '' ; ?>>
                              <option value="">NOT FOUND</option>
                              <option value="" selected>BLOCKED</option>
                              <?php foreach($pricing_type as $type){ ?>
                                <option value="<?php echo $type['doc_type'] ?>"> 
                                  <?php echo $type['doc_name']; ?>
                                </option>
                              <?php } ?>
                              </select>
                            <?php }else{ ?>
                              <select id="pricing_type" name="pricing_type" class="form-control pricing_type_option" <?php echo (isset($_GET['guid']) && $row['status'] != 'SUBMITTED') || $row['file_path'] != null ? 'disabled' : '' ; ?>>
                              <option value="">NOT FOUND</option>
                              <option value="">BLOCKED</option>
                              <?php foreach($pricing_type as $type){ ?>
                                <option value="<?php echo $type['doc_type'] ?>" <?php echo (isset($_GET['guid']) && $row['pricing_type'] == $type['doc_type']) ? 'selected' : '' ; ?>> 
                                  <?php echo $type['doc_name']; ?>
                                </option>
                              <?php } ?>
                              </select>
                            <?php } ?>
                          </td>
                        <?php } ?>
                        <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN' || $header_list[0]['status'] == 'APPROVED'){ ?>
                          <?php if($row['doc_type'] == '' || ($row['json_report'] == null && $row['file_path'] == null)){ ?>
                            <?php if($_SESSION['user_group_name'] == 'SUPER_ADMIN' || $header_list[0]['status'] == 'SUBMITTED'){ ?>
                              <td><a class="btn btn-xs btn-danger" id="btn_upload_pdf" guid="<?php echo $row['guid'];?>"><i class="fa fa-upload"></i> Upload</a></td>
                            <?php }else{ ?>
                              <td><a class="btn btn-xs btn-danger" style="pointer-events: none; cursor: default;"><i class="fa fa-times"></i> Not Available</a></td>
                            <?php } ?>
                          <?php }else{ ?>
                            <td><a class="btn btn-sm btn-warning" id="btn_view_report" guid="<?php echo $row['guid'];?>" doc_refno="<?php echo $row['doc_refno'];?>" doc_type="<?php echo $row['doc_type'];?>" file_path="<?php echo $row['file_path'];?>" customer_guid="<?php echo $row['customer_guid'];?>"><i class="fa fa-file"></i> View <input type="checkbox" class="checkbox_guid checkbox_input" name="selected[]" value="<?php echo $row['doc_refno']; ?>" style="vertical-align: text-bottom;" onclick="event.stopPropagation();" /></a></td>
                          <?php } ?>
                        <?php } ?>
                      </tr>
                    <?php } ?>
                  <?php $count++; ?>
                  <?php } ?>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="modal" id="setup-configuration-modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                  <b>Configuration Setup</b>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </h3>
                
            </div>
            <div class="modal-body">
              <form id="setup_form">
                <div class="col-md-12">
                  <div class="row">

                    <div class="row">
                      <div class="col-md-12" style="min-height: 4vh">
                        <h4>
                          <b>Email Setup:
                            <span style="cursor: help;" data-toggle="tooltip" title="Email address to receive notification whenever supplier request archived document."><i class="fa fa-info-circle"></i></span>
                            <span style="margin-right: 10vw;"></span><a class="btn btn-xs btn-info" id="addEmail"><i class="fa fa-plus"></i> Add</a>
                          </b>
                        </h4>
                      </div>

                      <div class="input-group">
                        <div class="col-md-10">
                            <input type="email" class="form-control email-input" name="email[]" style="width: 20vw;" required> 
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger removeEmail hidden"><i class="fa fa-minus"></i></button>
                        </div>
                      </div>

                      <div id="additionalEmails"></div>

                    </div>

                  </div>
                </div>  
              </form>
            </div>
            <div class="modal-footer">
              <button type="submit" id="submit_setup_form" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<div id="modalViewReport" class="modal" role="dialog" data-keyboard="false" data-backdrop ="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" id="close_modalViewReport1" name="close_modalViewReport1" class="close">×</button>    
          <h3 class="modal-title">Document</h3>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="col-md-12">
              <div class="col-md-12"  style="max-height: 70vh;overflow-x:auto;overflow-y:auto"> 
                <div id="accconceptCheck">
                  <div id="embed_loader" class="loader"></div>
                  <embed id="embed_report" height="1000px" width="100%"></embed>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <p class="full-width">
            <span class="pull-right">
              <input type="button" id="close_modalViewReport2" name="close_modalViewReport2" class="btn btn-default" value="Close"> 
            </span>
          </p>
        </div>
      </div>
  </div>
</div>

<div id="modalUploadPDF" class="modal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="close_modalUploadPDF1" name="close_modalUploadPDF1" class="close">×</button>
        <h4 class="modal-title">Import File</h4>
      </div>

      <div class="modal-body table-responsive modal-control-size">
        <div id="myDropZone" class="dropzone" style="height:100px;">
          <center><label class="vertical-center" id="output" for="upload_pdf">Select a PDF file to continue</label></center> 
        </div>
        <center>
          <small style="color:grey;font-weight:bold;">File Naming Format: refno_supcode_yyyymmdd(2021-01-01)_amount(88.88)</small></br>
          <small style="color:red;font-weight:bold;font-style:italic;">Example File Name: ABCPO21110123_A123_2021-12-31_200.00</small>
        </center>
        <div class="row" style="padding-top:10px;">
          <form id="excel_file_form" method="POST">
            <div class="col-md-6"><label for="upload_pdf" class="btn btn-block btn-primary">Select File</label></div>
            <div class="col-md-6"><button type="button" class="btn btn-block btn-danger" id="reset_input">Reset</button></div>
            <input type="hidden" name="doc_guid" id="doc_guid">
            <input type="file" name="upload_pdf[]" id="upload_pdf" accept=".pdf">
          </form>
        </div>
      </div>

      <div class="modal-footer">
        <p class="full-width">
          <span class="pull-right">
            <input type="button" id="close_modalUploadPDF2" name="close_modalUploadPDF2" class="btn btn-default" value="Close"> 
          </span>
        </p>
      </div>
    </div>
  </div>
</div>

<div id="loading-screen" class="hidden">
  <div class="loader"></div>
  <!-- <p style="color: #fff; font-size: 24px;" class="loader-text">Please wait while we resync the document</p> -->
  <p style="color: #fff; font-size: 24px;" class="loader-text"><span id="loader_message"></span></p>
  <progress id="progress-bar-meter" max="90" class="hidden"> <span id="progress-bar-value"></span>% </progress>
</div>

<button id="floatingButton" class="btn btn-xs btn-success hidden"><i class="fa fa-check-circle"></i> Saved</button>

<form target="_blank" action="<?php echo site_url('Archived_document/view_multiple_report') ?>" id="view_multiple_report" method="post"></form>

<script type="text/javascript">

  $(document).ready(function () {
    var doc_status = "<?php echo $header_list[0]['status']; ?>";

    if(doc_status != 'APPROVED'){
      $('.checkbox_input').addClass('hidden');
    }else{
      $('.checkbox_input').removeClass('hidden');
    }
  });

</script>

<script type="text/javascript">

  $(document).ready(function () {
    const modal = $('#setup-configuration-modal');
    const btn = $('#btn_setup');
    const closeBtn = $('.close');

    btn.click(function () {
        modal.css('display', 'block');
    });

    closeBtn.click(function () {
        modal.css('display', 'none');
    });

    $(window).click(function (event) {
        if (event.target === modal[0]) {
            modal.css('display', 'none');
        }
    });

    $('#setup_form').on('click', '#addEmail', function () {

      $('.removeEmail').removeClass('hidden');

      appendDiv = '</br>';
      appendDiv += '<div class="input-group">';
      appendDiv += '  <div class="col-md-10">';
      appendDiv += '    <input type="email" class="form-control email-input" name="email[]" style="width: 20vw;" required>';
      appendDiv += '  </div>';
      appendDiv += '  <div class="col-md-1">';
      appendDiv += '    <button type="button" class="btn btn-danger removeEmail"><i class="fa fa-minus"></i></button>';
      appendDiv += '  </div>';
      appendDiv += '</div>';

      const newEmailInput = $(appendDiv);
      $('#additionalEmails').append(newEmailInput);
    });

    $('#setup_form').on('click', '.removeEmail', function () {
      $(this).closest('.input-group').remove();
    });

    $('#setup_form').on('click', '#submit_setup_form', function () {
      alert(21321323);
      $('#setup_form').submit();
    });

    $('#setup_form').submit(function (event) {

        event.preventDefault();

        const emailArray = [];
        $('.email-input').each(function () {
            const emailValue = $(this).val();
            if (emailValue.trim() !== '') {
                emailArray.push(emailValue);
            }
        });

        // Now you can send the emailArray to your server to save it in the database
        console.log(emailArray);
    });

  });

</script>

<script type="text/javascript">

  $(document).ready(function() {

    var check_id = "<?php echo isset($_GET['guid']) ? 1 : 0 ; ?>";

    $('#view_pending_list thead tr').clone(true).addClass('filters').appendTo('#view_pending_list thead');

    var table = $('#view_pending_list').DataTable({
        columnDefs: [
          { className: "aligncenter", targets: [0,4] },
          { className: "alignright", targets: [2] },
          { className: "alignleft", targets: '_all' },
          { width: '12%', targets: [2] },
          // { width: '12%', targets: [1,4,5] },
        ],
        filter      : true,
        pageLength  : 10,
        processing  : true,
        paging      : true,
        lengthChange: true,
        lengthMenu  : [ [10, 25, 50, 99999999], ['10', '25', '50', 'ALL'] ],
        searching   : true,
        ordering    : true,
        info        : true,
        autoWidth   : false,
        bPaginate: true, 
        bFilter: true, 
        // sScrollY: "80vh", 
        // sScrollX: "100%", 
        sScrollXInner: "100%", 
        bScrollCollapse: true,
        orderCellsTop: true,
        fixedHeader: true,
        initComplete: function () {
          var api = this.api();
          api.columns().eq(0).each(function (colIdx) {

            var cell = $('.filters th').eq(
                $(api.column(colIdx).header()).index()
              );

            var title = $(cell).text();
            var dataTable = $('#view_pending_list').DataTable();
            var columnCells = dataTable.column(colIdx).nodes();
            var columnSize = $(columnCells[0]).width();

            if (colIdx == 0 || colIdx == 4) { 
              $(cell).html('');
            }else{
              $(cell).html('<input type="text" class="form-control" style="width:'+ columnSize +'px;" placeholder="' + title + '" />');
            }

             // On every keypress in this input
             $('input',$('.filters th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('change', function (e) {
                              
              // Get the search value
              $(this).attr('title', $(this).val());
              var regexr = '({search})';
              var cursorPosition = this.selectionStart;
              
              // Search the column for that value
              api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();
            }).on('keyup', function (e) {
              
              e.stopPropagation();
              $(this).trigger('change');
              $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
            });
          });
        },
    });

    if(check_id == 1){
      $('#btn_view_pending').removeClass('active');
      $('.class_complete_list_header').removeClass('hidden');
      $('.class_complete_list_child').removeClass('hidden');
      $('.class_pending_list').addClass('hidden');
    }

    $('#view_complete_list thead tr').clone(true).addClass('filters-complete').appendTo('#view_complete_list thead');

    var table = $('#view_complete_list').DataTable({
        columnDefs: [
          // { className: "aligncenter", targets: [0,-1,-2] },
          { className: "alignright", targets: [2] },
          { className: "alignleft", targets: '_all' },
          { width: '2%', targets: [1, -1] },
          { width: '1%', targets: '_all' },
          // { width: '7%', targets: 3 },
        ],
        filter      : true,
        pageLength  : 10,
        processing  : true,
        paging      : true,
        lengthChange: true,
        lengthMenu  : [ [10, 25, 50, 99999999], ['10', '25', '50', 'ALL'] ],
        searching   : true,
        ordering    : true,
        info        : true,
        autoWidth   : false,
        bPaginate: true, 
        bFilter: true, 
        // sScrollY: "80vh", 
        // sScrollX: "100%", 
        sScrollXInner: "100%", 
        bScrollCollapse: true,
        orderCellsTop: true,
        fixedHeader: true,
        // scrollX: true,
        initComplete: function () {
          var api = this.api();
          api.columns().eq(0).each(function (colIdx) {

            var cell = $('.filters-complete th').eq(
                $(api.column(colIdx).header()).index()
              );

            var title = $(cell).text();
            var dataTable = $('#view_complete_list').DataTable();
            var columnCells = dataTable.column(colIdx).nodes();
            var columnSize = $(columnCells[0]).width();

            if (colIdx == 0 || colIdx == 8) { 
              $(cell).html('');
            }else if(colIdx == 7){
              $(cell).html('<select id="filter_status_option" class="form-control"><option value=""> --Choose Value-- </option><option value="SUBMITTED">Submitted</option><option value="REVIEWED">Reviewed</option><option value="APPROVED">Approved</option><option value="REJECTED">Rejected</option></select>');
            }else{
              $(cell).html('<input type="text" class="form-control" style="width:'+ 120 +'px;" placeholder="' + title + '" />');
            }

            // On every keypress in this input
            $('input',$('.filters-complete th').eq($(api.column(colIdx).header()).index())).off('keyup change').on('change', function (e) {
                              
              // Get the search value
              $(this).attr('title', $(this).val());
              var regexr = '({search})';
              var cursorPosition = this.selectionStart;
              
              // Search the column for that value
              api.column(colIdx).search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '').draw();
            }).on('keyup', function (e) {

              e.stopPropagation();
              $(this).trigger('change');
              $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);

            });

            $('#filter_status_option').on('change', function() {
              var selectedValue = $(this).val();
              var regexr = '({search})';
              
              // Search the column for that value
              api.column(7).search(this.value != '' ? regexr.replace('{search}', '(((' + selectedValue + ')))') : '', selectedValue != '', selectedValue == '').draw();
            });

          });
        },
    });

    $('#view_complete_list_header').DataTable(
      {
        "columnDefs": [
          // { className: "aligncenter", targets: [-1] },
          // { className: "alignright", targets: [1,2,3] },
          { className: "alignleft", targets: '_all' },
        ],
        'filter'      : true,
        'pageLength'  : 10,
        'processing'  : true,
        'paging'      : false,
        'lengthChange': true,
        'lengthMenu'  : [ [10, 25, 50, 99999999], ['10', '25', '50', 'ALL'] ],
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false,
        "bPaginate": true, 
        "bFilter": true, 
        "sScrollY": "80vh", 
        "sScrollX": "100%", 
        "sScrollXInner": "100%", 
        "bScrollCollapse": true,
      }
    );

    $('#view_complete_list_child').DataTable(
      {
        "columnDefs": [
          {targets: -1, orderable: false },
          // { className: "aligncenter", targets: [] },
          // { className: "alignleft", targets: [0,1,2,3] },
          { className: "alignleft", targets: '_all' },
          // { width: '19%', targets: [3] },
        ],
        'filter'      : true,
        'pageLength'  : 99999999,
        'processing'  : true,
        'paging'      : true,
        'lengthChange': true,
        'lengthMenu'  : [ [10, 25, 50, 99999999], ['10', '25', '50', 'ALL'] ],
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        "bPaginate": true, 
        "bFilter": true, 
        // "sScrollY": "80vh", 
        // "sScrollX": "100%", 
        "sScrollXInner": "100%", 
        "bScrollCollapse": true,
      }
    );

    $('.class_complete_list').addClass('hidden');

    $(document).on('click','#btn_view_pending',function(){

      $('#btn_view_pending').addClass('active');
      $('#btn_view_complete').removeClass('active');

      $('.class_complete_list').addClass('hidden');
      $('.class_pending_list').removeClass('hidden');

      $('#btn_view_multiple').addClass('hidden');
      $('#btn_submit_review').addClass('hidden');
      $('#btn_approve_request').addClass('hidden');
      $('#btn_reject_request').addClass('hidden');
      $('#btn_trigger_ticket').addClass('hidden');
      $('#btn_resync_document').addClass('hidden');
      $('.class_complete_list_header').addClass('hidden');
      $('.class_complete_list_child').addClass('hidden');

      var url = window.location.href;
      var updatedUrl = url.split('?')[0];
      window.history.replaceState(null, null, updatedUrl);

    });

    $(document).on('click','#btn_view_complete',function(){

      $('#btn_view_complete').addClass('active');
      $('#btn_view_pending').removeClass('active');

      $('.class_pending_list').addClass('hidden');
      $('.class_complete_list').removeClass('hidden');

      $('#btn_view_multiple').addClass('hidden');
      $('#btn_submit_review').addClass('hidden');
      $('#btn_approve_request').addClass('hidden');
      $('#btn_reject_request').addClass('hidden');
      $('#btn_trigger_ticket').addClass('hidden');
      $('#btn_resync_document').addClass('hidden');
      $('.class_complete_list_header').addClass('hidden');
      $('.class_complete_list_child').addClass('hidden');

      var url = window.location.href;
      var updatedUrl = url.split('?')[0];
      window.history.replaceState(null, null, updatedUrl);

    });

    $(document).on('click','#btn_submit_review',function(){

      var req_guid = '<?php echo $_GET["guid"] ?>';
      var total_missing_doc = $('#total_missing_doc').text();

      if(req_guid == '' || req_guid == null)
      {

        Swal.fire('Missing Request No','','error');

        return;
      }

      if(total_missing_doc != 0){
        var text_message = 'There are still '+total_missing_doc+' unavailable documents';
      }else{
        var text_message = 'After submit the pricing will be finalized.';
      }

      Swal.fire({
        title: 'Submit review for this request document?',
        text: text_message,
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
            url:"<?php echo site_url('Archived_document/submit_review') ?>",
            method:"POST",
            data:{req_guid:req_guid},
            beforeSend:function(){
              $('.btn').button('loading');  
            },
            complete: function() {
                $('.btn').button('reset');
            },
            success:function(data)
            {
              json = JSON.parse(data);
              if (json.status == 1) {

                Swal.fire(json.message,'','success');

                setTimeout(function(){
                  window.location.href = '<?php echo site_url("Archived_document?guid=");?><?php echo $row["request_guid"];?>';
                },500);

              }else{

                Swal.fire(json.message,'','error');

                $('.btn').button('reset');

              }

            }
          });

        } else if (result.dismiss === Swal.DismissReason.cancel) {
          return;
        }
      });
    });

    $(document).on('click','#btn_resync_document',function(){

      var req_guid = '<?php echo $_GET["guid"] ?>';

      if(req_guid == '' || req_guid == null)
      {

        Swal.fire('Missing Request No','','error');

        return;
      }

      $('#progress-bar-meter').val(0);
      $('#progress-bar-meter').removeClass('hidden');
      $('#loading-screen').removeClass('hidden');
      $('#loader_message').text('Checking the document');

      function resyncDocument(req_guid, checking_flag = 0) {

        var initial_missing = $('#total_missing_doc').text();

        $.ajax({
          url:"<?php echo site_url('Archived_document/resync_document') ?>",
          type: 'POST',
          data:{req_guid:req_guid,checking_flag:checking_flag,initial_missing:initial_missing},
          success: function(data) {
            console.log(data);
            json = JSON.parse(data);

            $('#progress-bar-meter').val(json.progress);

            if (json.status == 1) {

              $('#loading-screen').addClass('hidden');
              Swal.fire(json.message,'','success');

              setTimeout(function(){
                window.location.href = '<?php echo site_url("Archived_document?guid=");?><?php echo $row["request_guid"];?>';
              },500);

            } else {
              setTimeout(function() {
                resyncDocument(req_guid, 1);
              }, 500);
            }
          },
          error: function(xhr, status, error) {
            console.error(error);
          }
        });
      }

      resyncDocument(req_guid);

    });

    $(document).on('click','#btn_trigger_ticket',function(){

      var req_guid = '<?php echo $_GET["guid"] ?>';

      if(req_guid == '' || req_guid == null)
      {

        Swal.fire('Missing Request No','','error');

        return;
      }

      Swal.fire({
        title: 'Create a ticket for the unavailable document?',
        text: '',
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

          $('#loading-screen').removeClass('hidden');
          $('#loader_message').text('Creating Panda Helpdesk ticket');

          $.ajax({
            url:"<?php echo site_url('Archived_document/trigger_ticket') ?>",
            method:"POST",
            data:{req_guid:req_guid},
            beforeSend:function(){
              $('.btn').button('loading');  
            },
            complete: function() {
                $('.btn').button('reset');
                $('#loading-screen').addClass('hidden');
            },
            success:function(data)
            {
              json = JSON.parse(data);
              if (json.status == 1) {

                $('#btn_trigger_ticket').addClass('hidden');
                $('#btn_resync_document').addClass('hidden');             
                $('#helpdesk_ticket').text(json.ticket_number);

                Swal.fire(json.message,'','success');

              }else{

                Swal.fire(json.message,'','error');

              }

            }
          });

        } else if (result.dismiss === Swal.DismissReason.cancel) {
          return;
        }
      });
    });

    $(document).on('click','#btn_approve_request',function(){

      var req_guid = '<?php echo $_GET["guid"] ?>';

      if(req_guid == '' || req_guid == null)
      {

        Swal.fire('Missing Request No','','error');

        return;
      }

      Swal.fire({
        title: 'Confirm approve this request document?',
        text: 'By approving this request document, you acknowledge that extra fees will be charged to your monthly billing statement.',
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
            url:"<?php echo site_url('Archived_document/approve_request') ?>",
            type: 'POST',
            data:{req_guid:req_guid},
            beforeSend:function(){
              $('.btn').button('loading');  
            },
            complete: function() {
              $('.btn').button('reset');
            },
            success: function(data) {
              json = JSON.parse(data);
              if (json.status == 1) {

                Swal.fire(json.message,'','success');

                setTimeout(function(){
                  window.location.href = '<?php echo site_url("Archived_document?guid=");?><?php echo $row["request_guid"];?>';
                },500);

              } else {
                Swal.fire(json.message,'','error');
              }
            },
            error: function(xhr, status, error) {
              console.error(error);
            }
          });

        } else if (result.dismiss === Swal.DismissReason.cancel) {
          return;
        }
      });
    });

    $(document).on('click','#btn_reject_request',function(){

      var req_guid = '<?php echo $_GET["guid"] ?>';

      if(req_guid == '' || req_guid == null)
      {

        Swal.fire('Missing Request No','','error');

        return;
      }

      Swal.fire({
        title: 'Confirm reject this request document?',
        text: 'Please confirm your decision to reject the request document. This action cannot be reversed.',
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
            url:"<?php echo site_url('Archived_document/reject_request') ?>",
            type: 'POST',
            data:{req_guid:req_guid},
            beforeSend:function(){
              $('.btn').button('loading');  
            },
            complete: function() {
              $('.btn').button('reset');
            },
            success: function(data) {
              json = JSON.parse(data);
              if (json.status == 1) {

                Swal.fire(json.message,'','success');

                setTimeout(function(){
                  window.location.href = '<?php echo site_url("Archived_document?guid=");?><?php echo $row["request_guid"];?>';
                },500);

              } else {
                Swal.fire(json.message,'','error');
              }
            },
            error: function(xhr, status, error) {
              console.error(error);
            }
          });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          return;
        }
      });
    });

    $(document).on('click', '#btn_view_report', function(event){

      $('#embed_loader').show();

      var guid = $(this).attr('guid');
      var doc_refno = $(this).attr('doc_refno');
      var doc_type = $(this).attr('doc_type');
      var file_path = $(this).attr('file_path');

      // var doc_refno = 'TPPPO23070166';
      var param_refno = '?refno='+doc_refno;
      var param_mode = '&mode=archived_doc';
      var param_type = '';

      if((file_path == '' || file_path == null) && doc_type != 'ACC')
      {

        if(doc_type == 'PO'){

          var report_url = "<?php echo site_url('B2b_po/po_report')?>";

        }else if(doc_type == 'GRN'){

          var report_url = "<?php echo site_url('B2b_gr/gr_report')?>";

        }else if(doc_type == 'GRDA'){

          var report_url = "<?php echo site_url('B2b_grda/grda_report')?>";

        }else if(doc_type == 'PRDN'){

          var report_url = "<?php echo site_url('B2b_prdncn/prdncn_report')?>";
          var param_type = "&type=DN";

        }else if(doc_type == 'PRCN'){

          var report_url = "<?php echo site_url('B2b_prdncn/prdncn_report')?>";
          var param_type = "&type=CN";

        }else if(doc_type == 'PDN'){

          var report_url = "<?php echo site_url('B2b_pdncn/pdncn_report')?>";

        }else if(doc_type == 'PCN'){

          var report_url = "<?php echo site_url('B2b_pdncn/pdncn_report')?>";

        }else if(doc_type == 'PCI'){

          var report_url = "<?php echo site_url('B2b_pci/pci_report')?>";

        }else if(doc_type == 'DI'){

          var report_url = "<?php echo site_url('B2b_di/di_report')?>";

        }else if(doc_type == 'SI'){

          var report_url = "<?php echo site_url('B2b_si/si_report')?>";

        }else if(doc_type == 'STRB'){

          var report_url = "<?php echo site_url('B2b_strb/strb_report')?>";

        }else{

          Swal.fire('Invalid Doc Type, Unable to Display Report','','error');
          return;
        }

        var src = report_url+param_refno+param_mode+param_type;

      }else{

        var src = file_path+doc_refno+'.pdf';

      }

      // var src = 'https://file.xbridge.my/b2b-pdf/misc_doc/1F90F5EF90DF11EA818B000D3AA2CAA9/0F1ADE9FC8C911E991FD000D3AA2838A/external_doc/2023-05/PRDN/MGTDN22040149.pdf';

      $('#embed_report').attr("src", src);
      $('#modalViewReport').fadeIn();
    }); 

    $(document).on('click', '#btn_upload_pdf', function(event){

      var guid = $(this).attr('guid');

      $.ajax({
        url:"<?php echo site_url('Archived_document/check_before_upload');?>",
        method:"POST",
        data: {guid:guid},
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

          if (json.status == true) {

            $('#doc_guid').val(guid);
            $('#modalUploadPDF').fadeIn();

          }else{

            Swal.fire(json.message,'','error');

            return;

          }

        }
      });
    }); 

    $(document).on('click', '#close_modalUploadPDF1, #close_modalUploadPDF2', function(event){
      $('#modalUploadPDF').fadeOut();
    });

    $(document).on('change', '#upload_pdf', function(e) {

      var fileName = e.target.files[0].name;

      if (fileName != '') {
          $('#submit_pdf').remove();

          $('#excel_file_form').append('<div class="col-md-12" ><button type="button" id="submit_pdf" class="btn btn-block btn-success" style="margin-top:10px;">Submit</button></div>');

          $('#output').html(fileName);

      } else {
          $('#output').html('No files selected');
          $('#submit_pdf').remove();
      }

    });

    $(document).on('click', '#reset_input', function() {

      $('#upload_pdf').val('');

      var file = $('#upload_pdf')[0].files[0];

      if (file === undefined) {
          $('#output').html('No files selected');
          $('#submit_pdf').remove();
      } else {
          var fileName = file.name;

          $('#submit_pdf').remove();

          $('#excel_file_form').append('<button type="button" class="btn btn-block btn-success" id="submit_pdf" style="margin-top:10px;">Submit</button>');

          $('#output').html(fileName);
      }
    });

    $(document).on('click', '#submit_pdf', function() {

      var guid = $('#doc_guid').val();

      var formData = new FormData();
      formData.append('file', $('#upload_pdf')[0].files[0]);
      formData.append('guid', guid);

      $.ajax({
        url:"<?php echo site_url('Archived_document/upload_pdf');?>",
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
          $('#modalUploadPDF').fadeOut();

          if (json.status == true) {

            Swal.fire(json.message,'','success');

            setTimeout(function(){
              window.location.href = '<?php echo site_url("Archived_document?guid=");?><?php echo $row["request_guid"];?>';
            },500);

          }else{

            Swal.fire(json.message,'','error');

            return;

          }

        }
      });
    });

    $(document).on('click', '#close_modalViewReport1, #close_modalViewReport2', function(event){
      $('#modalViewReport').fadeOut();
    }); 

  });

</script>

<script>

  $(document).ready(function() {

    $('#view_complete_list_child').on('change', '.doc_type_option', function() {    

      var rowData = [];
      var req_guid = '<?php echo $_GET["guid"] ?>';

      $('#view_complete_list_child tr').each(function() {
        var row = {};
        var guid = $(this).attr('guid');
        var doc_type = $(this).find('.doc_type_option').val();

        if(doc_type == ''){
          var doc_type = $(this).find('.doc_type_option').text();
        }
        
        row.guid = guid;
        row.doc_type = doc_type;
        
        rowData.push(row);
      });

      $.ajax({
        url: "<?php echo site_url('Archived_document/update_doctype');?>",
        type: 'POST',
        data: {req_guid:req_guid,rowData:rowData},
        success: function(data) {

          json = JSON.parse(data);

          $('#floatingButton').removeClass('hidden');
          $('#floatingButton').fadeIn().delay(500).fadeOut();

          $('#sum_total_price').text(formatNumberWithCommas(json.updated_total));

          var urlParams = window.location.search;
          var newUrl = window.location.pathname + urlParams;
          window.location.href = newUrl;

          // $("#btn_resync_document").trigger("click");

        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });

    });

    $('#view_complete_list_child').on('change', '.pricing_type_option', function() {    

      var rowData = [];
      var req_guid = '<?php echo $_GET["guid"] ?>';

      $('#view_complete_list_child tr').each(function() {
        var row = {};
        var guid = $(this).attr('guid');
        var pricing_type = $(this).find('.pricing_type_option').val();
        
        row.guid = guid;
        row.pricing_type = pricing_type;
        
        rowData.push(row);
      });

      $.ajax({
        url: "<?php echo site_url('Archived_document/update_pricing');?>",
        type: 'POST',
        data: {req_guid:req_guid,rowData:rowData},
        success: function(data) {

          json = JSON.parse(data);

          $('#floatingButton').removeClass('hidden');
          $('#floatingButton').fadeIn().delay(500).fadeOut();

          $('#sum_total_price').text(formatNumberWithCommas(json.updated_total));

        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
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

<script type="text/javascript">

  $(document).ready(function() {

    var embedElement = $('#embed_report');

    embedElement.on('load', function() {
      $('#embed_loader').hide();
    });
});
</script>

<script type="text/javascript">

  $(document).ready(function () {
    $("#btn_view_multiple").on("click", function () {
      var selectedReports = [];

      $(".checkbox_guid:checked").each(function () {
        var $parentRow = $(this).closest("tr");
        var doc_refno = $parentRow.find("#btn_view_report").attr("doc_refno");
        var doc_type = $parentRow.find("#btn_view_report").attr("doc_type");
        var file_path = $parentRow.find("#btn_view_report").attr("file_path");
        var customer_guid = $parentRow.find("#btn_view_report").attr("customer_guid");

        selectedReports.push({
          doc_refno: doc_refno,
          doc_type: doc_type,
          file_path: file_path,
          customer_guid: customer_guid
        });
      });

      if (selectedReports.length > 0) {

        var form = document.getElementById("view_multiple_report");
        
        var postdata = document.createElement('input');
        postdata.setAttribute('type', 'hidden');
        postdata.setAttribute('name', 'postdata');
        postdata.value = JSON.stringify(selectedReports);

        var submitButton = document.createElement('input');
        submitButton.setAttribute('type', 'submit');

        form.appendChild(postdata);
        form.appendChild(submitButton);
    
        form.submit();
      }else{
        Swal.fire('No document selected','','error');
      }
    });
  });
</script>
