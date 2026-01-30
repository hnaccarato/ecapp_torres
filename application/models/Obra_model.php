<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class obra_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('obra', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('obra', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('obra')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->select("obra.*,
			edificios.nombre as edificio");
		$this->db->join('edificios', 'obra.edificio_id = edificios.id');
		return $this->db->get('obra');
	}

	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('edificios',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}


	
}
