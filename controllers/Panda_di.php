<?php
class panda_di extends CI_Controller
{
   public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper(array('form', 'url'));
        $this->load->database();
        $this->load->library('pagination');
        $this->load->library('form_validation');


        //load the department_model
        $this->load->model('GR_model');
        $this->jasper_ip = $this->file_config_b2b->file_path_name($this->session->userdata('customer_guid'),'web','general_doc','jasper_invoice_ip','GDJIIP');
    }

    public function index()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login())
        {
            $setsession = array(
                'frommodule' => 'panda_di',
                );
            $this->session->set_userdata($setsession);

            if($_REQUEST['loc'] == '')
            {   
                redirect('login_c/location');
            };

            if(isset($_SESSION['from_other']) == 0 )
            {

                redirect('general/view_status?status='.$_REQUEST['status'].'&loc='.$_REQUEST['loc'].'&p_f=&p_t=&e_f=&e_t=&r_n=');
            }
            else
            {
                if($_REQUEST['status'] == '')
                {
                    unset($_SESSION['from_other']);
                    redirect('panda_di?loc='.$_REQUEST['loc']);
                };
                redirect('general/view_status?status='.$_REQUEST['status'].'&loc='.$_REQUEST['loc'].'&p_f=&p_t=&e_f=&e_t=&r_n=');
            };
        }
         else
        {
             $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }


    public function index_old()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login())
        {
            $setsession = array(
                'frommodule' => 'panda_di',
                );
            $this->session->set_userdata($setsession);

            if($_REQUEST['loc'] == '')
            {   
                redirect('login_c/location');
            };


            if($_SESSION['user_group_name'] != 'SUPER_ADMIN' && $_REQUEST['loc'] != 'HQ')
            {
                $result = $this->db->query("SELECT customer_guid, refno, loc_group, dono, invno,  date_format(docdate, '%Y-%m-%d %W') as docdate,date_format(grdate, '%Y-%m-%d %W') grdate,code, name, total, gst_tax_sum, tax_code_purchase, total_include_tax, doc_name_reg, status from b2b_summary.dischememain where customer_guid = '".$_SESSION['customer_guid']."' and loc_group = '".$_REQUEST['loc']."' and code IN (".$_SESSION['query_supcode'].")");
            };

            if($_SESSION['user_group_name'] != 'SUPER_ADMIN'  && $_REQUEST['loc'] == 'HQ')
            {
                $result = $this->db->query("SELECT customer_guid, refno, loc_group, dono, invno,  date_format(docdate, '%Y-%m-%d %W') as docdate,date_format(grdate, '%Y-%m-%d %W') grdate,code, name, total, gst_tax_sum, tax_code_purchase, total_include_tax, doc_name_reg, status from b2b_summary.dischememain where customer_guid = '".$_SESSION['customer_guid']."'and code IN (".$_SESSION['query_supcode'].")");
            };

            if($_SESSION['user_group_name'] == 'SUPER_ADMIN')
            {
                $result = $this->db->query("SELECT customer_guid, refno, loc_group, dono, invno,  date_format(docdate, '%Y-%m-%d %W') as docdate,date_format(grdate, '%Y-%m-%d %W') grdate,code, name, total, gst_tax_sum, tax_code_purchase, total_include_tax, doc_name_reg, status from b2b_summary.grmain where customer_guid = '".$_SESSION['customer_guid']."' and loc_group = '".$_REQUEST['loc']."' ");
            };
            $data = array (
                'result' => $result,
            );
      
           /* $data = array (
                'result' => $this->db->query("SELECT customer_guid, refno, location, dono, invno, date_format(docdate, '%Y-%m-%d %W') as docdate,date_format(grdate, '%Y-%m-%d %W') grdate, code, name, total, gst_tax_sum, tax_code_purchase, total_include_tax, doc_name_reg, status from b2b_summary.grmain where customer_guid = '".$_SESSION['customer_guid']."' and location = '".$_REQUEST['loc']."'"),
            );*/
        //$this->GR_model->update_expired();
        //$this->GR_model->update_grn($branch_code,$customer,$user_guid);
        //load the department_view
        $this->load->view('header');
       /* $this->load->view('panda_menu_view.php');*/
        $this->load->view('di/panda_di_list_view',$data);
        $this->load->view('footer');
        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    /*
    to prevent user from cincai key in the refno based on url, 
    remember to join all back to user guid so that when they key by refno, it will check if the user is valid to query or not then will show result or not..
    */

    public function gr_child()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login())
        {
            $refno = $_REQUEST['trans'];
            $loc = $_REQUEST['loc'];
            $filename = base_url('/uploads/grmain/'.$refno.'.pdf');

            if(!file_exists($filename))
            {
                 
            };

            $data = array(
                'file_path' => $filename,
                'title' => 'Goods Received',
            );

            $this->load->view('header');       
            $this->load->view('gr/panda_gr_pdf',$data);
            $this->load->view('footer');
        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

 

    public function pdi_child()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login())
        {
            // echo 2;die;
            $this->panda->get_uri();
            $inv_refno = $_REQUEST['trans'];
            $loc = $_REQUEST['loc'];

            $customer_guid = $_SESSION['customer_guid'];
            $user_guid = $_SESSION['user_guid'];
            $from_module = $_SESSION['frommodule'];

            if (isset($_REQUEST['view_json'])) 
            {
                $view_json = $_REQUEST['view_json'];
            } else {
                $view_json = $this->db->query("SELECT json_view_doc_btn FROM lite_b2b.acc_settings WHERE customer_guid = '$customer_guid'")->row('json_view_doc_btn');
            }

            if(!in_array('!DISUPPMOV',$_SESSION['module_code']))
            {
                
                $this->db->query("UPDATE b2b_summary.discheme_taxinv set status = 'viewed' where customer_guid ='$customer_guid' and inv_refno = '$inv_refno' and status = '' ");

                $this->db->query("REPLACE into supplier_movement select 
                upper(replace(uuid(),'-','')) as movement_guid
                , '$customer_guid'
                , '$user_guid'
                , 'viewed_DI'
                , '$from_module'
                , '$inv_refno'
                , now()
                ");
            
            };
          
            
            $check_scode = $this->db->query("SELECT sup_code from b2b_summary.discheme_taxinv where inv_refno = '$inv_refno' and customer_guid = '".$_SESSION['customer_guid']."'")->row('sup_code');
            $check_scode = str_replace("/","+-+",$check_scode);
            // echo $check_scode;die;

            $parameter = $this->db->query("SELECT * from menu where module_link = '".$_SESSION['frommodule']."'");
            $type = $parameter->row('type');
            $code = $check_scode;

            $replace_var = $this->db->query("SELECT REPLACE(REPLACE(REPLACE(filename_format, 'type', '$type'), 'code', '$code'), 'refno' , '$inv_refno') AS query FROM menu where module_link = '".$_SESSION['frommodule']."'")->row('query');


            $virtual_path = $this->db->query("SELECT file_path FROM acc WHERE acc_guid = '".$_SESSION['customer_guid']."'")->row('file_path');
           
            // $filename = base_url($virtual_path.'/'.$replace_var.'.pdf');

            $file_config_final_path = $this->file_config_b2b->file_path($customer_guid,'web','general_doc','main_path','GDMP');

            $filename = $file_config_final_path.'/'.$replace_var.'.pdf';
            // echo $filename;die;
 
            $file_headers = @get_headers($filename);

            // $check_status = $this->db->query("SELECT refno, if(status = '', 'Pending', status) as status, rejected_remark from b2b_summary.discheme_taxinv where inv_refno = '$inv_refno' and customer_guid = '".$_SESSION['customer_guid']."'");

// if($check_status->row('status') == 'Pending' || $check_status->row('status') == 'viewed' || $check_status->row('status') == 'printed' )
//             {
                
//                  if($_SESSION['user_group_name'] == 'CUSTOMER_ADMIN' || $_SESSION['user_group_name'] == 'CUSTOMER_ADMIN' )
//                 {
//                     $show_action_button = '0';
//                 }
//                 else
//                 {
//                     $show_action_button = '1';
//                 }
//             }
//             else
//             {
             
//                 $show_action_button = '0';   
//                 //echo $check_status->row('status'); echo  $show_action_button; die;
//             };


            // $check_url = $this->db->query("SELECT rest_url from acc where acc_guid = '".$_SESSION['customer_guid']."'")->row('rest_url');
            //     $to_shoot_url = $check_url."/pochild?table=pochild"."&refno=".$refno;

            // $ch = curl_init($to_shoot_url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $response = curl_exec($ch);

            //     if($response !== false) {
            //         echo "string";
            //     }   
            //     else
            //     {                     
            //         $this->session->set_flashdata("noconnection", '<div class="alert alert-warning fade in">Please Check Your Internet Connection</div>');
            //     }


            $data = array(
                'filename' => $filename,
                'file_headers' => $file_headers,
                'virtual_path' => $virtual_path,
                'title' => 'Display Incentive Tax Invoice',
                'document_no' => $inv_refno,
                'request_link' => site_url('panda_di/di_report?refno='.$inv_refno),
                'view_json' => $view_json,
        //         'set_code' => $set_code,
        //         'set_admin_code' =>  $set_admin_code,
        //         'accpt_po_status' => $accpt_po_status,
        // 'show_action_button' => $show_action_button,
            );
            
            $this->load->view('header');       
            $this->load->view('di/panda_di_pdf',$data);
            // $this->load->view('general_modal',$data);
            $this->load->view('footer');
        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    } 

    public function direct_print()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login())
        {
            $this->panda->get_uri();
            $refno = $_REQUEST['trans'];
            $loc = $_REQUEST['loc'];
            $customer_guid = $_SESSION['customer_guid'];
            $user_guid = $_SESSION['user_guid'];
            $from_module = $_SESSION['frommodule'];

            $check_scode = $this->db->query("SELECT sup_code from b2b_summary.discheme_taxinv where inv_refno = '$refno' and customer_guid = '".$_SESSION['customer_guid']."'")->row('sup_code');
            $check_scode = str_replace("/","+-+",$check_scode);
            // echo $this->db->last_query();die;
            // echo $check_scode;die;
            $parameter = $this->db->query("SELECT * from menu where module_link = '".$_SESSION['frommodule']."'");
            $type = $parameter->row('type');
            $code = $check_scode;
            $replace_var = $this->db->query("SELECT REPLACE(REPLACE(REPLACE(filename_format, 'type', '$type'), 'code', '$code'), 'refno' , '$refno') AS query FROM menu where module_link = '".$_SESSION['frommodule']."'")->row('query');

            $virtual_path = $this->db->query("SELECT file_path FROM acc WHERE acc_guid = '".$_SESSION['customer_guid']."'")->row('file_path');
           
            // $filename = base_url($virtual_path.'/'.$replace_var.'.pdf');
            $file_config_final_path = $this->file_config_b2b->file_path($customer_guid,'web','general_doc','main_path','GDMP');

            $filename = $file_config_final_path.'/'.$replace_var.'.pdf';
            $file_headers = @get_headers($filename);

            // echo $filename;die;
            // if(!file_exists('.'.$virtual_path.'/'.$replace_var.'.pdf'))
            // {
            //     // echo 1;die;
            //     echo "<script>alert('Document Not Found, Please Contact Support');</script>";
            //     echo "<script>window.close();</script>";die;
            // }
                
            // OLD
            if(in_array('HTTP/1.1 404 Not Found', $file_headers ))
            {
                
              echo "<script>window.close();</script>";
            }
            else
            {
                if(!in_array('!DISUPPMOV',$_SESSION['module_code']))
                {
                    $this->db->query("UPDATE b2b_summary.discheme_taxinv set status = 'printed' where customer_guid ='$customer_guid' and inv_refno = '$refno' and status IN ('','viewed') ");

                    $this->db->query("REPLACE into supplier_movement select 
                    upper(replace(uuid(),'-','')) as movement_guid
                    , '$customer_guid'
                    , '$user_guid'
                    , 'printed_DI'
                    , '$from_module'
                    , '$refno'
                    , now()
                    ");
                }
                redirect ($filename);
            } 
          
            /*$this->load->view('header');       
            $this->load->view('po/panda_po_pdf',$data);
            $this->load->view('general_modal',$data);
            $this->load->view('footer');*/
        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function direct_print_merge()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login())
        {
            $this->panda->get_uri();
            $refno = $_REQUEST['trans'];
            $loc = $_REQUEST['loc'];
            $customer_guid = $_SESSION['customer_guid'];
            $user_guid = $_SESSION['user_guid'];
            $from_module = $_SESSION['frommodule'];
            $pdf_name = $_REQUEST['pdfname'];
                // echo $refno;die;
            $virtual_path = $this->db->query("SELECT file_path FROM acc WHERE acc_guid = '".$_SESSION['customer_guid']."'")->row('file_path');
           
            // $filename = base_url($virtual_path.'/'.$pdf_name.'.pdf');
            // $filename = base_url('merge/'.$pdf_name.'.pdf');
            $path_seperator = $this->file_config_b2b->path_seperator($customer_guid,'web','general_doc','path_seperator','PS');

            $file_config_final_path = $this->file_config_b2b->merge_print_create_file_path($customer_guid,'web','general_doc','merge_print','MPMPCP');
            $merge_path = $this->file_config_b2b->file_path_name($customer_guid,'web','general_doc','merge_print','MPN');

            $filename = $file_config_final_path.$path_seperator.$merge_path.$path_seperator.$pdf_name.'.pdf';
            // $filename = 'http://192.168.10.29/lite_panda_b2b/uploads/tfvalue/merge.pdf';
            // echo $filename;die;
 
            $file_headers = @get_headers($filename);


            $refno_array = explode(",",$refno);
            // echo $refno;
            // print_r($refno_array);die;
            foreach($refno_array as $row2)
            {
                // echo 1;
               
                if(in_array('HTTP/1.1 404 Not Found', $file_headers ))
                {
                    
                  echo "<script>window.close();</script>";
                }
                else
                {
                    if(!in_array('!DISUPPMOV',$_SESSION['module_code']))
                    {
                        $this->db->query("UPDATE b2b_summary.discheme_taxinv set status = 'printed' where customer_guid ='$customer_guid' and inv_refno = '$row2' and status IN ('','viewed') ");

                        $this->db->query("REPLACE into supplier_movement select 
                        upper(replace(uuid(),'-','')) as movement_guid
                        , '$customer_guid'
                        , '$user_guid'
                        , 'printed_DI'
                        , '$from_module'
                        , '$row2'
                        , now()
                        ");
                    }
                    // redirect ($filename);
                }

            }
            $file = $filename; 
            if (!file_exists($file))
            {
                echo "The file not exists. Please Contact Admin";die;
            }
            // die;
            $type = 'inline';
            // $pdf_name = 'merge';
            // echo $pdf_name;die;
            header("Content-type: application/pdf");
            header('Content-Disposition: '.$type.'; filename="'.$pdf_name.'.pdf"'); 
            // header("Content-Disposition: attachment; filename=\"".$Filename."\"");
            // header("Content-Length: ".filesize($Filename));
            header('Cache-Control: public, must-revalidate, max-age=0');
            ob_clean();
            flush();
            readfile($file);
            die;  
            /*$this->load->view('header');       
            $this->load->view('po/panda_po_pdf',$data);
            $this->load->view('general_modal',$data);
            $this->load->view('footer');*/
        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function direct_print_merge_post_method()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != ''   && $_SESSION['user_logs'] == $this->panda->validate_login())
        {
            $this->panda->get_uri();
            $refno = $this->input->post('trans');
            $loc = $this->input->post('loc');
            $pdf_name = $this->input->post('pdfname');
            //$refno = $_REQUEST['trans'];
            //$loc = $_REQUEST['loc'];
            $customer_guid = $_SESSION['customer_guid'];
            $user_guid = $_SESSION['user_guid'];
            $from_module = $_SESSION['frommodule'];
            //$pdf_name = $_REQUEST['pdfname'];
                // echo $refno;die;
            $virtual_path = $this->db->query("SELECT file_path FROM acc WHERE acc_guid = '".$_SESSION['customer_guid']."'")->row('file_path');
           
            // $filename = base_url($virtual_path.'/'.$pdf_name.'.pdf');
            // $filename = base_url('merge/'.$pdf_name.'.pdf');
            $path_seperator = $this->file_config_b2b->path_seperator($customer_guid,'web','general_doc','path_seperator','PS');

            $file_config_final_path = $this->file_config_b2b->merge_print_create_file_path($customer_guid,'web','general_doc','merge_print','MPMPCP');
            $merge_path = $this->file_config_b2b->file_path_name($customer_guid,'web','general_doc','merge_print','MPN');

            $filename = $file_config_final_path.$path_seperator.$merge_path.$path_seperator.$pdf_name.'.pdf';
            // $filename = 'http://192.168.10.29/lite_panda_b2b/uploads/tfvalue/merge.pdf';
            // echo $filename;die;
 
            $file_headers = @get_headers($filename);


            $refno_array = explode(",",$refno);
            // echo $refno;
            // print_r($refno_array);die;
            foreach($refno_array as $row2)
            {
                // echo 1;
               
                if(in_array('HTTP/1.1 404 Not Found', $file_headers ))
                {
                    
                  echo "<script>window.close();</script>";
                }
                else
                {
                    if(!in_array('!DISUPPMOV',$_SESSION['module_code']))
                    {
                        $this->db->query("UPDATE b2b_summary.discheme_taxinv set status = 'printed' where customer_guid ='$customer_guid' and inv_refno = '$row2' and status IN ('','viewed') ");

                        $this->db->query("REPLACE into supplier_movement select 
                        upper(replace(uuid(),'-','')) as movement_guid
                        , '$customer_guid'
                        , '$user_guid'
                        , 'printed_DI'
                        , '$from_module'
                        , '$row2'
                        , now()
                        ");
                    }
                    // redirect ($filename);
                }

            }
            $file = $filename; 
            if (!file_exists($file))
            {
                echo "The file not exists. Please Contact Admin";die;
            }
            // die;
            $type = 'inline';
            // $pdf_name = 'merge';
            // echo $pdf_name;die;
            header("Content-type: application/pdf");
            header('Content-Disposition: '.$type.'; filename="'.$pdf_name.'.pdf"'); 
            // header("Content-Disposition: attachment; filename=\"".$Filename."\"");
            // header("Content-Length: ".filesize($Filename));
            header('Cache-Control: public, must-revalidate, max-age=0');
            ob_clean();
            flush();
            readfile($file);
            die;  
            /*$this->load->view('header');       
            $this->load->view('po/panda_po_pdf',$data);
            $this->load->view('general_modal',$data);
            $this->load->view('footer');*/
        }
        else
        {
            $this->session->set_flashdata('message', 'Session Expired! Please relogin');
            redirect('#');
        }
    }

    public function di_report()
    {
        $inv_refno = $_REQUEST['refno'];
        $customer_guid = $_SESSION['customer_guid'];
        $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
        $cloud_directory = $this->file_config_b2b->file_path_name($customer_guid,'web','general_doc','data_conversion_directory','DCD');
        $fileserver_url = $this->file_config_b2b->file_path_name($customer_guid,'web','file_server','main_path','FILESERVER');

        if($cloud_directory == null || $cloud_directory == ''){
            $cloud_directory = '/media/b2b-pdf/data_conversion/';
        }

        if($fileserver_url == null || $fileserver_url == ''){
            $fileserver_url = 'https://file.xbridge.my/';
        }

        $cloud_directory = $cloud_directory . $customer_guid . '/DI/';

        // check if pdf file already exist
        if (file_exists($cloud_directory.$inv_refno.'.pdf') && (filesize($cloud_directory.$inv_refno.'.pdf') / 1024 > 2)) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $fileserver_url. '/b2b-pdf/data_conversion/' . $customer_guid . '/DI/' . $inv_refno.'.pdf',
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

            curl_close($curl); 

            header('Content-type:application/pdf');
            header('Content-Disposition: inline; filename='.$inv_refno.'.pdf');

            echo $response; die;
        }

        $url = $this->jasper_ip ."/jasperserver/rest_v2/reports/reports/PandaReports/Backend_DIncentives/display_incentive_report.pdf?refno=".$inv_refno."&customer_guid=".$customer_guid."&mode=".$mode; // DI

        // echo $url; die;
        
        $check_code = $this->db->query("SELECT a.supplier_code from b2b_summary.promo_taxinv_info a where a.inv_refno = '$inv_refno' and a.customer_guid = '" . $_SESSION['customer_guid'] . "' ")->row('supplier_code');

        $check_code = str_replace("/", "+-+", $check_code);

        $parameter = $this->db->query("SELECT * from menu where module_link = 'panda_di'");
        $type = $parameter->row('type');
        $code = $check_code;

        $filename = $this->db->query("SELECT REPLACE(REPLACE(REPLACE(filename_format, 'type', '$type'), 'code', '$code'), 'refno' , '$inv_refno') AS query FROM menu where module_link = 'panda_di'")->row('query');

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

        // check pdf file directory
        if (!file_exists($cloud_directory)) {
            mkdir($cloud_directory, 0777, true);
        }

        // download pdf file into the cloud directory
        file_put_contents($cloud_directory.$inv_refno.'.pdf', $response);

        if(file_exists($cloud_directory.$inv_refno.'.pdf')){
            
            $update_data = array(
                'exported_by'       => 'trigger_button',
                'exported'          => 1,
                'exported_datetime' => $this->db->query("SELECT NOW() AS current_datetime")->row('current_datetime'),
            );

            $this->db->where('refno', $inv_refno);
            $this->db->where('customer_guid', $customer_guid);
            $this->db->update('b2b_summary.doc_export', $update_data);

        }

        header('Content-type:application/pdf');
        header('Content-Disposition: inline; filename='.$filename.'.pdf');
        echo $response; 

        curl_close($curl); 
    }
}
?>