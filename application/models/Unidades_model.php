<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class unidades_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('unidades', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}


	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('unidades', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		
		if($this->db->delete('unidades',$where)){
		//	$this->db->delete('users_unidad',array('unidad_id'=>$unidad_id));
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		return $this->db->get('unidades');
	}

	public function my_unidades($user_id){
		$this->db->select('users_unidad.*,unidades.name as unidad_name');
		$this->db->join('unidades','unidades.id = users_unidad.unidad_id');
		$this->db->group_by('unidades.id');
		$rs = $this->db->get_where('users_unidad',array('users_unidad.user_id'=>$user_id));
		return $rs;
	}	

	public function get_unidades_ocupadas($edificio_id){
        $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		$this->db->select('users_unidad.*,
			unidades.id as unidad_id,
			unidades.name as unidad_name,
			unidades.departamento');
		$this->db->join('unidades','unidades.id = users_unidad.unidad_id');
		$this->db->group_by('unidades.id');
		$rs = $this->db->get_where('users_unidad',array('unidades.edificio_id'=>$edificio_id));
		return $rs;
	}	

	public function get_email($unidad_id ,$user_id = false){

		$this->db->select('users.email');
		$this->db->join('unidades','unidades.id = users_unidad.unidad_id');
		$this->db->join('users','users.id = users_unidad.user_id');
		
		$email = array();

		if($user_id)
			$this->db->where('users.id',$user_id);

		$rs = $this->db->get_where('users_unidad',array('users_unidad.unidad_id'=>$unidad_id));
		foreach ($rs->result() as $value) {
			$email[] = $value->email;
		}

		return $email;

	}

	public function get_array($user_id){
		$rs = $this->db->get_where('users_unidad',array('user_id'=>$user_id));
		$unidades = array();
		foreach ($rs->result() as $value) {
			$unidades[] = $value->unidad_id;
		}
		return $unidades;
	}
	

	public function add_unidad($user_id,$data,$group = false){
		$sql = "DELETE users_unidad FROM users_unidad
			  	JOIN unidades ON unidades.id = users_unidad.unidad_id
			  	WHERE users_unidad.user_id = ? and unidades.edificio_id = ?";
		$this->db->query($sql,array($user_id,$this->edificio_id));
		foreach ($data as $key => $value) {
			$insert['user_id'] = $user_id;
			$insert['unidad_id'] =  $value;
			$insert['grupo_id'] =  $group;
			$this->db->insert('users_unidad',$insert);
		}
	}

	public function add_unidades($user_id,$edificio_id,$data){
		
		$sql = "DELETE users_unidad FROM users_unidad
			  	JOIN unidades ON unidades.id = users_unidad.unidad_id
			  	WHERE users_unidad.user_id = ? and unidades.edificio_id = ?";
		$this->db->query($sql,array($user_id,$this->edificio_id));

		foreach ($data as $key => $value) {
			$insert['user_id'] = $user_id;
			$insert['edificio_id'] = $edificio_id;
			$insert['name'] =  $value;
			$this->db->insert('unidades',$insert);
		}
	}

	public function disponibles($edificio_id){
		$sql = "SELECT  l.id, l.name, l.departamento 
				FROM    unidades l
				WHERE   NOT EXISTS
				        (
				        SELECT  id
				        FROM    users_unidad r
				        WHERE   r.unidad_id = l.id
				        )  and l.edificio_id = $edificio_id";
		$rs = $this->db->query($sql);
		return $rs;
	}		

	public function actives($edificio_id){
		$sql = "SELECT  l.id, l.name, l.departamento 
				FROM    unidades l
				WHERE   EXISTS
				        (
				        SELECT  id
				        FROM    users_unidad r
				        WHERE   r.unidad_id = l.id
				        )  and l.edificio_id = $edificio_id";
		$rs = $this->db->query($sql);
		return $rs;
	}	
/*
	public function my_disponibles($user_id){
		$sql = "SELECT  l.id as unidad_id, l.name as unidad_name
				FROM    unidades l
				inner join users_unidad uu
				on (uu.unidad_id = l.id )
				WHERE   NOT EXISTS
				(
				SELECT  id
				FROM    inquilinos i
				WHERE   i.unidad_id = uu.unidad_id
				)  and uu.user_id = $user_id";
		$rs = $this->db->query($sql);
		return $rs;
	}
	
	public function my_asignadas($user_id){
		$this->db->select('unidades.id as unidad_id, unidades.name as unidad_name');
		$this->db->join('users_unidad','users_unidad.unidad_id = inquilinos.unidad_id');
		$this->db->join('unidades','unidades.id = users_unidad.unidad_id ');
		return  $this->db->get_where('inquilinos',array('inquilinos.user_id'=>$user_id));
	}
*/
	public function get_user($unidad_id){
		 $rs = $this->db->get_where('users_unidad',array('unidad_id'=>$unidad_id));
		 if($rs->num_rows() > 0){
		 	
		 	if($rs->num_rows() == 1){
		 		return $rs->row();
		 	}else{	
		 		$rs =  $this->db->get_where('users_unidad',
		 			array('unidad_id'=>$unidad_id,'grupo_id'=>INQUILINO));
		 		return $rs->row();
		 	}	
		 	
		 }

	}	

	public function get_user_unidad($where){
		$this->db->select('unidades.name,unidades.id');
		$this->db->join('unidades','unidades.id = users_unidad.unidad_id');
		$rs = $this->db->get_where('users_unidad',$where);
		if($rs->num_rows() > 0){
			return $rs;
		}else{
			return false;
		}

	}

	public function get_edificio_by_hash($hash){
		$rs = $this->db->select('edificios.id,
			edificios.nombre,
			edificios.direccion,
			registeruser.email')
		->join('unidades','unidades.id = registeruser.unidad_id')
		->join('edificios','edificios.id = unidades.edificio_id')
		->where('registeruser.hash',$hash)
		->group_by('edificios.id')
		->get('registeruser');
		
		if($rs){
			return $rs->row();
		}

	}	

	public function get_unidades_by_hash($hash){
		$rs = $this->db->select('registeruser.unidad_id')
		->where('registeruser.hash',$hash)
		->get('registeruser');
		
		if($rs){
			$data = array();
			foreach ($rs->result() as $value) {
				$data[] = $value->unidad_id;
			}
			return $data;
		}
		return false;
	}

	public function my_unidad($user_id,$unidad_id){
		
		$this->db->select('unidades.name,
			unidades.departamento,
			edificios.nombre as edificio');
		$this->db->join('unidades','users_unidad.unidad_id = unidades.id');
		$this->db->join('edificios','unidades.edificio_id = edificios.id');
		
		$rs = $this->db->get_where('users_unidad',
			array('users_unidad.user_id'=>$user_id,
				'unidades.id'=>$unidad_id));
		
		if($rs){
			$unidad = $rs->row();
			return $unidad;
		}

		return false;
	}


	public function get_my_unidad($edificio_id, $unidad, $depto){
		 $rs = $this->db->get_where('unidades',
		 	array(
		 		'edificio_id' => $edificio_id,
		 		'name'=>$unidad
		 	)
		 );

		if($rs->num_rows() > 0){
			return $rs->row();
		}
	}	
	
}
