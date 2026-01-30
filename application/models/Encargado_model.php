<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class encargado_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('encargado', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('encargado', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('encargado')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select('encargado.*,edificios.nombre as edificio,cargos.nombre as cargo');
		$this->db->join('edificios', 'encargado.edificio_id = edificios.id');
		$this->db->join('cargos','cargos.id = encargado.cargo_id');
		//$this->db->order_by('encargado.id','DESC');
		return $this->db->get('encargado');
	}

	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('encargado',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}
	
}
