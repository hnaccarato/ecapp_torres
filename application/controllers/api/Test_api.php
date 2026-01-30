<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');



require(APPPATH.'/libraries/REST_Controller.php');


class Test_api extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();	
 
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
		    die();
		}

		$this->load->library('Authorization_Token');	

	}
	
	public function tokenGen_post()
	{   
		$token_data['user_id'] = 1001;
		$token_data['fullname'] = 'Hello World'; 
		$token_data['email'] = 'helloworld@gmail.com';

		$tokenData = $this->authorization_token->generateToken($token_data);

		$final = array();
		$final['token'] = $tokenData;
		$final['status'] = 'ok';
		
		$this->response($final); 
	}

	public function verify_post()
	{  
		$headers = $this->input->request_headers(); 

		if (isset($headers['Bearer'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Bearer']);
			$this->response($decodedToken);
		}
		else {
			$this->response(['Authentication failed'], REST_Controller::HTTP_OK);
		}
			
		  
	}



 
}

