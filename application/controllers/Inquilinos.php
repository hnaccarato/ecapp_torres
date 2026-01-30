<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

defined('BASEPATH') OR exit('No direct script access allowed');

class Inquilinos extends CI_Controller {

	public $user;
	public $edificio_id;
	public $unidad_id;
	public $espacio_id;


	public function __construct(){
	//	$this->rat->log(uri_string(),1);
		parent::__construct();
		$this->layout->setFolder('inquilinos');
		$this->layout->setLayout('inquilinos/layout');
		$this->load->model('Invitados_model');
		$this->load->model('calendario_model');
		$this->load->model('pagos_model');
		$this->load->model('Inquilino_model');
		$this->load->model('Mercadopago_model');
		$this->load->helper(array('url','language'));
		$this->load->library(array('ion_auth','form_validation'));
		$this->lang->load('auth');
		$this->user = $this->ion_auth->user()->row();
 		$this->load->model('Baned_model');
		if (!$this->ion_auth->in_group(INQUILINO)){
			redirect('accseslog');
		}

		$this->session->set_userdata(array('controller'=>INQUILINO));
		$unidad_id = $this->session->userdata('unidad_id');
		
		$espacio_id = $this->session->userdata('espacio_id');
		if(!empty($espacio_id)){
			$this->espacio_id = $espacio_id;
		}
		
		if(!empty($unidad_id)){
			$rs = $this->edificios_model->my_edificio_by_unidad($unidad_id);
			if($rs){

				$this->edificio_id = $rs->edificio_id;
				$this->unidad_id = $unidad_id;
				$this->layout->setUnidad($unidad_id);


			}else{

				$this->session->set_userdata(array('url'=>'inquilinos'));
				redirect('auth/load_unidades');

			}
			
		}else{
			$this->session->set_userdata(array('url'=>'inquilinos'));
			redirect('auth/load_unidades');
		}
		$this->send_email->set_edificio_id($this->edificio_id);
				//comentar en caso de falla
		$this->my_style->check_company_host(APP_URL,$this->edificio_id );
	}

	public function index(){
		$this->rat->log(uri_string(),1);
		$this->load->library('googlemaps');
		$where['edificios.id'] = $this->edificio_id;
		$data['edificio'] = $this->edificios_model->read($where)->row();
		$data['user'] = $this->user;
		$data['unidad'] = $this->db->get_where('unidades',array('id'=>$this->unidad_id))->row();
		/*Fin google Maps*/
		$date_ini = date("Y-m-d");
		//sumo 1 año
		$date_fin = date("Y-m-d",strtotime($date_ini."+ 1 year"));

		$this->event_model->set_building($this->edificio_id);
		$this->event_model->set_user_id($this->user->id);
		$this->event_model->set_unity($this->unidad_id);
		
		$data['jason_date'] = $this->event_model->get_event($date_ini,$date_fin);
		$this->layout->view('index/index',$data);
	}


	/*Muestros la formas de pago en un item aparta*/
	public function formas_pagos(){
		$this->rat->log(uri_string(),1);
		$where['edificios.id'] = $this->edificio_id;
		$data['edificio'] = $this->edificios_model->read($where)->row();
		$this->layout->view('pagos/formas_pagos',$data);
	}

	/***********************************Inicio de Expensas*******************************************/

	public function expensas_list($recibo_id = false){
		$this->rat->log(uri_string(),1);
		$data['tipo_gastos'] = $this->db->get('tipo_gastos',TRUE);
		$this->layout->view('recibos/list',$data);
		

	}

