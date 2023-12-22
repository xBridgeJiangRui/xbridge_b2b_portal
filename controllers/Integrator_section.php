<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Integrator_section extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('Export_model');
        $this->load->library('datatables');
        $this->load->library('session');
        $this->load->model('Datatable_model');
        $this->load->model('Send_email_model');
    }

    public function index()
    {
        if($this->session->userdata('loginuser') == true)
        { 
            $customer_guid = $_SESSION['customer_guid'];

            $get_config_settings = $this->db->query("SELECT
            a.customer_guid,
            a.integration_name,
            a.integration_guid,
            b.setting_guid
            FROM 
            lite_b2b.integration_config a
            INNER JOIN integration_settings b ON a.config_guid = b.config_guid
            WHERE 
            b.setting_type ='Supplier_Sync'");

            $get_supplier_debtor_code = $this->db->query("SELECT a.acc_code,a.supplier_name FROM lite_b2b.set_supplier a ORDER BY a.supplier_name ASC");

            $get_supplier_period_code = $this->db->query("SELECT aa.* FROM ( SELECT LEFT(updated_at,7) AS period_code FROM lite_b2b.set_supplier GROUP BY LEFT(updated_at,7) ) aa ORDER BY aa.period_code DESC ");

            $data = array(
                'get_config_settings' => $get_config_settings->result(),
                'get_code_list' => $get_supplier_debtor_code,
                'get_period_list' => $get_supplier_period_code,
            );
            //print_r($sessiondata);die;
            //$this->session->set_userdata($data);
            $this->load->view('header');
            $this->load->view('integrator/supplier_list.php', $data);      
            $this->load->view('footer');
        }
        else
        {
            redirect('#');
        }
    }

    public function supplier_tb()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login()) 
        {
            $doc = 'supplier_table';
            $user_guid = $_SESSION['user_guid'];
            $customer_guid = $_SESSION['customer_guid'];
            $select_acc_code = $this->input->post("select_acc_code");
            $select_period_code = $this->input->post("select_period_code");

            if(count(array_unique($select_acc_code)) > 0)
            {
                $implode_code = "'".implode("','",$select_acc_code)."'";
                $condition_1 = "AND a.acc_code IN ($implode_code) ";
            }
            else
            {
                $condition_1 = '';
            }

            if(count(array_unique($select_period_code)) > 0)
            {
                $implode_period = "'".implode("','",$select_period_code)."'";
                $condition_2 = "AND a.updated_at IN ($implode_period) ";
            }
            else
            {
                $condition_2 = '';
            }

            $query_data = "SELECT a.supplier_guid, a.supplier_name, a.reg_no, a.name_reg, a.isactive, a.suspended, a.gst_no, a.acc_code, a.payment_term,a.created_at,a.created_by,a.updated_at,a.updated_by
            FROM lite_b2b.set_supplier a
            WHERE a.isactive IN ('1','0')
            $condition_1
            $condition_2
            ORDER BY a.supplier_name ASC";

            $sql = "SELECT * FROM (
                $query_data
            ) zzz ";
            // echo $sql; die;
            // echo $this->db->last_query();die;
            
            $query = $this->Datatable_model->datatable_main($sql,'',$doc);
            // echo $this->db->last_query(); die;
            $fetch_data = $query->result();
            $data = array();
            if (count($fetch_data) > 0) {
                foreach ($fetch_data as $row) {
                    $tab = array();

                    $tab['supplier_guid'] = $row->supplier_guid;
                    $tab['supplier_name'] = $row->supplier_name;
                    $tab['reg_no'] = $row->reg_no;
                    $tab['name_reg'] = $row->name_reg;
                    $tab['isactive'] = $row->isactive;
                    $tab['suspended'] = $row->suspended;
                    $tab['gst_no'] = $row->gst_no;
                    $tab['acc_code'] = $row->acc_code;
                    $tab['payment_term'] = $row->payment_term;
                    $tab['created_at'] = $row->created_at;
                    $tab['created_by'] = $row->created_by;
                    $tab['updated_at'] = $row->updated_at;
                    $tab['updated_by'] = $row->updated_by;

                    $data[] = $tab;
                }
            } else {
                $data = '';
            }

            $output = array(
                "draw"                =>     intval($_POST["draw"]),
                "recordsTotal"        =>     intval($this->Datatable_model->general_get_all_data($sql, $doc)),
                "recordsFiltered"     =>     intval($this->Datatable_model->general_get_filtered_data($sql, $doc)),
                "data"                =>     $data
            );

            echo json_encode($output);
        } 
        else 
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function integrator_trigger() //for the trigger button
    {   
        $supplier_guid = $this->input->post('supplier_guid');
        $supplier_name = $this->input->post('supplier_name');
        $reg_no = $this->input->post('reg_no');
        $name_reg = $this->input->post('name_reg');
        $isactive = $this->input->post('isactive');
        $suspended = $this->input->post('suspended');
        $gst_no = $this->input->post('gst_no');
        $acc_code = $this->input->post('acc_code');
        $payment_term = $this->input->post('payment_term');
        $select_integration = $this->input->post('select_integration');

        $get_supplier = $this->db->query("SELECT a.supplier_guid, a.supplier_name, a.reg_no, a.name_reg, a.isactive, a.suspended, a.gst_no, a.acc_code, a.payment_term
        FROM lite_b2b.set_supplier 
        WHERE supplier_guid = '$supplier_guid' ")->result_array();

        $get_url = $this->db->query("SELECT a.url_trigger FROM lite_b2b.integration_settings a WHERE a.setting_guid = '$select_integration'")->row('url_trigger');

        if($get_url == '' || $get_url == 'null' || $get_url == null)
        {
            $response = array(
                'status' => 'false',
                'message' => 'Invalid URL',
            );
            echo json_encode($response);
            die;
        }

        if(count($get_supplier) == 0)
        {
            $response = array(
                'status' => 'false',
                'message' => 'Invalid Supplier Data',
            );
            echo json_encode($response);
            die;
        }

        $data = $get_supplier;

        // echo json_encode($data);die;

        $to_shoot_url = $get_url;

        // echo $to_shoot_url;die;

        $cuser_name = 'ADMIN';
        $cuser_pass = '1234';

        $ch = curl_init($to_shoot_url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-API-KEY: " . "CODEX1234" ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-KEY: 123456"));
        curl_setopt($ch, CURLOPT_USERPWD, "$cuser_name:$cuser_pass");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        $result = curl_exec($ch);
        $output = json_decode($result);
        // $status = json_encode($output);
        // print_r($output->result);die;
        //echo $result;die; //testing use this
        //close connection
        curl_close($ch); 

        if($output->status == 'true')
        {
            $status = $output->status;
            $return_message = $output->message;
            $total_count = $output->total_count;
            $total_insert = $output->total_insert;
            $total_failed = $output->total_failed;
            $total_duplicate = $output->total_duplicate;
            $success_array = $output->success_array;

            $message = $return_message . '\nTotal: ' . $total_count . '\nInserted: ' . $total_insert . '\nFailed: ' . $total_failed . '\nDuplicate: ' . $total_duplicate ;


        }
        else
        {
            $status = $output->status;
            $return_message = $output->message;
            $total_count = $output->total_count;
            $total_insert = $output->total_insert;
            $total_failed = $output->total_failed;
            $total_duplicate = $output->total_duplicate;
            $success_array = $output->success_array;
            $failed_array = $output->failed_array;
            $duplicate_array = $output->duplicate_array;

            $message = $return_message . '\nTotal: ' . $total_count . '\nInserted: ' . $total_insert . '\nFailed: ' . $total_failed . '\nDuplicate: ' . $total_duplicate ;
        }

        if(count($success_array) > 0)
        {
            foreach($success_array as $row)
            {
                $data_log = array();

                $data_log[] = array(
                    'integration_guid' => $get_integration_guid,
                    'supplier_guid' => $row->supplier_guid,
                    'int_supplier_guid' => $row->new_supplier_guid,
                    'acc_code_status' => '0',
                    'status' => '1',
                    'created_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'created_by' => 'b2b_system',
                    'updated_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'updated_by' => 'b2b_system',
                );
                $this->db->replace_batch('lite_b2b.integration_supplier_info', $data_log);
            }
        }

        if(count($failed_array) > 0)
        {
            foreach($failed_array as $row)
            {
                $data_log = array();
                
                $data_log[] = array(
                    'integration_guid' => $get_integration_guid,
                    'supplier_guid' => $row->supplier_guid,
                    'int_supplier_guid' => '',
                    'acc_code_status' => '0',
                    'status' => '9',
                    'created_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'created_by' => 'b2b_system',
                    'updated_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'updated_by' => 'b2b_system',
                );
                $this->db->replace_batch('lite_b2b.integration_supplier_info', $data_log);
            }
        }

        if(count($duplicate_array) > 0)
        {
            foreach($duplicate_array as $row)
            {
                $data_log = array();
                
                $data_log[] = array(
                    'integration_guid' => $get_integration_guid,
                    'supplier_guid' => $row->supplier_guid,
                    'int_supplier_guid' => '',
                    'acc_code_status' => '0',
                    'status' => '9',
                    'created_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'created_by' => 'b2b_system',
                    'updated_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'updated_by' => 'b2b_system',
                );
                $this->db->replace_batch('lite_b2b.integration_supplier_info', $data_log);
            }
        }

        $response = array(
            'status' => $status,
            'message' => $message,
        );
        echo json_encode($response);
        die;
        // echo 1;die;

    } 

    public function integrator_trigger_batch()
    {
        $selection_integration_batch = $this->input->post('selection_integration_batch');
        $details = $this->input->post('details');

        // print_r($selection_integration_batch); die;

        $get_integration_info = $this->db->query("SELECT a.url_trigger,a.integration_guid FROM lite_b2b.integration_settings a WHERE a.setting_guid = '$selection_integration_batch'");

        $get_url = $get_integration_info->row('url_trigger');

        $get_integration_guid = $get_integration_info->row('integration_guid');

        if($get_url == '' || $get_url == 'null' || $get_url == null)
        {
            $response = array(
                'status' => 'false',
                'message' => 'Invalid URL',
            );
            echo json_encode($response);
            die;
        }

        if($get_integration_guid == '' || $get_integration_guid == 'null' || $get_integration_guid == null)
        {
            $response = array(
                'status' => 'false',
                'message' => 'Invalid Integration GUID',
            );
            echo json_encode($response);
            die;
        }

        if(count($details) == 0)
        {
            $response = array(
                'status' => 'false',
                'message' => 'Invalid Data',
            );
            echo json_encode($response);
            die;
        }

        //echo json_encode($details);die;

        $to_shoot_url = $get_url;
        // echo $to_shoot_url;die;
        // $block = $this->db->query("SELECT * FROM set_setting WHERE module_name = 'CONSIGNMENT' AND code = 'CONS' LIMIT 1");

        $cuser_name = 'ADMIN';
        $cuser_pass = '1234';

        $ch = curl_init($to_shoot_url);
       // curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-API-KEY: " . "CODEX1234" ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-KEY: 123456"));
        curl_setopt($ch, CURLOPT_USERPWD, "$cuser_name:$cuser_pass");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($details));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        $result = curl_exec($ch);
        $output = json_decode($result);
        // $status = json_encode($output);
        // print_r($output->result);die;
        // echo $result;die; //testing use this
        //close connection
        curl_close($ch); 

        if($output->status == 'true')
        {
            $status = $output->status;
            $return_message = $output->message;
            $total_count = $output->total_count;
            $total_insert = $output->total_insert;
            $total_failed = $output->total_failed;
            $total_duplicate = $output->total_duplicate;
            $success_array = $output->success_array;

            $message = $return_message . '\nTotal: ' . $total_count . '\nInserted: ' . $total_insert . '\nFailed: ' . $total_failed . '\nDuplicate: ' . $total_duplicate ;


        }
        else
        {
            $status = $output->status;
            $return_message = $output->message;
            $total_count = $output->total_count;
            $total_insert = $output->total_insert;
            $total_failed = $output->total_failed;
            $total_duplicate = $output->total_duplicate;
            $success_array = $output->success_array;
            $failed_array = $output->failed_array;
            $duplicate_array = $output->duplicate_array;

            $message = $return_message . '\nTotal: ' . $total_count . '\nInserted: ' . $total_insert . '\nFailed: ' . $total_failed . '\nDuplicate: ' . $total_duplicate ;
        }

        if(count($success_array) > 0)
        {
            foreach($success_array as $row)
            {
                $data_log = array();

                $data_log[] = array(
                    'integration_guid' => $get_integration_guid,
                    'supplier_guid' => $row->supplier_guid,
                    'int_supplier_guid' => $row->new_supplier_guid,
                    'acc_code_status' => '0',
                    'status' => '1',
                    'created_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'created_by' => 'b2b_system',
                    'updated_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'updated_by' => 'b2b_system',
                );
                $this->db->replace_batch('lite_b2b.integration_supplier_info', $data_log);
            }
        }

        if(count($failed_array) > 0)
        {
            foreach($failed_array as $row)
            {
                $data_log = array();
                
                $data_log[] = array(
                    'integration_guid' => $get_integration_guid,
                    'supplier_guid' => $row->supplier_guid,
                    'int_supplier_guid' => '',
                    'acc_code_status' => '0',
                    'status' => '9',
                    'created_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'created_by' => 'b2b_system',
                    'updated_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'updated_by' => 'b2b_system',
                );
                $this->db->replace_batch('lite_b2b.integration_supplier_info', $data_log);
            }
        }

        if(count($duplicate_array) > 0)
        {
            foreach($duplicate_array as $row)
            {
                $data_log = array();
                
                $data_log[] = array(
                    'integration_guid' => $get_integration_guid,
                    'supplier_guid' => $row->supplier_guid,
                    'int_supplier_guid' => '',
                    'acc_code_status' => '0',
                    'status' => '8',
                    'created_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'created_by' => 'b2b_system',
                    'updated_at' => $this->db->query("SELECT NOW() as today")->row('today'),
                    'updated_by' => 'b2b_system',
                );
                $this->db->replace_batch('lite_b2b.integration_supplier_info', $data_log);
            }
        }

        $response = array(
            'status' => $status,
            'message' => $message,
        );
        echo json_encode($response);
        die;
        // echo 1;die;
    } 

    public function supplier_integration_tb()
    {
        if ($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login()) 
        {
            $doc = 'int_supplier_table';
            $user_guid = $_SESSION['user_guid'];
            $customer_guid = $_SESSION['customer_guid'];
            $integration_guid = $this->input->post("integration_guid");

            if(count(array_unique($select_acc_code)) > 0)
            {
                $implode_code = "'".implode("','",$select_acc_code)."'";
                $condition_1 = "WHERE a.integration_guid = '$integration_guid' ";
            }
            else
            {
                $condition_1 = '';
            }

            $query_data = "SELECT b.supplier_name,b.reg_no,b.acc_code,a.supplier_guid,a.int_supplier_guid,
            IF(a.status = '1', 'Success', IF(a.status = '8', 'Duplicate', IF(a.status = '9', 'Error', ''))) AS naming_status,
            a.created_at
            FROM lite_b2b.integration_supplier_info a 
            INNER JOIN lite_b2b.set_supplier b
            ON a.supplier_guid = b.supplier_guid
            $condition_1
            ORDER BY b.supplier_name ASC";

            $sql = "SELECT * FROM (
                $query_data
            ) zzz ";
            // echo $sql; die;
            // echo $this->db->last_query();die;
            
            $query = $this->Datatable_model->datatable_main($sql,'',$doc);
            // echo $this->db->last_query(); die;
            $fetch_data = $query->result();
            $data = array();
            if (count($fetch_data) > 0) {
                foreach ($fetch_data as $row) {
                    $tab = array();

                    $tab['supplier_guid'] = $row->supplier_guid;
                    $tab['supplier_name'] = $row->supplier_name;
                    $tab['int_supplier_guid'] = $row->int_supplier_guid;
                    $tab['reg_no'] = $row->reg_no;
                    $tab['acc_code'] = $row->acc_code;
                    $tab['naming_status'] = $row->naming_status;
                    $tab['created_at'] = $row->created_at;

                    $data[] = $tab;
                }
            } else {
                $data = '';
            }

            $output = array(
                "draw"                =>     intval($_POST["draw"]),
                "recordsTotal"        =>     intval($this->Datatable_model->general_get_all_data($sql, $doc)),
                "recordsFiltered"     =>     intval($this->Datatable_model->general_get_filtered_data($sql, $doc)),
                "data"                =>     $data
            );

            echo json_encode($output);
        } 
        else 
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }
}
?>