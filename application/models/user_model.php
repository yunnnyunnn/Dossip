<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class User_model extends CI_Model {
        
        function __construct()
        {
            // Call the Model constructor
            parent::__construct();
            $this->load->database();
        }
        
        function get_all_users()
        {
            $query = $this->db->get('user');
            return $query->result();
        }
        
        
        function insert_user($data)
        {
            if($this->db->insert('user', $data))
                return TRUE;
            else
                return FALSE;
        }
        
        
        /*
        function update_entry()
        {
            $this->title   = $_POST['title'];
            $this->content = $_POST['content'];
            $this->date    = time();
            
            $this->db->update('entries', $this, array('id' => $_POST['id']));
        }
         
         */
        
    }