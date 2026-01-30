<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Propietarios extends CI_Controller {

	public $user;
	public $edificio_id;
	public $unidad_id;
	public $espacio_id;

	public function __construct(){

		parent::__construct();
		$this->layout->setFolder('propietarios');
		$this->layout->setLayout('propietarios/layout');
		$this->load->model('calendario_model');
		$this->load->model('pagos_model');
		$this->load->model('event_model');
		$this->load->model('Invitados_model');
		$this->load->model('Inquilino_model');
		$this->load->model('Mercadopago_model');
		$this->load->helper(array('url','language'));
		$this->load->library(array('ion_auth','form_validation'));
		$this->lang->load('auth');
		$this->user = $this->ion_auth->user()->row();
		$this->load->model('Baned_model');

		if (!$this->ion_auth->in_group(PROPIETARIO)){
			redirect('accseslog');
		}

		$this->session->set_userdata(array('controller'=>PROPIETARIO));
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
				$this->session->set_userdata(array('url'=>'propietarios'));
				redirect('auth/load_unidades');
			}
			
		}else{
			$this->session->set_userdata(array('url'=>'propietarios'));
			redirect('auth/load_unidades');
		}
	//	$this->my_style->load_company($this->edificio_id);
	//	$this->send_email->set_edificio_id($this->edificio_id);

				//comentar en caso de falla
	//	$this->my_style->check_company_host(APP_URL,$this->edificio_id );
	}


	public function index(){
		$this->rat->log(uri_string(),1);
		$this->load->library('googlemaps');
		$where['edificios.id'] = $this->edificio_id;
		$data['edificio'] = $this->edificios_model->read($where)->row();
		$data['user'] = $this->user;
		$data['unidad'] = $this->db->get_where('unidades',array('id'=>$this->unidad_id))->row();
	
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

		$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 20;
		$order_type = 'DESC';
		$order_by = 'recibos.id';
		$search = false;

		if($_POST){
			$limit = $this->input->post('limit',true);
			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);
			$recibo_id = intval($this->input->post('recibo_id',TRUE));
			$edificio_id = intval($this->input->post('edificio_id',TRUE));
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
		$this->load->view('propietarios/recibos/read', $data);
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
			redirect('propietarios/expensas_list');
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


	public function set_comprobante(){
		$this->rat->log(uri_string(),1);

		$edificio_id = $this->edificio_id;
		$this->db->where('recibos.estado_id',ENVIADO);
		$data['recibos'] =  $this->pagos_model->pending($this->unidad_id,$edificio_id);
		echo $this->load->view('propietarios/pagos/ajax/set_comprobante',$data,TRUE);

	}

	public function pagos_read(){
		$this->rat->log(uri_string(),1);
		$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
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
		$this->load->view('propietarios/pagos/read', $data);
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
				$search = $this->db->escape_str($this->input->get('search',true));
				$estado_id = intval($this->input->get('estado_id',TRUE));
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
				$pago_id = $this->pagos_model->create($data);
				$this->send_pago($pago_id);
			}
			
					
		}
		
		redirect('propietarios/expensas_pagos_list');
	
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
			redirect('propietarios/expensas_list');
		}

		$data  = $rs->result();
		echo json_encode($data);
	}

	public function pagar(){
        $this->rat->log(uri_string(),1);

        $this->Mercadopago_model->set_edificio($this->edificio_id);
        $unidad = $this->unidades_model->my_unidad($this->user->id,$this->unidad_id);


        $congif = $this->Mercadopago_model->get_mp();

        if(empty($congif[TOKEN_MP]))
            redirect('propietarios/expensas_list');
        
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
                $item->description = " Pago de ".$item->title;
                $item->quantity = 1;
                $subtotal = ( (real) $value * (real) $congif['porcentaje'])/100;
                $total = (real) $subtotal + (real) $value;
                $item->unit_price = (real) $total;
                $ventas[] = $item;
            
            }

            $external_reference = "Unidad :".$unidad->name." 
            Departamento :".$unidad->departamento." 
            Edificio :".$unidad->edificio;
            
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
            
            $preference->external_reference = $external_reference;
            $preference->auto_return = "approved";
            $preference->binary_mode = true;
            
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

	public function subir_comprobante(){
		$this->rat->log(uri_string(),1);

		if(!empty($_FILES['comprobante']['name'])){

			$config['upload_path'] = BASEPATH.'../upload/comprobante/';
			$config['allowed_types'] = 'xlsx|docx|pdf|gif|jpg|png|jpeg';
			$file = $_FILES['comprobante']['name'];
			$file_data = pathinfo($file);
			$name_file = $this->toAscii($file_data['filename']);
			$filename =  $name_file.'.'.$file_data['extension'];
			$config['file_name'] = $filename;
			
			if($this->upload($config,'comprobante')){ 
				$data['file'] = $filename;
			}

		}


		$data['recibo_id'] = intval($this->input->post('recibo_id',TRUE));
		$data['descripcion'] = $this->input->post('descripcion',TRUE);
		$data['user_id'] = $this->user->id;
		$data['fecha'] = date("Y-m-d");
		$this->pagos_model->create($data);
		redirect('propietarios/expensas_pagos_list');
	}

	public function view_pagos(){
	$this->rat->log(uri_string(),1);

		$id = $this->input->post('id');
		$data['expensa'] = $this->pagos_model->get($id);
		$this->db->where_in('estados.id',array(9,11,10,1,3));
		$data['estados'] = $this->db->get('estados')->result();
		echo $this->load->view('propietarios/pagos/view',$data,TRUE);
	}

	public function set_pago(){
	$this->rat->log(uri_string(),1);

		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])){ 
			$pago_id = $this->input->post('pago_id');

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
				$data['descripcion'] = $this->input->post('descripcion',TRUE);
			}
	
			$data['estado_id'] = ACTIVO;
			
			if(isset($_POST['detalle'])){
				$data['detalle'] = $this->input->post('detalle',TRUE);
			}

			if(!$this->consultas_model->create($data)){
				redirect('propietarios/consultas_error');
			}

			$this->notificar_consulta($data);

			redirect('propietarios/consultas_list');
		}

		$data['tipo_consultas'] = $this->tipo_consultas_model->read();
		$this->layout->view('consultas/create',$data);
	}


	public function consultas_read(){
	$this->rat->log(uri_string(),1);

		$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
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
		$this->load->view('propietarios/consultas/read', $data);
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
				$search = $this->db->escape_str($this->input->get('search',true));
				$estado_id = intval($this->input->get('estado_id',TRUE));
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
			redirect('propietarios/consultas_list');

		$data['respuestas'] = $this->consultas_model->get_respuesta($consulta_id);
		$this->layout->view('consultas/view',$data);
	}



	public function consultas_delete($primary_key_value){
	$this->rat->log(uri_string(),1);

		$this->load->model('consultas_model');

		$where['id'] = $primary_key_value;
		
		if(!$this->consultas_model->delete($where)){
			redirect('propietarios/consultas_error');
		}

		redirect('propietarios/consultas_list');

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
			$this->notificar_respuesta($data);
			$this->consultas_model->update(
				array('estado_id'=>ACTIVO),array('id'=>$data['consulta_id']));
			redirect('propietarios/consultas_list');
		}

	}


