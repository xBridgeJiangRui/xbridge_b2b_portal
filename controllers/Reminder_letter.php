<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reminder_letter extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Acc_model');
    $this->load->library('form_validation');        
    $this->load->library('datatables');
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0); 
  }

  public function demand_list()
  {
    if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login() && in_array('IAVA',$_SESSION['module_code']))
    {
        $reminder_config_status = $this->db->query("SELECT * FROM lite_b2b.reminder_config WHERE type = 'Reminder_demand_letter' AND code = 'RMDDMDLTR'");

        $check_demand_letter = $this->db->query("SELECT COUNT(batch_no) AS group_batch_no, CONCAT(batch_no,' - ',COUNT(batch_no)) AS concat_value
        FROM `lite_b2b`.`reminder_demand_letter` GROUP BY batch_no ASC
        HAVING group_batch_no > 1")->result_array();

        $check_demand_letter_invoices = $this->db->query("SELECT batch_no, COUNT(batch_no) AS count_tt , CONCAT(batch_no,' - ',COUNT(batch_no)) AS concat_value FROM
        (
        SELECT supplier_guid,batch_no,COUNT(batch_no) FROM lite_b2b.`reminder_demand_letter_invoices`
        GROUP BY batch_no,supplier_guid
        )a
        GROUP BY a.batch_no
        HAVING count_tt > 1")->result_array();
        
        $data = array (
          'sync_status' => $reminder_config_status->row('value'),
          'latest_sync_on' => $reminder_config_status->row('updated_at'),
          'check_demand_letter' => count($check_demand_letter),
          'check_demand_letter_invoices' => count($check_demand_letter_invoices),
        );

        $this->load->view('header');
        $this->load->view('query_outstanding/reminder_demand', $data);  
        $this->load->view('footer');
    }
    else
    {
        $this->session->set_flashdata('message', 'Session Expired! Please relogin');
        redirect('#');
    }
  }

  public function demand_tb()
  {
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0); 

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
        0=>'guid',
        1=>'guid',
        2=>'batch_no',
        3=>'acc_name',
        4=>'supplier_name',
        5=>'email',
        6=>'amount',
        7=>'letter_date',
        8=>'first_send',
        9=>'second_send',
        10=>'next_send_date',
        11=>'status_naming',
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

    $sql = "SELECT a.*, IF(a.first_send = '1980-01-01', '' , DATE_ADD(a.first_send,INTERVAL 14 DAY )) AS next_send_date, IF(a.status = '3', 'Paid', IF(a.status ='4','No Paid', IF(a.status IN ('8','9'), 'Failed To Send',IF(a.status IN ('88','99'), 'Ready To Send', IF(a.status = '5', 'New Batch', 'Pending'))))) AS status_naming, IF(a.status IN ('0''99') , 'DMD_1ST', IF(a.status IN ('2','88') , 'DMD_FINAL', IF(a.first_send = '1980-01-01','DMD_1ST',IF(a.second_send = '1980-01-01','DMD_FINAL','DMD_FINAL')))) AS mail_type, IFNULL(b.batch_no,'') AS valid_demand_letter FROM lite_b2b.`reminder_demand_letter` a LEFT JOIN lite_b2b.`reminder_demand_letter_invoices` b
    ON a.batch_no = b.batch_no AND a.guid = b.letter_guid GROUP BY a.guid";
    
    $query = "SELECT * FROM ( ".$sql." ) aa ".$like_first_query.$like_second_query.$order_query.$limit_query;

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
        $nestedData['guid'] = $row->guid;
        $nestedData['customer_guid'] = $row->customer_guid;
        $nestedData['supplier_guid'] = $row->supplier_guid;
        $nestedData['acc_name'] = $row->acc_name;
        $nestedData['supplier_name'] = $row->supplier_name;
        $nestedData['address'] = $row->address;
        $nestedData['email'] = $row->email;
        $nestedData['amount'] = $row->amount;
        $nestedData['word_amount'] = $row->word_amount;
        $nestedData['created_at'] = $row->created_at;
        $nestedData['status'] = $row->status;
        $nestedData['letter_date'] = $row->letter_date;
        $nestedData['next_send_date'] = $row->next_send_date;
        $nestedData['status_naming'] = $row->status_naming;
        $nestedData['mail_type'] = $row->mail_type;
        $nestedData['cur_date'] = date("Y-m-d");
        $nestedData['batch_no'] = $row->batch_no;
        $nestedData['valid_demand_letter'] = $row->valid_demand_letter;

        if($row->first_send == '0000-00-00')
        {
          $nestedData['first_send'] = '';
        }
        else
        {
          $nestedData['first_send'] = $row->first_send;
        }

        if($row->second_send == '0000-00-00')
        {
          $nestedData['second_send'] = '';
        }
        else
        {
          $nestedData['second_send'] = $row->second_send;
        }

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

  public function demand_appendix_tb()
  {
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0); 

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
      0 => 'guid',
      1 => 'batch_no',
      2 => 'docdate',
      3 => 'docno',
      4 => 'outstanding',
      5 => 'created_at',
      6 => 'created_by',
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

    $sql = "SELECT a.* FROM lite_b2b.reminder_demand_letter_invoices a ";
    
    $query = "SELECT * FROM ( ".$sql." ) aa ".$like_first_query.$like_second_query.$order_query.$limit_query;

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
        $nestedData['guid'] = $row->guid;
        $nestedData['letter_guid'] = $row->letter_guid;
        $nestedData['supplier_guid'] = $row->supplier_guid;
        $nestedData['batch_no'] = $row->batch_no;
        $nestedData['docdate'] = $row->docdate;
        $nestedData['docno'] = $row->docno;
        $nestedData['duedate'] = $row->duedate;
        $nestedData['outstanding'] = $row->outstanding;
        $nestedData['date_1'] = $row->date_1;
        $nestedData['date_2'] = $row->date_2;
        $nestedData['created_at'] = $row->created_at;
        $nestedData['created_by'] = $row->created_by;

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

  public function update_letter()
  {
    $guid = $this->input->post("guid");
    $address= $this->input->post("address");
    $email= $this->input->post("email");
    $amount= $this->input->post("amount");
    $word_amount= $this->input->post("word_amount");

    $check_data = $this->db->query("SELECT * FROM lite_b2b.reminder_demand_letter WHERE guid = '$guid'")->result_array();

    if(count($check_data) == 0)
    {
      $data = array(
        'para1' => 'false',
        'msg' => 'No Data Found',
      );    
      echo json_encode($data); 
      exit();
    }

    $data = array(
      'address' => $address,
      'email' => $email,
      'amount' => $amount,
      'word_amount' => $word_amount,
    );

    $this->db->where('guid', $guid);
    $this->db->update('reminder_demand_letter', $data);

    $error = $this->db->affected_rows();

    if($error > 0){

      $data = array(
        'para1' => 0,
        'msg' => 'Update Successfully',

        );    
        echo json_encode($data);   
    }
    else
    {   
        $data = array(
        'para1' => 1,
        'msg' => 'No Data Update.',

        );    
        echo json_encode($data);   
    }
  }

  public function update_send_letter()
  {
    $guid = $this->input->post("guid");
    //$status= $this->input->post("status");

    $check_data = $this->db->query("SELECT * FROM lite_b2b.reminder_demand_letter WHERE guid = '$guid' ");

    if($check_data->num_rows() == 0)
    {
      $data = array(
        'para1' => 'false',
        'msg' => 'No Data Found',
      );    
      echo json_encode($data); 
      exit();
    }

    $status = $check_data->row('status'); 

    if($status == '0')
    {
      $update_status = '99';
    }

    if($status == '1')
    {
      $update_status = '88';
    }
    
    $update_data = $this->db->query("UPDATE lite_b2b.reminder_demand_letter SET `status` = '$update_status' WHERE `guid` = '$guid' ");

    $error = $this->db->affected_rows();

    if($error > 0){

      $data = array(
        'para1' => 0,
        'msg' => 'Success Set Send Status.',

        );    
        echo json_encode($data);   
    }
    else
    {   
        $data = array(
        'para1' => 1,
        'msg' => 'No Data Update.',

        );    
        echo json_encode($data);   
    }

  }

  public function update_send_letter_by_batch()
  {
    $details = $this->input->post('details');
    $implode_guid = json_decode(json_encode($details),true);
    $details = json_encode($details);
    $details = json_decode($details);
    //$status= $this->input->post("status");
    //print_r($details); die;
    $check_error = 0;
    $i = 0;
    $msg = '';
    $count_details = count($details);

    foreach($details as $row)
    {
      $guid = $row->guid;

      $check_data = $this->db->query("SELECT * FROM lite_b2b.reminder_demand_letter WHERE guid = '$guid' ");

      if($check_data->num_rows() == 0)
      {
        continue;
      }

      $status = $check_data->row('status'); 

      if($status == '0')
      {
        $update_status = '99';
      }

      if($status == '1')
      {
        $update_status = '88';
      }
      
      $update_data = $this->db->query("UPDATE lite_b2b.reminder_demand_letter SET `status` = '$update_status' WHERE `guid` = '$guid' ");

      $i++;
    }

    if($count_details == $i){

      $data = array(
        'para1' => 'true',
        'msg' => $i.' Updated Data total of '.$count_details,

        );    
        echo json_encode($data);   
    }
    else
    {   
        $data = array(
        'para1' => 'false',
        'msg' => $i.' Updated Data total of '.$count_details,

        );    
        echo json_encode($data);   
    }
  }

  public function update_demand_config()
  {
    $update = $this->db->query("UPDATE lite_b2b.reminder_config
    SET `value` = 'Yes',
    updated_at = NOW()
    WHERE `code` = 'RMDDMDLTR'
    AND `type` = 'Reminder_demand_letter'
    AND isactive = '1'");

    $error = $this->db->affected_rows();

    if($error > 0){

      $data = array(
        'para1' => 0,
        'msg' => 'Update Success.',

        );    
        echo json_encode($data);   
    }
    else
    {   
        $data = array(
        'para1' => 1,
        'msg' => 'No Data Update.',

        );    
        echo json_encode($data);   
    }
  }

  public function remove_appendix()
  {
    $guid = $this->input->post("guid");

    $check_data = $this->db->query("SELECT * FROM lite_b2b.reminder_demand_letter_invoices WHERE `guid`= '$guid' ")->result_array();

    if(count($check_data) == 0 )
    {
      $data = array(
        'para1' => 'false',
        'msg' => 'Data Not Found.',
      );    
      echo json_encode($data);   
      exit();
    }

    $delete_data = $this->db->query("DELETE FROM lite_b2b.reminder_demand_letter_invoices WHERE `guid`= '$guid' ");

    $error = $this->db->affected_rows();
    
    if($error > 0)
    {
      $data = array(
        'para1' => 'True',
        'msg' => 'Delete Successful.',
      );    
      echo json_encode($data);   
      exit();
    }
    else
    {
      $data = array(
        'para1' => 'false',
        'msg' => 'Process Error.',
      );    
      echo json_encode($data);   
      exit();
    }

  }
}
?>
