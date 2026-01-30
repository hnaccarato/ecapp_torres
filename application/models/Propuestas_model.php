<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class propuestas_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('propuestas', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('propuestas', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('propuestas')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->select("propuestas.*,
			edificios.nombre as edificio,
			users.first_name as nombre,
			estados.nombre as estado,
			users.last_name as apellido");
		$this->db->join('edificios', 'propuestas.edificio_id = edificios.id');
		$this->db->join('users', 'propuestas.usuario_id = users.id');
		$this->db->join('estados', 'propuestas.estado_id = estados.id');
		return $this->db->get('propuestas');
	}

	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('propuestas',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}


	public function desactivate(){
		$this->db->where('propuestas.fecha_fin <',date('Y-m-d'));
		$this->db->update('propuestas',array('estado_id'=>6));
	}
	
}
