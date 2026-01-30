<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Event_model extends CI_MODEL
{

	public $building_id;	
	public $unidad_id;	
	public $user_id;	

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('America/Argentina/Buenos_Aires');
	}


	function set_building($building_id){
		$this->building_id = $building_id;
	}
	
	function set_user_id($user_id){
		$this->user_id = $user_id;
	}

	function set_unity($unity_id){
		$this->unidad_id = $unity_id;
	}

	function get_event($date_ini,$date_fin,$alert=false){

		$controllers = $this->router->fetch_class();
		$array =  array();
		if ($data = $this->get_circular($date_ini,$date_fin,$controllers,$alert))
			$array = array_merge($array,$data);	

	  	if ($controllers == 'propietarios' || $controllers == 'inquilinos' ){
			if ($data = $this->get_reservas($date_ini,$date_fin,$controllers,$alert))
				$array = array_merge($data,$array);		
		}
		
		if ($data = $this->get_propuestas($date_ini,$date_fin,$controllers,$alert))
			$array = array_merge($array,$data);		

		if ($data = $this->get_expensa($date_ini,$date_fin,$controllers,$alert))
			$array = array_merge($array,$data);

		return json_encode(array("monthly"=>$array));

	}

	function get_nearby_events($date_ini,$date_fin,$alert=false){

		$controllers = $this->router->fetch_class();
		$array =  array();
		if ($data = $this->get_circular($date_ini,$date_fin,$controllers,$alert))
			$array = array_merge($array,$data);	

		if ($data = $this->get_reservas($date_ini,$date_fin,$controllers,$alert))
			$array = array_merge($data,$array);		
		
		if ($data = $this->get_propuestas($date_ini,$date_fin,$controllers,$alert))
			$array = array_merge($array,$data);		

		if ($data = $this->get_expensa($date_ini,$date_fin,$controllers,$alert))
			$array = array_merge($array,$data);

		return json_encode(array("monthly"=>$array));

	}

	function get_event_now(){
		
		$controllers = $this->router->fetch_class();
		$array =  array();

		if ($data = $this->get_circular(date("Y-m-d"),date("Y-m-d"),$controllers,date("Y-m-d"))){
		//	$data[key($data)]['type'] = 'circular';
		//	$array = array_merge($array,$data);		
			$array = $data;
		}


		if ($data = $this->get_reservas(date("Y-m-d"),date("Y-m-d"),$controllers,date("Y-m-d"))){
		//	$data[key($data)]['type'] = 'reservas';
		//	$array = array_merge($data,$array);		
			$array = $data;
		}

		if ($data = $this->get_propuestas(date("Y-m-d"),date("Y-m-d"),$controllers,date("Y-m-d"))){
			//$data[key($data)]['type'] = 'propuestas';
		//	$array = array_merge($array,$data);
			$array = $data;
		}		

		if ($data = $this->get_expensa(date("Y-m-d"),date("Y-m-d"),$controllers,date("Y-m-d"))){
			//$data[key($data)]['type'] = 'propuestas';
		//	$array = array_merge($array,$data);
			$array = $data;
		}

		return $array;
	}

	function get_circular($date_ini,$date_fin,$controllers,$alert = false){
		//$array = array();
		$this->db->select('id,titulo,fecha_envio,fecha');
		$this->db->group_start();
		$this->db->where(array('estado_id'=>ENVIADO,'edificio_id'=>$this->building_id));
		$this->db->group_end();
		
		if($alert){
			$this->db->or_group_start();
			$this->db->where(array('edificio_id'=>$this->building_id,'estado_id'=>ENVIADO,'date(timestamp)'=>$alert));
			$this->db->group_end();
		}

		$rs =  $this->db->get('circular');

		$c = 0;
		foreach ($rs->result() as $key => $value) {
			$array[$c] = array('id' => $value->id,
				'name'=>"Circular :".$value->titulo,
				'startdate'=>$value->fecha,
				'enddate'=>$value->fecha_envio,
				"color"=>"#993466",
				"url"=>base_url($controllers.'/view_circular/'.$value->id));
		
			if($alert)
				$array[$c]['type'] = 'Circular';
			$c++;
		}

		if(isset($array)){
			return $array;
		}else{
			return false;
		}
	}	

	function get_reservas($date_ini,$date_fin,$controllers,$alert = false){

		$order_type = 'DESC';
		$order_by = 'espacios.id';

		$this->db->where('reservas.dia_calendario >=',$date_ini);
	//	$this->db->where('reservas.dia_calendario <=',$date_fin);
		$this->db->order_by($order_by, $order_type);
		$this->db->where('espacios.edificio_id',$this->building_id);
		$this->db->where('reservas.unidad_id',$this->unidad_id);
	//	$this->db->where('reservas.user_id',$this->user_id);
		
		$rs = $this->espacios_model->reservados();

		$c=0;
		foreach ($rs->result() as $key => $value) {
			$array[$c] = array('id' => $value->id,
				'name'=>"Reserva de: ".$value->nombre_espacio . ' '.date("d/m",strtotime($value->date)).'  '.$value->desde,
				'startdate'=>$value->date,
				"color"=>"orange",
				"url"=>base_url($controllers.'/espacios_reservar/'.date('Y',strtotime($value->date)).'/'.date('m',strtotime($value->date)).'/'.$value->espacio_id));
			if($alert)
				$array[$c]['type'] = 'reservas';
			$c++;
		}

		if(isset($array)){
			return $array;
		}else{
			return false;
		}
	}	

	function get_propuestas($date_ini,$date_fin,$controllers,$alert=false){
		if ($this->ion_auth->in_group(INQUILINO)){
			return false;
		}

		$this->db->select('id,titulo,fecha_ini,fecha_fin');
		
		$this->db->where(array('edificio_id'=>$this->building_id,'fecha_fin <='=>$date_fin,'estado_id'=>ACTIVO));

		if($alert){
			$this->db->or_where(array('date(timestamp)'=>$alert));
		}

		$rs = $this->db->get('propuestas');

		$c=0;
		foreach ($rs->result() as $key => $value) {
			$array[$c] = array('id' => $value->id,
				'name'=>"Votación: ".$value->titulo,
				'startdate'=>$value->fecha_ini,
				'enddate'=>$value->fecha_fin,
				"color"=>"#239966",
				"url"=>base_url($controllers.'/propuestas_view/'.$value->id));
			if($alert)
				$array[$c]['type'] = 'Votación';

			$c++;
		}

		if(isset($array)){
			return $array;
		}else{
			return false;
		}

	}	

	function get_expensa($date_ini,$date_fin,$controllers,$alert=false){
		$this->db->select('id,titulo,fecha');
		
		$this->db->where(array('edificio_id'=>$this->building_id,'fecha >='=>$date_ini,'fecha <='=>$date_fin,'estado_id'=>ENVIADO));
		
		if($alert){
			$this->db->where(array('date(timestamp)'=>$alert));
		}
		$rs =  $this->db->get('recibos');
		$c=0;
		foreach ($rs->result() as $key => $value) {
			$array[$c] = array('id' => $value->id,
				'name'=>"Expensa Titulo: ".$value->titulo,
				'startdate'=>$value->fecha,
				"color"=>"red",
				"url"=>base_url($controllers.'/expensas_view/'.$value->id));
			if($alert)
				$array[$c]['type'] = 'Expensa cargada';

			$c++;
		}

		if(isset($array)){
			return $array;
		}else{
			return false;
		}
		
	}

}