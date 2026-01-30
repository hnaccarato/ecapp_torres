<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

defined('BASEPATH') OR exit('No direct script access allowed');

class Encargado extends CI_Controller {

	public $user;
	public $edificio_id;
	private $espacio_id;

	public function __construct(){
		parent::__construct();

		$this->layout->setFolder('encargado');
		$this->layout->setLayout('encargado/layout');
		$this->load->model('Invitados_model');
		$this->load->model('Inquilino_model');
		$this->load->model('Baned_model');
		$this->lang->load('auth');

		$this->user = $this->ion_auth->user()->row();

		if (!$this->ion_auth->in_group(ENCARGADO)){
			redirect('accseslog');
		}

		$this->session->set_userdata(array('controller'=>ENCARGADO));


		$edificio = $this->session->userdata('edificio_id');
		$espacio_id = $this->session->userdata('espacio_id');

		if(!empty($espacio_id)){
			$this->espacio_id = $espacio_id;
		}

		if(!empty($edificio)){
			$this->edificio_id = $edificio;
			$this->layout->setEdificio($edificio);
		}else{
			redirect('auth/load_edificio');
		}

		$this->my_style->load_company($this->edificio_id);
		$this->send_email->set_edificio_id($this->edificio_id);
		$this->espacios_model->set_rol(encargado);
		//comentar en caso de falla
		$this->my_style->check_company_host(APP_URL,$this->edificio_id );
	}

	public function index(){
		$this->rat->log(uri_string(),1);
		$this->edificios_update();
	}

	/*inicio de Edificos*/

	public function edificios_update(){
		
		$this->rat->log(uri_string(),1);
		$this->load->library('googlemaps');

		$where['edificios.id'] = $this->edificio_id;
		if(!empty($_POST)){

			if(isset($_POST['nombre'])){
				$data['nombre'] = $this->input->post('nombre',true);
			}

			if(isset($_POST['direccion'])){
				$data['direccion'] = $this->input->post('direccion',true);
			}

			if(isset($_POST['telefono'])){
				$data['telefono'] = $this->input->post('telefono',true);
			}

			if(isset($_POST['description'])){
				$data['description'] = $this->input->post('description',true);
			}			
			if(isset($_POST['cuit'])){
				$data['cuit'] = $this->input->post('cuit',true);
			}

			if(isset($_POST['position'])){

				$position = $this->input->post('position',true);
				$position = str_replace('(','',$position);
				$position = str_replace(')','',$position);
				$data['position'] = $position;

			}

			if(!empty($_FILES['imagen']['name'])){

				$config['upload_path'] = BASEPATH.'../upload/edificios/';
				$config['allowed_types'] = 'xlsx|docx|pdf|gif|jpg|png|jpeg';
				$file = $_FILES['imagen']['name'];
				$file_data = pathinfo($file);

				if(!isset($file_data['extension'])){
					echo "Archivo invalido";
					return false;
				}

				$name_file = $this->toAscii($file_data['filename']);
				$filename =  $name_file.'.'.$file_data['extension'];
				$config['file_name'] = $filename;

				if($this->upload($config,'imagen')){ 
					$data['imagen'] = $filename;
				}

			}

			if(isset($_POST['cod_color'])){
				$data['cod_color'] = $this->input->post('cod_color',true);
			}

			if(!$this->edificios_model->update($data, $where)){
				redirect('encargado/edificios_error');
			}

			redirect('encargado');
		}

		$data['values'] = $this->edificios_model->read($where)->row();
		/*Google Maps*/

		$config['center'] = '-34.5767733,-58.4588453';
		$config['zoom'] = 15;
		$config['places'] = TRUE;

		if(!empty($data['values']->position)){
			$config['center'] = $data['values']->position;
			$marker['position'] = $data['values']->position;
			$marker['draggable'] = true;
			$marker['ondragend'] = 'set_position(event.latLng);';
			$this->googlemaps->add_marker($marker);
		}

		$config['placesAutocompleteInputID'] = 'tex_map';
		$config['placesAutocompleteBoundsMap'] = TRUE; // set results biased towards the maps viewport
		$config['placesAutocompleteOnChange'] = 'maker = createMarker_map({ map: map, 
			position:this.getPlace().geometry.location,
			draggable:true
			});
			map.setCenter(this.getPlace().geometry.location);
			map.setZoom(15);
			set_position(this.getPlace().geometry.location);
			google.maps.event.addListener(maker, "dragend", function(event){
				set_position(event.latLng);
			});';

		$this->googlemaps->initialize($config);
		$data['map'] = $this->googlemaps->create_map();
		/*Fin google Maps*/
		
		$date_ini = date("Y-m-d");
		//sumo 1 año
		$date_fin = date("Y-m-d",strtotime($date_ini."+ 1 year"));

		$this->event_model->set_building($this->edificio_id);
		$this->event_model->set_user_id($this->user->id);
		$data['jason_date'] = $this->event_model->get_event($date_ini,$date_fin);
		$this->layout->view('edificios/update', $data);
	}

/*Fin de edificios*/

/*********************************************Expensas*******************************************/

public function expensas_list($recibo_id = false){
	$this->rat->log(uri_string(),1);
	$data['tipo_gastos'] = $this->db->get('tipo_gastos');
	$data['edificios'] = $this->edificio_id;
	$data['unidades'] = $this->unidades_model->actives($this->edificio_id);
	$this->layout->view('recibos/list',$data);
}

public function expensas_read(){
	$this->rat->log(uri_string(),1);
	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'recibos.id';
	$search = false;

	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
		$recibo_id = $this->input->post('recibo_id',true);
		$edificio_id = $this->input->post('edificio_id',true);
	}


	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);		

	if(intval($edificio_id) > 0){
		$this->db->where('recibos.edificio_id',$edificio_id);	
	}

	if($search != ''){

		$searchables = array(
			"edificios.nombre",
			"recibos.titulo",
			"recibos.descripcion",
			"recibos.file",
			"recibos.fecha");

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;

			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$group = true;

					$this->db->or_like($searchable, $search);
				}
			}
			$this->db->group_end();
		}
	}
	$this->db->where('edificios.id',$this->edificio_id);
	$data['registers']  = $this->recibos_model->read();
//echo $this->db->last_query();
	$this->load->view('encargado/recibos/read', $data);
}

public function expensas_excel(){
	$this->rat->log(uri_string(),1);
	$excelables = array("id",
		"fecha",
		"titulo",
		"descripcion");

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_expensas_'.date('Y-m-d').'.xls';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();	
		$order_type = 'DESC';
		$order_by = 'recibos.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
			$this->db->order_by($order_by, $order_type);
		}

		if($search != ''){
			$searchables = array(
				"edificios.nombre",
				"recibos.titulo",
				"recibos.descripcion",
				"recibos.tipo_recibos",
				"recibos.file",
				"recibos.fecha");

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}						
				}

				$this->db->group_end();
			}
		}
		$this->db->where('edificios.id',$this->edificio_id);
		$rows =$this->recibos_model->read()->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function expensas_create(){
	$this->rat->log(uri_string(),1);
	
	if(!empty($_POST)){

		$data['edificio_id'] = $this->edificio_id;
		$data['usuarios_id'] = $this->user->id;

		if(isset($_POST['titulo'])){
			$data['titulo'] = $this->input->post('titulo',true);
		}

		if(isset($_POST['descripcion'])){
			$data['descripcion'] = $this->input->post('descripcion',true);
		}

		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $data['edificio_id']."_".date("Y_m_d_H_i_s").
			'_expensa_'.'.'.$file_data['extension'];

			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;
			}
		}

		if(!empty($_FILES['prorrateo']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['prorrateo']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $data['edificio_id']."_".date("Y_m_d_H_i_s").'_prorrateo_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'prorrateo')){ 
				$data['prorrateo'] = $filename;
			}
		}

		if(!empty($_FILES['lsueldo']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['lsueldo']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $data['edificio_id']."_".date("Y_m_d_H_i_s").'_Libro_sueldos_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'lsueldo')){ 
				$data['lsueldo'] = $filename;
			}
		}

		if(!empty($_FILES['ebancarios']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['ebancarios']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $data['edificio_id']."_".date("Y_m_d_H_i_s").'_extractos_bancarios_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'ebancarios')){ 
				$data['ebancarios'] = $filename;
			}
		}

		if(!empty($_FILES['anexo1']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['anexo1']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $data['edificio_id']."_".date("Y_m_d_H_i_s").'_anexo1_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'anexo1')){ 
				$data['anexo1'] = $filename;
			}
		}			

		if(!empty($_FILES['anexo2']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc|jpeg';
			$file = $_FILES['anexo2']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $data['edificio_id']."_".date("Y_m_d_H_i_s").'_anexo2_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'anexo2')){ 
				$data['anexo2'] = $filename;
			}
		}			

		if(!empty($_FILES['gparticulares']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['gparticulares']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $data['edificio_id']."_".date("Y_m_d_H_i_s").'_gastos_particulares_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'gparticulares')){ 
				$data['gparticulares'] = $filename;
			}else{
				die();	
			}
		}

		if(isset($_POST['fecha'])){
			$data['fecha'] = $this->input->post('fecha',true);
		}			

		if(isset($_POST['estado_id'])){
			$data['estado_id'] = $this->input->post('estado_id',true);
		}
			
		$data['pendiente_pago'] = TRUE;
		
		if(!$this->recibos_model->create($data)){
			redirect('encargado/recibos_error');
		}

		$recibo_id =  $this->db->insert_id();

		if($this->input->post('estado_id',true) == ENVIADO){
			$this->notificar($recibo_id);
		}


		redirect('encargado/expensas_list/');
	}

	$data['edificios'] = $this->db->get('edificios',TRUE);
	$data['usuarios'] = $this->db->get('users',TRUE);
	$this->layout->view('recibos/create',$data);
}

public function expensas_update($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['recibos.id'] = $primary_key_value;
	$where['edificios.id'] = $this->edificio_id;
	$rs = $this->recibos_model->read($where);
	if(!$rs->num_rows()){
		redirect('encargado/expensas_list');
	}


	$data['values'] = $rs->row();
	if(!empty($_POST)){

		$data['edificio_id'] = $this->edificio_id;
		$data['usuarios_id'] = $this->user->id;		

		if(isset($_POST['titulo'])){
			$update['titulo'] = $this->input->post('titulo',true);
		}

		if(isset($_POST['descripcion'])){
			$update['descripcion'] = $this->input->post('descripcion',true);
		}

		$update['tipo_recibos'] = $data['values']->tipo_recibos;

		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $this->edificio_id."_".date("Y_m_d_H_i_s").'_expensas_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$update['file'] = $filename;
			}

		}			

		if(!empty($_FILES['prorrateo']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['prorrateo']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $this->edificio_id."_".date("Y_m_d_H_i_s").'_prorrateo_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'prorrateo')){ 
				$update['prorrateo'] = $filename;
			}
		}

		if(!empty($_FILES['lsueldo']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['lsueldo']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $this->edificio_id."_".date("Y_m_d_H_i_s").'_libro_sueldo_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'lsueldo')){ 
				$update['lsueldo'] = $filename;
			}
		}

		if(!empty($_FILES['ebancarios']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['ebancarios']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $this->edificio_id."_".date("Y_m_d_H_i_s").'_etractos_bancarios_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'ebancarios')){ 
				$update['ebancarios'] = $filename;
			}
		}

		if(!empty($_FILES['anexo1']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['anexo1']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $this->edificio_id."_".date("Y_m_d_H_i_s").'_anexo1_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'anexo1')){ 
				$update['anexo1'] = $filename;
			}
		}			

		if(!empty($_FILES['anexo2']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['anexo2']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $this->edificio_id."_".date("Y_m_d_H_i_s").'_anexo2_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'anexo2')){ 
				$update['anexo2'] = $filename;
			}
		}			

		if(!empty($_FILES['gparticulares']['name'])){

			$config['upload_path'] = UPLOAD_EXPENSAS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc';
			$file = $_FILES['gparticulares']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$filename =  $this->edificio_id."_".date("Y_m_d_H_i_s").'_gastos_particulares_'.
			'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'gparticulares')){ 
				$update['gparticulares'] = $filename;
			}
		}

		if(isset($_POST['fecha'])){
			$update['fecha'] = $this->input->post('fecha',true);
		}

		if($_POST['estado_id'] > 0){
			$update['estado_id'] = $this->input->post('estado_id',true);
		}

		$update['pendiente_pago'] = TRUE;
		if(!$this->recibos_model->update($update, array('id'=> $primary_key_value))){
			redirect('encargado/recibos_error');
		}

		if($this->input->post('estado_id',TRUE) == ENVIADO){
			$this->notificar($primary_key_value);
		}

		redirect('encargado/expensas_list/'.$data['values']->tipo_recibos);
	}

	$data['edificios'] = $this->db->get('edificios');
	$data['usuarios'] = $this->db->get('users');
	$this->layout->view('recibos/update', $data);
}

public function add_gastos(){
	$this->rat->log(uri_string(),1);

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

		$insert['recibo_id'] =$this->input->post('recibo_id',true);

		$insert['tipo_gasto_id'] = $this->input->post('tipo_gasto_id',true);

		if(!empty($_FILES['comprobante']['name'])){

			$this->load->library('upload');
			$config['upload_path'] = UPLOAD_GASTOS;
			$config['allowed_types'] = 'xlsx|csv|docx|xls|pdf|txt|doc|gif|jpg|png|jpeg';
			$files = $_FILES;
			$cpt = count($_FILES['comprobante']['name']);

			for($i=0; $i<$cpt; $i++)
			{           
				$file = $files['comprobante']['name'][$i];
				$file_data = pathinfo($file);
				$name_file = $this->toAscii($file_data['filename']);
				$filename =  date("m_d_y").'-'.
				$insert['recibo_id'].
				$name_file.'.'.
				$file_data['extension'];

				$config['file_name'] = $filename;
				$_FILES['comprobante']['name']= $files['comprobante']['name'][$i];
				$_FILES['comprobante']['type']= $files['comprobante']['type'][$i];
				$_FILES['comprobante']['tmp_name']= $files['comprobante']['tmp_name'][$i];
				$_FILES['comprobante']['error']= $files['comprobante']['error'][$i];
				$_FILES['comprobante']['size']= $files['comprobante']['size'][$i];

				$this->upload->initialize($config);
				if($this->upload->do_upload('comprobante')){
					$titulo = $this->input->post('title',true);
					if(count($titulo) > 0 ){
						$insert['titulo'] = $file_data['filename'];
					}else{
						$insert['titulo'] = $titulo[$i];
					}
					$insert['comprobante'] = $filename;
					$this->db->insert('gastos',$insert);
				}else{
					$error[$i]['upload_error'] = "No se pudo subir el archivo ".$files['comprobante']['name'][$i]."</br>".$this->upload->display_errors("<span class='error'>", "</span>");
				}


			}


		}
	}
}

public function expensas_view($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['recibos.id'] = $primary_key_value;
	$this->db->where('recibos.edificio_id',$this->edificio_id);

	$rs = $this->recibos_model->read($where);
	if(!$rs->num_rows()){
		redirect('admionistrador/expensas_list');
	}

	$data['values'] = $rs->row();
	$this->db->select('tipo_gastos.name as tipo, gastos.*');
	$this->db->join('tipo_gastos','gastos.tipo_gasto_id = tipo_gastos.id');

	$this->db->order_by('gastos.tipo_gasto_id');
	$this->db->order_by('gastos.titulo','asc');
	$data['gastos'] = $this->db->get_where('gastos',array('gastos.recibo_id'=>$primary_key_value));
	$this->layout->view('recibos/view', $data);
}

public function recibos_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['id'] = $primary_key_value;
	$where['recibos.edificio_id'] = $this->edificio_id;
	if(!$this->recibos_model->delete($where)){
		redirect('encargado/recibos_error');
	}
	redirect('encargado/expensas_list');

}

public function expensas_delete_gasto(){
	$this->rat->log(uri_string(),1);

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$id = $this->input->post('comprobante_id',true);
		$this->db->delete('gastos',array('id'=>$id));
	}
}

public function notificar($recibo_id){
	$this->rat->log(uri_string(),1);

	if(isset($_POST['unidad_id'])){
		$unidades = $_POST['unidad_id'];
		$email =  $this->db->where_in('unidades.id',$unidades);
	}
	
	$email = $this->users_model->get_array($this->edificio_id);

	$data['expensa'] = $this->recibos_model->get($recibo_id)->row();
	
	$fcm_token = $this->users_model->get_all_push_movile($this->edificio_id);
	if(count($fcm_token) > 0){
		$this->notification->title = "Nueva expensa cargada";
		$this->notification->body = $data['expensa']->titulo;
		$this->notification->send_push_movile($fcm_token);
	}
	$this->send_email->new_expensas($email,$data);
}


/********************************************Fin de Expensas****************************************/
/******************************************** Pagos  ************************************************/

public function expensas_pagos_list(){
	$this->rat->log(uri_string(),1);

	$this->db->where_in('estados.id',array(9,11,10,1,3));
	$data['estados'] = $this->db->get('estados')->result();
	$this->layout->view('pagos/list',$data);
}

public function pagos_create(){
	$this->rat->log(uri_string(),1);

	if(!empty($_POST)){

		$expensas = $this->input->post('expensas',true);

		if(!empty($_FILES['comprobante']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/comprobante/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['comprobante']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'comprobante')){ 
				$data['file'] = $filename;
			}else{
				die("error");
			}

		}


		$data['descripcion'] = $this->input->post('detalle',true);	
		$data['unidad_id'] = $this->input->post('unidad_id',true);
		$data['fecha'] = $this->input->post('fecha_envio',true);

		$user = $this->unidades_model->get_user($data['unidad_id']);

		foreach ($expensas as $key => $value) {
			$data['recibo_id'] = $value;
			$data['user_id'] = $user->user_id;
			$data['estado_id'] = APROBADO;
			$pago_id = $this->pagos_model->create($data);
			$this->informe_de_pago($pago_id);
		}

		redirect('encargado/expensas_pagos_list');
	}

	$data['unidades'] = $this->unidades_model->read(array('edificio_id'=>$this->edificio_id));
	$this->layout->view('pagos/create',$data);
}