	public function expensas_read(){
	$this->rat->log(uri_string(),1);

		$limit = (isset($_POST['limit']))? $this->input->post('limit',TRUE) : 50;
		$order_type = 'DESC';
		$order_by = 'recibos.id';
		$search = false;

		if($_POST){
			$limit = $this->input->post('limit',true);
			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);
			$recibo_id = $this->input->post('recibo_id',TRUE);
			$edificio_id = $this->input->post('edificio_id',TRUE);
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

		$this->db->where('recibos.estado_id',ENVIADO);
		$this->db->where('edificios.id',$this->edificio_id);
		$data['registers']  = $this->recibos_model->read();
		//echo $this->db->last_query();
		$this->load->view('inquilinos/recibos/read', $data);
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
				$search = urldecode($this->input->get('search',TRUE));
				
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
			$this->db->where('recibos.estado_id',ENVIADO);
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

	public function expensas_view($primary_key_value){
		$this->rat->log(uri_string(),1);
		$where['recibos.id'] = $primary_key_value;
		$this->db->where('recibos.edificio_id',$this->edificio_id);
		$this->db->where('recibos.estado_id',ENVIADO);
		$rs = $this->recibos_model->read($where);
		if(!$rs->num_rows()){
			redirect('inquilinos/expensas_list');
		}
		$data['values'] = $rs->row();

		$this->db->select('tipo_gastos.name as tipo, gastos.*');
		$this->db->join('tipo_gastos','gastos.tipo_gasto_id = tipo_gastos.id');
		$this->db->order_by('gastos.tipo_gasto_id');
		$data['gastos'] = $this->db->get_where('gastos',array('gastos.recibo_id'=>$primary_key_value));
		$this->layout->view('recibos/view', $data);

	}


	public function expensas_pagos_list(){
		$this->rat->log(uri_string(),1);
		$user_id = $this->user->id;
		$edificio_id = $this->edificio_id;
		$this->db->where('recibos.estado_id',ENVIADO);
		$data['recibos'] =  $this->pagos_model->pending($this->unidad_id,$edificio_id );
		$this->db->where_in('estados.id',array(9,11,10,1,3));
		$data['estados'] = $this->db->get('estados')->result();
		$this->layout->view('pagos/list',$data);
	}


	public function pagos_read(){
		$this->rat->log(uri_string(),1);
		$limit = (isset($_POST['limit']))? $this->input->post('limit',TRUE) : 50;
		$order_type = 'DESC';
		$order_by = 'pagos_users.id';
		$search = false;
		if($_POST){
			$limit = $this->input->post('limit',true);
			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);
			$estado_id = $this->input->post('estado_id',TRUE);
		}
		$this->db->order_by($order_by, $order_type);
		
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
		if(intval($estado_id) > 0){
			$this->db->where('pagos_users.estado_id',$estado_id);	
		}

		$this->db->where_in('pagos_users.estado_id',array(9,11,10,1,3));
		$this->db->where('pagos_users.unidad_id',$this->unidad_id);

		$data['registers']  = $this->pagos_model->read();
		$this->load->view('inquilinos/pagos/read', $data);
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
			$filename = 'Mis_pagos_'.date('Y-m-d').'.xls';
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
				$estado_id = $this->input->get('estado_id',TRUE);
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

			$this->db->where_in('pagos_users.estado_id',array(9,11,10,1,3));
			$this->db->where('pagos_users.unidad_id',$this->unidad_id);
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

	public function nuevos_pagos(){
		$this->rat->log(uri_string(),1);

		if ($_POST){
			$recibos = $this->input->post('recibos');
			
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
				}

			}

			$data['descripcion'] = $this->input->post('descripcion',TRUE);
			$data['user_id'] = $this->user->id;
			$data['unidad_id'] = $this->unidad_id;
			$data['estado_id'] = PENDIENTE;
			$data['active'] = TRUE;
			$data['importe'] = $this->input->post('importe',TRUE);
			$data['fecha'] = date("Y-m-d");
			
			foreach ($recibos as $value) {
				$data['recibo_id'] = $value;
				//$this->pagos_model->create($data);
				$pago_id = $this->pagos_model->create($data);
				$this->send_pago($pago_id);
			}

		
			redirect('inquilinos/expensas_pagos_list');
		}
		
	}

	public function pagar(){
		$this->rat->log(uri_string(),1);

		$this->Mercadopago_model->set_edificio($this->edificio_id);
		$congif = $this->Mercadopago_model->get_mp();
		if(empty($congif[TOKEN_MP]))
			redirect('inquilinos/expensas_list');
		
		$data['porcentaje'] = $congif['porcentaje'];

		$this->form_validation->set_rules('recibos[]',">Expensas Pendientes" ,'required');
		$this->form_validation->set_rules('importe[]',"importe" ,'required|numeric');

		if ($this->form_validation->run() == true){

			
			$token_mp = $congif[TOKEN_MP];
			MercadoPago\SDK::setAccessToken($token_mp);
			$preference = new MercadoPago\Preference();

			$payer = new MercadoPago\Payer();
			$payer->name = $this->user->first_name;
			$payer->surname = $this->user->last_name;
			$payer->email = $this->user->email;
			$payer->date_created = date("y-m-d H:i:s");
			
			$ventas = array();
			$recibos = $this->input->post('recibos');
			$importe = $this->input->post('importe');
			$divisor = count($recibos);		


			foreach ($importe as $key =>$value) {	
				$item = new MercadoPago\Item();
				$expensa_id = intval($recibos[$key]);
				$item->title = $this->recibos_model->get_row($expensa_id)->titulo;
				$item->quantity = 1;
				$subtotal = ( (real) $value * (real) $congif['porcentaje'])/100;
				$total = (real) $subtotal + (real) $value;
				$item->unit_price = (real) $total;
				$ventas[] = $item;
			
			}

		
			$preference->items = $ventas;
			$preference->payer = $payer;
			$preference->sponsor_id = (int) SPONSOR_ID;
			$preference->payment_methods = array(
			  "excluded_payment_types" => array(
			    array("id" => "ticket"),
			    array("id" => "atm")
			  ),
			  "installments" => 12
			);


			$preference->back_urls = array(
			  "success" => base_url('auth/success/'),
			  "failure" => base_url('auth/failure/'),
			  "pending" => base_url('auth/pending/'),
			);
			
			$preference->external_reference = "Pagos de expensa";
			$preference->auto_return = "approved";
			$preference->binary_mode = true;
		 	//$payment->description = "junio y Julio";	 	
			$preference->save();

			$insert['user_id'] = $this->user->id;
			$insert['unidad_id'] = $this->unidad_id;
			$insert['estado_id'] = PENDIENTE;
			$insert['fecha'] = date("Y-m-d");

			foreach ($recibos as $key =>$value) {
			
				$insert['recibo_id'] = $value;
				$subtotal= ($importe[$key] * $congif['porcentaje'])/100;
				$total = $subtotal + $importe[$key];
				$insert['importe'] = (real) $total;
				$insert['mp_preference_id'] = $preference->id;
				$insert['active'] = FALSE;
				$pagos_id = $this->pagos_model->create($insert);

			}

			header("Location:". $preference->init_point);
			
			die();		

		}else{

			$user_id = $this->user->id;
			$edificio_id = $this->edificio_id;
			$this->db->where('recibos.estado_id',ENVIADO);
			$data['recibos'] =  $this->pagos_model->last_pending($this->unidad_id,$edificio_id );
			$this->layout->view('pagos/pago',$data);

		}

	}

	
	public function porcentaje($cantidad,$porciento){
		$subtotal= ( (real)$cantidad *  (real)$porciento)/100;
		echo $total =  (real)$subtotal +  (real)$cantidad;
	}

	public function load_liquidacion(){
		$this->rat->log(uri_string(),1);

		$expensa_id = $this->input->post('expensa_id');
		
	//	$where['recibos.id'] = $expensa_id;
		$where['recibos.edificio_id'] = $this->edificio_id;

		$this->db->select('recibos.id,recibos.prorrateo as file,recibos.titulo');
		$this->db->join('edificios', 'recibos.edificio_id = edificios.id');
		$this->db->join('users', 'recibos.usuarios_id = users.id');

		$this->db->where_in('recibos.id',$expensa_id);
		$rs = $this->db->get_where('recibos',$where);
		if(!$rs->num_rows()){
			redirect('inquilinos/expensas_list');
		}

		$data  = $rs->result();
		echo json_encode($data);
	}
	public function progree(){

		if($_GET){
			if(isset($_GET['preference_id'])){
				$preference_id = $_GET['preference_id'];
				if(isset($_GET['merchant_order_id'])){
					$data['mp_merchant_order_id']= $_GET['merchant_order_id'];
					$data['active'] = TRUE;
					$this->pagos_model->update($data,array('mp_preference_id'=>$preference_id));
					//echo $this->db->last_query();
				}
			}
		}
	redirect('inquilinos/expensas_pagos_list');
	}

	public function view_pagos(){
		$this->rat->log(uri_string(),1);
		$id = intval($this->input->post('id',TRUE));
		$data['expensa'] = $this->pagos_model->get($id);
		$data['estados'] = $this->db->get('estados')->result();
		echo $this->load->view('inquilinos/pagos/view',$data,TRUE);
	}

	public function set_pago(){
	$this->rat->log(uri_string(),1);

		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])){ 
			$pago_id = $this->input->post('pago_id',TRUE);

			if(!empty($_FILES['comprobante']['name'])){

				$config['upload_path'] = BASEPATH.'../upload/comprobante/';
				$config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
				$file = $_FILES['comprobante']['name'];
				$file_data = pathinfo($file);
				$name_file = $this->toAscii($file_data['filename']);
				$filename =  $name_file.'.'.$file_data['extension'];
				$config['file_name'] = $filename;
				
				if($this->upload($config,'comprobante')){ 
					$update['file'] = $filename;
					$update['estado_id'] = PENDIENTE;
					$update['importe'] = $this->input->post('importe',TRUE);
				//	$update['active'] = true;
					$rs = $this->pagos_model->update($update,array('id'=>$pago_id));

					if($rs){
						$this->send_pago($pago_id);
					}
				}

			}


		}

	}


