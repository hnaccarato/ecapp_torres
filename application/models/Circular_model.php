<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class circular_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('circular', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('circular', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('circular')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select("circular.*,
			estados.nombre as estado,
			edificios.nombre as edificio,
			users.first_name as nombre,
			users.last_name as apellido");
		$this->db->join('edificios', 'circular.edificio_id = edificios.id');
		$this->db->join('users', 'circular.usuario_id = users.id');
		$this->db->join('estados', 'circular.estado_id = estados.id');
		return $this->db->get('circular');
	}

	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('circular',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}

	public function get($id){
		$where= array('circular.id'=>$id);
		$rs = $this->read($where);
		if($rs->num_rows() > 0)
			return $rs->row();
		else
			return false;
	}
	

	public function view_read($circular_id){
		
		$this->db->select('view_circular.id,
			users.first_name,
			users.last_name,
			users.email,
			unidades.name,
			unidades.departamento');

		$this->db->join('circular','circular.id = view_circular.circular_id');
		$this->db->join('users','users.id = view_circular.user_id');
		$this->db->join('unidades','unidades.id = view_circular.unidad_id');
		return $this->db->get_where('view_circular',array('circular_id'=>$circular_id));
	}


	public function desactivate(){
		$this->db->where('circular.fecha_envio <',date('Y-m-d'));
		$this->db->update('circular',array('estado_id'=>6));
	}
}