public function pagos_read(){
	$this->rat->log(uri_string(),1);

	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'pagos_users.id';
	$search = false;

	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
		$estado_id = $this->input->post('estado_id',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);

	if($search != ''){
		$searchables=array("edificios.nombre",
			"recibos.titulo",
			"recibos.fecha",
			"users.unidad",
			"users.first_name",
			"users.last_name",
			"pagos_users.fecha",
			"pagos_users.estado_id",
			"pagos_users.file");

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();
		}
	}

	if(intval($estado_id) > 0){
//		$this->db->where('pagos_users.active',TRUE);	
		$this->db->where('pagos_users.estado_id',$estado_id);	
	}

	$this->db->where('edificios.id',$this->edificio_id);
	$data['registers']  = $this->pagos_model->read();
	$this->load->view('encargado/pagos/read', $data);
}


public function pagos_excel(){
	$this->rat->log(uri_string(),1);

	$excelables=array("edificio",
		"titulo",
		"expensas",
		"unidad",
		"nombre",
		"apellido",
		"fecha_pago",
		"estado");	

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();

		$order_type = 'DESC';
		$order_by = 'pagos_users.id';
		$search = false;

		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = $this->input->get('search',true);
			$estado_id = $this->input->get('estado_id');
		}

		$this->db->order_by($order_by, $order_type);
		if($search != ''){
			$searchables=array("edificios.nombre",
				"recibos.titulo",
				"recibos.fecha",
				"users.unidad",
				"users.first_name",
				"users.last_name",
				"pagos_users.fecha",
				"pagos_users.estado_id");

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}

				$this->db->group_end();	
			}
		}

		$this->db->where('edificios.id',$this->edificio_id);
		$data['registers']  = $this->pagos_model->read();
		$rows =$this->pagos_model->read()->result_array();

		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function pagos_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['pagos_users.id'] = $primary_key_value;
	$where['edificios.id'] = $this->edificio_id;
	if(!$this->pagos_model->delete($where)){
		redirect('encargado/pagos_error');
	}
	redirect('encargado/expensas_pagos_list');
}

public function view_pagos(){
	$this->rat->log(uri_string(),1);

	$id = $this->input->post('id',true);
	$data['expensa'] = $this->pagos_model->get($id);
	$this->db->where_in('estados.id',array(9,11,10,1,3));
	$data['estados'] = $this->db->get('estados')->result();
	echo $this->load->view('encargado/pagos/view',$data,TRUE);
}

public function set_pago(){
	
	$this->rat->log(uri_string(),1);
	
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
		strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])){ 

		$pago_id = $this->input->post('pago_id',true);
		$update['estado_id'] = $this->input->post('estado_id',true);
		$descripcion = $this->input->post('detalle',true);

	if(!empty($descripcion)){
		$update['descripcion'] = $this->input->post('detalle',true);
	}

	$this->pagos_model->update($update,array('id'=>$pago_id));

	if($update['estado_id'] == ACREDITADO){
		$this->send_recibo($pago_id);
	}else{
		$this->send_notifica_pago($pago_id);
	}
}

}

public function get_pagos_pendientes(){
	$this->rat->log(uri_string(),1);

	$unidad_id = $this->input->post('unidad_id',true);
	$rs = $this->pagos_model->pending($unidad_id,$this->edificio_id);
	echo json_encode($rs->result());
}


public function informe_de_pago($pago_id){
	$this->rat->log(uri_string(),1);

	if($data['pago'] = $this->pagos_model->get($pago_id)){
		$this->send_email->new_pago($data);


		$fcm_token = $this->users_model->get_all_push_movile($this->edificio_id);
		if(count($fcm_token) > 0){
			$this->notification->title = "Informe de Pago";
			$this->notification->send_push_movile($fcm_token);
		}

	}
}

/******************************************** Fin de Pagos*******************************************/
/******************************************** unidades   *******************************************/
public function unidad_create(){
	$this->rat->log(uri_string(),1);


	if(!empty($_POST)){



		$data['edificio_id'] = $this->edificio_id;

		if(!empty($_FILES['base_unidades']['name'])){


			$config['upload_path'] = BASEPATH.'../upload/base/';
			$config['allowed_types'] = 'xlsx|xls|csv';
			$file = $_FILES['base_unidades']['name'];
			$file_data = pathinfo($file);

			if(!isset($file_data['extension'])){
				echo "Archivo invalido";
				return false;
			}

			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'base_unidades')){ 


				$filename;
				$hash_registro = md5(uniqid().$filename);

				if (($fichero = fopen($config['upload_path'].$filename, "r")) !== FALSE) {
					$boolean = (count(fgetcsv($fichero,0,',','"')) > 1 ? ',' : ';');
					fseek($fichero,0);

					while (($datos = fgetcsv($fichero,null,$boolean)) !== FALSE) {
							

						
						if(trim($datos[0]) == 'Unidad funcional' && trim($datos[1]) == 'Departamento'){
							break;
						}

						$data['name'] = $datos[0];
						$data['departamento'] = $datos[1];
						$data['porc'] = $datos[2];
						$data['sector'] = $datos[3];

						$unidad_id = $this->unidades_model->create($data);
						
						if($unidad_id){
							$email = trim($datos[4]);
							if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
								$parents=array('unidad_id'=>$unidad_id,
								'email'=>$email,
								'edificio_id'=>$this->edificio_id,
								'hash'=>md5($this->edificio_id.$email),
								'hash_registro'=>$hash_registro);

								$this->db->insert('registeruser',$parents);
							}else{
								$message = "Algunos elementos no pudieron ser cargados";
								$this->session->set_flashdata('message', $message);
							}
						}
					}
				}

				$this->send_registros($hash_registro);
			}
		
		}else{

			if(isset($_POST['nombre'])){
				$data['name'] = $this->input->post('nombre',true);
			}				

			if(isset($_POST['departamento'])){
				$data['departamento'] = $this->input->post('departamento',true);
			}

			if(isset($_POST['porc'])){
				$data['porc'] = $this->input->post('porc',true);
			}

			if(!$this->unidades_model->create($data)){
				redirect('encargado/edificios_error');
			}

		}


		redirect('encargado/unidad_list');
	}

	$this->layout->view('unidades/create');
}


public function unidad_read(){
	$this->rat->log(uri_string(),1);
	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'unidades.id';
	$search = false;
	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	if($search != ''){


		$searchables = array("unidades.name");

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();
		}
	}

	$this->db->where('unidades.edificio_id',$this->edificio_id);
	$data['registers']  = $this->unidades_model->read();
	$this->load->view('encargado/unidades/read', $data);
}

public function unidad_list(){
	$this->rat->log(uri_string(),1);

	$this->layout->view('unidades/list');
}

public function unidad_excel(){
	$this->rat->log(uri_string(),1);

	$excelables =  array("name","departamento");

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();
		$order_type = 'DESC';
		$order_by = 'id';
		$search = '';

		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}

		$this->db->order_by($order_by, $order_type);
		if($search != ''){

			$searchables = array("name","departamento");
			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}

				$this->db->group_end();
			}
		}
		$this->db->where('unidades.edificio_id',$this->edificio_id);
		$rows = $this->unidades_model->read()->result_array();

		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function unidad_update($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['unidades.id'] = $primary_key_value;
	$where['unidades.edificio_id'] = $this->edificio_id;
	if(!empty($_POST)){

		if(isset($_POST['nombre'])){
			$data['name'] = $this->input->post('nombre',true);
		}

		if(isset($_POST['departamento'])){
			$data['departamento'] = $this->input->post('departamento',true);
		}			

		if(isset($_POST['porc'])){
			$data['porc'] = $this->input->post('porc',true);
		}

		if(!$this->unidades_model->update($data, $where)){
			redirect('encargado/edificios_error');
		}

		redirect('encargado/unidad_list');
	}

	$rs = $this->unidades_model->read($where);
	if(!$rs->num_rows()){
		redirect('encargado/unidad_list');
	}

	$data['values'] = $rs->row();
	$this->layout->view('unidades/update', $data);
}

public function unidad_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['id'] = $primary_key_value;
	$where['edificio_id'] = $this->edificio_id;

	if(!$this->unidades_model->delete($where)){
		redirect('encargado/edificios_error');
	}
	redirect('encargado/unidad_list');

}

/**************************************** fin de unidades ****************************************/
/**************************************** Propietarios ****************************************/
public function propietarios_create(){
	$this->rat->log(uri_string(),1);

	$this->data['title'] = $this->lang->line('create_user_heading');

	$tables = $this->config->item('tables','ion_auth');
	$identity_column = $this->config->item('identity','ion_auth');
	$this->data['identity_column'] = $identity_column;

	$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
	$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');

	$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');

	$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
	$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
	$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
	$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

	if ($this->form_validation->run() == true)
	{

		$email    = strtolower($this->input->post('email',true));

		if($user_id = $this->check_email($email)){

			$this->users_model->add_propietario($user_id);
			$unidades = $this->input->post('unidad_id',true);
			$this->unidades_model->add_unidad($user_id,$unidades,PROPIETARIO);
			$edificios = array($this->edificio_id);
			$this->edificios_model->add_edificios($user_id,$edificios);
			$this->ion_auth->set_password($email);
			//$this->send_email->new_welcome(array($email),$this->edificio_id);
			redirect('encargado/propietarios_list');
			return TRUE;
		}


		$identity = ($identity_column==='email') ? $email : $this->input->post('identity',true);
		$password = $this->input->post('password',true);

		$additional_data = array(
			'first_name' => $this->input->post('first_name',true),
			'last_name'  => $this->input->post('last_name',true),
			'phone'      => $this->input->post('phone',true),
		);

		$groups = array(PROPIETARIO);
		if ($this->form_validation->run() == true && $id = $this->ion_auth->register($identity, $password, $email, $additional_data,$groups)){
			$unidades = $this->input->post('unidad_id',true);
			$this->unidades_model->add_unidad($id,$unidades,PROPIETARIO);
			$edificios = array($this->edificio_id);
			$this->edificios_model->add_edificios($id,$edificios);
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			$this->ion_auth->set_password($email);
			//$this->send_email->new_welcome(array($email),$this->edificio_id);
			redirect('encargado/propietarios_list');
		}

	}
	else
	{
	// display the create user form
	// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		$this->data['first_name'] = array(
			'name'  => 'first_name',
			'id'    => 'first_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('first_name'),
		);
		$this->data['last_name'] = array(
			'name'  => 'last_name',
			'id'    => 'last_name',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('last_name'),
		);
		$this->data['identity'] = array(
			'name'  => 'identity',
			'id'    => 'identity',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('identity'),
		);
		$this->data['email'] = array(
			'name'  => 'email',
			'id'    => 'email',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('email'),
		);
		$this->data['unidad'] = array(
			'name'  => 'unidad',
			'id'    => 'unidad',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('unidad'),
		);
		$this->data['phone'] = array(
			'name'  => 'phone',
			'id'    => 'phone',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('phone'),
		);
		$this->data['password'] = array(
			'name'  => 'password',
			'id'    => 'password',
			'type'  => 'password',
			'value' => $this->form_validation->set_value('password'),
		);
		$this->data['password_confirm'] = array(
			'name'  => 'password_confirm',
			'id'    => 'password_confirm',
			'type'  => 'password',
			'value' => $this->form_validation->set_value('password_confirm'),
		);

		$this->data['unidades'] = $this->unidades_model->disponibles($this->edificio_id);
		$this->layout->view('propietarios/create', $this->data);
	}
}


public function propietarios_read(){
	$this->rat->log(uri_string(),1);

	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'users.id';
	$search = false;

	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
	}


	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	$searchables = array('users.first_name',
		'users.last_name',
		'users.email',
		'unidades.name',
		'unidades.departamento',
	);
	if($search != ''){

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;

			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();
		}
	}

	$this->db->where('edificios.id',$this->edificio_id);
	$this->db->where('users_groups.group_id',PROPIETARIO);
	$data['registers']  = $this->users_model->my_owners($this->edificio_id);
	//	echo $this->db->last_query();
	$this->load->view('encargado/propietarios/read', $data);

}

public function propietarios_list(){
	$this->rat->log(uri_string(),1);
	$this->layout->view('propietarios/list');
}

public function propietarios_excel(){
	$this->rat->log(uri_string(),1);

	$excelables = array('id',
		'first_name',
		'last_name',
		'email',
		'phone',
		'edificio',
		'unidades',
		'departamentos',
	);
	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();


		$order_type = 'DESC';
		$order_by = 'users.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}
		$this->db->order_by($order_by, $order_type);
		if($search != ''){
			$searchables = array('users.id',
				'users.first_name',
				'users.last_name',
				'users.email',
				'users.phone',
				'edificios.nombre',
				'unidades.name',
				'unidades.departamento',
			);
			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();
				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}
				$this->db->group_end();
			}
		}

		$this->db->where('edificios.id',$this->edificio_id);
		$this->db->where('users_groups.group_id',PROPIETARIO);
		$rows  = $this->users_model->my_owners($this->edificio_id)->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}



		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function propietarios_update($primary_key_value){
	
	$this->rat->log(uri_string(),1);
	$user = $this->load_propietario($primary_key_value);
	$change = false;
	if(!$user)
		redirect('encargado/propietarios_list');


	if(!empty($_POST)){

		if(isset($_POST['edificio_id'])){
			$data['edificio_id'] = $this->edificio_id;
		}

		if(isset($_POST['username'])){
			$data['username'] = $this->input->post('username',true);
		}

		if(!empty($_POST['password'])){
			$data['password'] = $this->input->post('password',true);
			$change = TRUE;
		}		

		if(isset($_POST['email_fw'])){
			$data['email_fw'] = $this->input->post('email_fw',true);
		}

	/*	if(isset($_POST['email'])){
			$data['email'] = $this->input->post('email',true);
		}
*/
		if(isset($_POST['active'])){
			$data['active'] = $this->input->post('active',true);
		}

		if(isset($_POST['first_name'])){
			$data['first_name'] = $this->input->post('first_name',true);
		}

		if(isset($_POST['last_name'])){
			$data['last_name'] = $this->input->post('last_name',true);
		}

		if(isset($_POST['unidad'])){
			$data['unidad'] = $this->input->post('unidad',true);
		}

		if(isset($_POST['phone'])){
			$data['phone'] = $this->input->post('phone',true);
		}

		if(isset($_POST['alternative_phone'])){
			$data['alternative_phone'] = $this->input->post('alternative_phone',true);
		}

		if(!$this->ion_auth->update($primary_key_value, $data)){
			redirect('encargado/users_error');
		}

		$unidades = $this->input->post('unidad_id',true);
		$this->unidades_model->add_unidad($primary_key_value,$unidades,PROPIETARIO);
		$baneos = $this->input->post('baned',true); 
		$this->Baned_model->new_baned($baneos,$primary_key_value);
		if($change){
			$values = $user->row();
			$this->ion_auth->set_password($values->email);
			//$this->send_email->new_welcome(array($values->email),$this->edificio_id);
		}


		redirect('encargado/propietarios_list');
	}

	$data['values'] = $user->row();

	$this->db->where('edificio_id',$this->edificio_id);

	$data['unidades'] = $this->unidades_model->disponibles($this->edificio_id);
	$data['unidades'] = $this->unidades_model->disponibles($this->edificio_id);
	$data['my_unidades'] = $this->unidades_model->my_unidades($primary_key_value);
	
	$data['baneos']  = $this->Baned_model->read(['baneos.user_id'=>$primary_key_value]);
	$array_baned = false ;
	if($data['baneos']){
		$array_baned = $this->Baned_model->get_array($data['baneos']);
	}
	$data['espacios']  = $this->espacios_model->get_espacios_baned($this->edificio_id,$array_baned);
	$this->layout->view('propietarios/update', $data);

}

private function load_propietario($user_id){
	$where['users.id'] = $user_id;
	$where['edificios.id'] = $this->edificio_id;
	$rs = $this->users_model->read($where);
	if(!$rs->num_rows()){
		return FALSE;
	}
	return $rs;
}

public function propietarios_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['id'] = $primary_key_value;

	if(!$this->users_model->delete_propietario($primary_key_value)){
		redirect('encargado/users_error');
	}

	redirect('encargado/propietarios_list');

}


public function set_baned(){

}

/*************************************** Fin propietarios **********************************/


