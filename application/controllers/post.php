<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require_once(APPPATH.'libraries/REST_Controller.php');
    require_once(APPPATH.'libraries/vendor/autoload.php');
    use Aws\S3\S3Client;
    
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
        $this->load->model('User_model');

    }
    
    public function index_get($id='')
    {
        
        
        try {
            
            // Instantiate the S3 client with your AWS credentials
            $client = S3Client::factory(array(
                                                'credentials' => array(
                                                                       'key'    => AWS_ACCESS_KEY_ID,
                                                                       'secret' => AWS_SECRET_ACCESS_KEY,
                                                                       )
                                                ));
            
            // Upload an object to Amazon S3
            $result = $client->putObject(array(
                                               'Bucket' => 'dossip.dev',
                                               'Key'    => 'data.txt',
                                               'Body'   => 'Hello!'
                                               ));
            
            // Access parts of the result object
            echo $result['Expiration'] . "\n";
            echo $result['ServerSideEncryption'] . "\n";
            echo $result['ETag'] . "\n";
            echo $result['VersionId'] . "\n";
            echo $result['RequestId'] . "\n";
            
            // Get the URL the object can be downloaded from
            echo $result['ObjectURL'] . "\n";
            
            
        }
        catch(Exception $e) {
            
            exit($e->getMessage());
        }
        
        
        return;
        
        if(!$id) $id = $this->get('id');
        
        if($id) // with id, we get one single post
        {
            $posts = $this->Post_model->get_posts(array('post_id' => $id));
        }
        
        else // no id, we get all the users
        {
            $posts = $this->Post_model->get_all_posts();
        }
        
        
        if($posts)
        {
            
            $this->add_post_links($posts);
            
            $action_array = array();
            
            $action_array[] = array("href" => "/post",
                                    "rel" => "create",
                                    "method" => "POST");
            
            $info = array("posts" => $posts,
                          "links" => $action_array
                          );
            
            $this->response($info, 200); // 200 being the HTTP response code
        }
        
        else
        {
            $this->response(array('error' => 'post could not be found'), 404);
        }
    }
    
    public function index_post()
    {
        // check if all required data received
        if (!$this->post('post_title')||!$this->post('post_content')||!$this->post('post_lat')||!$this->post('post_long')||!$this->post('post_user')) {
            
            $this->response(array('error' => 'post values not complete'), 400);
            
        }
        
        // check if user exists
        $post_user_id = $this->post('post_user');
        
        if (!$this->User_model->get_users(array('user_id' => $post_user_id))) {
            $this->response(array('error' => 'no such user id'), 404);
        }
        
        
        
        $data = array('post_id' => $this->post('post_id'),
                      'post_title' => $this->post('post_title'),
                      'post_content' => $this->post('post_content'),
                      'post_time' => date("Y-m-d H:i:s"),
                      'post_lat' => $this->post('post_lat'),
                      'post_long' => $this->post('post_long'),
                      'post_user' => $this->post('post_user'));
        //
        // fetch photo and add the url to data array
        //
        
        $result = $this->Post_model->insert_post($data);
        
        if($result)
        {
            $this->add_post_links($result);
            
            
            
            $action_array = array();
            
            $action_array[] = array("href" => "/post",
                                    "rel" => "list",
                                    "method" => "get");
            
            $info = array("post" => $result,
                          "links" => $action_array
                          );
            
            $this->response($info, 201);
            
        }
        
        else
        {
            
            $this->response(NULL, 404);
            
        }
        
    }

    private function add_post_links($posts)
    {
        foreach ($posts as $post) {
            
            $post_id = $post->post_id;
            
            $post_links = array();
            $post_links[] = array("href" => "/post/$post_id",
                                  "rel" => "self",
                                  "method" => "GET");
            
            $post->links = $post_links;
            
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */