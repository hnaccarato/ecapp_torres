<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class respuesta_contactos_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('respuesta_contactos', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('respuesta_contactos', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('respuesta_contactos')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->join('reclamos', 'respuesta_contactos.reclamo_id = reclamos.id');


		return $this->db->get('respuesta_contactos');
	}




	
}