/*************************************** inquilinos **********************************************/
    public function inquilinos($user_id){
    	$this->rat->log(uri_string(),1);
    	
    	$data['user_id'] = $user_id;

    	if($this->load_propietario($user_id))
    		$this->layout->view('inquilinos/list',$data);
    	else
    		redirect('encargado/propietarios_list');
    }

     public function inquilinos_read($user_id){
   		$this->rat->log(uri_string(),1);

   		$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
   		$order_type = 'DESC';
   		$order_by = 'users_unidad.id';
   		$search = false;
   		
   		if($_POST){
   			$limit = $this->input->post('limit',true);
   			$order_by = $this->input->post('order_by',true);
   			$order_type = $this->input->post('order_type',true);
   			$search = $this->input->post('search',true);
   		}

   		$this->load->model('Inquilino_model');
   		$this->db->order_by($order_by, $order_type);
   		$unidades = $this->unidades_model->get_array($user_id);


   		if($limit > 0)
   			$this->db->limit($limit);
   		
   		if($search != ''){

   			if(isset($searchables) && count($searchables) > 0){
   				$first_run = true;
   				$this->db->group_start();

   					foreach($searchables as $searchable){
   						if($first_run){
   							$this->db->like($searchable, $search);
   							$first_run = false;
   						}else{
   							$this->db->or_like($searchable, $search);
   						}
   					}

   				$this->db->group_end();
   			}
   		}

   		$this->db->where('unidades.edificio_id',$this->edificio_id);
   		$this->db->where_in('users_unidad.unidad_id',$unidades);

   		$data['registers']  = $this->Inquilino_model->read();
   		//echo $this->db->last_query();
   		$this->load->view('encargado/inquilinos/read', $data);
   		
   	}



		public function inquilinos_create($user_id){
			$this->rat->log(uri_string(),1);

	        $this->data['title'] = $this->lang->line('create_user_heading');
	        $tables = $this->config->item('tables','ion_auth');
	        $identity_column = $this->config->item('identity','ion_auth');
	        $this->data['identity_column'] = $identity_column;
	        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
	        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');
	        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
	        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
	        $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
	        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
	        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

	        if ($this->form_validation->run() == true)
	        {

	            $email    = strtolower($this->input->post('email'));
	            $unidad_id = intval($this->input->post('unidad_id',true));
	        	$rs = $this->unidades_model->get_user_unidad(array('users_unidad.unidad_id'=>$unidad_id,'users_unidad.user_id'=>$user_id));

	        	if(!$rs)
					redirect('encargado');

				$this->users_model->delete_inquilino_byunidad($unidad_id);
				
	          	if($this->add_inquilino_existent($email,$unidad_id))
	            	redirect('encargado/inquilinos/'.$user_id);


	            $identity = ($identity_column==='email') ? $email : $this->input->post('identity');
	            $password = $this->input->post('password');

	            $additional_data = array(
	                'edificio_id'=> $this->edificio_id,
	                'first_name' => $this->input->post('first_name',TRUE),
	                'last_name'  => $this->input->post('last_name',TRUE),
	                'phone'      => $this->input->post('phone',TRUE),
	            );
	            $groups = array(INQUILINO);

	        }

	        if ($this->form_validation->run() == true && $id = $this->ion_auth->register($identity, $password, $email, $additional_data,$groups))
	        {

	          	$inquilino['user_id'] = $id;
	            $inquilino['unidad_id'] = $unidad_id;
	            $inquilino['grupo_id'] = INQUILINO;
				$this->Inquilino_model->create($inquilino);
				$edificios = array($this->edificio_id);
				$this->edificios_model->add_edificios($id,$edificios);
	            $this->session->set_flashdata('message', $this->ion_auth->messages());
	           // die("dsdfd");
	            $this->ion_auth->set_password($email);
	           // $this->send_email->new_welcome(array($email),$this->edificio_id);
	           	redirect('encargado/inquilinos/'.$user_id);

	        }
	        else
	        {
	            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

	            $this->data['first_name'] = array(
	                'name'  => 'first_name',
	                'id'    => 'first_name',
	                'type'  => 'text',
	                'value' => $this->form_validation->set_value('first_name'),
	            );
	            $this->data['last_name'] = array(
	                'name'  => 'last_name',
	                'id'    => 'last_name',
	                'type'  => 'text',
	                'value' => $this->form_validation->set_value('last_name'),
	            );
	            $this->data['identity'] = array(
	                'name'  => 'identity',
	                'id'    => 'identity',
	                'type'  => 'text',
	                'value' => $this->form_validation->set_value('identity'),
	            );
	            $this->data['email'] = array(
	                'name'  => 'email',
	                'id'    => 'email',
	                'type'  => 'text',
	                'value' => $this->form_validation->set_value('email'),
	            );
	            $this->data['unidad'] = array(
	                'name'  => 'unidad',
	                'id'    => 'unidad',
	                'type'  => 'text',
	                'value' => $this->form_validation->set_value('unidad'),
	            );
	            $this->data['phone'] = array(
	                'name'  => 'phone',
	                'id'    => 'phone',
	                'type'  => 'text',
	                'value' => $this->form_validation->set_value('phone'),
	            );
	            $this->data['password'] = array(
	                'name'  => 'password',
	                'id'    => 'password',
	                'type'  => 'password',
	                'value' => $this->form_validation->set_value('password'),
	            );
	            $this->data['password_confirm'] = array(
	                'name'  => 'password_confirm',
	                'id'    => 'password_confirm',
	                'type'  => 'password',
	                'value' => $this->form_validation->set_value('password_confirm'),
	            );

	            $this->data['unidades'] =  $this->unidades_model->get_user_unidad(array('users_unidad.user_id'=>$user_id));
	            $this->data['user_id'] = $user_id;
	            $this->layout->view('inquilinos/create', $this->data);
	        }

	    }


	    public function inquilino_update($id){
			$this->rat->log(uri_string(),1);
	    	$this->data['title'] = $this->lang->line('edit_user_heading');
	    	$user = $this->ion_auth->user($id)->row();
	    	$groups=$this->ion_auth->groups()->result_array();
	    	$currentGroups = $this->ion_auth->get_users_groups($id)->result();

	    	$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required');
	    	$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required');
	    	$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required');

	    	if (isset($_POST) && !empty($_POST))
	    	{

	    		if ($this->input->post('password'))
	    		{
	    			$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
	    			$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
	    		}

	    		if ($this->form_validation->run() === TRUE)
	    		{
	    			$data = array(
	    				'first_name' => $this->input->post('first_name'),
	    				'last_name'  => $this->input->post('last_name'),
	    				'company'    => $this->input->post('company'),
	    				'phone'      => $this->input->post('phone'),
	    				'phone'      => $this->input->post('phone'),
	    				'active'     => 1,
	    			);

	    			if ($this->input->post('password'))
	    			{
	    				$data['password'] = $this->input->post('password',TRUE);
	    			}

	    			if ($this->ion_auth->is_admin())
	    			{
	    			
	    				$groupData = $this->input->post('groups',TRUE);

	    				if (isset($groupData) && !empty($groupData)) {

	    					$this->ion_auth->remove_from_group('', $id);

	    					foreach ($groupData as $grp) {
	    						$this->ion_auth->add_to_group($grp, $id);
	    					}
	    				}
	    			}

	    		   if($this->ion_auth->update($user->id, $data))
	    		    {

	    			    $this->session->set_flashdata('message', $this->ion_auth->messages() );
	    			    redirect('encargado/inquilinos/'.$id);

	    		    }
	    		    else
	    		    {

	    			    $this->session->set_flashdata('message', $this->ion_auth->errors() );

	    		    }

	    		}
	    	}

	    	// set the flash data error message if there is one
	    	$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

	    	$this->data['user'] = $user;
	    	$this->data['groups'] = $groups;
	    	$this->data['currentGroups'] = $currentGroups;

	    	$this->data['first_name'] = array(
	    		'name'  => 'first_name',
	    		'id'    => 'first_name',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('first_name', $user->first_name),
	    	);

	    	$this->data['last_name'] = array(
	    		'name'  => 'last_name',
	    		'id'    => 'last_name',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('last_name', $user->last_name),
	    	);
	    	$this->data['email'] = array(
	    		'name'  => 'email',
	    		'disabled'  => 'disabled',
	    		'id'    => 'email',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('email', $user->email),
	    	);

	    	$this->data['phone'] = array(
	    		'name'  => 'phone',
	    		'id'    => 'phone',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('phone', $user->phone),
	    	);

	    	$this->data['password'] = array(
	    		'name' => 'password',
	    		'id'   => 'password',
	    		'type' => 'password'
	    	);

	    	$this->data['password_confirm'] = array(
	    		'name' => 'password_confirm',
	    		'id'   => 'password_confirm',
	    		'type' => 'password'
	    	);

	    	$this->data['user_id'] = $id;
	    	$this->layout->view('inquilinos/update', $this->data);
	    }


	    public function inquilino_delete($users_unidad_id){
			$this->rat->log(uri_string(),1);
	    	$this->load->model('users_model');

        	$rs = $this->unidades_model->get_user_unidad(array('unidades.edificio_id'=>$this->edificio_id,'users_unidad.id'=>$users_unidad_id));
	    	if($rs->num_rows()){
	    		if(!$this->Inquilino_model->delete($users_unidad_id)){
	    		//	echo $this->db->last_query();
	    		}
	    		redirect('encargado/inquilinos');
	    	}else{
	    		redirect('encargado/inquilinos');
	    	}

	    }

/************************************************ fin Inquilinos ************************************/	    

/************************************* Consultas **********************************************/

public function consultas_list(){
	$this->rat->log(uri_string(),1);
	$this->db->where_in('estados.id',array(1,5,6));
	$data['estados'] = $this->db->get('estados')->result();
	$this->layout->view('consultas/list',$data);
}

public function consultas_create($user_id){
	$this->rat->log(uri_string(),1);

	$rs = $this->db->select('users.id,
		users.username,
		users.email,
		users.first_name,
		users.last_name,
		users.phone')
	->join('users_unidad','users_unidad.user_id = users.id')
	->join('unidades','unidades.id = users_unidad.unidad_id')
	->group_by('users.id')
	->get_where('users',array('users.id'=>$user_id,'unidades.edificio_id'=>$this->edificio_id));

	if(!$rs->num_rows())
		redirect('encargado');

	$user = $rs->row();

	if(!empty($_POST)){

		$data['edificio_id'] = $this->edificio_id;
		$data['usaurio_id'] = $user->id;
		$data['quien_id'] = $this->user->id;
		$data['tipo_consultas_id'] = $this->input->post('tipo_consulta_id',true);

		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/consultas/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;
			}

		}

		$data['fecha'] = date('Y-m-d');

		if(isset($_POST['descripcion'])){
			$data['descripcion'] = $this->input->post('descripcion',true);
		}

		$data['estado_id'] = PENDIENTE;
		//$data['is_admin'] = TRUE;

		if(isset($_POST['detalle'])){
			$data['detalle'] = $this->input->post('detalle',true);
		}

		if(!$this->consultas_model->create($data)){
			redirect('encargado/consultas_error');
		}

		redirect('encargado/consultas_list');
	}

	$data['tipo_consultas'] = $this->tipo_consultas_model->read();
	$data['user'] = $user;
	$this->layout->view('consultas/create',$data);
}

public function consultas_read(){
	$this->rat->log(uri_string(),1);

	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'consultas.estado_id';
	$search = false;

	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
		$estado_id = $this->input->post('estado_id',true);
	}

	$this->load->model('consultas_model');
	$this->db->order_by($order_by, $order_type);
	
	if($limit > 0)
		$this->db->limit($limit);

	$searchables = array('users.first_name',
		'users.last_name',
		'unidades.name',
		'unidades.departamento',
		'consultas.fecha',
		'consultas.detalle',
		'consultas.descripcion',
		'tipo_consultas.nombre',
		'consultas.estado_id',
		'estados.nombre',
	);

	if($search != ''){

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}						
			}

			$this->db->group_end();
		}
	}

	if(intval($estado_id) > 0){
		$this->db->where('consultas.estado_id',$estado_id);	
	}

	$this->db->where('consultas.edificio_id ',$this->edificio_id);

	$data['registers']  = $this->consultas_model->read();

	$this->load->view('encargado/consultas/read', $data);
}

public function consultas_excel(){
	$this->rat->log(uri_string(),1);
	$excelables=array("edificio",
		"unidad",
		"nombre",
		"apellido",
		"fecha",
		"categoria",
		"detalle",
		"descripcion",
		"estado");	

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_consultas_'.date('Y-m-d').'.xls';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();
		$order_type = 'DESC';
		$order_by = 'consultas.id';
		$search = false;
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = $this->input->get('search',true);
			$estado_id = $this->input->get('estado_id');
			if($limit > 0)
				$this->db->limit($limit);
		}

		$this->load->model('consultas_model');
		$this->db->order_by($order_by, $order_type);

		$searchables = array('users.first_name',
			'users.last_name',
			'unidades.name',
			'unidades.departamento',
			'consultas.fecha',
			'consultas.detalle',
			'consultas.descripcion',
			'tipo_consultas.nombre',
			'consultas.estado_id',
			'estados.nombre',
		);


		if($search != ''){

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}

				$this->db->group_end();
			}
		}


		$this->db->where('consultas.edificio_id ',$this->edificio_id);
		$rows =$this->consultas_model->read()->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}


public function consultas_view($consulta_id){
	$this->rat->log(uri_string(),1);
	$data['consulta'] = $this->consultas_model->get($consulta_id);
	if($data['consulta']){
		if($data['consulta']->edificio_id != $this->edificio_id)
			redirect('encargado/consultas_list');

		$data['respuestas'] = $this->consultas_model->get_respuesta($consulta_id);
		$this->layout->view('consultas/view',$data);	
	}else{
		redirect('encargado/consultas_list');
	}

}

public function consultas_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$this->load->model('consultas_model');

	$where['id'] = $primary_key_value;

	if(!$this->consultas_model->delete($where)){
		redirect('encargado/consultas_error');
	}
	redirect('encargado/consultas_list');
}

public function respuesta_consultas_create(){
	$this->rat->log(uri_string(),1);

	if($_POST){


		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/consultas/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;

			}
		}

		$data['consulta_id'] = $this->input->post('consulta_id',true);
		$estado_id = $this->input->post('estado_id',true);
		$data['fecha'] = date('Y-m-d');
		$data['user_id'] = $this->user->id;
		$data['is_admin'] = TRUE;

		if(isset($_POST['descripcion'])){
			$data['respuesta'] = $this->input->post('descripcion',true);

		}

		$this->db->insert('respuesta_consultas',$data);
		$this->notificar_respuesta($data);
		$this->consultas_model->update(
			array('estado_id'=>$estado_id),array('id'=>$data['consulta_id']));
		redirect('encargado/consultas_list');
	}

}


/*********************************** fin Consultas **********************************************/

/*********************************** Circulares *************************************************/


public function circular_create(){
	$this->rat->log(uri_string(),1);

	if(!empty($_POST)){

		$data['edificio_id'] = $this->edificio_id;

		$data['usuario_id'] = $this->user->id;

		

		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/circular/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg|zip|rar';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;
			}
		}

		if(isset($_POST['fecha'])){
			$data['fecha'] = $this->input->post('fecha',true);
		}			

		if(isset($_POST['fecha_envio'])){
			$data['fecha_envio'] = $this->input->post('fecha_envio',true);
		}			

		if(isset($_POST['titulo'])){
			$data['titulo'] = $this->input->post('titulo',true);
		}

		if(isset($_POST['detalle'])){
			$data['detalle'] = $this->input->post('detalle',true);
		}			

		if(isset($_POST['tipo_servicio_id'])){
			$data['tipo_servicio_id'] = $this->input->post('tipo_servicio_id',true);
		}


        if(isset($_POST['estado_id'])){
             $data['estado_id'] = $this->input->post('estado_id',true);
        }
        

		if(!$this->circular_model->create($data)){
			redirect('encargado/circular_error');
		}

		$circular_id = $this->db->insert_id();

		if($this->input->post('estado_id',TRUE) == ENVIADO){
			$this->notificar_circular($circular_id);
		}


		redirect('encargado/circular_list');
	}

	$data['edificios'] = $this->db->get('edificios');
	$data['categorias'] = $this->db->get('tipo_servicio');

	$this->layout->view('circular/create',$data);
}


public function circular_read(){
	$this->rat->log(uri_string(),1);

	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'circular.id';
	$search = false;
	$estado_id = false;
	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
		$estado_id = $this->input->post('estado_id',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	if($search != ''){
		$searchables = array("circular.edificio_id",
			"circular.titulo",
			"circular.fecha",
			"circular.fecha_envio",
			"circular.detalle",
			"circular.estado_id");


		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();
			foreach($searchables as $searchable){

				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}

			}
			$this->db->group_end();
		}
	}

	if($estado_id > 0){
		$this->db->where('circular.estado_id ',$estado_id);
	}

	$this->db->where('circular.edificio_id',$this->edificio_id);
	$data['registers']  = $this->circular_model->read();
	$this->load->view('encargado/circular/read', $data);
}

public function circular_list(){
	$this->rat->log(uri_string(),1);
	$data['unidades'] = $this->unidades_model->disponibles($this->edificio_id);
	$this->circular_model->desactivate();
	$this->layout->view('circular/list',$data);
}

public function circular_excel(){
	$this->rat->log(uri_string(),1);
	$excelables = array("edificio_id",
		"titulo",
		"fecha",
		"fecha_envio",
		"detalle",
		"estado");

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$objPHPExcel = new PHPExcel();


		$order_type = 'DESC';
		$order_by = 'circular.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}
		$this->db->order_by($order_by, $order_type);
		if($search != ''){
			$searchables = array("circular.edificio_id",
				"circular.titulo",
				"circular.fecha",
				"circular.fecha_envio",
				"circular.detalle",
				"circular.estado_id");
			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}

				}

				$this->db->group_end();
			}
		}

//$this->db->where('circular.estado_id',TRUE);
		$this->db->where('circular.edificio_id',$this->edificio_id);
		$rows =$this->circular_model->read()->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function circular_update($primary_key_value){
	$this->rat->log(uri_string(),1);


	$where['circular.id'] = $primary_key_value;
	$where['circular.edificio_id'] = $this->edificio_id;
	if(!empty($_POST)){


		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/circular/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg|zip';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;
			}
		}

		if(isset($_POST['fecha_envio'])){
			$data['fecha_envio'] = $this->input->post('fecha_envio',true);
		}					

		if(isset($_POST['fecha'])){
			$data['fecha'] = $this->input->post('fecha',true);
		}			

		if(isset($_POST['titulo'])){
			$data['titulo'] = $this->input->post('titulo',true);
		}

		if(isset($_POST['detalle'])){
			$data['detalle'] = $this->input->post('detalle',true);
		}

        if(isset($_POST['estado_id'])){
            if($this->input->post('estado_id',TRUE) != 13){
                $data['estado_id'] = $this->input->post('estado_id',true);
            }else{
                $data['estado_id'] = ENVIADO;
            }
        }

		if(isset($_POST['tipo_servicio_id'])){
			$data['tipo_servicio_id'] = $this->input->post('tipo_servicio_id',true);
		}

        if($this->input->post('estado_id',TRUE) != 13){
            if($this->input->post('estado_id',TRUE) == ENVIADO){
                $this->notificar_circular($primary_key_value);
            }
        }


		if(!$this->circular_model->update($data, $where)){
			redirect('encargado/circular_error');
		}

		redirect('encargado/circular_list');
	}

	$data['categorias'] = $this->db->get('tipo_servicio');
	$data['values'] = $this->circular_model->read($where)->row();
	if(!$data['values'])
		redirect('encargado/circular_list');
	$this->layout->view('circular/update', $data);
}

