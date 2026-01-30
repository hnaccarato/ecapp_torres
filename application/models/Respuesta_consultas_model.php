<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Respuesta_consultas_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('respuesta_consultas', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('respuesta_consultas', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('respuesta_consultas')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->select("respuesta_consultas.*,
			users.first_name as nombre,
			users.last_name as apellido");
		$this->db->join('consultas', 'respuesta_consultas.reclamo_id = consultas.id');
		$this->db->join('users', 'respuesta_consultas.user_id = users.id');
		return $this->db->get('respuesta_consultas');
	}




	
}