/**************************************Consultas***************************************************/

	public function consultas_list(){
		$this->rat->log(uri_string(),1);
		$this->layout->view('consultas/list');
	}

	public function consultas_create(){
		$this->rat->log(uri_string(),1);

		if(!empty($_POST)){

			$data['edificio_id'] = $this->edificio_id;
			$data['usaurio_id'] = $this->user->id;
			$data['quien_id'] = $this->user->id;
			$data['tipo_consultas_id'] = $this->input->post('tipo_consulta_id');

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
				$data['descripcion'] = $this->input->post('descripcion');
			}
	
			$data['estado_id'] = ACTIVO;
			
			if(isset($_POST['detalle'])){
				$data['detalle'] = $this->input->post('detalle');
			}

			if(!$this->consultas_model->create($data)){
				redirect('inquilinos/consultas_error');
			}
			$this->notificar_consulta($data);

			redirect('inquilinos/consultas_list');
		}

		$data['tipo_consultas'] = $this->tipo_consultas_model->read();
		$this->layout->view('consultas/create',$data);
	}


	public function consultas_read(){
		$this->rat->log(uri_string(),1);
		$limit = (isset($_POST['limit']))? $this->input->post('limit',TRUE) : 50;
		$order_type = 'DESC';
		$order_by = 'consultas.id';
		$search = false;
		if($_POST){
			$limit = $this->input->post('limit',true);
			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);
		}
		$this->load->model('consultas_model');
		$this->db->order_by($order_by, $order_type);
		
		if($limit > 0)
			$this->db->limit($limit);

		if($search != ''){

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

		$this->db->where('consultas.usaurio_id',$this->user->id);
		$data['registers']  = $this->consultas_model->read();
	//	echo $this->db->last_query();
		$this->load->view('inquilinos/consultas/read', $data);
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
				$estado_id = $this->input->get('estado_id',TRUE);
				
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


			$this->db->where('consultas.usaurio_id',$this->user->id);
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
		
		if($data['consulta']->user_id != $this->user->id)
			redirect('inquilinos/consultas_list');

		$data['respuestas'] = $this->consultas_model->get_respuesta($consulta_id);
		$this->layout->view('consultas/view',$data);
	
	}



	public function consultas_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

		$this->load->model('consultas_model');

		$where['id'] = $primary_key_value;
		
		if(!$this->consultas_model->delete($where)){
			redirect('inquilinos/consultas_error');
		}

		redirect('inquilinos/consultas_list');

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

			$data['consulta_id'] = $this->input->post('consulta_id',TRUE);
			$data['fecha'] = date('Y-m-d');
			$data['user_id'] = $this->user->id;
			
			if(isset($_POST['respuesta'])){
				$data['respuesta'] = $this->input->post('respuesta',TRUE);
			}

			$this->db->insert('respuesta_consultas',$data);
			$this->consultas_model->update(
				array('estado_id'=>ACTIVO),array('id'=>$data['consulta_id']));
			redirect('inquilinos/consultas_list');
		}

	}


/************************************fin Consultas**************************************************/


