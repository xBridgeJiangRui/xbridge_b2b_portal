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

                                <!-- REMOVE RETAILER -->
                                <!--<div class="col-md-2"><b>Retailer Name</b></div>
                                <div class="col-md-4">
                                    <select name="customer_guid" id="customer_guid" class="form-control">
                                        <option value="" disabled selected>-Select-</option>
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
                                <div class="clearfix"></div><br>-->
                                <!-- REMOVE RETAILER -->

                                <div class="col-md-2"><b>Supplier Name</b></div>
                                <div class="col-md-4">
                                    <select name="supplier_guid" id="supplier_guid" class="form-control select2">
                                        <option value="" disabled selected>-Select-</option>
                                        <?php
                                        foreach($set_supplier->result() as $row)
                                        {
                                        ?>
                                            <option value="<?=$row->supplier_guid;?>"><?=$row->supplier_name;?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="clearfix"></div><br>

                                <div class="col-md-12">
                                    <button type="button" id="search" class="btn btn-primary" onmouseover="CompareDate()" style="margin-right: 10px;"><i class="fa fa-search"></i> Search</button>
                                    <button type="button" id="reset" class="btn btn-default"><i class="fa fa-repeat"></i>Reset</a></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Body -->
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><b>Registration</b></h3> &nbsp;
                <span id="parameter_span"></span>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>

            <div class="box-body">
                <table id="registration_table" class="table table-bordered">
                    <thead>
                        <tr>
                            <!-- Column Headers -->
                            <th>Retailer Name</th>
                            <th>Supplier Name</th>
                            <th>Invoice Date</th>
                            <!-- End Column Headers -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // Function to initialize and populate the data table
        function registration_table(supplier_guid) {
            if ($.fn.DataTable.isDataTable('#registration_table')) {
                $('#registration_table').DataTable().destroy();
            }

            // Variable to hold HTML for displaying selected supplier as a "pill button"
            var span_button_para = '';

            if (supplier_guid != '' && supplier_guid != null) {
                var display_supplier = $('#supplier_guid option:selected').text();

                // Create a "pill button" with the selected supplier's name
                span_button_para += '<span class="pill_button"> ' + display_supplier + ' </span>';
            }

            // Clear and update the parameter_span with the "pill button"
            $('#parameter_span').html(span_button_para);

            var table;
            table = $('#registration_table').DataTable({
                "processing": true,
                'paging': true,
                'lengthChange': true,
                'lengthMenu': [[10, 25, 50, 9999999999999999], [10, 25, 50, "ALL"]],
                'searching': true,
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': false,
                "bPaginate": true,
                "bFilter": true,
                "ajax": {
                    "url": "<?php echo site_url('Registration_dashboard/registration_table'); ?>",
                    "type": "POST",
                    "data": { supplier_guid: supplier_guid },
                },
                columns: [
                    { "data": "acc_name" },
                    { "data": "supplier_name" },
                    { "data": "invoice_date" },
                ],
                dom: '<"row"<"col-sm-4" l><"col-sm-8" f>>rtip',
                "initComplete": function (settings, json) {
                    setTimeout(function () {
                        interval();
                    }, 300);
                }
            }); // close datatable
        }

        // Initialize the registration_table function with the default selected supplier
        registration_table('');

        // Event listener for the "Search" button
        $(document).on('click', '#search', function () {
            var supplier_guid = $('#supplier_guid').val();
            console.log("Selected supplier:", supplier_guid);

            if (supplier_guid === '' || supplier_guid === null) {
                // Display an error message using Swal (SweetAlert)
                Swal.fire({
                    icon: 'error',
                    title: 'Please select Supplier Name first',
                    html: 'Please select <strong>Supplier Name</strong> first',
                });
                return;
            }

            // Call the registration_table function with the selected supplier
            registration_table(supplier_guid);
        });

        // Event listener for the "Reset" button
        $(document).on('click', '#reset', function () {
            // Reset the supplier dropdown to its default value ("-Select-")
            $('#supplier_guid').val('').trigger('change');
            
            // Clear the parameter_span
            $('#parameter_span').html('');

            // Call the registration_table function with an empty supplier_guid
            registration_table('');
        });
    });
</script>
