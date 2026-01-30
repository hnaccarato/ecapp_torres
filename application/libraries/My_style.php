<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class my_style
{
	protected $obj;
	private $color;
	private $color2;
	private $logo_login;
	private $logo;
	private $name;
	private $css;
	private $color_icono;
	private $ruta_file = '/upload/empresa/logo/';

	public function __construct()
	{
		$this->obj =& get_instance();
		$this->color = '#7438ff;';
		$this->color2 = '#33AEF2;';
		$this->color_icono = '#fff;';
		$this->logo_login = base_url('access/images/Logo.jpg');
		$this->logo = base_url('access/images/logo.png');
	}

	function get_css()
	{
		$data['color1'] = $this->color;
		$data['color_icono'] = $this->color_icono;
		return $this->obj->load->view('my_style', $data, true);
	}

	function get_logo()
	{
		return $this->logo;
	}

	function get_name()
	{
		return $this->name;
	}

	function get_color()
	{
		return $this->color;
	}

	function get_color_icono()
	{
		return $this->color_icono;
	}

	function get_logo_login()
	{
		return $this->logo_login;
	}
	function load_company($edificio_id)
	{

		$company = $this->obj->empresas_model->my_empresa($edificio_id);

		if ($company) {
			$this->name = $company['nombre'];
			$this->logo = base_url($this->ruta_file . $company['logo']);
			$this->logo_login = base_url($this->ruta_file . $company['logo_login']);
			$this->color = trim($company['color']);
			$this->color_icono = trim($company['color_icono']);
		}

	}

	function load_company_by_host($url)
	{

		$company = $this->obj->empresas_model->my_url($url);
		//echo $this->obj->db->last_query();
		if ($company) {
			$this->name = $company['nombre'];
			$this->logo = base_url($this->ruta_file . $company['logo']);
			$this->logo_login = base_url($this->ruta_file . $company['logo_login']);
			$this->color = trim($company['color']);
			$this->color_icono = trim($company['color_icono']);
			define('EMAIL_FROMT', $company['email_front']);
		}

	}

	function check_company_host($url, $edificio_id)
	{

		$company = $this->obj->empresas_model->my_url($url);
		$this->obj->load->library('ion_auth');

		if (!$company) {

			$this->obj->ion_auth->logout();
			redirect('administrador');

		} else {

			$rs = $this->obj->db->get_where(
				'edificios',
				array('id' => $edificio_id)
			);
			$edificio = $rs->row();

			if ($edificio)
				$rs_company = $this->obj->db->get_where(
					'empresas',
					array('id' => $edificio->empresa_id)
				);
			else
				$this->obj->ion_auth->logout();

			$company = $rs_company->row();

			if ($company->url == $url)
				return true;
			else {
				$this->obj->ion_auth->logout();
				header('Location: ' . $company->url);
			}

		}

	}

}