/*********************************** Circulares *************************************************/

	public function circular_read(){
	$this->rat->log(uri_string(),1);

		$limit = (isset($_POST['limit']))? $this->input->post('limit',TRUE) : 50;
		$order_type = 'DESC';
		$order_by = 'circular.id';
		$search = false;
		$estado_id = false;
		if($_POST){
			$limit = $this->input->post('limit',true);
			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);
			$estado_id = $this->input->post('estado_id');
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
				"circular.estado_id",
				"estados.nombre");


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
		//$this->db->where('circular.estado_id',ENVIADO);
		//para que liste todos los estados 
		$this->db->where('circular.estado_id >',TRUE);
		$this->db->where('circular.edificio_id',$this->edificio_id);
		$data['registers']  = $this->circular_model->read();
		$this->load->view('inquilinos/circular/read', $data);
	}

	public function circular_list(){
		$this->rat->log(uri_string(),1);
		$this->layout->view('circular/list');
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
				$estado_id = $this->input->get('estado_id',TRUE);
				$search = urldecode($this->input->get('search',TRUE));
				
				if($limit > 0)
					$this->db->limit($limit);

				
				if($estado_id > 0){
					$this->db->where('circular.estado_id',$estado_id);
				}

			}

			$this->db->order_by($order_by, $order_type);

			if($search != ''){
				$searchables = array("circular.edificio_id",
					"circular.titulo",
					"circular.fecha",
					"circular.fecha_envio",
					"circular.detalle",
					"estados.nombre");

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

				if($estado_id > 0){
					$this->db->where('circular.estado_id ',$estado_id);
				}

			}

			//$this->db->where('circular.estado_id',ENVIADO);
			$this->db->where('circular.estado_id >',TRUE);
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

	public function view_circular($circular_id){
		$this->rat->log(uri_string(),1);
		$this->load_circular($circular_id);
		$data['circular']  = $this->circular_model->get($circular_id);
		if($data['circular'])
			$this->layout->view('circular/view', $data);
		else
			redirect('inquilinos/circular_list');

	}

	private function load_circular($circular_id){
		$data['circular_id'] = $circular_id;
		$data['user_id'] = $this->user->id;
		$data['unidad_id'] = $this->unidad_id;
		$rs = $this->db->get_where('view_circular',$data);
		if($rs->num_rows() == 0){
			$this->db->insert('view_circular',$data);
		}

	}



