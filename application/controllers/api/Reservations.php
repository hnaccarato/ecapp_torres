<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Reservations extends REST_Controller {

    public $user;
    public $edificio_id;
    function __construct()
    {
        parent::__construct();
   //     $this->methods['Reservations_get']['limit'] = 500;
        $this->load->model('espacios_model');
        $this->load->model('calendario_model');
        
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $this->load->library('Authorization_Token');
        $method = $_SERVER['REQUEST_METHOD'];
        $this->load->library('session');
        if(!$this->verify_post()) {
            die();
        }
        $this->session->set_userdata('error_api_set', true);
    }


    public function Reservations_get(){
        $this->db->where('espacios.edificio_id',9);
        $espacios  = $this->espacios_model->read()->result();
        if ($espacios)
        {
            $this->response($espacios, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No espacios were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }


    /**
    * @get : http://{{URL}}/api/Reservations/placesReservation
    *
    * @param int space
    * @param int year
    * @param int month
    * 
    * @return json array();
    *  
    */ 

    public function placesReservation_get(){
		$espacio_id = (int ) $this->get('space');
		$year = (int) $this->get('year');
		$month = (int) $this->get('month');

		if($month == date("m")){
			$this->db->where('reservas.dia_calendario >=',$year.'-'.$month.'-'.date('d'));
		}
		else{
			$this->db->like('reservas.dia_calendario',$year.'-'.$month);
		}
		
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

        if ($data)
        {
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => 400,
                'message' => 'No espacios were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
	}


    /**
     * @post : http://{{URL}}/api/Reservations/myReservations
     *
     * @param int buildingID
     * @param int unityId
     * @param date from
     * @param date to
     *
     * @return json array();
     *  
     */ 
    public function myReservations_post(){

        $buildingId = $this->input->post('buildingId');
        $unityId = $this->input->post('unityId');
        $from = $this->input->post('from');
        $to = $this->input->post('to');

        $order_type = 'DESC';
        $order_by = 'espacios.id';
        $search = false;

        if(!empty($from)){
            $this->db->where('reservas.dia_calendario >= ',$from);
            $this->db->where('reservas.dia_calendario <=',$to);
        }else{
            $this->db->where('reservas.dia_calendario >= now()' );
        }

        $this->db->order_by($order_by, $order_type);
        $this->db->where('espacios.edificio_id',$buildingId);
        $this->db->where('reservas.unidad_id',$unityId);
        
        $data['registers']  = $this->espacios_model->reservadosApi()->result();
        
        if ($data)
        {
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => 400,
                'message' => 'No Reservations were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }

    }


    /**
    * @post : http://{{URL}}/api/Reservations/rejectedReservations
    *
    * @param int buildingID
    * @param int unityId
    * @param date userId
    * @param date from
    * @param date to
    * 
    * @return json array();
    *  
    */ 
    public function rejectedReservations_post(){
        
        $buildingId = $this->input->post('buildingId');
        $unityId = $this->input->post('unityId');
        $userId = $this->input->post('userId');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $order_type = 'DESC';
        $order_by = 'espacios.id';
        $search = false;

        $this->db->order_by($order_by, $order_type);
        $this->db->where('espacios.edificio_id',$buildingId);
        $this->db->where('reservas_rechazados.unidad_id',$unityId);
        $this->db->where('reservas.user_id',$userId);
        
        $data['registers']  = $this->espacios_model->rechasados()->result();

        if ($data)
        {
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => 400,
                'message' => 'No Reservations were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }


    /**
    * @post : http://{{URL}}/api/Reservations/cancelReservation
    *
    * @param int buildingId
    * @param int reservationId
    * @param int unityId
    * @param int buildingId
    * @param int userId
    * 
    * @return json array();
    *  
    */ 
    public function cancelReservation_post(){

        $buildingId = (int) $this->input->post('buildingId');
        $reserva_id = (int) $this->input->post('reservationId');
        $unityId = (int) $this->input->post('unityId');
        $buildingId = (int) $this->input->post('buildingId');
        $userId = (int) $this->input->post('userId');
        $reserva = $this->db->get_where('reservas',array('id'=>$reserva_id))->row();
        
        if($reserva){
            if($reserva->unidad_id == $unityId) {
                $hora_reserva = strtotime($reserva->dia_calendario.' '.$reserva->hora_reserva);
                $my_hora =  strtotime(date("Y-m-d H:i:s",(strtotime ("+1 Hours")))); 

                if($hora_reserva >= $my_hora ){
                    $this->calendario_model->rechazar_reserva($reserva->reserva_hash,$buildingId,$unityId,$userId);
                    $this->response(true, REST_Controller::HTTP_OK);
                }else{
                    $this->response(false, REST_Controller::HTTP_OK);
                }   
            }
        }      
    }



    /**
    * @get : http://{{URL}}/api/Reservations/spaces
    *
    * @param int buildingId
    * @param text search
    * 
    * @return json array();
    *  
    */ 
    public function spaces_get()
    {
        $order_type = 'DESC';
        $order_by = 'espacios.id';
        $buildingId = (int) $this->input->get('buildingId');
        $search = (int) $this->input->get('search');
        $this->db->order_by($order_by, $order_type);
        
        if($search == ''){
            $search = null;
        }

        if($search){
            $searchables = array('edificios.nombre','nombre_espacio','descripcion','periodo','init_hora','fin_hora',);
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

        $this->db->where('espacios.edificio_id',$buildingId);
        $data['registers']  = $this->espacios_model->read()->result();

        if ($data)
        {
            $this->response($data, REST_Controller::HTTP_OK); 
        }
        else
        {
            $this->response([
                'status' => 400,
                'message' => 'No get Spaces were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
    * @get : http://{{URL}}/api/Reservations/CalendarReservations
    *
    * @param int spaceId
    * @param int buildingId
    * @param int year
    * @param int month
    * 
    * @return json array();
    *  
    */ 
    public function calendarReservations_get(){
        
        $spaceId = (int) $this->input->get('spaceId');
        $buildingId = (int) $this->input->get('buildingId');
        $year = (int) $this->input->get('year');
        $month = (int) $this->input->get('month');

        if(!$year)
        {
            $year = date('Y');
        }
        if(!$month)
        {
            $month = date('m');
        }

        $this->calendario_model->insert_calendario($month,$year,$spaceId);
        $where = array('espacios.edificio_id'=>$buildingId,
            'espacios.id'=>$spaceId);
        $data['calendario'] = $this->calendario_model->generar_calendario($year, $month,$spaceId);
        $turnos = $this->calendario_model->get_turnos($spaceId);
        if( $turnos->num_rows()){
            $data['reservas'] = $this->calendario_model->get_reservas_turno_api($spaceId)->result();
        }else{
            $data['reservas'] = $this->calendario_model->get_reservas_periodo_api($spaceId)->result();
        }
        
        $data['turnos'] = $turnos->result();
        $data['periodos'] = $this->calendario_model->get_periodos($spaceId)->result();
        $data['values'] = $this->espacios_model->read($where)->row();
        $data['turno_id'] = 0;
        $data['year']= $year;
        $data['month']= $month;

        $this->db->like('reservas.dia_calendario',$year.'-'.$month,FALSE);
        $this->db->where('espacios.edificio_id',$buildingId);
        $this->db->where('espacios.id',$spaceId);

        if ($data)
        {
            $this->response($data, REST_Controller::HTTP_OK); 
        }
        else
        {
            $this->response([
                'status' => 400,
                'message' => 'No get Spaces were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }


    /**
    * @post : http://{{URL}}/api/Reservations/selectReservations
    *
    * @param int num
    * @param int year
    * @param int month
    * @param int buildingId
    * @param int dayChoosed
    * @param int monthChoosed
    * @param int spaceId
    * @param int turnId
    * 
    * @return json array();
    *  
    */ 
    public function selectReservations_post()
    {
        
        $dia = $this->input->post('num');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $buildingId = (int) $this->input->post('buildingId');
        
        $fecha_completa = $year.'-'.$month.'-'.$dia;
        $dia_escogido = $this->input->post('dayChoosed');
        $mes_escogido = $this->input->post('monthChoosed');
        $espacio_id = (int) $this->input->post('spaceId');
        $turno_id = (int) $this->input->post('turnId');

        if(!$this->calendario_model->check_date($fecha_completa ,$espacio_id)){
            echo "El dia se encuentra Cerrado";
            return true;
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

        if ($espacio_id == 28) {
            $turno = '01:00:00';
        }

        $info_dia = $this->calendario_model->horas_seleccionadas_api($year,$month,$dia ,$espacio_id,$turno);

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

        $where = array('espacios.edificio_id'=>$buildingId,
        'espacios.id'=>$espacio_id);
        $data['values'] = $this->espacios_model->read($where)->row();
        $data['unidades'] = $this->unidades_model->get_unidades_ocupadas($buildingId)->result();
        $data['turnos'] = $this->calendario_model->get_turnos($espacio_id);

        if( $data['turnos']->num_rows()){
            $data['reservas'] = $this->calendario_model->get_reservas_turno_api($espacio_id,$fecha_completa)->result();
        }else{
            $data['reservas'] = $this->calendario_model->get_reservas_periodo_api($espacio_id,$fecha_completa)->result();
        }

        $data['turnos'] = $data['turnos']->result();

        //si hay horas disponibles para ese día mostramos 
        //la vista pasando la info en el array data 
        if($info_dia !== false)
        {   
            $periodo = $this->calendario_model->get_periodos($espacio_id);
   
            if($periodo->num_rows()){
                $data['periodo']= $periodo;
                
            }
            $this->response($data, REST_Controller::HTTP_OK); 
        }else{
           $this->response([
                'status' => 400,
                'message' => 'No get Spaces were found'
            ], REST_Controller::HTTP_NOT_FOUND);  
        }
    }

    /**
    * @post : http://{{URL}}/api/Reservations/newReservation
    *
    * @param int adicionales
    * @param int reserva_id
    * @param int date
    * @param int hora
    * @param int espacio_id
    * @param int buildingId
    * @param int unityId
    * @param int dateSelected
    * @param int userId
    * @param int condition
    * 
    * @return json array();
    *  
    */ 
    public function newReservation_post() {
        $invitados = intval($this->input->post('adicionales',TRUE));
        $reserva_id = (int) $this->input->post('reserva_id');

        if(!$invitados){
            $rs = $this->calendario_model->get_disponibles($reserva_id);
            $reserva = $rs->row();

            if (is_null($reserva)) {
                $message = [
                    'status' => 400,
                    'message' => 'El Horario se encuentra ocupado'
                ];
                $this->set_response($message, REST_Controller::HTTP_CREATED);   
                return false; 
            }

            $_POST['reserva_id'] = $reserva->id;
            $this->addReservation(); 
        }else{
            for ($i=0; $i < $invitados; $i++) { 

                $rs = $this->calendario_model->get_disponibles($reserva_id);
                if(!$rs) {
                    $message = [
                        'status' => 400,
                        'message' => 'El Horario se encuentra ocupado'
                    ];
                    $this->set_response($message, REST_Controller::HTTP_CREATED); 
                    return false;    
                }
                $reserva = $rs->row();
                $_POST['reserva_id'] = $reserva->id;
                $this->addReservation(); 
            }
        }
    }

    private function addReservation()
    {
        if($this->checkedReservation()){   
            return false;
        }

        $dia = $this->input->post('dateSelected');
        $hora = $this->input->post('hora');
        $espacio_id = (int) $this->input->post('espacio_id');
        $buildingId = (int) $this->input->post('buildingId');
        $unidad_id = (int) $this->input->post('unityId');
        $fecha_escogida = $this->input->post('dateSelected'); 
        $userId = (int) $this->input->post('userId');
        $this->user = $userId;
        $termsAndConditions = $this->input->post('condition');
        $reserva_id = (int) $this->input->post('reserva_id');
        $this->edificio_id = $buildingId;
        
        if(!$termsAndConditions) {
            $message = [
                'status' => 400,
                'message' => 'Debés aceptar los términos y condiciones'
            ];
            $this->set_response($message, REST_Controller::HTTP_CREATED);
            return true;
        }
       
        $turno = false;
        
        if(!$this->calendario_model->check_date($fecha_escogida ,$espacio_id)){
            $message = [
                'status' => 400,
                'message' => 'El dia se encuentra Cerrado'
            ];
            $this->set_response($message, REST_Controller::HTTP_CREATED);
            return true;
        }

        $data = array("estado" => "ocupado",
        "unidad_id"=>$unidad_id,'user_id'=> $userId);

        if(!$espacio = $this->getEspacio()){
           $this->response([
               'status' => 404,
               'message' => 'El espacio seleccionado no existe'
           ], REST_Controller::HTTP_NOT_FOUND); 
           return false;
        }

        if(isset($_POST['turno_id'])){
            $turno_id = $this->input->post('turno_id');
            $rs = $this->calendario_model->get_turno($turno_id);    
            
            if($rs){
                $turno = $rs->turno;
            }else
            {
                $turno = false;
            }
            $data['turno_id'] = $_POST['turno_id'];
        }               

        if(isset($_POST['periodo_id']) && $_POST['periodo_id'] != 0){
            $data['periodo_id'] = $_POST['periodo_id'];
            $turno =  false;
        }
       // $data['nueva_reserva'] = null;
        
        $data['estado_id'] = ($espacio->autorizacion == true)? PENDIENTE:APROBADO;

        $nueva_reserva = $this->calendario_model->nueva_reserva($reserva_id, $data,$dia,$hora,$espacio_id,$turno,$buildingId,$userId);

        
        if($nueva_reserva)
        {   
            $data['unidad_id'] = $unidad_id;
            $data['hash'] = md5($unidad_id.$nueva_reserva.$userId);
            $data['menssage'] = 'Su reserva se ha realizado para el día '.$fecha_escogida.' a las '.$hora;
            if($espacio->autorizacion == true){
                $data['menssage'] = "Su reserva se encuentra pendiente de aprobación";
            }
            $data['aprobado'] = ($espacio->autorizacion == true)? "Su reserva esta pendiente de aprobación":"Su reserva fue Aprobada";
            $unidad = $this->db->get_where('unidades',array('id'=>$unidad_id))->row();
            $data['unidad'] = $unidad->name." ".$unidad->departamento;
            $this->notification($espacio_id,$data); 
        } else {
              $this->response([
                  'status' => 400,
                  'message' => $this->session->userdata('error_message_reservation')
              ], REST_Controller::HTTP_BAD_REQUEST);
              return false;
        }

        if ($data)
        {
            $this->response($data, REST_Controller::HTTP_OK); 
        }
        else
        {
            $this->response([
                'status' => 400,
                'message' => 'La reserva no pudo realizarce correctamente'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            return false;
        }
    }

    /**
    * @post : http://{{URL}}/api/Reservations/newReservation
    *
    * @param int date
    * @param int hora
    * @param int spaceId
    * @param int dateSelected
    * 
    * @return json array();
    *  
    */ 

    public function checkedReservation(){
        $dia = $this->input->post('date');
        $hora = $this->input->post('hora');
        $espacio_id = $this->input->post('spaceId');
        $estado = 'ocupado';
        $fecha_escogida = $this->input->post('dateSelected'); 
        $rs = $this->calendario_model->existe_reserva($dia,$hora,$espacio_id,$estado);
       
        if($rs){
            $message = [
                'id' => 100, 
                'message' => 'La reserva para el día '.$fecha_escogida." ya se ecnuentra reservado"
            ];
            $this->set_response($message, REST_Controller::HTTP_CREATED);
            return TRUE;
        }
    }

    private function getEspacio(){

        $data['edificio_id'] = $this->input->post('buildingId');
        $data['id'] = $this->input->post('espacio_id');
        $rs = $this->db->get_where('espacios',$data);
        if($rs->num_rows() > 0){
            return $rs->row();
        }else{
            return false;
        }
        
    }

    public function notification($espacio_id,$data){
        $this->rat->log(uri_string(),1);

        //return TRUE;
        $email_propietarios = $this->unidades_model->get_email($data['unidad_id'],$this->user);
        $email_administradores = $this->users_model->get_my_adminstrador($this->edificio_id);
        $email = array_merge($email_propietarios,$email_administradores);
        $where = array('espacios.id'=>$espacio_id);
        $data['espacio'] = $this->espacios_model->read($where)->row();
        $this->send_email->new_reserva($email,$data);
    }  


    public function verify()
    {  
        return true; 
        $headers = $this->input->request_headers(); 
        if (isset($headers['Bearer'])) {
            return true; 
        }
        else {
            $this->response(['Authentication failed'], REST_Controller::HTTP_OK);
            die();
        }
    }

    public function verify_post()
    {  
        $headers = $this->input->request_headers(); 
        if (isset($headers['Bearer'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Bearer']);
            if ($decodedToken) {
                return true;
            }
        }
        else {
            return FALSE;
        }
    }

}
