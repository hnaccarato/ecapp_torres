<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

defined('BASEPATH') OR exit('No direct script access allowed');
class Seguridad extends CI_Controller {

	public $user;
	public $edificio_id;
	private $espacio_id;

	public function __construct(){
		parent::__construct();

		$this->layout->setFolder('seguridad');
		$this->layout->setLayout('seguridad/layout');
		$this->lang->load('auth');
		$this->user = $this->ion_auth->user()->row();

		if (!$this->ion_auth->in_group(SEGURIDAD)){
			redirect('accseslog');
		}

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
                //comentar en caso de falla
        $this->my_style->check_company_host(APP_URL,$this->edificio_id );
	}

	public function index(){
		$this->espacios_informes();
	}


	public function espacios_load($espacio_id){
		$this->session->set_userdata(array('espacio_id'=>$espacio_id));
		redirect('seguridad/espacios_reservar/'.date('Y').'/'.date('m').'/'.$espacio_id);
	}

public function espacios_reservar($year = null, $month = null,$espacio_id=null){
	$this->rat->log(uri_string(),1);
	
	if(!$this->espacios_model->reservas_habilitado($this->espacio_id))
		redirect('seguridad/espacios_list');

	if(empty($this->espacio_id) && $espacio_id)
		redirect('seguridad/espacios_list');

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
		redirect('seguridad/espacios_reservar/'.date('Y').'/'.date('m'));
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
		
		$this->calendario_model->delete_turno("espacio_id = $espacio_id");	
		$identificacion = $_POST['identificacion'];
		
		for ($i=0 ; $i < count($identificacion) ; $i++ ) { 

			if(!empty($_POST['identificacion'][$i])){
				$data_periodo['id'] = (isset($_POST['turno_id'][$i]))? $_POST['turno_id'][$i]:"";
				$data['espacio_id'] = $espacio_id;
				$data['identificacion'] = $_POST['identificacion'][$i];
				$data['turno'] = $_POST['turno'][$i];
				$data['importe'] = $_POST['importe'][$i];
				$data['active'] = TRUE;
				$this->calendario_model->insert_turno($data);
			}

		}

	}
	
	public function add_periodo($espacio_id){
		$this->calendario_model->delete_periodo("espacio_id = $espacio_id");

		$periodos = $_POST['desde'];
		for ($i=0 ; $i < count($periodos)  ; $i++ ) { 
			if(!empty($_POST['desde'][$i])){
				$data_periodo['id'] = (isset($_POST['periodo_id'][$i]))? $_POST['periodo_id'][$i]:"";
				$data_periodo['espacio_id'] = $espacio_id;
				$data_periodo['desde'] = $_POST['desde'][$i];
				$data_periodo['hasta'] = $_POST['hasta'][$i];
				$data_periodo['importe'] = $_POST['importe_periodo'][$i];
				$data_periodo['active'] = TRUE;
				$this->calendario_model->insert_periodo($data_periodo);
			}
		}
	}

