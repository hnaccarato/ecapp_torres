<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');
defined('BASEPATH') OR exit('No direct script access allowed');

class Accseslog extends CI_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('ion_auth', 'form_validation'));
	}

	public function index()
	{

		$logged_in = $this->ion_auth->logged_in();
		

		if (!$logged_in) {
			redirect('auth/login');
			return true;
		}


		$controller = $this->session->userdata('controller');

		if ($controller == PROPIETARIO) {
			redirect('propietarios');
		}

		if ($controller == INQUILINO) {
			redirect('inquilinos');
		}

		if ($controller == SEGURIDAD) {
			redirect('seguridad');
		}

		if ($controller == ENCARGADO) {
			redirect('encargado');
		}

		if ($this->ion_auth->is_admin()) {
			redirect('admin');
		}

	}

}