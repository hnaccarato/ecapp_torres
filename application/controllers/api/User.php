<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User class.
 * 
 * @extends REST_Controller
 */
    require(APPPATH.'/libraries/REST_Controller.php');
    use Restserver\Libraries\REST_Controller;

class User extends REST_Controller {

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->load->library('Authorization_Token');
		$this->load->model('Users_model');
		$this->load->library('ion_auth');
	  	$this->load->library('session');
	  	
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
		    die();
		}
	}


	/**
	 * login function.
	 * 
	 * @access public
	 * @return void
	 */
	public function login_post() {
		
		// set validation rules

		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		
		if ($this->form_validation->run() == false) {
			
			// validation not ok, send validation errors to the view
            $this->response(['Validation rules violated'], REST_Controller::HTTP_OK);

		} else {
			
			// set variables from the form
			$identity = $this->input->post('username');
			$password = $this->input->post('password');
			$firebase = $this->input->post('firebase');

			$correctCredentials = $this->ion_auth->login($identity, $password);
	
			if($correctCredentials){
				
				$where = "email =  '$identity' ";
				$user = $this->users_model->read($where)->row();
				$whereUsersGroup = ' users_groups.user_id = '.$user->id;
				$groups = $this->users_groups_model->read($whereUsersGroup)->row();
				$unities = $this->edificios_model->my_unidad($user->id)->result();
				$buildings = [];
				
				foreach ($unities as $unity) {
					$buildings[] = $this->edificios_model->my_edificio_by_unidad($unity->id);
				}
				
				// set session user datas
				$this->session->set_userdata('user', $user);
				$this->session->set_userdata('buildings', $buildings);
				$this->session->set_userdata('status', true);
				$this->session->set_userdata('groups', $groups);

				// user login ok
				$token_data['uid'] = $user->id;
				$token_data['unidad'] = $unities;
				$token_data['groups'] = $groups; 
				$tokenData = $this->authorization_token->generateToken($token_data);
				$final = array();
				$final['access_token'] = $tokenData;
				$final['status'] = true;
				$final['message'] = 'Login success!';
				$final['note'] = 'You are now logged in.';
				
				if (!empty( $firebase )) {
					$this->users_model->registerFcmToken($user->id, $firebase);
				}
				
				$this->response($final, REST_Controller::HTTP_OK); 
				
			} else {
				
				// login failed
                $this->response([
                'status' => 400,
                'message' => 'Wrong username or password.'                	
                ], REST_Controller::HTTP_OK);
				
			}
			
		}
		
	}

	public function forgot_password_api(){

		$email = $this->input->post('email');

		$forgotten = $this->ion_auth->forgotten_password($email);

		echo json_encode($forgotten);

	}
	
	/**
	 * logout function.
	 * 
	 * @access public
	 * @return void
	 */
	public function logout_post() {

		if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
			
			foreach ($_SESSION as $key => $value) {
				unset($_SESSION[$key]);
			}
            $this->response(['Logout success!'], REST_Controller::HTTP_OK);
			
		} else {
			
            $this->response(['There was a problem. Please try again.'], REST_Controller::HTTP_OK);	
		}
		
	}
	
}
