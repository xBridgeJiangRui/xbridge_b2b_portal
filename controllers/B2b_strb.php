<?php
class b2b_strb extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper(array('form', 'url'));
        $this->load->database();
        $this->load->library('pagination');
        $this->load->library('form_validation');
        $this->load->library(array('session'));
        $this->load->library('session');
        $this->load->helper('html');
        
        //load the department_model
        $this->load->model('Po_model');
        $this->load->model('General_model');
        $this->load->model('Datatable_model');
        $this->jasper_ip = $this->file_config_b2b->file_path_name($customer_guid,'web','general_doc','jasper_invoice_ip','GDJIIP');
    }

    public function index()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login() && $_SESSION['user_group_name'] == 'SUPER_ADMIN')
        {   
            $setsession = array(
                'frommodule' => 'b2b_strb',
                );
            $this->session->set_userdata($setsession);

            if($_REQUEST['loc'] == '')
            {   
                redirect('login_c/location');
            };

            // if(isset($_SESSION['from_other']) == 0 )
            // {
                $setsession = array(
                    'strb_loc' => $_REQUEST['loc'],
                );
                $this->session->set_userdata($setsession);
                redirect('b2b_strb/strb_list');
                $this->panda->get_uri();
               
            // }
            // else
            // {
            //     if($_REQUEST['status'] == '')
            //     {
            //         unset($_SESSION['from_other']);
            //         redirect('b2b_po?loc='.$_REQUEST['loc']);
            //     };
            //     $this->panda->get_uri();
            //     redirect('general/view_status?status='.$_REQUEST['loc']);
            // }
        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function strb_list()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login() && $_SESSION['user_group_name'] == 'SUPER_ADMIN') {
            $check_loc = $_SESSION['strb_loc'];
 
            $hq_branch_code = $this->db->query("SELECT branch_code FROM acc_branch WHERE is_hq = '1'")->result();

            $hq_branch_code_array = array();

            foreach ($hq_branch_code as $key) {
                array_push($hq_branch_code_array, $key->branch_code);
            }

            $data = array(
                'po_status' => $this->db->query("SELECT code, reason from lite_b2b.set_setting where module_name = 'RC_FILTER_STATUS' order by code='ALL' desc, code asc"),
                'period_code' => $this->db->query("SELECT period_code FROM lite_b2b.list_period_code ORDER BY period_code DESC "),
                'location_description' => $this->db->query("SELECT * FROM b2b_summary.cp_set_branch WHERE BRANCH_CODE = '$check_loc' and customer_guid = '" . $_SESSION['customer_guid'] . "'"),
            );


            $data_footer = array(
                'activity_logs_section' => 'strb'
            );

            $this->panda->get_uri();
            $this->load->view('header');
            $this->load->view('return_collection/b2b_rc_list_view', $data);
            //$this->load->view('general_modal', $data);
            $this->load->view('footer', $data_footer);
        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function strb_datatable()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login()) {
            //die;
            $doc = 'strb_table';
            $ref_no = $this->input->post('ref_no');
            $status = $this->input->post('status');
            $datefrom = $this->input->post('datefrom');
            $dateto = $this->input->post('dateto');
            $exp_datefrom = $this->input->post('exp_datefrom');
            $exp_dateto = $this->input->post('exp_dateto');
            $period_code = $this->input->post('period_code');
            $type = $this->input->post('type');
            $customer_guid = $_SESSION['customer_guid'];
            $query_loc = $_SESSION['query_loc'];

            $hq_branch_code = $this->db->query("SELECT branch_code FROM acc_branch WHERE is_hq = '1'")->result();
            $hq_branch_code_array = array();
            foreach ($hq_branch_code as $key) {
                array_push($hq_branch_code_array, $key->branch_code);
            }

            if (in_array($_SESSION['strb_loc'], $hq_branch_code_array)) {
                $loc = $query_loc;
            } else {
                $loc = "'" . $_SESSION['strb_loc'] . "'";
            }

            if (in_array('IAVA', $_SESSION['module_code'])) {
                $module_code_in = '';
            } else {
                $module_code_in = "AND a.sup_code IN (" . $_SESSION['query_supcode'] . ") ";
            }

            if ($ref_no == '') {
                $ref_no_in = '';
            } else {
                $ref_no_in = " AND a.batch_no LIKE '%" . $ref_no . "%' ";
            }

            if ($status == '') {
                $status_in = " AND a.status IN ('','0','1','2','3','4','8','9')";
            } else if ($status == 'ALL') {
                $get_stat = $this->db->query("SELECT `code`,reason FROM lite_b2b.set_setting WHERE module_name = 'RC_FILTER_STATUS'");

                foreach ($get_stat->result() as $row) {
                    $check_stat[] = $row->code;
                }

                foreach ($check_stat as &$value) {
                    $value = "'" . trim($value) . "'";
                }
                $check_status = implode(',', array_filter($check_stat));
                $status_in = " AND a.status IN ($check_status) ";
            } else {
                $status_in = " AND a.status = '$status' ";
            }

            if ($datefrom == '' || $dateto == '') {
                $doc_daterange_in = '';
            } else {
                $doc_daterange_in = " AND a.doc_date BETWEEN '$datefrom' AND '$dateto' ";
            }

            if ($exp_datefrom == '' || $exp_dateto == '') {
                $exp_daterange_in = '';
            } else {
                $exp_daterange_in = " AND a.expiry_date BETWEEN '$exp_datefrom' AND '$exp_dateto' ";
            }

            if ($period_code == '') {
                $period_code_in = '';
            } else {
                $period_code_in = " AND LEFT(a.doc_date, 7) = '$period_code'";
            }

            $query_count = "SELECT * FROM ( SELECT a.customer_guid AS customer_guid,
            IFNULL(a.b2b_dn_refno, '') AS prdn_refno, 
            a.batch_no,
            a.location,
            a.expiry_date,
            a.sup_name,
            a.doc_date,
            a.sup_code,
            a.b2b_dn_refno
            FROM b2b_summary.dbnote_batch_info a FORCE INDEX (customer_guid)
            INNER JOIN b2b_summary.supcus AS b 
            ON a.sup_code = b.`code` 
            AND a.customer_guid = b.customer_guid 
            AND b.b2b_registration = '1' 
            LEFT JOIN b2b_summary.dbnote_batch c
            ON a.dbnote_guid = c.dbnote_guid
            AND a.customer_guid = c.customer_guid
            WHERE a.customer_guid = '$customer_guid' 
            AND a.loc_group IN ($loc)
            $status_in 
            $doc_daterange_in
            $exp_daterange_in 
            $ref_no_in
            $period_code_in
            ) zzz
            ";

            $query = "SELECT a.customer_guid, 
            IFNULL(a.b2b_dn_refno, '') AS prdn_refno, 
            a.batch_no, 
            a.location, 
            a.doc_date, 
            a.expiry_date, 
            a.sup_code, 
            a.sup_name, 
            a.canceled, 
            IF( a.status = '0', 'Pending Accept', IF( a.status = '1', 'Accepted', IF( a.status = '2', 'Pending PRDN', IF( a.status = '3' AND a.b2b_dn_refno IS NOT NULL AND a.b2b_dn_refno != '', 'PRDN generated', IF( a.status = '8', 'Amended', IF(a.status = '9', 'Cancel', IF(STATUS = '3' AND (a.b2b_dn_refno IS NULL OR a.b2b_dn_refno = ''), 'Pending PRDN' , 'No Status') ) ) ) ) ) ) AS status_desc, 
            a.status, 
            a.accepted_at, 
            a.accepted_by, 
            IF( a.status NOT IN ('8','9') , a.uploaded_image, '0') AS uploaded_image,
            a.cancel_remark, 
            b.b2b_registration, 
            a.srb_accept_days, 
            DATE_ADD(a.doc_date, INTERVAL a.srb_accept_days DAY) AS new_expiry_date 
            FROM b2b_summary.dbnote_batch_info a FORCE INDEX (customer_guid)
            INNER JOIN b2b_summary.supcus AS b 
            ON a.sup_code = b.`code` 
            AND a.customer_guid = b.customer_guid 
            AND b.b2b_registration = '1' 
            LEFT JOIN b2b_summary.dbnote_batch c
            ON a.dbnote_guid = c.dbnote_guid
            AND a.customer_guid = c.customer_guid
            WHERE a.customer_guid = '$customer_guid' 
            AND a.loc_group IN ($loc)
            $status_in 
            $doc_daterange_in
            $exp_daterange_in 
            $ref_no_in
            $period_code_in
            ";
            //AND a.loc_group IN ($loc) -- up to production need change this

            $sql = "SELECT * FROM (
                $query
            ) zzz ";
            
            $query_strb = $this->Datatable_model->datatable_main($sql, $type, $doc);
            //print_r($query); die;
            $fetch_data = $query_strb->result();
            //print_r($fetch_data); die;
            //echo $this->db->last_query(); die;
            $data = array();
            if (count($fetch_data) > 0) {
                
                foreach ($fetch_data as $row) {
                    $tab = array();
                    
                    $datefrom = date_create(date('Y-m-d'));
                    $dateto = date_create($row->expiry_date);
                    $tab['expiry_date'] = $row->expiry_date;
                    if($row->srb_accept_days != '0')
                    {
                        $dateto = date_create($row->new_expiry_date);
                        $tab['expiry_date'] = $row->new_expiry_date;
                    }
                    
                    $interval = date_diff($dateto, $datefrom);

                    $tab['batch_no'] = $row->batch_no;
                    $tab['prdn_refno'] = "<a href=" . site_url('b2b_prdncn/prdncn_child?trans=' . $row->prdn_refno . '&loc=' .$_SESSION['strb_loc']) . '&type=DEBIT' . ">" . $row->prdn_refno . "</a>";
                    $tab['location'] = $row->location;
                    $tab['doc_date'] = $row->doc_date;
                    
                    $tab['sup_code'] = $row->sup_code;
                    $tab['sup_name'] = $row->sup_name;
                    $tab['status'] = $row->status_desc;
                    if ($datefrom > $dateto) {
                        if ($row->status != 0) {
                            $tab['canceled'] = "<span style='color:red;font-weight:bold;'>-</span>";
                        } else {
                            $tab['canceled'] = "<span style='color:red;font-weight:bold;'>-" . $interval->format('%a') . "</span>";
                        }
                    } else {
                        if ($row->status != 0) {
                            $tab['canceled'] = "<span style='color:red;font-weight:bold;'>-</span>";
                        } else {
                            $tab['canceled'] = "<span style='color:red;font-weight:bold;'>" . $interval->format('%a') . "</span>";
                        }
                    }

                    if($row->b2b_registration == '0')
                    {
                        $tab['canceled'] = "<span style='color:red;font-weight:bold;'>-</span>";
                    }

                    if($row->accepted_at == '0000-00-00 00:00:00' || $row->accepted_at == '' || $row->accepted_at == 'null' || $row->accepted_at == '1000-01-01 00:00:00' || $row->accepted_at == null)
                    {
                        $tab['accepted_at'] = '';
                        $tab['accepted_by'] = '';
                    }
                    else
                    {
                        $tab['accepted_at'] = $row->accepted_at;
                        $tab['accepted_by'] = $row->accepted_by;
                    }

                    $tab['cancel_remark'] = $row->cancel_remark;
                    if($row->uploaded_image == '1')
                    {
                        $tab['button'] =  "<a href=" . site_url('B2b_strb/strb_child') . "?refno=" . $row->batch_no . "&loc=" .$_SESSION['strb_loc'] . "&pc=" . substr($row->doc_date,0,7) . " style='float:left;margin-left:5px;' class='btn btn-sm btn-info' role='button'><span class='glyphicon glyphicon-eye-open'></span></a>
                        <button style='margin-left:5px;' id='btn_image' type='button'  title='IMAGE' class='btn btn-sm btn-warning' refno=" . $row->batch_no . " period_code=" . $row->doc_date . " outlet=" . $row->location . " image_type='STRB'><i class='fa fa-file-image-o'></i></button>";
                    }
                    else
                    {   
                        $tab['button'] =  "<a href=" . site_url('panda_return_collection/strb_child') . "?refno=" . $row->batch_no . "&loc=" .$_SESSION['strb_loc'] . "&pc=" . substr($row->doc_date,0,7) . " style='float:left;margin-left:5px;' class='btn btn-sm btn-info' role='button'><span class='glyphicon glyphicon-eye-open'></span></a>";
                    }

                    $tab['test'] = $row->status;
                    $tab['uploaded_image'] = $row->uploaded_image;

                    $data[] = $tab;
                }
            } else {
                $data = '';
            }
            
            $output = array(
                "draw"                =>     intval($_POST["draw"]),
                "recordsTotal"        =>     $this->Datatable_model->general_get_all_data($query_count, $doc),
                "recordsFiltered"     =>     $this->Datatable_model->general_get_filtered_data($query_count, $doc),
                "data"                =>     $data
            );
            
            echo json_encode($output);
        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function strb_child()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login())
        {
            $this->panda->get_uri();
            $refno = $_REQUEST['refno'];
            $loc = $_REQUEST['loc'];
            $check_status = '';
            $customer_guid = $this->session->userdata("customer_guid");
            $user_guid = $this->session->userdata("user_guid");
            
            $set_code = $this->db->query("SELECT code,reason from  set_setting where module_name = 'RETURN_COLLECTION' order by reason asc");
            $set_admin_code = $this->db->query("SELECT code,reason from  set_setting where module_name = 'ADMIN' order by reason asc");

            $get_current_status = $this->db->query("SELECT strb_json_info,dbnote_guid,`status`,LEFT(doc_date,7) as period_code,IF( STATUS NOT IN ('8','9') ,uploaded_image, '0') AS uploaded_image FROM b2b_summary.`dbnote_batch_info` WHERE batch_no = '$refno' AND customer_guid = '$customer_guid'");
            
            if($get_current_status->num_rows() == 0 )
            {
                echo 'No Data Found'; die;
            }

            $uploaded_image = $get_current_status->row('uploaded_image');
            $response = $get_current_status->row('strb_json_info');
            $get_child_detail = json_decode($response, true)['dbnote_batch_c'];
            $child_result_validation = $get_child_detail[0]['line'];

            if($child_result_validation != '1')
            {
                $get_child_detail = array();

                $check_child = array(array(
                    'line' => 'No Records Founds',
                    'itemcode' => '',
                    'barcode' => '',
                    'description' => '',
                    'packsize' => '',
                    'um' => '',
                    'input_cost' => '0.00',
                    'reason' => '',
                    'qty' => '',
                )); 
                $set_disabled = '1';
                $this->session->set_flashdata('warning', 'Connection fail at customer server. Please try again in a few minutes');
            }
            else
            {
                $check_child = $get_child_detail; 
                $set_disabled = '0';
            }

            $data = array(
                'title' => 'Return Collection',
                'get_child_detail' => $get_child_detail,
                'check_child' => $check_child,
                'set_code' => $set_code,
                'set_admin_code' => $set_admin_code,
                'get_current_status' => $get_current_status->row('status'),
                'stock_guid' => $get_current_status->row('dbnote_guid'),
                'uploaded_image' => $uploaded_image,
                'set_disabled' => $set_disabled,
                'request_link_strb' => site_url('B2b_strb/strb_report?refno='.$refno),
            );

            //print_r($data); die;
            $this->load->view('header');       
            $this->load->view('return_collection/b2b_rc_pdf',$data);
            $this->load->view('general_modal',$data);
            $this->load->view('footer');
        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function strb_report()
    {
        $refno = $_REQUEST['refno'];
        $customer_guid = $_SESSION['customer_guid'];
        $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

        $url = $this->jasper_ip . "/jasperserver/rest_v2/reports/reports/PandaReports/Backend_PO/Stock_Return_Batch_Json.pdf?refno=".$refno."&customer_guid=".$customer_guid; // po
        // $url = $this->jasper_ip . "/jasperserver/rest_v2/reports/reports/PandaReports/Backend_PO/main_jrxml.pdf?refno=".$refno."&customer_guid=".$customer_guid."&mode=".$mode; // po

        //print_r($url); die;
        $check_code = $this->db->query("SELECT sup_code from b2b_summary.dbnote_batch_info where batch_no = '$refno' and customer_guid = '" . $_SESSION['customer_guid'] . "'")->row('sup_code');

        $check_code = str_replace("/", "+-+", $check_code);

        $parameter = $this->db->query("SELECT * from menu where module_link = 'b2b_strb'");
        $type = $parameter->row('type');
        $code = $check_code;

        $filename = $this->db->query("SELECT REPLACE(REPLACE(REPLACE(filename_format, 'type', '$type'), 'code', '$code'), 'refno' , '$refno') AS query FROM menu where module_link = 'b2b_strb'")->row('query');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic cGFuZGFfYjJiOmIyYkBhZG5hcA==',
                'Cookie: userLocale=en_US; JSESSIONID=5221928B4926B138CB796C763F550CB4'
            ),
        ));
            
        $response = curl_exec($curl);

        header('Content-type:application/pdf');
        header('Content-Disposition: inline; filename='.$filename.'.pdf');
        echo $response; 

        curl_close($curl); 
    }

    public function strb_view_image()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login()) {
            $customer_guid = $this->session->userdata("customer_guid");
            $outlet = $this->input->post('outlet');
            $refno = $this->input->post('refno');
            //$period_code = date('Ym', strtotime($this->input->post('period_code')));
            $doc_type = $this->input->post('image_type');
            $action = 'list';
            if($outlet == '')
            {
                $outlet = $this->db->query("SELECT `location` FROM b2b_summary.dbnote_batch_info WHERE batch_no = '$refno' AND customer_guid = '$customer_guid' ")->row('location');
            }

            $period_code = $this->db->query("SELECT REPLACE(LEFT(created_at,7),'-','') as period_code FROM b2b_summary.dbnote_batch_info WHERE batch_no = '$refno' AND customer_guid = '$customer_guid' ")->row('period_code');

            $azure_container_name = $this->db->query("SELECT azure_container_name FROM lite_b2b.acc WHERE acc_guid = '$customer_guid' AND isactive = '1'")->row('azure_container_name');
    
            if($azure_container_name == '' || $azure_container_name == 'null' || $azure_container_name == null)
            {
                $data = array(
                    'para1' => 'false',
                    'msg' => 'Invalid ERR01. Please Contact Admin',

                );
                echo json_encode($data);
                exit();
            }

            $azure_directory_path = $outlet . "/" . $doc_type . "/" . $period_code . "/" . $refno ."/";
            //$azure_directory_path = '1017/STRB/202204/1017DNB22040004';
            //print_r($azure_directory_path); die;

            $to_shoot_url = 'https://api3.xbridge.my/api/token/';

            //echo $to_shoot_url;die;
            $data = array();

            //id - pass
            $data = array(
                'username' => 'panda',
                'password' => '&_)GZh9Kd?D6gHRu',
            );

            $ch = curl_init($to_shoot_url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-KEY: 123456", "Content-Type: application/json")); // content-type important
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // need use json_encode
            $result = curl_exec($ch);
            //$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $output = json_decode($result);

            curl_close($ch);

            if (isset($output->refresh) || isset($output->access)) {
                $token_access = $output->access;
                $token_refresh = $output->refresh;
            } else {
                //insert_log
                $token_access = '';
                $token_refresh = '';
            }

            //print_r($token_access); die;

            $to_shoot_url2 = 'https://api3.xbridge.my/azure/upload/';
            //echo $to_shoot_url2;die;
            $data2 = array();

            $data2 = array(
                'file' => '',
                'action' => 'list',
                'azure_directory_path' => $azure_directory_path,
                'doc_type' => 'STRB',
                'azure_container_name' => $azure_container_name,
            );
            //print_r($data2); die;
            //echo json_encode($data2);die;

            $headers = array(
                "X-Api-KEY: 123456",
                "Authorization: Bearer $token_access",
            );
            //print_r($headers); die;

            $ch = curl_init($to_shoot_url2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);

            $result = curl_exec($ch);
            //$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $output = json_decode($result);
            //print_r($result);die;
            curl_close($ch);

            if ($output->status == 'true') {
                $user_guid = $_SESSION['user_guid'];
                $from_module = $_SESSION['frommodule'];

                $guid = $this->db->query("SELECT REPLACE(UPPER(UUID()),'-','') AS guid")->row('guid');
                $log_1 = array(
                    'movement_guid' => $guid,
                    'customer_guid' => $customer_guid,
                    'user_guid' => $user_guid,
                    'action' => 'list_image_strb',
                    'module' => $from_module,
                    'value' => $refno,
                    'created_at' => $this->db->query("SELECT NOW() as now")->row('now'),
                );
                $this->db->insert('lite_b2b_apps.supplier_movement', $log_1);

                $total_file = $output->total_file;
                $file_path_list = $output->file_path_list;

                $data = array(
                    'para1' => 'true',
                    'total_file' => $total_file,
                    'file_path_list' => $file_path_list,
                );
                echo json_encode($data);
            } else {
                $data = array(
                    'para1' => 'false',
                    'msg' => 'Get Image Failed. Please Try Again.',

                );
                echo json_encode($data);
            }
        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function confirm()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $this->session->userdata('user_logs') == $this->panda->validate_login()) {
            $table = $_REQUEST['table'];
            $refno = $_REQUEST['refno'];
            $customer_guid = $_REQUEST['customer_guid'];
            $user_guid = $_SESSION['user_guid'];
            $from_module = $_SESSION['frommodule'];
            $loc = $_REQUEST['loc'];

            if ($from_module == 'b2b_strb') // return collection accept collection detail
            {
                $check_status = $this->db->query("SELECT * from b2b_summary.dbnote_batch_info where batch_no = '$refno' and customer_guid = '$customer_guid'");

                $userlog_module = 'accept_return';

                if ($check_status->row('status') == '1') {
                    $this->session->set_flashdata('warning', 'Unable to accept refno ' . $refno . '! Status is not empty');

                    //echo $this->session->userdata('warning') ;die;
                    redirect($_SESSION['frommodule'] . "/strb_child?refno=" . $refno . "&loc=" . $_REQUEST['loc'] .  "&pc=" . substr($check_status->row('doc_date'),0,7));
                } else {
                    $user_name = $this->db->query("SELECT a.user_name FROM lite_b2b.set_user a WHERE a.user_guid ='$user_guid'")->row('user_name');
                    $this->session->set_flashdata('message', 'Accept refno ' . $refno . ' Successfully');

                    $this->db->query("UPDATE b2b_summary.dbnote_batch_info set status = '1' , accepted_by = '$user_name', accepted_at =  now(),uploaded = 0,action_date = CURDATE() where customer_guid ='$customer_guid' and batch_no = '$refno' and status = '0'");

                    $this->db->query("REPLACE into supplier_movement select 
                        upper(replace(uuid(),'-','')) as movement_guid
                        , '$customer_guid'
                        , '$user_guid'
                        , '$userlog_module'
                        , '$from_module'
                        , '$refno'
                        , now()
                        ");
                    redirect($_SESSION['frommodule'] . "/strb_child?refno=" . $refno . "&loc=" . $_REQUEST['loc'] . "&pc=" . substr($check_status->row('doc_date'),0,7));
                }
            }

        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');

            redirect('#');
        }
    }

} // nothing after this
