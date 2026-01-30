<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class servicios_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('servicios', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('servicios', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('servicios')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select('servicios.*,edificios.nombre as edificio,tipo_servicio.nombre as tipo');
		$this->db->join('edificios', 'servicios.edificio_id = edificios.id');
		$this->db->join('tipo_servicio', 'servicios.tipo_servicios = tipo_servicio.id');
		return $this->db->get('servicios');

	}




	
}
