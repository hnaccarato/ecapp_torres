<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class seguro_personal_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('seguro_personal', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('seguro_personal', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('seguro_personal')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select('seguro_personal.*,edificios.nombre as edificio');
		$this->db->join('edificios', 'seguro_personal.edificio_id = edificios.id');
		return $this->db->get('seguro_personal');
	}




	
}
