<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require_once(APPPATH.'libraries/REST_Controller.php');
    
class User extends REST_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
    
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->load->model('User_model');
    }
    
	public function index_get()
	{
        $users = $this->User_model->get_all_users();
        
        if($users)
        {
            
            $action_array = array();
            
            $action_array[] = array("href" => "/user",
                                    "rel" => "create",
                                    "method" => "POST");
            
            $info = array("users" => $users,
                          "links" => $action_array
                          );
            
            $this->response($info, 200); // 200 being the HTTP response code
        }
        
        else
        {
            $this->response(NULL, 404);
        }
    }

    
    public function index_post()
    {
        
        
        $data = array('user_id' => $this->post('user_id'));
        
        
        if($this->post('user_name')) {
            $data['user_name'] = $this->post('user_name');
        }
        
        if($this->post('user_email')) {
            $data['user_email'] = $this->post('user_email');
        }
        
        if($this->post('user_password')) {
            $data['user_password'] = $this->post('user_password');
        }
        
        $result = $this->User_model->insert_user($data);
        
        if($result)
        {
            
            $this->response($result, 201);

        }
        
        else
        {
            
            $this->response(NULL, 404);
            
        }
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */