<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class archivos_propuesta_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('archivos_propuesta', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('archivos_propuesta', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('archivos_propuesta')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		return $this->db->get('archivos_propuesta');
	}




	
}
