<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class users_groups_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('users_groups', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('users_groups', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('users_groups')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->join('groups', 'users_groups.group_id = groups.id');
		$this->db->join('users', 'users_groups.user_id = users.id');


		return $this->db->get('users_groups');
	}




	
}