public function circular_delete($primary_key_value){
	$this->rat->log(uri_string(),1);



	$where['id'] = $primary_key_value;

	if(!$this->circular_model->delete($where)){
		redirect('encargado/circular_error');
	}

	redirect('encargado/circular_list');

}

public function circular_view($circular_id){
	$this->rat->log(uri_string(),1);
	$this->layout->view('circular/view',array('circular_id'=>$circular_id));
}



public function view_circular($circular_id){

	$this->rat->log(uri_string(),1);
	//$this->load_circular($circular_id);
	$this->db->where('circular.edificio_id',$this->edificio_id);
	$data['circular']  = $this->circular_model->get($circular_id);
	
	if($data['circular'])
		$this->layout->view('circular/view_circular', $data);
	else
		redirect('encargado/circular_list');


}


public function view_circular_read($circular_id){
	$this->rat->log(uri_string(),1);

	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'view_circular.id';
	$search = false;
	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	if($search != ''){

		$searchables = array("users.first_name",
			"users.last_name",
			"users.email",
			"unidades.name",
			"unidades.departamento");

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();
		}
	}

	$data['registers']  = $this->circular_model->view_read($circular_id);
	$this->load->view('encargado/circular/read_view', $data);
}

public function notificar_circular($circular_id){
	$this->rat->log(uri_string(),1);

	if(isset($_POST['unidad_id'])){
		$unidades = $_POST['unidad_id'];
		$email =  $this->db->where_in('unidades.id',$unidades);
	}
	
	$email = $this->users_model->get_array($this->edificio_id);
	

	if($email){
		
		$data['circular'] = $this->circular_model->read(array('circular.id'=>$circular_id))->row();
		$this->send_email->new_circular($email,$data);

		$fcm_token = $this->users_model->get_all_push_movile($this->edificio_id);
		if(count($fcm_token) > 0){
			$this->notification->title = "Nueva circular cargada";
			$this->notification->body = $data['circular']->titulo;
			$this->notification->send_push_movile($fcm_token);
		}

		$this->session->set_flashdata('message', "Circular creada exitosamente todos los  propietarios / Inquilinos fueron notificados" );
	}else{
		$this->session->set_flashdata('error_message', "No Se pudo notificar a ningun propietario / Inquilino" );
	}



}

/*********************************** Fin de Circulares *****************************************/


/***********************************Propuesta *********************************************/


public function propuestas_create(){
	$this->rat->log(uri_string(),1);

	if(!empty($_POST)){

		$data['usuario_id'] = $this->user->id;
		$data['edificio_id'] = $this->edificio_id;


		if(isset($_POST['fecha_ini'])){
			$data['fecha_ini'] = $this->input->post('fecha_ini',true);
		}

		if(isset($_POST['fecha_fin'])){
			$data['fecha_fin'] = $this->input->post('fecha_fin',true);
		}

		if(isset($_POST['titulo'])){
			$data['titulo'] = $this->input->post('titulo',true);
		}

		if(isset($_POST['descripcion'])){
			$data['descripcion'] = $this->input->post('descripcion',true);
		}		

		if(isset($_POST['estado_id'])){
			$data['estado_id'] = $this->input->post('estado_id',true);
		}

		if(!$id_propuesta = $this->propuestas_model->create($data)){
			redirect('encargado/propuestas_error');
		}

		$this->add_opciones($id_propuesta);
		$this->add_archivos_propuesta($id_propuesta);

		redirect('encargado/propuestas_list');
	}

	$this->layout->view('propuestas/create');
}


public function propuestas_read(){
	$this->rat->log(uri_string(),1);
	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'propuestas.id';
	$search = false;
	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
		$estado_id = $this->input->post('estado_id',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	
	if($search != ''){

		$searchables = array("edificios.nombre",
			"users.first_name",
			"users.last_name",
			"propuestas.fecha_fin",
			"propuestas.titulo",
			"estados.nombre",
			"propuestas.descripcion",
			"propuestas.estado_id");

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();
		}
	}

	/*if(!empty($estado_id)){
		$this->db->where('propuestas.estado_id',$estado_id);
	}*/
	$this->db->where(array('propuestas.edificio_id'=>$this->edificio_id));


	$data['registers']  = $this->propuestas_model->read();
	//echo $this->db->last_query();
	$this->load->view('encargado/propuestas/read', $data);
}

public function propuestas_list(){
	$this->rat->log(uri_string(),1);
	$this->db->where_in('estados.id',array(5,6,1));
	$this->db->order_by('estados.sort','asc');
	$data['estados'] = $this->db->get('estados');
	$this->propuestas_model->desactivate();
	$data['unidades'] = $this->unidades_model->disponibles($this->edificio_id);
	$this->layout->view('propuestas/list',$data);
}

public function propuestas_excel(){
	$this->rat->log(uri_string(),1);

	$excelables = array(
		"propuestas.estado_id",
		"edificios",
		"nombre",
		"Apellido",
		"propuestas.fecha_fin",
		"propuestas.titulo",
		"propuestas.descripcion",
		"estados.nombre"
	);

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$objPHPExcel = new PHPExcel();


		$order_type = 'DESC';
		$order_by = 'propuestas.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}
		$this->db->order_by($order_by, $order_type);
		if($search != ''){

			$searchables = array("edificios.nombre",
				"users.first_name",
				"users.last_name",
				"propuestas.fecha_fin",
				"propuestas.titulo",
				"estados.nombre",
				"propuestas.descripcion",
				"propuestas.estado_id");

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}

				$this->db->group_end();

			}
		}
		$rows =$this->propuestas_model->read()->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function propuestas_update($primary_key_value){
	$this->rat->log(uri_string(),1);


	$where['propuestas.id'] = $primary_key_value;
	if(!empty($_POST)){

		
		if(isset($_POST['fecha_ini'])){
			$data['fecha_ini'] = $this->input->post('fecha_ini',true);
		}
		
		if(isset($_POST['fecha_fin'])){
			$data['fecha_fin'] = $this->input->post('fecha_fin',true);
		}

		if(isset($_POST['usuario_id'])){
			$data['usuario_id'] = $this->input->post('usuario_id',true);
		}

		
		$data['edificio_id'] = $this->edificio_id;
		



		if(isset($_POST['sector'])){
			$data['sector'] = $this->input->post('sector',true);
		}

		if(isset($_POST['descripcion'])){
			$data['descripcion'] = $this->input->post('descripcion',true);
		}

		if(isset($_POST['estado_id'])){
			$data['estado_id'] = $this->input->post('estado_id',true);
		}

		$this->add_opciones($primary_key_value);

		$this->add_archivos_propuesta($primary_key_value);

		if(!$this->propuestas_model->update($data, $where)){
			redirect('encargado/propuestas_error');
		}

		redirect('encargado/propuestas_list');
	}

	$data['values'] = $this->propuestas_model->read($where)->row();
	$data['opciones'] = $this->opciones_model->read(array('propuesta_id'=>$primary_key_value));
	$data['files'] = $this->archivos_propuesta_model->read(array('propuesta_id'=>$primary_key_value));
	$this->layout->view('propuestas/update', $data);
}

public function propuestas_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['id'] = $primary_key_value;

	if(!$this->propuestas_model->delete($where)){
		redirect('encargado/propuestas_error');
	}

	redirect('encargado/propuestas_list');

}


private function add_opciones($id_propuesta){
	$this->opciones_model->delete(array('propuesta_id' =>$id_propuesta));
	$opciones = $_POST['opciones'];
	if(is_array($opciones)){
		foreach ($opciones as $key => $value) {
			if(!empty( $value)){
				$insert['propuesta_id'] = $id_propuesta;
				$insert['titulo'] = $value;
				$this->opciones_model->create($insert);
			}
		}
	}
}

private function add_archivos_propuesta($id_propuesta){

	$num = count($_FILES['file']['name']);

	for ($i = 0; $i < $num; $i++){

		if(!empty($_FILES['file']['name'][$i])){
			$config['upload_path'] = BASEPATH.'../upload/propuesta/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['file']['name'][$i];;
			$_FILES['file_1']['name'] = $_FILES['file']['name'][$i];
			$_FILES['file_1']['type'] = $_FILES['file']['type'][$i];
			$_FILES['file_1']['tmp_name']= $_FILES['file']['tmp_name'][$i];
			$_FILES['file_1']['error']= $_FILES['file']['error'][$i];
			$_FILES['file_1']['size']= $_FILES['file']['size'][$i];    

			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;
			if($this->upload($config,'file_1')){ 
				$insert['file'] = $filename;
				$insert['propuesta_id'] = $id_propuesta;
				$this->archivos_propuesta_model->create($insert);
			}
		}
	}
}

public function delete_file_propuesta($propuesta_id){
	$this->archivos_propuesta_model->delete(array('id'=>$propuesta_id));
}

public function propuestas_votacion($propuesta_id){
	$this->rat->log(uri_string(),1);
	$where = array('propuestas.id'=>$propuesta_id,
		'edificio_id'=>$this->edificio_id);
	$data['propuesta'] = $this->propuestas_model->read($where)->row();
	$data['opciones'] = $this->opciones_model->votos($propuesta_id);
	$data['votantes'] = $this->opciones_model->total_votos($propuesta_id)->row();
	$data['unidades'] = $this->unidades_model->read(array('edificio_id'=>$this->edificio_id));
	$data['files'] = $this->archivos_propuesta_model->read(array('propuesta_id'=>$propuesta_id));
	$this->layout->view('propuestas/votaciones',$data);

}

/***********************************Fin propuesta *********************************************/

/*********************************** asamblea *************************************************/


public function asamblea_create(){
	$this->rat->log(uri_string(),1);

	if(!empty($_POST)){

		$data['edificio_id'] = $this->edificio_id;

		$data['usuario_id'] = $this->user->id;

		$data['fecha'] = date("Y-m-d");

		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/asamblea/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;
			}else{
				die("error al subir el archivo");
			}
		}			

		if(!empty($_FILES['memoria_balanse']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/asamblea/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['memoria_balanse']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'memoria_balanse')){ 
				$data['memoria_balanse'] = $filename;
			}else{
				die("error al subir el archivo");
			}
		}			

		if(!empty($_FILES['acta_asamblea']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/asamblea/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['acta_asamblea']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'acta_asamblea')){ 
				$data['acta_asamblea'] = $filename;
			}else{
				die("error al subir el archivo");
			}
		}

		if(isset($_POST['fecha_envio'])){
			$data['fecha_envio'] = $this->input->post('fecha_envio',true);
		}			

		if(isset($_POST['titulo'])){
			$data['titulo'] = $this->input->post('titulo',true);
		}

		if(isset($_POST['detalle'])){
			$data['detalle'] = $this->input->post('detalle',true);
		}			

		if(isset($_POST['tipo_servicio_id'])){
			$data['tipo_servicio_id'] = $this->input->post('tipo_servicio_id',true);
		}

		if(isset($_POST['estado_id'])){
			$data['estado_id'] = intval($this->input->post('estado_id',true));

			if($data['estado_id'] == ACTIVO){
				$this->notificar_asamblea("Archivo de asamblea cargado");
			}

		}

		if(!$this->asambleas_model->create($data)){
			redirect('encargado/asamblea_error');

		}
		
		redirect('encargado/asamblea_list');
	}
	$this->layout->view('asamblea/create');
}


public function asamblea_read(){
	$this->rat->log(uri_string(),1);

	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'asambleas.id';
	$search = false;
	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	if($search != ''){
		$searchables = array("asambleas.edificio_id",
			"asambleas.titulo",
			"asambleas.fecha",
			"asambleas.fecha_envio",
			"asambleas.detalle",
			"asambleas.estado_id");


		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();

		}
	}

	$this->db->where('asambleas.edificio_id',$this->edificio_id);
	$data['registers']  = $this->asambleas_model->read();
	$this->load->view('encargado/asamblea/read', $data);
}

public function asamblea_list(){
	$this->rat->log(uri_string(),1);
	$data['unidades'] = $this->unidades_model->disponibles($this->edificio_id);
	$this->layout->view('asamblea/list',$data);
}

public function asamblea_excel(){
	$this->rat->log(uri_string(),1);
	$excelables = array("edificio_id",
		"titulo",
		"fecha",
		"fecha_envio",
		"detalle",
		"autorizacion");

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$objPHPExcel = new PHPExcel();


		$order_type = 'DESC';
		$order_by = 'asambleas.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}
		$this->db->order_by($order_by, $order_type);
		if($search != ''){
			$searchables = array("asambleas.edificio_id",
				"asambleas.titulo",
				"asambleas.fecha",
				"asambleas.fecha_envio",
				"asambleas.detalle",
				"asambleas.estado_id");
			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();
				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}
				$this->db->group_end();
			}
		}

//	$this->db->where('asambleas.estado_id',TRUE);
		$this->db->where('asambleas.edificio_id',$this->edificio_id);
		$rows =$this->asambleas_model->read()->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function asamblea_update($primary_key_value){
	$this->rat->log(uri_string(),1);


	$where['asambleas.id'] = $primary_key_value;
	if(!empty($_POST)){


		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/asamblea/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg|rar|zip';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;
			}
		}		

		if(!empty($_FILES['memoria_balanse']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/asamblea/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg|rar|zip';
			$file = $_FILES['memoria_balanse']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'memoria_balanse')){ 
				$data['memoria_balanse'] = $filename;
			}
		}		

		if(!empty($_FILES['acta_asamblea']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/asamblea/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg|rar|zip';
			$file = $_FILES['acta_asamblea']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'acta_asamblea')){ 
				$data['acta_asamblea'] = $filename;
			}
		}

		if(isset($_POST['fecha_envio'])){
			$data['fecha_envio'] = $this->input->post('fecha_envio',true);
		}			

		if(isset($_POST['titulo'])){
			$data['titulo'] = $this->input->post('titulo',true);
		}

		if(isset($_POST['detalle'])){
			$data['detalle'] = $this->input->post('detalle',true);
		}

		if(isset($_POST['estado_id'])){
			$data['estado_id'] = $this->input->post('estado_id',true);

			if($data['estado_id'] == ACTIVO){
				$this->notificar_asamblea("Archivo de asamblea cargado");
			}

		}

		if(!$this->asambleas_model->update($data, $where)){
			redirect('encargado/asamblea_error');
		}
		

	//	$this->notificar_asamblea("Archivo de asamblea cargado");
		redirect('encargado/asamblea_list');
	}

	$data['values'] = $this->asambleas_model->read($where)->row();

	$this->layout->view('asamblea/update', $data);
}

public function asamblea_delete($primary_key_value){
	$this->rat->log(uri_string(),1);



	$where['id'] = $primary_key_value;

	if(!$this->asambleas_model->delete($where)){
		redirect('encargado/asamblea_error');
	}

	redirect('encargado/asamblea_list');

}


public function notificar_asamblea($menssage){
	$this->rat->log(uri_string(),1);
	$email = $this->users_model->get_array($this->edificio_id);
	$data['edificio_id'] = $this->edificio_id;
	$data['menssage'] = $menssage;
	$this->send_email->new_asamblea($email,$data);
}

/*********************************** Fin de Asambleas *****************************************/
/*********************************** Encargado ********************************************/


public function encargado_create(){
	$this->rat->log(uri_string(),1);

	if(!empty($_POST)){


		$data['edificio_id'] = $this->edificio_id;

		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/encargado/';
			$config['allowed_types'] = 'jpeg|gif|jpg|png|jpeg';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['foto'] = $filename;
			}
		}			

		if(!empty($_FILES['art']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/encargado/';
			$config['allowed_types'] = 'pdf|docx|doc|xls|xlsx|jpeg|gif|jpg|png|jpeg';
			$file = $_FILES['art']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'art')){ 
				$data['art'] = $filename;
			}
		}			

		if(isset($_POST['nombre'])){
			$data['nombre'] = $this->input->post('nombre',true);
		}			

		if(isset($_POST['cargo_id'])){
			$data['cargo_id'] = $this->input->post('cargo_id',true);
		}

		if(isset($_POST['email'])){
			$data['email'] = $this->input->post('email',true);
		}	

		if(isset($_POST['telefono'])){
			$data['telefono'] = $this->input->post('telefono',true);
		}

		if(isset($_POST['legajo'])){
			$data['legajo'] = $this->input->post('legajo',true);
		}

		if(isset($_POST['horario'])){
			$data['horario'] = $this->input->post('horario',true);
		}

		if(isset($_POST['recibos'])){
			$data['recibos'] = $this->input->post('recibos',true);
		}

		if(isset($_POST['vacaciones'])){
			$data['vacaciones'] = $this->input->post('vacaciones',true);
		}

		if(isset($_POST['seguros'])){
			$data['seguros'] = $this->input->post('seguros',true);
		}

		if(isset($_POST['tarea'])){
			$data['tarea'] = $this->input->post('tarea',true);
		}

		if(isset($_POST['medicina'])){
			$data['medicina'] = $this->input->post('medicina',true);
		}

		if(isset($_POST['ropa'])){
			$data['ropa'] = $this->input->post('ropa',true);
		}

		if(isset($_POST['cronograma'])){
			$data['cronograma'] = $this->input->post('cronograma',true);
		}

		if(!$this->encargado_model->create($data)){
			redirect('encargado/encargado_error');
		}

		redirect('encargado/encargado_list');
	}
	$this->db->order_by('sort','asc');
	$data['cargos']  = $this->db->get('cargos')->result();
	$this->layout->view('encargado/create',$data);
}

