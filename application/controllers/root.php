<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require_once(APPPATH.'libraries/REST_Controller.php');
    
class Root extends REST_Controller {

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
        //$this->load->model('User_model');

    }
    
	public function index_get()
	{
        
        $version = "1.0";
        
        $action_array = array();
        
        $action_array[] = array("href" => "/user",
                         "rel" => "list",
                         "method" => "GET");
        
        $action_array[] = array("href" => "/user",
                         "rel" => "create",
                         "method" => "POST");
        
        $action_array[] = array("href" => "/post",
                                "rel" => "list",
                                "method" => "GET");
        
        $action_array[] = array("href" => "/post",
                                "rel" => "create",
                                "method" => "POST");
        
        $info = array("version" => $version,
                      "links" => $action_array
                      );
        
        $this->response($info, 200); // 200 being the HTTP response code

    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */