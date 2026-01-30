<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class recibos_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('recibos', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('recibos', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('recibos')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false,$orderBy = false){
		if($where !== false){
			$this->db->where($where);
		}
		if($orderBy !== false){
			$this->db->order_by($orderBy);
		}
		$this->db->select('recibos.*,
			users.first_name as nombre,
			users.last_name as apellido,
			recibos.pendiente_pago,
			edificios.nombre as edificio,');
		$this->db->join('edificios', 'recibos.edificio_id = edificios.id');
		$this->db->join('users', 'recibos.usuarios_id = users.id');
		return $this->db->get('recibos');
	}


	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('recibos',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}

	public function get($id){
		$rs = $this->db->get_where('recibos',array('id'=>$id));
		if($rs->num_rows() > 0){
			return $rs;
		}else{
			return false;
		}

	}

	public function get_row($id){
		$rs = $this->get($id);
		if($rs)
			return $rs->row();
	}

	
}
