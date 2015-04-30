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
        
        function get_users($where)
        {
            return $this->db->where($where)->get('user')->result();
        }
        
        function insert_user($data)
        {
            
            if ($this->db->insert('user', $data)) {
                return $this->db->get_where('user', array('user_id' => $this->db->insert_id()))->result();
            }
            else {
                return false;
            }
            
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