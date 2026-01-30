<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class seguro_edificio_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('seguro_edificio', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('seguro_edificio', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('seguro_edificio')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->select('seguro_edificio.*,
			edificios.nombre as edificio');
		$this->db->join('edificios', 'seguro_edificio.edificio_id = edificios.id');
		return $this->db->get('seguro_edificio');
	}




	
}