/*********************************** Fin de Circulares *****************************************/
/*********************************** asamblea *************************************************/

	public function asamblea_read(){
	$this->rat->log(uri_string(),1);

		$limit = (isset($_POST['limit']))? $this->input->post('limit',TRUE) : 50;
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

		$this->db->where(array(
			'asambleas.edificio_id'=>$this->edificio_id,
			'asambleas.estado_id'=>ACTIVO));
		$data['registers']  = $this->asambleas_model->read();
		$this->load->view('inquilinos/asamblea/read', $data);
	}

	public function asamblea_list(){
		$this->rat->log(uri_string(),1);
		$this->layout->view('asamblea/list');
	}

	public function asamblea_excel(){
		$this->rat->log(uri_string(),1);
		$excelables = array("edificio_id",
				"titulo",
				"fecha",
				"fecha_envio",
				"detalle",
				"estado_id");

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
				$search = urldecode($this->input->get('search'));
				
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
			
			$this->db->where(array(
				'asambleas.edificio_id'=>$this->edificio_id,
				'asambleas.estado_id'=>ACTIVO));
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

	function view_asamblea($asamblea_id){
		$data['asamblea']  = $this->asambleas_model->get($asamblea_id);
		$this->layout->view('asamblea/view', $data);
	}


/*********************************** Fin de Asambleas *****************************************/

    
    public function upload($config,$imput_name)    	
    {
    	$this->rat->log(uri_string(),1);

    	if (!file_exists($config['upload_path'])) {
    	    mkdir($config['upload_path'], 777, true);
    	}

    	$config['overwrite'] = TRUE;

    	$this->load->library('upload', $config);

    	if ( ! $this->upload->do_upload($imput_name))
    	{
    		echo $this->upload->display_errors();
    		die();
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
/*
    public function toAscii($str, $replace=array(), $delimiter='-') {
	$this->rat->log(uri_string(),1);

    	$str = strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');

    	setlocale(LC_ALL, 'en_US.UTF8');

    	if( !empty($replace) ) {
    		$str = str_replace((array)$replace, ' ', $str);
    	}

    	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    	$clean = strtolower(trim($clean, '-'));
    	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

    	return $clean;

    }
	*/
    public function toAscii($str, $replace=array(), $delimiter='-') {
    	$this->rat->log(uri_string(),1);
    	return uniqid();
    }
    // create a new user
	

    private function check_email($email){
    	$rs = $this->db->get_where('users',array('email'=>$email));
    	
    	if($rs->num_rows() > 0){
    			$user = $rs->row();
    			$unidades = $this->input->post('unidad_id');
    			foreach ($unidades as $key => $value) {
					$inquilino['user_id'] = $user->id;
					$inquilino['propietario_id'] = $this->user->id;
					$inquilino['edificio_id'] = $this->edificio_id;
					$inquilino['unidad_id'] = $value;
					$this->Inquilino_model->create($inquilino);
					$group = $this->db->get_where('users_groups',
						array('user_id'=>$user->id,'group_id'=>INQUILINO));
					if($group->num_rows() == 0){
						$this->db->insert('users_groups',
						array('user_id'=>$user->id,'group_id'=>INQUILINO));
					}

					return TRUE;
    			}
    	}else{
    		return false;
    	}

    }

    /********************************Encargado*******************************************************/
    public function encargado_list(){
    	$this->rat->log(uri_string(),1);
    	$this->layout->view('encargado/list');
    }

    public function encargado_read(){
    	$this->rat->log(uri_string(),1);
    	$limit = (isset($_POST['limit']))? $this->input->post('limit',TRUE) : 50;
    	$order_type = 'DESC';
    	$order_by = 'encargado.id';
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
    //	echo $this->db->last_query();
    	$this->load->view('inquilinos/encargado/read', $data);
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
    			$search = urldecode($this->input->get('search'));
    			
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

    public function encargado_view($encargado_id){
	$this->rat->log(uri_string(),1);

    	$where['encargado.id'] = $encargado_id;
    	$where['encargado.edificio_id'] = $this->edificio_id;
    	$rs = $this->encargado_model->read($where);
    	
    	if(!$rs->num_rows())
    		redirect('inquilinos/encargado_list');

    	$data['values']  = $rs->row();
    	$this->layout->view('encargado/view', $data);
    }

    /******************************** Fin Encargado*******************************************************/
    /******************************** Seguros*******************************************************/

    public function seguros_read(){
    	$this->rat->log(uri_string(),1);
    	$limit = (isset($_POST['limit']))? $this->input->post('limit',TRUE) : 50;
    	$order_type = 'DESC';
    	$order_by = 'seguros.id';
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
				"seguros.titulo",
				"seguros.file",
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
    	$this->db->where('seguros.edificio_id',$this->edificio_id);
    	$data['registers']  = $this->seguros_model->read();
    	$this->load->view('inquilinos/seguros/read', $data);
    }

    public function seguros_list(){
    	$this->rat->log(uri_string(),1);
    	$this->layout->view('seguros/list');
    }

    public function seguros_excel(){
    	$this->rat->log(uri_string(),1);
    	$excelables=array('titulo','file','timestamp');

    	if(isset($excelables) && count($excelables) > 0){
    		$filename = 'report_'.date('Y-m-d').'.xls';
    		$objPHPExcel = new PHPExcel();

    		
    		$order_type = 'DESC';
    		$order_by = 'seguros.id';
    		$search = '';
    		if($_GET){
    			$limit = $this->input->get('limit',true);
    			$order_by = $this->input->get('order_by',true);
    			$order_type = $this->input->get('order_type',true);
    			$search = urldecode($this->input->get('search'));
    			
    			if($limit > 0)
    				$this->db->limit($limit);

    		}
    		$this->db->order_by($order_by, $order_type);
    		if($search != ''){

				$searchables = array("edificios.nombre",
					"seguros.titulo",
					"seguros.file",
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

    		$this->db->get('seguros.edificio_id',$this->edificio_id);
    		$rows =$this->seguros_model->read()->result_array();
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

    /******************************** fin seguros*******************************************************/
    /******************************** Reservas *******************************************************/

    public function espacios_load($espacio_id){
    	$this->rat->log(uri_string(),1);
    	$this->session->set_userdata(array('espacio_id'=>$espacio_id));
    	redirect('inquilinos/espacios_reservar/'.date('Y').'/'.date('m').'/'.$espacio_id);
    }
	public function espacios_reservar($year = null, $month = null,$espacio_id=null){
		$this->rat->log(uri_string(),1);


		if($this->Baned_model->is_baned($this->user->id,$espacio_id))
			redirect('inquilinos/baned');

		if(empty($this->espacio_id) && $espacio_id)
			redirect('inquilinos/espacios_list');
		
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
			redirect('inquilinos/espacios_reservar/'.date('Y').'/'.date('m'));
		}

		$where = array('espacios.edificio_id'=>$this->edificio_id,
			'espacios.id'=>$espacio_id);
	    $data['calendario'] = $this->calendario_model->generar_calendario($year, $month,$espacio_id);

	    $data['turnos'] = $this->calendario_model->get_turnos($espacio_id);

	    if( $data['turnos']->num_rows()){
	    	$data['reservas'] = $this->calendario_model->get_reservas_turno($espacio_id);
	    }else{
	    	$data['reservas'] = $this->calendario_model->get_reservas_periodo($espacio_id);
	    }

	    $data['periodos'] = $this->calendario_model->get_periodos($espacio_id);
	   	$data['values'] = $this->espacios_model->read($where)->row();
	   	$data['turno_id'] = 0;
	   	$data['year']= $year;
	   	$data['month']= $month;
		$this->layout->view('espacios/espacios_reservar',$data);
	}



	public function espacios_active(){
		
		$this->rat->log(uri_string(),1);

		$reserva_id = intval($this->input->post('reserva_id'));;
		$reserva = $this->calendario_model->get_reserva($reserva_id);
		$data['error'] = '';
		if($reserva){

			if($reserva->unidad_id == $this->unidad_id && $reserva->user_id == $this->user->id){

				$hora_reserva = strtotime($reserva->dia_calendario.''.$reserva->hora_reserva);
				$my_hora =  strtotime(date("Y-m-d H:i:s",(strtotime ("+1 Hours")))); 
				if($hora_reserva >= $my_hora ){
					
					$this->calendario_model->rechazar_reserva($reserva->reserva_hash,$this->edificio_id,$this->unidad_id);
					//echo 1;
				
				}else{
					$data['error'] = "Esta reserva supero el tiempo para poder ser rechazada.";
				}

			}else{

				$data['error'] = "Secuencia no permitida";

			}

			echo json_encode($data);	
		}
		
	}

    public function coger_hora(){

		$this->rat->log(uri_string(),1);
        if($this->input->is_ajax_request())
        {
            $dia = $this->input->post('num');
            $year = $this->input->post('year');
            $month = $this->input->post('month');
            
            $fecha_completa = $year.'-'.$month.'-'.$dia;
            
            $dia_escogido = $this->input->post('dia_escogido');
            $mes_escogido = $this->input->post('mes_escogido');
            $espacio_id = $this->input->post('espacio_id');
            $turno_id = $this->input->post('turno_id');

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
                	$this->load->view("inquilinos/espacios/ajax/get_periodo",$data);
            	}
                elseif($turnos->num_rows()){
                	$this->load->view("inquilinos/espacios/ajax/get_turnos",$data);
                }else{
                	echo "Error de sistema póngase en contacto con el administrador";
                	return false;
                }
                
            }
        }else{
             die("no hay reserva");
            show_404();
        }
    }

		       
	public function espacios_read(){
	$this->rat->log(uri_string(),1);

		$limit = (isset($_POST['limit']))? $this->input->post('limit',TRUE) : 50;
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
		
		/*
		if($limit > 0)
			$this->db->limit($limit);
		*/
		if($search != ''){
			$searchables = array('edificios.nombre','nombre_espacio','descripcion','importe','periodo','init_hora','fin_hora','Cant_horas',);
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
		$data['registers']  = $this->espacios_model->read();
		$this->load->view('inquilinos/espacios/read', $data);
	}

	public function espacios_list(){
		$this->rat->log(uri_string(),1);
		$data['edificios'] = $this->db->get('edificios');
		$this->layout->view('espacios/list',$data);
	}

	public function espacios_excel(){
		$this->rat->log(uri_string(),1);
		$excelables = array('edificios.nombre','nombre_espacio','descripcion','importe','periodo','init_hora','fin_hora','Cant_horas',);
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
				$search = urldecode($this->input->get('search'));
				
				if($limit > 0)
					$this->db->limit($limit);

			}
			$this->db->order_by($order_by, $order_type);
			if($search != ''){
				$searchables = array('edificios.nombre','nombre_espacio','descripcion','importe','periodo','init_hora','fin_hora','Cant_horas',);
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

	public function nueva_reserva($adicional = FALSE)		
	{
		$this->rat->log(uri_string(),1);
		//comprobamos que sea una petición ajax

		if($this->input->is_ajax_request()){

			$this->form_validation->set_rules('textarea', 'Comentario', 'trim|xss_clean');
			$this->form_validation->set_rules('hora', 'Hora', 'trim|xss_clean');
			$this->form_validation->set_rules('unidad_id', 'seleccione unidad', 'required|trim|xss_clean');

			if($this->reserva_checked()){	
				return false;
			}


			$dia = $this->input->post('dia_update',TRUE);
			$hora = $this->input->post('hora',TRUE);
			$espacio_id = $this->input->post('espacio_id',TRUE);
			$unidad_id = $this->unidad_id;
			$fecha_escogida = $this->input->post('fecha_escogida',TRUE); 

			if(!$this->calendario_model->check_date($fecha_escogida ,$espacio_id)){
				echo "El dia se encuentra Cerrado";
				return true;
			}

			$data = array("estado" => "ocupado",
            "unidad_id"=>$unidad_id,'user_id'=> $this->user->id);

			if(!$espacio = $this->get_espacio()){
				return false;
			}

            if(isset($_POST['turno_id'])){
          		$turno_id = $this->input->post('turno_id',TRUE);
            	$rs = $this->calendario_model->get_turno($turno_id);	
            	
            	if($rs){
					$turno = $rs->turno;
					$reserva_id = 0;
            	}else
            	{
            		$turno = false;
            	}
            	
            	$data['turno_id'] = $_POST['turno_id'];
            }	            

            if(isset($_POST['periodo_id'])){
            	$data['periodo_id'] = $_POST['periodo_id'];
            	$reserva_id = $this->input->post('reserva_id',true);
            	$turno =  false;
            }

            $data['estado_id'] = ($espacio->autorizacion == true)? PENDIENTE:APROBADO;

			$nueva_reserva = $this->calendario_model->nueva_reserva($reserva_id,$data,$dia,$hora,$espacio_id,$turno);
			
			if($nueva_reserva)
			{   
				$data['unidad_id'] = $unidad_id;
				$data['hash'] = md5($unidad_id.$nueva_reserva.$this->user->id);
				$data['menssage'] = $fecha_escogida.' a las '.date("G:i",strtotime($hora));
				$data['aprobado'] = ($espacio->autorizacion == true)? "Su reserva esta pendiente de aprobación":"Su reserva fue Aprobada";
				$unidad = $this->db->get_where('unidades',array('id'=>$unidad_id))->row();
				$data['unidad'] = $unidad->name." ".$unidad->departamento; 
				$text = "Su reserva del: ".$data['menssage'] ;
				echo  ($espacio->autorizacion == true)? $text." está pendiente aprobación":$text." está aprobada";
				$this->notificar_reserva($espacio_id,$data); 
			}else{
				echo "Comuniquese con el Administrador";
			}

		}else{
			show_404();
		}
	}
	
	/*
	public function reserva_checked(){
		
		$this->rat->log(uri_string(),1);
		$dia = $this->input->post('dia_update',TRUE);
		$hora = $this->input->post('hora',TRUE);
		$espacio_id = $this->input->post('espacio_id',TRUE);
		$estado = 'ocupado';
		$fecha_escogida = $this->input->post('fecha_escogida',TRUE); 
		$rs = $this->calendario_model->existe_reserva($dia,$hora,$espacio_id,$estado);
		if($rs){
			echo $fecha_escogida." ya se ecnuentra reservado";
			return TRUE;
		}
	}*/

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



	public function get_reservas(){
		$this->rat->log(uri_string(),1);

		$espacio_id = $this->input->post('espacio_id',true);
		$year = $this->input->post('year',true);
		$month = $this->input->post('month',true);
		$turnos = intval($this->input->post('turno',TRUE));

		if($month == date("m")){
			$this->db->where('reservas.dia_calendario >=',$year.'-'.$month.'-'.date('d'));
		}
		else{
			$this->db->like('reservas.dia_calendario',$year.'-'.$month);
		}
		
		$this->db->where('espacios.edificio_id',$this->edificio_id);
		$this->db->where('espacios.id',$espacio_id);
		$this->db->order_by('reservas.dia_calendario','asc');
		$rs  = $this->espacios_model->reservados()->result();
		$data = array();
		foreach ($rs as $key => $value) {
			$data[$key]['id'] = $value->id;
			$data[$key]['estado'] = $value->estado;
			$data[$key]['unidad'] = $value->unidad;
			$data[$key]['user_id'] = $value->user_id;
			$data[$key]['date'] = date('d/m/Y',strtotime($value->date));
			$data[$key]['desde'] = $value->desde;
			$data[$key]['hasta'] = $value->hasta;
		}

		echo json_encode($data);
	}

	public function mis_reservas(){
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

			$espacio_id = $this->input->post('espacio_id',TRUE);
			$fecha_desde = $this->input->post('fecha_desde',TRUE);
			$fecha_hasta = $this->input->post('fecha_hasta',TRUE);
			
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
		$this->db->where('reservas.unidad_id',$this->unidad_id);
		$this->db->where('reservas.user_id',$this->user->id);

		$data['registers']  = $this->espacios_model->reservados();
	//	echo $this->db->last_query();
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
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);

			$espacio_id = $this->input->post('espacio_id',TRUE);
			$fecha_desde = $this->input->post('fecha_desde',TRUE);
			$fecha_hasta = $this->input->post('fecha_hasta',TRUE);
			
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
		$this->db->where('reservas_rechazados.unidad_id',$this->unidad_id);
		$this->db->where('reservas.user_id',$this->user->id);
		
		$data['registers']  = $this->espacios_model->rechasados();
		$data['is_rechasado'] = TRUE;
		$this->espacios_informes_read($data);
	}

	public function espacios_informes_read($data){
		$this->rat->log(uri_string(),1);
		$this->load->view('inquilinos/espacios/informes_read', $data);
	}


	public function reservados_excel(){
		
		$this->rat->log(uri_string(),1);
		$excelables = array('id','nombre_espacio','date','desde','hasta','unidad','first_name','last_name','cuando','importe');

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

				$espacio_id = $this->input->get('espacio_id',TRUE);
				$fecha_desde = $this->input->get('fecha_desde',TRUE);
				$fecha_hasta = $this->input->get('fecha_hasta');
				
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
			$this->db->where('reservas.unidad_id',$this->unidad_id);
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
    			    redirect('inquilinos');

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

    /******************************** fin reservas*******************************************************/
 



    /********************************Invitados************************************************/
    		public function espacios_invitados($reserva_id){
    			$this->rat->log(uri_string(),1);
    			$where['reservas.id'] = $reserva_id;
    			$where['reservas.unidad_id'] = $this->unidad_id;
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
    				$this->layout->view('invitados/create',$data);
    			}
    			
    		}

    		public function add_invitados(){
    			$reserva_id = $this->input->post('reserva_id',true);
    			$rs = $this->db->get_where('reservas',array('reservas.id'=>$reserva_id,
    	            'reservas.unidad_id'=>$this->unidad_id));
    			if($rs->num_rows() > 0){
					$data = array(
						'nombre' => $this->input->post('nombre',true),
						'reserva_id' => $this->input->post('reserva_id',true),
						'dni' => $this->input->post('dni',true),
						'email' =>  $this->input->post('email',true),
						'patente' =>  $this->input->post('patente',true),
						'tipo_invitado_id' => $this->input->post('tipo',true),
						'trabajo' => $this->input->post('trabajo',true),
						'user_id' => $this->user->id,
						'phone' =>  $this->input->post('phone',true),
						'company' =>  $this->input->post('company',true)
					);

    				if(!empty($_FILES['art']['name'])){

    					$config['upload_path'] = BASEPATH.'../upload/art/';
    					$config['allowed_types'] = 'webp|gif|jpg|png|jpeg|doc';
    					$file = $_FILES['art']['name'];
    					$file_data = pathinfo($file);
    					$name_file = $this->toAscii($file_data['filename']);
    					$filename =  $name_file.'.'.$file_data['extension'];
    					$config['file_name'] = $filename;
    					
    					if($this->upload($config,'art')){ 
    						$data['art'] = $filename;
    					}

    				}
    				
    				$invitados_id = $this->input->post('invitado_id',TRUE);
    				
    				if($invitados_id > 0){
    					$this->Invitados_model->update($invitados_id,$data);
    				}else{
    					$invitado_id = $this->Invitados_model->create($data);
    				}

    				
    				echo $invitado_id;
    			}
    			return FALSE;
    		}

    		public function list_invitados(){
    			$reserva_id = $this->input->post('reserva_id',true);
    			$tipo_invitado_id = $this->input->post('tipo',true);
    			
    			$rs = $this->db->get_where('reservas',array('reservas.id'=>$reserva_id,
    	            'reservas.unidad_id'=>$this->unidad_id));

    			if($rs->num_rows() > 0){
    				$where = array('reserva_id'=>$reserva_id,'tipo_invitado_id'=>$tipo_invitado_id);
    				$invitados = $this->Invitados_model->read($where);
    				echo json_encode($invitados->result());
    			}
    		}

    		public function get_invitado(){
    			$id = $this->input->post('id');
    			$where= array('id'=>$id,'user_id'=>$this->user->id);
    			$rs = $this->Invitados_model->get($where);
    			if($rs){
    				echo  json_encode($rs);
    			}else{
    				echo 0;
    			}
    		}

    		public function delete_invitados($id){
    			//$id = $this->input->post('id');
    			$where= array('id'=>$id,'user_id'=>$this->user->id);
    			$rs = $this->Invitados_model->delete($where);
    			if($rs){
    				echo 1;
    			}else{
    				echo 0;
    			}
    		}


    /********************************Fin Invitados************************************************/
    
    private function get_espacio(){
    	$data['edificio_id'] = $this->edificio_id;
    	$data['id'] = $this->input->post('espacio_id',TRUE);
    	$rs = $this->db->get_where('espacios',$data);
    	if($rs->num_rows() > 0){
    		return $rs->row();
    	}else{
    		return false;
    	}
    }

    public function notificar_reserva($espacio_id,$data){
    	$this->rat->log(uri_string(),1);
    	//return TRUE;
    	$email_propietarios = $this->unidades_model->get_email($data['unidad_id'],$this->user->id);
    	$email_administradores = $this->users_model->get_my_adminstrador($this->edificio_id);
    	$email = array_merge($email_propietarios,$email_administradores);
    	$where = array('espacios.id'=>$espacio_id);
    	$data['espacio'] = $this->espacios_model->read($where)->row();
    	$this->send_email->new_reserva($email,$data);
    }

    public function notificar_consulta($data){
    	$this->rat->log(uri_string(),1);
    	//return TRUE;
    	$user_email = $this->users_model->get_email($data['usaurio_id'],$this->user->id);
    	$email = array($user_email);
    	$email_administradores = $this->users_model->get_my_adminstrador($this->edificio_id);
    	$email = array_merge($email ,$email_administradores);
    	$this->send_email->new_consulta($email,$data);
    }

    public function send_pago($pago_id){
    	$this->rat->log(uri_string(),1);
    	//return TRUE;
    	$data['pago'] = $this->pagos_model->get($pago_id);
    	if( $data['pago'] ){
    		$email = array($data['pago']->email);
    		$email_administradores = $this->users_model->get_my_adminstrador($this->edificio_id);
    		$email = array_merge($email ,$email_administradores);
    		$this->send_email->new_pago_comprobante($email,$data);
    	}
    	
    }

    public function nuevo_producto(){
    	$this->rat->log(uri_string(),1);
    	$this->layout->view('nuevo_producto');
    }
    public function legales_list($legal_id){
    	$this->rat->log(uri_string(),1);
    	$this->session->set_userdata('legal_id',$legal_id);
    	if(!$rs = $this->Legales_model->get_tipo()){
    		redirect('administrador');
    	}else{

    		$data['title'] = $rs->name;
    		$data['legal_id'] = $legal_id;
    		$this->layout->view('legales/list',$data);
    	}
    }

    public function load_file($legal_id){

    	$rs = $this->Legales_model->my_files($legal_id);
     
        if(!$rs)
        	return FALSE;

        $data['files'] = $rs;
        echo  $this->load->view('inquilinos/legales/load_files',$data,true);

    }

    public function legales_read(){
    	$this->rat->log(uri_string(),1);
    	
    	if($this->session->has_userdata('legal_id')){
    		$legal_id = $this->session->userdata('legal_id');
    	}else{
    		redirect('inquilinos/legales_list/'.SEGUROS);
    	}

    	$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
    	$order_type = 'DESC';
    	$order_by = 'legales.id';
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
    	$this->load->view('inquilinos/legales/read', $data);
    }

    public function legales_excel(){
    	$this->rat->log(uri_string(),1);

    	if($this->session->has_userdate('legal_id')){
    		$legal_id = $this->session->userdata('legal_id');
    	}else{
    		redirect('inquilinos/legales_list/'.SEGUROS);
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
    		redirect('inquilinos/legales_error');
    	}
    	redirect('inquilinos/legales_list');

    }

    public function get_tyc(){
    	$data['espacio'] = $this->espacios_model->get($this->espacio_id);
    	$data['user'] = $this->user;
    	$data['unidad'] = $this->unidades_model->my_unidad($this->user->id,$this->unidad_id);
    	$filedescargar = $data['espacio']->nombre_espacio.".docx";
    	 header("Cache-Control: ");// leave blank to avoid IE errors
    	 header("Pragma: ");// leave blank to avoid IE errors
    	 header("Content-type: application/octet-stream");
    	 header("content-disposition: attachment;filename=".$data['espacio']->nombre_espacio.".doc");

    	echo $this->load->view('declaracion',$data,TRUE);

    }
    
    public function check_permitidos(){
    	$reserva_id = $this->input->post('reserva_id');
    	$cant = $this->calendario_model->get_number_periodo($reserva_id,TRUE);
    	echo $cant;
    }

     
 	public function reservation(){
 		$invitados = intval($this->input->post('adicionales',TRUE));
 		$reserva_id = $this->input->post('reserva_id');
		if(!$invitados){
			$rs = $this->calendario_model->get_disponibles($reserva_id);
            if(!$rs)
                echo "El Horario se encuentra ocupado";
			$reserva = $rs->row();
			$_POST['reserva_id'] = $reserva->id;
			$this->nueva_reserva(TRUE);	
		}else{
			for ($i=0; $i < $invitados; $i++) { 

				$rs = $this->calendario_model->get_disponibles($reserva_id);
                if(!$rs)
                echo "El Horario se encuentra ocupado";

				$reserva = $rs->row();
				$_POST['reserva_id'] = $reserva->id;
				$this->nueva_reserva(TRUE);	
			}
		}

 	}

 	public function baned(){
 		$this->layout->view('banned/index');
 	}

}

