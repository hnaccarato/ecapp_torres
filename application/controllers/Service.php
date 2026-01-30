<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

defined('BASEPATH') OR exit('No direct script access allowed');
class Service extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
	}

	public function view_consulta($hash){
		$rs = $this->db->get_where('consultas_asignadas',array('hash'=>$hash));
		if($rs->num_rows() > 0){
			$consulta_id = $rs->row()->id;
			$data['consulta'] = $this->consultas_model->get($consulta_id);
			$data['respuestas'] = $this->consultas_model->get_respuesta($consulta_id);
			$this->load->view('service/consultas/view',$data);
		}
	}

	public function view_email(){
		$data['title'] = "nuevas expesas cargadas";
		$data['descripcion'] = "Se cargaron las expensas del mes de enero del 2018 re cuerde que las misma se vencen el 29 de enero del 2018";
		$this->load->view('email/new_expensa',$data);
	}

}