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
    
    public function index_get($id='')
    {
        
        if(!$id) $id = $this->get('id');
        
        if($id) // with id, we get one single user
        {
            $users = $this->User_model->get_users(array('user_id' => $id));
        }
        
        else // no id, we get all the users
        {
            $users = $this->User_model->get_all_users();
        }
        
        
        if($users)
        {
            
            $this->add_user_links($users);
            
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
            $this->response(array('error' => 'user could not be found'), 404);
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
            $this->add_user_links($result);
            
            
            
            $action_array = array();
            
            $action_array[] = array("href" => "/user",
                                    "rel" => "list",
                                    "method" => "get");
            
            $info = array("user" => $result,
                          "links" => $action_array
                          );
            
            $this->response($info, 201);

        }
        
        else
        {
            
            $this->response(NULL, 404);
            
        }
        
    }
    
    private function add_user_links($users)
    {
        foreach ($users as $user) {
            
            $user_id = $user->user_id;
            
            $user_links = array();
            $user_links[] = array("href" => "/user/$user_id",
                                  "rel" => "self",
                                  "method" => "GET");
            
            $user->links = $user_links;
            
        }
        //return $users;
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */