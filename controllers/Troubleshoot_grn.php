<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


    class Troubleshoot_grn extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');        
        $this->load->library('datatables');
        $this->load->library('Panda_PHPMailer');   
        $this->load->model('General_model');
    }

    public function index()
    {

        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login())
        { 

         $data = array(
                'acc' => $this->db->query("SELECT * FROM acc WHERE isactive = 1 ORDER BY acc_name ASC"),
                'gr_status' => $this->db->query("SELECT code, reason from set_setting where module_name = 'GR_FILTER_STATUS' ORDER BY code='ALL' DESC, code ASC"), 
                'period_code' => $this->db->query("SELECT period_code from lite_b2b.period_code"),
                );  
         

        $this->load->view('header');
        $this->load->view('troubleshoot_grn/troubleshoot_grn',$data);
        $this->load->view('footer');

        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }

    }//close index

    public function vendor_code_dropdown(){

            $customer_guid = $this->input->post('customer_guid');
    
            $set_supplier = $this->db->query("SELECT * FROM lite_b2b.set_supplier a INNER JOIN lite_b2b.set_supplier_group b ON a.supplier_guid = b.supplier_guid WHERE b.customer_guid = '$customer_guid' GROUP BY a.supplier_guid ORDER BY a.supplier_name ASC ");
    
            $data = array(
                'set_supplier' => $set_supplier->result()
            );
    
            echo json_encode($data);
    
        //close vendor code dropdowm
    }

    public function gr_new_table()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0); 

        $customer_guid = $this->input->post('customer_guid');
        $vendor_guid = null != $this->input->post('vendor_guid') ? $this->input->post('vendor_guid') : '';
        $gr_num = null != $this->input->post('gr_num') ? $this->input->post('gr_num') : '';
        $gr_status = null != $this->input->post('gr_status') ? $this->input->post('gr_status') : '';
        $daterange = null != $this->input->post('daterange') ? $this->input->post('daterange') : '';
        $period_code = null != $this->input->post('period_code') ? $this->input->post('period_code') : '';
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        $order = $this->input->post("order");
        $search= $this->input->post("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";

        if(!empty($order))
        {
          foreach($order as $o)
          {
              $col = $o['column'];
              $dir= $o['dir'];
          }
        }

        if($dir != "asc" && $dir != "desc")
        {
          $dir = "desc";
        }

        $valid_columns = array(
            0 => 'RefNo', 
            1 => 'GRDA', 
            2 => 'Location', 
            3 => 'Code', 
            4 => 'Name', 
            5 => 'GRDate', 
            6 => 'InvNo', 
            7 => 'einvno', 
            8 => 'Total', 
            9 => 'gst_tax_sum', 
            10 => 'total_include_tax', 
            11 => 'status', 
            12=> 'RefNo'
            
        );  

        if(!isset($valid_columns[$col]))
        {
          $order = null;
        }
        else
        {
          $order = $valid_columns[$col];
        }

        if($order !=null)
        {  
          $order_query = "ORDER BY " .$order. "  " .$dir;
        }

        $like_first_query = '';
        $like_second_query = '';

        if(!empty($search))
        {
          $x=0;
          foreach($valid_columns as $sterm)
          {
              if($x==0)
              {

                  $like_first_query = "WHERE $sterm LIKE '%".$search."%'";

              }
              else
              {

                  $like_second_query .= "OR $sterm LIKE '%".$search."%'";

              }
              $x++;
          }
           
        }

        $limit_query = " LIMIT " .$start. " , " .$length;

        $vendor_code = '';

        $all_map_vendor_code = $this->db->query("SELECT * FROM lite_b2b.set_supplier a INNER JOIN lite_b2b.set_supplier_group b ON a.supplier_guid = b.supplier_guid WHERE b.customer_guid = '$customer_guid' AND a.supplier_guid = '$vendor_guid' ");
        
        foreach ($all_map_vendor_code->result() as $value) {

            $vendor_code .= "'".$value->supplier_group_name."',";
 
        }

        $vendor_code = rtrim($vendor_code,",");

        //echo $vendor_code;die;


        if($gr_num == '')
        {
            $gr_num = "";
        }//close gr_num
        else
        {
            $gr_num = "AND a.RefNo = '$gr_num'";
        }//close else gr_num


        if($gr_status == 'ALL')
        {
            $gr_status = "";
        }//close gr_status
        else
        {
            $gr_status = "AND a.status = '$gr_status'";
        }//close else gr_status


        if($daterange != '')
        {
            $daterange1 = explode(' - ', $daterange);
            $daterange_from = date('Y-m-d',strtotime($daterange1[0]));
            $daterange_to = date('Y-m-d',strtotime($daterange1[1]));

            $daterange = "AND a.GRDate BETWEEN '$daterange_from' AND '$daterange_to'";
        }
        else
        {   
            $daterange_from = "";
            $daterange_to = "";
        };


        if($period_code == '')
        {
            $period_code = "";
        }//close period_code
        else
        {
            $period_code = "AND left(a.GRDate,7)  = '$period_code'";
        }//close else period_code

        $sql = "SELECT a.RefNo, gd.RefNo AS GRDA, a.Location, a.Code, a.Name, a.GRDate, a.InvNo, einv.einvno, a.Total, a.gst_tax_sum, a.total_include_tax, CASE 
        WHEN a.status = '' THEN 'New' 
        ELSE a.status 
        END AS status
        FROM b2b_summary.grmain a 
        
        LEFT JOIN b2b_summary.grmain_dncn gd
        ON a.RefNo = gd.RefNo
        AND a.customer_guid = gd.customer_guid
        
        LEFT JOIN b2b_summary.einv_main einv
        ON a.RefNo = einv.refno
        AND a.customer_guid = einv.customer_guid
        
        WHERE a.customer_guid = '$customer_guid'
        AND a.Code IN ($vendor_code)
        $gr_status
        $gr_num 
        $daterange 
        $period_code ";

        // echo $sql;die;

        $query = "SELECT * FROM (".$sql.") a ".$like_first_query.$like_second_query.$order_query.$limit_query;

        // echo $query;die;

        $result = $this->db->query($query);

        if(!empty($search))
        {   
            $query_filter = "SELECT * FROM (".$sql.") a ".$like_first_query.$like_second_query;
            $result_filter = $this->db->query($query_filter)->result();
            $total = count($result_filter);
        }
        else
        {   
            $total = $this->db->query($sql)->num_rows();
            // $total = count($result->result_array());
        }

            $data = array();
            foreach($result->result() as $row)
            {   
                $refno = $row->RefNo;

                $check_scode = $this->db->query("SELECT Code from b2b_summary.grmain where refno = '$refno' and customer_guid = '$customer_guid' ")->row('Code');

                $parameter = $this->db->query("SELECT * from menu where module_link = 'panda_gr'");

                $type = $parameter->row('type');
                $code = $check_scode;

                $replace_var = $this->db->query("SELECT REPLACE(REPLACE(REPLACE(filename_format, 'type', '$type'), 'code', REPLACE('$code','/','+-+')), 'refno' , '$refno') AS query FROM menu where module_link = 'panda_gr'")->row('query');

                $virtual_path = $this->db->query("SELECT CONCAT(file_host,file_path) AS full_path FROM lite_b2b.acc WHERE acc_guid = '$customer_guid' ")->row('full_path');

                $filename = $virtual_path.'/'.$replace_var.'.pdf';
            
                $nestedData['RefNo'] = $row->RefNo;
                $nestedData['grda_refno'] = $row->GRDA;
                $nestedData['Location'] = $row->Location;
                $nestedData['Code'] = $row->Code;
                $nestedData['Name'] = $row->Name;
                $nestedData['GRDate'] = $row->GRDate;
                $nestedData['InvNo'] = $row->InvNo;
                $nestedData['einvno'] = $row->einvno;
                $nestedData['Total'] = $row->Total;
                $nestedData['gst_tax_sum'] = $row->gst_tax_sum;
                $nestedData['total_include_tax'] = $row->total_include_tax;
                $nestedData['status'] = $row->status;
                $nestedData['filename'] = $filename;

                $data[] = $nestedData;
            }
        
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        );

        echo json_encode($output);
    }

    public function user_movements_table()
    {
        $customer_guid = $this->input->post('customer_guid');
        $RefNo = $this->input->post('RefNo');

        $database = 'lite_b2b';
        $database_app = 'lite_b2b_apps';

        $user_movements_table = $this->db->query("SELECT a.*,a.created_at as c_date , IF( b.user_id IS NULL, a.user_guid, b.user_id ) AS user_id,'web' as type FROM $database.supplier_movement a LEFT JOIN $database.set_user b ON a.user_guid = b.user_guid AND a.customer_guid = b.acc_guid WHERE a.value = '$RefNo' AND a.customer_guid = '$customer_guid' AND b.acc_guid = '$customer_guid' UNION ALL SELECT a.*,(SELECT created_at FROM $database_app.supplier_movement WHERE value = a.value AND action = a.action  AND customer_guid = a.customer_guid GROUP BY VALUE ORDER BY created_at DESC LIMIT 1) as c_date , IF( b.user_id IS NULL, a.user_guid, b.user_id ) AS user_id,'app' as type FROM $database_app.supplier_movement a LEFT JOIN $database.set_user b ON a.user_guid = b.user_guid AND a.customer_guid = b.acc_guid WHERE a.value = '$RefNo' AND a.customer_guid = '$customer_guid' AND b.acc_guid = '$customer_guid' GROUP BY a.action,a.value ");

        // echo $this->db->last_query();die;

        $data = array(
            'movements' => $user_movements_table->result()
        );

        echo json_encode($data);

    }//close user_movements_table
}
?>