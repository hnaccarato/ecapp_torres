<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class users_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('users', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('users', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		return false;
		$this->db->where($where);
		if($this->db->delete('users')){
			return true;
		}else{
			return false;
		}
	}


	public function my_security($edificio_id){
		$this->db->select('users.*,
			if(users.active = 1,"Activo","Inactivo") as active');
		$this->db->join('users_groups','users_groups.user_id = users.id');
		$this->db->join('users_edificio','users_edificio.user_id = users.id');
		$this->db->where('users_edificio.edificio_id',$edificio_id);
		return $this->db->get('users');
	}
	

	public function read($where){
		$this->db->select('users.*,
			unidades.name as unidad,
			edificios.id as edificio_id,
			edificios.nombre as edificio,
			if(users.active = 1,"Activo","Inactivo") as active');
		$this->db->join('users_edificio','users_edificio.user_id = users.id');
		$this->db->join('users_unidad','users_unidad.user_id = users.id');
		$this->db->join('unidades','users_unidad.unidad_id = unidades.id');
		$this->db->join('users_groups','users_groups.user_id = users.id');
		$this->db->join('edificios','edificios.id = unidades.edificio_id');
		$this->db->where($where);
		$this->db->where('users_unidad.active',true);
		return $this->db->get('users');
	}

	public function my_users($edificio_id){
		$this->db->select('users.*,
			unidades.name as unidad,
			edificios.id as edificio_id,
			edificios.nombre as edificio,
			if(users.active = 1,"Activo","Inactivo") as active');
		//$this->db->join('users_edificio','users_edificio.user_id = users.id');
		$this->db->join('users_unidad','users_unidad.user_id = users.id');
		$this->db->join('unidades','users_unidad.unidad_id = unidades.id');
		$this->db->join('users_groups','users_groups.user_id = users.id');
		$this->db->join('edificios','edificios.id = unidades.edificio_id');
		$this->db->where('unidades.edificio_id',$edificio_id);
		$this->db->where('users_unidad.active',true);
		return $this->db->get('users');

	}	

	public function my_owners($edificio_id){
		$this->db->select('users.*,
			edificios.id as edificio_id,
			edificios.nombre as edificio,
			GROUP_CONCAT(unidades.name ORDER BY unidades.name SEPARATOR \',\' ) AS unidades,
			GROUP_CONCAT(unidades.departamento ORDER BY unidades.departamento SEPARATOR \',\' ) AS departamentos,
			if(users.active = 1,"Activo","Inactivo") as active');
		$this->db->join('users_unidad','users_unidad.user_id = users.id');
		$this->db->join('unidades','users_unidad.unidad_id = unidades.id');
		$this->db->join('users_groups','users_groups.user_id = users.id');
		$this->db->join('edificios','edificios.id = unidades.edificio_id');
		$this->db->where('unidades.edificio_id',$edificio_id);
		$this->db->where('users_unidad.active',true);
		$this->db->group_by('users.id');
		return $this->db->get('users');

	}


	public function get_my_unidades($user_id){
		 $rs = $this->db->get_where('users_unidad',array('user_id'=>$user_id,'active'=>true));
		 if($rs->num_rows() > 0){
		 	return $rs;
		 }
	}	

	public function get_email($user_id){
		 $rs = $this->db->get_where('users',array('id'=>$user_id));
		 if($rs->num_rows() > 0){
		 	return $rs->row()->email;
		 }
	}	

	public function get_push_movile($user_id){
		 $rs = $this->db->get_where('users',array('id'=>$user_id));
		 if($rs->num_rows() > 0){
		 	return array($rs->row()->fcm_token);
		 }
	}

	public function get_array($edificio_id){

		$this->db->select('users.email,users.email_fw');
		$this->db->join('users_groups','users_groups.user_id = users.id');
		$this->db->join('users_edificio','users_edificio.user_id = users.id');
		$rs = $this->db->get_where('users',array('users_edificio.edificio_id'=>$edificio_id));
		$email = array();
		$email_fw = array();
		foreach ($rs->result() as $value) {
			$email[] = $value->email;
			$email_fw[] = $value->email_fw;
		}
		$email_notifica = array_merge($email,$email_fw);
		$email = array_filter($email_notifica, "strlen");
		return $email;
	}	

	public function get_all_push_movile($edificio_id){

		$this->db->select('users.email,users.email_fw, users.fcm_token');
		$this->db->join('users_groups','users_groups.user_id = users.id');
		$this->db->join('users_edificio','users_edificio.user_id = users.id');
		$rs = $this->db->get_where('users',array('users_edificio.edificio_id'=>$edificio_id));

		$fcm_token = array();

		foreach ($rs->result() as $value) {
			if(!empty($value->fcm_token))
				$fcm_token[] = $value->fcm_token;
		}
		
		return $fcm_token;
	}
	
	public function add_propietario($user_id){
		$rs = $this->db->get_where('users_groups',array('user_id'=>$user_id,'group_id'=>PROPIETARIO));
		if($rs->num_rows() == 0 ){
			$this->db->insert('users_groups',array('user_id'=>$user_id,'group_id'=>PROPIETARIO));
		}
	}

	public function get_my_adminstrador($edificio_id){
		$this->db->where('users_groups.group_id',ADMINISTRADOR);
		return $this->get_array($edificio_id);
	}

	public function getTrustedPersons($userId = null)
	{
		
		$this->db->select('user_trusted_persons.*');
		$this->db->join('users','users.id = user_trusted_persons.user_id');
		$this->db->where('user_trusted_persons.user_id',$userId);

		return $this->db->get('user_trusted_persons');


	}

	public function addOrUpdateTrustedPerson($name,$cell_phone,$userId,$trustedPersonId)
	{
		if($trustedPersonId){
			$this->updateTrustedPerson($name,$cell_phone,$trustedPersonId);
		}else{
			$this->addTrustedPerson($name,$cell_phone,$userId);
		}
	}

	public function addTrustedPerson($name,$cell_phone,$userId)
	{
		$data = [
			'name' => $name,
			'cell_phone' => $cell_phone,
			'user_id' => $userId
		];

		$this->db->insert('user_trusted_persons', $data);

		return $this->db->insert_id();
	}

	public function updateTrustedPerson($name,$cell_phone,$trustedPersonId)
	{
		$data = [
			'name' => $name,
			'cell_phone' => $cell_phone,
		];

		$this->db->where('id',$trustedPersonId);
		$this->db->update('user_trusted_persons', $data);

	}

	public function deleteTrustedPerson($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('user_trusted_persons');
	}

	public function registerFcmToken($userId,$fcm_token)
	{

		$data = [
			'fcm_token' => $fcm_token
		];
		
		$this->db->where('id',$userId);
		$this->db->update('users', $data);

		return $this->db->last_query();

	}

	public function registerWebToken($userId,$fcm_token)
	{

		$data = [
			'web_token' => $fcm_token
		];
		
		$this->db->where('id',$userId);
		$this->db->update('users', $data);

		return $this->db->last_query();

	}

	public function delete_inquilino($user_id){
		$this->db->delete('users_unidad',array('unidad_id'=>$this->unidad_id,'user_id'=>$user_id));
		/*$this->db->delete('users_edificio',array('edificio_id'=>$this->edificio_id,'user_id'=>$user_id));
		$this->db->delete('users_groups',array('user_id'=>$user_id,'group_id'=>INQUILINO));*/
	}	

	public function delete_propietario($user_id){
		$this->db->delete('users_unidad',array('unidad_id'=>$this->unidad_id,'user_id'=>$user_id));
		/*$this->db->delete('users_edificio',array('edificio_id'=>$this->edificio_id,'user_id'=>$user_id));
	//	$this->db->delete('users_groups',array('user_id'=>$user_id,'group_id'=>INQUILINO));*/
	}

	public function delete_inquilino_byunidad($unidad_id = false){
		if(!$unidad_id){
			$unidad_id = intval($this->unidad_id);
		}
		$this->db->delete('users_unidad',array('unidad_id'=>$unidad_id,'grupo_id'=>INQUILINO));
	}	

	public function delete_seguridad($user_id = false){
		$this->db->delete('users_edificio',array('edificio_id'=>$this->edificio_id,'user_id'=>$user_id));
		$this->db->delete('users_groups',array('user_id'=>$user_id,'group_id'=>SEGURIDAD));
	}


}
