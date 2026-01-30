<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Inquilino_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	/*public function  read(){
		$this->db->select("users.id,
			edificios.nombre as edificio,
			unidades.name as unidad_nombre,
			users.unidad,
			users.email,
			users.phone,
			users.active as active_int,
			IF(users.active = 1,'Activo','Inactivo') as active,
			users.first_name as nombre,
			users.last_name as apellido");
		$this->db->join('unidades','inquilinos.unidad_id = unidades.id');
		$this->db->join('users','inquilinos.user_id = users.id');
		$this->db->join('edificios', 'inquilinos.edificio_id = edificios.id');
		$this->db->group_by('users.id');
		return $this->db->get('inquilinos');
	}*/

	public function read(){
        $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

		$this->db->select("users.id,
			edificios.nombre as edificio,
			unidades.name as unidad_nombre,
			users.unidad,
			users_unidad.id as users_unidad_id,
			users.email,
			users.phone,
			users.active as active_int,
			IF(users.active = 1,'Activo','Inactivo') as active,
			users.first_name as nombre,
			users.last_name as apellido");
		$this->db->join('unidades','users_unidad.unidad_id = unidades.id');
		$this->db->join('users','users_unidad.user_id = users.id');
		$this->db->join('edificios', 'unidades.edificio_id = edificios.id');
		$this->db->group_by('users.id');
		return $this->db->get_where('users_unidad',array('users_unidad.grupo_id'=>INQUILINO,
			'users_unidad.active'=>true));	
	}

	public function create($data){
		$rs = $this->db->get_where('users_unidad',
			array('unidad_id'=>$data['unidad_id'],'grupo_id'=>INQUILINO));
		
		if($rs->num_rows() > 0){
			foreach ($rs->result() as  $value) {
				$this->update(array('active'=>FALSE),array('id'=>$value->id));
			}
		}

		if($this->db->insert('users_unidad', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('users_unidad', $data)){
			return true;
		}else{
			return false;
		}
		
	}


	/*public function my_asignadas($user_id){
		$this->db->select('unidades.id as unidad_id, unidades.name as unidad_name');
		$this->db->join('users_unidad','users_unidad.unidad_id = inquilinos.unidad_id');
		$this->db->join('unidades','unidades.id = users_unidad.unidad_id ');
		return  $this->db->get_where('inquilinos',array('inquilinos.user_id'=>$user_id));
	}*/	

	public function my_asignadas($user_id){
		$this->db->select('unidades.id as unidad_id, unidades.name as unidad_name');
		$this->db->join('unidades','unidades.id = users_unidad.unidad_id ');
		return  $this->db->get_where('users_unidad',array('users_unidad.user_id'=>$user_id,'users_unidad.grupo_id'=>INQUILINO,'users_unidad.active'=>true));
	}
/*
	public function get_my_unidad($user_id){
		$rs = $this->db->get_where('inquilinos',array('user_id'=>$user_id));
		if($rs->num_rows() > 0){
			return $rs->row();
		}

	}*/
	public function get_my_unidad($user_id){
		$rs = $this->db->get_where('users_unidad',array('users_unidad.user_id'=>$user_id,'users_unidad.grupo_id'=>INQUILINO,'users_unidad.active'=>true));
		if($rs->num_rows() > 0){
			return $rs->row();
		}

	}


	public function delete($users_unidad_id){
		$rs = $this->db->get_where('users_unidad',array('id'=>$users_unidad_id));
		if($rs->num_rows() > 0){
			$inqulino = $rs->row();
			$inquilino_id = $inqulino->user_id; 
			$this->db->delete('users_unidad',array('id'=>$users_unidad_id));
			/*Si tiene mas de una unidad desactivo el usarurio*/
			$num_inquilino = $this->db->get_where('users_unidad',array('user_id'=>$inquilino_id));
			if($num_inquilino->num_rows() == 0){
				$this->db->where('id',$inquilino_id);
				$data = array('active'=>0);
				$this->db->update('users', $data);
			}
		}

	}

}