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
    }

    public function send_email_notification($req_refno){

        $customer_guid = $this->session->userdata('customer_guid');

        $request_info = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE request_refno = '$req_refno' GROUP BY request_refno");
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
                'description'   => 'Archieve document request notification',
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
        }else{
            $to_receive = $this->db->query("SELECT user_id FROM lite_b2b.set_user WHERE user_name = '$doc_requestor' AND acc_guid = '$customer_guid'")->result_array();
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
            '%redirect_url%'    => site_url("Archived_document?refno=").$req_refno,
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
            CURLOPT_URL => $to_shoot_url.'/rest_b2b/index.php/Blast_email_process/process_email_list?testing_email=nabil.haziq@pandasoftware.my', // nabil testing
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
        $req_refno = isset($_GET['refno']) ? $_GET['refno'] : '';

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
        $pending_results = $this->db->query("SELECT *, COUNT(*) AS total_doc FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND `status` = 'NEW' GROUP BY request_refno ORDER BY requested_at DESC");
        $complete_results = $this->db->query("SELECT ad.*, SUM(ies.amount) AS total_price, COUNT(*) AS total_doc FROM lite_b2b.archived_document ad LEFT JOIN b2b_invoice.invoice_external_setting ies ON ad.pricing_type = ies.doc_type WHERE ad.customer_guid = '$customer_guid' AND ad.`status` <> 'NEW' GROUP BY ad.request_refno ORDER BY ad.requested_at DESC");
        $complete_by_guid_results = $this->db->query("SELECT ad.*, SUM(ies.amount) AS total_price, COUNT(*) AS total_doc, COUNT(CASE WHEN (ad.doc_type IS NULL OR ad.pricing_type = '' OR (ad.json_report IS NULL AND ad.file_path IS NULL)) THEN 1 END) AS missing_doc FROM lite_b2b.archived_document ad LEFT JOIN b2b_invoice.invoice_external_setting ies ON ad.pricing_type = ies.doc_type WHERE ad.customer_guid = '$customer_guid' AND ad.request_refno = '$req_refno' GROUP BY ad.request_refno ORDER BY ad.requested_at DESC");
        $complete_child_by_guid_results = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_refno = '$req_refno' ORDER BY requested_at DESC");

        $data = array(
            'retailer'      => $retailer,
            'doc_type'      => $doc_type,
            'pricing_type'  => $pricing_type->result_array(),
            'pending_list'  => $pending_results->result_array(),
            'complete_list' => $complete_results->result_array(),
            'header_list'   => $complete_by_guid_results->result_array(),
            'child_list'    => $complete_child_by_guid_results->result_array(),
        );

        // print_r("SELECT *, COUNT(*) AS total_doc FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' GROUP BY request_refno ORDER BY requested_at DESC"); die;

        $this->load->view('header');  
        $this->load->view('archived_document/request_document_record', $data);  
        $this->load->view('footer' );  
    }

    public function update_doctype()
    {   
        $req_refno = $this->input->post('req_refno');
        $rowData = $this->input->post('rowData');
        $customer_guid = $this->session->userdata('customer_guid');

        foreach($rowData as $row){

            $doc_type = $row['doc_type'] != '-' ? $row['doc_type'] : '';

            $data = array(
                'doc_type' => $doc_type,
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

    public function update_pricing()
    {   
        $req_refno = $this->input->post('req_refno');
        $rowData = $this->input->post('rowData');
        $customer_guid = $this->session->userdata('customer_guid');

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

        if($this->session->userdata('loginuser') == true)
        {

            $data = array(
                'customer_guid' => $customer_guid,
                'username'      => $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE acc_guid = '$customer_guid' AND user_guid = '$user_guid' LIMIT 1")->row('user_name'),
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
            'guid'          => $this->db->query("SELECT REPLACE(UPPER(UUID()),'-','') AS uuid")->row('uuid'),
            'request_refno' => $req_refno,
            'customer_guid' => $customer_guid,
            'doc_refno'     => $refno,
            'requested_at'  => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
            'requested_by'  => $username,
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

        $data = array(
            'doc_refno' => $doc_refno,
        );

        $this->db->where('guid', $guid);
        $this->db->update('lite_b2b.archived_document', $data);

        $affectedRows = $this->db->affected_rows();

        if ($affectedRows > 0) {

            echo "<script> alert('Successfully Edit');</script>";
            echo "<script> document.location='" . base_url() . "index.php/Archived_document/request_document?refno=".$req_refno."' </script>";

        } else {

            echo "<script> alert('Fail to Update, Please try again');</script>";
            echo "<script> document.location='" . base_url() . "index.php/Archived_document/request_document?refno=".$req_refno."' </script>";

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

        $req_refno = isset($_GET['req_refno']) && $_GET['req_refno'] != '' ? $_GET['req_refno'] : 'template';
        $doctype_list = "PO, GRN, GRDA, PRDN, PRCN, PDN, PCI, DI, SI, STRB, ACC";
        $doctype_options = ['PO', 'GRN', 'GRDA', 'PRDN', 'PRCN', 'PDN', 'PCI', 'DI', 'SI', 'STRB', 'ACC'];
        $uppercase_options = array_map('strtoupper', $doctype_options);
        $doctype_list = implode(',', $uppercase_options);

        if($req_refno != 'template'){
            $q_result = $this->db->query("SELECT doc_refno AS 'Document Ref No' FROM lite_b2b.archived_document WHERE request_refno = '$req_refno'")->result_array();
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
        $file = $_FILES['file'];

        $fileName = $file["name"];
        $fileTmpPath = $file["tmp_name"];
        $fileSize = $file["size"];
        $fileError = $file["error"];
        $filePath = 'media/' . $fileName;

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');

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
                                'guid'          => $this->db->query("SELECT REPLACE(UPPER(UUID()),'-','') AS uuid")->row('uuid'),
                                'request_refno' => $req_refno,
                                'customer_guid' => $customer_guid,
                                'doc_refno'     => $doc_refno,
                                'requested_at'  => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
                                'requested_by'  => $username,
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

    public function retrieve_from_HQ()
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
                $doc_total = null;
                $supplier_code = null;
                $supplier_name = null;
                $pricing_type = '';

            }else{

                $doc_date = $info['query1'][0][$parameter];
                $doc_total = $info['query1'][0]['total'];
                $supplier = $info['query1'][0]['supplier'];

                $current_year = date('Y', strtotime($current_datetime));
                $doc_year = date('Y', strtotime($doc_date));

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

            }
            
            $update_data = array(
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
        $req_refno = $this->input->post("req_refno");

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
        $req_refno = $this->input->post("req_refno");
        $checking_flag = $this->input->post("checking_flag");

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

        if ($check_incomplete == 0) {

            $response = array(
                'status'    => true,
                'message'   => 'Resync Complete!'
            );

        } else {
            
            $response = array(
                'status'    => false,
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
        $req_refno = $this->input->post("req_refno");

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');

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

            foreach ($doc_list as $doc){

                $this->send_email_notification($req_refno);

                $guid = $doc['guid'];

                $json_result = $this->db->query("SELECT JSON_UNQUOTE(JSON_EXTRACT(`json_report`,'$.data')) AS `json_report`
                FROM lite_b2b.`archived_document`
                WHERE `guid` = '$guid';")->row('json_report');

                if(sizeof(json_decode($json_result,true)) == 0){
                    $json_result = $this->db->query("SELECT `json_report`
                    FROM lite_b2b.`archived_document`
                    WHERE `guid` = '$guid';")->row('json_report');
                }

                $json = json_decode($json_result, true);

                $supplier_info = str_replace("Supplier Code", "", $json['query1'][0]['supplier']);
                $supplier_info = explode("-", $supplier_info);

                $insert_data = array(
                    'customer_guid' => $doc['customer_guid'],
                    'doc_type'      => $doc['doc_type'],
                    'charge_type'   => $doc['pricing_type'],
                    'status'        => '',
                    'RefNo'         => $doc['doc_refno'],
                    'PODate'        => $json['query1'][0]['podate'],
                    'SCode'         => trim($supplier_info[0]),
                    'SName'         => ltrim($supplier_info[1]),
                    'SubTotal1'     => $json['query1'][0]['total'],
                    'page'          => 1,
                    'created_by'    => 'system',
                    'created_at'    => $this->db->query("SELECT NOW() as created_at")->row('created_at'),
                );

                // $this->db->insert('b2b_summary.extra_doc', $insert_data);

                // nabil testing, need to comment this to avoid extra charge to customer when testing
                // $sql = $this->db->insert_string('b2b_summary.extra_doc', $insert_data);
                // $sql = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $sql);

                // $this->db->query($sql);

            }

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
        $req_refno = $this->input->post("req_refno");

        $username = $this->db->query("SELECT user_name FROM lite_b2b.set_user WHERE user_guid = '$user_guid' AND isactive = '1' LIMIT 1")->row('user_name');

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
        $req_refno = $this->input->post("req_refno");

        $subtopic = $this->file_config_b2b->file_path_name($customer_guid,'web','panda_helpdesk','archived_doc_subtopic','HADST');
        $acc_name = $this->db->query("SELECT * FROM lite_b2b.acc WHERE acc_guid = '$customer_guid' AND isactive = '1' LIMIT 1");
        $missing_doc = $this->db->query("SELECT * FROM lite_b2b.archived_document WHERE customer_guid = '$customer_guid' AND request_refno = '$req_refno' AND `status` <> 'NEW' AND hq_sync <> '1' AND (doc_type IS NULL OR pricing_type = '' OR (json_report IS NULL AND file_path IS NULL))")->result_array();

        $text_message = '';
        $text_message .= '<table border="1" style="width: 100%;">';
        $text_message .= '  <tr>';
        $text_message .= '      <th style="width: 15%;">';
        $text_message .= '          Missing Document';
        $text_message .= '      </th>';
        $text_message .= '      <td>';
        $text_message .= '          <table border="1" style="width: 50%;">';
        $text_message .= '              <tr>';
        $text_message .= '                  <th style="width: 20%;">Client Name</th>';
        $text_message .= '                  <td style="width: 30%;">'.$acc_name->row("acc_name").'</td>';
        $text_message .= '              </tr>';
        $text_message .= '              <tr>';
        $text_message .= '                  <th style="width: 20%;">';
        $text_message .= '                      Doc Refno</br>';
        $text_message .= '                      Total Document : '.sizeof($missing_doc);
        $text_message .= '                  </th>';
        $text_message .= '                  <td style="width: 30%;">';

        foreach($missing_doc as $doc){
            $text_message .= $doc["doc_refno"].'</br>';
        }

        $text_message .= '                  </td>';
        $text_message .= '              </tr>';
        $text_message .= '          </table>';
        $text_message .= '      </td>';
        $text_message .= '  </tr>';
        $text_message .= '</table>';

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
    
}
?>