public function encargado_update($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['encargado.id'] = $primary_key_value;
	$where['encargado.edificio_id'] = $this->edificio_id;

	if(!empty($_POST)){


		$data['edificio_id'] = $this->edificio_id;

		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/encargado/';
			$config['allowed_types'] = 'jpeg|gif|jpg|png|jpeg';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['foto'] = $filename;
			}
		}

		if(!empty($_FILES['art']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/encargado/';
			$config['allowed_types'] = 'pdf|docx|doc|xls|xlsx|jpeg|gif|jpg|png|jpeg';
			$file = $_FILES['art']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'art')){ 
				$data['art'] = $filename;
			}
		}

		if(isset($_POST['nombre'])){
			$data['nombre'] = $this->input->post('nombre',true);
		}		

		if(isset($_POST['cargo_id'])){
			$data['cargo_id'] = $this->input->post('cargo_id',true);
		}

		if(isset($_POST['email'])){
			$data['email'] = $this->input->post('email',true);
		}	

		if(isset($_POST['telefono'])){
			$data['telefono'] = $this->input->post('telefono',true);
		}

		if(isset($_POST['legajo'])){
			$data['legajo'] = $this->input->post('legajo',true);
		}

		if(isset($_POST['horario'])){
			$data['horario'] = $this->input->post('horario',true);
		}

		if(isset($_POST['recibos'])){
			$data['recibos'] = $this->input->post('recibos',true);
		}

		if(isset($_POST['vacaciones'])){
			$data['vacaciones'] = $this->input->post('vacaciones',true);
		}


		if(isset($_POST['seguros'])){
			$data['seguros'] = $this->input->post('seguros',true);
		}

		if(isset($_POST['medicina'])){
			$data['medicina'] = $this->input->post('medicina',true);
		}			

		if(isset($_POST['tarea'])){
			$data['tarea'] = $this->input->post('tarea',true);
		}

		if(isset($_POST['ropa'])){
			$data['ropa'] = $this->input->post('ropa',true);
		}

		if(isset($_POST['cronograma'])){
			$data['cronograma'] = $this->input->post('cronograma',true);
		}

		if(!$this->encargado_model->update($data, $where)){
			redirect('encargado/encargado_error');
		}

		redirect('encargado/encargado_list');
	}

	$rs = $this->encargado_model->read($where);

	if(!$rs->num_rows())
		redirect('encargado/encargado_list');

	$data['values']  = $rs->row();
	$this->db->order_by('sort','asc');
	$data['cargos']  = $this->db->get('cargos')->result();
	$this->layout->view('encargado/update', $data);
}


public function encargado_list(){
	$this->rat->log(uri_string(),1);
	$this->layout->view('encargado/list');
}

public function encargado_read(){
	$this->rat->log(uri_string(),1);
	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'cargos.id';
	$search = false;
	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	if($search != ''){
		$searchables = array("edificios.nombre",
			"encargado.cargo",
			"encargado.nombre",
			"encargado.legajo",
			"encargado.horario",
			"encargado.recibos",
			"encargado.vacaciones",
			"encargado.art",
			"encargado.seguros",
			"encargado.medicina",
			"encargado.ropa",
			"encargado.cronograma");

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();
			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}
			$this->db->group_end();
		}
	}
	$this->db->where('encargado.edificio_id',$this->edificio_id);
	$data['registers']  = $this->encargado_model->read();
	$this->load->view('encargado/encargado/read', $data);
}

public function encargado_excel(){
	$this->rat->log(uri_string(),1);

	$excelables = array("edificio",
		"nombre",
		"legajo",
		"horario",
		"recibos",
		"vacaciones",
		"art",
		"seguros",
		"medicina",
		"ropa",
		"cronograma");

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$objPHPExcel = new PHPExcel();


		$order_type = 'DESC';
		$order_by = 'encargado.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}
		$this->db->order_by($order_by, $order_type);
		if($search != ''){

			$searchables = array("edificios.nombre",
				"encargado.nombre",
				"encargado.legajo",
				"encargado.horario",
				"encargado.recibos",
				"encargado.vacaciones",
				"encargado.art",
				"encargado.seguros",
				"encargado.medicina",
				"encargado.ropa",
				"encargado.cronograma");

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();
				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}

				}
				$this->db->group_end();
			}
		}
		$this->db->where('encargado.edificio_id',$this->edificio_id);
		$rows =$this->encargado_model->read()->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}


public function encargado_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['id'] = $primary_key_value;

	if(!$this->encargado_model->delete($where)){
		redirect('encargado/encargado_error');
	}
	redirect('encargado/encargado_list');
}

/*********************************** fin Encargado *****************************************/
/******************************************* Seguros ********************************************************/


public function legales_list($legal_id){
	$this->rat->log(uri_string(),1);
	$this->session->set_userdata('legal_id',$legal_id);
	if(!$rs = $this->Legales_model->get_tipo()){
		redirect('encargado');
	}else{

		$data['title'] = $rs->name;
		$data['legal_id'] = $legal_id;
		$this->layout->view('legales/list',$data);
	}

}

public function legales_create(){

	$this->rat->log(uri_string(),1);

	if($this->session->has_userdata('legal_id')){
		$legal_id = $this->session->userdata('legal_id');
		if(!$rs = $this->Legales_model->get_tipo()){
			redirect('encargado');
		}
	}else{
		redirect('encargado/legales_list/'.SEGUROS);
	}

	if(!empty($_POST)){

		if(isset($_POST['titulo'])){
			$parents['titulo'] = $this->input->post('titulo',true);
		}
		if(isset($_POST['abogado'])){
			$parents['abogado'] = $this->input->post('abogado',true);
		}
		if(isset($_POST['legajo'])){
			$parents['legajo'] = $this->input->post('legajo',true);
		}

		$parents['edificio_id'] = $this->edificio_id;

		$parents['tipo_legal_id'] =$legal_id;

		if(!$id = $this->Legales_model->create($parents)){

			redirect('encargado/legales_error');
		}else{
			
			if(!empty($_FILES['file']['name'])){

			    $this->load->library('upload');
			    $uploadPath = BASEPATH.'../upload/legales/';
			    $config['upload_path'] = $uploadPath;
			    $config['allowed_types'] = 'xlsx|txt|doc|ppt|pptx|xls|docx|pdf|gif|jpg|png|jpeg';
			    $files = $_FILES;
			    $cpt = count($_FILES['file']['name']);

			    $archivos = array();

			    for($i=0; $i<$cpt; $i++)
			    {           
			        $file = $files['file']['name'][$i];
			        $file = str_replace ( ' ', '_', $file);
			        $name = $_POST['name_legal'][$i];

			    	//die($file);
			        if(!empty($file)){

			            $config['file_name'] = $file;
			            $_FILES['file']['name']= $files['file']['name'][$i];
			            $_FILES['file']['type']= $files['file']['type'][$i];
			            $_FILES['file']['tmp_name']= $files['file']['tmp_name'][$i];
			            $_FILES['file']['error']= $files['file']['error'][$i];
			            $_FILES['file']['size']= $files['file']['size'][$i];

			            $this->upload->initialize($config);
			            if($this->upload->do_upload('file')){
			            	
			            	$file_legales = array('legal_id'=>$id,
			            		'name'=>$name,
			            		'file'=>$file,
			            	);

			                $this->db->insert('file_legales',$file_legales);
			            }else{
			                echo  $this->upload->display_errors();
			                die();
			            }
			        }

			    }

			}

		}

		redirect('encargado/legales_list/'.$legal_id);
	}

	$data['title'] = "Nuevo archivo de ".$rs->name;
	$data['legal_id'] = $legal_id;
	$this->layout->view('legales/create',$data);
}

public function legales_update($primary_key_value){

	$this->rat->log(uri_string(),1);
	$where['legales.id'] = $primary_key_value;
	$where['legales.edificio_id'] = $this->edificio_id;
	$data['values'] = $this->Legales_model->read($where)->row();
	$rs = $this->Legales_model->get_tipo($data['values']->tipo_legal_id);

	if(!empty($_POST)){

		if(isset($_POST['titulo'])){
			$parents['titulo'] = $this->input->post('titulo',true);
		}
		if(isset($_POST['abogado'])){
			$parents['abogado'] = $this->input->post('abogado',true);
		}
		if(isset($_POST['legajo'])){
			$parents['legajo'] = $this->input->post('legajo',true);
		}

		$parents['edificio_id'] = $this->edificio_id;


		if(!empty($_FILES['file']['name'])){

		    $this->load->library('upload');
		    $uploadPath = BASEPATH.'../upload/legales/';
		    $config['upload_path'] = $uploadPath;
		    $config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
		    $files = $_FILES;
		    $cpt = count($_FILES['file']['name']);

		    $archivos = array();

		    for($i=0; $i<$cpt; $i++)
		    {           
		        $file = $files['file']['name'][$i];
		        $file = str_replace ( ' ', '_', $file);
		        $name = $_POST['name_legal'][$i];

		    	//die($file);
		        if(!empty($file)){

		            $config['file_name'] = $file;
		            $_FILES['file']['name']= $files['file']['name'][$i];
		            $_FILES['file']['type']= $files['file']['type'][$i];
		            $_FILES['file']['tmp_name']= $files['file']['tmp_name'][$i];
		            $_FILES['file']['error']= $files['file']['error'][$i];
		            $_FILES['file']['size']= $files['file']['size'][$i];

		            $this->upload->initialize($config);
		            if($this->upload->do_upload('file')){
		            	
		            	$file_legales = array('legal_id'=>$primary_key_value,
		            		'name'=>$name,
		            		'file'=>$file,
		            	);

		                $this->db->insert('file_legales',$file_legales);
		            }else{
		                echo  $this->upload->display_errors();
		                die();
		            }
		        }

		    }

		}


		if(!$this->Legales_model->update($parents, $where)){
			redirect('encargado/legales_error');
		}

		redirect('encargado/legales_list/'.$data['values']->tipo_legal_id);
	}



	$data['title'] = "Editar archivo de ".$rs->name;
	$this->layout->view('legales/update', $data);
}

public function load_file($legal_id){

	$rs = $this->Legales_model->my_files($legal_id);
 
    if(!$rs)
    	return FALSE;

    $data['files'] = $rs;
    echo  $this->load->view('encargado/legales/load_files',$data,true);

}

public function legales_read(){
	$this->rat->log(uri_string(),1);
	
	if($this->session->has_userdata('legal_id')){
		$legal_id = $this->session->userdata('legal_id');
	}else{
		redirect('encargado/legales_list/'.SEGUROS);
	}

	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'legales.id';
	$search = false;
	if($_POST){
		$limit = $this->input->post('li	mit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);

	if($search != ''){

		$searchables = array("edificios.nombre",
			"legales.titulo",
			"legales.file",
		);

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();
		}
	}
	
	$this->db->where('legales.edificio_id',$this->edificio_id);
	$this->db->where('legales.tipo_legal_id',$legal_id);
	$data['registers']  = $this->Legales_model->read();
	$data['legal_id'] = $legal_id;
	$this->load->view('encargado/legales/read', $data);
}

public function legales_excel(){

	$this->rat->log(uri_string(),1);

	if($this->session->has_userdate('legal_id')){
		$legal_id = $this->session->userdata('legal_id');
	}else{
		redirect('encargado/legales_list/'.SEGUROS);
	}
	

	$excelables=array('titulo','file','timestamp');

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$objPHPExcel = new PHPExcel();
		$order_type = 'DESC';
		$order_by = 'legales.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}
		$this->db->order_by($order_by, $order_type);
		if($search != ''){

			$searchables = array("edificios.nombre",
				"legales.titulo",
				"legales.file",
			);

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}
				$this->db->group_end();
			}
		}

		$this->db->where('legales.edificio_id',$this->edificio_id);
		$this->db->where('legales.tipo_legal_id',$legal_id);

		$rows =$this->Legales_model->read()->result_array();
		$first = true;

		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}

		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}


public function legales_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['id'] = $primary_key_value;
	$where['edificio_id'] = $this->edificio_id;

	if(!$this->Legales_model->delete($where)){
		redirect('encargado/legales_error');
	}

	redirect('encargado/legales_list');

}



/*******************fin seguros ******************************/
/************ reglamentos ********************/



public function reglamentos_create(){
	$this->rat->log(uri_string(),1);

	if(!empty($_POST)){

		if(isset($_POST['titulo'])){
			$data['titulo'] = $this->input->post('titulo',true);
		}

		$data['edificio_id'] = $this->edificio_id;

		if(!empty($_FILES['file']['name'])){



			$config['upload_path'] = BASEPATH.'../upload/reglamentos/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;
			}
		}


		if(!$this->reglamentos_model->create($data)){
			redirect('encargado/reglamentos_error');
		}

		redirect('encargado/reglamentos_list');
	}

	$this->layout->view('reglamentos/create');
}


public function reglamentos_read(){
	$this->rat->log(uri_string(),1);
	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'reglamentos.id';
	$search = false;
	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	if($search != ''){

		$searchables = array("edificios.nombre",
			"reglamentos.titulo",
			"reglamentos.file",
		);

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();
		}
	}
	$this->db->where('reglamentos.edificio_id',$this->edificio_id);
	$data['registers']  = $this->reglamentos_model->read();
	$this->load->view('encargado/reglamentos/read', $data);
}

public function reglamentos_list(){
	$this->rat->log(uri_string(),1);
	$this->layout->view('reglamentos/list');
}

public function reglamentos_excel(){
	$this->rat->log(uri_string(),1);
	$excelables=array('titulo','file','timestamp');

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$objPHPExcel = new PHPExcel();


		$order_type = 'DESC';
		$order_by = 'reglamentos.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}
		$this->db->order_by($order_by, $order_type);
		if($search != ''){

			$searchables = array("edificios.nombre",
				"reglamentos.titulo",
				"reglamentos.file",
			);


			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}

				$this->db->group_end();
			}
		}

		$this->db->get('reglamentos.edificio_id',$this->edificio_id);

		$rows =$this->reglamentos_model->read()->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function reglamentos_update($primary_key_value){
	$this->rat->log(uri_string(),1);


	$where['reglamentos.id'] = $primary_key_value;
	$where['reglamentos.edificio_id'] = $this->edificio_id;

	if(!empty($_POST)){

		if(isset($_POST['titulo'])){
			$data['titulo'] = $this->input->post('titulo',true);
		}


		$data['edificio_id'] = $this->edificio_id;


		if(isset($_POST['timetamp'])){
			$data['timetamp'] = $this->input->post('timetamp',true);
		}

		if(!empty($_FILES['file']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/reglamentos/';
			$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['file']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'file')){ 
				$data['file'] = $filename;
			}
		}


		if(!$this->reglamentos_model->update($data, $where)){
			redirect('encargado/reglamentos_error');
		}

		redirect('encargado/reglamentos_list');
	}

	$data['values'] = $this->reglamentos_model->read($where)->row();

	$this->layout->view('reglamentos/update', $data);
}

public function reglamentos_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

	$where['id'] = $primary_key_value;
	$where['edificio_id'] = $this->edificio_id;

	if(!$this->reglamentos_model->delete($where)){
		redirect('encargado/reglamentos_error');
	}

	redirect('encargado/reglamentos_list');

}


/******************** fin reglamentos *************************/


