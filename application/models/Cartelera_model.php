<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class cartelera_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('cartelera', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('cartelera', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('cartelera')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select("cartelera.*,
			edificios.nombre as edificio,
			users.first_name as nombre,
			users.last_name as apellido");
		$this->db->join('edificios', 'cartelera.edificio_id = edificios.id');
		$this->db->join('users', 'cartelera.usuario_id = users.id');
		return $this->db->get('cartelera');
	}

	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('cartelera',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}

	
}
