<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class Post_model extends CI_Model {
        
        function __construct()
        {
            // Call the Model constructor
            parent::__construct();
            $this->load->database();
        }
        
        function get_all_posts()
        {
            $query = $this->db->get('post');
            return $query->result();
        }
        
        function insert_post($data)
        {
            if($this->db->insert('post', $data))
                return TRUE;
            else
                return FALSE;
        }
        
        /*
        function insert_entry()
        {
            $this->title   = $_POST['title']; // please read the below note
            $this->content = $_POST['content'];
            $this->date    = time();
            
            $this->db->insert('entries', $this);
        }
        
        function update_entry()
        {
            $this->title   = $_POST['title'];
            $this->content = $_POST['content'];
            $this->date    = time();
            
            $this->db->update('entries', $this, array('id' => $_POST['id']));
        }
         
         */
        
    }