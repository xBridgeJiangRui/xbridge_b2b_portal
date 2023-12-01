<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_doc extends CI_Controller {
    
  public function __construct()
  {
    parent::__construct();
    $this->load->library('Panda_PHPMailer');

  }

  public function archive_doc_list()
  {
    if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login() )
    {   
      $customer_guid = $_SESSION['customer_guid'];

      $customer_name = $this->db->query("SELECT acc_name FROM lite_b2b.acc WHERE acc_guid = '$customer_guid'")->row('acc_name');

      $get_valid_view = $this->db->query("SELECT a.* FROM lite_b2b.set_supplier_user_relationship a
      INNER JOIN lite_b2b.set_user b
      ON a.user_guid = b.user_guid
      AND a.customer_guid = b.acc_guid
      INNER JOIN lite_b2b.acc_settings c
      ON a.customer_guid = c.customer_guid
      WHERE a.supplier_guid = '4177E2DE057711E8A366A81E8453CCF0' 
      AND a.customer_guid = '" . $_SESSION['customer_guid'] . "'
      AND b.user_name NOT IN ('Loo Chee Wee')
      AND a.user_guid = '" . $_SESSION['user_guid'] . "'
      AND c.archived_doc_view = '1'
      GROUP BY a.user_guid")->result_array();

      if(count($get_valid_view) == 0)
      {
        echo '<script>alert("Expired to view Archived Document.");window.location.href = "'.site_url('dashboard').'";</script>;';
        die;
      }

      $data = array(
        'customer_name' => $customer_name,  
      );

      $this->load->view('header'); 
      $this->load->view('archive_doc/archive_doc_list',$data);  
      $this->load->view('footer' );  
    }
    else
    {
        $this->session->set_flashdata('message', 'Session Expired! Please relogin');
        redirect('#');
    }
  }

  public function archive_doc_table()
  {
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0); 

    $doc_type = $this->input->post('document_type');
    $refno = $this->input->post('refno');
    $customer_guid = $_SESSION['customer_guid'];
    $query_loc = $_SESSION['query_loc'];

    $get_customer = $this->db->query("SELECT SUBSTRING(file_path,10,20) AS file_path, rest_url FROM lite_b2b.acc WHERE acc_guid = '$customer_guid' AND isactive = '1'");

    $customer = $get_customer->row('file_path');

    if($customer == '' || $customer == 'null' || $customer == null)
    {
        $data = array(
            'para1' => 'false',
            'msg' => 'Invalid File Path Name.',
        );

        echo json_encode($data);
        exit();
    }

    // if($doc_type == 'pomain')
    // {
    //     $type = 'PO';
    //     $column_scode = 'SCode';
    //     $module_link = 'panda_po_2';
    // }
    // else if($doc_type == 'grmain')
    // {
    //     $type = 'GRN';
    //     $column_scode = 'Code';
    //     $module_link = 'panda_gr';
    // }
    // else if($doc_type == 'grmain_dncn')
    // {
    //     $type = 'GRDA';
    //     $column_scode = 'GRDA_DATA';
    //     $module_link = 'panda_grda';
    // }
    // if($doc_type == 'dbnotemain')
    // {
    //     $type = 'PRDN';
    //     $column_scode = 'Code';
    //     $module_link = 'panda_prdncn';
    // }
    // else if($doc_type == 'cnnotemain')
    // {
    //     $type = 'PRCN';
    //     $column_scode = 'Code';
    //     $module_link = 'panda_pdncn';
    // }
    // else if($doc_type == 'pdn_amt')
    // {
    //     $doc_type = 'cndn_amt';
    //     $type = 'PDN';
    //     $column_scode = 'code';
    //     $module_link = 'panda_prdn';
    // }
    // else if($doc_type == 'pcn_amt')
    // {
    //     $doc_type = 'cndn_amt';
    //     $type = 'PCN';
    //     $column_scode = 'code';
    //     $module_link = 'panda_prcn';
    // }
    // else if($doc_type == 'pci')
    // {
    //     $type = 'PROMO';
    //     $column_scode = 'sup_code';
    //     $module_link = 'panda_pci';
    // }
    // else if($doc_type == 'discheme_taxinv')
    // {
    //     $type = 'DISCHEME';
    //     $column_scode = 'sup_code';
    //     $module_link = 'panda_di';
    // }
    // else if($doc_type == 'other_doc')
    // {
    //     $type = '';
    //     $column_scode = 'supcode';
    // }
    // else
    // {
    //     $data = array(
    //         'para1' => 'false',
    //         'msg' => 'Invalid Document Type',
    //     );

    //     echo json_encode($data);
    //     exit();
    // }
    
    // if($doc_type == 'pomain')
    // {
    //   if(($refno == '') || ($refno == null) || ($refno == 'null'))
    //   {
    //     $filter = '';
    //   }
    //   else
    //   {
    //     $filter = "AND refno = '$refno'";
    //   }

    //   if(in_array('IAVA',$_SESSION['module_code']))
    //   {
    //     $query_data = $this->db->query("SELECT refno AS refno_val,podate AS document_date,`status`,location AS loc_group ,scode AS supplier_code,sname AS supplier_name,total,hide_at,hide_by,hide_remark FROM b2b_amend.`pomain` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");
    //   }
    //   else
    //   {
    //     $query_data = $this->db->query("SELECT refno AS refno_val,podate AS document_date,`status`,location AS loc_group ,scode AS supplier_code,sname AS supplier_name,total,hide_at,hide_by,hide_remark FROM b2b_amend.`pomain` WHERE customer_guid = '$customer_guid' AND location IN ($query_loc) and scode IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
    //   }
    // }
    // else if($doc_type == 'grmain')
    // {
    //   if(($refno == '') || ($refno == null) || ($refno == 'null'))
    //   {
    //     $filter = '';
    //   }
    //   else
    //   {
    //     $filter = "AND refno = '$refno'";
    //   }

    //   if(in_array('IAVA',$_SESSION['module_code']))
    //   {
    //     $query_data = $this->db->query("SELECT refno AS refno_val,grdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,total,hide_at,hide_by,hide_remark FROM b2b_amend.`grmain` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");

    //     //echo $this->db->last_query();die;
    //   }
    //   else
    //   {
    //     $query_data = $this->db->query("SELECT refno AS refno_val,grdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,total,hide_at,hide_by,hide_remark FROM b2b_amend.`grmain` WHERE customer_guid = '$customer_guid' AND location IN ($query_loc) and code IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
    //   }
    // }
    // else if($doc_type == 'grda')
    // {
    //   if(($refno == '') || ($refno == null) || ($refno == 'null'))
    //   {
    //     $filter = '';
    //   }
    //   else
    //   {
    //     $filter = "AND a.refno = '$refno'";
    //   }

    //   if(in_array('IAVA',$_SESSION['module_code']))
    //   {
    //     $query_data = $this->db->query("SELECT a.refno AS refno_val, a.created_at AS document_date, a.status, a.location AS loc_group, a.ap_sup_code AS supplier_code, IF(b.name IS NULL, c.name , b.name ) AS supplier_name, a.varianceamt AS total, a.hide_at, a.hide_by, a.hide_remark FROM b2b_amend.`grmain_dncn` a LEFT JOIN b2b_amend.grmain b ON a.`refno` = b.`refno` LEFT JOIN b2b_summary.grmain c ON a.refno = c.refno WHERE a.customer_guid = '$customer_guid' $filter ORDER BY a.hide_at DESC");
    //   }
    //   else
    //   {
    //     $query_data = $this->db->query("SELECT a.refno AS refno_val, a.created_at AS document_date, a.status, a.location AS loc_group, a.ap_sup_code AS supplier_code, IF(b.name IS NULL, c.name , b.name ) AS supplier_name, a.varianceamt AS total, a.hide_at, a.hide_by, a.hide_remark FROM b2b_amend.`grmain_dncn` a LEFT JOIN b2b_amend.grmain b ON a.`refno` = b.`refno` LEFT JOIN b2b_summary.grmain c ON a.refno = c.refno WHERE a.customer_guid = '$customer_guid' AND a.location IN ($query_loc) and a.ap_sup_code IN (".$_SESSION['query_supcode'].") $filter ORDER BY a.hide_at DESC");
    //   }
    // }
    if($doc_type == 'dbnotemain')
    {
        $type = 'PRDN';

        if(($refno == '') || ($refno == null) || ($refno == 'null'))
        {
            $filter = '';
        }
        else
        {
            $filter = "WHERE refno LIKE '%$refno%' OR promo_refno LIKE '%$refno%'";
        }

        if(in_array('IAVA',$_SESSION['module_code']))
        {
            $query_data = $this->db->query("SELECT * FROM ( SELECT a.refno,'' AS promo_refno,a.location,a.docdate AS document_date,
            a.code AS supplier_code,
            a.name AS supplier_name,
            '$type' AS doc_type,
            '$customer' AS pdf_customer_name
            FROM b2b_archive.`dbnotemain` a 
            WHERE a.code IN (SELECT supplier_group_name FROM lite_b2b.set_supplier_group WHERE 
            supplier_guid = '4177E2DE057711E8A366A81E8453CCF0' 
            AND customer_guid = '$customer_guid')
            AND a.customer_guid = '$customer_guid' 
            AND LEFT(a.docdate,7) BETWEEN '2020-06' AND '2021-06'
            GROUP BY a.refno ) aa
            $filter ");
        }
        else
        {
            $query_data = $this->db->query("SELECT * FROM ( SELECT a.refno,'' AS promo_refno,a.location,a.docdate AS document_date,
            a.code AS supplier_code,
            a.name AS supplier_name,
            '$type' AS doc_type,
            '$customer' AS pdf_customer_name
            FROM b2b_archive.`dbnotemain` a 
            WHERE a.customer_guid = '$customer_guid' 
            AND LEFT(a.docdate,7) BETWEEN '2020-06' AND '2021-06'
            AND a.location IN ($query_loc) 
            AND a.code IN (".$_SESSION['query_supcode'].")
            GROUP BY a.refno ) aa
            $filter");
        }
    }
    // else if($doc_type == 'cnnotemain')
    // {
    //   if(($refno == '') || ($refno == null) || ($refno == 'null'))
    //   {
    //     $filter = '';
    //   }
    //   else
    //   {
    //     $filter = "AND refno = '$refno'";
    //   }

    //   if(in_array('IAVA',$_SESSION['module_code']))
    //   {
    //     $query_data = $this->db->query("SELECT refno AS refno_val,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cnnotemain` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");
    //   }
    //   else
    //   {
    //     $query_data = $this->db->query("SELECT refno AS refno_val,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cnnotemain` WHERE customer_guid = '$customer_guid' AND location IN ($query_loc) and code IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
    //   }
    // }
    // else if($doc_type == 'cndn_amt')
    // {

    //   if(in_array('IAVA',$_SESSION['module_code']))
    //   {
    //     if(($refno == '') || ($refno == null) || ($refno == 'null'))
    //     {
    //       $filter = '';
    //     }
    //     else
    //     {
    //       $filter = "WHERE a.refno_val = '$refno'";
    //     }

    //     $query_data = $this->db->query("SELECT * FROM ( SELECT refno AS refno_val,trans_type,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cndn_amt` WHERE customer_guid = '$customer_guid' AND trans_type IN ('PCNAMT', 'PCNamt') UNION ALL SELECT refno AS refno_val,trans_type,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cndn_amt` WHERE customer_guid = '$customer_guid' AND trans_type IN ('PDNAMT', 'PDNamt') )a $filter ORDER BY a.hide_at DESC");
    //   }
    //   else
    //   {
    //     if(($refno == '') || ($refno == null) || ($refno == 'null'))
    //     {
    //       $filter = '';
    //     }
    //     else
    //     {
    //       $filter = "AND a.refno_val = '$refno'";
    //     }

    //     $query_data = $this->db->query("SELECT * FROM ( SELECT refno AS refno_val,trans_type,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cndn_amt` WHERE customer_guid = '$customer_guid' AND trans_type IN ('PCNAMT', 'PCNamt') UNION ALL SELECT refno AS refno_val,trans_type,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cndn_amt` WHERE customer_guid = '$customer_guid' AND trans_type IN ('PDNAMT', 'PDNamt') )a WHERE a.loc_group IN ($query_loc) and a.supplier_code IN (".$_SESSION['query_supcode'].") $filter ORDER BY a.hide_at DESC");
    //   }
    // }
    else if($doc_type == 'pci')
    {
        $type = 'PROMO';

        if(($refno == '') || ($refno == null) || ($refno == 'null'))
        {
            $filter = '';
        }
        else
        {
            $filter = "WHERE refno LIKE '%$refno%' OR promo_refno LIKE '%$refno%'";
        }

        if(in_array('IAVA',$_SESSION['module_code']))
        {
            $query_data = $this->db->query("SELECT * FROM ( SELECT a.inv_refno AS refno,
            a.refno AS promo_refno,
            a.loc_group AS location,
            a.docdate AS document_date,
            a.sup_code AS supplier_code,
            a.sup_name AS supplier_name,
            '$type' AS doc_type,
            '$customer' AS pdf_customer_name
            FROM b2b_archive.`promo_taxinv` a WHERE 
            a.sup_code IN (SELECT supplier_group_name FROM lite_b2b.set_supplier_group WHERE 
            supplier_guid = '4177E2DE057711E8A366A81E8453CCF0' 
            AND customer_guid = '$customer_guid')
            AND a.customer_guid = '$customer_guid' 
            AND LEFT(a.docdate,7) BETWEEN '2020-07' AND '2021-01'
            GROUP BY a.inv_refno ) aa
            $filter");
        }
        else
        {
            $query_data = $this->db->query("SELECT * FROM ( SELECT a.inv_refno AS refno,
            a.refno AS promo_refno,
            a.loc_group AS location,
            a.docdate AS document_date,
            a.sup_code AS supplier_code,
            a.sup_name AS supplier_name,
            '$type' AS doc_type,
            '$customer' AS pdf_customer_name
            FROM b2b_archive.`promo_taxinv` a 
            WHERE a.customer_guid = '$customer_guid' 
            AND LEFT(a.docdate,7) BETWEEN '2020-07' AND '2021-01'
            AND a.loc_group IN ($query_loc) 
            AND a.sup_code IN (".$_SESSION['query_supcode'].")
            GROUP BY a.inv_refno ) aa
            $filter");
        }
    }
    else if($doc_type == 'display_incentive')
    {
        $type = 'DISCHEME';

        if(($refno == '') || ($refno == null) || ($refno == 'null'))
        {
            $filter = '';
        }
        else
        {
            $filter = "WHERE refno LIKE '%$refno%' OR promo_refno LIKE '%$refno%'";
        }

        if(in_array('IAVA',$_SESSION['module_code']))
        {
            $query_data = $this->db->query("SELECT * FROM ( SELECT a.inv_refno AS refno,
            a.refno AS promo_refno,
            a.loc_group AS location,
            a.docdate AS document_date,
            a.sup_code AS supplier_code,
            a.sup_name AS supplier_name,
            '$type' AS doc_type,
            '$customer' AS pdf_customer_name
            FROM b2b_archive.`discheme_taxinv` a WHERE 
            a.sup_code IN (SELECT supplier_group_name FROM lite_b2b.set_supplier_group WHERE 
            supplier_guid = '4177E2DE057711E8A366A81E8453CCF0' 
            AND customer_guid = '$customer_guid')
            AND a.customer_guid = '$customer_guid' 
            AND LEFT(a.docdate,7) BETWEEN '2020-06' AND '2021-06'
            GROUP BY a.inv_refno ) aa
            $filter");
        }
        else
        {
            $query_data = $this->db->query("SELECT * FROM ( SELECT a.inv_refno AS refno,
            a.refno AS promo_refno,
            a.loc_group AS location,
            a.docdate AS document_date,
            a.sup_code AS supplier_code,
            a.sup_name AS supplier_name,
            '$type' AS doc_type,
            '$customer' AS pdf_customer_name
            FROM b2b_archive.`discheme_taxinv` a 
            WHERE a.customer_guid = '$customer_guid' 
            AND LEFT(a.docdate,7) BETWEEN '2020-06' AND '2021-06'
            AND a.loc_group IN ($query_loc) 
            AND a.sup_code IN (".$_SESSION['query_supcode'].") 
            $filter
            GROUP BY a.inv_refno ) aa
            $filter");
        }
    }
    else
    {
        $data = array(
        'para1' => 1,
        'msg' => 'Invalid Action. Error To Filter.',
        );    
        echo json_encode($data);  
        die;
    }

    $data = array(  
      'query_data' => $query_data->result(),
    );

    echo json_encode($data); 
  }

  public function retrieve_pdf_path()
  {
    $refno = $this->input->post('refno');
    $promo_refno = $this->input->post('promo_refno');
    $supplier_code = $this->input->post('supplier_code');
    $doc_type = $this->input->post('doc_type');
    $pdf_customer_name = $this->input->post('pdf_customer_name');
    $refno = $this->input->post('refno');
    $customer_guid = $_SESSION['customer_guid'];
    $user_name = $this->db->query("SELECT a.user_name FROM set_user a WHERE a.user_guid ='" . $_SESSION['user_guid'] . "'")->row('user_name');
    $now = $this->db->query("SELECT now() as now")->row('now');

    if($customer_guid == '907FAFE053F011EB8099063B6ABE2862' || $customer_guid == '1F90F5EF90DF11EA818B000D3AA2CAA9' || $customer_guid == 'D361F8521E1211EAAD7CC8CBB8CC0C93')
    {
        $refno = $refno;
    }
    else
    {
        $refno = $promo_refno;
    }
    
    if($doc_type != 'other_doc')
    {
        $url = "http://52.163.112.202/rest_api/index.php/Panda_b2b_/Document?type=".$doc_type."&refno=".$refno."&supcode=".$supplier_code."&customer=".$pdf_customer_name."";
        $url2 = "http://52.163.112.202/rest_api/index.php/Panda_b2b_/Document_new?type=".$doc_type."&refno=".$refno."&supcode=".$supplier_code."&customer=".$pdf_customer_name."";
        // print_r($url); die;
    }
    else
    {
        $url2 = '';
        $redirect = 'true';
        $doctime = $this->db->query("SELECT doctime FROM b2b_archive.$doc_type WHERE customer_guid = '$customer_guid' $and_refno ")->row('doctime');
        $acc_sys_type = $this->db->query("SELECT accounting_doc FROM acc WHERE acc_guid = '$customer_guid'")->row('accounting_doc');
        if($doctime == '' || $doctime == 'null' || $doctime == null)
        {
            $data = array(
                'para1' => 'false',
                'msg' => 'Data Not Found.',
            );
    
            echo json_encode($data);
            exit();
        }

        $doctime = str_replace(' ', '%20', $doctime);
        $path = $get_customer->row('rest_url');
        if($path == '' || $path == 'null' || $path == null)
        {
            $data = array(
                'para1' => 'false',
                'msg' => 'Path Not Found.',
            );
    
            echo json_encode($data);
            exit();
        }
        //$path = "http://18.139.87.215/rest_api/index.php/return_json";

        //$url = "http://18.139.87.215/rest_api/index.php/return_json/Document?refno=THSBAT21000004&doctype=PVV&supcode=S404&doctime=2021-01-29%2023:15:02";

        if ($acc_sys_type == 'nav') {
            $url = "".$path."/Document?refno=". urlencode($refno)."&doctype=".$doc_type."&supcode=".$supplier_code."&doctime=".$doctime."";
        } else {
            $url = "".$path."/Document_autocount?refno=". urlencode(str_replace('/', '', $refno))."&doctype=".$doc_type."&supcode=".$supplier_code."&doctime=".$doctime."";
        }
    }

    $pdfContent = @file_get_contents($url);
    // $httpCode = curl_getinfo($pdfContent);

    // Check if the PDF content is valid
    if (substr($pdfContent, 0, 4) === '%PDF') {
        // Valid PDF file
        $valid_url = '1';
    } else {
        // Not a PDF file
        $valid_url = '99';
    }

    $pdfContent2 = @file_get_contents($url2);
    // $httpCode = curl_getinfo($pdfContent);

    // Check if the PDF content is valid
    if (substr($pdfContent2, 0, 4) === '%PDF') {
        // Valid PDF file
        $valid_url2 = '1';
    } else {
        // Not a PDF file
        $valid_url2 = '99';
    }

    $data = array(
        'customer_guid' => $customer_guid,
        'refno' => $refno,
        '1st_url' => $url,
        '2nd_url' => $url2,
        '1st_url_status' => $valid_url,
        '2nd_url_status' => $valid_url2,
        'created_at' => $now,
        'created_by' => $user_name,
    );
    $this->db->insert('jiangrui_table.check_pdf', $data);

    if($valid_url == '1')
    {
        $success_url = $url;
    }
    else
    {
        $success_url = $url2;
    }

    $data = array(
        'para' => 'true',
        'success_url' => $success_url,
    );

    echo json_encode($data);
    
  }

}
?>

