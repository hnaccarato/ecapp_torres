<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class reservas_pedido_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('reservas_pedido', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('reservas_pedido', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('reservas_pedido')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->join('edificios', 'reservas_pedido.edificio_id = edificios.id');
		$this->db->join('users', 'reservas_pedido.usuario_id = users.id');
		$this->db->join('espacios', 'reservas_pedido.espacios_id = espacios.id');


		return $this->db->get('reservas_pedido');
	}




	
}
