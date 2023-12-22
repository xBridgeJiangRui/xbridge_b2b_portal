<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Panda_rms extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('General_model');
        $this->load->library('form_validation');
        $this->load->library('datatables');

        // nabil testing
        // $_SESSION['customer_guid'] = "8D5B38E931FA11E79E7E33210BD612D3";
    }

    public function index()
    {

        if($_SESSION['userid'] == 'nabil.haziq@pandasoftware.my'){
            $_SESSION['customer_guid'] == '8D5B38E931FA11E79E7E33210BD612D3';
        }

        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login()) {

            $b2b_doc_code = $_REQUEST['code'];
            $customer_guid = $this->session->userdata('customer_guid');
            $other_doc_filter_drop_down = $this->db->query("SELECT * FROM set_setting WHERE module_name = 'other_doc_filter' order by code='ALL' desc, code asc");

            $details = $this->db->query("SELECT * FROM other_doc_setting WHERE customer_guid = '$customer_guid' AND code = '$b2b_doc_code' LIMIT 1");

            if($b2b_doc_code == 'RMS_TTA'){
                $title = 'TTA';
            }else if($b2b_doc_code == 'RMS_MNC'){
                $title = 'Manual Claim';
            }else if($b2b_doc_code == 'RMS_DCR'){
                $title = 'DC Rebate';
            }else if($b2b_doc_code == 'RMS_TTB'){
                $title = 'TTA Back Charge';
            }else if($b2b_doc_code == 'RMS_CON'){
                $title = 'TTA Conditional Rebate';
            }else{
                $title = '';
            }

            $query = '';
            if (isset($_REQUEST['refno'])) {
                $refno = $_REQUEST['refno'];
            } else {
                $refno = '';
            }

            if (isset($_REQUEST['status'])) {
                $status = $_REQUEST['status'];
            } else {
                $status = '';
            }

            if (isset($_REQUEST['datetime'])) {
                $datetime = $_REQUEST['datetime'];
                $start_date = substr($datetime, 0, 10);
                $end_date = substr($datetime, 12, 21);
            } else {
                $start_date = '';
                $end_date = '';
            }

            $data = array(
                'b2b_doc_code' => $b2b_doc_code,
                'title' => $title,
                'refno' => $refno,
                'status' => $status,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'other_doc_filter_drop_down' => $other_doc_filter_drop_down,
            );
            // print_r($data);die;

            $this->load->view('header');
            $this->load->view('rms/panda_rms', $data);
            $this->load->view('footer');
            // echo $url.$b2b_doc_code.$customer_guid;die;
        } else {
            redirect('login_c');
        }
    }

    public function view_table()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login()) {
            $customer_guid = $this->session->userdata('customer_guid');

            $title = $this->input->post('title');
            $doctype = $this->input->post('b2b_doc_code');

            $query = '';
            if ($this->input->post('refno') != '') {
                $refno = $this->input->post('refno');
                $query .= " AND refno LIKE '%" . $refno . "%'";
            } else {
                $query .= '';
            }

            if ($this->input->post('status') != '') {
                $status = $this->input->post('status');
                if ($status == 'ALL') {
                    $get_status = $this->db->query("SELECT * FROM set_setting WHERE module_name = 'other_doc_filter'");
                    $status = '';
                    foreach ($get_status->result() as $row) {
                        $status .= "'" . $row->code . "',";
                    }
                    $status = rtrim($status, ',');
                } else {
                    $status = "'" . $this->input->post('status') . "'";
                }
                $query .= ' AND status IN (' . $status . ')';
            } else {
                $query .= "AND status IN ('')";
            }

            if ($this->input->post('start_date') != '') {
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');

                $query .= " AND doctime BETWEEN '" . $start_date . " 00:00:00' AND '" . $end_date . " 23:59:59'";
            } else {
                $query .= "";
            }
            // echo $query;die;

            $columns = array(
                0 => 'refno',
                1 => 'supcode',
                2 => 'debtor_code',
                3 => 'supname',
                4 => 'doctime',
                5 => 'status',
                6 => 'button',
                7 => 'box',
            );

            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $order = $columns[$this->input->post('order')[0]['column']];
            $dir = $this->input->post('order')[0]['dir'];

            $supcode = $this->session->userdata('query_supcode');

            if (!in_array('IAVA', $_SESSION['module_code'])) {
                $xsupcode = $this->session->userdata('query_supcode');
                $debit_supcode = $this->db->query("SELECT accpdebit FROM b2b_summary.supcus WHERE customer_guid = '$customer_guid' AND code IN($xsupcode) AND (accpdebit <> '' AND accpdebit IS NOT NULL)");
                // echo $this->db->last_query();die;
                if ($debit_supcode->num_rows() > 0) {
                    $dcode = '';
                    foreach ($debit_supcode->result()  as $debitcode) {
                        $dcode .= "'" . $debitcode->accpdebit . "',";
                    }
                    $dcode = rtrim($dcode, ",");
                    if ($dcode != '' || $dcode != null) {
                        $supcode = $supcode . ',' . $dcode;
                    }
                }
            }

            if (empty($this->input->post('search')['value'])) {
                if (in_array('IAVA', $_SESSION['module_code'])) {

                    $totalData = $this->db->query("SELECT COUNT(`count`) AS `count` FROM ( SELECT COUNT(DISTINCT refno) as count FROM b2b_summary.other_doc WHERE customer_guid = '$customer_guid' AND doctype = '$doctype' $query GROUP BY refno,doctime ) a ")->row('count');

                    $totalFiltered = $totalData;
                } else {
                    $totalData = $this->db->query("SELECT COUNT(DISTINCT refno) as count FROM b2b_summary.other_doc WHERE customer_guid = '$customer_guid' AND doctype = '$doctype' AND supcode IN ($supcode) $query")->row('count');
                    $totalFiltered = $totalData;
                }

            } else {
                $search = addslashes($this->input->post('search')['value']);
                if (in_array('IAVA', $_SESSION['module_code'])) {
                    $totalData = $this->db->query("SELECT count(refno) as count FROM (SELECT IF( STATUS IS NULL || STATUS = '', 'NEW', STATUS ) AS status, refno, supname, doctime, doctype, IF( b.code IS NULL, a.supcode, b.accpdebit ) AS debtor_code, CASE WHEN b.code IS NULL AND c.code IS NOT NULL THEN GROUP_CONCAT(DISTINCT c.code) WHEN b.code IS NULL AND c.code IS NULL THEN '' WHEN b.code IS NOT NULL AND c.code IS NOT NULL THEN 'error' WHEN b.code IS NOT NULL AND c.code IS NULL THEN GROUP_CONCAT(DISTINCT b.code) WHEN b.code IS NOT NULL AND c.code = '' THEN 'error' ELSE '0' END AS supcode FROM b2b_summary.other_doc  a LEFT JOIN b2b_summary.supcus b ON a.customer_guid = b.customer_guid AND a.supcode = b.code AND b.type <> 'C' LEFT JOIN b2b_summary.supcus c ON a.supcode = c.`AccPDebit` AND a.`customer_guid` = c.`customer_guid` AND c.type <> 'C' WHERE a.customer_guid = '$customer_guid' AND a.doctype = '$doctype' $query GROUP BY a.refno,a.doctime) a WHERE (refno like '%" . $search . "%' OR supname like '%" . $search . "%' OR supcode like '%" . $search . "%')")->row('count');
                    $totalFiltered = $totalData;
                } else {
                    $totalData = $this->db->query("SELECT count(refno) FROM (SELECT IF( STATUS IS NULL || STATUS = '', 'NEW', STATUS ) AS status, refno, supname, doctime, doctype, IF( b.code IS NULL, a.supcode, b.accpdebit ) AS debtor_code, CASE WHEN b.code IS NULL AND c.code IS NOT NULL THEN GROUP_CONCAT(DISTINCT c.code) WHEN b.code IS NULL AND c.code IS NULL THEN '' WHEN b.code IS NOT NULL AND c.code IS NOT NULL THEN 'error' WHEN b.code IS NOT NULL AND c.code IS NULL THEN GROUP_CONCAT(DISTINCT b.code) WHEN b.code IS NOT NULL AND c.code = '' THEN 'error' ELSE '0' END AS supcode FROM b2b_summary.other_doc  a LEFT JOIN b2b_summary.supcus b ON a.customer_guid = b.customer_guid AND a.supcode = b.code AND b.type <> 'C' LEFT JOIN b2b_summary.supcus c ON a.supcode = c.`AccPDebit` AND a.`customer_guid` = c.`customer_guid` AND c.type <> 'C' WHERE a.customer_guid = '$customer_guid' AND a.doctype = '$doctype' AND supcode IN ($supcode) $query GROUP BY a.refno) a WHERE (refno like '%" . $search . "%' OR supname like '%" . $search . "%' OR supcode like '%" . $search . "%') ORDER BY $order $dir LIMIT $start,$limit")->row('count');
                    $totalFiltered = $totalData;
                }

                // $totalFiltered = $totalData; 
                // echo $this->db->last_query();
            }

            if (empty($this->input->post('search')['value'])) {

                if (in_array('IAVA', $_SESSION['module_code'])) {
                    // echo $start;die;
                    $posts = $this->db->query("SELECT IF( STATUS IS NULL || STATUS = '', 'NEW', STATUS ) AS status, refno, supname, doctime, doctype, IF( b.code IS NULL, a.supcode, b.accpdebit ) AS debtor_code, CASE WHEN b.code IS NULL AND c.code IS NOT NULL THEN GROUP_CONCAT(DISTINCT c.code) WHEN b.code IS NULL AND c.code IS NULL THEN '' WHEN b.code IS NOT NULL AND c.code IS NOT NULL THEN 'error' WHEN b.code IS NOT NULL AND c.code IS NULL THEN GROUP_CONCAT(DISTINCT b.code) WHEN b.code IS NOT NULL AND c.code = '' THEN 'error' ELSE '0' END AS supcode FROM b2b_summary.other_doc  a LEFT JOIN b2b_summary.supcus b ON a.customer_guid = b.customer_guid AND a.supcode = b.code AND b.type <> 'C' LEFT JOIN b2b_summary.supcus c ON a.supcode = c.`AccPDebit` AND a.`customer_guid` = c.`customer_guid` AND c.type <> 'C' WHERE a.customer_guid = '$customer_guid' AND a.doctype = '$doctype' $query GROUP BY a.refno,a.doctime ORDER BY $order $dir LIMIT $start,$limit")->result();
                    // echo $this->db->last_query();die;
                } else {
                    $posts = $this->db->query("SELECT IF( STATUS IS NULL || STATUS = '', 'NEW', STATUS ) AS status, refno, supname, doctime, doctype, IF( b.code IS NULL, a.supcode, b.accpdebit ) AS debtor_code, CASE WHEN b.code IS NULL AND c.code IS NOT NULL THEN GROUP_CONCAT(DISTINCT c.code) WHEN b.code IS NULL AND c.code IS NULL THEN '' WHEN b.code IS NOT NULL AND c.code IS NOT NULL THEN 'error' WHEN b.code IS NOT NULL AND c.code IS NULL THEN GROUP_CONCAT(DISTINCT b.code) WHEN b.code IS NOT NULL AND c.code = '' THEN 'error' ELSE '0' END AS supcode FROM b2b_summary.other_doc  a LEFT JOIN b2b_summary.supcus b ON a.customer_guid = b.customer_guid AND a.supcode = b.code AND b.type <> 'C' LEFT JOIN b2b_summary.supcus c ON a.supcode = c.`AccPDebit` AND a.`customer_guid` = c.`customer_guid` AND c.type <> 'C' WHERE a.customer_guid = '$customer_guid' AND a.doctype = '$doctype' AND supcode IN ($supcode) $query GROUP BY a.refno ORDER BY $order $dir LIMIT $start,$limit")->result();
                    // $posts = $this->db->query("SELECT IF(status is null || status = '','NEW',status) as status,refno,supcode,supname,doctime,doctype FROM $b2b_database.$b2b_table WHERE customer_guid = '$customer_guid' AND doctype = '$doctype' AND supcode IN ($supcode) $query ORDER BY $order $dir LIMIT $start,$limit")->result();
                }
                // echo $this->db->last_query();die;
            } else {

                $search = addslashes($this->input->post('search')['value']);
                if (in_array('IAVA', $_SESSION['module_code'])) {
                    $posts =  $this->db->query("SELECT * FROM (SELECT IF( STATUS IS NULL || STATUS = '', 'NEW', STATUS ) AS status, refno, supname, doctime, doctype, IF( b.code IS NULL, a.supcode, b.accpdebit ) AS debtor_code, CASE WHEN b.code IS NULL AND c.code IS NOT NULL THEN GROUP_CONCAT(DISTINCT c.code) WHEN b.code IS NULL AND c.code IS NULL THEN '' WHEN b.code IS NOT NULL AND c.code IS NOT NULL THEN 'error' WHEN b.code IS NOT NULL AND c.code IS NULL THEN GROUP_CONCAT(DISTINCT b.code) WHEN b.code IS NOT NULL AND c.code = '' THEN 'error' ELSE '0' END AS supcode FROM b2b_summary.other_doc  a LEFT JOIN b2b_summary.supcus b ON a.customer_guid = b.customer_guid AND a.supcode = b.code AND b.type <> 'C' LEFT JOIN b2b_summary.supcus c ON a.supcode = c.`AccPDebit` AND a.`customer_guid` = c.`customer_guid` AND c.type <> 'C' WHERE a.customer_guid = '$customer_guid' AND a.doctype = '$doctype' $query GROUP BY a.refno,a.doctime) a WHERE (refno like '%" . $search . "%' OR supname like '%" . $search . "%' OR supcode like '%" . $search . "%') ORDER BY $order $dir LIMIT $start,$limit")->result();
                    $totalFiltered = $totalData;
                } else {
                    $posts =  $this->db->query("SELECT * FROM (SELECT IF( STATUS IS NULL || STATUS = '', 'NEW', STATUS ) AS status, refno, supname, doctime, doctype, IF( b.code IS NULL, a.supcode, b.accpdebit ) AS debtor_code, CASE WHEN b.code IS NULL AND c.code IS NOT NULL THEN GROUP_CONCAT(DISTINCT c.code) WHEN b.code IS NULL AND c.code IS NULL THEN '' WHEN b.code IS NOT NULL AND c.code IS NOT NULL THEN 'error' WHEN b.code IS NOT NULL AND c.code IS NULL THEN GROUP_CONCAT(DISTINCT b.code) WHEN b.code IS NOT NULL AND c.code = '' THEN 'error' ELSE '0' END AS supcode FROM b2b_summary.other_doc  a LEFT JOIN b2b_summary.supcus b ON a.customer_guid = b.customer_guid AND a.supcode = b.code AND b.type <> 'C' LEFT JOIN b2b_summary.supcus c ON a.supcode = c.`AccPDebit` AND a.`customer_guid` = c.`customer_guid` AND c.type <> 'C' WHERE a.customer_guid = '$customer_guid' AND a.doctype = '$doctype' AND supcode IN ($supcode) $query GROUP BY a.refno) a WHERE (refno like '%" . $search . "%' OR supname like '%" . $search . "%' OR supcode like '%" . $search . "%') ORDER BY $order $dir LIMIT $start,$limit")->result();
                    // $posts =  $this->db->query("SELECT IF(status is null || status = '','NEW',status) as status,refno,supcode,supname,doctime,doctype FROM $b2b_database.$b2b_table WHERE customer_guid = '$customer_guid' AND doctype = '$doctype' AND supcode IN ($supcode) $query AND (refno like '%".$search."%' OR supname like '%".$search."%' OR supcode like '%".$search."%') ORDER BY $order $dir LIMIT $start,$limit")->result();
                    $totalFiltered = $totalData;
                }

                // $totalFiltered =   $totalData; 
            }

            $data = array();
            if (!empty($posts)) {
                // print_r($posts) ;die;
                // echo var_dump($posts);die;

                foreach ($posts as $post) {

                    if($customer_guid == '599348EDCB2F11EA9A81000C29C6CEB2') // ninso
                    {
                        if ($doctype == 'SDN' || $doctype == 'SIN' || $doctype == 'SVI') 
                        {
                            $get_supcode = $post->debtor_code;
                        }
                        else
                        {
                            $get_supcode = $post->supcode;

                            if ($get_supcode == '') 
                            {
                                $get_supcode = $post->debtor_code;
                            }
                        }
                    }
                    else
                    {
                        $get_supcode = $post->supcode;
                    
                        if ($get_supcode == '') 
                        {
                            $get_supcode = $post->debtor_code;
                        }
                    }
                    
                    $nestedData['refno'] = $post->refno;
                    $nestedData['supcode'] = $post->supcode;
                    $nestedData['debtor_code'] = $post->debtor_code;
                    $nestedData['supname'] = $post->supname;
                    $nestedData['doctime'] = $post->doctime;
                    $nestedData['status'] = $post->status;
                    $nestedData['button'] = "<a href=" . site_url('Panda_rms/rms_child') . "?trans=" . urlencode(str_replace(' ', '%20', $post->refno)) . "&code=" . $doctype . "&supcode=" . $get_supcode . "&title=" . urlencode(str_replace(' ', '%20', $title)) . " style='float:left' class='btn-sm btn-info' role='button'><span class='glyphicon glyphicon-eye-open'></span></a>
                              ";
                    $nestedData['box'] = '<input type="checkbox" class="data-check" value="' . $post->refno . '">';


                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data
            );

            echo json_encode($json_data);
        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function rms_child()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login()) {

            $this->panda->get_uri();
            $refno = urldecode($_REQUEST['trans']);
            $doctype = urldecode($_REQUEST['code']);
            $supcode = urldecode($_REQUEST['supcode']);

            $customer_guid = $_SESSION['customer_guid'];
            $user_guid = $_SESSION['user_guid'];

            if ($supcode != '' && $supcode != 'null' && $supcode != null) {
                $add_where_supcode = "AND supcode = '$supcode' ";
            } else {
                $add_where_supcode = "";
            }

            $status = $this->db->query("SELECT IF(status is null || status = '','NEW',status) as status,supcode FROM b2b_summary.other_doc WHERE customer_guid = '$customer_guid' AND doctype = '$doctype' AND refno = '$refno' AND supcode = '$supcode'");

            $doc_mapping = $this->db->query("SELECT * FROM b2b_summary.other_doc_mapping WHERE doctype = '$doctype' AND cross_refno = '$refno' AND cross_supcode = '$supcode'");

            $file_config_main_path = $this->file_config_b2b->file_path_name($customer_guid, 'web', 'rms_doc', 'main_path', 'RMS');

            if (!in_array('!VODSUPPMOV', $_SESSION['module_code'])) {

                $this->db->query("UPDATE b2b_summary.other_doc set status = 'viewed' where customer_guid ='$customer_guid' and refno = '$refno' and status = '' AND doctype = '$doctype' AND supcode = '$supcode' ");

                $this->db->query("REPLACE into supplier_movement select 
                upper(replace(uuid(),'-','')) as movement_guid
                , '$customer_guid'
                , '$user_guid'
                , 'viewed_$doctype-$supcode'
                , 'other_doc'
                , '$refno'
                , now()
                ");
            };

            $main_path = $file_config_main_path;
            if (!file_exists($main_path)) {

                mkdir($main_path, 0777);
                chmod($main_path, 0777);
            }

            $data = array(
                'title' => $this->input->get('title'),
                'refno' => $refno,
                'doctype' => $doctype,
                'supcode' => $supcode,
                'status' => $status->row('status'),
                'doc_mapping' => $doc_mapping->result_array(),
            );

            $this->load->view('header');
            $this->load->view('rms/rms_pdf', $data);
            $this->load->view('footer');
        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function readfile()
    {
        $customer_guid = $this->session->userdata('customer_guid');
        $refno = urldecode($_REQUEST['refno']);
        $code = urldecode($_REQUEST['code']);
        $supcode = urldecode($_REQUEST['supcode']);
        $user_guid = $this->session->userdata('user_guid');

        if(isset($_GET['trans'])){
            $trans = $_GET['trans'];
            $cross_ref = "cross_refno = '$trans' AND ";
        }else{
            $cross_ref = "";
        }

        $filename = $this->db->query("SELECT * FROM b2b_summary.`other_doc_mapping` WHERE $cross_ref file_refno = '$refno' AND doctype = '$code' AND cross_supcode = '$supcode'");

        $file_server = $this->file_config_b2b->file_path_name($customer_guid, 'web', 'file_server', 'main_path', 'FILESERVER');
        $directory = $this->file_config_b2b->file_path_name($customer_guid, 'web', 'rms_doc', 'main_path', 'RMS');

        // print_r($directory); die;

        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== 'uploads') {
                $file_name = str_replace('.pdf', '', $file);
  
                if(trim($file_name) == trim($filename->row('file_refno'))){
                    // Set the appropriate headers
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; filename="file.pdf"');
                    header('Content-Length: ' . filesize($directory.$file));

                    $directory = ltrim($directory, '/');    
                    readfile($file_server.rtrim(str_replace('media/', '', $directory), '/').'/'.$file);
                }
            }
        }
    }

    public function rms_filter()
    {
        $refno = $this->input->post('other_doc_refno');
        $status = $this->input->post('other_doc_status');
        $datetime = $this->input->post('other_doc_datetime');
        $code = $_REQUEST['code'];

        redirect(site_url('Panda_rms') . '?code=' . $code . '&refno=' . $refno . '&status=' . $status . '&datetime=' . $datetime);
        // print_r($this->input->post());die;
    }

    public function read_64()
    {
        $customer_guid = $this->session->userdata('customer_guid');
        $refno = urldecode($_REQUEST['refno']);
        $code = urldecode($_REQUEST['code']);
        $supcode = urldecode($_REQUEST['supcode']);
        $user_guid = $this->session->userdata('user_guid');

        if(isset($_GET['trans'])){
            $trans = $_GET['trans'];
            $cross_ref = "cross_refno = '$trans' AND ";
        }else{
            $cross_ref = "";
        }

        $filename = $this->db->query("SELECT * FROM b2b_summary.`other_doc_mapping` WHERE $cross_ref file_refno = '$refno' AND doctype = '$code' AND cross_supcode = '$supcode'");

        $directory = $this->file_config_b2b->file_path_name($customer_guid, 'web', 'rms_doc', 'main_path', 'RMS');

        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== 'uploads') {
                $file_name = str_replace('.pdf', '', $file);

                if($file_name == $filename->row('file_refno')){
                    // Set the appropriate headers
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; filename="'.$file.'"');
                    header('Content-Length: ' . filesize($directory.$file));

                    // Output the PDF file
                    readfile($directory.$file);
                }
            }
        }
    }

    public function direct_print_merge()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login()) {
            $this->panda->get_uri();
            // $refno = $_REQUEST['trans'];
            // $loc = $_REQUEST['loc'];
            // $customer_guid = $_SESSION['customer_guid'];
            // $user_guid = $_SESSION['user_guid'];
            // $from_module = $_SESSION['frommodule'];
            $pdf_name = $_REQUEST['pdfname'];
            $action = $_REQUEST['action'];
            $file_config_final_path = $this->file_config_b2b->file_path_name($this->session->userdata('customer_guid'), 'web', 'general_doc', 'merge_print_acc_doc', 'ACCMRGPRT');
            $file = $file_config_final_path . '/' . $pdf_name . '.pdf';
            //$file = 'merge/'.$pdf_name.'.pdf'; // generate the file

            // $file = $to_location.'/'.$to_location2.'/'.$filename.'.pdf'; 
            // echo $action;die;
            // $filename =$filename.'.pdf'; 
            if ($action == 'print') {
                $type = 'inline';
            } elseif ($action == 'download') {
                $type = 'attachment';
            } else {
                $type = 'attachment';
            }
            // echo $type;die;
            $b64Doc = chunk_split(base64_encode(file_get_contents($file)));
            // echo $b64Doc;
            $pdf_b64 = base64_decode($b64Doc);
            // $pdf_b64 = base64_decode(str_replace('\r\n', '', $b64Doc));
            date_default_timezone_set("Asia/Kuala_Lumpur");
            $pdf_name1 = 'Accounting_document_' . date('d-m-Y_H:i:s');
            unlink($file_config_final_path . '/' . $pdf_name . '.pdf');
            header("Content-type: application/pdf");
            header('Content-Disposition: ' . $type . '; filename="' . $pdf_name1 . '.pdf"');
            echo $pdf_b64;
            die;
        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function upload_acc_excel()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login()) {
            // echo 1;die;
            $this->panda->get_uri();
            $customer_guid = $this->session->userdata('customer_guid');
            // $refno = $_REQUEST['trans'];
            // $loc = $_REQUEST['loc'];
            // $customer_guid = $_SESSION['customer_guid'];
            // $user_guid = $_SESSION['user_guid'];
            // $from_module = $_SESSION['frommodule'];
            $drop_down = $this->db->query("SELECT * FROM lite_b2b.other_doc_setting WHERE customer_guid = '$customer_guid' ORDER BY seq ASC");
            $supcode = $this->db->query("SELECT * FROM lite_b2b.set_supplier_group a INNER JOIN b2b_summary.supcus b ON a.supplier_group_name = b.code AND b.customer_guid = '$customer_guid' WHERE a.customer_guid = '$customer_guid' ORDER BY a.supplier_group_name ASC");
            // echo $this->db->last_query();die;
            // print_r($supcode->result());die;
            $data = array(
                'drop_down' => $drop_down,
                'supcode' => $supcode,
            );
            // echo $this->db->last_query();
            $this->load->view('header');
            $this->load->view('rms/upload_rms', $data);
            $this->load->view('footer');

            // print_r($drop_down);die;

        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }



    public function upload_excel_file_acc_doc()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');


        $refno = $this->input->post('other_doc_refno');
        $supcode = $this->input->post('supcode');
        $doctype = $this->input->post('doctype');
        $customer_guid = $_SESSION['customer_guid'];
        $file_config_main_path = $this->file_config_b2b->file_path_name($customer_guid, 'web', 'acc_doc', 'main_path', 'ACC');
        $file_path = $this->db->query("SELECT file_path FROM acc WHERE acc_guid = '$customer_guid'")->row('file_path');
        $path2 = $file_path;
        // print_r($this->input->post());die;
        // echo $_FILES["file"]["tmp_name"];die;
        $defined_path = $file_config_main_path . $path2 . '/' . $doctype; //'./acc_doc/uploads/tfvaluemart/'
        $file_uuid = $doctype . '_' . $supcode . '_' . $refno;
        // echo $file_uuid;die;
        $config['upload_path'] = $defined_path;
        $config['allowed_types'] = '*';
        $config['max_size'] = 50000;
        $config['file_name'] = $file_uuid;
        // echo $defined_path;die;
        // var_dump( $this->input->post('file') );die; 
        // print_r($this->input->post());die;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            // print_r($this->upload->display_errors());die;
            $error = array('error' => $this->upload->display_errors());

            if (null != $error) {
                // $error_message = $error['error'];
                // echo $error_message;die;
                // $xerror = $this->db->error();
                // $xerror['message'] = ($xerror['message'] == '') || ($xerror['message'] == null) ? $error_message : $xerror['message'];
                // $this->message->error_message($xerror['message'], '1');
                // exit();
            } //close else

        } else {
            $data = array('upload_data' => $this->upload->data());

            // print_r($_FILES["file"]);

            $filename = $defined_path . $data['upload_data']['file_name'];

            //  Include PHPExcel_IOFactory
            $this->load->library('Excel');

            $inputFileName = $filename;

            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {

                //   $error_message = $this->lang->line('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                // $xerror = $this->db->error();
                // $xerror['message'] = ($xerror['message'] == '') || ($xerror['message'] == null) ? $error_message : $xerror['message'];
                // $this->message->error_message_with_status($xerror['message'], '1', '');
                // exit();

            }

            $this->session->set_flashdata('message', 'File Uploaded');
            redirect(site_url('Panda_rms/upload_acc_excel'));
            // unlink($filename);

        }

        // return $objPHPExcel;

    }

    public function old_upload_document(){

        $customer_guid = $this->session->userdata('customer_guid');
        $uploadDirectory = $this->file_config_b2b->file_path_name($customer_guid, 'web', 'rms_doc', 'main_path', 'RMS');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
        
            // Check if the directory exists, if not, create it
            if (!file_exists($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }
        
            // Generate a unique filename to avoid overwriting existing files
            $uploadFilePath = $uploadDirectory . '/' . $_FILES['file']['name'];
        
            // Move the uploaded file to the destination
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath)) {

                $response = array(
                    'status'    => true,
                    'message'   => 'File uploaded successfully',
                );

            } else {
                
                $response = array(
                    'status'    => false,
                    'message'   => 'File upload failed',
                );

            }
        } else {

            $response = array(
                'status'    => false,
                'message'   => 'Invalid request',
            );

        }

        echo json_encode($response);
    }

    public function upload_document(){

        $customer_guid = $this->session->userdata('customer_guid');
        $uploadDirectory = $this->file_config_b2b->file_path_name($customer_guid, 'web', 'rms_doc', 'main_path', 'RMS');
        $fileData = file_get_contents('php://input');
        $originalFilename = $this->input->get('filename');
        $retailer = $this->input->get('retailer');
        $supcode = $this->input->get('$supcode');
        $doctype = $this->input->get('$doctype');
        // $originalFilename = $this->input->get_request_header('Original-Filename');

        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }
        
        $uploadFilePath = $uploadDirectory . '/' . $retailer . '/' . $supcode . '/' . $doctype . '/' . $originalFilename;

        if (file_put_contents($uploadFilePath.'.pdf', $fileData) !== false) {

            $response = array(
                'status'    => true,
                'message'   => 'File uploaded successfully',
            );

        } else {
                
            $response = array(
                'status'    => false,
                'message'   => 'File upload failed',
            );

        }

        echo json_encode($response);
    }
}
