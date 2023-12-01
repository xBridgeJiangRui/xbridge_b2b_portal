<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Amend_doc extends CI_Controller {
    
  public function __construct()
  {
    parent::__construct();
    $this->load->library('Panda_PHPMailer');

  }

  public function amend_sites()
  {
    if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login() && (in_array('IAVA',$_SESSION['module_code'])))
    {   
      $customer_guid = $_SESSION['customer_guid'];

      $customer_name = $this->db->query("SELECT acc_name FROM lite_b2b.acc WHERE acc_guid = '$customer_guid'")->row('acc_name');

      $data = array(
        'customer_name' => $customer_name,  
      );

      $this->load->view('header'); 
      $this->load->view('amend_doc/amend_sites',$data);  
      $this->load->view('footer' );  
    }
    else
    {
        $this->session->set_flashdata('message', 'Session Expired! Please relogin');
        redirect('#');
    }
  }

  public function fetch_ref_no()
  {
    $refno = $this->input->post('refno');
    $doc_type = $this->input->post('doc_type');
    $action_type = $this->input->post('action_type');
    $customer_guid = $_SESSION['customer_guid'];
    $query_loc = $_SESSION['query_loc'];

    //$ref_no_array = implode("','",$refno);
    //$ref_no_array = "".$ref_no_array."";
    $set_date = date('Y-m-04');
    $cur_date = $this->db->query("SELECT CURDATE() as curdate")->row('curdate');
    if($cur_date <= $set_date)
    {
      $data = array(
        'para1' => 1,
        'msg' => 'B2B processing Invoice cut-off of the month.',
      );    
      echo json_encode($data);  
      die;
    }

    if($action_type != 'show_the_data')
    {
      if($doc_type == 'pomain')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno, a.scode FROM b2b_summary.pomain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
              
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno, a.scode FROM b2b_summary.pomain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) and a.scode IN (".$_SESSION['query_supcode'].") AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");

        }
      }
      else if($doc_type == 'grmain')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS scode FROM b2b_summary.grmain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
              
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS scode  FROM b2b_summary.grmain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' and a.loc_group in ($query_loc) and a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC ");

        }
      }
      else if($doc_type == 'grda')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT b.refno , b.ap_sup_code AS `scode` FROM b2b_summary.grmain a LEFT JOIN b2b_summary.grmain_dncn b ON a.refno = b.refno  AND a.customer_guid = b.customer_guid WHERE b.refno LIKE '%$refno%' AND b.`customer_guid` = '$customer_guid' AND a.loc_group in ($query_loc) AND LEFT(b.created_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY b.refno ORDER BY b.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT b.refno , b.ap_sup_code AS `scode` FROM b2b_summary.grmain a LEFT JOIN b2b_summary.grmain_dncn b ON a.refno = b.refno AND a.customer_guid = b.customer_guid WHERE b.refno LIKE '%$refno%' AND b.`customer_guid` = '$customer_guid' AND a.loc_group in ($query_loc) AND b.ap_sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(b.created_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY b.refno ORDER BY b.refno ASC");
        }
      }
      else if($doc_type == 'dbnotemain')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_summary.dbnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_summary.dbnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
      }
      else if($doc_type == 'cnnotemain')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_summary.cnnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_summary.cnnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
      }
      else if($doc_type == 'cndn_amt')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno,a.code AS `scode` FROM b2b_summary.cndn_amt a WHERE a.refno LIKE '%$refno%' AND a.trans_type IN ('PCNAMT' , 'PDNAMT') AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno,a.code AS `scode` FROM b2b_summary.cndn_amt a WHERE a.refno LIKE '%$refno%' AND a.trans_type IN ('PCNAMT' , 'PDNAMT') AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
      }
      else if($doc_type == 'pci')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.inv_refno AS refno, a.sup_code AS `scode` FROM b2b_summary.promo_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.inv_refno AS refno, a.sup_code AS `scode` FROM b2b_summary.promo_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) a.sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
      }
      else if($doc_type == 'display_incentive')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.inv_refno AS `refno`, sup_code AS `scode` FROM b2b_summary.discheme_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.inv_refno ORDER BY a.inv_refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.inv_refno AS `refno`, sup_code AS `scode` FROM b2b_summary.discheme_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) a.sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.inv_refno ORDER BY a.inv_refno ASC");
        }
      }
      else
      {
          $data = array(
          'para1' => 1,
          'msg' => 'Invalid Action. Error To Update.',
          );    
          echo json_encode($data);  
          die;
      }
    }

    if($action_type == 'show_the_data')
    {
      if($doc_type == 'pomain')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno, a.scode FROM b2b_amend.pomain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
              
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno, a.scode FROM b2b_amend.pomain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) and a.scode IN (".$_SESSION['query_supcode'].") AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");

        }
      }
      else if($doc_type == 'grmain')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS scode FROM b2b_amend.grmain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
              
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS scode  FROM b2b_amend.grmain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' and a.loc_group in ($query_loc) and a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC ");

        }
      }
      else if($doc_type == 'grda')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT b.refno , b.ap_sup_code AS `scode` FROM b2b_amend.grmain_dncn b WHERE b.refno LIKE '%$refno%' AND b.`customer_guid` = '$customer_guid' AND LEFT(b.created_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY b.refno ORDER BY b.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT b.refno , b.ap_sup_code AS `scode` FROM b2b_amend.grmain a LEFT JOIN b2b_amend.grmain_dncn b ON a.refno = b.refno AND a.customer_guid = b.customer_guid WHERE b.refno LIKE '%$refno%' AND b.`customer_guid` = '$customer_guid' AND a.loc_group in ($query_loc) AND b.ap_sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(b.created_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY b.refno ORDER BY b.refno ASC");
        }
      }
      else if($doc_type == 'dbnotemain')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_amend.dbnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_amend.dbnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
      }
      else if($doc_type == 'cnnotemain')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_amend.cnnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_amend.cnnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.postdatetime, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
      }
      else if($doc_type == 'cndn_amt')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.refno,a.code AS `scode` FROM b2b_amend.cndn_amt a WHERE a.refno LIKE '%$refno%' AND a.trans_type IN ('PCNAMT' , 'PDNAMT') AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.refno,a.code AS `scode` FROM b2b_amend.cndn_amt a WHERE a.refno LIKE '%$refno%' AND a.trans_type IN ('PCNAMT' , 'PDNAMT') AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
      }
      else if($doc_type == 'pci')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.inv_refno AS refno, a.sup_code AS `scode` FROM b2b_amend.promo_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.inv_refno AS refno, a.sup_code AS `scode` FROM b2b_amend.promo_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) a.sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
        }
      }
      else if($doc_type == 'display_incentive')
      {
        if(in_array('IAVA',$_SESSION['module_code']))
        {
          $query_data = $this->db->query("SELECT a.inv_refno AS `refno`, sup_code AS `scode` FROM b2b_amend.discheme_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.inv_refno ORDER BY a.inv_refno ASC");
        }
        else
        {
          $query_data = $this->db->query("SELECT a.inv_refno AS `refno`, sup_code AS `scode` FROM b2b_amend.discheme_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) a.sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.inv_refno ORDER BY a.inv_refno ASC");
        }
      }
      else
      {
          $data = array(
          'para1' => 1,
          'msg' => 'Invalid Action. Error To Update.',
          );    
          echo json_encode($data);  
          die;
      }
    }

    // need remove after put to production
      // if($doc_type == 'pomain')
      // {
      //   if(in_array('IAVA',$_SESSION['module_code']))
      //   {
      //     $query_data = $this->db->query("SELECT a.refno, a.scode FROM b2b_summary.pomain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' GROUP BY a.refno ORDER BY a.refno ASC LIMIT 100");
              
      //   }
      //   else
      //   {
      //     $query_data = $this->db->query("SELECT a.refno, a.scode FROM b2b_summary.pomain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) and a.scode IN (".$_SESSION['query_supcode'].") GROUP BY a.refno ORDER BY a.refno ASC");

      //   }
      // }
      // else if($doc_type == 'grmain')
      // {
      //   if(in_array('IAVA',$_SESSION['module_code']))
      //   {
      //     $query_data = $this->db->query("SELECT a.refno, a.code AS scode FROM b2b_summary.grmain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' GROUP BY a.refno ORDER BY a.refno ASC LIMIT 100");
              
      //   }
      //   else
      //   {
      //     $query_data = $this->db->query("SELECT a.refno, a.code AS scode  FROM b2b_summary.grmain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' and a.loc_group in ($query_loc) and a.code IN (".$_SESSION['query_supcode'].") GROUP BY a.refno ORDER BY a.refno ASC ");

      //   }
      // }
      // else if($doc_type == 'grda')
      // {
      //   if(in_array('IAVA',$_SESSION['module_code']))
      //   {
      //     $query_data = $this->db->query("SELECT b.refno , b.ap_sup_code AS `scode` FROM b2b_summary.grmain a LEFT JOIN b2b_summary.grmain_dncn b ON a.refno = b.refno  AND a.customer_guid = b.customer_guid WHERE b.refno LIKE '%$refno%' AND b.`customer_guid` = '$customer_guid' AND a.loc_group in ($query_loc) GROUP BY b.refno ORDER BY b.refno ASC LIMIT 100");
      //   }
      //   else
      //   {
      //     $query_data = $this->db->query("SELECT b.refno , b.ap_sup_code AS `scode` FROM b2b_summary.grmain a LEFT JOIN b2b_summary.grmain_dncn b ON a.refno = b.refno AND a.customer_guid = b.customer_guid WHERE b.refno LIKE '%$refno%' AND b.`customer_guid` = '$customer_guid' AND a.loc_group in ($query_loc) AND b.ap_sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(a.created_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY b.refno ORDER BY b.refno ASC");
      //   }
      // }
      // else if($doc_type == 'dbnotemain')
      // {
      //   if(in_array('IAVA',$_SESSION['module_code']))
      //   {
      //     $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_summary.dbnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) GROUP BY a.refno ORDER BY a.refno ASC LIMIT 100");
      //   }
      //   else
      //   {
      //     $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_summary.dbnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") GROUP BY a.refno ORDER BY a.refno ASC");
      //   }
      // }
      // else if($doc_type == 'cnnotemain')
      // {
      //   if(in_array('IAVA',$_SESSION['module_code']))
      //   {
      //     $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_summary.cnnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) GROUP BY a.refno ORDER BY a.refno ASC LIMIT 100");
      //   }
      //   else
      //   {
      //     $query_data = $this->db->query("SELECT a.refno, a.code AS `scode` FROM b2b_summary.cnnotemain a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.locgroup IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") GROUP BY a.refno ORDER BY a.refno ASC");
      //   }
      // }
      // else if($doc_type == 'cndn_amt')
      // {
      //   if(in_array('IAVA',$_SESSION['module_code']))
      //   {
      //     $query_data = $this->db->query("SELECT a.refno,a.code AS `scode` FROM b2b_summary.cndn_amt a WHERE a.refno LIKE '%$refno%' AND a.trans_type IN ('PCNAMT' , 'PDNAMT') AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) GROUP BY a.refno ORDER BY a.refno ASC LIMIT 100");
      //   }
      //   else
      //   {
      //     $query_data = $this->db->query("SELECT a.refno,a.code AS `scode` FROM b2b_summary.cndn_amt a WHERE a.refno LIKE '%$refno%' AND a.trans_type IN ('PCNAMT' , 'PDNAMT') AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) AND a.code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
      //   }
      // }
      // else if($doc_type == 'pci')
      // {
      //   if(in_array('IAVA',$_SESSION['module_code']))
      //   {
      //     $query_data = $this->db->query("SELECT a.inv_refno AS refno, a.sup_code AS `scode` FROM b2b_summary.promo_taxinv a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) GROUP BY a.refno ORDER BY a.refno ASC LIMIT 100");
      //   }
      //   else
      //   {
      //     $query_data = $this->db->query("SELECT a.inv_refno AS refno, a.sup_code AS `scode` FROM b2b_summary.promo_taxinv a WHERE a.refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) a.sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.refno ORDER BY a.refno ASC");
      //   }
      // }
      // else if($doc_type == 'display_incentive')
      // {
      //   if(in_array('IAVA',$_SESSION['module_code']))
      //   {
      //     $query_data = $this->db->query("SELECT a.inv_refno AS `refno`, sup_code AS `scode` FROM b2b_summary.discheme_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) GROUP BY a.inv_refno ORDER BY a.inv_refno ASC LIMIT 100");
      //   }
      //   else
      //   {
      //     $query_data = $this->db->query("SELECT a.inv_refno AS `refno`, sup_code AS `scode` FROM b2b_summary.discheme_taxinv a WHERE a.inv_refno LIKE '%$refno%' AND a.customer_guid = '$customer_guid' AND a.loc_group IN ($query_loc) a.sup_code IN (".$_SESSION['query_supcode'].") AND LEFT(a.posted_at, 7) = LEFT( CURDATE(), 7 ) GROUP BY a.inv_refno ORDER BY a.inv_refno ASC");
      //   }
      // }
    //echo $this->db->last_query(); die;
    $data = array(
      'para1' => 0,
      'query_data' => $query_data->result(),
    );

    echo json_encode($data);
  }

  public function fetch_period_code()
  {
    $customer_guid = $_SESSION['customer_guid'];
    $doc_type = $this->input->post('doc_type');

    if($doc_type == 'pomain')
    {
      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $period_code = $this->db->query("SELECT LEFT(a.PODate, 7) AS period_code FROM b2b_summary.pomain a WHERE a.customer_guid = '$customer_guid'  GROUP BY LEFT(a.PODate, 7) ORDER BY a.PODate DESC");
      }
    }
    else if($doc_type == 'grmain')
    {
      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $period_code = $this->db->query("SELECT LEFT(a.GRDate, 7) AS period_code FROM b2b_summary.grmain a WHERE a.customer_guid = '$customer_guid' GROUP BY LEFT(a.GRDate, 7) ORDER BY a.GRDate DESC");
            
      }
    }
    else if($doc_type == 'grda')
    {
      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $period_code = $this->db->query("SELECT LEFT(b.created_at, 7) AS period_code FROM b2b_summary.grmain a LEFT JOIN b2b_summary.grmain_dncn b ON a.refno = b.refno AND a.customer_guid = b.customer_guid WHERE b.`customer_guid` = '$customer_guid' GROUP BY LEFT(b.created_at, 7) ORDER BY a.created_at DESC");
      }
    }
    else if($doc_type == 'dbnotemain')
    {
      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $period_code = $this->db->query("SELECT LEFT(a.docdate, 7) AS period_code FROM b2b_summary.dbnotemain a WHERE a.customer_guid = '$customer_guid' GROUP BY LEFT(a.docdate, 7) ORDER BY a.docdate DESC");
      }
    }
    else if($doc_type == 'cnnotemain')
    {
      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $period_code = $this->db->query("SELECT LEFT(a.docdate, 7) AS period_code FROM b2b_summary.cnnotemain a WHERE a.customer_guid = '$customer_guid' GROUP BY LEFT(a.docdate, 7) ORDER BY a.docdate DESC");
      }
    }
    else if($doc_type == 'cndn_amt')
    {
      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $period_code = $this->db->query("SELECT LEFT(a.docdate, 7) AS period_code FROM b2b_summary.cndn_amt a WHERE a.trans_type IN ('PCNAMT' , 'PDNAMT') AND a.customer_guid = '$customer_guid' GROUP BY LEFT(a.docdate, 7) ORDER BY a.docdate DESC");
      }
    }
    else if($doc_type == 'pci')
    {
      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $period_code = $this->db->query("SELECT LEFT(a.docdate, 7) AS period_code FROM b2b_summary.promo_taxinv a WHERE a.customer_guid = '$customer_guid' GROUP BY LEFT(a.docdate, 7) ORDER BY a.docdate DESC");
      }
    }
    else if($doc_type == 'display_incentive')
    {
      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $period_code = $this->db->query("SELECT LEFT(a.docdate, 7) AS period_code FROM b2b_summary.discheme_taxinv a WHERE a.customer_guid = '$customer_guid' GROUP BY LEFT(a.docdate, 7) ORDER BY a.docdate DESC");
      }
    }
    else
    {
        $data = array(
        'para1' => 1,
        'msg' => 'Invalid Action. Error To Update.',
        );    
        echo json_encode($data);  
        die;
    }

    $data = array(
      'period_code' => $period_code->result(),
    );

    echo json_encode($data);
  }

  public function amend_doc_list()
  {
    if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '' && $_SESSION['user_logs'] == $this->panda->validate_login() && (in_array('IAVA',$_SESSION['module_code'])))
    {   
      $customer_guid = $_SESSION['customer_guid'];

      $customer_name = $this->db->query("SELECT acc_name FROM lite_b2b.acc WHERE acc_guid = '$customer_guid'")->row('acc_name');

      $data = array(
        'customer_name' => $customer_name,  
      );

      $this->load->view('header'); 
      $this->load->view('amend_doc/amend_doc_list',$data);  
      $this->load->view('footer' );  
    }
    else
    {
        $this->session->set_flashdata('message', 'Session Expired! Please relogin');
        redirect('#');
    }
  }

  public function amend_doc_table()
  {
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0); 

    $doc_type = $this->input->post('document_type');
    $refno = $this->input->post('refno');
    $customer_guid = $_SESSION['customer_guid'];
    $query_loc = $_SESSION['query_loc'];
    
    if($doc_type == 'pomain')
    {
      if(($refno == '') || ($refno == null) || ($refno == 'null'))
      {
        $filter = '';
      }
      else
      {
        $filter = "AND refno = '$refno'";
      }

      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $query_data = $this->db->query("SELECT refno AS refno_val,podate AS document_date,`status`,location AS loc_group ,scode AS supplier_code,sname AS supplier_name,total,hide_at,hide_by,hide_remark FROM b2b_amend.`pomain` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");
      }
      else
      {
        $query_data = $this->db->query("SELECT refno AS refno_val,podate AS document_date,`status`,location AS loc_group ,scode AS supplier_code,sname AS supplier_name,total,hide_at,hide_by,hide_remark FROM b2b_amend.`pomain` WHERE customer_guid = '$customer_guid' AND location IN ($query_loc) and scode IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
      }
    }
    else if($doc_type == 'grmain')
    {
      if(($refno == '') || ($refno == null) || ($refno == 'null'))
      {
        $filter = '';
      }
      else
      {
        $filter = "AND refno = '$refno'";
      }

      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $query_data = $this->db->query("SELECT refno AS refno_val,grdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,total,hide_at,hide_by,hide_remark FROM b2b_amend.`grmain` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");

        //echo $this->db->last_query();die;
      }
      else
      {
        $query_data = $this->db->query("SELECT refno AS refno_val,grdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,total,hide_at,hide_by,hide_remark FROM b2b_amend.`grmain` WHERE customer_guid = '$customer_guid' AND location IN ($query_loc) and code IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
      }
    }
    else if($doc_type == 'grda')
    {
      if(($refno == '') || ($refno == null) || ($refno == 'null'))
      {
        $filter = '';
      }
      else
      {
        $filter = "AND a.refno = '$refno'";
      }

      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $query_data = $this->db->query("SELECT a.refno AS refno_val, a.created_at AS document_date, a.status, a.location AS loc_group, a.ap_sup_code AS supplier_code, IF(b.name IS NULL, c.name , b.name ) AS supplier_name, a.varianceamt AS total, a.hide_at, a.hide_by, a.hide_remark FROM b2b_amend.`grmain_dncn` a LEFT JOIN b2b_amend.grmain b ON a.`refno` = b.`refno` LEFT JOIN b2b_summary.grmain c ON a.refno = c.refno WHERE a.customer_guid = '$customer_guid' $filter ORDER BY a.hide_at DESC");
      }
      else
      {
        $query_data = $this->db->query("SELECT a.refno AS refno_val, a.created_at AS document_date, a.status, a.location AS loc_group, a.ap_sup_code AS supplier_code, IF(b.name IS NULL, c.name , b.name ) AS supplier_name, a.varianceamt AS total, a.hide_at, a.hide_by, a.hide_remark FROM b2b_amend.`grmain_dncn` a LEFT JOIN b2b_amend.grmain b ON a.`refno` = b.`refno` LEFT JOIN b2b_summary.grmain c ON a.refno = c.refno WHERE a.customer_guid = '$customer_guid' AND a.location IN ($query_loc) and a.ap_sup_code IN (".$_SESSION['query_supcode'].") $filter ORDER BY a.hide_at DESC");
      }
    }
    else if($doc_type == 'dbnotemain')
    {
      if(($refno == '') || ($refno == null) || ($refno == 'null'))
      {
        $filter = '';
      }
      else
      {
        $filter = "AND refno = '$refno'";
      }

      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $query_data = $this->db->query("SELECT refno AS refno_val,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`dbnotemain` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");
      }
      else
      {
        $query_data = $this->db->query("SELECT refno AS refno_val,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`dbnotemain` WHERE customer_guid = '$customer_guid' AND location IN ($query_loc) and code IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
      }
    }
    else if($doc_type == 'cnnotemain')
    {
      if(($refno == '') || ($refno == null) || ($refno == 'null'))
      {
        $filter = '';
      }
      else
      {
        $filter = "AND refno = '$refno'";
      }

      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $query_data = $this->db->query("SELECT refno AS refno_val,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cnnotemain` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");
      }
      else
      {
        $query_data = $this->db->query("SELECT refno AS refno_val,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cnnotemain` WHERE customer_guid = '$customer_guid' AND location IN ($query_loc) and code IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
      }
    }
    else if($doc_type == 'cndn_amt')
    {

      if(in_array('IAVA',$_SESSION['module_code']))
      {
        if(($refno == '') || ($refno == null) || ($refno == 'null'))
        {
          $filter = '';
        }
        else
        {
          $filter = "WHERE a.refno_val = '$refno'";
        }

        $query_data = $this->db->query("SELECT * FROM ( SELECT refno AS refno_val,trans_type,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cndn_amt` WHERE customer_guid = '$customer_guid' AND trans_type IN ('PCNAMT', 'PCNamt') UNION ALL SELECT refno AS refno_val,trans_type,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cndn_amt` WHERE customer_guid = '$customer_guid' AND trans_type IN ('PDNAMT', 'PDNamt') )a $filter ORDER BY a.hide_at DESC");
      }
      else
      {
        if(($refno == '') || ($refno == null) || ($refno == 'null'))
        {
          $filter = '';
        }
        else
        {
          $filter = "AND a.refno_val = '$refno'";
        }

        $query_data = $this->db->query("SELECT * FROM ( SELECT refno AS refno_val,trans_type,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cndn_amt` WHERE customer_guid = '$customer_guid' AND trans_type IN ('PCNAMT', 'PCNamt') UNION ALL SELECT refno AS refno_val,trans_type,docdate AS document_date,`status`,location AS loc_group,`code` AS supplier_code,`name` AS supplier_name,amount AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`cndn_amt` WHERE customer_guid = '$customer_guid' AND trans_type IN ('PDNAMT', 'PDNamt') )a WHERE a.loc_group IN ($query_loc) and a.supplier_code IN (".$_SESSION['query_supcode'].") $filter ORDER BY a.hide_at DESC");
      }
    }
    else if($doc_type == 'pci')
    {
      if(($refno == '') || ($refno == null) || ($refno == 'null'))
      {
        $filter = '';
      }
      else
      {
        $filter = "AND inv_refno = '$refno'";
      }

      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $query_data = $this->db->query("SELECT inv_refno AS refno_val ,refno,docdate AS document_date,`status`,loc_group,`sup_code` AS supplier_code,`sup_name` AS supplier_name,total_net AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`promo_taxinv` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");
      }
      else
      {
        $query_data = $this->db->query("SELECT inv_refno AS refno_val ,refno,docdate AS document_date,`status`,loc_group,`sup_code` AS supplier_code,`sup_name` AS supplier_name,total_net AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`promo_taxinv` WHERE customer_guid = '$customer_guid' AND loc_group IN ($query_loc) and sup_code IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
      }
    }
    else if($doc_type == 'display_incentive')
    {
      if(($refno == '') || ($refno == null) || ($refno == 'null'))
      {
        $filter = '';
      }
      else
      {
        $filter = "AND inv_refno = '$refno'";
      }

      if(in_array('IAVA',$_SESSION['module_code']))
      {
        $query_data = $this->db->query("SELECT inv_refno AS refno_val,refno,docdate AS document_date,`status`,loc_group,`sup_code` AS supplier_code,`sup_name` AS supplier_name,total_net AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`discheme_taxinv` WHERE customer_guid = '$customer_guid' $filter ORDER BY hide_at DESC");
      }
      else
      {
        $query_data = $this->db->query("SELECT inv_refno AS refno_val,refno,docdate AS document_date,`status`,loc_group,`sup_code` AS supplier_code,`sup_name` AS supplier_name,total_net AS total,hide_at,hide_by,hide_remark FROM b2b_amend.`discheme_taxinv` WHERE customer_guid = '$customer_guid' AND loc_group IN ($query_loc) and sup_code IN (".$_SESSION['query_supcode'].") $filter ORDER BY hide_at DESC");
      }
    }
    else
    {
        $data = array(
        'para1' => 1,
        'msg' => 'Invalid Action. Error To Update.',
        );    
        echo json_encode($data);  
        die;
    }

    $data = array(  
      'query_data' => $query_data->result(),
    );

    echo json_encode($data); 
  }

  public function run_amend_function()
  {
    #change customer_guid
    $ref_no = $this->input->post('ref_no');
    $doc_type = $this->input->post('doc_type');
    $action_type = $this->input->post('action_type');
    $remark = $this->input->post('remark');
    $period_code = $this->input->post('period_code');
    $customer_guid = $_SESSION['customer_guid'];
    $controller = $this->router->fetch_class();
    $function = $this->router->fetch_method();
    $user_guid = $this->session->userdata('user_guid');
    $now_time = $this->db->query("SELECT NOW() AS now_time")->row('now_time');
    $user_id = $this->db->query("SELECT a.user_id FROM lite_b2b.set_user a WHERE a.user_guid ='$user_guid'")->row('user_id');

    if($action_type != 'before_go_live')
    {
      $ref_no_array = implode(",",$ref_no);
      $ref_no_array = explode(",",$ref_no_array);
      $ref_no_implode = implode(",",$ref_no);
      $ref_no = implode("','",$ref_no);
      $ref_no = "'".$ref_no."'";
      //print_r(count($ref_no_array)); echo '<br/>';
      //print_r($ref_no_array); die;

      $set_date = date('Y-m-04');
      $cur_date = $this->db->query("SELECT CURDATE() as curdate")->row('curdate');
      if($cur_date <= $set_date)
      {
        $data = array(
          'para1' => 1,
          'msg' => 'Sorry, B2B processing Invoice cut-off of the month.',
        );    
        echo json_encode($data);  
        die;
      }

      if(($ref_no == '') || ($ref_no == null) || ($ref_no =='null'))
      {
        $data = array(
            'para1' => 1,
            'msg' => 'Invalid Ref Number. Error To Do Action.',
        );    
        echo json_encode($data);  
        die;
      }
    }

    if($action_type == 'before_go_live')
    {
      if(($period_code == '') || ($period_code == null) || ($period_code =='null'))
      {
        $data = array(
            'para1' => 1,
            'msg' => 'Invalid Period Code. Error To Do Action.',
        );    
        echo json_encode($data);  
        die;
      }
    }

    #action1
    if($action_type == 'status_to_new')
    {
      if($doc_type == 'pomain')
      {   
        $check_refno = $this->db->query("SELECT * FROM b2b_summary.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = 'viewed' OR status = 'Accepted' OR status = 'Rejected')");

        //echo $this->db->last_query(); die;
        if($check_refno->num_rows() != count($ref_no_array))
        {
            $data = array(
            'para1' => 1,
            'msg' => 'POMAIN Invalid Data. Error To Update.',
            );    
            echo json_encode($data);  
            die;
        }

        $logs_1 = array(
          'logs_controller' => $controller,
          'logs_function' => $function,
          'logs_query' => $this->db->last_query(),
          'logs_details' => json_encode($check_refno->result()),
          'created_at' => $now_time,
          'created_by' => $user_id,
          'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
          'action_type' => 'Status To New',
          'customer_guid' => $customer_guid,
        );   
        $this->db->insert('lite_b2b.update_logs',$logs_1);

        $update_data = $this->db->query("UPDATE b2b_summary.pomain SET status = '', b2b_status = 'readysend' , rejected_remark = '' , rejected = '0' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = 'Accepted' OR status = 'Rejected')");

        $last_query = $this->db->last_query();

        $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' ");

        $logs_2 = array(
          'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
          'logs_controller' => $controller,
          'logs_function' => $function,
          'logs_query' => $last_query,
          'logs_details' => json_encode($check_po_after_update->result()),
          'created_at' => $now_time,
          'created_by' => $user_id, 
          'action_type' => 'Status To New Update',
          'customer_guid' => $customer_guid,
        );   
        
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
            'msg' => 'Error.',

            );    
            echo json_encode($data);   
        }

        $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      elseif ($doc_type == 'grmain' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.grmain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND ( status = 'viewed')");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'GRMAIN Invalid Data. Error To Update.',
              );    
              echo json_encode($data);  
              die;
          }

          $logs_1 = array(
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $this->db->last_query(),
            'logs_details' => json_encode($check_refno->result()),
            'created_at' => $now_time,
            'created_by' => $user_id,
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),  
            'action_type' => 'Status To New',
            'customer_guid' => $customer_guid,
          );   
          $this->db->insert('lite_b2b.update_logs',$logs_1);

          $update_data = $this->db->query("UPDATE b2b_summary.grmain SET status = '' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed' ");

          $last_query = $this->db->last_query();

          $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.grmain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' ");

          $logs_2 = array(
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $last_query,
            'logs_details' => json_encode($check_po_after_update->result()),
            'created_at' => $now_time,
            'created_by' => $user_id, 
            'action_type' => 'Status To New Update',
            'customer_guid' => $customer_guid,
          );   
          
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
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

          $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      elseif ($doc_type == 'grda' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.grmain_dncn WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'GRDA Invalid Data. Error To Update.',
              );    
              echo json_encode($data);  
              die;
          }

          $logs_1 = array(
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $this->db->last_query(),
            'logs_details' => json_encode($check_refno->result()),
            'created_at' => $now_time,
            'created_by' => $user_id,
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),  
            'action_type' => 'Status To New',
            'customer_guid' => $customer_guid,
          );   
          $this->db->insert('lite_b2b.update_logs',$logs_1);

          $update_data = $this->db->query("UPDATE b2b_summary.grmain_dncn SET status = '' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed' ");

          $last_query = $this->db->last_query();

          $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.grmain_dncn WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' ");

          $logs_2 = array(
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $last_query,
            'logs_details' => json_encode($check_po_after_update->result()),
            'created_at' => $now_time,
            'created_by' => $user_id, 
            'action_type' => 'Status To New Update',
            'customer_guid' => $customer_guid,
          );   
          
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
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

          $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      elseif ($doc_type == 'dbnotemain' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.dbnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PRDN Invalid Data. Error To Update.',
              );    
              echo json_encode($data);  
              die;
          }

          $logs_1 = array(
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $this->db->last_query(),
            'logs_details' => json_encode($check_refno->result()),
            'created_at' => $now_time,
            'created_by' => $user_id,
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),  
            'action_type' => 'Status To New',    
            'customer_guid' => $customer_guid,
          );   
          $this->db->insert('lite_b2b.update_logs',$logs_1);

          $update_data = $this->db->query("UPDATE b2b_summary.dbnotemain SET status = '' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed' ");

          $last_query = $this->db->last_query();

          $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.dbnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' ");

          $logs_2 = array(
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $last_query,
            'logs_details' => json_encode($check_po_after_update->result()),
            'created_at' => $now_time,
            'created_by' => $user_id, 
            'action_type' => 'Status To New Update',
            'customer_guid' => $customer_guid,
          );   
          
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
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

          $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      elseif ($doc_type == 'cnnotemain' )
      {
         $check_refno = $this->db->query("SELECT * FROM b2b_summary.cnnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PRCN Invalid Data. Error To Update.',
              );    
              echo json_encode($data);  
              die;
          }

          $logs_1 = array(
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $this->db->last_query(),
            'logs_details' => json_encode($check_refno->result()),
            'created_at' => $now_time,
            'created_by' => $user_id,
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),      
            'action_type' => 'Status To New',
            'customer_guid' => $customer_guid,
          );   
          $this->db->insert('lite_b2b.update_logs',$logs_1);

          $update_data = $this->db->query("UPDATE b2b_summary.cnnotemain SET status = '' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed' ");

          $last_query = $this->db->last_query();

          $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.cnnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' ");

          $logs_2 = array(
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $last_query,
            'logs_details' => json_encode($check_po_after_update->result()),
            'created_at' => $now_time,
            'created_by' => $user_id, 
            'action_type' => 'Status To New Update',
            'customer_guid' => $customer_guid,
          );   
          
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
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

          $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      elseif ($doc_type == 'cndn_amt' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.cndn_amt WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PCN Invalid Data. Error To Update.',
              );    
              echo json_encode($data);  
              die;
          }

          $logs_1 = array(
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $this->db->last_query(),
            'logs_details' => json_encode($check_refno->result()),
            'created_at' => $now_time,
            'created_by' => $user_id,
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),     
            'action_type' => 'Status To New',
            'customer_guid' => $customer_guid,
          );   
          $this->db->insert('lite_b2b.update_logs',$logs_1);

          $update_data = $this->db->query("UPDATE b2b_summary.cndn_amt SET status = '' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed' ");

          $last_query = $this->db->last_query();

          $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.cndn_amt WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' ");

          $logs_2 = array(
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $last_query,
            'logs_details' => json_encode($check_po_after_update->result()),
            'created_at' => $now_time,
            'created_by' => $user_id, 
            'action_type' => 'Status To New Update',
            'customer_guid' => $customer_guid,
          );   
          
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
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

          $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      elseif ($doc_type == 'pci' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.promo_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PCI Invalid Data. Error To Update.',
              );    
              echo json_encode($data);  
              die;
          }

          $logs_1 = array(
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $this->db->last_query(),
            'logs_details' => json_encode($check_refno->result()),
            'created_at' => $now_time,
            'created_by' => $user_id,
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),     
            'action_type' => 'Status To New',
            'customer_guid' => $customer_guid,
          );   
          $this->db->insert('lite_b2b.update_logs',$logs_1);

          $update_data = $this->db->query("UPDATE b2b_summary.promo_taxinv SET status = '' WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed' ");

          $last_query = $this->db->last_query();

          $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.promo_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' ");

          $logs_2 = array(
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $last_query,
            'logs_details' => json_encode($check_po_after_update->result()),
            'created_at' => $now_time,
            'created_by' => $user_id, 
            'action_type' => 'Status To New Update',
            'customer_guid' => $customer_guid,
          );   
          
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
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

          $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      elseif ($doc_type == 'display_incentive' )
      {
         $check_refno = $this->db->query("SELECT * FROM b2b_summary.discheme_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'Display Incentive Invalid Data. Error To Update.',
              );    
              echo json_encode($data);  
              die;
          }

          $logs_1 = array(
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $this->db->last_query(),
            'logs_details' => json_encode($check_refno->result()),
            'created_at' => $now_time,
            'created_by' => $user_id,
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),      
            'action_type' => 'Status To New',
            'customer_guid' => $customer_guid,
          );   
          $this->db->insert('lite_b2b.update_logs',$logs_1);

          $update_data = $this->db->query("UPDATE b2b_summary.discheme_taxinv SET status = '' WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'viewed' ");

          $last_query = $this->db->last_query();

          $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.discheme_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' ");

          $logs_2 = array(
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $last_query,
            'logs_details' => json_encode($check_po_after_update->result()),
            'created_at' => $now_time,
            'created_by' => $user_id, 
            'action_type' => 'Status To New Update',
            'customer_guid' => $customer_guid,
          );   
          
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
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

          $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      else
      {
          $data = array(
          'para1' => 1,
          'msg' => 'Invalid Action. Error To Update.',
          );    
          echo json_encode($data);  
          die;
      }
    }

    #action2
    if($action_type == 'hide_the_data')
    {
      if($doc_type == 'pomain')
      {   
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = '' OR status = 'viewed' OR status = 'printed' OR status = 'cancel') ");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'POMAIN Invalid Data/Status. Error To Hide.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_amend.pomain 
          SELECT *,'','','' FROM b2b_summary.pomain
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_amend.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_summary.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Hide Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $update_amend = $this->db->query("UPDATE b2b_amend.pomain SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    

              $data = array(
              'para1' => 0,
              'msg' => 'Hide Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

      }
      elseif ($doc_type == 'grmain' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.grmain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = '' OR status = 'viewed' OR status = 'printed')");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'GRMAIN Invalid Data. Error To Hide.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_amend.grmain 
          SELECT *,'','','' FROM b2b_summary.grmain
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_amend.grmain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_summary.grmain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Hide Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $update_amend = $this->db->query("UPDATE b2b_amend.grmain SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");  

              $data = array(
              'para1' => 0,
              'msg' => 'Hide Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

      }
      elseif ($doc_type == 'grda' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.grmain_dncn WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = '' OR status = 'viewed' OR status = 'printed')");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'GRDA Invalid Data. Error To Hide.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_amend.grmain_dncn 
          SELECT *,'','','' FROM b2b_summary.grmain_dncn
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_amend.grmain_dncn WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_summary.grmain_dncn WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Hide Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $update_amend = $this->db->query("UPDATE b2b_amend.grmain_dncn SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'"); 

              $data = array(
              'para1' => 0,
              'msg' => 'Hide Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'dbnotemain' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.dbnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = '' OR status = 'viewed' OR status = 'printed')");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PRDN Invalid Data. Error To Hide.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_amend.dbnotemain 
          SELECT *,'','','' FROM b2b_summary.dbnotemain
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_amend.dbnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_summary.dbnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Hide Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $update_amend = $this->db->query("UPDATE b2b_amend.dbnotemain SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'"); 

              $data = array(
              'para1' => 0,
              'msg' => 'Hide Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'cnnotemain' )
      {
         $check_refno = $this->db->query("SELECT * FROM b2b_summary.cnnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = '' OR status = 'viewed' OR status = 'printed')");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PRCN Invalid Data. Error To Hide.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_amend.cnnotemain 
          SELECT *,'','','' FROM b2b_summary.cnnotemain
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_amend.cnnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_summary.cnnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Hide Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $update_amend = $this->db->query("UPDATE b2b_amend.cnnotemain SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'"); 

              $data = array(
              'para1' => 0,
              'msg' => 'Hide Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'cndn_amt' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.cndn_amt WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = '' OR status = 'viewed' OR status = 'printed')");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PCN Invalid Data. Error To Hide.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_amend.cndn_amt 
          SELECT *,'','','' FROM b2b_summary.cndn_amt
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_amend.cndn_amt WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_summary.cndn_amt WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Hide Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $update_amend = $this->db->query("UPDATE b2b_amend.cndn_amt SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'"); 

              $data = array(
              'para1' => 0,
              'msg' => 'Hide Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'pci' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.promo_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = '' OR status = 'viewed' OR status = 'printed')");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PCI Invalid Data. Error To Hide.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_amend.promo_taxinv 
          SELECT *,'','','' FROM b2b_summary.promo_taxinv
          WHERE inv_refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_amend.promo_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_summary.promo_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Hide Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $update_amend = $this->db->query("UPDATE b2b_amend.promo_taxinv SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'"); 

              $data = array(
              'para1' => 0,
              'msg' => 'Hide Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'display_incentive' )
      {
         $check_refno = $this->db->query("SELECT * FROM b2b_summary.discheme_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid' AND (status = '' OR status = 'viewed' OR status = 'printed')");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'Display Incentive Invalid Data. Error To Hide.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_amend.discheme_taxinv 
          SELECT *,'','','' FROM b2b_summary.discheme_taxinv
          WHERE inv_refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_amend.discheme_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_summary.discheme_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Hide Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $update_amend = $this->db->query("UPDATE b2b_amend.discheme_taxinv SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");    

              $data = array(
              'para1' => 0,
              'msg' => 'Hide Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      else
      {
          $data = array(
          'para1' => 1,
          'msg' => 'Invalid Action. Error To Hide.',
          );    
          echo json_encode($data);  
          die;
      }
    }

    #action3
    if($action_type == 'show_the_data')
    {
      if($doc_type == 'pomain')
      {   
          $check_refno = $this->db->query("SELECT * FROM b2b_amend.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'POMAIN Invalid Data. Error To Show.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_summary.pomain 
          SELECT `customer_guid`,`status`,`RefNo`,`PODate`,`DeliverDate`,`DueDate`,`IssueStamp`,`IssuedBy`,`LastStamp`,`Dept`,`Location`,`ApprovedBy`,`SCode`,`SName`,`STerm`,`STel`,`SFax`,`Remark`,`SubTotal1`,`Discount1`,`Discount1Type`,`SubTotal2`,`Discount2`,`Discount2Type`,`Total`,`BillStatus`,`AccStatus`,`Closed`,`Amendment`,`Completed`,`Disc1Percent`,`Disc2Percent`,`SubDeptCode`,`postby`,`postdatetime`,`CalDueDateby`,`expiry_date`,`pur_expiry_days`,`hq_update`,`cp_main_guid`,`AutoClosePO`,`stockday_min`,`stockday_max`,`send`,`send_remark`,`send_at`,`send_by`,`rejected`,`rejected_remark`,`rejected_at`,`rejected_by`,`approved`,`approved_remark`,`approved_at`,`approved_by`,`loc_group`,`run_cost`,`rebate_amt`,`dn_amt`,`in_kind`,`cross_ref`,`cross_ref_module`,`hq_issue`,`gst_tax_sum`,`tax_code_purchase`,`total_include_tax`,`gst_tax_rate`,`price_include_tax`,`surchg_tax_sum`,`tax_inclusive`,`doc_name_reg`,`ibt`,`multi_tax_code`,`refno2`,`discount_as_inv`,`ibt_gst`,`rebate_as_inv`,`uploaded`,`uploaded_at`,`unpost`,`unpost_at`,`unpost_by`,`cancel`,`cancel_at`,`cancel_by`,`cancel_reason`,`b2b_status`,`hide_reason`,`old_expiry`,`extend` FROM b2b_amend.pomain
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_summary.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_amend.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Show Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $data = array(
              'para1' => 0,
              'msg' => 'Show Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

      }
      elseif ($doc_type == 'grmain' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_amend.grmain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'GRMAIN Invalid Data. Error To Show.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_summary.grmain 
          SELECT `customer_guid`,`status`,`RefNo`,`Location`,`DONo`,`InvNo`,`DocDate`,`GRDate`,`IssueStamp`,`LastStamp`,`Code`,`Name`,`Term`,`Receivedby`,`Remark`,`BillStatus`,`AccStatus`,`DueDate`,`Total`,`Closed`,`Subtotal1`,`Discount1`,`Discount1Type`,`Subtotal2`,`Discount2`,`Discount2Type`,`Disc1Percent`,`Disc2Percent`,`Cancelled`,`DOState`,`InvState`,`InvRefno`,`subdept`,`CalcCost`,`SubDeptCode`,`consign`,`postby`,`postdatetime`,`unpostby`,`unpostdatetime`,`CalDueDateby`,`hq_update`,`EXPORT_ACCOUNT`,`EXPORT_AT`,`EXPORT_BY`,`InvAmount_Vendor`,`InvSurchargeDisc_Vendor`,`InvNetAmt_Vendor`,`loc_group`,`pay_by_invoice`,`rebate_amt`,`ibt`,`dn_amt`,`m_trans_type`,`in_kind`,`rebate`,`gst_tax_sum`,`tax_code_purchase`,`gst_tax_rate`,`gst_tax_sum_inv`,`InvSurcharge`,`price_include_tax`,`surchg_tax_sum`,`surchg_tax_sum_inv`,`total_include_tax`,`doc_name_reg`,`multi_tax_code`,`refno2`,`gst_adj`,`rounding_adj`,`discount_as_inv`,`rebate_as_inv`,`ibt_gst`,`acc_post_date`,`uploaded`,`uploaded_at`,`input_amt_exc_tax`,`input_gst`,`input_amt_inc_tax`,`amt_matched`,`ibt_qty_actual`,`ibt_qty_grda`,`cross_ref`,`cross_ref_module`,`b2b_sync_acc` FROM b2b_amend.grmain
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_summary.grmain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_amend.grmain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Show Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $data = array(
              'para1' => 0,
              'msg' => 'Show Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

      }
      elseif ($doc_type == 'grda' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_amend.grmain_dncn WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'GRDA Invalid Data. Error To Show.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_summary.grmain_dncn 
          SELECT `customer_guid`,`status`,`location`,`RefNo`,`VarianceAmt`,`Created_at`,`Created_by`,`Updated_at`,`Updated_by`,`hq_update`,`EXPORT_ACCOUNT`,`EXPORT_AT`,`EXPORT_BY`,`transtype`,`share_cost`,`gst_tax_sum`,`gst_adjust`,`gl_code`,`tax_invoice`,`ap_sup_code`,`refno2`,`rounding_adj`,`sup_cn_no`,`sup_cn_date`,`dncn_date`,`dncn_date_acc`,`trans_seq`,`b2b_sync_acc` FROM b2b_amend.grmain_dncn
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_summary.grmain_dncn WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_amend.grmain_dncn WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Show Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $data = array(
              'para1' => 0,
              'msg' => 'Show Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'dbnotemain' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_amend.dbnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PRDN Invalid Data. Error To Show.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_summary.dbnotemain 
          SELECT `customer_guid`,`status`,`Type`,`RefNo`,`Location`,`DocNo`,`DocDate`,`IssueStamp`,`LastStamp`,`PONo`,`SCType`,`Code`,`Name`,`Term`,`Issuedby`,`Remark`,`BillStatus`,`AccStatus`,`DueDate`,`Amount`,`Closed`,`SubDeptCode`,`postby`,`postdatetime`,`Consign`,`EXPORT_ACCOUNT`,`EXPORT_AT`,`EXPORT_BY`,`hq_update`,`locgroup`,`ibt`,`SubTotal1`,`Discount1`,`Discount1Type`,`SubTotal2`,`Discount2`,`Discount2Type`,`gst_tax_sum`,`tax_code_purchase`,`sup_cn_no`,`sup_cn_date`,`doc_name_reg`,`gst_tax_rate`,`multi_tax_code`,`refno2`,`surchg_tax_sum`,`gst_adj`,`rounding_adj`,`unpostby`,`unpostdatetime`,`ibt_gst`,`acc_posting_date`,`RoundAdjNeed`,`stock_collected`,`date_collected`,`stock_collected_by`,`b2b_sync_acc`,`CONVERTED_FROM_MODULE`,`CONVERTED_FROM_GUID` FROM b2b_amend.dbnotemain
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_summary.dbnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_amend.dbnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Show Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $data = array(
              'para1' => 0,
              'msg' => 'Show Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'cnnotemain' )
      {
         $check_refno = $this->db->query("SELECT * FROM b2b_amend.cnnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PRCN Invalid Data. Error To Show.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_summary.cnnotemain 
          SELECT `customer_guid`,`Type`,`status`,`RefNo`,`Location`,`DocNo`,`DocDate`,`IssueStamp`,`LastStamp`,`PONo`,`SCType`,`Code`,`Name`,`term`,`Issuedby`,`Remark`,`BillStatus`,`AccStatus`,`DueDate`,`Amount`,`Closed`,`postby`,`postdatetime`,`subdeptcode`,`hq_update`,`EXPORT_ACCOUNT`,`EXPORT_AT`,`EXPORT_BY`,`Consign`,`locgroup`,`ibt`,`SubTotal1`,`Discount1`,`Discount1Type`,`SubTotal2`,`Discount2`,`Discount2Type`,`gst_tax_sum`,`tax_code_purchase`,`sup_cn_no`,`sup_cn_date`,`refno2`,`gst_tax_rate`,`multi_tax_code`,`doc_name_reg`,`surchg_tax_sum`,`gst_adj`,`rounding_adj`,`unpostdatetime`,`unpostby`,`ibt_gst`,`acc_posting_date`,`RoundAdjNeed` FROM b2b_amend.cnnotemain
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_summary.cnnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_amend.cnnotemain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Show Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $data = array(
              'para1' => 0,
              'msg' => 'Show Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'cndn_amt' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_amend.cndn_amt WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PCN Invalid Data. Error To Show.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_summary.cndn_amt 
          SELECT `customer_guid`,`cndn_guid`,`trans_type`,`loc_group`,`location`,`STATUS`,`refno`,`docno`,`docdate`,`code`,`name`,`tax_code`,`remark`,`term`,`amount`,`gst_tax_sum`,`amount_include_tax`,`cndn_group`,`created_at`,`created_by`,`updated_at`,`updated_by`,`posted`,`posted_at`,`posted_by`,`Consign`,`sup_cn_no`,`sup_cn_date`,`doc_name_reg`,`gst_tax_rate`,`multi_tax_code`,`refno2`,`gst_adj`,`rounding_adj`,`unpostby`,`unpostdatetime`,`ibt_gst`,`subdeptcode`,`EXPORT_ACCOUNT`,`EXPORT_AT`,`EXPORT_BY`,`hq_update`,`ibt`,`acc_posting_date`,`trans_type_acc`,`RoundAdjNeed`,`RoundingAdjust` FROM b2b_amend.cndn_amt
          WHERE refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_summary.cndn_amt WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_amend.cndn_amt WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Show Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $data = array(
              'para1' => 0,
              'msg' => 'Show Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'pci' )
      {
          $check_refno = $this->db->query("SELECT * FROM b2b_amend.promo_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'PCI Invalid Data. Error To Show.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_summary.promo_taxinv 
          SELECT `customer_guid`,`STATUS`,`taxinv_guid`,`loc_group`,`loc_group_issue`,`seq`,`docdate`,`term`,`datedue`,`tax_inclusive`,`sup_code`,`sup_name`,`total_bf_tax`,`tax_code_supply`,`gst_tax_rate`,`gst_value`,`total_af_tax`,`gst_adj`,`rounding_adj`,`total_net`,`remark`,`created_at`,`created_by`,`updated_at`,`updated_by`,`posted`,`posted_at`,`posted_by`,`inv_refno`,`promo_refno`,`promo_guid`,`AR_cuscode`,`gl_code`,`EXPORT_ACCOUNT`,`EXPORT_AT`,`EXPORT_BY`,`hq_update`,`refno`,`refno_line`,`uploaded`,`uploaded_at`,`issued_by_hq` FROM b2b_amend.promo_taxinv
          WHERE inv_refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_summary.promo_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_amend.promo_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Show Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);

              $data = array(
              'para1' => 0,
              'msg' => 'Show Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      elseif ($doc_type == 'display_incentive' )
      {
         $check_refno = $this->db->query("SELECT * FROM b2b_amend.discheme_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'Display Incentive Invalid Data. Error To Show.',
              );    
              echo json_encode($data);  
              die;
          }

          $move_to_amend = $this->db->query("INSERT INTO b2b_summary.discheme_taxinv 
          SELECT `customer_guid`,`STATUS`,`taxinv_guid`,`loc_group`,`loc_group_issue`,`seq`,`docdate`,`term`,`datedue`,`tax_inclusive`,`sup_code`,`sup_name`,`total_bf_tax`,`tax_code_supply`,`gst_tax_rate`,`gst_value`,`total_af_tax`,`gst_adj`,`rounding_adj`,`total_net`,`remark`,`created_at`,`created_by`,`updated_at`,`updated_by`,`posted`,`posted_at`,`posted_by`,`inv_refno`,`refno`,`refno_line`,`AR_cuscode`,`gl_code`,`EXPORT_ACCOUNT`,`EXPORT_AT`,`EXPORT_BY`,`hq_update`,`uploaded`,`uploaded_at`,`division` FROM b2b_amend.discheme_taxinv
          WHERE inv_refno IN ($ref_no) 
          AND customer_guid = '$customer_guid'");

          $check_amend = $this->db->query("SELECT * FROM b2b_summary.discheme_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");    
          if($check_amend->num_rows() == $check_refno->num_rows()){

              $hide_data = $this->db->query("DELETE FROM b2b_amend.discheme_taxinv WHERE inv_refno IN ($ref_no) AND customer_guid = '$customer_guid'");

              $last_query_hide = $this->db->last_query();

              $logs_3 = array(
                'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                'logs_controller' => $controller,
                'logs_function' => $function,
                'logs_query' => $last_query_hide,
                'logs_details' => json_encode($check_refno->result()),
                'created_at' => $now_time,
                'created_by' => $user_id, 
                'action_type' => 'Show Data',
                'customer_guid' => $customer_guid,
              ); 

              $this->db->insert('lite_b2b.update_logs',$logs_3);
              
              $data = array(
              'para1' => 0,
              'msg' => 'Show Successfully',

              );    
              echo json_encode($data);   
          }
          else
          {   
              $data = array(
              'para1' => 1,
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }
      }
      else
      {
          $data = array(
          'para1' => 1,
          'msg' => 'Invalid Action. Error To Show.',
          );    
          echo json_encode($data);  
          die;
      }
    }

    #action4 status_to_accept
    if($action_type == 'status_to_accept')
    {
      if($doc_type == 'pomain')
      {   
          $check_refno = $this->db->query("SELECT * FROM b2b_summary.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' AND BillStatus = '1' ");

          //echo $this->db->last_query(); die;
          if($check_refno->num_rows() != count($ref_no_array))
          {
              $data = array(
              'para1' => 1,
              'msg' => 'POMAIN Invalid Data. Error To Update.',
              );    
              echo json_encode($data);  
              die;
          }

          $logs_1 = array(
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $this->db->last_query(),
            'logs_details' => json_encode($check_refno->result()),
            'created_at' => $now_time,
            'created_by' => $user_id,
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),      
            'action_type' => 'Status To Accepted',
            'customer_guid' => $customer_guid,
          );   
          $this->db->insert('lite_b2b.update_logs',$logs_1);

          $update_data = $this->db->query("UPDATE b2b_summary.pomain SET status = 'Accepted', rejected_remark = '' , rejected = '0' WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = '' AND BillStatus = '1'");

          $last_query = $this->db->last_query();

          $check_po_after_update = $this->db->query("SELECT * FROM b2b_summary.pomain WHERE refno IN ($ref_no) AND customer_guid = '$customer_guid' AND status = 'Accepted' AND BillStatus = '1'");

          $logs_2 = array(
            'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
            'logs_controller' => $controller,
            'logs_function' => $function,
            'logs_query' => $last_query,
            'logs_details' => json_encode($check_po_after_update->result()),
            'created_at' => $now_time,
            'created_by' => $user_id, 
            'action_type' => 'Status To Accepted',
            'customer_guid' => $customer_guid,
          );   
          
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
              'msg' => 'Error.',

              );    
              echo json_encode($data);   
          }

          $this->db->insert('lite_b2b.update_logs',$logs_2);
      }
      else
      {
          $data = array(
          'para1' => 1,
          'msg' => 'Invalid Action. Error To Update.',
          );    
          echo json_encode($data);  
          die;
      }
    }

    #action5 hide document before go live
    if(in_array('IAVA',$_SESSION['module_code']))
    {
      if($action_type == 'before_go_live')
      {
        if($doc_type == 'pomain')
        {
          if(in_array('IAVA',$_SESSION['module_code']))
          {
            $check_refno = $this->db->query("SELECT * FROM b2b_summary.pomain WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

            //echo $this->db->last_query(); die;
            if($check_refno->num_rows() == 0)
            {
                $data = array(
                'para1' => 1,
                'msg' => 'POMAIN Invalid Data. Error To Hide.',
                );    
                echo json_encode($data);  
                die;
            }

            $move_to_amend = $this->db->query("INSERT INTO b2b_amend.pomain 
            SELECT *,'','','' FROM b2b_summary.pomain
            WHERE LEFT(PODate, 7) = '$period_code'
            AND customer_guid = '$customer_guid'");

            $check_amend = $this->db->query("SELECT * FROM b2b_amend.pomain WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");    
            if($check_amend->num_rows() == $check_refno->num_rows()){

                $hide_data = $this->db->query("DELETE FROM b2b_summary.pomain WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $last_query_hide = $this->db->last_query();

                $logs_3 = array(
                  'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                  'logs_controller' => $controller,
                  'logs_function' => $function,
                  'logs_query' => $last_query_hide,
                  'logs_details' => json_encode($check_refno->result()),
                  'created_at' => $now_time,
                  'created_by' => $user_id, 
                  'action_type' => 'Before GO Live',
                ); 

                $this->db->insert('lite_b2b.update_logs',$logs_3);

                $update_amend = $this->db->query("UPDATE b2b_amend.pomain SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");  

                $data = array(
                'para1' => 0,
                'msg' => 'Hide Successfully',

                );    
                echo json_encode($data);   
            }
            else
            {   
                $data = array(
                'para1' => 1,
                'msg' => 'Error.',

                );    
                echo json_encode($data);   
            }
          }
        }else if($doc_type == 'grmain')
        {
          if(in_array('IAVA',$_SESSION['module_code']))
          {
            $check_refno = $this->db->query("SELECT * FROM b2b_summary.grmain WHERE LEFT(GRDate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

            //echo $this->db->last_query(); die;
            if($check_refno->num_rows() == 0)
            {
                $data = array(
                'para1' => 1,
                'msg' => 'GRMAIN Invalid Data. Error To Hide.',
                );    
                echo json_encode($data);  
                die;
            }

            $move_to_amend = $this->db->query("INSERT INTO b2b_amend.grmain 
            SELECT *,'','','' FROM b2b_summary.grmain
            WHERE LEFT(GRDate, 7) = '$period_code'
            AND customer_guid = '$customer_guid'");

            $check_amend = $this->db->query("SELECT * FROM b2b_amend.grmain WHERE LEFT(GRDate, 7) = '$period_code' AND customer_guid = '$customer_guid'");    
            if($check_amend->num_rows() == $check_refno->num_rows()){

                $hide_data = $this->db->query("DELETE FROM b2b_summary.grmain WHERE LEFT(GRDate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $last_query_hide = $this->db->last_query();

                $logs_3 = array(
                  'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                  'logs_controller' => $controller,
                  'logs_function' => $function,
                  'logs_query' => $last_query_hide,
                  'logs_details' => json_encode($check_refno->result()),
                  'created_at' => $now_time,
                  'created_by' => $user_id, 
                  'action_type' => 'Before GO Live',
                ); 

                $this->db->insert('lite_b2b.update_logs',$logs_3);

                $update_amend = $this->db->query("UPDATE b2b_amend.grmain SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");  

                $data = array(
                'para1' => 0,
                'msg' => 'Hide Successfully',

                );    
                echo json_encode($data);   
            }
            else
            {   
                $data = array(
                'para1' => 1,
                'msg' => 'Error.',

                );    
                echo json_encode($data);   
            }
                
          }
        }
        else if($doc_type == 'grda')
        {
          if(in_array('IAVA',$_SESSION['module_code']))
          {
            $check_refno = $this->db->query("SELECT * FROM b2b_summary.grmain_dncn WHERE LEFT(created_at, 7) = '$period_code' AND customer_guid = '$customer_guid'");

            //echo $this->db->last_query(); die;
            if($check_refno->num_rows() == 0)
            {
                $data = array(
                'para1' => 1,
                'msg' => 'GRDA Invalid Data. Error To Hide.',
                );    
                echo json_encode($data);  
                die;
            }

            $move_to_amend = $this->db->query("INSERT INTO b2b_amend.grmain_dncn 
            SELECT *,'','','' FROM b2b_summary.grmain_dncn
            WHERE LEFT(created_at, 7) = '$period_code'
            AND customer_guid = '$customer_guid'");

            $check_amend = $this->db->query("SELECT * FROM b2b_amend.grmain_dncn WHERE LEFT(created_at, 7) = '$period_code' AND customer_guid = '$customer_guid'");    
            if($check_amend->num_rows() == $check_refno->num_rows()){

                $hide_data = $this->db->query("DELETE FROM b2b_summary.grmain_dncn WHERE LEFT(created_at, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $last_query_hide = $this->db->last_query();

                $logs_3 = array(
                  'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                  'logs_controller' => $controller,
                  'logs_function' => $function,
                  'logs_query' => $last_query_hide,
                  'logs_details' => json_encode($check_refno->result()),
                  'created_at' => $now_time,
                  'created_by' => $user_id, 
                  'action_type' => 'Before GO Live',
                ); 

                $this->db->insert('lite_b2b.update_logs',$logs_3);

                $update_amend = $this->db->query("UPDATE b2b_amend.grmain_dncn SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");  

                $data = array(
                'para1' => 0,
                'msg' => 'Hide Successfully',

                );    
                echo json_encode($data);   
            }
            else
            {   
                $data = array(
                'para1' => 1,
                'msg' => 'Error.',

                );    
                echo json_encode($data);   
            }

          }
        }
        else if($doc_type == 'dbnotemain')
        {
          if(in_array('IAVA',$_SESSION['module_code']))
          {
            $check_refno = $this->db->query("SELECT * FROM b2b_summary.dbnotemain WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

            //echo $this->db->last_query(); die;
            if($check_refno->num_rows() == 0)
            {
                $data = array(
                'para1' => 1,
                'msg' => 'PRDN Invalid Data. Error To Hide.',
                );    
                echo json_encode($data);  
                die;
            }

            $move_to_amend = $this->db->query("INSERT INTO b2b_amend.dbnotemain 
            SELECT *,'','','' FROM b2b_summary.dbnotemain
            WHERE LEFT(docdate, 7) = '$period_code'
            AND customer_guid = '$customer_guid'");

            $check_amend = $this->db->query("SELECT * FROM b2b_amend.dbnotemain WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");    
            if($check_amend->num_rows() == $check_refno->num_rows()){

                $hide_data = $this->db->query("DELETE FROM b2b_summary.dbnotemain WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $last_query_hide = $this->db->last_query();

                $logs_3 = array(
                  'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                  'logs_controller' => $controller,
                  'logs_function' => $function,
                  'logs_query' => $last_query_hide,
                  'logs_details' => json_encode($check_refno->result()),
                  'created_at' => $now_time,
                  'created_by' => $user_id, 
                  'action_type' => 'Before GO Live',
                ); 

                $this->db->insert('lite_b2b.update_logs',$logs_3);

                $update_amend = $this->db->query("UPDATE b2b_amend.dbnotemain SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $data = array(
                'para1' => 0,
                'msg' => 'Hide Successfully',

                );    
                echo json_encode($data);   
            }
            else
            {   
                $data = array(
                'para1' => 1,
                'msg' => 'Error.',

                );    
                echo json_encode($data);   
            }
          }
        }
        else if($doc_type == 'cnnotemain')
        {
          if(in_array('IAVA',$_SESSION['module_code']))
          {
            $check_refno = $this->db->query("SELECT * FROM b2b_summary.cnnotemain WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

            //echo $this->db->last_query(); die;
            if($check_refno->num_rows() == 0)
            {
                $data = array(
                'para1' => 1,
                'msg' => 'PRCN Invalid Data. Error To Hide.',
                );    
                echo json_encode($data);  
                die;
            }

            $move_to_amend = $this->db->query("INSERT INTO b2b_amend.cnnotemain 
            SELECT *,'','','' FROM b2b_summary.cnnotemain
            WHERE LEFT(docdate, 7) = '$period_code'
            AND customer_guid = '$customer_guid'");

            $check_amend = $this->db->query("SELECT * FROM b2b_amend.cnnotemain WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");    
            if($check_amend->num_rows() == $check_refno->num_rows()){

                $hide_data = $this->db->query("DELETE FROM b2b_summary.cnnotemain WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $last_query_hide = $this->db->last_query();

                $logs_3 = array(
                  'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                  'logs_controller' => $controller,
                  'logs_function' => $function,
                  'logs_query' => $last_query_hide,
                  'logs_details' => json_encode($check_refno->result()),
                  'created_at' => $now_time,
                  'created_by' => $user_id, 
                  'action_type' => 'Before GO Live',
                ); 

                $this->db->insert('lite_b2b.update_logs',$logs_3);

                $update_amend = $this->db->query("UPDATE b2b_amend.cnnotemain SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $data = array(
                'para1' => 0,
                'msg' => 'Hide Successfully',

                );    
                echo json_encode($data);   
            }
            else
            {   
                $data = array(
                'para1' => 1,
                'msg' => 'Error.',

                );    
                echo json_encode($data);   
            }
          }
        }
        else if($doc_type == 'cndn_amt')
        {
          if(in_array('IAVA',$_SESSION['module_code']))
          {
            $check_refno = $this->db->query("SELECT * FROM b2b_summary.cndn_amt WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

            //echo $this->db->last_query(); die;
            if($check_refno->num_rows() == 0)
            {
                $data = array(
                'para1' => 1,
                'msg' => 'PCN Invalid Data. Error To Hide.',
                );    
                echo json_encode($data);  
                die;
            }

            $move_to_amend = $this->db->query("INSERT INTO b2b_amend.cndn_amt 
            SELECT *,'','','' FROM b2b_summary.cndn_amt
            WHERE LEFT(docdate, 7) = '$period_code'
            AND customer_guid = '$customer_guid'");

            $check_amend = $this->db->query("SELECT * FROM b2b_amend.cndn_amt WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");    
            if($check_amend->num_rows() == $check_refno->num_rows()){

                $hide_data = $this->db->query("DELETE FROM b2b_summary.cndn_amt WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $last_query_hide = $this->db->last_query();

                $logs_3 = array(
                  'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                  'logs_controller' => $controller,
                  'logs_function' => $function,
                  'logs_query' => $last_query_hide,
                  'logs_details' => json_encode($check_refno->result()),
                  'created_at' => $now_time,
                  'created_by' => $user_id, 
                  'action_type' => 'Before GO Live',
                ); 

                $this->db->insert('lite_b2b.update_logs',$logs_3);

                $update_amend = $this->db->query("UPDATE b2b_amend.cndn_amt SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $data = array(
                'para1' => 0,
                'msg' => 'Hide Successfully',

                );    
                echo json_encode($data);   
            }
            else
            {   
                $data = array(
                'para1' => 1,
                'msg' => 'Error.',

                );    
                echo json_encode($data);   
            }
          }
        }
        else if($doc_type == 'pci')
        {
          if(in_array('IAVA',$_SESSION['module_code']))
          {
            $check_refno = $this->db->query("SELECT * FROM b2b_summary.promo_taxinv WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

            //echo $this->db->last_query(); die;
            if($check_refno->num_rows() == 0)
            {
                $data = array(
                'para1' => 1,
                'msg' => 'PCI Invalid Data. Error To Hide.',
                );    
                echo json_encode($data);  
                die;
            }

            $move_to_amend = $this->db->query("INSERT INTO b2b_amend.promo_taxinv 
            SELECT *,'','','' FROM b2b_summary.promo_taxinv
            WHERE LEFT(docdate, 7) = '$period_code'
            AND customer_guid = '$customer_guid'");

            $check_amend = $this->db->query("SELECT * FROM b2b_amend.promo_taxinv WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");    
            if($check_amend->num_rows() == $check_refno->num_rows()){

                $hide_data = $this->db->query("DELETE FROM b2b_summary.promo_taxinv WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $last_query_hide = $this->db->last_query();

                $logs_3 = array(
                  'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                  'logs_controller' => $controller,
                  'logs_function' => $function,
                  'logs_query' => $last_query_hide,
                  'logs_details' => json_encode($check_refno->result()),
                  'created_at' => $now_time,
                  'created_by' => $user_id, 
                  'action_type' => 'Before GO Live',
                ); 

                $this->db->insert('lite_b2b.update_logs',$logs_3);

                $update_amend = $this->db->query("UPDATE b2b_amend.promo_taxinv SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $data = array(
                'para1' => 0,
                'msg' => 'Hide Successfully',

                );    
                echo json_encode($data);   
            }
            else
            {   
                $data = array(
                'para1' => 1,
                'msg' => 'Error.',

                );    
                echo json_encode($data);   
            }
          }
        }
        else if($doc_type == 'display_incentive')
        {
          if(in_array('IAVA',$_SESSION['module_code']))
          {
            $check_refno = $this->db->query("SELECT * FROM b2b_summary.discheme_taxinv WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

            //echo $this->db->last_query(); die;
            if($check_refno->num_rows() == 0)
            {
                $data = array(
                'para1' => 1,
                'msg' => 'Display Incentive Invalid Data. Error To Hide.',
                );    
                echo json_encode($data);  
                die;
            }

            $move_to_amend = $this->db->query("INSERT INTO b2b_amend.discheme_taxinv 
            SELECT *,'','','' FROM b2b_summary.discheme_taxinv
            WHERE LEFT(docdate, 7) = '$period_code'
            AND customer_guid = '$customer_guid'");

            $check_amend = $this->db->query("SELECT * FROM b2b_amend.discheme_taxinv WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");    
            if($check_amend->num_rows() == $check_refno->num_rows()){

                $hide_data = $this->db->query("DELETE FROM b2b_summary.discheme_taxinv WHERE LEFT(docdate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $last_query_hide = $this->db->last_query();

                $logs_3 = array(
                  'logs_guid' => $this->db->query("SELECT upper(replace(uuid(),'-','')) as guid")->row('guid'),
                  'logs_controller' => $controller,
                  'logs_function' => $function,
                  'logs_query' => $last_query_hide,
                  'logs_details' => json_encode($check_refno->result()),
                  'created_at' => $now_time,
                  'created_by' => $user_id, 
                  'action_type' => 'Before GO Live',
                ); 

                $this->db->insert('lite_b2b.update_logs',$logs_3);

                $update_amend = $this->db->query("UPDATE b2b_amend.discheme_taxinv SET hide_by = '$user_id' , hide_at = '$now_time' , hide_remark = '$remark' WHERE LEFT(PODate, 7) = '$period_code' AND customer_guid = '$customer_guid'");

                $data = array(
                'para1' => 0,
                'msg' => 'Hide Successfully',

                );    
                echo json_encode($data);   
            }
            else
            {   
                $data = array(
                'para1' => 1,
                'msg' => 'Error.',

                );    
                echo json_encode($data);   
            }
          }
        }
        else
        {
            $data = array(
            'para1' => 1,
            'msg' => 'Invalid Action. Error To Update.',
            );    
            echo json_encode($data);  
            die;
        }
      }
    }
  }
}
?>

