<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Asambleas_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('asambleas', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('asambleas', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('asambleas')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select("asambleas.*,
			estados.nombre as estado,
			edificios.nombre as edificio,
			users.first_name as nombre,
			users.last_name as apellido");
		$this->db->join('edificios', 'asambleas.edificio_id = edificios.id');
		$this->db->join('users', 'asambleas.usuario_id = users.id');
		$this->db->join('estados', 'asambleas.estado_id = estados.id');
		return $this->db->get('asambleas');
	}

	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('asambleas',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}

	public function get($id){
		$where= array('asambleas.id'=>$id);
		$rs = $this->read($where);
		return $rs->row();
	}
	
}
