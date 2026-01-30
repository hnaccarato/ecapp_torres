<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class contactos_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('contactos', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('contactos', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('contactos')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->join('edificios', 'contactos.edificio_id = edificios.id');
		$this->db->join('users', 'contactos.usuario_id = users.id');


		return $this->db->get('contactos');
	}

	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('contactos',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}


	
}
