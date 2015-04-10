<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require_once(APPPATH.'libraries/REST_Controller.php');
    
class Post extends REST_Controller {

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
        $this->load->model('Post_model');
    }
    
	public function index_get()
	{
        $posts = $this->Post_model->get_all_posts();
        
        if($posts)
        {
            $this->response($posts, 200); // 200 being the HTTP response code
        }
        
        else
        {
            $this->response(NULL, 404);
        }
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */