<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class consultas_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}



	public function get($id){
		$rs = $this->read(array('consultas.id'=>$id));
		if($rs->num_rows() > 0){
			return $rs->row();
		}else{
			return false;
		}
	}

	public function create($data){

		if($this->db->insert('consultas', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('consultas', $data)){
			return true;
		}else{
			return false;
		}
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('consultas')){
			return true;
		}else{
			return false;
		}
	}

	public function getQuestions($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select('consultas.*,
			users.first_name as nombre,
			users.last_name as apellido,
			estados.id as estado_id,
			tipo_consultas.nombre as categoria,
			estados.nombre as estado,
			unidades.name as unidad,
			unidades.departamento as departamento,
			edificios.nombre as edificio');
		$this->db->join('estados', 'consultas.estado_id = estados.id');
		$this->db->join('edificios', 'consultas.edificio_id = edificios.id');
		$this->db->join('tipo_consultas', 'consultas.tipo_consultas_id = tipo_consultas.id');
		$this->db->join('users', 'consultas.usaurio_id = users.id');
		$this->db->join('users_unidad', 'users_unidad.user_id = users.id');
		$this->db->join('unidades', 'users_unidad.unidad_id = unidades.id');
		// $this->db->group_by('consultas.id');
		return $this->db->get('consultas');
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select('consultas.*,
			consultas.usaurio_id as user_id,
			users.first_name as nombre,
			users.last_name as apellido,
			estados.id as estado_id,
			tipo_consultas.nombre as categoria,
			estados.nombre as estado,
			unidades.name as unidad,
			unidades.departamento as departamento');
		$this->db->join('estados', 'consultas.estado_id = estados.id');
		$this->db->join('tipo_consultas', 'consultas.tipo_consultas_id = tipo_consultas.id');
		$this->db->join('users', 'consultas.usaurio_id = users.id');
		$this->db->join('users_unidad', 'users_unidad.user_id = users.id');
		$this->db->join('unidades', 'users_unidad.unidad_id = unidades.id');
		$this->db->group_by('consultas.id');
		return $this->db->get('consultas');
	}


	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('consultas',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}

	public function get_respuesta($consulta_id){
		$this->db->select('respuesta_consultas.*,
			users.first_name as nombre,
			users.last_name as apellido');
		$this->db->join('users', 'respuesta_consultas.user_id = users.id');
		$this->db->where('respuesta_consultas.consulta_id',$consulta_id);
		return $this->db->get('respuesta_consultas');
	}


	public function get_active(){
		if ($this->ion_auth->in_group(ADMINISTRADOR)){
			$this->db->where('estado_id',ACTIVO);
		}else{
			$this->db->where('estado_id',PENDIENTE);
			$this->db->where('usaurio_id',$this->user->id);
		}

		$rs = $this->db->get_where('consultas',array('edificio_id'=>$this->edificio_id));
		return $rs;
	}
	
}
