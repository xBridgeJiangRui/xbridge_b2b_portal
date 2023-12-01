<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ticket_portal extends CI_Controller
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
        $this->load->library('Panda_PHPMailer'); 
         
    }

    public function index()
    {
        if($this->session->userdata('loginuser') == true && $this->session->userdata('userid') != '')
        { 
            $last_12_month_ticket = $this->db->query("

                SELECT COUNT(*) as Count FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 11 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 11 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 10 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 10 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 9 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 9 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 8 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 8 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 7 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 7 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 6 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 6 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 5 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 5 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 4 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 4 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 3 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 3 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 2 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 2 MONTH)
                            UNION ALL
                SELECT COUNT(*) FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                            UNION ALL
                SELECT COUNT(*) AS ticket_created_count FROM ticket WHERE YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH)
                            AND MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)
            ")->result();

            $Topic = $this->db->query("SELECT a.name, COUNT(a.name) AS topic_count FROM ticket_topic a INNER JOIN ticket b ON a.t_topic_guid = b.`topic_guid` GROUP BY name ORDER BY topic_count DESC");
 
            $topic1 = $this->db->query("SELECT 
                a.topic_guid,
                ROUND(
                    COUNT(*) / 
                    (SELECT 
                    COUNT(*) 
                    FROM
                    ticket) * 100,
                    0
                ) AS percent,
                COUNT(*) AS count,
                (SELECT 
                    COUNT(*) 
                FROM
                    ticket) - COUNT(*) AS others,
                b.name 
                FROM
                ticket a 
                INNER JOIN ticket_topic b 
                    ON a.topic_guid = b.t_topic_guid 
                GROUP BY a.topic_guid ORDER BY count DESC ");

            $all = $this->db->query("SELECT COUNT(*) as count FROM ticket")->row('count');

            $New = $this->db->query("SELECT COUNT(*) as count FROM ticket WHERE ticket_status = 'New'")->row('count');

            $In_Progress = $this->db->query("SELECT COUNT(*) as count FROM ticket WHERE ticket_status = 'In-Progress'")->row('count');

            $Closed = $this->db->query("SELECT COUNT(*) as count FROM ticket WHERE ticket_status = 'Closed'")->row('count');

            $category = $this->db->query("SELECT * FROM ticket_topic ORDER BY name ASC");

            $sub_category = $this->db->query("SELECT * FROM ticket_sub_topic ORDER BY name ASC");

            $ticket_child = $this->db->query("SELECT 
                  a.hide,
                  a.ticket_c_guid,                
                  a.messages_type,
                  a.messages,
                  a.created_at,
                  a.created_by,
                  a.ticket_file,
                  a.ticket_path,
                  b.user_name 
                FROM
                  ticket_child a 
                  INNER JOIN lite_b2b.set_user b 
                    ON a.`created_by` = b.`user_guid` 
                WHERE a.hide = 0
            ORDER BY created_at ASC ");
            


            $data = array (

                'last_12_month_ticket' => $last_12_month_ticket,


            );

            $this->load->view('header');
            $this->load->view('ticket/ticket_new', $data);
            $this->load->view('footer');
        }
        else
        {
            redirect('#');
        }
    }


}
