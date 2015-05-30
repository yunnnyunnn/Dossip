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
        $this->load->helper(array('form', 'url'));
        $this->load->helper('file');
    }
    
    public function index_get($id='')
    {
        
        
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
                      'post_lat' => $this->post('post_lat'),
                      'post_long' => $this->post('post_long'),
                      'post_user' => $this->post('post_user'));
        
        // has image to upload
        if ($this->post('has_file')) {
            
            
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['file_name'] = md5(uniqid(rand(), true));
            
            $temp_filepath = $config['upload_path'].$config['file_name'];
            
            while (file_exists($temp_filepath)) { // check if the file name exists. if yes, make another attempt
                
                $config['file_name'] = md5(uniqid(rand(), true));
                $temp_filepath = $config['upload_path'].$config['file_name'];

            }
            
            
            $this->load->library('upload', $config);
            
            if ( ! $this->upload->do_upload())
            {
                $error = array('error' => $this->upload->display_errors());
                
                $this->response($error, 400);
                
            }
            
            $temp_file_info = $this->upload->data();
            
            /* what info inside:
             
             array(14) {
             ["file_name"]=>
             string(36) "52334d83f253f6fc644106680e7f0d3d.PNG"
             ["file_type"]=>
             string(9) "image/png"
             ["file_path"]=>
             string(41) "/Applications/MAMP/htdocs/Dossip/uploads/"
             ["full_path"]=>
             string(77) "/Applications/MAMP/htdocs/Dossip/uploads/52334d83f253f6fc644106680e7f0d3d.PNG"
             ["raw_name"]=>
             string(32) "52334d83f253f6fc644106680e7f0d3d"
             ["orig_name"]=>
             string(36) "52334d83f253f6fc644106680e7f0d3d.PNG"
             ["client_name"]=>
             string(12) "IMG_3289.PNG"
             ["file_ext"]=>
             string(4) ".PNG"
             ["file_size"]=>
             float(177.09)
             ["is_image"]=>
             bool(true)
             ["image_width"]=>
             int(640)
             ["image_height"]=>
             int(1136)
             ["image_type"]=>
             string(3) "png"
             ["image_size_str"]=>
             string(25) "width="640" height="1136""
             }

             
             */
            
        
            // upload to server success, start attempting upload to s3
            try {
                
                // Instantiate the S3 client with your AWS credentials
                $client = S3Client::factory(array(
                                                  'credentials' => array(
                                                                         'key'    => AWS_ACCESS_KEY_ID,
                                                                         'secret' => AWS_SECRET_ACCESS_KEY,
                                                                         )
                                                  ));
                $file_name = $temp_file_info['file_name'];
                $bucket_name = 'dossip.dev';
                $file_name_on_s3 = "$post_user_id/$file_name";
                $pathToFile = $temp_file_info['full_path'];
                
                // Upload an object by streaming the contents of a file
                // $pathToFile should be absolute path to a file on disk
                $result = $client->putObject(array(
                                                   'Bucket'     => $bucket_name,
                                                   'Key'        => $file_name_on_s3,
                                                   'SourceFile' => $pathToFile,
                                                   'Metadata'   => array(
                                                                         'Foo' => 'abc',
                                                                         'Baz' => '123'
                                                                         )
                                                   ));
                
                // We can poll the object until it is accessible
                $client->waitUntil('ObjectExists', array(
                                                         'Bucket' => $bucket_name,
                                                         'Key'    => $file_name_on_s3
                                                         ));
                
                
                // delete the temporary file
                unlink($temp_file_info['full_path']);
                
                // Get the URL the object can be downloaded from
                // upload success
                $uploaded_file_url = $result['ObjectURL'];
                
                $data['post_imgurl'] = $file_name_on_s3;
                
                
            }
            catch(Exception $e) {
                
                $error = array('error' => $e->getMessage());
                $this->response($error, 400);
                
            }
            
            
        }
        
        // we record the time after the file's been uploaded
        $data['post_time'] = date("Y-m-d H:i:s");
        
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