/****************************************************** Espacios  ********************************************/
public function espacios_create(){
	$this->rat->log(uri_string(),1);

	//$this->form_validation->set_rules('nombre_espacio', 'Nombre del espacio', 'required');
	$this->form_validation->set_rules('init_hora', 'Hora de apertura', 'required');
	$this->form_validation->set_rules('fin_hora', 'Hora de cerrado', 'required');
	$this->form_validation->set_rules('nombre_espacio', 'Nombre del espacio', 'required');

	if($this->form_validation->run()){

		$data['edificio_id'] = $this->edificio_id;


		if(!empty($_FILES['foto_espacio']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/espacios/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$file = $_FILES['foto_espacio']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'foto_espacio')){ 
				$data['foto_espacio'] = $filename;
			}
		}

		if(!empty($_FILES['reglamento']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/espacios/';
			$config['allowed_types'] = 'txt|pdf|doc|docx|gif|jpg|png|jpeg|rar|zip';
			$file = $_FILES['reglamento']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'reglamento')){ 
				$data['reglamento'] = $filename;
			}
		}

		if(isset($_POST['email_notifica'])){
			$data['email_notifica'] = $this->input->post('email_notifica',true);
		}	

		if(isset($_POST['nombre_espacio'])){
			$data['nombre_espacio'] = $this->input->post('nombre_espacio',true);
		}					

		if(isset($_POST['cancel_dia'])){
			$data['cancel_dia'] = $this->input->post('cancel_dia',true);
		}					

		if(isset($_POST['asoc_espacio_id'])){
			$data['asoc_espacio_id'] = $this->input->post('asoc_espacio_id',true);
		}					

		if(isset($_POST['asoc_from'])){
			$data['asoc_from'] = $this->input->post('asoc_from',true);
		}			

		if(isset($_POST['periodo_permitido'])){
			$data['periodo_permitido'] = $this->input->post('periodo_permitido',true);
		}			

		if(isset($_POST['bloqueado'])){
			$data['bloqueado'] = $this->input->post('bloqueado',true);
		}					

		if(isset($_POST['asoc_to'])){
			$data['asoc_to'] = $this->input->post('asoc_to',true);
		}								

		if(isset($_POST['descripcion'])){
			$data['descripcion'] = $this->input->post('descripcion',true);
		}				

		if(isset($_POST['autorizacion'])){
			$data['autorizacion'] = $this->input->post('autorizacion',true);
		}				

		if(isset($_POST['init_hora'])){
			$data['init_hora'] = $this->input->post('init_hora',true);
		}			

		if(isset($_POST['fin_hora'])){
			$data['fin_hora'] = $this->input->post('fin_hora',true);
		}		

		if(isset($_POST['max'])){
			$data['max'] = $this->input->post('max',true);
		}						

		if(isset($_POST['max_meses'])){
			$data['max_meses'] = $this->input->post('max_meses',true);
		}				
		if(isset($_POST['ilim_permiso_reserva'])){
			$data['ilim_permiso_reserva'] = $this->input->post('ilim_permiso_reserva',true);
		}			

		if(isset($_POST['permiso_reserva'])){
			$data['permiso_reserva'] = $this->input->post('permiso_reserva',true);
		}					
	
		if(isset($_POST['max_invitados'])){
			$data['max_invitados'] = $this->input->post('max_invitados',true);
		}		

		if(isset($_POST['tiempo_espera'])){
			$data['tiempo_espera'] = $this->input->post('tiempo_espera',true);
		}	

		if(isset($_POST['view_reservation'])){
			$data['view_reservation'] = $this->input->post('view_reservation',true);
		}						
				

		if(isset($_POST['day'])){
			$days = $this->input->post('day',true);
			//$str_day = implode("','",$days);
			$str_day = json_encode($days);		
			$data['dias'] = $str_day;
		}

		$espacio_id = $this->espacios_model->create($data);

		if(!$espacio_id){
			redirect('admin/espacios_error');
		}

		if($this->add_turnos($espacio_id)){
			$this->session->set_flashdata('message', "Espacio Cargado con exito" );
		}

			
		if($this->add_periodo($espacio_id)){
			$this->session->set_flashdata('message', "Espacio Cargado con exito" );
		}


		redirect('encargado/espacios_list');

	}


	$this->db->where('espacios.edificio_id',$this->edificio_id);
	$data['espacios']  = $this->espacios_model->read();
	$this->layout->view('espacios/create',$data);
}



public function espacios_update($espacio_id){
	$this->rat->log(uri_string(),1);


	$where['espacios.id'] = $espacio_id;

	$this->form_validation->set_rules('nombre_espacio', 'Nombre del espacio', 'required');
	$this->form_validation->set_rules('init_hora', 'Hora de apertura', 'required');
	$this->form_validation->set_rules('fin_hora', 'Hora de cerrado', 'required');
	$this->form_validation->set_rules('nombre_espacio', 'Nombre del espacio', 'required');

	if($this->form_validation->run()){

		$data['edificio_id'] = $this->edificio_id;


		if(!empty($_FILES['foto_espacio']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/espacios/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$file = $_FILES['foto_espacio']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'foto_espacio')){ 
				$data['foto_espacio'] = $filename;
			}
		}

		if(!empty($_FILES['reglamento']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/espacios/';
			$config['allowed_types'] = 'txt|pdf|doc|docx|gif|jpg|png|jpeg|rar|zip';
			$file = $_FILES['reglamento']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;

			if($this->upload($config,'reglamento')){ 
				$data['reglamento'] = $filename;
			}
		}


		if(isset($_POST['nombre_espacio'])){
			$data['nombre_espacio'] = $this->input->post('nombre_espacio',true);
		}					

		if(isset($_POST['asoc_espacio_id'])){
			$data['asoc_espacio_id'] = $this->input->post('asoc_espacio_id',true);
		}			

		if(isset($_POST['cancel_dia'])){
			$data['cancel_dia'] = $this->input->post('cancel_dia',true);
		}			

		if(isset($_POST['active'])){
			$data['active'] = $this->input->post('active',true);
		}			

		if(isset($_POST['asoc_from'])){
			$data['asoc_from'] = $this->input->post('asoc_from',true);
		}					

		if(isset($_POST['asoc_to'])){
			$data['asoc_to'] = $this->input->post('asoc_to',true);
		}				

		if(isset($_POST['email_notifica'])){
			$data['email_notifica'] = $this->input->post('email_notifica',true);
		}	

		if(isset($_POST['nombre_espacio'])){
			$data['nombre_espacio'] = $this->input->post('nombre_espacio',true);
		}				

		if(isset($_POST['descripcion'])){
			$data['descripcion'] = $this->input->post('descripcion',true);
		}				

		if(isset($_POST['autorizacion'])){
			$data['autorizacion'] = $this->input->post('autorizacion',true);
		}		

		if(isset($_POST['init_hora'])){
			$data['init_hora'] = $this->input->post('init_hora',true);
		}			

		if(isset($_POST['fin_hora'])){
			$data['fin_hora'] = $this->input->post('fin_hora',true);
		}		

		if(isset($_POST['max'])){
			$data['max'] = $this->input->post('max',true);
		}						

		if(isset($_POST['max_meses'])){
			$data['max_meses'] = $this->input->post('max_meses',true);
		}			

		if(isset($_POST['ilim_permiso_reserva'])){
			$data['ilim_permiso_reserva'] = $this->input->post('ilim_permiso_reserva',true);
		}			

		if(isset($_POST['permiso_reserva'])){
			$data['permiso_reserva'] = $this->input->post('permiso_reserva',true);
		}	

		if(isset($_POST['periodo_permitido'])){
			$data['periodo_permitido'] = $this->input->post('periodo_permitido',true);
		}			

		if(isset($_POST['bloqueado'])){
			$data['bloqueado'] = $this->input->post('bloqueado',true);
		}				

		if(isset($_POST['max_invitados'])){
			$data['max_invitados'] = $this->input->post('max_invitados',true);
		}	

		if(isset($_POST['tiempo_espera'])){
			$data['tiempo_espera'] = $this->input->post('tiempo_espera',true);
		}		

		if(isset($_POST['view_reservation'])){
			$data['view_reservation'] = $this->input->post('view_reservation',true);
		}						

	
		$days = $this->input->post('day',true);
		//$str_day = implode("','",$days);
		$str_day = json_encode($days);		
		$data['dias'] = $str_day;
		

		if(!$this->espacios_model->update($data, $where)){
			redirect('encargado/espacios_error');
		}

		if($this->add_turnos($espacio_id)){
			$this->session->set_flashdata('message', "Espacio Cargado con exito" );
		}

			
		if($this->add_periodo($espacio_id)){
			$this->session->set_flashdata('message', "Espacio Cargado con exito" );
		}

		redirect('encargado/espacios_list');

	}
	$data['turnos'] = $this->calendario_model->get_turnos($espacio_id);
	$data['periodos'] = $this->calendario_model->get_periodos($espacio_id);
	$data['values'] = $this->espacios_model->read($where)->row();
	$data['edificios'] = $this->db->get('edificios');
	$this->db->where('espacios.edificio_id',$this->edificio_id);
	$this->db->where('espacios.id !=',$espacio_id);
	$data['espacios']  = $this->espacios_model->read();
	$this->layout->view('espacios/update', $data);
}

public function espacios_load($espacio_id){
	$this->rat->log(uri_string(),1);
	$this->session->set_userdata(array('espacio_id'=>$espacio_id));
	redirect('encargado/espacios_reservar/'.date('Y').'/'.date('m').'/'.$espacio_id);
}

public function espacios_reservar($year = null, $month = null,$espacio_id=null){
	$this->rat->log(uri_string(),1);
	
	if(!$this->espacios_model->reservas_habilitado($this->espacio_id))
		redirect('encargado/espacios_list');

	if(empty($this->espacio_id) && $espacio_id)
		redirect('encargado/espacios_list');

	if(!empty($this->espacio_id))
		$espacio_id = $this->espacio_id;

	if(!$year)
	{
		$year = date('Y');
	}
	if(!$month)
	{
		$month = date('m');
	}

	$this->calendario_model->insert_calendario($month,$year,$espacio_id);

	if($this->uri->segment(3).'/'.$this->uri->segment(4) < date('Y').'/'.date('m'))
	{
		redirect('encargado/espacios_reservar/'.date('Y').'/'.date('m'));
	}

	$where = array('espacios.edificio_id'=>$this->edificio_id,
		'espacios.id'=>$espacio_id);
	$data['values'] = $this->espacios_model->read($where)->row();

	$data['calendario'] = $this->calendario_model->generar_calendario($year, $month,$espacio_id);
	$data['turnos'] = $this->calendario_model->get_turnos($espacio_id);
	$data['periodos'] = $this->calendario_model->get_periodos($espacio_id);
	$data['turno_id'] = 0;
	$data['year']= $year;
	$data['month']= $month;
	$this->layout->view('espacios/espacios_reservar',$data);

}                                                                                       

public function add_turnos($espacio_id){

	$this->rat->log(uri_string(),1);
	/*si no hay periodos no hagao nada por si fue un error*/
	$identificacion = $this->input->post('identificacion',TRUE);
	
	if(!isset($identificacion))
		return FALSE;

	if(intval($espacio_id) > 0 || count($identificacion) > 1){

	//	$this->calendario_model->delete_turno("espacio_id = $espacio_id");	
		
		try {

			for ($i=0 ; $i < count($identificacion) ; $i++ ) { 

				if(!empty($_POST['identificacion'][$i])){

					$data['id'] = (isset($_POST['turno_id'][$i]))? $_POST['turno_id'][$i]:"";
					$data['espacio_id'] = $espacio_id;
					$data['identificacion'] = $_POST['identificacion'][$i];
					$data['turno'] = $_POST['turno'][$i];
					$data['importe'] = $_POST['importe'][$i];
					$data['active'] = TRUE;
					$data['user_id'] = $this->user->id;
					$this->calendario_model->insert_turno($data);
				}

			}
			//die();
			return TRUE;
		} catch (Exception $e) {
		    $this->session->set_flashdata('erro_message', "Error en la carga de turnos" );
		    return FALSE;
		}

	}

}

public function add_periodo($espacio_id){
	$this->rat->log(uri_string(),1);
	
	/*si no hay periodos no hagao nada por si fue un error*/
	$periodos = $this->input->post('desde',TRUE);
	
	if(intval($espacio_id) > 0 || count($periodos) > 1){

		//$this->calendario_model->delete_periodo("espacio_id = $espacio_id");
	  	
	  	try {
	  		$data_periodo = [];
			for ($i=0 ; $i < count($periodos)  ; $i++ ) { 
				if(!empty($_POST['desde'][$i])){
					$data_periodo['id'] = (isset($_POST['periodo_id'][$i]))? $_POST['periodo_id'][$i]:"";
					$data_periodo['espacio_id'] = $espacio_id;
					$data_periodo['desde'] = $_POST['desde'][$i];
					$data_periodo['hasta'] = $_POST['hasta'][$i];
					$data_periodo['importe'] = $_POST['importe_periodo'][$i];
					$data_periodo['name_cant'] = $_POST['name_periodo'][$i];
					$data_periodo['cantidad'] = $_POST['cant_periodo'][$i];
					$data_periodo['user_id'] = $this->user->id;
					$data_periodo['active'] = TRUE;
				}
				$this->calendario_model->insert_periodo($data_periodo);
			}
			return TRUE;
		} catch (Exception $e) {
		    $this->session->set_flashdata('erro_message', "Error en la carga de periodos" );
		    return FALSE;
		}	
	}

}

public function coger_hora(){
	$this->rat->log(uri_string(),1);

	if($this->input->is_ajax_request())
	{
		$dia = $this->input->post('num',true);
		$year = $this->input->post('year',true);
		$month = $this->input->post('month',true);

		$fecha_completa = $year.'-'.$month.'-'.$dia;

		$dia_escogido = $this->input->post('dia_escogido',true);
		$mes_escogido = $this->input->post('mes_escogido',true);
		$espacio_id = $this->input->post('espacio_id',true);
		$turno_id = $this->input->post('turno_id',true);

		if(!$this->calendario_model->check_date($fecha_completa ,$espacio_id)){
			echo json_encode(array("error"=>"El dia se encuentra Cerrado"));
			return FALSE;
		}

		$turno = 0;
		$data_turno = FALSE;
		if($turno_id > 0){
			$data_turno = $this->calendario_model->get_turno($turno_id);
			$turno = $data_turno->turno;
		}else{
			$turno = false;
		}


//insertamos las horas para ese día en la tabla reservas
		$this->calendario_model->insert_horas($year,$month,$dia,$espacio_id);
//obtenemos la información de las horas de ese día

		$info_dia = $this->calendario_model->horas_seleccionadas($year,$month,$dia ,$espacio_id,$turno);
		//echo $this->db->last_query();

		$data = array(
			"year" => $year,
			"dia" => $dia,
			"month" => $month,
			"fecha_completa" => $fecha_completa,
			"dia_escogido" => $dia_escogido, 
			"mes_escogido" => $mes_escogido,
			"espacio_id"=>$espacio_id,
			"info_dia" => $info_dia,
			"turno" => $data_turno
		);

		$where = array('espacios.edificio_id'=>$this->edificio_id,
			'espacios.id'=>$espacio_id);
		$data['values'] = $this->espacios_model->read($where)->row();
		$data['unidades'] = $this->unidades_model->get_unidades_ocupadas($this->edificio_id);
		$data['turnos'] = $this->calendario_model->get_turnos($espacio_id);

		if( $data['turnos']->num_rows()){
			$data['reservas'] = $this->calendario_model->get_reservas_turno($espacio_id,$fecha_completa);
		}else{
			$data['reservas'] = $this->calendario_model->get_reservas_periodo($espacio_id,$fecha_completa);
		}

			//si hay horas disponibles para ese día mostramos 
			//la vista pasando la info en el array data 
		
		if($info_dia !== false)
		{   
			$periodo = $this->calendario_model->get_periodos($espacio_id);
			$turnos = $this->calendario_model->get_turnos($espacio_id);

			if($periodo->num_rows()){
				$data['periodo']= $periodo;
				$this->load->view("encargado/espacios/ajax/get_periodo",$data);
			}
			elseif($turnos->num_rows()){
				$this->load->view("encargado/espacios/ajax/get_turnos",$data);
			}else{
				echo "Error comunicarce con el encargado";
				return false;
			}

		}

	}else{
		die("no hay reserva");
		return false;
		show_404();
	}
}

public function espacios_read(){

	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
	$order_type = 'DESC';
	$order_by = 'espacios.id';
	$search = false;
	if($_POST){
		$limit = $this->input->post('limit',true);
		$order_by = $this->input->post('order_by',true);
		$order_type = $this->input->post('order_type',true);
		$search = $this->input->post('search',true);
	}

	$this->db->order_by($order_by, $order_type);
	if($limit > 0)
		$this->db->limit($limit);
	if($search != ''){

		$searchables =array('edificios.nombre',
			'espacios.nombre_espacio',
			'espacios.descripcion',
			'espacios.max',
			'espacios.max_meses',
			'espacios.init_hora',			
			'espacios.fin_hora',			
			'espacios.active'
		);

		if(isset($searchables) && count($searchables) > 0){
			$first_run = true;
			$this->db->group_start();

			foreach($searchables as $searchable){
				if($first_run){
					$this->db->like($searchable, $search);
					$first_run = false;
				}else{
					$this->db->or_like($searchable, $search);
				}
			}

			$this->db->group_end();
		}
	}

	$this->db->where('espacios.edificio_id',$this->edificio_id);
	/* para que liste todos los los espacios aun cuando estan desactivados*/
	$data['registers']  = $this->espacios_model->read();
	$this->load->view('encargado/espacios/read',$data);
}

public function espacios_list(){
	$this->rat->log(uri_string(),1);
	$this->layout->view('espacios/list');
}

public function espacios_excel(){
	$this->rat->log(uri_string(),1);

	$excelables = array('edificios.nombre',
		'espacios.nombre_espacio',
		'espacios.descripcion',
		'espacios.max',
		'espacios.max_meses',
		'espacios.init_hora',			
		'espacios.fin_hora',			
		'espacios.foto_espacio'
	);

	if(isset($excelables) && count($excelables) > 0){
		$filename = 'report_'.date('Y-m-d').'.xls';
		$objPHPExcel = new PHPExcel();


		$order_type = 'DESC';
		$order_by = 'espacios.id';
		$search = '';
		if($_GET){
			$limit = $this->input->get('limit',true);
			$order_by = $this->input->get('order_by',true);
			$order_type = $this->input->get('order_type',true);
			$search = urldecode($this->input->get('search',true));
			if($limit > 0)
				$this->db->limit($limit);
		}
		$this->db->order_by($order_by, $order_type);
		if($search != ''){

			$searchables =array('edificios.nombre',
				'espacios.nombre_espacio',
				'espacios.descripcion',
				'espacios.max',
				'espacios.max_meses',
				'espacios.init_hora',			
				'espacios.fin_hora',			
				'espacios.foto_espacio',
			);

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}

				$this->db->group_end();
			}
		}
		$this->db->where('espacios.edificio_id',$this->edificio_id);
		$rows =$this->espacios_model->read()->result_array();
		$first = true;
		foreach($excelables as $key => $excelable){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

		}
		foreach ($rows as $row_key => $row){
			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
			}
		}     
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		return true; 
	}
}

