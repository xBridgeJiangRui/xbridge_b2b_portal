<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auto_settings extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->library(array('session'));
    $this->load->library('session');
    $this->load->helper('form');
    $this->load->helper('url');
    $this->load->helper(array('form','url'));
    $this->load->helper('html');
    $this->load->database();
    $this->load->library('form_validation'); 
  }

  public function index()
  {
      if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '')
      { 
          $acc_guid = $_SESSION['customer_guid'];

          //$supplier = $this->db->query("SELECT supplier_name,supplier_guid FROM lite_b2b.set_supplier WHERE isactive = '1' ORDER BY supplier_name ASC");

          $acc_data = $this->db->query("SELECT * FROM lite_b2b.acc WHERE isactive = '1' ORDER BY acc_name ASC ");

          $data = array(
              //'supplier_data' => $supplier->result(),
              'acc_data' => $acc_data->result(),

          );
          $this->load->view('header');
          $this->load->view('auto_mapping/einv_list', $data);      
          $this->load->view('footer');
      }
      else
      {
          redirect('#');
      }
  }

  public function doc_list_table()
  {
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0); 

    $draw = intval($this->input->post("draw"));
    $start = intval($this->input->post("start"));
    $length = intval($this->input->post("length"));
    $order = $this->input->post("order");
    $search= $this->input->post("search");
    $search = addslashes($search['value']);
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
        0=> 'customer_guid',
        1=> 'supplier_guid',
        2=> 'acc_name',
        3=> 'supplier_name',
        4=> 'supplier_code',
        5=> 'auto_days',
        6=> 'isactive',
        7=> 'created_at',
        8=> 'created_by',
        9=> 'updated_at',
        10=> 'updated_by',


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
      // $this->db->order_by($order, $dir);

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
              // $this->db->like($sterm,$search);

              $like_first_query = "WHERE $sterm LIKE '%".$search."%'";

          }
          else
          {
              // $this->db->or_like($sterm,$search);

              $like_second_query .= "OR $sterm LIKE '%".$search."%'";

          }
          $x++;
      }
       
    }

    // $this->db->limit($length,$start);

    $limit_query = " LIMIT " .$start. " , " .$length;
    //$group_query = " GROUP BY b.supplier_guid ";
    $acc_guid = $_SESSION['customer_guid'];

    $sql = "SELECT c.acc_name,b.supplier_name,a.* FROM lite_b2b.einv_auto_settings a 
      INNER JOIN lite_b2b.set_supplier b
      ON a.supplier_guid = b.supplier_guid
      INNER JOIN lite_b2b.acc c
      ON a.customer_guid = c.acc_guid";

    $query = "SELECT * FROM ( ".$sql." ) a ".$like_first_query.$like_second_query.$order_query.$limit_query;

    // $import_item_gen_c = $this->db->get("backend.import_item_gen_c");

    $result = $this->db->query($query);

    //echo $this->db->last_query(); die;

    if(!empty($search))
    {
      $query_filter = "SELECT * FROM ( ".$sql." ) a ".$like_first_query.$like_second_query;
      $result_filter = $this->db->query($query_filter)->result();
      $total = count($result_filter);
    }
    else
    {
      $total = $this->db->query($sql)->num_rows();
    }

    $data = array();
    foreach($result->result() as $row)
    { 
      $nestedData['customer_guid'] = $row->customer_guid;
      $nestedData['supplier_guid'] = $row->supplier_guid;
      $nestedData['acc_name'] = $row->acc_name;
      $nestedData['supplier_name'] = $row->supplier_name;
      $nestedData['supplier_code'] = $row->supplier_code;
      $nestedData['auto_days'] = $row->auto_days;
      $nestedData['created_by'] = $row->created_by;
      $nestedData['created_at'] = $row->created_at;
      $nestedData['isactive'] = $row->isactive;
      $nestedData['updated_at'] = $row->updated_at;
      $nestedData['updated_by'] = $row->updated_by;
      
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

  public function fetch_supplier()
  {
    $type_val = $this->input->post('type_val');

    $supplier_data = $this->db->query("SELECT b.supplier_guid, b.supplier_name FROM lite_b2b.set_supplier_group a INNER JOIN lite_b2b.set_supplier b ON a.supplier_guid = b.supplier_guid AND b.isactive = '1' WHERE a.customer_guid = '$type_val' GROUP BY a.supplier_guid , a.customer_guid");

    $data = array(
      'supplier' => $supplier_data->result(),
    );

    echo json_encode($data);
  }

  public function fetch_supplier_code()
  {
    $type_val_acc = $this->input->post('type_val_acc');
    $type_val_supp = $this->input->post('type_val_supp');

    $code_data = $this->db->query("SELECT a.customer_guid, b.supplier_guid, b.supplier_name , a.supplier_group_name AS supplier_code FROM lite_b2b.set_supplier_group a INNER JOIN lite_b2b.set_supplier b ON a.supplier_guid = b.supplier_guid AND b.isactive = '1' LEFT JOIN lite_b2b.einv_auto_settings c ON a.supplier_guid = c.supplier_guid AND a.customer_guid = c.customer_guid AND a.supplier_group_name = c.supplier_code WHERE a.customer_guid = '$type_val_acc' AND a.supplier_guid = '$type_val_supp' AND c.supplier_code IS NULL GROUP BY a.supplier_group_name , a.customer_guid");

    $data = array(
      'code' => $code_data->result(),
    );

    echo json_encode($data);
  }

  public function add_new_setting()
  {
    $user_guid = $_SESSION['user_guid'];
    $user_id = $this->db->query("SELECT a.user_name FROM lite_b2b.set_user a WHERE a.user_guid ='$user_guid'")->row('user_name');;
    $created_at = $this->db->query("SELECT NOW() as now")->row('now');
    $add_status = $this->input->post('add_status');
    $add_days = $this->input->post('add_days');
    $add_supplier_code = $this->input->post('add_supplier_code');
    $add_supplier_name = $this->input->post('add_supplier_name');
    $add_acc_name = $this->input->post('add_acc_name');
    $count_code = count($add_supplier_code); 
    $count = 0;
    //print_r($count_code); die;
    foreach($add_supplier_code as $row)
    {
      $supplier_code = $row;

      $data_2 = array(
          'customer_guid' => $add_acc_name,
          'supplier_guid' => $add_supplier_name,
          'supplier_code' => $supplier_code,
          'auto_days' => $add_days,
          'isactive' => $add_status,
          'created_at' => $created_at,
          'created_by' => $user_id
      );
    
      $this->db->insert('lite_b2b.einv_auto_settings', $data_2);

      $count++;
    }

    if ($count_code == $count) {
      $data = array(
        'para1' => 0,
        'msg' => 'Create Successfully',
  
      );
      echo json_encode($data);
    } else {
      $data = array(
        'para1' => 1,
        'msg' => 'Error.',
  
      );
      echo json_encode($data);
    }
  }

  public function edit_details()
  {
    $user_guid = $_SESSION['user_guid'];
    $user_id = $this->db->query("SELECT a.user_name FROM lite_b2b.set_user a WHERE a.user_guid ='$user_guid'")->row('user_name');
    $doc_customer_guid = $this->input->post('doc_customer_guid');
    $doc_supplier_guid = $this->input->post('doc_supplier_guid');
    $edit_sup_code = $this->input->post('edit_sup_code');
    $edit_duration_days = $this->input->post('edit_duration_days');
    $edit_status = $this->input->post('edit_status');

    $check_details = $this->db->query("SELECT * FROM lite_b2b.einv_auto_settings WHERE customer_guid = '$doc_customer_guid' AND supplier_guid = '$doc_supplier_guid' AND supplier_code = '$edit_sup_code'")->result_array();

    if(count($check_details) == 0)
    {
      $data = array(
        'para1' => 'false',
        'msg' => 'Error Find Data',
      );    
      echo json_encode($data);  
      exit(); 
    }

    $data = array(
      'auto_days' => $edit_duration_days,
      'isactive' => $edit_status,
      'updated_at' => $this->db->query("SELECT NOW() as now")->row('now'),
      'updated_by' => $user_id,
    );
    $this->db->where('customer_guid', $doc_customer_guid);
    $this->db->where('supplier_guid', $doc_supplier_guid);
    $this->db->where('supplier_code', $edit_sup_code);
    $this->db->update('lite_b2b.einv_auto_settings', $data);

    $error = $this->db->affected_rows();

    if($error > 0)
    {
      $data = array(
         'para1' => 'true',
         'msg' => 'Edit Successful',
      );    
      echo json_encode($data);   
      exit();
    }
    else
    {   
      $data = array(
      'para1' => 'false',
      'msg' => 'Failed to Edit',
      );    
      echo json_encode($data);  
      exit(); 
    }
  }

  public function remove_code()
  {
    $details = $this->input->post('details');
    $details = json_encode($details);
    $details = json_decode($details);
    $array_count = count($details);
    $i = 0;

    foreach($details as $row)
    {
      $customer_guid = $row->customer_guid;
      $supplier_guid = $row->supplier_guid;
      $supplier_code = $row->supplier_code;

      if($customer_guid == '' || $customer_guid == 'null' || $customer_guid == null )
      {
        $data = array(
          'para1' => 'false',
          'msg' => 'Invalid Retailer.',
        ); 

        echo json_encode($data);  
        exit(); 
      }

      if($supplier_guid == '' || $supplier_guid == 'null' || $supplier_guid == null )
      {
        $data = array(
          'para1' => 'false',
          'msg' => 'Invalid Supplier.',
        ); 
           
        echo json_encode($data);  
        exit(); 
      }

      if($supplier_code == '' || $supplier_code == 'null' || $supplier_code == null )
      {
        $data = array(
          'para1' => 'false',
          'msg' => 'Invalid Code.',
        ); 
           
        echo json_encode($data);  
        exit(); 
      }

      $check_details = $this->db->query("SELECT * FROM lite_b2b.einv_auto_settings WHERE customer_guid = '$customer_guid' AND supplier_guid = '$supplier_guid' AND supplier_code = '$supplier_code'")->result_array();

      if(count($check_details) == 0)
      {
        $data = array(
          'para1' => 'false',
          'msg' => 'Error Find Data',
        );    
        echo json_encode($data);  
        exit(); 
      }
      
      $delete_data = $this->db->query("DELETE FROM lite_b2b.einv_auto_settings WHERE customer_guid = '$customer_guid' AND supplier_guid = '$supplier_guid' AND supplier_code = '$supplier_code'");

      $error = $this->db->affected_rows();

      if($error > 0)
      {
        $i++;
      }

    }
  
    if($array_count == $i)
    {
      $data = array(
         'para1' => 'true',
         'msg' => 'Total deleted '. $i . ' of '. $array_count,
      );    
      echo json_encode($data);   
      exit();
    }
    else
    {   
      $data = array(
      'para1' => 'false',
      'msg' => 'Total deleted '. $i . ' of '. $array_count,
      );    
      echo json_encode($data);  
      exit(); 
    }
  }

}