/************************************fin Consultas**************************************************/


/*************************************** inquilinos **********************************************/
    public function inquilinos(){
    	$this->rat->log(uri_string(),1);

    	$this->layout->view('inquilinos/list');
    }

    public function inquilinos_read(){
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
		$this->db->where('users_unidad.unidad_id',$this->unidad_id);
		$data['registers']  = $this->Inquilino_model->read();
		//echo $this->db->last_query();
		$this->load->view('propietarios/inquilinos/read', $data);
		
	}


		public function inquilinos_create(){

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
				$this->users_model->delete_inquilino_byunidad();
	            if($this->check_email($email))
	            	redirect('propietarios/inquilinos');


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
	            $inquilino['unidad_id'] = $this->unidad_id;
	            $inquilino['grupo_id'] = INQUILINO;
				$this->Inquilino_model->create($inquilino);
				$edificios = array($this->edificio_id);
				$this->edificios_model->add_edificios($id,$edificios);
	            $this->session->set_flashdata('message', $this->ion_auth->messages());
	            $this->ion_auth->set_password($email);
	           // $this->send_email->new_welcome(array($email),$this->edificio_id);
	           
	           	redirect('propietarios/inquilinos');
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

	            $this->layout->view('inquilinos/create', $this->data);
	        }

	    }

	    public function inquilino_excel(){

			$this->rat->log(uri_string(),1);

	    	if(isset($excelables) && count($excelables) > 0){
	    		$filename = 'report_'.date('Y-m-d').'.xls';
	    		$this->load->library('PHPExcel');
	    		$objPHPExcel = new PHPExcel();

	    		$this->load->model('users_model');
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
	    		$rows =$this->users_model->read()->result_array();
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
	    		    	/*	
		              	$unidades = $this->input->post('unidad_id');
		              	$this->db->delete('inquilinos',array('user_id'=>$id));
		              	foreach ($unidades as $key => $value) {
			              	$inquilino['user_id'] = $id;
			                $inquilino['propietario_id'] = $this->user->id;
			                $inquilino['edificio_id'] = $this->edificio_id;
			                $inquilino['unidad_id'] = $value;
			                $this->Inquilino_model->create($inquilino);
		              	}
						*/

	    			    $this->session->set_flashdata('message', $this->ion_auth->messages() );
	    			    redirect('propietarios/inquilinos');


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
		      /*	$this->data['unidades'] = $this->unidades_model->my_disponibles($this->user->id);
			       	$this->data['asignadas'] = $this->unidades_model->my_asignadas($id);*/
		    	$this->layout->view('inquilinos/update', $this->data);
		    }


	    public function inquilino_delete($user_id){
			$this->rat->log(uri_string(),1);
	    	$this->load->model('users_model');
	    	$rs = $this->db->get_where('users_unidad',array('unidad_id'=>$this->unidad_id,'user_id'=>$user_id));
	    	if($rs->num_rows()){
	    		$where['id'] = $user_id;
	    		if(!$this->users_model->delete_inquilino($user_id)){
	    			//echo $this->db->last_query();
	    			redirect('propietarios/users_error');
	    		}
	    		redirect('propietarios/inquilinos');
	    	}else{
	    		redirect('propietarios/inquilinos');
	    	}


	    }

/************************************************ fin Inquilinos ************************************/	    

/*********************************** Circulares *************************************************/

	public function circular_read(){
	$this->rat->log(uri_string(),1);

		$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
		$order_type = 'DESC';
		$order_by = 'circular.id';
		$search = false;
		$estado_id = false;
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
		$this->db->where('circular.estado_id >',TRUE);
		$this->db->where('circular.edificio_id',$this->edificio_id);
		$data['registers']  = $this->circular_model->read();
		$this->load->view('propietarios/circular/read', $data);
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
				$estado_id = intval($this->input->get('estado_id',TRUE));
				$search = urldecode($this->input->get('search',true));
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
		$this->db->where('circular.edificio_id',$this->edificio_id);
		$data['circular']  = $this->circular_model->get($circular_id);
		if($data['circular'])
			$this->layout->view('circular/view', $data);
		else
			redirect('propietarios/circular_list');

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

		$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
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
		$this->load->view('propietarios/asamblea/read', $data);
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

    
    public function upload($config,$imput_name){
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


    
    public function toAscii($str, $replace=array(), $delimiter='-'){
    	$this->rat->log(uri_string(),1);
    	return uniqid();
    }

    // create a new user
	

    private function check_email($email){
    	$rs = $this->db->get_where('users',array('email'=>$email));
    	
    	if($rs->num_rows() > 0){
    			$user = $rs->row();
    			//$unidades = $this->input->post('unidad_id');
    		//	$unidad = $this->input->post('unidad_id');
    			//foreach ($unidades as $key => $value) {
					$inquilino['user_id'] = $user->id;
					//$inquilino['propietario_id'] = $this->user->id;
				//	$inquilino['edificio_id'] = $this->edificio_id;
					$inquilino['unidad_id'] = $this->unidad_id;
					$inquilino['active'] = TRUE;
					$inquilino['grupo_id'] = INQUILINO;
					$this->Inquilino_model->create($inquilino);

					$edificios = array($this->edificio_id);
					$this->edificios_model->add_edificios($user->id,$edificios);

					$group = $this->db->get_where('users_groups',
						array('user_id'=>$user->id,'group_id'=>INQUILINO));
					if($group->num_rows() == 0){
						$this->db->insert('users_groups',
						array('user_id'=>$user->id,'group_id'=>INQUILINO));
					}

					return TRUE;
    			//}
    	}else{
    		return false;
    	}

    }


    /************************************* Votaciones ****************************************************/
    	
    	public function propuestas_list(){
    		$this->rat->log(uri_string(),1);
    		$this->layout->view('propuestas/list');
    	}

    	public function propuestas_read(){
    		$this->rat->log(uri_string(),1);
    		$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
    		$order_type = 'DESC';
    		$order_by = 'propuestas.id';
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
    							"users.first_name",
    							"users.last_name",
    							"propuestas.fecha_fin",
    							"propuestas.titulo",
    							"propuestas.descripcion",
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

    		$this->db->where(array('propuestas.edificio_id'=>$this->edificio_id
    			,'propuestas.estado_id'=>ACTIVO));
    		$data['registers']  = $this->propuestas_model->read();
    		//echo $this->db->last_query();
    		$this->load->view('propietarios/propuestas/read', $data);
    	}

    	public function propuestas_excel(){
	$this->rat->log(uri_string(),1);

    		$excelables = array("edificio",
    							"nombre",
    							"apellido",
    							"fecha_fin",
    							"titulo",
    							"descripcion",
    							"estado");

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
    						"propuestas.sector",
    						"propuestas.titulo",
    						"propuestas.descripcion",
    						"propuestas.autorizado");

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

    	public function propuestas_view($propuesta_id){
			$this->rat->log(uri_string(),1);
    		$where = array('propuestas.id'=>$propuesta_id,
    			'edificio_id'=>$this->edificio_id);

    		$data['propuesta'] = $this->propuestas_model->read($where)->row();
    		$data['opciones'] = $this->opciones_model->read(array('propuesta_id'=>$propuesta_id));
    		$data['files'] = $this->archivos_propuesta_model->read(array('propuesta_id'=>$propuesta_id));
			$where_voto = array('votaciones.propuesta_id'=>$propuesta_id,
				'votaciones.unidad_id'=>$this->unidad_id);

    		$rs = $this->votaciones_model->read($where_voto);
    		if($rs->num_rows() == 0){
    			if($data['propuesta'])
    				$this->layout->view('propuestas/view',$data);
    			else
    				redirect('propietarios/propuestas_list');
    		}else{
    			$data['voto'] = $rs->row();
    			$data['opciones'] = $this->opciones_model->votos($propuesta_id);
    			$data['votantes'] = $this->opciones_model->total_votos($propuesta_id)->row();
    			$data['unidades'] = $this->unidades_model->read(array('edificio_id'=>$this->edificio_id));
    			$data['files'] = $this->archivos_propuesta_model->read(array('propuesta_id'=>$propuesta_id));
    			$this->layout->view('propuestas/view_voto',$data);
    		}
    		
    	}

    	public function propuesta_votar(){
    		$this->rat->log(uri_string(),1);
    		$propuesta_id = $this->input->post('propuesta_id');
    		$opcion_id = $this->input->post('opcion');
    		$unidad_id = $this->unidad_id;
    		$user_id = $this->user->id;
    		$data = array('votaciones.propuesta_id'=>$propuesta_id,
    			'votaciones.unidad_id'=>$unidad_id,'votaciones.usuario_id'=>$user_id);
    		$rs = $this->votaciones_model->read($data);
    		if($rs->num_rows() == 0){
    			$data['opcion_id'] = $opcion_id;
    			$this->votaciones_model->create($data);
    		}
    		redirect('propietarios/propuestas_list');
    	}

    /******************************** FIN  Votaciones ***********************************************/
    /********************************Encargado*******************************************************/
    public function encargado_list(){
    	$this->rat->log(uri_string(),1);
    	$this->layout->view('encargado/list');
    }

    public function encargado_read(){
    	$this->rat->log(uri_string(),1);
    	$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
    	$order_type = 'DESC';
    	$order_by = 'cargo.id';
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
    	$this->load->view('propietarios/encargado/read', $data);
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


    public function encargado_view($encargado_id){
	$this->rat->log(uri_string(),1);

    	$where['encargado.id'] = $encargado_id;
    	$where['encargado.edificio_id'] = $this->edificio_id;
    	$rs = $this->encargado_model->read($where);
    	
    	if(!$rs->num_rows())
    		redirect('propietarios/encargado_list');

    	$data['values']  = $rs->row();
    	$this->layout->view('encargado/view', $data);
    }

    /******************************** Fin Encargado*******************************************************/
    /******************************** Legales *******************************************************/
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
        echo  $this->load->view('propietarios/legales/load_files',$data,true);

    }

    public function legales_read(){
    	$this->rat->log(uri_string(),1);
    	
    	if($this->session->has_userdata('legal_id')){
    		$legal_id = $this->session->userdata('legal_id');
    	}else{
    		redirect('propietarios/legales_list/'.SEGUROS);
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
    	$this->load->view('propietarios/legales/read', $data);
    }

    public function legales_excel(){
    	$this->rat->log(uri_string(),1);

    	if($this->session->has_userdate('legal_id')){
    		$legal_id = $this->session->userdata('legal_id');
    	}else{
    		redirect('propietarios/legales_list/'.SEGUROS);
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
    		redirect('propietarios/legales_error');
    	}
    	redirect('propietarios/legales_list');

    }


    /******************************** fin seguros*******************************************************/
    /******************************** Reservas *******************************************************/

    public function espacios_load($espacio_id){
    	$this->rat->log(uri_string(),1);
    	$this->session->set_userdata(array('espacio_id'=>$espacio_id));
    	redirect('propietarios/espacios_reservar/'.date('Y').'/'.date('m').'/'.$espacio_id);
	}
	
	public function espacios_reservar($year = null, $month = null,$espacio_id=null){
		$this->rat->log(uri_string(),1);
		if($this->Baned_model->is_baned($this->user->id,$espacio_id))
			redirect('propietarios/baned');

		if(empty($this->espacio_id) && $espacio_id)
			redirect('propietarios/espacios_list');
		
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
			redirect('propietarios/espacios_reservar/'.date('Y').'/'.date('m'));
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
		$reserva_id = intval(intval($this->input->post('reserva_id')));
		$reserva = $this->calendario_model->get_reserva($reserva_id);
		$data['error'] = '';
		if($reserva){
			if($reserva->unidad_id == $this->unidad_id && $reserva->user_id == $this->user->id){

				$hora_reserva = strtotime($reserva->dia_calendario.' '.$reserva->hora_reserva);
				$my_hora =  strtotime(date("Y-m-d H:i:s",(strtotime ("+1 Hours")))); 

				
				
				if($hora_reserva > $my_hora ){

					$this->calendario_model->rechazar_reserva($reserva->reserva_hash,$this->edificio_id,$this->unidad_id);
					//echo 1;
				}else{
					$data['error'] = "Esta reserva supero el tiempo para poder ser rechazada.";
				}

			}else{
				$data['error'] = "sentencia no permitida";
			}
			echo json_encode($data);
				
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
            
            $dia_escogido = $this->input->post('dia_escogido',TRUE);
            $mes_escogido = $this->input->post('mes_escogido',TRUE);
            $espacio_id = $this->input->post('espacio_id',true);
            $turno_id = $this->input->post('turno_id',TRUE);

            if(!$this->calendario_model->check_date($fecha_completa ,$espacio_id)){
            	echo json_encode(array("error"=>"El día se encuentra cerrado"));
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
            	/*$periodo = $this->calendario_model->get_periodos($espacio_id);
       
            	if($periodo->num_rows()){
            		$data['periodo']= $periodo;
                	$this->load->view("propietarios/espacios/ajax/get_periodo",$data);
            	}
                else{

                	$this->load->view("propietarios/espacios/ajax/get_turnos",$data);
                }*/

                
            	$periodo = $this->calendario_model->get_periodos($espacio_id);
            	$turnos = $this->calendario_model->get_turnos($espacio_id);
            
            	if($periodo->num_rows()){
            		$data['periodo']= $periodo;
                	$this->load->view("propietarios/espacios/ajax/get_periodo",$data);
            	}
                elseif($turnos->num_rows()){
                	$this->load->view("propietarios/espacios/ajax/get_turnos",$data);
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

		$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
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
		$this->load->view('propietarios/espacios/read', $data);
	}


	public function espacios_list(){
	$this->rat->log(uri_string(),1);

		$data['edificios'] = $this->db->get('edificios',TRUE);
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
				$search = urldecode($this->input->get('search',true));
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

			$dia = $this->input->post('dia_update',true);
			$hora = $this->input->post('hora',true);
			$espacio_id = $this->input->post('espacio_id',true);
			$unidad_id = $this->unidad_id;
			$fecha_escogida = $this->input->post('fecha_escogida',true); 
			$invitados = $this->input->post('invitados',true);

			if(!$this->calendario_model->check_date($fecha_escogida ,$espacio_id)){
				echo "El día se encuentra cerrado.\n";
				return true;
			}

			$data = array("estado" => "ocupado",
            "unidad_id"=>$unidad_id,'user_id'=> $this->user->id);

			if(!$espacio = $this->get_espacio()){
				return false;
			}

            if(isset($_POST['turno_id'])){
          		$turno_id = intval($this->input->post('turno_id',TRUE));
            	$rs = $this->calendario_model->get_turno($turno_id);	
            	
            	if($rs){
					$turno = $rs->turno;
					$reserva_id = 0;
            	}else
            	{
            		$turno = false;
            	}
            	
            	$data['turno_id'] = $turno_id ;
            }	            

            if(isset($_POST['periodo_id'])){
            	$data['periodo_id'] = $this->input->post('periodo_id',TRUE);
				$reserva_id = $this->input->post('reserva_id',true);
            	$turno =  false;
            }

            $data['estado_id'] = ($espacio->autorizacion == true)? PENDIENTE:APROBADO;

			$data['invitados'] = $invitados;
			
			$nueva_reserva = $this->calendario_model->nueva_reserva($reserva_id,$data,$dia,$hora,$espacio_id,$turno);

			if($nueva_reserva)
			{   
				$data['unidad_id'] = $unidad_id;
				$data['hash'] = md5($unidad_id.$nueva_reserva.$this->user->id);
				$data['menssage'] = $fecha_escogida.' a las '.date("G:i",strtotime($hora));
				$data['aprobado'] = ($espacio->autorizacion == true)? "Su reserva esta pendiente de aprobación.\n":"Su reserva fue Aprobada.\n";
				$unidad = $this->db->get_where('unidades',array('id'=>$unidad_id))->row();
				$data['unidad'] = $unidad->name." ".$unidad->departamento;
				$this->notificar_reserva($espacio_id,$data);  
				$text = "Su reserva del: ".$data['menssage'] ;
				echo  ($espacio->autorizacion == true)? $text." está pendiente aprobación":$text." está aprobada.\n";
			}else{
				echo "Comuniquese con el Administrador";
			}

		}else{
			//die("asd");
			show_404();
		}
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
/*
	public function reserva_checked(){
	$this->rat->log(uri_string(),1);

		$dia = $this->input->post('dia_update',true);
		$hora = $this->input->post('hora',true);
		$espacio_id = $this->input->post('espacio_id',true);
		$estado = 'ocupado';
		$fecha_escogida = $this->input->post('fecha_escogida',true); 
		$rs = $this->calendario_model->existe_reserva($dia,$hora,$espacio_id,$estado);
		if($rs){
			echo $fecha_escogida." ya se ecunuentra reservado";
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
				$config['allowed_types'] = 'webp|gif|jpg|png|jpeg|xlsx|xls|doc|pdf|pdfx';
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

	public function reservados(){
		$this->rat->log(uri_string(),1);
		$order_type = 'DESC';
		$order_by = 'espacios.id';
		$search = false;

		if($_POST){

			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);

			$espacio_id = $this->input->post('espacio_id',true);
			$fecha_desde = $this->input->post('fecha_desde',true);
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
				$search = $this->db->escape_str($this->input->get('search',true));

				$espacio_id = $this->input->get('espacio_id',TRUE);
				$fecha_desde = $this->input->get('fecha_desde',TRUE);
				$fecha_hasta = $this->input->get('fecha_hasta',TRUE);
				
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


	public function rechasados(){
	$this->rat->log(uri_string(),1);

		$order_type = 'DESC';
		$order_by = 'espacios.id';
		$search = false;

		if($_POST){

			$order_by = $this->input->post('order_by',true);
			$order_type = $this->input->post('order_type',true);
			$search = $this->input->post('search',true);

			$espacio_id = $this->input->post('espacio_id',true);
			$fecha_desde = $this->input->post('fecha_desde',true);
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
	//	echo $this->db->last_query();
		$data['is_rechasado'] = TRUE;
		$this->espacios_informes_read($data);
	}

	public function espacios_informes_read($data){
	$this->rat->log(uri_string(),1);

		$this->load->view('propietarios/espacios/informes_read', $data);
	}

	/*public function my_user(){
		$this->rat->log(uri_string(),1);


		$where['users.id'] = $this->user->id;
		$where['edificios.id'] = $this->edificio_id;
		if(!empty($_POST)){

			
			$data['edificio_id'] = $this->edificio_id;
			

			if(isset($_POST['username'])){
				$data['username'] = $this->input->post('username',TRUE);
			}

			if(isset($_POST['password'])){
				$data['password'] = $this->input->post('password',TRUE);
			}

			if(isset($_POST['email'])){
				$data['email'] = $this->input->post('email',TRUE);
			}			

			if(isset($_POST['email_fw'])){
				$data['email_fw'] = $this->input->post('email_fw',TRUE);
			}

			if(isset($_POST['active'])){
				$data['active'] = $this->input->post('active',TRUE);
			}

			if(isset($_POST['first_name'])){
				$data['first_name'] = $this->input->post('first_name',TRUE);
			}

			if(isset($_POST['last_name'])){
				$data['last_name'] = $this->input->post('last_name',TRUE);
			}

			if(isset($_POST['unidad'])){
				$data['unidad'] = $this->input->post('unidad',TRUE);
			}

			if(isset($_POST['phone'])){
				$data['phone'] = $this->input->post('phone',TRUE);
			}

			if(isset($_POST['alternative_phone'])){
				$data['alternative_phone'] = $this->input->post('alternative_phone',TRUE);
			}


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




			if(!$this->ion_auth->update($this->user->id, $data)){
				redirect('propietarios/users_error');
			}

			redirect('propietarios');
		}

		$rs = $this->users_model->read($where);
		if(!$rs->num_rows()){
			redirect('propietarios');
		}

		$data['values'] = $rs->row();
		$this->layout->view('usuarios/update', $data);
	}*/

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
	    			    redirect('propietarios');

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
    /****Reglamentos ****/
    public function reglamentos_read(){
	$this->rat->log(uri_string(),1);

    	$limit = (isset($_POST['limit']))? $this->input->post('limit',true) : 50;
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
    	$this->load->view('propietarios/reglamentos/read', $data);
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
    /**** Fin de Reglamentos ****/

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
    //	echo "aca";
    	$this->send_email->new_consulta($email,$data);
    }    

    public function notificar_respuesta($data){
    	$this->rat->log(uri_string(),1);
    	//return TRUE;
    	$user_email = $this->users_model->get_email($this->user->id);
    	$email = array($user_email);
    	$email_administradores = $this->users_model->get_my_adminstrador($this->edificio_id);
    	$email = array_merge($email ,$email_administradores);
    	$this->send_email->new_respuesta($email,$data);
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

	public function check_permitidos(){
		$reserva_id = $this->input->post('reserva_id');
		$cant = $this->calendario_model->get_number_periodo($reserva_id,TRUE);
		echo $cant;
	}
 
 	public function reservation() {

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

