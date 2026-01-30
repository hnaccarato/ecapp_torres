<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class reglamentos_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('reglamentos', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('reglamentos', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('reglamentos')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select('reglamentos.*',
			'edificios.nombre as edificio');
		$this->db->join('edificios','edificios.id = reglamentos.edificio_id');

		return $this->db->get('reglamentos');
	}




	
}