public function espacios_delete($primary_key_value){
	$this->rat->log(uri_string(),1);
	$where['id'] = $primary_key_value;
	$where['edificio_id'] = $this->edificio_id;
	if(!$this->espacios_model->delete($where)){
		redirect('encargado/espacios_error');
	}
	redirect('encargado/espacios_list');
}

	public function espacios_active(){
		$this->rat->log(uri_string(),1);

		$data['estado_id'] = intval($this->input->post('estado_id',true));
		$reserva_id = intval($this->input->post('reserva_id',true));
		if($data['estado_id'] == RECHAZADO){

			$reserva = $this->calendario_model->get_reserva($reserva_id);
			$data['error'] = '';
			if($reserva){
				$espacio = $this->espacios_model->read(array('espacios.id'=>$reserva->espacio_id))->row();
	            $datetime_reserva =date("Y-m-d H:i:s",
	                strtotime($reserva->dia_calendario." ".$reserva->hora_reserva));
	            $date_max = date("Y-m-d H:i:s",strtotime($datetime_reserva."+ ".$espacio->cancel_dia." days")); 

				if(strtotime($date_max) >= strtotime(date("Y-m-d H:i:s"))){
					
					$data['email'] = $this->unidades_model->get_email($reserva->unidad_id);
					$data['menssage'] = 'Su reserva <br/> <strong>'.$espacio->nombre_espacio.' </strong></br>El '.date("d/m/Y",strtotime($reserva->dia_calendario)).' '.$reserva->hora_reserva.' - '.$reserva->hora_hasta.'<br/> <h3 style="color:red"> Su reserva fue rechazada por el encargado</h3>'; 
					$data['subject'] = "Reserva rechazada";

					if($this->calendario_model->rechazar_reserva($reserva->reserva_hash,$this->edificio_id,$reserva->unidad_id)){
						$this->informar_email($data);
					}

				}else{

					$data['error'] = "Esta reserva supero el tiempo para poder ser rechazada.";

				}

				echo json_encode($data);
			}

		}else{
			
			$reserva = $this->calendario_model->get_reserva($reserva_id);
			$send_mail['error'] = '';
			if($reserva){

				$espacio = $this->espacios_model->read(array('espacios.id'=>$reserva->espacio_id))->row();
				$send_mail['email'] = $this->unidades_model->get_email($reserva->unidad_id);
				$send_mail['menssage'] = 'Su reserva <br/> <strong>'.$espacio->nombre_espacio.' </strong></br>El '.date("d/m/Y",strtotime($reserva->dia_calendario)).' '.$reserva->hora_reserva.' - '.$reserva->hora_hasta.'<br/> <h3 style="color:red"> Su reserva fue Aprobada por el encargado</h3>'; 
				$send_mail['subject'] = "Reserva Aprobada";

				$this->db->update('reservas',$data,"id=$reserva_id");
				$this->informar_email($send_mail);
				//echo "aca active";
			}
			//echo "aca active";
		}
	}

	public function get_reservas(){

		$this->rat->log(uri_string(),1);
		$espacio_id = $this->input->post('espacio_id',true);
		$year = $this->input->post('year',true);
		$month = $this->input->post('month',true);
		$turnos = intval($this->input->post('turno',true));
		
	/*	if($month == date("m")){
			$this->db->where('reservas.dia_calendario >=',$year.'-'.$month.'-'.date('d'));
		}
		else{
			$this->db->like('reservas.dia_calendario',$year.'-'.$month);
		}*/
		
		$this->db->like('reservas.dia_calendario',$year.'-'.$month,FALSE);
		$this->db->where('espacios.edificio_id',$this->edificio_id);
		$this->db->where('espacios.id',$espacio_id);
		$this->db->order_by('reservas.dia_calendario','asc');
		$data['reservas']  = $this->espacios_model->reservados()->result();
		echo json_encode($data['reservas']);
	}
	

	public function espacios_informes(){
		$this->rat->log(uri_string(),1);
		$data['espacios'] = $this->espacios_model->read(array('edificios.id'=>$this->edificio_id));
		$this->layout->view('espacios/informes',$data);
	}

	public function reservados(){
		$this->rat->log(uri_string(),1);

		$order_type = 'DESC';
		$order_by = 'espacios.id';
		$search = false;

		if($_POST){

			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);
			$limit = $this->input->post('limit',true);
			$espacio_id = $this->input->post('espacio_id',true);
			$fecha_desde = $this->input->post('fecha_desde',true);
			$fecha_hasta = $this->input->post('fecha_hasta',true);

			if(!empty($fecha_desde)){
				$this->db->where('reservas.dia_calendario >=',$fecha_desde);
				$this->db->where('reservas.dia_calendario <=',$fecha_hasta);
			}


			if($espacio_id > 0){
				$this->db->where('reservas.espacio_id',$espacio_id);
			}

		}

		$this->db->order_by($order_by, $order_type);
		$this->db->where('espacios.edificio_id',$this->edificio_id);
		if($limit > 0)
			$this->db->limit($limit);
		$data['registers']  = $this->espacios_model->reservados();
		$data['is_rechasado'] = FALSE;
		$this->espacios_informes_read($data);
	}

	public function rechasados(){
		$this->rat->log(uri_string(),1);
		$order_type = 'DESC';
		$order_by = 'espacios.id';
		$search = false;

		if($_POST){

			$order_by = $this->input->post('order_by',true);
			$limit = $this->input->post('limit',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);

			$espacio_id = $this->input->post('espacio_id',true);
			$fecha_desde = $this->input->post('fecha_desde',true);
			$fecha_hasta = $this->input->post('fecha_hasta',true);

			if(!empty($fecha_desde)){
				$this->db->where('reservas.dia_calendario >=',$fecha_desde);
				$this->db->where('reservas.dia_calendario <=',$fecha_hasta);
			}


			if($espacio_id > 0){
				$this->db->where('reservas.espacio_id',$espacio_id);
			}

		}
		$this->db->order_by($order_by, $order_type);
		$this->db->where('espacios.edificio_id',$this->edificio_id);
		if($limit > 0)
			$this->db->limit($limit);
		$data['registers']  = $this->espacios_model->rechasados();
		$data['is_rechasado'] = true;
		$this->espacios_informes_read($data);
	}

	public function espacios_informes_read($data){
		$this->rat->log(uri_string(),1);
		$this->load->view('encargado/espacios/informes_read', $data);
	}

	public function rechasados_excel(){
		$this->rat->log(uri_string(),1);

		$excelables = array('id','nombre_espacio','date','desde','hasta','unidad','departamento','first_name','last_name','cuando','importe');

		if(isset($excelables) && count($excelables) > 0){
			$filename = 'reservas_'.date('Y-m-d').'.xls';
			$objPHPExcel = new PHPExcel();

			$order_type = 'DESC';
			$order_by = 'reservas.id';
			$search = false;


			if($_GET){

				$order_by = $this->input->get('order_by',true);
				$order_type = $this->input->get('order_type',true);
				$search = $this->input->get('search',true);

				$espacio_id = $this->input->get('espacio_id',true);
				$fecha_desde = $this->input->get('fecha_desde',true);
				$fecha_hasta = $this->input->get('fecha_hasta',true);

				if(!empty($fecha_desde)){
					$this->db->where('reservas_rechazados.dia_calendario >=',$fecha_desde);
					$this->db->where('reservas_rechazados.dia_calendario <=',$fecha_hasta);
				}


				if($espacio_id > 0){
					$this->db->where('reservas.espacio_id',$espacio_id);
				}

			}


			$this->db->order_by($order_by, $order_type);
			$this->db->where('espacios.edificio_id',$this->edificio_id);

			$rows = $this->espacios_model->rechasados()->result_array();
			$first = true;

			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

			}
			foreach ($rows as $row_key => $row){
				foreach($excelables as $key => $excelable){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
				}
			}     
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			header('Cache-Control: max-age=1');
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header ('Cache-Control: cache, must-revalidate'); 
			header ('Pragma: public'); 
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			return true; 

		}

	}

	public function reservados_excel(){
		$this->rat->log(uri_string(),1);

		$excelables = array('id','nombre_espacio','date','desde','hasta','unidad','departamento','first_name','last_name','cuando','importe');

		if(isset($excelables) && count($excelables) > 0){
			$filename = 'reservas_'.date('Y-m-d').'.xls';
			$objPHPExcel = new PHPExcel();

			$order_type = 'DESC';
			$order_by = 'reservas.id';
			$search = false;

			if($_GET){

				$order_by = $this->input->get('order_by',true);
				$order_type = $this->input->get('order_type',true);
				$search = $this->input->get('search',true);

				$espacio_id = $this->input->get('espacio_id',true);
				$fecha_desde = $this->input->get('fecha_desde',true);
				$fecha_hasta = $this->input->get('fecha_hasta',true);

				if(!empty($fecha_desde)){
					$this->db->where('reservas.dia_calendario >=',$fecha_desde);
					$this->db->where('reservas.dia_calendario <=',$fecha_hasta);
				}


				if($espacio_id > 0){
					$this->db->where('reservas.espacio_id',$espacio_id);
				}

			}

			$this->db->order_by($order_by, $order_type);
			$this->db->where('espacios.edificio_id',$this->edificio_id);

			$rows = $this->espacios_model->reservados()->result_array();
			$first = true;

			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

			}
			foreach ($rows as $row_key => $row){
				foreach($excelables as $key => $excelable){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
				}
			}     
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			header('Cache-Control: max-age=1');
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header ('Cache-Control: cache, must-revalidate'); 
			header ('Pragma: public'); 
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			return true; 

		}

	}


	/****************************************************** fin Espacios *****************************************/

	/*************************************** Funciones **********************************/

	private function upload($config,$imput_name)
	{

		$config['overwrite'] = TRUE;

		$this->load->library('upload');
		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload($imput_name))
		{
			echo $this->upload->display_errors();
			die();
		}
		else
		{
			return true;
		}
	}


	public function toAscii($str, $replace=array(), $delimiter='-') {
		$this->rat->log(uri_string(),1);
		return uniqid();
	}

	public function get_name(){
		$this->rat->log(uri_string(),1);

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i > d12; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;

	}

	//hacemos el update de la tabla reservas cuando 
	//el usuario hace submit al form popup del calendario
	public function nueva_reserva($adicional = FALSE){
		$this->rat->log(uri_string(),1);
	//comprobamos que sea una petición ajax

		if($this->input->is_ajax_request()){

			$this->form_validation->set_rules('textarea', 'Comentario', 'trim|xss_clean');
			$this->form_validation->set_rules('hora', 'Hora', 'trim|xss_clean');
			$this->form_validation->set_rules('unidad_id', 'seleccione unidad', 'required|trim|xss_clean');

			if($this->reserva_checked()){	
				return false;
			}


			$desactivar_dia = $this->input->post('desactivar_dia',true);


			$dia = $this->input->post('dia_update',true);
			$hora = $this->input->post('hora',true);
			$espacio_id = $this->input->post('espacio_id',true);
			$comentario = $this->input->post('textarea',true);
			$unidad_id = $this->input->post('unidad_id',true);
			$invitados = $this->input->post('invitados',true);
			$fecha_escogida = $this->input->post('fecha_escogida',true); 


			if(!$this->calendario_model->check_date($fecha_escogida ,$espacio_id)){
				echo "El dia se encuentra Cerrado";
				return true;
			}

			if($desactivar_dia == 1){
				$this->desactivar_dia($espacio_id,$dia);
				echo "Ha cerrado la $fecha_escogida se le informara a todos los propietarios";
				return true;
			}


			$data = array("estado" => "ocupado",
				"unidad_id"=>$unidad_id,
				"comentario_reserva" => $comentario,'user_id'=> $this->user->id);

			if(!$espacio = $this->get_espacio()){
				return false;
			}

			if(isset($_POST['turno_id'])){
				$turno_id = $this->input->post('turno_id',true);
				$rs = $this->calendario_model->get_turno($turno_id);	

				if($rs){
					$turno = $rs->turno;
					$reserva_id = 0;
				}else
				{
					$turno = false;
				}

				$data['turno_id'] = $turno_id;
			}	            

			if(isset($_POST['periodo_id'])){
				$data['periodo_id'] = $this->input->post('periodo_id',TRUE);
				$reserva_id = $this->input->post('reserva_id',true);
				$turno =  false;
			}

	//$data['estado_id'] = ($espacio->autorizacion == true)? PENDIENTE:APROBADO;

	// es el administrador aprueba la reserva de una 
			$data['estado_id'] = APROBADO;
			$data['invitados'] = $invitados;
			

			$nueva_reserva = $this->calendario_model->nueva_reserva($reserva_id,$data,$dia,$hora,$espacio_id,$turno);

			if($nueva_reserva)
			{   
				$data['reserva_hash'] = $nueva_reserva;
				$data['unidad_id'] = $unidad_id;
				$data['hash'] = md5($unidad_id.$nueva_reserva.$this->user->id);
				$data['menssage'] = $fecha_escogida.' a las '.$hora;
				$data['aprobado'] =  "Su reserva fue Aprobada";
				$unidad = $this->db->get_where('unidades',array('id'=>$unidad_id))->row();
				$data['unidad'] = $unidad->name." ".$unidad->departamento;
				$this->notificar_reserva($espacio_id,$data);  
				if($adicional)
					echo "Su reserva del: ".$data['menssage']." fue ingresada \r\n" ;
				else					
					echo "Su reserva del: ".$data['menssage']." fue ingresada" ;
			}else{
				echo "Comuniquese con el Administrador";
			}

		}else{
			show_404();
		}
	}


	private function get_espacio(){
		$data['edificio_id'] = $this->edificio_id;
		$data['id'] = $this->input->post('espacio_id',true);
		$rs = $this->db->get_where('espacios',$data);
		if($rs->num_rows() > 0){
			return $rs->row();
		}else{
			return false;
		}
	}

	public function reserva_checked(){
		$this->rat->log(uri_string(),1);
		$dia = $this->input->post('dia_update',true);
		$hora = $this->input->post('hora',true);
		$espacio_id = $this->input->post('espacio_id',true);
		$estado = 'ocupado';
		$fecha_escogida = $this->input->post('fecha_escogida',true); 
		$reserva_id = intval($this->input->post('reserva_id',true));
		$rs = $this->calendario_model->existe_reserva($dia,$hora,$espacio_id,$estado,$reserva_id);
		if($rs){
			echo $fecha_escogida." ya se ecnuentra reservado";
			return TRUE;
		}
	}	

	public function reservas_list(){
		$this->rat->log(uri_string(),1);
		$data['edificios'] = $this->db->get('edificios');
		$this->layout->view("reservas/list",$data);
	}

	public function load_edificio(){
		$this->rat->log(uri_string(),1);
		if($_POST){
			$id = $this->input->post('id',true);
			$this->session->set_userdata(array('edificio_id'=>$id));
		}
		$data['edificios'] = $this->edificios_model->my_edificios($this->user->id);
		echo $this->load->view('encargado/load_edificio.php',$data,true);	

	}

	private function check_email($email){
		$rs = $this->db->get_where('users',array('email'=>$email));

		if($rs->num_rows() > 0){
			$user = $rs->row();
			$unidades = $this->input->post('unidad_id',true);
			if(count($unidades) > 0 ){
				$this->unidades_model->add_unidad($user->id,$unidades,PROPIETARIO);
				$edificios = array($this->edificio_id);
				$this->edificios_model->add_edificios($user->id,$edificios);
				$this->session->set_flashdata('message', $this->ion_auth->messages());
			}
			return $user->id;
		}else{
			return false;
		}

	}

    private function add_inquilino_existent($email,$unidad_id){
    	$rs = $this->db->get_where('users',array('email'=>$email));
    	
    	if($rs->num_rows() > 0){

    			$user = $rs->row();
				$inquilino['user_id'] = $user->id;

				$inquilino['unidad_id'] = $unidad_id;
				$inquilino['active'] = TRUE;
				$inquilino['grupo_id'] = INQUILINO;

				$this->Inquilino_model->create($inquilino);
			//	echo $this->db->last_query();

				$edificios = array($this->edificio_id);
				$this->edificios_model->add_edificios($user->id,$edificios);


				$group = $this->db->get_where('users_groups',
					array('user_id'=>$user->id,'group_id'=>INQUILINO));
				if($group->num_rows() == 0){
					$this->db->insert('users_groups',
					array('user_id'=>$user->id,'group_id'=>INQUILINO));
				}
				
			//	die('axa');
				return TRUE;
    	
    	}else{
    		//die('axa gfgfg');
    		return false;
    	}

    }

	private function desactivar_dia($espacio_id,$dia){
		$reservas = $this->calendario_model->get_reservas($espacio_id,$dia);
		foreach ($reservas->result() as $value){
			$data['email'] = $value->email;
			$data['menssage'] = "El espacio fue cerrado por el administador el ".$dia;
			$data['subject'] = "Espacio Cerrado";
			$this->informar_email($data);
		}
		$this->calendario_model->desactivar_dia($espacio_id,$dia);
	}

	private function informar_email($data){
		$this->send_email->new_notificar($data['email'],$data);
		return true;
	}


	public function invitacion(){
		$this->rat->log(uri_string(),1);
		$email = $this->input->post('email',true);
		$name = $this->input->post('name',true);
		$consulta_id = intval($this->input->post('consulta_id',true));
		$insert['consulta_id'] = $consulta_id;
		$insert['nombre'] = $name;
		$insert['email'] = $email;
		$insert['hash'] = md5(date('H _ i _ s').$consulta_id.$name.$email);
		$this->db->insert('consultas_asignadas',$insert);
		echo $this->db->insert_id();
	}



	public function users_delete($id){
		$this->rat->log(uri_string(),1);
	//return true;
		$this->db->where('users.id',$id);
		$rs = $this->users_model->my_users($this->edificio_id);
		if($rs->num_rows() > 0){
			$this->db->where(array('user_id'=>$id,'edificio_id'=>$this->edificio_id));
			$this->db->delete('users_edificio');

			$sql = "DELETE users_unidad
			FROM users_unidad
			INNER JOIN unidades 
			ON unidades.id = users_unidad.unidad_id
			WHERE unidades.edificio_id = $this->edificio_id
			and  users_unidad.user_id = $id";

			$this->db->query($sql);		

		}
	}


	public function notificar_reserva($espacio_id,$data){
		$this->rat->log(uri_string(),1);
	//return TRUE;
		$email = $this->unidades_model->get_email($data['unidad_id']);
		$where = array('espacios.id'=>$espacio_id);
		$data['espacio'] = $this->espacios_model->read($where)->row();
		$this->send_email->new_reserva($email,$data);
	}

	public function test(){
		$this->rat->log(uri_string(),1);
		date_default_timezone_get();
		date_default_timezone_set('America/Argentina/Buenos_Aires'); 
		$fecha = new DateTime('NOW');
		echo 'Fecha/hora actual: ', $fecha->format('H:i:s');
	}

	/*************************Seguridad********************************************/

	public function seguridad_create(){
		$this->rat->log(uri_string(),1);

		$this->data['title'] = $this->lang->line('create_user_heading');

		$tables = $this->config->item('tables','ion_auth');
		$identity_column = $this->config->item('identity','ion_auth');
		$this->data['identity_column'] = $identity_column;


		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');

		$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');

		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true)
		{

			$email    = strtolower($this->input->post('email',TRUE));

			$identity = ($identity_column==='email') ? $email : $this->input->post('identity',true);
			$password = $this->input->post('password',true);

			$additional_data = array(
				'first_name' => $this->input->post('first_name',true),
				'last_name'  => $this->input->post('last_name',true),
				'phone'      => $this->input->post('phone',true),
			);

			$groups = array(SEGURIDAD);

		}

		if ($this->form_validation->run() == true && $id = $this->ion_auth->register($identity, $password, $email, $additional_data,$groups))
		{
	// check to see if we are creating the user
	// redirect them back to the admin page
			$edificios = array($this->edificio_id);
			$this->edificios_model->add_edificios($id,$edificios);
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect('encargado/seguridad_list');
		}
		else
		{
	// display the create user form
	// set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['first_name'] = array(
				'name'  => 'first_name',
				'id'    => 'first_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('first_name'),
			);
			$this->data['last_name'] = array(
				'name'  => 'last_name',
				'id'    => 'last_name',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('last_name'),
			);
			$this->data['identity'] = array(
				'name'  => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$this->data['email'] = array(
				'name'  => 'email',
				'id'    => 'email',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('email'),
			);
			$this->data['unidad'] = array(
				'name'  => 'unidad',
				'id'    => 'unidad',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('unidad'),
			);
			$this->data['phone'] = array(
				'name'  => 'phone',
				'id'    => 'phone',
				'type'  => 'text',
				'value' => $this->form_validation->set_value('phone'),
			);
			$this->data['password'] = array(
				'name'  => 'password',
				'id'    => 'password',
				'type'  => 'password',
				'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name'  => 'password_confirm',
				'id'    => 'password_confirm',
				'type'  => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			);

			$this->layout->view('seguridad/create', $this->data);
		}
	}


	public function seguridad_read(){
		$this->rat->log(uri_string(),1);

		$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
		$order_type = 'DESC';
		$order_by = 'users.id';
		$search = false;

		if($_POST){
			$limit = $this->input->post('limit',true);
			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);
		}


		$this->db->order_by($order_by, $order_type);
		if($limit > 0)
			$this->db->limit($limit);

		$searchables = array('users.id',
			'users.first_name',
			'users.last_name',
			'users.email',
			'users.phone',
			'edificios.nombre'
		);

		if($search != ''){

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;

				$this->db->group_start();

				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
				}

				$this->db->group_end();
			}
		}

		//$this->db->where('edificios.id',$this->edificio_id);
		$this->db->where('users_groups.group_id',SEGURIDAD);
		$data['registers']  = $this->users_model->my_security($this->edificio_id);
	//	echo $this->db->last_query();
		$this->load->view('encargado/seguridad/read', $data);
	}

	public function seguridad_list(){
		$this->rat->log(uri_string(),1);
		$this->layout->view('seguridad/list');
	}

	public function seguridad_excel(){
		$this->rat->log(uri_string(),1);

		$excelables = array('id',
			'first_name',
			'last_name',
			'email',
			'phone',
			'edificio',
		);

		if(isset($excelables) && count($excelables) > 0){
			$filename = 'report_'.date('Y-m-d').'.xls';
			$this->load->library('PHPExcel');
			$objPHPExcel = new PHPExcel();


			$order_type = 'DESC';
			$order_by = 'users.id';
			$search = '';

			if($_GET){
				$limit = $this->input->get('limit',true);
				$order_by = $this->input->get('order_by',true);
				$order_type = $this->input->get('order_type',true);
				$search = urldecode($this->input->get('search',true));
				if($limit > 0)
					$this->db->limit($limit);
			}

			$this->db->order_by($order_by, $order_type);

			if($search != ''){

				$searchables = array('users.id',
					'users.first_name',
					'users.last_name',
					'users.email',
					'users.phone',
					'edificios.nombre',
				);

				if(isset($searchables) && count($searchables) > 0){
					$first_run = true;
					$this->db->group_start();
					foreach($searchables as $searchable){
						if($first_run){
							$this->db->like($searchable, $search);
							$first_run = false;
						}else{
							$this->db->or_like($searchable, $search);
						}
					}
					$this->db->group_end();
				}
			}

			$this->db->where('edificios.id',$this->edificio_id);
			$this->db->where('users_groups.group_id',SEGURIDAD);

			$rows  = $this->users_model->my_security($this->edificio_id)->result_array();
			$first = true;

			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);

			}



			foreach ($rows as $row_key => $row){
				foreach($excelables as $key => $excelable){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
				}
			}     
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			header('Cache-Control: max-age=1');
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header ('Cache-Control: cache, must-revalidate'); 
			header ('Pragma: public'); 
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			return true; 
		}
	}

	public function seguridad_update($primary_key_value){
		$this->rat->log(uri_string(),1);

		if(!empty($_POST)){

			if(isset($_POST['edificio_id'])){
				$data['edificio_id'] = $this->edificio_id;
			}

			if(isset($_POST['username'])){
				$data['username'] = $this->input->post('username',true);
			}

			if(isset($_POST['password'])){
				$data['password'] = $this->input->post('password',true);
			}

			if(isset($_POST['email'])){
				$data['email'] = $this->input->post('email',true);
			}

			if(isset($_POST['active'])){
				$data['active'] = $this->input->post('active',true);
			}

			if(isset($_POST['first_name'])){
				$data['first_name'] = $this->input->post('first_name',true);
			}

			if(isset($_POST['last_name'])){
				$data['last_name'] = $this->input->post('last_name',true);
			}

			if(isset($_POST['unidad'])){
				$data['unidad'] = $this->input->post('unidad',true);
			}

			if(isset($_POST['phone'])){
				$data['phone'] = $this->input->post('phone',true);
			}			

			if(isset($_POST['phone'])){
				$data['phone'] = $this->input->post('phone',true);
			}

	//if(!$this->users_model->update($data, $where)){
			if(!$this->ion_auth->update($primary_key_value, $data)){
				redirect('encargado/users_error');
			}

		//	redirect('encargado/seguridad_list');
		}

		$this->db->where('users.id',$primary_key_value);
		$rs = $this->users_model->my_security($this->edificio_id);
		if(!$rs->num_rows()){
			redirect('encargado/seguridad_list');
		}

		$data['values'] = $rs->row();
		$this->layout->view('seguridad/update', $data);
	}

	public function seguridad_delete($user_id){
		$this->rat->log(uri_string(),1);
		if(!$this->users_model->delete_seguridad($user_id)){
			redirect('encargado/users_error');
		}
	}


	public function remove_file(){
		
		$legal_id = $this->input->post('legal_id');
		$file_id = $this->input->post('file_id');
		$this->db->where(array('legal_id'=>$legal_id,'id'=>$file_id));
		$rs = $this->db->delete('file_legales');
		if($rs){
			return TRUE;
		}else{
			return FALSE;
		}

	}

	/**************************Fin de seguridad*************************************/

	public function notificar_respuesta($data){
		$this->rat->log(uri_string(),1);
	//return TRUE;
		$consulta = $this->consultas_model->get($data['consulta_id']);
		$user_email = $this->users_model->get_email($consulta->usaurio_id);

		$email = array($user_email);
		$email_encargadoes = $this->users_model->get_my_adminstrador($this->edificio_id);
		$email = array_merge($email ,$email_encargadoes);
		$this->send_email->new_respuesta($email,$data);
/*
		$fcm_token = $this->users_model->get_push_movile($consulta->usaurio_id);
		if(count($fcm_token) > 0){
			$this->notification->title = "Nueva Consulta cargada";
			$this->notification->body = $consulta->descripcion;
			$this->notification->send_push_movile($fcm_token);
		}*/

	}        


	public function send_recibo($pago_id){
		$this->rat->log(uri_string(),1);
		$data['pago'] = $this->pagos_model->get($pago_id);
		if( $data['pago'] ){
			$email = array($data['pago']->email);
			$email_encargadoes = $this->users_model->get_my_adminstrador($this->edificio_id);
			$email = array_merge($email ,$email_encargadoes);
			$this->send_email->new_recibo($email,$data);
		}
	}        

	public function send_notifica_pago($pago_id){
		$this->rat->log(uri_string(),1);
	//return TRUE;
		$data['pago'] = $this->pagos_model->get($pago_id);
		if( $data['pago'] ){
			$email = array($data['pago']->email);
			$email_encargadoes = $this->users_model->get_my_adminstrador($this->edificio_id);
			$email = array_merge($email ,$email_encargadoes);
			$this->send_email->new_notifica_pago($email,$data);
		}

	}

	public function run__dicponibilidad($edificio_id = false){
		$this->rat->log(uri_string(),1);

		if($edificio_id)
			$where = array('espacios.edificio_id'=>$edificio_id);

		$espacios = $this->espacios_model->read($where);
		foreach ($espacios->result() as $value) {
			if(!$this->check_dicponibilidad($value->id)){
				break;
			}
		}
	}


	public function check_dicponibilidad($espacio_id){
		$this->rat->log(uri_string(),1);
		/*Buscos los dias habilitados para esa planilla */
		$fecha_now = date('Y-m-d');
		$calendar = $this->db->get_where('calendario',array('fecha >='=>$fecha_now, 'espacio_id'=>$espacio_id));
		foreach ($calendar->result() as $value) {

			$dia_calendario = $value->fecha;

			/*Verifico que tenga la planilla echa*/
			$rs = $this->db->get_where('reservas',array('dia_calendario'=>$dia_calendario,
				'espacio_id'=>$espacio_id));

			/*Si tiene al menos una */
			if($rs->num_rows() > 0 ){
				/*si no hay disponible cierro el dia */
				$disponibles = $this->calendario_model->disponibilidad($dia_calendario,$espacio_id);

				if($disponibles == 0){
					$data['estado'] = 'clouse';
					$this->db->where('espacio_id',$espacio_id);
					$this->db->where('fecha', $dia_calendario);
					$this->db->update('calendario', $data);
	//   return TRUE;
				}
			}

		}

	}

	public function nuevo_producto(){
		$this->rat->log(uri_string(),1);
		$this->layout->view('nuevo_producto');
	}

	public function registerWebToken()
	{
		$userId = $this->user->id;
		$FCMToken = $this->input->post('token');

		echo json_encode($this->users_model->registerWebToken($userId,$FCMToken));

	}

	public function espacios_invitados($reserva_id){
		$this->rat->log(uri_string(),1);
		$where['reservas.id'] = $reserva_id;
		$where['espacios.edificio_id'] = $this->edificio_id;
		$this->db->select('espacios.nombre_espacio as nombre,
			reservas.dia_calendario as dia,
			reservas.hora_reserva as desde,
			reservas.hora_hasta as hasta,
			reservas.id as reserva_id,
			');
		$this->db->join('espacios','espacios.id = reservas.espacio_id');
		$rs = $this->db->get_where('reservas',$where);
		if($rs->num_rows() > 0 ){
			$data['espacio'] = $rs->row();
			$this->layout->view('invitados/list',$data);
		}
		
	}

	public function list_invitados(){
		$reserva_id = $this->input->post('reserva_id',true);
		$tipo_invitado_id = $this->input->post('tipo',true);
		$this->db->join('espacios','espacios.id = reservas.espacio_id');
		$rs = $this->db->get_where('reservas',array('reservas.id'=>$reserva_id,
            'espacios.edificio_id'=>$this->edificio_id));

		if($rs->num_rows() > 0){
			$where = array('reserva_id'=>$reserva_id,'tipo_invitado_id'=>$tipo_invitado_id);
			$invitados = $this->Invitados_model->read($where);
			echo json_encode($invitados->result());
		}
	}


	function send_registros($hash_registro){
		$rs = $this->db->get_where('registeruser',array('hash_registro'=>$hash_registro));
		if($rs->num_rows() > 0){
			foreach ($rs->result() as $value){
				$data['link'] = base_url('auth/register_propietario/'.$value->hash);
				$email = $value->email;
				$this->send_email->new_register($email,$data);
			}

		}
	}


		public function my_user(){
			$this->rat->log(uri_string(),1);

	    	$id = $this->user->id;
	    	$this->data['title'] = $this->lang->line('edit_user_heading');
	    	$user = $this->ion_auth->user($id)->row();
	    	$groups=$this->ion_auth->groups()->result_array();
	    	$currentGroups = $this->ion_auth->get_users_groups($id)->result();

	    	$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required');
	    	$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required');
	    	$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required');

	    	if (isset($_POST) && !empty($_POST))
	    	{

	    		if ($this->input->post('password'))
	    		{
	    			$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
	    			$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
	    		}

	    		if ($this->form_validation->run() === TRUE)
	    		{
	    			$data = array(
	    				'first_name' => $this->input->post('first_name',true),
	    				'last_name'  => $this->input->post('last_name',true),
	    				'company'    => $this->input->post('company',true),
	    				'phone'      => $this->input->post('phone',TRUE),
	    				'email_fw'      => $this->input->post('email_fw',true),
	    			);

	    			if(!empty($_FILES['photo']['name'])){

	    				$config['upload_path'] = BASEPATH.'../upload/profile/';
	    				$config['allowed_types'] = 'webp|gif|jpg|png|jpeg';
	    				$file = $_FILES['photo']['name'];
	    				$file_data = pathinfo($file);
	    				$name_file = $this->toAscii($file_data['filename']);
	    				$filename =  $name_file.'.'.$file_data['extension'];
	    				$config['file_name'] = $filename;
	    				
	    				if($this->upload($config,'photo')){ 
	    					$data['photo'] = $filename;
	    				}

	    			}

	    			if ($this->input->post('password'))
	    			{
	    				$data['password'] = $this->input->post('password',TRUE);
	    			}


	    		   if($this->ion_auth->update($user->id, $data))
	    		    {

	    			    $this->session->set_flashdata('message', "Su perfil fue actualizado con exito" );
	    			    redirect('encargado');

	    		    }
	    		    else
	    		    {
	    			    $this->session->set_flashdata('message', $this->ion_auth->errors() );
	    		    }

	    		}
	    	}

	    	// set the flash data error message if there is one
	    	$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

	    	// pass the user to the view
	    	$this->data['user'] = $user;
	    	$this->data['groups'] = $groups;
	    	$this->data['currentGroups'] = $currentGroups;

	    	$this->data['first_name'] = array(
	    		'name'  => 'first_name',
	    		'id'    => 'first_name',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('first_name', $user->first_name),
	    	);

	    	$this->data['last_name'] = array(
	    		'name'  => 'last_name',
	    		'id'    => 'last_name',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('last_name', $user->last_name),
	    	);
	    	$this->data['email'] = array(
	    		'name'  => 'email',
	    		'disabled'  => 'disabled',
	    		'id'    => 'email',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('email', $user->email),
	    	);

	    	$this->data['phone'] = array(
	    		'name'  => 'phone',
	    		'id'    => 'phone',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('phone', $user->phone),
	    	);			    	

	    	$this->data['email_fw'] = array(
	    		'name'  => 'email_fw',
	    		'id'    => 'email_fw',
	    		'type'  => 'text',
	    		'value' => $this->form_validation->set_value('email_fw', $user->email_fw),
	    	);

	    	$this->data['password'] = array(
	    		'name' => 'password',
	    		'id'   => 'password',
	    		'type' => 'password'
	    	);

	    	$this->data['password_confirm'] = array(
	    		'name' => 'password_confirm',
	    		'id'   => 'password_confirm',
	    		'type' => 'password'
	    	);
	     	$this->data['photo'] = $user->photo;
	     	$this->data['unidades'] = $user->phone;

	    	$this->layout->view('usuarios/update',$this->data);
		}


		public function check_persmisos(){
			
			$espacio_id = $this->input->post('espacio_id');
			$dia_calendario = $this->input->post('dia_update');
			$unidad_id = $this->input->post('unidad_id');
			$espacio_id = $this->input->post('espacio_id');

			$check_persmisos = $this->calendario_model->permisos($espacio_id,
				$dia_calendario,$unidad_id);
			
			echo $check_persmisos;

		}

		public function check_permitidos(){
			$reserva_id = $this->input->post('reserva_id');
			$cant = $this->calendario_model->get_number_periodo($reserva_id);
			echo intval($cant);
		}
	 
	 	public function reservation(){
	 		$invitados =intval($this->input->post('adicionales',TRUE));
	 		$reserva_id = $this->input->post('reserva_id');
	 		//die($invitados);
	 		for ($i=0; $i < $invitados; $i++) { 
	 			$rs = $this->calendario_model->get_disponibles($reserva_id);
	 			if($rs){
	 				$reserva = $rs->row();
	 				$_POST['reserva_id'] = $reserva->id;
	 				$this->nueva_reserva(TRUE);	
	 			}else{
	 				 echo "El Horario se encuentra ocupado";
	 			}

	 		}

	 	}
}