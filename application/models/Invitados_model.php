<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Invitados_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get($where=false){
		$rs = $this->read($where);
		if($rs){
			return $rs->row();
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		return $this->db->get('invitados');
	}

	public function create($data){

		if($this->db->insert('invitados', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($id,$data){
		$this->db->where('id',$id);
		if($this->db->update('invitados',$data)){
			return true;
		}else{
			return false;
		}	
	}	

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('invitados')){
			return true;
		}else{
			return false;
		}	
	}


}
