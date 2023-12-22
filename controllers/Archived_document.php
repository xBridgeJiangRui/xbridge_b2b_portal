<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Archived_document extends CI_Controller {
    
    // this function is for bulk update/insert database, if 1 got error, then it will rollback all the changes made before
    // $this->db->trans_start();
    // do update or insert here
    // $this->db->trans_complete();

    // if ($this->db->trans_status() === FALSE) {
    //     echo "Background process encountered an error";
    // } else {
    //     echo "Background process completed";
    // }

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper(array('form', 'url'));
        $this->load->database();
        $this->load->library('pagination');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('datatables');
        $this->load->library('Panda_PHPMailer');
        $this->load->library('excel');

        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);

        // $_SESSION['user_group_name'] = 'SUPER_ADMIN';
        // $_SESSION['customer_guid'] = '8D5B38E931FA11E79E7E33210BD612D3';
    }

    public function test(){
        $doc_refno = 'PDNPDsN22100001';
        $wordToFind = 'PDN';

        if (strrpos($doc_refno, $wordToFind) !== false && strrpos($doc_refno, $wordToFind) != 0) {
            echo "The word '$wordToFind' was found in the string.";
            echo strrpos($doc_refno, $wordToFind);
        } else {
            echo "The word '$wordToFind' was not found in the string.";
        } die;

        // The array of substrings to search in
        $substringArray = ["app", "ann", "ran", "gra", "xam"];

        // The word to check for
        $wordToFind = 'example';

        // Check if $wordToFind is in any of the substrings in the array
        $foundInArray = false;

        foreach ($substringArray as $substring) {
            if (strpos($wordToFind, $substring) !== false) {
                echo $substring;
                $foundInArray = true;
                break;
            }
        }

        if ($foundInArray) {
            echo "The word '$wordToFind' contains one of the substrings in the array.";
        } else {
            echo "The word '$wordToFind' does not contain any of the substrings in the array.";
        }
    }

    public function send_email_notification($req_refno){

        $customer_guid = $this->session->userdata('customer_guid');

        $request_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE request_refno = '$req_refno' GROUP BY request_refno");
        $req_guid = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE request_refno = '$req_refno' GROUP BY request_refno")->row('request_guid');
        $doc_status = $request_info->row('status');
        $doc_requestor = $request_info->row('requested_by');
        $doc_action = ($doc_status == 'SUBMITTED') ? 'created' : 'updated';

        $to_shoot_url = 'https://api.xbridge.my'; // nabil testing
        $to_shoot_url = rtrim($to_shoot_url, '/');
        $email_template = $this->db->query("SELECT template_guid FROM lite_b2b.email_template WHERE template_guid = 'BL804ARCHIEVEDOCUMENTNOTIFY894G6'")->row('template_guid');

        if($email_template == ''){

            $insert_data = array(
                'template_guid' => 'BL804ARCHIEVEDOCUMENTNOTIFY894G6',
                'type'          => 'ARCHIVE_DOC',
                'mail_type'     => 'Archive_doc',
                'description'   => 'Archive document request notification',
                'mail_subject'  => 'Action Required : Document Request #%req_refno% - %doc_status%',
                'body_header'   => NULL,
                'body_content'  => '<tr>\r\n                <td class=\"h2\">\r\n                  xBridge B2B Portal<br><br>\r\n                  Archive Document Request (%req_refno%)\r\n                </td>\r\n              </tr>\r\n              <td class=\"bodycopy\">\r\n\r\n                  We would like to inform you that request reference no <b>%req_refno%</b> has been <b>%doc_action%</b>. Current status for this request is <b>%doc_status%</b>. You may <a href=\"%redirect_url%\" target=\"_blank\">click here</a> to access our portal to view the detailed information of the document.<br><br>\r\n\r\n                  Please contact xBridge Support Team at support@xbridge.my or call us +60177451185 / +60177159340 if you required any assistance.',
                'body_footer'   => NULL,
                'remark'        => NULL,
                'is_active'     => 1,
                'is_replace'    => 1,
                'is_editable'   => 1,
                'created_at'    => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
                'created_by'    => 'system',
                'updated_at'    => NULL,
                'updated_by'    => NULL,
            );

            $this->db->insert('lite_b2b.email_template',$insert_data);

            $email_template = $this->db->query("SELECT template_guid FROM lite_b2b.email_template WHERE template_guid = 'BL804ARCHIEVEDOCUMENTNOTIFY894G6'")->row('template_guid');
        }

        if($doc_status != 'REVIEWED'){
            $to_receive = ['support@xbridge.my'];

            // nabil testing
            $to_receive = ['xytai@xbridge.my'];
        }else{
            $to_receive = $this->db->query("SELECT user_id FROM lite_b2b.set_user WHERE user_name = '$doc_requestor' AND acc_guid = '$customer_guid'")->result_array();

            // nabil testing
            $to_receive = ['nabil.haziq@pandasoftware.my'];
        }

        // nabil testing
        $to_receive = ['nabil.haziq@pandasoftware.my'];

        foreach ($to_receive as $email) {
            $email_list[] = array('email_address' => $email);
        }

        $json_param = array(
            '%req_refno%'       => $req_refno,
            '%doc_status%'      => $doc_status,
            '%doc_action%'      => $doc_action,
            '%redirect_url%'    => site_url("Archived_document?guid=").$req_guid,
        );

        $post_data[] = array(
            'customer_guid' => $customer_guid,
            'template_guid' => $email_template,
            'supplier_code' => '',
            'email_address' => $email_list,
            'email_type'    => 'archived_document',
            'status'        => '1',
            'json_param'    => $json_param
        );

        // $test = json_encode($post_data);
        // print_r(json_decode($test)); die;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $to_shoot_url.'/rest_b2b/index.php/Blast_email_process/process_email_list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // print_r($response); die;

        // return json_decode($response, true);
    }

    public function index()
    {   
        $customer_guid = $this->session->userdata('customer_guid');
        $request_guid = isset($_GET['guid']) ? $_GET['guid'] : '';
        $user_guid = $this->session->userdata('user_guid');

        $supplier_info = $this->db->query("SELECT `supplier_group_guid` FROM set_supplier_user_relationship WHERE user_guid = '$user_guid' AND customer_guid = '$customer_guid' GROUP BY `supplier_group_guid`")->result_array();
        $supplier_list = array();
        $supplier_filter = array();
        $supplier_filter_query = '';

        if($request_guid != ''){
            $supplier_info = $this->db->query("SELECT `requested_by_supplier` FROM lite_b2b.archived_document WHERE request_guid = '$request_guid' AND customer_guid = '$customer_guid' LIMIT 1")->row('requested_by_supplier');

            $array_supplier_info = json_decode($supplier_info, true);

            $supplier_info = array_map(function($item) {
                return ['supplier_group_guid' => $item];
            }, $array_supplier_info);

        }

        foreach($supplier_info as $value){

            if($value['supplier_group_guid'] != '' && $value['supplier_group_guid'] != null && $value['supplier_group_guid'] != 'null'){

                $supplier_guid = $value['supplier_group_guid'];
                $supplier_code = $this->db->query("SELECT `supplier_group_name` FROM set_supplier_group WHERE supplier_group_guid = '$supplier_guid' AND customer_guid = '$customer_guid'")->result_array();

                $supplier_filter[] = "requested_by_supplier LIKE '%$supplier_guid%'";
                
                foreach($supplier_code as $code){
                    $supplier_list[] = $code['supplier_group_name'];
                }

            }
        }

        if (!empty($supplier_filter) && $_SESSION['user_group_name'] != 'SUPER_ADMIN') {
            $supplier_filter_query = " OR " . implode(" OR ", $supplier_filter);
            $supplier_filter_query = ' AND (' . substr($supplier_filter_query, 3) . ')';
        }

        $retailer = $this->db->query("SELECT acc_name FROM lite_b2b.acc WHERE acc_guid = '$customer_guid'")->row('acc_name');

        $doc_type = [
            'PO',
            'GRN',
            'GRDA',
            'PRDN',
            'PRCN',
            'PDN',
            'PCN',
            'PCI',
            'DI',
            'SI',
            'STRB',
            'ACC'
        ];

        $pricing_type = $this->db->query("SELECT * FROM b2b_invoice.invoice_external_setting ORDER BY sorting ASC");
        $pending_results = $this->db->query("SELECT *, COUNT(*) AS total_doc FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND `status` = 'NEW' $supplier_filter_query GROUP BY request_refno ORDER BY requested_at DESC");
        $complete_results = $this->db->query("SELECT ad.*, SUM(ies.amount) AS total_price, COUNT(*) AS total_doc FROM lite_b2b.archived_document ad LEFT JOIN b2b_invoice.invoice_external_setting ies ON ad.pricing_type = ies.doc_type WHERE ad.customer_guid = '$customer_guid' AND ad.`status` <> 'NEW' $supplier_filter_query GROUP BY ad.request_refno ORDER BY ad.requested_at DESC");
        $complete_by_guid_results = $this->db->query("SELECT ad.*, SUM(ies.amount) AS total_price, COUNT(*) AS total_doc, COUNT(CASE WHEN (ad.doc_type IS NULL OR (ad.json_report IS NULL AND ad.file_path IS NULL)) THEN 1 END) AS missing_doc, COUNT(CASE WHEN (ad.doc_type IS NOT NULL AND (ad.json_report IS NOT NULL OR ad.file_path IS NOT NULL) AND ad.pricing_type = '') THEN 1 END) AS blocked_doc FROM lite_b2b.archived_document ad LEFT JOIN b2b_invoice.invoice_external_setting ies ON ad.pricing_type = ies.doc_type WHERE ad.customer_guid = '$customer_guid' AND ad.request_guid = '$request_guid' $supplier_filter_query GROUP BY ad.request_refno ORDER BY ad.requested_at DESC");

        if($_SESSION['user_group_name'] == 'SUPER_ADMIN'){
            $complete_child_by_guid_results = $this->db->query("SELECT ad.*,ssg.`supplier_guid` FROM lite_b2b.archived_document ad LEFT JOIN lite_b2b.`set_supplier_group` ssg ON ad.`SCode` = ssg.`supplier_group_name` AND ad.customer_guid = ssg.customer_guid WHERE ad.customer_guid = '$customer_guid' AND ad.request_guid = '$request_guid' $supplier_filter_query GROUP BY ad.`request_refno`, ad.`doc_refno` ORDER BY ad.doc_type ASC");
        }else{
            $complete_child_by_guid_results = $this->db->query("SELECT ad.*,ssg.`supplier_guid` FROM lite_b2b.archived_document ad LEFT JOIN lite_b2b.`set_supplier_group` ssg ON ad.`SCode` = ssg.`supplier_group_name` AND ad.customer_guid = ssg.customer_guid WHERE ad.customer_guid = '$customer_guid' AND ad.request_guid = '$request_guid' $supplier_filter_query GROUP BY ad.`request_refno`, ad.`doc_refno` ORDER BY ad.pricing_type DESC");
        }

        $requested_by_supplier = json_decode($complete_by_guid_results->row('requested_by_supplier'), true);
        $requested_supplier = array();

        // echo sizeof($requested_by_supplier); die;

        if(sizeof($requested_by_supplier) > 0){
            $values = explode(",", trim($complete_by_guid_results->row('requested_by_supplier'), "[]"));
            $result = "(" . implode(",", $values) . ")";
        }

        foreach($requested_by_supplier as $guid){
            
            $supcode_array = $this->db->query("SELECT supplier_group_name FROM lite_b2b.set_supplier_group WHERE supplier_group_guid = '$guid' AND customer_guid = '$customer_guid'")->result_array();

            foreach($supcode_array as $supcode){
                $requested_supplier[] = $supcode['supplier_group_name'];
            }
        }

        $requested_supplier_array = $requested_supplier;
        $requested_supplier = implode(",", $requested_supplier);

        $live_date = null;
        $check_portal_live_date = $this->db->query("SELECT COUNT(*) AS result FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'lite_b2b' AND TABLE_NAME = 'acc_settings' AND COLUMN_NAME = 'portal_live_date'")->row('result');

        if($check_portal_live_date != '0'){
            $live_date = $this->db->query("SELECT portal_live_date FROM lite_b2b.acc_settings WHERE customer_guid = '$customer_guid'")->row('portal_live_date');
        }

        $data = array(
            'live_date'                 => $live_date,
            'retailer'                  => $retailer,
            'supplier'                  => $supplier_list,
            'doc_type'                  => $doc_type,
            'requested_supplier'        => $requested_supplier,
            'requested_supplier_array'  => $requested_supplier_array,
            'pricing_type'              => $pricing_type->result_array(),
            'pending_list'              => $pending_results->result_array(),
            'complete_list'             => $complete_results->result_array(),
            'header_list'               => $complete_by_guid_results->result_array(),
            'child_list'                => $complete_child_by_guid_results->result_array(),
        );

        // print_r("SELECT *, COUNT(*) AS total_doc FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' GROUP BY request_refno ORDER BY requested_at DESC"); die;

        $this->load->view('header');
        $this->load->view('archived_document/request_document_record', $data);  
        $this->load->view('footer' );  
    }

    public function update_doctype()
    {   
        $req_guid = $this->input->post('req_guid');
        $rowData = $this->input->post('rowData');
        $customer_guid = $this->session->userdata('customer_guid');
        $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');

        foreach($rowData as $row){

            $doc_guid = $row['guid'];

            $current_doctype = $this->db->query("SELECT doc_type FROM lite_b2b.archived_document WHERE guid = '$doc_guid'")->row('doc_type');
            $doc_type = $row['doc_type'] != '-' ? $row['doc_type'] : '';

            $data = array(
                'doc_type'      => $doc_type,
            );

            if($current_doctype != $doc_type){
            //     $data['hq_sync'] = 1;
                $data['json_report'] = null;
            }
    
            $this->db->where('guid', $row['guid']);
            $this->db->update('lite_b2b.archived_document', $data);

            if($current_doctype != $doc_type){
                $this->retrieve_from_HQ($row['guid']);
            }
        }

        $updated_total = $this->db->query("SELECT SUM(ies.amount) AS total_price FROM lite_b2b.archived_document ad INNER JOIN b2b_invoice.invoice_external_setting ies ON ad.pricing_type = ies.doc_type WHERE ad.customer_guid = '$customer_guid' AND ad.request_refno = '$req_refno' GROUP BY ad.request_refno")->row('total_price');

        $response = array(
            'status'        => true,
            'message'       => 'Successfully Update',
            'updated_total' => $updated_total
        );

        echo json_encode($response); die;
    }

    public function update_pricing()
    {   
        $req_guid = $this->input->post('req_guid');
        $rowData = $this->input->post('rowData');
        $customer_guid = $this->session->userdata('customer_guid');
        $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');

        foreach($rowData as $row){

            $data = array(
                'pricing_type' => $row['pricing_type'],
            );
    
            $this->db->where('guid', $row['guid']);
            $this->db->update('lite_b2b.archived_document', $data);
        }

        $updated_total = $this->db->query("SELECT SUM(ies.amount) AS total_price FROM lite_b2b.archived_document ad INNER JOIN b2b_invoice.invoice_external_setting ies ON ad.pricing_type = ies.doc_type WHERE ad.customer_guid = '$customer_guid' AND ad.request_refno = '$req_refno' GROUP BY ad.request_refno")->row('total_price');

        $response = array(
            'status'        => true,
            'message'       => 'Successfully Update',
            'updated_total' => $updated_total
        );

        echo json_encode($response); die;
    }

    public function request_document()
    {   
        $customer_guid = $this->session->userdata('customer_guid');
        $user_guid = $this->session->userdata('user_guid');

        if(isset($_GET['guid'])){
            $req_guid = $_GET['guid'];
            $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');
        }else{
            $req_refno = '';
        }

        if($this->session->userdata('loginuser') == true)
        {

            $data = array(
                'customer_guid'     => $customer_guid,
                'req_refno'         => $req_refno,
                'username'          => $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE acc_guid = '$customer_guid' AND user_guid = '$user_guid' LIMIT 1")->row('user_name'),
            );

            $this->load->view('header'); 
            $this->load->view('archived_document/request_document', $data);  
            $this->load->view('footer' );  
        }
        else
        {
            redirect('#');
        }
    }

    public function request_document_listing()
    {   
        $req_refno = $this->input->post('req_refno');

        $result = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE request_refno = '$req_refno' ORDER BY requested_at DESC")->result_array();

        echo json_encode($result);
    }

    public function document_details()
    {
        $guid = $this->input->post('guid');
        $html = '';

        $doc_detail = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE guid = '$guid' LIMIT 1")->result_array();
        $doc_detail = $doc_detail[0];

        $req_refno = $doc_detail['request_refno'];
        $customer_guid = $doc_detail['customer_guid'];
        $doc_refno = $doc_detail['doc_refno'];
        $requested_at = $doc_detail['requested_at'];
        $username = $doc_detail['requested_by'];

        $retailer_name = $this->db->query("SELECT acc_name FROM lite_b2b.acc WHERE acc_guid = '$customer_guid'")->row('acc_name');

        $html .='<div class="row">';
        
        $html .= '<div class="col-md-6"> <label>Retailer</label><input value="'.$retailer_name.'" type="text" id="customer" name="customer" class="form-control" readonly > </div>';

        $html .= '<div class="col-md-6"> <label>Request Ref No</label><input value="'.$req_refno.'" type="text" id="req_refno" name="req_refno" class="form-control" readonly > </div>';

        $html .= '<div class="col-md-6"> <label>Requested By</label><input value="'.$username.'" type="text" id="requested_by" name="requested_by" class="form-control" readonly > </div>';

        $html .= '<div class="col-md-6"> <label>Requested At</label><input value="'.$requested_at.'" type="text" id="requested_at" name="requested_at" class="form-control" readonly > </div>';

        $html .= '<div class="col-md-6"> <label>Doc Ref No</label><input value="'.$doc_refno.'" type="text" id="doc_refno" name="doc_refno" class="form-control" > </div>';

        // $html .= '<div class="col-md-6"> <label>Doc Type</label>';
        // $html .= ' <select id="doc_type" name="doc_type" class="form-control">';
        // $html .= '  <option value="">-Select Document Type-</option>';

        // if($doc_type == 'PO'){
        //     $html .= '  <option value="PO" selected>Purchase Order (PO)</option>';
        // }else{
        //     $html .= '  <option value="PO">Purchase Order (PO)</option>';
        // }

        // if($doc_type == 'GRN'){
        //     $html .= '  <option value="GRN" selected>Goods Received Note (GRN)</option>';
        // }else{
        //     $html .= '  <option value="GRN">Goods Received Note (GRN)</option>';
        // }

        // if($doc_type == 'GRDA'){
        //     $html .= '  <option value="GRDA" selected>Goods Received Diff Advice (GRDA)</option>';
        // }else{
        //     $html .= '  <option value="GRDA">Goods Received Diff Advice (GRDA)</option>';
        // }

        // if($doc_type == 'PRDN'){
        //     $html .= '  <option value="PRDN" selected>Purchase Return DN (PRDN)</option>';
        // }else{
        //     $html .= '  <option value="PRDN">Purchase Return DN (PRDN)</option>';
        // }

        // if($doc_type == 'PRCN'){
        //     $html .= '  <option value="PRCN" selected> Purchase Return CN (PRCN)</option>';
        // }else{
        //     $html .= '  <option value="PRCN"> Purchase Return CN (PRCN)</option>';
        // }

        // if($doc_type == 'PDN'){
        //     $html .= '  <option value="PDN" selected>Purchase DN/CN (PDN)</option>';
        // }else{
        //     $html .= '  <option value="PDN">Purchase DN/CN (PDN)</option>';
        // }

        // if($doc_type == 'PCI'){
        //     $html .= '  <option value="PCI" selected>Promotion Claim Tax Invoice (PCI)</option>';
        // }else{
        //     $html .= '  <option value="PCI">Promotion Claim Tax Invoice (PCI)</option>';
        // }

        // if($doc_type == 'DI'){
        //     $html .= '  <option value="DI" selected>Display Incentive (DI)</option>';
        // }else{
        //     $html .= '  <option value="DI">Display Incentive (DI)</option>';
        // }
        
        // $html .= ' </select> </div>';

        $html .= '<div class="col-md-12"><input value="'.$guid.'" type="hidden" id="guid" name="guid" class="form-control" > </div>';

        $html .= '</div>';

        echo $html;
    }

    public function add_document()
    {   
        $customer_guid = $this->session->userdata('customer_guid');
        $user_guid = $this->session->userdata('user_guid');
        $req_refno = $this->input->post('req_refno');
        $refno = $this->input->post('refno');

        $check_doc_exist = $this->db->query("SELECT * FROM archived_document WHERE request_refno = '$req_refno' AND customer_guid = '$customer_guid' LIMIT 1");

        if(sizeof($check_doc_exist->result_array()) > 0){
            $request_guid = $check_doc_exist->row('request_guid');
        }else{
            $request_guid = $this->db->query("SELECT REPLACE(UPPER(UUID()),'-','') AS uuid")->row('uuid');
        }        
        
        $supplier_info = $this->db->query("SELECT `supplier_group_guid` FROM set_supplier_user_relationship WHERE user_guid = '$user_guid' AND customer_guid = '$customer_guid' GROUP BY `supplier_group_guid`")->result_array();
        $supplier_list = array();

        foreach($supplier_info as $value){

            if($value['supplier_group_guid'] != '' && $value['supplier_group_guid'] != null && $value['supplier_group_guid'] != 'null'){
                $supplier_list[] = $value['supplier_group_guid'];
            }
        }

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');
        $checking = $this->db->query("SELECT COUNT(*) AS cnt FROM lite_b2b.archived_document WHERE request_refno = '$req_refno' AND customer_guid = '$customer_guid' AND doc_refno = '$refno'")->row('cnt');

        if($checking > 0){

            $response = array(
                'status'    => false,
                'req_refno' => $req_refno,
                'message'   => 'Doc Ref No ('.$refno.') already being requested'
            );

            echo json_encode($response); die;
        }

        if($req_refno == ''){
            $this->db->query("SET @orderdate = CURDATE();");
            $this->db->query("SET @maxno = IFNULL((SELECT MAX(RIGHT(request_refno,4)) FROM lite_b2b.archived_document WHERE LEFT(DATE(requested_at),7) = LEFT(@orderdate,7)),'0000');");
            $this->db->query("SET @month = DATE_FORMAT(NOW(), '%y%m');");
            $this->db->query("SET @refno = CONCAT('RD',@month,CONCAT(IF(@maxno<9,'000',IF(@maxno<99,'00',IF(@maxno<999,'0',''))),@maxno+1));");		
            
            $req_refno = $this->db->query("SELECT @refno AS req_id;")->row('req_id');
        }

        $data = array(
            'guid'                  => $this->db->query("SELECT REPLACE(UPPER(UUID()),'-','') AS uuid")->row('uuid'),
            'request_guid'          => $request_guid,
            'request_refno'         => $req_refno,
            'customer_guid'         => $customer_guid,
            'doc_refno'             => $refno,
            'requested_by_supplier' => json_encode($supplier_list),
            'requested_at'          => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
            'requested_by'          => $username,
        );
    
        $this->db->insert('lite_b2b.archived_document',$data);

        $affectedRows = $this->db->affected_rows();

        if ($affectedRows > 0) {

            $response = array(
                'status'    => true,
                'req_refno' => $req_refno,
                'message'   => 'Successfully Added'
            );

        } else {
            
            $response = array(
                'status'    => false,
                'req_refno' => $req_refno,
                'message'   => 'Fail to Add, Please try again'
            );

        }

        echo json_encode($response);

    }

    public function edit_document()
    {   
        $guid = $this->input->post('guid');
        $req_refno = $this->input->post('req_refno');
        $doc_refno = $this->input->post('doc_refno');
        $req_guid = $this->db->query("SELECT request_guid FROM lite_b2b.archived_document WHERE request_refno = '$req_refno' LIMIT 1")->row('request_guid');

        $data = array(
            'doc_refno' => $doc_refno,
        );

        $this->db->where('guid', $guid);
        $this->db->update('lite_b2b.archived_document', $data);

        $affectedRows = $this->db->affected_rows();

        if ($affectedRows > 0) {

            echo "<script> alert('Successfully Edit');</script>";
            echo "<script> document.location='" . base_url() . "index.php/Archived_document/request_document?guid=".$req_guid."' </script>";

        } else {

            echo "<script> alert('Fail to Update, Please try again');</script>";
            echo "<script> document.location='" . base_url() . "index.php/Archived_document/request_document?guid=".$req_guid."' </script>";

        }

    }

    public function delete_document()
    {   
        $guid = $this->input->post('guid');

        $this->db->query("DELETE FROM lite_b2b.archived_document WHERE guid = '$guid'");

        $affectedRows = $this->db->affected_rows();

        if ($affectedRows > 0) {

            $response = array(
                'status'    => true,
                'message'   => 'Successfully Removed'
            );

        } else {
            
            $response = array(
                'status'    => false,
                'message'   => 'Fail to Remove, Please try again'
            );

        }

        echo json_encode($response);

    }

    public function export_excel()
    {   
        ini_set('max_execution_time', 0); 

        $req_guid = isset($_GET['guid']) && $_GET['guid'] != '' ? $_GET['guid'] : 'template';
        $customer_guid = $this->session->userdata('customer_guid');
        $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');
        $doctype_list = "PO, GRN, GRDA, PRDN, PRCN, PDN, PCI, DI, SI, STRB, ACC";
        $doctype_options = ['PO', 'GRN', 'GRDA', 'PRDN', 'PRCN', 'PDN', 'PCI', 'DI', 'SI', 'STRB', 'ACC'];
        $uppercase_options = array_map('strtoupper', $doctype_options);
        $doctype_list = implode(',', $uppercase_options);

        if($req_guid != 'template'){
            $q_result = $this->db->query("SELECT doc_refno AS 'Document Ref No' FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_refno = '$req_refno'")->result_array();
        }else{

            
                $q_result[] = array(
                    'Document Ref No' => '',
                );
            
        }

        for ($i = sizeof($q_result); $i < 40; $i++) {
            $q_result[$i] = array(
                'Document Ref No' => '',
            );
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $set_paramater_header = array();

        foreach($q_result[0] as $xheader => $xrow)
        {
            $set_paramater_header[] = $xheader;
            continue;
        }

        $x = 'A';

        foreach($set_paramater_header AS $header_name)
        {
            $objPHPExcel->getActiveSheet()->SetCellValue($x.'1', $header_name);
            $objPHPExcel->getActiveSheet()->getStyle($x)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);  
            $objPHPExcel->getActiveSheet()->getStyle($x.'1')->getFont()->setBold(true);
            $x++;
             
        }
        
        $rowCount = '2';
        foreach($q_result AS $q_row)
        { 
            $c = 'A';
            foreach($q_row AS $header => $row)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue($c.$rowCount, $row);

                if ($header === 'Document Type') {
                    $objValidation = $objPHPExcel->getActiveSheet()->getCell($c . $rowCount)->getDataValidation();
                    $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                    $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                    $objValidation->setShowDropDown(true);
                    $objValidation->setFormula1($doctype_list);
            
                    $objPHPExcel->getActiveSheet()->getCell($c . $rowCount)->setDataValidation(clone $objValidation);
                    $objPHPExcel->getActiveSheet()->getCell($c . $rowCount)->setValue($row);

                    $objValidation->setFormula2('"' . $doctype_list . '"');
                    $objValidation->setOperator(PHPExcel_Cell_DataValidation::OPERATOR_EQUAL);
                    $objValidation->setAllowBlank(false);
                }

                $objPHPExcel->getActiveSheet()->getColumnDimension($c)->setAutoSize(true);   
                $c++;
            }

            $objPHPExcel->getActiveSheet()->getStyle($c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $rowCount++;
                
        }

        $today = date("Y-m-d");

        $fileName = $req_refno.'_'.$today.'.xlsx';
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$fileName.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        header("Pragma: no-cache");
        header("Expires: 0");

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        ob_end_clean();

        $objWriter->save('php://output'); die;

    }

    public function import_excel()
    {   
        ini_set('max_execution_time', 0); 

        $customer_guid = $this->session->userdata('customer_guid');
        $user_guid = $this->session->userdata('user_guid');
        $req_refno = $this->input->post('req_refno');
        $request_guid = $this->db->query("SELECT REPLACE(UPPER(UUID()),'-','') AS uuid")->row('uuid');
        $file = $_FILES['file'];

        $fileName = $file["name"];
        $fileTmpPath = $file["tmp_name"];
        $fileSize = $file["size"];
        $fileError = $file["error"];
        $filePath = 'media/' . $fileName;

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');
        $supplier_info = $this->db->query("SELECT `supplier_group_guid` FROM set_supplier_user_relationship WHERE user_guid = '$user_guid' AND customer_guid = '$customer_guid' GROUP BY `supplier_group_guid`")->result_array();
        $supplier_list = array();

        foreach($supplier_info as $value){

            if($value['supplier_group_guid'] != '' && $value['supplier_group_guid'] != null && $value['supplier_group_guid'] != 'null'){
                $supplier_list[] = $value['supplier_group_guid'];
            }
        }

        if (!move_uploaded_file($fileTmpPath, $filePath)) {

            $response = array(
                'status'    => false,
                'message'   => 'An error occurred while saving the file.'
            );

            echo json_encode($response); die;
        }

        if($req_refno == ''){
            $this->db->query("SET @orderdate = CURDATE();");
            $this->db->query("SET @maxno = IFNULL((SELECT MAX(RIGHT(request_refno,4)) FROM lite_b2b.archived_document WHERE LEFT(DATE(requested_at),7) = LEFT(@orderdate,7)),'0000');");
            $this->db->query("SET @month = DATE_FORMAT(NOW(), '%y%m');");
            $this->db->query("SET @refno = CONCAT('RD',@month,CONCAT(IF(@maxno<9,'000',IF(@maxno<99,'00',IF(@maxno<999,'0',''))),@maxno+1));");		
            
            $req_refno = $this->db->query("SELECT @refno AS req_id;")->row('req_id');
        }

        if ($fileError == 0) {

            $allowedExtensions = ["xls","xlsx","csv"];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
            if (in_array($fileExtension, $allowedExtensions)) {

                $objPHPExcel = PHPExcel_IOFactory::load($filePath);
                $worksheet = $objPHPExcel->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();

                if($worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true)[0][0] != 'Document Ref No'){

                    $response = array(
                        'status'    => false,
                        'message'   => 'Invalid excel template. Please download the excel template.'
                    );

                }else{

                    for ($row = 2; $row <= $highestRow; $row++) {
                        $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true)[0];

                        $doc_refno = isset($rowData[0]) ? $rowData[0] : '';

                        $checking = $this->db->query("SELECT COUNT(*) AS cnt FROM lite_b2b.archived_document WHERE request_refno = '$req_refno' AND customer_guid = '$customer_guid' AND doc_refno = '$doc_refno' AND requested_by = '$username'")->row('cnt');

                        if($checking == 0 && $doc_refno != ''){
                            $data = array(
                                'guid'                  => $this->db->query("SELECT REPLACE(UPPER(UUID()),'-','') AS uuid")->row('uuid'),
                                'request_guid'          => $request_guid,
                                'request_refno'         => $req_refno,
                                'customer_guid'         => $customer_guid,
                                'doc_refno'             => $doc_refno,
                                'requested_by_supplier' => json_encode($supplier_list),
                                'requested_at'          => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
                                'requested_by'          => $username,
                            );
                        
                            $this->db->insert('lite_b2b.archived_document',$data);
                        }
                    }

                    $response = array(
                        'status'    => true,
                        'req_refno' => $req_refno,
                        'message'   => 'File imported successfully!'
                    );
                }

                unlink($filePath);  

            } else {

                $response = array(
                    'status'    => false,
                    'message'   => 'Invalid file format. Please upload a valid file.'
                );

            }
        } else {

            $response = array(
                'status'    => false,
                'message'   => 'Error uploading file. Please try again.'
            );

        }

        echo json_encode($response);

    }

    function isHQServerDown($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    
        // Execute the request
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
    
        if ($response === false || $info['http_code'] !== 200) {
            return true;
        } else {
            return false;
        }
    }

    public function retrieve_from_HQ($doc_guid = '')
    {
        $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE hq_sync = '1' OR (doc_type <> '' AND doc_date IS NULL AND SCode IS NULL AND SName IS NULL AND total IS NULL AND pricing_type = '' AND json_report IS NULL AND file_path IS NULL AND status = 'SUBMITTED' AND ticket_created IS NULL) ")->result_array();
        // $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE doc_refno = 'METPCI22040453' AND request_guid = '7727C64C86DF11EE8A5C6045BD209184' LIMIT 1")->result_array();

        if($doc_guid != ''){
            $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE guid = '$doc_guid' LIMIT 1")->result_array();
        }
        
        foreach($doc_info as $doc){

            $customer_guid = $doc['customer_guid'];

            $live_date = null;
            $check_portal_live_date = $this->db->query("SELECT COUNT(*) AS result FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'lite_b2b' AND TABLE_NAME = 'acc_settings' AND COLUMN_NAME = 'portal_live_date'")->row('result');

            if($check_portal_live_date != '0'){
                $live_date = $this->db->query("SELECT portal_live_date FROM lite_b2b.acc_settings WHERE customer_guid = '$customer_guid'")->row('portal_live_date');
            }

            if($live_date == null){

                $response = array(
                    'status'    => false,
                    'message'   => 'Live date were not setup, kindly inform developer to check.',
                );
        
                echo json_encode($response); die;
            }

            $check_portal['status'] = false;

            if($doc_guid == ''){

                $check_portal = $this->retrieve_from_portal($doc['doc_refno'], '', 'b2b_archive', $live_date);

                if($check_portal['status'] == false){
                    $check_portal = $this->retrieve_from_portal($doc['doc_refno'], '', 'b2b_summary', $live_date);
                }

            }

            if($check_portal['status'] == true){

                $result_portal = $check_portal['result'][0];
                $supplier_code = $result_portal['supplier_code'];
                $pricing_type = $result_portal['pricing_type'];
                $requested_supplier = array();

                $supplier_guid = $this->db->query("SELECT supplier_group_guid FROM lite_b2b.set_supplier_group WHERE supplier_group_name = '$supplier_code' AND customer_guid = '$customer_guid'")->result_array();

                $supplier_exists = 0;

                foreach($supplier_guid as $guid){

                    if(in_array($guid['supplier_group_guid'], json_decode($doc['requested_by_supplier'], true))){
                        $supplier_exists++;
                    }
                }

                foreach(json_decode($doc['requested_by_supplier'],true) as $guid){
            
                    $supcode_array = $this->db->query("SELECT supplier_group_name FROM lite_b2b.set_supplier_group WHERE supplier_group_guid = '$guid' AND customer_guid = '$customer_guid'")->result_array();
        
                    foreach($supcode_array as $supcode){
                        $requested_supplier[] = $supcode['supplier_group_name'];
                    }
                }

                if(!in_array($supplier_code, $requested_supplier) || sizeof($supplier_guid) == 0){
                    $pricing_type = '';
                }

                $update_data = array(
                    'doc_date'      => $result_portal['doc_date'],
                    'doc_type'      => $result_portal['doc_type'],
                    'SCode'         => $result_portal['supplier_code'],
                    'SName'         => $result_portal['supplier_name'],
                    'total'         => $result_portal['doc_amount'],
                    'pricing_type'  => $pricing_type,
                    'json_report'   => $result_portal['doc_report'],
                    'hq_sync'       => 2,
                );
    
                $this->db->where('guid', $doc['guid']);
                $this->db->update('lite_b2b.archived_document', $update_data);

                if(isset($result_portal['doc_export'])){
                    continue;
                }
            }

            $acc_info = $this->db->query("SELECT public_ip from lite_b2b.acc where acc_guid = '$customer_guid'");
            $api_url = trim($acc_info->row('public_ip'), "/");

            if($api_url == '' || $api_url == null){

                $response = array(
                    'status'    => false,
                    'message'   => 'HQ endpoint were not setup, kindly inform developer to check.',
                );
        
                echo json_encode($response); die;
            }

            $to_shoot_url = $api_url."/lite_panda_b2b_checking_rest/index.php/Archived_document/check_document";

            $update_data = array(
                'hq_sync'       => 99,
            );
    
            $this->db->where('guid', $doc['guid']);
            $this->db->update('lite_b2b.archived_document', $update_data);

            if ($this->isHQServerDown($api_url."/lite_panda_b2b_checking_rest/index.php/Archived_document")){

                $response = array(
                    'status'    => false,
                    'message'   => 'HQ server down, kindly inform CTS to check.',
                );
        
                echo json_encode($response); die;

            }

            $post_data = array(
                'doc_refno' => $doc['doc_refno'],
                'doc_type'  => $doc['doc_type'],
            );

            $ch = curl_init();	

            curl_setopt_array($ch, array(
                CURLOPT_URL => $to_shoot_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $post_data,
            ));

            $result = curl_exec($ch);

            curl_close($ch);

            $data = json_decode($result, true);
            // $info = json_decode($data['data'],true);

            // print_r($result); die;

            // print_r($info['query1'][0]['supplier']); die;
            // $modifiedString = str_replace(["\n", "\r", "\r\n"], '', $result);

            // Remove extra spaces using preg_replace
            // $modifiedString = preg_replace('/\s+/', ' ', $modifiedString);
            // $modifiedString = str_replace(["\n", "\'", "\s{2,}", "\s+"], "", $result);
            // print_r(stripslashes($modifiedString));die;

            $doc_date = null;
            $doc_total = null;
            $supplier_code = null;
            $supplier_name = null;
            $pricing_type = '';

            if(sizeof($data['data']['query1']) != 0){

                $doc_type = $data['doc_type'];

                $check_portal = $this->retrieve_from_portal($doc['doc_refno'], $doc_type, 'b2b_archive', $live_date);

                if($check_portal['status'] == false){
                    $check_portal = $this->retrieve_from_portal($doc['doc_refno'], $doc_type, 'b2b_summary', $live_date);
                }

                if($check_portal['status'] == true){

                    $result_portal = $check_portal['result'][0];
                    $doc_date = $result_portal['doc_date'];
                    $doc_total = $result_portal['doc_amount'];
                    $supplier_code = $result_portal['supplier_code'];
                    $supplier_name = $result_portal['supplier_name'];
                    $pricing_type = $result_portal['pricing_type'];
                    $requested_supplier = array();
    
                    $supplier_guid = $this->db->query("SELECT supplier_group_guid FROM lite_b2b.set_supplier_group WHERE supplier_group_name = '$supplier_code' AND customer_guid = '$customer_guid'")->result_array();
    
                    $supplier_exists = 0;
    
                    foreach($supplier_guid as $guid){
    
                        if(in_array($guid['supplier_group_guid'], json_decode($doc['requested_by_supplier'], true))){
                            $supplier_exists++;
                        }
                    }
                    
                    foreach(json_decode($doc['requested_by_supplier'],true) as $guid){
            
                        $supcode_array = $this->db->query("SELECT supplier_group_name FROM lite_b2b.set_supplier_group WHERE supplier_group_guid = '$guid' AND customer_guid = '$customer_guid'")->result_array();
            
                        foreach($supcode_array as $supcode){
                            $requested_supplier[] = $supcode['supplier_group_name'];
                        }
                    }
    
                    if(!in_array($supplier_code, $requested_supplier) || sizeof($supplier_guid) == 0){
                        $pricing_type = '';
                    }

                }

                $result = stripslashes(trim($result));
                $result = str_replace('{"data":"{"', '{"data":{"', $result);
                $result = str_replace('}","doc_type":', '},"doc_type":', $result);

            }else{
                $result = null;
            }
          
            $update_data = array(
                'doc_date'      => $doc_date,
                'doc_type'      => $doc_type,
                'SCode'         => $supplier_code,
                'SName'         => $supplier_name,
                'total'         => $doc_total,
                'pricing_type'  => $pricing_type,
                'json_report'   => $result,
                'hq_sync'       => 2,
            );

            if(($doc_type == '' || $doc_type == null) && ($result == '' || $result == null)){

                $doctype_lists = ['PO', 'GR', 'GR', 'DN', 'CN', 'PDN', 'PCN', 'PCI', 'DI', 'SI', 'DNB'];

                foreach ($doctype_lists as $list) {
                    
                    if (strrpos($doc['doc_refno'], $list) !== false && strrpos($doc['doc_refno'], $list) != 0) {
                        
                        if($list == 'GR'){
                            $list = 'GRN';
                        }else if($list == 'DN'){
                            $list = 'PRDN';
                        }else if($list == 'CN'){
                            $list = 'PRCN';
                        }else if($list == 'DNB'){
                            $list = 'STRB';
                        }

                        $doc_type = $list;

                        break;
                    }
                }

                $update_data['doc_type'] = $doc_type;
                $update_data['hq_sync'] = 1;
            }

            $this->db->where('guid', $doc['guid']);
            $this->db->update('lite_b2b.archived_document', $update_data);

        }

        if(sizeof($doc_info) == 0){

            $response = array(
                'status'    => true,
                'message'   => 'No Data to Retrieve.',
            );

        }else{

            $response = array(
                'status'    => true,
                'message'   => 'Successfully Retrieve '.sizeof($doc_info).' data.',
            );

        }

        if($doc_guid == ''){
            echo json_encode($response);
        }

    }

    public function retrieve_from_HQ_old()
    {
        $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE hq_sync = '1'")->result_array();

        foreach($doc_info as $doc){

            $customer_guid = $doc['customer_guid'];

            $acc_info = $this->db->query("SELECT public_ip from lite_b2b.acc where acc_guid = '$customer_guid'");
            $api_url = trim($acc_info->row('public_ip'), "/");

            if($api_url == '' || $api_url == null){

                $response = array(
                    'status'    => false,
                    'message'   => 'HQ endpoint were not setup, kindly inform developer to check.',
                );
        
                echo json_encode($response); die;
            }

            $to_shoot_url = $api_url."/lite_panda_b2b_checking_rest/index.php/Archived_document/check_document";

            $update_data = array(
                'hq_sync'       => 99,
            );
    
            $this->db->where('guid', $doc['guid']);
            $this->db->update('lite_b2b.archived_document', $update_data);

            if ($this->isHQServerDown($api_url."/lite_panda_b2b_checking_rest/index.php/Archived_document")){

                $response = array(
                    'status'    => false,
                    'message'   => 'HQ server down, kindly inform CTS to check.',
                );
        
                echo json_encode($response); die;

            }

            $post_data = array(
                'doc_refno' => $doc['doc_refno'],
            );

            $ch = curl_init();	

            curl_setopt_array($ch, array(
                CURLOPT_URL => $to_shoot_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $post_data,
            ));

            $result = curl_exec($ch);

            curl_close($ch);

            $data = json_decode($result, true);
            $info = json_decode($data['data'],true);

            $doc_type = $data['doc_type'];

            if($doc_type == 'PO'){
                $parameter = 'postdatetime';
            }else if($doc_type == 'GRN'){
                $parameter = 'postdatetime';
            }else if($doc_type == 'GRDA'){
                $parameter = 'postdatetime';
            }else if($doc_type == 'PRDN'){
                $parameter = 'postdatetime';
            }else if($doc_type == 'PRCN'){
                $parameter = 'postdatetime';
            }else if($doc_type == 'PDN'){
                $parameter = 'posted_at';
            }else if($doc_type == 'PCN'){
                $parameter = 'posted_at';
            }else if($doc_type == 'PCI'){
                $parameter = 'posted_at';
            }else if($doc_type == 'DI'){
                $parameter = 'posted_at';
            }else{
                $parameter = '';
            }

            $current_datetime = $this->db->query("SELECT NOW() AS current_datetime")->row('current_datetime');

            if(sizeof($data['data']['query1']) == 0){
                $result = null;
                $doc_date = null;
                $doc_total = null;
                $supplier_code = null;
                $supplier_name = null;
                $pricing_type = '';

            }else{

                $posted_date = $info['query1'][0][$parameter];
                $doc_date = $doc_type == 'PO' ? $info['query1'][0]['podate'] : $info['query1'][0]['docdate'];
                $doc_total = $info['query1'][0]['total'];
                $supplier = $info['query1'][0]['supplier'];

                $current_year = date('Y', strtotime($current_datetime));
                $doc_year = date('Y', strtotime($posted_date));

                $yearDifference = $current_year - $doc_year;

                if($yearDifference <= 1){
                    $pricing_type = 'other_doc';
                }else if($yearDifference > 1){
                    $pricing_type = 'archived_doc';
                }

                $supplier = str_replace('Supplier Code', '', $supplier);
                $supplier_info = explode('-', $supplier);
                $supplier_code = trim($supplier_info[0]);
                $supplier_name = ltrim($supplier_info[1]);
                $supplier_guid = $this->db->query("SELECT supplier_group_guid FROM lite_b2b.set_supplier_group WHERE supplier_group_name = '$supplier_code' AND customer_guid = '$customer_guid'")->result_array();
                $requested_supplier = array();

                $supplier_exists = 0;

                foreach($supplier_guid as $guid){

                    if(in_array($guid['supplier_group_guid'], json_decode($doc['requested_by_supplier'], true))){
                        $supplier_exists++;
                    }
                }

                foreach(json_decode($doc['requested_by_supplier'],true) as $guid){
            
                    $supcode_array = $this->db->query("SELECT supplier_group_name FROM lite_b2b.set_supplier_group WHERE supplier_group_guid = '$guid' AND customer_guid = '$customer_guid'")->result_array();
        
                    foreach($supcode_array as $supcode){
                        $requested_supplier[] = $supcode['supplier_group_name'];
                    }
                }

                if(!in_array($supplier_code, $requested_supplier) || sizeof($supplier_guid) == 0){
                    $pricing_type = '';
                }

            }
            
            $update_data = array(
                'doc_date'      => $doc_date,
                'doc_type'      => $doc_type,
                'SCode'         => $supplier_code,
                'SName'         => $supplier_name,
                'total'         => $doc_total,
                'pricing_type'  => $pricing_type,
                'json_report'   => $result,
                'hq_sync'       => 2,
            );

            $this->db->where('guid', $doc['guid']);
            $this->db->update('lite_b2b.archived_document', $update_data);

        }

        if(sizeof($doc_info) == 0){

            $response = array(
                'status'    => true,
                'message'   => 'No Data to Retrieve.',
            );

        }else{

            $response = array(
                'status'    => true,
                'message'   => 'Successfully Retrieve '.sizeof($doc_info).' data.',
            );

        }

        echo json_encode($response);

    }

    public function retrieve_from_portal($doc_refno, $doc_type, $db_instance, $live_date)
    {

        if($doc_type == ''){

            $table_list = [
                'PO|pomain_info',
                'GRN|grmain_info',
                'GRDA|grmain_dncn_info',
                'PRDN|dbnotemain_info',
                'PRCN|cnnotemain_info',
                'PDNCN|cndn_amt_info',
                'PCI|promo_taxinv_info',
                'DI|discheme_taxinv_info',
                'STRB|dbnote_batch_info',
                'SI|simain_info'
            ];

            $result = array();

            foreach($table_list as $rows){

                $row = explode('|',$rows);
                $doc_type = $row[0];
                $table_name = $row[1];

                $check_table = $this->db->query("SELECT COUNT(*) AS result FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$db_instance' AND TABLE_NAME = '$table_name'")->row('result');

                if($check_table != 0){

                    if($doc_type == 'PO'){

                        $result = $this->db->query("SELECT 'PO' AS doc_type, supplier_code AS supplier_code, supplier_name AS supplier_name, PODate AS doc_date, total_include_tax AS doc_amount, po_json_report AS doc_report FROM $db_instance.$table_name WHERE RefNo = '$doc_refno'")->result_array();

                    }else if($doc_type == 'GRN'){

                        $result = $this->db->query("SELECT 'GRN' AS doc_type, supplier_code AS supplier_code, supplier_name AS supplier_name, DocDate AS doc_date, total_include_tax AS doc_amount, gr_json_report AS doc_report FROM $db_instance.$table_name WHERE RefNo = '$doc_refno'")->result_array();

                    }else if($doc_type == 'GRDA'){

                        $result = $this->db->query("SELECT 'GRDA' AS doc_type, gr.supplier_code AS supplier_code, gr.supplier_name AS supplier_name, grda.sup_cn_date AS doc_date, grda.VarianceAmt AS doc_amount, grda.grda_json_report AS doc_report FROM $db_instance.$table_name grda INNER JOIN $db_instance.grmain_info gr ON grda.`refno` = gr.`refno` WHERE grda.`refno` = '$doc_refno'")->result_array();

                    }else if($doc_type == 'PRDN'){

                        $result = $this->db->query("SELECT 'PRDN' AS doc_type, supplier_code AS supplier_code, supplier_name AS supplier_name, DocDate AS doc_date, total_incl_tax AS doc_amount, prdn_json_report AS doc_report FROM $db_instance.$table_name WHERE (refno = '$doc_refno' OR DocNo = '$doc_refno')")->result_array();
                        
                    }else if($doc_type == 'PRCN'){

                        $result = $this->db->query("SELECT 'PRCN' AS doc_type, supplier_code AS supplier_code, supplier_name AS supplier_name, DocDate AS doc_date, total_incl_tax AS doc_amount, prcn_json_report AS doc_report FROM $db_instance.$table_name WHERE refno = '$doc_refno'")->result_array();

                    }else if($doc_type == 'PDNCN'){

                        $result = $this->db->query("SELECT  IF(trans_type = 'PCNAMT', 'PCN', 'PDN') AS trans_type, supplier_code AS supplier_code, supplier_name AS supplier_name, docdate AS doc_date, total_incl_tax AS doc_amount, pdncn_json_report AS doc_report FROM $db_instance.$table_name WHERE refno = '$doc_refno'")->result_array();

                    }else if($doc_type == 'PCI'){

                        $result = $this->db->query("SELECT 'PCI' AS doc_type, supplier_code AS supplier_code, supplier_name AS supplier_name, docdate AS doc_date, total_af_tax AS doc_amount, pci_json_report AS doc_report FROM $db_instance.$table_name WHERE (inv_refno = '$doc_refno' OR promo_refno = '$doc_refno')")->result_array();

                    }else if($doc_type == 'STRB'){

                        $result = $this->db->query("SELECT 'STRB' AS doc_type, sup_code AS supplier_code, sup_name AS supplier_name, doc_date AS doc_date, `amount` AS doc_amount, strb_json_report AS doc_report FROM $db_instance.$table_name WHERE batch_no = '$doc_refno'")->result_array();

                    }else if($doc_type == 'DI'){
                        
                        $result = $this->db->query("SELECT 'DI' AS doc_type, supplier_code AS supplier_code, supplier_name AS supplier_name, docdate AS doc_date, total_net AS doc_amount, di_json_report AS doc_report FROM $db_instance.$table_name WHERE (refno = '$doc_refno' OR inv_refno = '$doc_refno')")->result_array();

                    }else if($doc_type == 'SI'){
                        
                        $result = $this->db->query("SELECT 'SI' AS doc_type, `Code` AS supplier_code, supplier_name AS supplier_name, InvoiceDate AS doc_date, total_incl_tax AS doc_amount, si_json_report AS doc_report FROM $db_instance.$table_name WHERE (RefNo = '$doc_refno' OR DocNo = '$doc_refno')")->result_array();
                    }

                }

                if(sizeof($result) != 0){
                    break;
                }

            }

            if(sizeof($result) != 0){

                if($db_instance == 'b2b_archive'){
                    $result[0]['pricing_type'] = 'archived_doc';
                }else if($db_instance == 'b2b_summary'){
                    $result[0]['pricing_type'] = 'other_doc';
                }

                if($result[0]['doc_date'] < $live_date){
                    $result[0]['pricing_type'] = '';
                }

                $response = array(
                    'status'    => true,
                    'message'   => 'Success Retrieve Data',
                    'result'    => $result,
                );

            }else{

                $response = array(
                    'status'    => false,
                    'message'   => 'Failed to Retrieve Data',
                    'result'    => $result,
                );

            }

            return $response;

        }

        if($doc_type == 'PO'){

            $result = $this->db->query("SELECT 'PO' AS doc_type, SCode AS supplier_code, SName AS supplier_name, PODate AS doc_date, total_include_tax AS doc_amount FROM $db_instance.pomain WHERE RefNo = '$doc_refno'")->result_array();

        }else if($doc_type == 'GRN'){

            $result = $this->db->query("SELECT 'GRN' AS doc_type, `Code` AS supplier_code, `Name` AS supplier_name, DocDate AS doc_date, total_include_tax AS doc_amount FROM $db_instance.grmain WHERE RefNo = '$doc_refno'")->result_array();

        }else if($doc_type == 'GRDA'){

            $result = $this->db->query("SELECT 'GRDA' AS doc_type, gr.Code AS supplier_code, gr.Name AS supplier_name, grda.sup_cn_date AS doc_date, grda.VarianceAmt AS doc_amount FROM $db_instance.grmain_dncn grda INNER JOIN $db_instance.grmain gr ON grda.`refno` = gr.`refno` WHERE grda.`refno` = '$doc_refno'")->result_array();

        }else if($doc_type == 'PRDN'){

            $result = $this->db->query("SELECT 'PRDN' AS doc_type, `Code` AS supplier_code, `Name` AS supplier_name, DocDate AS doc_date, `Amount` AS doc_amount FROM $db_instance.dbnotemain WHERE (RefNo = '$doc_refno' OR DocNo = '$doc_refno')")->result_array();
            
        }else if($doc_type == 'PRCN'){

            $result = $this->db->query("SELECT 'PRCN' AS doc_type, `Code` AS supplier_code, `Name` AS supplier_name, DocDate AS doc_date, `Amount` AS doc_amount FROM $db_instance.cnnotemain WHERE (RefNo = '$doc_refno' OR DocNo = '$doc_refno')")->result_array();

        }else if($doc_type == 'PDNCN'){

            $result = $this->db->query("SELECT  IF(trans_type = 'PCNAMT', 'PCN', 'PDN') AS trans_type, `code` AS supplier_code, `name` AS supplier_name, docdate AS doc_date, amount_include_tax AS doc_amount FROM $db_instance.cndn_amt WHERE (refno = '$doc_refno' OR docno = '$doc_refno')")->result_array();

        }else if($doc_type == 'PDN'){

            $result = $this->db->query("SELECT  IF(trans_type = 'PCNAMT', 'PCN', 'PDN') AS trans_type, `code` AS supplier_code, `name` AS supplier_name, docdate AS doc_date, amount_include_tax AS doc_amount FROM $db_instance.cndn_amt WHERE (refno = '$doc_refno' OR docno = '$doc_refno')")->result_array();

        }else if($doc_type == 'PCN'){

            $result = $this->db->query("SELECT  IF(trans_type = 'PCNAMT', 'PCN', 'PDN') AS trans_type, `code` AS supplier_code, `name` AS supplier_name, docdate AS doc_date, amount_include_tax AS doc_amount FROM $db_instance.cndn_amt WHERE (refno = '$doc_refno' OR docno = '$doc_refno')")->result_array();

        }else if($doc_type == 'PCI'){

            $result = $this->db->query("SELECT 'PCI' AS doc_type, sup_code AS supplier_code, sup_name AS supplier_name, docdate AS doc_date, total_af_tax AS doc_amount FROM $db_instance.promo_taxinv WHERE (inv_refno = '$doc_refno' OR promo_refno = '$doc_refno')")->result_array();

        }else if($doc_type == 'STRB'){

            $result = $this->db->query("SELECT 'STRB' AS doc_type, sup_code AS supplier_code, sup_name AS supplier_name, doc_date AS doc_date, `Amount` AS doc_amount FROM $db_instance.dbnote_batch WHERE batch_no = '$doc_refno'")->result_array();

        }else if($doc_type == 'DI'){
            
            $result = $this->db->query("SELECT 'DI' AS doc_type, sup_code AS supplier_code, sup_name AS supplier_name, docdate AS doc_date, total_net AS doc_amount FROM $db_instance.discheme_taxinv WHERE (refno = '$doc_refno' OR inv_refno = '$doc_refno')")->result_array();

        }else if($doc_type == 'SI'){
            
            $result = $this->db->query("SELECT 'SI' AS doc_type, `Code` AS supplier_code, `Name` AS supplier_name, InvoiceDate AS doc_date, total_incl_tax AS doc_amount FROM $db_instance.simain WHERE (RefNo = '$doc_refno' OR DocNo = '$doc_refno')")->result_array();
        }

        if(sizeof($result) != 0){

            if($db_instance == 'b2b_archive'){
                $result[0]['pricing_type'] = 'archived_doc';
            }else if($db_instance == 'b2b_summary'){
                $result[0]['pricing_type'] = 'other_doc';
            }

            if($result[0]['doc_date'] < $live_date){
                $result[0]['pricing_type'] = '';
            }

            $response = array(
                'status'    => true,
                'message'   => 'Success Retrieve Data',
                'result'    => $result,
            );

        }else{

            $response = array(
                'status'    => false,
                'message'   => 'Failed to Retrieve Data',
                'result'    => $result,
            );

        }

        return $response;

    }

    public function confirm_request()
    {
        $user_guid = $this->session->userdata('user_guid');
        $customer_guid = $this->session->userdata('customer_guid');
        $req_refno = $this->input->post("req_refno");

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');

        $data = array(
            'status'        => 'SUBMITTED',
            'hq_sync'       => 1,
            'submitted_at'  => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
            'submitted_by'  => $username,
        );

        $this->db->where('request_refno', $req_refno);
        $this->db->where('customer_guid', $customer_guid);
        $this->db->update('lite_b2b.archived_document', $data);

        $affectedRows = $this->db->affected_rows();

        if ($affectedRows > 0) {

            $this->send_email_notification($req_refno);

            $response = array(
                'status'    => true,
                'message'   => 'Successfully Submitted'
            );

        } else {
            
            $response = array(
                'status'    => false,
                'message'   => 'Fail to Submit the request, Please try again'
            );

        }

        echo json_encode($response);

    }

    public function submit_review()
    {
        $user_guid = $this->session->userdata('user_guid');
        $customer_guid = $this->session->userdata('customer_guid');
        $req_guid = $this->input->post("req_guid");
        $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');
        $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_refno = '$req_refno'")->result_array();

        if($doc_info[0]['reviewed_at'] != null && $doc_info[0]['reviewed_by'] != null){

            $reviewed_at = $doc_info[0]['reviewed_at'];
            $reviewed_by = $doc_info[0]['reviewed_by'];

            $response = array(
                'status'    => false,
                'message'   => 'This request has already been reviewed by '.$reviewed_by.' at '.$reviewed_at
            );

            echo json_encode($response); die;
        }

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');

        $data = array(
            'status' => 'REVIEWED',
            'reviewed_at'  => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
            'reviewed_by'  => $username,
        );

        $this->db->where('request_refno', $req_refno);
        $this->db->where('customer_guid', $customer_guid);
        $this->db->update('lite_b2b.archived_document', $data);

        $affectedRows = $this->db->affected_rows();

        if ($affectedRows > 0) {

            $this->send_email_notification($req_refno);

            $response = array(
                'status'    => true,
                'message'   => 'Success'
            );

        } else {
            
            $response = array(
                'status'    => false,
                'message'   => 'Fail to Submit the review, Please try again'
            );

        }

        echo json_encode($response);

    }

    public function resync_document()
    {
        $customer_guid = $this->session->userdata('customer_guid');
        $req_guid = $this->input->post("req_guid");
        $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');
        $checking_flag = $this->input->post("checking_flag");
        $initial_missing = $this->input->post("initial_missing");

        if($checking_flag == 0){
            $data = array(
                'hq_sync' => 1,
            );

            $this->db->where('customer_guid', $customer_guid);
            $this->db->where('request_refno', $req_refno);
            $this->db->where('pricing_type', '');
            $this->db->update('lite_b2b.archived_document', $data);
        }

        $check_incomplete = $this->db->query("SELECT COUNT(*) AS cnt FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_refno = '$req_refno' AND hq_sync = 1")->row('cnt');

        $balance = (int)$initial_missing - (int)$check_incomplete;
        if($balance == 0){
            $balance = 1;
        }
        $progress = ($balance / $initial_missing) * 100;

        if ($check_incomplete == 0) {

            $response = array(
                'status'    => true,
                'progress'  => $progress,
                'message'   => 'Resync Complete!'
            );

        } else {
            
            $response = array(
                'status'    => false,
                'progress'  => $progress,
                'message'   => 'Syncing still in progress...'
            );

        }

        echo json_encode($response);

    }

    public function check_before_upload()
    {   
        $guid = $this->input->post('guid');
        $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE guid = '$guid' LIMIT 1");

        if($doc_info->row('doc_type') == ''){

            $response = array(
                'status'        => false,
                'message'       => 'Please Select Document Type',
            );

        }else if($doc_info->row('pricing_type') == ''){

            $response = array(
                'status'        => false,
                'message'       => 'Please Select Pricing Type',
            );

        }else{

            $response = array(
                'status'        => true,
                'message'       => 'Success',
            );

        }

        echo json_encode($response); die;
    }

    public function upload_pdf()
    {   
        ini_set('max_execution_time', 0); 

        $customer_guid = $this->session->userdata('customer_guid');
        $user_guid = $this->session->userdata('user_guid');
        $guid = $this->input->post('guid');
        $file = $_FILES['file'];

        $fileName = $file["name"];
        $fileTmpPath = $file["tmp_name"];
        $fileSize = $file["size"];
        $fileError = $file["error"];

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');
        $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE guid = '$guid' LIMIT 1");
        $fileServerURL = $this->file_config_b2b->file_path_name($customer_guid,'web','file_server','main_path','FILESERVER');
        $uploadPath = $this->file_config_b2b->file_path_name($customer_guid,'web','general_doc','misc_doc','UMD');

        if ($fileError == 0) {

            $file_info = explode('_', $fileName);
            
            if(sizeof($file_info) != 4){

                $response = array(
                    'status'    => false,
                    'message'   => 'Invalid file naming, please rename the file according to the format.'
                );

                echo json_encode($response); die;
            }

            $allowedExtensions = ["pdf"];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
            if (in_array($fileExtension, $allowedExtensions)) {

                $file_refno = $file_info[0];
                $file_supplier_code = $file_info[1];
                $file_date = $file_info[2];
                $file_amount = $file_info[3];
                $pricing_type = $doc_info->row('pricing_type');
                $doc_type = $doc_info->row('doc_type');

                $period_code = $this->db->query("SELECT LEFT('$file_date',7) as period_code")->row('period_code');

                $supplier_info = $this->db->query("SELECT b.supplier_guid, b.supplier_name FROM lite_b2b.set_supplier_group a INNER JOIN lite_b2b.set_supplier b ON a.supplier_guid = b.supplier_guid WHERE a.supplier_group_name = '$file_supplier_code' AND a.customer_guid = '$customer_guid' AND b.isactive = 1 LIMIT 1");

                if(sizeof($supplier_info->result_array()) == 0){

                    $response = array(
                        'status'    => false,
                        'message'   => 'Supplier guid cannot be found.'
                    );
    
                    echo json_encode($response); die;
                }

                $supplier_guid = $supplier_info->row('supplier_guid');
                $supplier_name = $supplier_info->row('supplier_name');

                $filePath = $uploadPath.'/'.$customer_guid.'/'.$supplier_guid.'/'.$pricing_type.'/'.$period_code.'/'.$doc_type.'/';

                if (!is_dir($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                if($file_refno != $doc_info->row('doc_refno')){

                    $response = array(
                        'status'    => false,
                        'message'   => 'Invalid file naming, please rename the file according to the document refno and the format.'
                    );
    
                    echo json_encode($response); die;
                }

                if (!move_uploaded_file($fileTmpPath, $filePath.$file_refno.'.pdf')) {

                    $response = array(
                        'status'    => false,
                        'message'   => 'An error occurred while saving the file.'
                    );

                    echo json_encode($response); die;
                }

                $filePath = str_replace('/media/','',$filePath);

                $data = array(
                    'file_path' => $fileServerURL.$filePath,
                    'SCode'     => $file_supplier_code,
                    'SName'     => $supplier_name,
                    'total'     => $file_amount
                );
        
                $this->db->where('guid', $guid);
                $this->db->update('lite_b2b.archived_document', $data);

                $response = array(
                    'status'    => true,
                    'message'   => 'PDF uploaded successfully!'
                );

            } else {

                $response = array(
                    'status'    => false,
                    'message'   => 'Invalid file format. Please upload PDF file.'
                );

            }
        } else {

            $response = array(
                'status'    => false,
                'message'   => 'Error uploading pdf. Please try again.'
            );

        }

        echo json_encode($response);

    }
    public function approve_request()
    {
        $user_guid = $this->session->userdata('user_guid');
        $customer_guid = $this->session->userdata('customer_guid');
        $req_guid = $this->input->post("req_guid");
        $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');
        $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_refno = '$req_refno'")->result_array();

        if($doc_info[0]['approved_at'] != null && $doc_info[0]['approved_by'] != null){

            $approved_at = $doc_info[0]['approved_at'];
            $approved_by = $doc_info[0]['approved_by'];

            $response = array(
                'status'    => false,
                'message'   => 'This request has already been approved by '.$approved_by.' at '.$approved_at
            );

            echo json_encode($response); die;
        }

        if($doc_info[0]['rejected_at'] != null && $doc_info[0]['rejected_by'] != null){

            $rejected_at = $doc_info[0]['rejected_at'];
            $rejected_by = $doc_info[0]['rejected_by'];

            $response = array(
                'status'    => false,
                'message'   => 'This request has already been rejected by '.$rejected_by.' at '.$rejected_at
            );

            echo json_encode($response); die;
        }

        $data = array(
            'status'        => 'APPROVED',
            'approved_at'   => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
            'approved_by'   => $username,
        );

        $this->db->where('request_refno', $req_refno);
        $this->db->where('customer_guid', $customer_guid);
        $this->db->update('lite_b2b.archived_document', $data);

        $affectedRows = $this->db->affected_rows();

        if ($affectedRows > 0) {

            $doc_list = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE request_refno = '$req_refno' AND customer_guid = '$customer_guid' AND `status` = 'APPROVED' AND doc_type IS NOT NULL AND pricing_type <> ''")->result_array();

            // nabil testing , commented since need to do testing at production
            // foreach ($doc_list as $doc){

            //     $this->send_email_notification($req_refno);

            //     $guid = $doc['guid'];

            //     $json_result = $this->db->query("SELECT JSON_UNQUOTE(JSON_EXTRACT(`json_report`,'$.data')) AS `json_report`
            //     FROM lite_b2b.`archived_document`
            //     WHERE `guid` = '$guid';")->row('json_report');

            //     if(sizeof(json_decode($json_result,true)) == 0){
            //         $json_result = $this->db->query("SELECT `json_report`
            //         FROM lite_b2b.`archived_document`
            //         WHERE `guid` = '$guid';")->row('json_report');
            //     }

            //     $json = json_decode($json_result, true);

            //     $supplier_info = str_replace("Supplier Code", "", $json['query1'][0]['supplier']);
            //     $supplier_info = explode("-", $supplier_info);

            //     $insert_data = array(
            //         'customer_guid' => $doc['customer_guid'],
            //         'doc_type'      => $doc['doc_type'],
            //         'charge_type'   => $doc['pricing_type'],
            //         'status'        => '',
            //         'RefNo'         => $doc['doc_refno'],
            //         'PODate'        => $json['query1'][0]['podate'],
            //         'SCode'         => trim($supplier_info[0]),
            //         'SName'         => ltrim($supplier_info[1]),
            //         'SubTotal1'     => $json['query1'][0]['total'],
            //         'page'          => 1,
            //         'created_by'    => 'system',
            //         'created_at'    => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
            //     );

            //     // $this->db->insert('b2b_summary.extra_doc', $insert_data);

            //     $sql = $this->db->insert_string('b2b_summary.extra_doc', $insert_data);
            //     $sql = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $sql);

            //     $this->db->query($sql);

            // }

            $response = array(
                'status'    => true,
                'message'   => 'Successfully Approved'
            );

        } else {
            
            $response = array(
                'status'    => false,
                'message'   => 'Fail to approve the request, Please try again'
            );

        }

        echo json_encode($response);

    }

    public function reject_request()
    {
        $user_guid = $this->session->userdata('user_guid');
        $customer_guid = $this->session->userdata('customer_guid');
        $req_guid = $this->input->post("req_guid");
        $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');
        $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_refno = '$req_refno'")->result_array();

        if($doc_info[0]['approved_at'] != null && $doc_info[0]['approved_by'] != null){

            $approved_at = $doc_info[0]['approved_at'];
            $approved_by = $doc_info[0]['approved_by'];

            $response = array(
                'status'    => false,
                'message'   => 'This request has already been approved by '.$approved_by.' at '.$approved_at
            );

            echo json_encode($response); die;
        }

        if($doc_info[0]['rejected_at'] != null && $doc_info[0]['rejected_by'] != null){

            $rejected_at = $doc_info[0]['rejected_at'];
            $rejected_by = $doc_info[0]['rejected_by'];

            $response = array(
                'status'    => false,
                'message'   => 'This request has already been rejected by '.$rejected_by.' at '.$rejected_at
            );

            echo json_encode($response); die;
        }

        $data = array(
            'status' => 'REJECTED',
            'rejected_at'  => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
            'rejected_by'  => $username,
        );

        $this->db->where('request_refno', $req_refno);
        $this->db->where('customer_guid', $customer_guid);
        $this->db->update('lite_b2b.archived_document', $data);

        $affectedRows = $this->db->affected_rows();

        if ($affectedRows > 0) {

            $this->send_email_notification($req_refno);

            $response = array(
                'status'    => true,
                'message'   => 'Successfully Reject'
            );

        } else {
            
            $response = array(
                'status'    => false,
                'message'   => 'Fail to reject the request, Please try again'
            );

        }

        echo json_encode($response);

    }

    public function trigger_ticket()
    {

        $user_guid = $this->session->userdata('user_guid');
        $customer_guid = $this->session->userdata('customer_guid');
        $req_guid = $this->input->post("req_guid");
        $req_refno = $this->db->query("SELECT request_refno FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_guid = '$req_guid' LIMIT 1")->row('request_refno');

        $subtopic = $this->file_config_b2b->file_path_name($customer_guid,'web','panda_helpdesk','archived_doc_subtopic','HADST');
        $acc_name = $this->db->query("SELECT * FROM lite_b2b.acc WHERE acc_guid = '$customer_guid' AND isactive = '1' LIMIT 1");
        $doc_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_refno = '$req_refno' AND `status` <> 'NEW' AND hq_sync <> '1' AND (doc_type IS NULL OR (json_report IS NULL AND file_path IS NULL))")->result_array();

        if($doc_info[0]['ticket_created'] != null){

            $ticket_no = $doc_info[0]['ticket_created'];

            $response = array(
                'status'    => false,
                'message'   => 'Fail to create ticket as there is already an existing ticket for this request with reference #'.$ticket_no
            );

            echo json_encode($response); die;
        }

        $supplier = array();
        $supplier_list = json_decode($doc_info[0]['requested_by_supplier'], true);

        foreach($supplier_list as $row){
            $supplier_info = $this->db->query("SELECT * FROM lite_b2b.set_supplier ss INNER JOIN lite_b2b.set_supplier_group ssg ON ss.`supplier_guid` = ssg.`supplier_guid` WHERE ssg.`supplier_group_guid` = '$row' AND ssg.`customer_guid` = '$customer_guid' LIMIT 1")->result_array();
            $supplier[] = $supplier_info[0]['supplier_group_name'].' - '.$supplier_info[0]['supplier_name']; 
        }

        $text_message = '';
        $text_message .= 'Hi,';
        $text_message .= '</br></br>';
        $text_message .= 'Kindly assist in retrieving below documents for the specified retailer and suppliers.';
        $text_message .= '</br></br>';
        $text_message .= '<table border="1" style="width: 100%;">';
        $text_message .= '  <tr>';
        $text_message .= '      <th style="width: 15%;">';
        $text_message .= '          Unavailable Document';
        $text_message .= '      </th>';
        $text_message .= '      <td>';
        $text_message .= '          <table border="1" style="width: 50%;">';
        $text_message .= '              <tr>';
        $text_message .= '                  <th style="width: 20%;">Retailer</th>';
        $text_message .= '                  <td style="width: 30%;">'.$acc_name->row("acc_name").'</td>';
        $text_message .= '              </tr>';
        $text_message .= '              <tr>';
        $text_message .= '                  <th style="width: 20%;">Supplier</th>';
        $text_message .= '                  <td style="width: 30%;">';

        foreach($supplier as $sup){
            $text_message .=                   $sup.'</br>';
        }
        $text_message .= '                  </td>';
        $text_message .= '              </tr>';
        $text_message .= '              <tr>';
        $text_message .= '                  <th style="width: 20%;">';
        $text_message .= '                      Doc Refno</br>';
        $text_message .= '                      Total Document : '.sizeof($doc_info);
        $text_message .= '                  </th>';
        $text_message .= '                  <td style="width: 30%;">';

        foreach($doc_info as $doc){
            $text_message .=                    $doc["doc_refno"].'</br>';
        }

        $text_message .= '                  </td>';
        $text_message .= '              </tr>';
        $text_message .= '          </table>';
        $text_message .= '      </td>';
        $text_message .= '  </tr>';
        $text_message .= '</table>';
        $text_message .= '</br>';
        $text_message .= 'Thanks for your help.';

        $response = $this->panda_helpdesk->auto_trigger_ticket($subtopic, $text_message);

        if ($response['status'] == true) {

            $data = array(
                'ticket_created' => $response['ticket_number'],
            );
    
            $this->db->where('request_refno', $req_refno);
            $this->db->where('customer_guid', $customer_guid);
            $this->db->update('lite_b2b.archived_document', $data);

        }

        echo json_encode($response);

    }

    public function view_multiple_report()
    {
        // ob_start();
        require_once(APPPATH . 'libraries/PDFMerger/wraper.php');
        require(APPPATH . 'libraries/PDFMerger/PDFMerger.php');
        $pdf = new PDFMerger;

        $postdata = $this->input->post('postdata');
        $postdata = json_decode($postdata, true);

        foreach ($postdata as $count => $row) {

            $customer_guid = $row['customer_guid'];
            $doc_refno = $row['doc_refno'];
            $doc_type = $row['doc_type'];
            $file_path = $row['file_path'];
            $jasper_ip = $this->file_config_b2b->file_path_name($customer_guid,'web','general_doc','jasper_invoice_ip','GDJIIP');

            $param_refno = '?refno='.$doc_refno;
            $param_customer = '&customer_guid='.$customer_guid;
            $param_mode = '&mode=archived_doc';

            if(($file_path == '' || $file_path == null) && $doc_type != 'ACC')
            {

                if($doc_type == 'PO'){

                    $report_url = $jasper_ip . "/jasperserver/rest_v2/reports/reports/PandaReports/Backend_PO/main_jrxml.pdf";

                }else if($doc_type == 'GRN'){

                    $report_url = $jasper_ip ."/jasperserver/rest_v2/reports/reports/PandaReports/Backend_GRN/gr_supplier_copy.pdf";

                }else if($doc_type == 'GRDA'){

                    $report_url = $jasper_ip ."/jasperserver/rest_v2/reports/reports/PandaReports/Backend_GRN/GRDA.pdf";

                }else if($doc_type == 'PRDN'){

                    $report_url = $jasper_ip . "/jasperserver/rest_v2/reports/reports/PandaReports/Backend_PRDN/main_jrxml.pdf";

                }else if($doc_type == 'PRCN'){

                    $report_url = $jasper_ip . "/jasperserver/rest_v2/reports/reports/PandaReports/Backend_PRCN/main_jrxml.pdf";

                }else if($doc_type == 'PDN'){

                    $report_url = $jasper_ip . "/jasperserver/rest_v2/reports/reports/PandaReports/Backend_PDN_PCN/main_jrxml.pdf";

                }else if($doc_type == 'PCN'){

                    $report_url = $jasper_ip . "/jasperserver/rest_v2/reports/reports/PandaReports/Backend_PDN_PCN/main_jrxml.pdf";

                }else if($doc_type == 'PCI'){

                    $report_url = $jasper_ip ."/jasperserver/rest_v2/reports/reports/PandaReports/Backend_Promotion/promo_claim_inv.pdf";

                }else if($doc_type == 'DI'){

                    $report_url = $jasper_ip ."/jasperserver/rest_v2/reports/reports/PandaReports/Backend_DIncentives/display_incentive_report.pdf";

                }else if($doc_type == 'SI'){

                    $report_url = $jasper_ip."/jasperserver/rest_v2/reports/reports/PandaReports/Backend_SI/si_landscape.pdf";

                }else if($doc_type == 'STRB'){

                    $report_url = $jasper_ip . "/jasperserver/rest_v2/reports/reports/PandaReports/Backend_PO/Stock_Return_Batch_Json.pdf";

                }

                $url = $report_url.$param_refno.$param_customer.$param_mode;

            }else{

                $url = $file_path.$doc_refno.'.pdf';

            }

            $curl[$count] = curl_init();
            curl_setopt_array($curl[$count], array(
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

            $response[$count] = curl_exec($curl[$count]);
            $httpcode = curl_getinfo($curl[$count],CURLINFO_HTTP_CODE);

            if($httpcode != '400')
            {
                $pdf->addPDF(VarStream::createReference($response[$count]), 'all');
                curl_close($curl);
            }
        }

        $pdf_name = 'MERGE_' . uniqid();
        ob_clean();
        $test = $pdf->merge('browser', $pdf_name . '.pdf'); // generate the file
        // ob_end_flush();
    }
    
}
?>