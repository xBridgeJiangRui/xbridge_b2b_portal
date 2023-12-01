<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_modal extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('datatables');
        $this->load->library('session');
    }

    public function index()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $this->session->userdata('user_logs') == $this->panda->validate_login()) {

            $get_table = $this->db->query("SELECT * FROM lite_b2b.notification_modal nm WHERE nm.isactive = '1'");

            $get_acc = $this->db->query("SELECT * FROM lite_b2b.acc ORDER BY acc_name ASC");

            $data = array(
                'get_table' => $get_table->result(),
                'get_acc' => $get_acc->result(),
            );

            $this->load->view('header');
            $this->load->view('notification/modal_subscribe',$data);
            $this->load->view('footer');

        } else {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function notification_list_tb()
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

            // 0 =>'notification_guid',
            0 =>'description',
            1 =>'created_at',
            2 =>'created_by',
            3 =>'updated_at',
            4 =>'updated_by',
            //5 =>'isactive',
        );

        if (!isset($valid_columns[$col])) {
            $order = null;
        } else {
            $order = $valid_columns[$col];
        }

        if ($order != null) {
            $order_query = "ORDER BY " . $order . "  " . $dir;
        }

        $like_first_query = '';
        $like_second_query = '';

        if (!empty($search)) {
            $x = 0;
            foreach ($valid_columns as $sterm) {
                if ($x == 0) {
                    $like_first_query = "WHERE $sterm LIKE '%" . $search . "%'";
                } else {
                    $like_second_query .= "OR $sterm LIKE '%" . $search . "%'";
                }
                $x++;
            }
        }

        $limit_query = " LIMIT " . $start . " , " . $length;

        $sql = "SELECT * FROM lite_b2b.notification_modal WHERE isactive = '1'";

        $query = "SELECT * FROM ( " . $sql . " ) aa " . $like_first_query . $like_second_query . $order_query . $limit_query;
        $result = $this->db->query($query);

        if (!empty($search)) {
            $query_filter = "SELECT * FROM ( " . $sql . " ) a " . $like_first_query . $like_second_query;
            $result_filter = $this->db->query($query_filter)->result();
            $total = count($result_filter);
        } else {
            $total = $this->db->query($sql)->num_rows();
        }

        $data = array();

        foreach ($result->result() as $row) {
            $nestedData['notification_guid'] = $row->notification_guid;
            $nestedData['description'] = $row->description;
            $nestedData['created_at'] = $row->created_at;
            $nestedData['created_by'] = $row->created_by;
            $nestedData['updated_at'] = $row->updated_at;
            $nestedData['updated_by'] = $row->updated_by;
            $nestedData['isactive'] = $row->isactive;
        
            // Rest of the nestedData assignments remain the same
        
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

    public function notification_modal_sub()
    {
        $notification_guid = $this->input->post("notification_guid");

        $data = $this->db->query("SELECT a.acc_name, nms.created_at, nms.created_by, nms.updated_at, nms.updated_by, nms.notification_subscribe_guid
            FROM lite_b2b.notification_modal AS nm
            INNER JOIN lite_b2b.notification_modal_subscribe AS nms
            ON nm.notification_guid = nms.notification_guid
            INNER JOIN lite_b2b.acc AS a
            ON nms.customer_guid = a.acc_guid
            WHERE nm.notification_guid = '$notification_guid'");

        $output = array(
            "data" => $data->result(),
        );

        echo json_encode($output);
    }

    public function modal_subscribe_create()
    {
        $user_guid = $_SESSION['user_guid'];
        $user_id = $this->db->query("SELECT a.user_id FROM set_user a WHERE a.user_guid ='$user_guid'")->row('user_id');

        $notification_des = $this->input->post('notification_des');
        $add_retailer = $this->input->post('add_retailer');

        $check_data = $this->db->query("SELECT nms.* from lite_b2b.notification_modal_subscribe nms WHERE nms.notification_guid = '$notification_des' AND nms.customer_guid = '$add_retailer'")->result_array();

        if (count($check_data) > 0) {
            $data = array(
                'para1' => 'false',
                'msg' => 'Retailer Name already exist',
            );
        } else {
            $data = array(
                'notification_subscribe_guid' => $this->db->query("SELECT REPLACE(UPPER(UUID()),'-','') AS uuid")->row('uuid'),
                'notification_guid' => $notification_des,
                'customer_guid' => $add_retailer,
                'created_at' => $this->db->query("SELECT NOW() as updated_at")->row('updated_at'),
                'created_by' => $user_id,
                'updated_at' => $this->db->query("SELECT NOW() as updated_at")->row('updated_at'),
                'updated_by' => $user_id,   
            );
            
            $this->db->insert('lite_b2b.notification_modal_subscribe', $data);
            $error = $this->db->affected_rows();

            if ($error > 0) {
                $data = array(
                    'para1' => 'True',
                    'msg' => 'Success: Data inserted successfully',
                );
            } else {
                $data = array(
                    'para1' => 'false',
                    'msg' => 'Error: Failed to insert data',
                );
            }
        }

        echo json_encode($data);  
        exit(); 
    }

    public function remove_modal_subscribe()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $this->session->userdata('user_logs') == $this->panda->validate_login()) 
        {
            $user_guid = $_SESSION['user_guid'];
            $user_id = $this->db->query("SELECT a.user_id FROM set_user a WHERE a.user_guid ='$user_guid'")->row('user_id');

            $details = $this->input->post('details');

            //print_r($details);die;

            $count_value = count($details);

            $array_data = json_decode(json_encode($details));

            $i = 0;

            foreach($array_data as $row)
            {
                $delete_guid = $row->d_guid;
                //echo $delete_guid;die;

                $check_data_relationship = $this->db->query("SELECT nms.* FROM lite_b2b.notification_modal_subscribe nms WHERE nms.notification_subscribe_guid = '$delete_guid' ")->result_array();
                //print_r($check_data_relationship);die;

                if(count($check_data_relationship) == 0 )
                {
                    continue;
                }

                $delete_child = $this->db->query("DELETE FROM lite_b2b.notification_modal_subscribe WHERE notification_subscribe_guid = '$delete_guid' ");

                $error = $this->db->affected_rows();

                if($error > 0)
                {
                    $i++;
                }
            
            }

            if($count_value == $i)
            {
                $data = array(
                'para1' => 'True',
                'msg' => 'Total Data deleted successfully : ' .$i. ' / ' .$count_value,
                );    
                echo json_encode($data);   
                exit();
            }
            else
            {   
                $data = array(
                'para1' => 'false',
                'msg' => 'Total Data deleted unsuccessfully : ' .$i. ' / ' .$count_value,
                );    
                echo json_encode($data);  
                exit(); 
            }
        } 
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

}
?>

