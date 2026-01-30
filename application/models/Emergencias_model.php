<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class emergencias_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('emergencias', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('emergencias', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('emergencias')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select('emergencias.*,
			tipo_emergencias.name as tipo,
			edificios.nombre as edificio');
		$this->db->join('edificios', 'emergencias.edificio_id = edificios.id');
		$this->db->join('tipo_emergencias', 'emergencias.tipo_emergencia_id = tipo_emergencias.id');
		return $this->db->get('emergencias');
	}


	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('emergencias',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}

	
}
