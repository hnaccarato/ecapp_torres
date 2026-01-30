<?php defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");


class Auth extends CI_Controller
{
	public $edificio_id;

		
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array('ion_auth', 'form_validation'));
		$this->load->helper(array('url', 'language'));
		$this->load->model('edificios_model');
		$this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
		$this->lang->load('auth');
		$this->layout->setFolder('auth');
		$this->layout->setLayout('admin/layout');
	}

	// redirect if needed, otherwise display the user list
	public function index()
	{

		if (!$this->ion_auth->logged_in()) {
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		} elseif (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			return show_error('You must be an administrator to view this page.');
		} else {
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();
			foreach ($this->data['users'] as $k => $user) {
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}

			$this->layout->view('index', $this->data);
		}
	}


	public function groups()
	{

		if (!$this->ion_auth->logged_in()) {
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		} elseif (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			return show_error('You must be an administrator to view this page.');
		} else {
			//list the groups
			$this->data['groups'] = $this->ion_auth->groups()->result();
			$this->layout->view('groups', $this->data);
		}
	}

	// log the user in
	public function login()
	{

	
	
		$this->data['title'] = $this->lang->line('login_heading');

		//validate form input
		$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
		$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
		

		if ($this->form_validation->run() == true) {
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
			//	die('accedio');
				//if the login is successful
				//redirect them back to the home page	
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('accseslog');
			} else {
				// if the login was un-successful
				// redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/login', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		} else {
			// the user is not logging in so display the login page
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['identity'] = array(
				'name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);

			$this->_render_page('auth/login', $this->data);
		}
	}

	// log the user out
	public function logout()
	{
		$this->data['title'] = "Logout";

		// log the user out
		$logout = $this->ion_auth->logout();

		// redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('auth/login', 'refresh');
	}

	// change password
	public function change_password()
	{
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == false) {
			// display the form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');

			$this->data['old_password'] = array(
				'name' => 'old',
				'id' => 'old',
				'class' => 'form-control',
				'type' => 'password',
			);

			$this->data['new_password'] = array(
				'name' => 'new',
				'id' => 'new',
				'class' => 'form-control',
				'type' => 'password',
				'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
			);

			$this->data['new_password_confirm'] = array(
				'name' => 'new_confirm',
				'id' => 'new_confirm',
				'type' => 'password',
				'class' => 'form-control',
				'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
			);


			$this->data['user_id'] = array(
				'name' => 'user_id',
				'id' => 'user_id',
				'type' => 'hidden',
				'class' => 'form-control',
				'value' => $user->id,
			);

			// render
			$this->_render_page('auth/change_password', $this->data);
		} else {
			$identity = $this->session->userdata('identity');

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change) {
				//if the password was successfully changed
				$this->ion_auth->set_check();
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/change_password', 'refresh');
			}
		}
	}

	// forgot password
	public function forgot_password()
	{
		// setting validation rules by checking whether identity is username or email
		if ($this->config->item('identity', 'ion_auth') != 'email') {
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
		} else {
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
		}


		if ($this->form_validation->run() == false) {
			$this->data['type'] = $this->config->item('identity', 'ion_auth');
			// setup the input
			$this->data['identity'] = array(
				'name' => 'identity',
				'id' => 'identity',
			);

			if ($this->config->item('identity', 'ion_auth') != 'email') {
				$this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
			} else {
				$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
			}

			// set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->_render_page('auth/forgot_password', $this->data);
		} else {
			$identity_column = $this->config->item('identity', 'ion_auth');
			$identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

			if (empty($identity)) {

				if ($this->config->item('identity', 'ion_auth') != 'email') {
					$this->ion_auth->set_error('forgot_password_identity_not_found');
				} else {
					$this->ion_auth->set_error('forgot_password_email_not_found');
				}

				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}

			// run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten) {
				die('asd entro enviar');
				// if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/thnaks", 'refresh'); //we should display a confirmation page here instead of the login page
			} else {
				die('asd error entro enviar');
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	public function thnaks()
	{
		$this->data['title'] = "Te hemos enviado un email para que puedas cambiar la contraseña";
		$this->_render_page('auth/thnaks', $this->data);
	}

	// reset password - final step for forgotten password
	public function reset_password($code = NULL)
	{
		if (!$code) {
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user) {
			// if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false) {
				// display the form

				// set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = array(
					'name' => 'new',
					'id' => 'new',
					'type' => 'password',
					'class' => 'form-control',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				);
				$this->data['new_password_confirm'] = array(
					'name' => 'new_confirm',
					'id' => 'new_confirm',
					'type' => 'password',
					'class' => 'form-control',
					'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
				);
				$this->data['user_id'] = array(
					'name' => 'user_id',
					'id' => 'user_id',
					'type' => 'hidden',
					'class' => 'form-control',
					'value' => $user->id,
				);
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;

				// render
				$this->_render_page('auth/reset_password', $this->data);
			} else {
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {

					// something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					show_error($this->lang->line('error_csrf'));

				} else {
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};

					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change) {
						// if the password was successfully changed
						$this->session->set_flashdata('message', 'Tu contraseña fue generada con éxito! Por favor vuelve a loguearte.');
						redirect("auth/login", 'refresh');
					} else {
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		} else {
			// if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}


	// activate the user
	public function activate($id, $code = false)
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('auth', 'refresh');
		}

		if ($code !== false) {
			$activation = $this->ion_auth->activate($id, $code);
		} else if ($this->ion_auth->is_admin()) {
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation) {
			// redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		} else {
			// redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	// deactivate the user
	public function deactivate($id = NULL)
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('auth', 'refresh');
		}

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			// redirect them to the home page because they must be an administrator to view this
			return show_error('You must be an administrator to view this page.');
		}

		$id = (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

		if ($this->form_validation->run() == FALSE) {
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();
			$this->layout->view('deactivate_user', $this->data);
		} else {
			// do we really want to deactivate?
			if ($this->input->post('confirm') == 'yes') {
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
					show_error($this->lang->line('error_csrf'));
				}

				// do we have the right userlevel?
				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
					$this->ion_auth->deactivate($id);
				}
			}

			// redirect them back to the auth page
			redirect('auth', 'refresh');
		}
	}

	// create a new user
	public function create_user()
	{
		$this->data['title'] = $this->lang->line('create_user_heading');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}

		$tables = $this->config->item('tables', 'ion_auth');
		$identity_column = $this->config->item('identity', 'ion_auth');
		$this->data['identity_column'] = $identity_column;

		// validate form input

		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');

		if ($identity_column !== 'email') {
			$this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
			$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
		} else {
			$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
		}


		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true) {
			$email = strtolower($this->input->post('email'));
			$identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
			$password = $this->input->post('password');
			$edificios = $this->input->post('edificio_id');
			$additional_data = array(

				'edificio_id' => $edificios[0],
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'phone' => $this->input->post('phone'),
			);

			$groups = array($this->input->post('group_id'));
		}

		if ($this->form_validation->run() == true && $id = $this->ion_auth->register($identity, $password, $email, $additional_data, $groups)) {
			// check to see if we are creating the user
			// redirect them back to the admin page
			/*	$unidades = $this->input->post('unidad');
				$this->edificios_model->add_unidades($id,$edificios,$unidades);*/
			$this->edificios_model->add_edificios($id, $edificios);
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			$this->ion_auth->set_password($email);
			redirect("auth", 'refresh');
		} else {
			// display the create user form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('first_name'),
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('last_name'),
			);
			$this->data['identity'] = array(
				'name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'value' => $this->form_validation->set_value('email'),
			);
			$this->data['unidad'] = array(
				'name' => 'unidad',
				'id' => 'unidad',
				'type' => 'text',
				'value' => $this->form_validation->set_value('unidad'),
			);
			$this->data['phone'] = array(
				'name' => 'phone',
				'id' => 'phone',
				'type' => 'text',
				'value' => $this->form_validation->set_value('phone'),
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			);

			$this->data['edificios'] = $this->db->get('edificios');
			$this->data['grupos'] = $this->db->get('groups');
			$this->layout->view('create_user', $this->data);
		}
	}


	// edit a user
	public function edit_user($id)
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('auth', 'refresh');
		}

		$this->data['title'] = $this->lang->line('edit_user_heading');

		if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))) {
			redirect('auth', 'refresh');
		}

		$user = $this->ion_auth->user($id)->row();
		$groups = $this->ion_auth->groups()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();

		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required');
		$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required');
		/*$this->form_validation->set_rules('edificio_id', 
			$this->lang->line('create_user_edificio_label'), 'required');*/

		if (isset($_POST) && !empty($_POST)) {
			// do we have a valid request?
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
				show_error($this->lang->line('error_csrf'));
			}

			// update the password if it was posted
			if ($this->input->post('password')) {
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE) {
				$data = array(
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'company' => $this->input->post('company'),
					'phone' => $this->input->post('phone'),
				);

				// update the password if it was posted
				if ($this->input->post('password')) {
					$data['password'] = $this->input->post('password');
				}



				// Only allow updating groups if user is admin
				if ($this->ion_auth->is_admin()) {
					//Update the groups user belongs to
					$groupData = $this->input->post('groups');

					if (isset($groupData) && !empty($groupData)) {

						$this->ion_auth->remove_from_group('', $id);

						foreach ($groupData as $grp) {
							$this->ion_auth->add_to_group($grp, $id);
						}

					}
				}

				// check to see if we are updating the user
				if ($this->ion_auth->update($user->id, $data)) {

					$edificios = $this->input->post('edificio_id');
					$this->edificios_model->add_edificios($user->id, $edificios);



					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('message', $this->ion_auth->messages());
					if ($this->ion_auth->is_admin()) {
						redirect('auth', 'refresh');
					} else {
						redirect('/', 'refresh');
					}

				} else {
					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('message', $this->ion_auth->errors());
					if ($this->ion_auth->is_admin()) {
						redirect('auth', 'refresh');
					} else {
						redirect('/', 'refresh');
					}

				}

			}
		}

		// display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();

		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		// pass the user to the view
		$this->data['user'] = $user;
		$this->data['groups'] = $groups;
		$this->data['currentGroups'] = $currentGroups;

		$this->data['first_name'] = array(
			'name' => 'first_name',
			'id' => 'first_name',
			'type' => 'text',
			'value' => $this->form_validation->set_value('first_name', $user->first_name),
		);
		$this->data['last_name'] = array(
			'name' => 'last_name',
			'id' => 'last_name',
			'type' => 'text',
			'value' => $this->form_validation->set_value('last_name', $user->last_name),
		);


		$this->data['phone'] = array(
			'name' => 'phone',
			'id' => 'phone',
			'type' => 'text',
			'value' => $this->form_validation->set_value('phone', $user->phone),
		);
		$this->data['password'] = array(
			'name' => 'password',
			'id' => 'password',
			'type' => 'password'
		);

		$this->data['password_confirm'] = array(
			'name' => 'password_confirm',
			'id' => 'password_confirm',
			'type' => 'password'
		);
		$this->data['my_edificios'] = $this->edificios_model->get_array_my_edificios($id);
		$this->data['edificios'] = $this->db->get('edificios');
		$this->layout->view('edit_user', $this->data);
	}

	// create a new group
	public function create_group()
	{
		$this->data['title'] = $this->lang->line('create_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash');

		if ($this->form_validation->run() == TRUE) {
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if ($new_group_id) {
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/groups", 'refresh');
			}
		} else {
			// display the create group form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['group_name'] = array(
				'name' => 'group_name',
				'id' => 'group_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('group_name'),
			);
			$this->data['description'] = array(
				'name' => 'description',
				'id' => 'description',
				'type' => 'text',
				'value' => $this->form_validation->set_value('description'),
			);
			$this->layout->view('create_group', $this->data);
		}
	}

	// edit a group
	public function edit_group($id)
	{
		// bail if no group id given
		if (!$id || empty($id)) {
			redirect('auth', 'refresh');
		}

		$this->data['title'] = $this->lang->line('edit_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('auth', 'refresh');
		}

		$group = $this->ion_auth->group($id)->row();

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash');

		if (isset($_POST) && !empty($_POST)) {
			if ($this->form_validation->run() === TRUE) {
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

				if ($group_update) {
					$this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
				} else {
					$this->session->set_flashdata('message', $this->ion_auth->errors());
				}
				redirect("auth/groups", 'refresh');
			}
		}

		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		// pass the user to the view
		$this->data['group'] = $group;

		$readonly = $this->config->item('admin_group', 'ion_auth') === $group->name ? 'readonly' : '';

		$this->data['group_name'] = array(
			'name' => 'group_name',
			'id' => 'group_name',
			'type' => 'text',
			'value' => $this->form_validation->set_value('group_name', $group->name),
			$readonly => $readonly,
		);
		$this->data['group_description'] = array(
			'name' => 'group_description',
			'id' => 'group_description',
			'type' => 'text',
			'value' => $this->form_validation->set_value('group_description', $group->description),
		);

		$this->layout->view('edit_group', $this->data);
	}


	public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	public function _valid_csrf_nonce()
	{
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		if ($csrfkey && $csrfkey == $this->session->flashdata('csrfvalue')) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function _render_page($view, $data = null, $returnhtml = false)//I think this makes more sense
	{

		$this->viewdata = (empty($data)) ? $this->data : $data;

		$view_html = $this->load->view($view, $this->viewdata, $returnhtml);

		if ($returnhtml)
			return $view_html;//This will return html on 3rd argument being true
	}

	//chenge edificio 
	public function load_edificio()
	{

		if (!$this->ion_auth->logged_in()) {
			redirect('auth', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($_POST) {
			$id = $this->input->post('id');
			$this->session->set_userdata(array('edificio_id' => $id));
		}
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
		$edificio = $this->edificios_model->my_edificios($user->id);
		$data['url'] = $this->session->userdata('url');


		if ($edificio->num_rows() == 1) {
			$id = $edificio->row()->id;
			$this->session->set_userdata(array('edificio_id' => $id));
			redirect($data['url']);
		} else {
			$data['edificios'] = $edificio;
		}

		echo $this->load->view('auth/load_edificio.php', $data, true);

	}

	//chenge edificio 
	public function load_unidades()
	{

		if (!$this->ion_auth->logged_in()) {
			redirect('auth', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($_POST) {
			$id = $this->input->post('id');
			$this->session->set_userdata(array('unidad_id' => $id));
		}

		$controller = $this->session->userdata('controller');

		$this->db->where('users_unidad.grupo_id', $controller);

		$unidades = $this->edificios_model->my_unidad($user->id);
		$data['url'] = $this->session->userdata('url');

		if ($unidades->num_rows() == 1) {
			$id = $unidades->row()->id;
			$this->session->set_userdata(array('unidad_id' => $id));
			redirect($data['url']);
		} else {
			$data['unidades'] = $unidades;
		}

		echo $this->load->view('auth/load_unidades', $data, true);

	}


	public function delete_user($user_id)
	{

		if (!$this->ion_auth->logged_in()) {
			redirect('auth', 'refresh');
		}

		$this->ion_auth->delete_user($user_id);
		redirect('auth', 'refresh');
	}

	/*registrar usiarios por hash*/
	// create a new user
	public function register_propietario($hash = FALSE)
	{

		if (!$edificio = $this->unidades_model->get_edificio_by_hash($hash)) {
			echo "<center><h1>Error</h1></center>";
			echo "<br/>";
			echo "<br/>";
			echo '<center><iframe width="560" height="315" src="https://www.youtube.com/embed/EmQKzhpc4-4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></center>';
			die();
		}


		if (!$unidades = $this->unidades_model->get_unidades_by_hash($hash)) {
			echo "<center><h1>Error</h1></center>";
			echo "<br/>";
			echo "<br/>";
			echo '<center><iframe width="560" height="315" src="https://www.youtube.com/embed/EmQKzhpc4-4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></center>';
			die();
		}

		$this->edificio_id = $edificio->id;

		if (!$edificio && !$unidades)
			redirect('auth/login');

		$this->data['title'] = $this->lang->line('create_user_heading');
		$tables = $this->config->item('tables', 'ion_auth');
		$identity_column = $this->config->item('identity', 'ion_auth');
		$this->data['identity_column'] = $identity_column;
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');
		//$this->form_validation->set_rules('unidad_id[]','Unidad' , 'required');

		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true) {

			$email = strtolower($edificio->email);

			$user_id = false;

			$rs = $this->db->get_where('users', array('email' => $email));

			if ($rs->num_rows() > 0) {
				$user = $rs->row();
				$unidades = $this->input->post('unidad_id');
				if (count($unidades) > 0) {
					$this->unidades_model->add_unidad($user->id, $unidades);
					$edificios = array($edificio_id);
					$this->edificios_model->add_edificios($user->id, $edificios);
					$this->session->set_flashdata('message', $this->ion_auth->messages());
				}
				$user_id = $user->id;
			}

			if ($user_id = $this->check_email($email, $this->edificio_id)) {
				$edificio_id = array($this->edificio_id);
				$this->edificios_model->add_edificios($user_id, $edificio_id);
				$this->users_model->add_propietario($user_id);
				$this->unidades_model->add_unidad($user_id, $unidades, PROPIETARIO);
				redirect('auth');
			}


			$identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
			$password = $this->input->post('password');

			$additional_data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'phone' => $this->input->post('phone'),
			);

			$groups = array(PROPIETARIO);

		}

		if ($this->form_validation->run() == true && $id = $this->ion_auth->register($identity, $password, $email, $additional_data, $groups)) {


			$this->unidades_model->add_unidad($id, $unidades, PROPIETARIO);
			$edificio_id = array($this->edificio_id);
			$this->edificios_model->add_edificios($id, $edificio_id);
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			$this->send_email->new_welcome(array($email), $this->edificio_id);
			//	die("asd");
			redirect('auth');
		} else {
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('first_name'),
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('last_name'),
			);
			$this->data['identity'] = array(
				'name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('identity'),
			);

			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'disabled' => 'disabled',
				'autocomplete' => 'nope',
				'value' => $edificio->email,
			);
			$this->data['unidad'] = array(
				'name' => 'unidad',
				'id' => 'unidad',
				'type' => 'text',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('unidad'),
			);
			$this->data['phone'] = array(
				'name' => 'phone',
				'id' => 'phone_number',
				'type' => 'text',
				'autocomplete' => 'nope',
				'placeholder' => '011-555-5555',
				'value' => '.',
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			);

			$this->data['text'] = 'Bienvenid@, registrarse para poder acceder al sistema de propietarios de ' . $edificio->nombre;
			$this->data['edificio'] = $edificio;
			$this->data['accion'] = 'register_propietario';
			$this->data['unidades'] = $this->unidades_model->disponibles($edificio->id);
			$this->_render_page('auth/register', $this->data);
		}

	}

	public function register_user_old($hash)
	{
		echo "<h1>Momentáneamente fuera de servicio</h1>";
	}

	public function register_user($hash)
	{

		if (!$edificio = $this->edificios_model->get_edificio_by_hash($hash)) {
			echo "<center><h1>Error</h1></center>";
			echo "<br/>";
			echo "<br/>";
			echo '<center><iframe width="560" height="315" src="https://www.youtube.com/embed/EmQKzhpc4-4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></center>';
			die();
		}


		$this->edificio_id = $edificio->id;

		$this->data['title'] = $this->lang->line('create_user_heading');
		$tables = $this->config->item('tables', 'ion_auth');
		$identity_column = $this->config->item('identity', 'ion_auth');
		$this->data['identity_column'] = $identity_column;
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');
		//$this->form_validation->set_rules('unidad_id[]','Unidad' , 'required');

		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true) {


			$email = $this->input->post('email');
			$unidades = array($this->input->post('unidad'));

			$rs = $this->db->get_where('users', array('email' => $email));

			if ($rs->num_rows() > 0) {
				$user = $rs->row();
				$user_id = $user->id;
				$edificio_id = array($this->edificio_id);
				$this->edificios_model->add_edificios($user_id, $edificio_id);
				$this->users_model->add_propietario($user_id);
				$this->unidades_model->add_unidad($user_id, $unidades, PROPIETARIO);
				redirect('auth');
			}


			$identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
			$password = $this->input->post('password');

			$additional_data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'phone' => $this->input->post('phone'),
			);

			$groups = array(PROPIETARIO);

		}

		if ($this->form_validation->run() == true && $id = $this->ion_auth->register($identity, $password, $email, $additional_data, $groups)) {

			$this->unidades_model->add_unidad($id, $unidades, PROPIETARIO);
			$edificio_id = array($this->edificio_id);
			$this->edificios_model->add_edificios($id, $edificio_id);
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			$this->send_email->new_welcome(array($email), $this->edificio_id);
			redirect('auth');
		} else {
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('first_name'),
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('last_name'),
			);
			$this->data['identity'] = array(
				'name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('identity'),
			);

			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'email',
				'autocomplete' => 'nope',
				'value' => '',
			);
			$this->data['unidad'] = array(
				'name' => 'unidad',
				'id' => 'unidad',
				'type' => 'text',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('unidad'),
			);
			$this->data['phone'] = array(
				'name' => 'phone',
				'id' => 'phone_number',
				'type' => 'text',
				'autocomplete' => 'nope',
				'placeholder' => '011-555-5555',
				'value' => '.',
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'autocomplete' => 'nope',
				'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			);

			$this->data['text'] = 'Bienvenid@, registrarse para poder acceder al sistema de propietarios de ' . $edificio->nombre;
			$this->data['edificio'] = $edificio;
			$this->data['accion'] = 'register_propietario';
			$this->data['unidades'] = $this->unidades_model->disponibles($edificio->id);
			$this->_render_page('auth/register', $this->data);
		}
	}


	private function check_email($email, $edificio_id)
	{

		if (!$this->ion_auth->logged_in()) {
			redirect('auth', 'refresh');
		}

		$rs = $this->db->get_where('users', array('email' => $email));

		if ($rs->num_rows() > 0) {
			$user = $rs->row();
			$unidades = $this->input->post('unidad_id');
			if (count($unidades) > 0) {
				$this->unidades_model->add_unidad($user->id, $unidades);
				$edificios = array($edificio_id);
				$this->edificios_model->add_edificios($user->id, $edificios);
				$this->session->set_flashdata('message', $this->ion_auth->messages());
			}
			return $user->id;
		} else {
			return false;
		}

	}


	/**** Mercado Pago **********/

	public function success()
	{

		if ($this->check_ip()) {
			$message = 'Su pago fue procesado con éxito, estará "acreditado" cuando el equipo de administración controle el comprobante de pago realizado.';
			$this->session->set_flashdata('message', $message);
			$this->progree();
		}
	}


	public function pending()
	{
		if ($this->check_ip()) {

			$message = 'Su pago está pendiente de revisión, estará  procesado con éxito cuando Mercado Pago lo apruebe. Luego será "acreditado" cuando el equipo de administración controle el comprobante de pago realizado.';

			$this->session->set_flashdata('message', $message);
			$this->progree();
		}
	}

	public function failure()
	{
		if ($this->check_ip()) {
			$message = 'Su pago no pudo ser procesado, vuelva a realizar la operacion o contactarse con administración.';
			$this->session->set_flashdata('erro_message', $message);
			redirect('propietarios/expensas_pagos_list');
			//$this->progree();

		}
	}

	private function progree()
	{

		if ($_GET) {
			if (isset($_GET['preference_id'])) {
				$preference_id = $_GET['preference_id'];
				if (isset($_GET['merchant_order_id'])) {

					$data['collection_id'] = $_GET['collection_id'];
					$data['mp_merchant_order_id'] = $_GET['merchant_order_id'];
					$data['active'] = TRUE;

					if ($this->pagos_model->update($data, array('mp_preference_id' => $preference_id))) {

						$rs = $this->db->get_where(
							'pagos_users',
							array('mp_preference_id' => $preference_id)
						);

						if ($rs->num_rows() > 0) {
							$pago = $rs->row();
							$this->send_pago($pago->id);
						}

					}

				}
			}
		}

		redirect('propietarios/expensas_pagos_list');
	}

	public function send_pago($pago_id)
	{
		$this->rat->log(uri_string(), 1);
		//return TRUE;
		$data['pago'] = $this->pagos_model->get($pago_id);
		if ($data['pago']) {
			$email = array($data['pago']->email);
			$email_administradores = $this->users_model->get_my_adminstrador($data['pago']->edificio_id);
			$email = array_merge($email, $email_administradores);
			$this->send_email->set_edificio_id($data['pago']->edificio_id);
			$this->send_email->new_pago_comprobante($email, $data);
		}

	}

	private function check_ip()
	{
		return true;
		$allowlist = array(
			'192.168.0.',
			'209.225.49',
			'216.33.197',
			'216.33.196',
			'63.128.82.',
			'63.128.83.',
			'63.128.94.'
		);

		foreach ($allowlist as $key => $ip) {
			if (strncmp($ip, $_SERVER['REMOTE_ADDR'], 10) === 0) {
				return false;
			}
		}
	}

	public function get_tyc($hash)
	{
		$rs = $this->db->get_where('reservas', array('reserva_hash' => $hash));
		if ($rs) {
			$reserva = $rs->row();
			$data['espacio'] = $this->espacios_model->get($reserva->espacio_id);
			$this->db->join('users_unidad', 'users_unidad.user_id = users.id ');
			$data['user'] = $this->db->get_where('users', array('users_unidad.unidad_id' => $reserva->unidad_id))->row();

			$this->db->select('unidades.*,edificios.nombre as edificio');
			$this->db->join('edificios', 'edificios.id = unidades.edificio_id');
			$data['unidad'] = $this->db->get_where(
				'unidades',
				array('unidades.id' => $reserva->unidad_id)
			)->row();
			$filedescargar = $data['espacio']->nombre_espacio . ".docx";
			header("Cache-Control: ");// leave blank to avoid IE errors
			header("Pragma: ");// leave blank to avoid IE errors
			header("Content-type: application/octet-stream");
			header("content-disposition: attachment;filename=" . $data['espacio']->nombre_espacio . ".doc");
			echo $this->load->view('declaracion', $data, TRUE);
		} else {
			return FALSE;
		}

	}

}