    public function coger_hora(){

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
       
            	if($periodo->num_rows()){
            		$data['periodo']= $periodo;
                	$this->load->view("seguridad/espacios/ajax/get_periodo",$data);
            	}
                else{

                	$this->load->view("seguridad/espacios/ajax/get_turnos",$data);
                }
                
            }
        }else{
         	die("no hay reserva");
            show_404();
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
                }

            }else{
                show_404();
            }
        }

	public function espacios_read(){

		$limit = (isset($_POST['limit']))? $this->input->post('limit') : 50;
		$order_type = 'DESC';
		$order_by = 'espacios.id';
		$search = false;
		if($_POST){
			$limit = $this->input->post('limit',TRUE);
			$order_by = $this->input->post('order_by',TRUE);
			$order_type = $this->input->post('order_type',TRUE);
			$search = $this->input->post('search',TRUE);
		}
	
		$this->db->order_by($order_by, $order_type);
		$this->db->limit($limit);
		if($search != ''){

			$searchables =array('edificios.nombre',
				'espacios.nombre_espacio',
				'espacios.descripcion',
				'espacios.max',
				'espacios.max_meses',
				'espacios.init_hora',			
				'espacios.fin_hora',			
				'espacios.foto_espacio'
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
		$data['registers']  = $this->espacios_model->read();
		$this->load->view('seguridad/espacios/read', $data);
	}

	public function espacios_list(){
		$this->layout->view('espacios/list');
	}

	public function espacios_excel(){

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
				$limit = $this->input->get('limit',TRUE);
				$order_by = $this->input->get('order_by',TRUE);
				$order_type = $this->input->get('order_type',TRUE);
				$search = urldecode($this->input->get('search',TRUE));
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
					$data['menssage'] = 'Su reserva <br/> <strong>'.$espacio->nombre_espacio.' </strong></br>El '.date("d/m/Y",strtotime($reserva->dia_calendario)).' '.$reserva->hora_reserva.' - '.$reserva->hora_hasta.'<br/> <h3 style="color:red"> Su reserva fue rechazada por el Administrador</h3>'; 
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
				$send_mail['menssage'] = 'Su reserva <br/> <strong>'.$espacio->nombre_espacio.' </strong></br>El '.date("d/m/Y",strtotime($reserva->dia_calendario)).' '.$reserva->hora_reserva.' - '.$reserva->hora_hasta.'<br/> <h3 style="color:red"> Su reserva fue Aprobada por el Administrador</h3>'; 
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
		$data['espacios'] = $this->espacios_model->read(array('edificios.id'=>$this->edificio_id));
		$this->layout->view('espacios/informes',$data);
	}

	public function reservados(){

		$order_type = 'DESC';
		$order_by = 'espacios.id';
		$search = false;

		if($_POST){

			$order_by = $this->input->post('order_by');
			$order_type = $this->input->post('order_type');
			$search = $this->input->post('search');

			$espacio_id = $this->input->post('espacio_id');
			$fecha_desde = $this->input->post('fecha_desde');
			$fecha_hasta = $this->input->post('fecha_hasta');
			
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
		$data['registers']  = $this->espacios_model->reservados();
		$this->espacios_informes_read($data);
	}

	public function rechasados(){
		$order_type = 'DESC';
		$order_by = 'espacios.id';
		$search = false;

		if($_POST){

			$order_by = $this->input->post('order_by');
			$order_type = $this->input->post('order_type');
			$search = $this->input->post('search');

			$espacio_id = $this->input->post('espacio_id');
			$fecha_desde = $this->input->post('fecha_desde');
			$fecha_hasta = $this->input->post('fecha_hasta');
			
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
		$data['registers']  = $this->espacios_model->rechasados();
		$this->espacios_informes_read($data);
	}

	public function espacios_informes_read($data){
		$this->load->view('seguridad/espacios/informes_read', $data);
	}

	public function rechasados_excel(){

		$excelables = array('id','nombre_espacio','date','desde','hasta','unidad','first_name','last_name','cuando','importe');

		if(isset($excelables) && count($excelables) > 0){
			$filename = 'reservas_'.date('Y-m-d').'.xls';
			$objPHPExcel = new PHPExcel();

			$order_type = 'DESC';
			$order_by = 'reservas.id';
			$search = false;


			if($_GET){

				$order_by = $this->input->get('order_by');
				$order_type = $this->input->get('order_type');
				$search = $this->input->get('search',TRUE);

				$espacio_id = $this->input->get('espacio_id');
				$fecha_desde = $this->input->get('fecha_desde');
				$fecha_hasta = $this->input->get('fecha_hasta');
				
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

		$excelables = array('id','nombre_espacio','date','desde','hasta','unidad','first_name','last_name','cuando','importe');

		if(isset($excelables) && count($excelables) > 0){
			$filename = 'reservas_'.date('Y-m-d').'.xls';
			$objPHPExcel = new PHPExcel();

			$order_type = 'DESC';
			$order_by = 'reservas.id';
			$search = false;

			if($_GET){

				$order_by = $this->input->get('order_by');
				$order_type = $this->input->get('order_type');
				$search = $this->input->get('search',TRUE);

				$espacio_id = $this->input->get('espacio_id');
				$fecha_desde = $this->input->get('fecha_desde');
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

	
    public function notificar_reserva($espacio_id,$data){
        $this->rat->log(uri_string(),1);
    //return TRUE;
        $email = $this->unidades_model->get_email($data['unidad_id']);
        $where = array('espacios.id'=>$espacio_id);
        $data['espacio'] = $this->espacios_model->read($where)->row();
        $this->send_email->new_reserva($email,$data);
    }



    public function check_permitidos(){
        $reserva_id = $this->input->post('reserva_id');
        $cant = $this->calendario_model->get_number_periodo($reserva_id);
        echo $cant;
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
            }

        }

    }

    private function informar_email($data){
    	$this->send_email->new_notificar($data['email'],$data);
    	return true;
    }
}	