<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Baned_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('baneos', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('baneos', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('baneos')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->select("baneos.*,
			espacios.nombre_espacio as espacio");
		$this->db->join('espacios', 'baneos.espacio_id = espacios.id');
		return $this->db->get('baneos');
	}

	public function get_baneos($edificio_id){
	    $rs =  $this->db->get_where('baneos',
	        array(
	            'espacio_id'=>$espacio_id
	        )
	    );
	    return $rs->result();

	}

	public function new_baned($espacios, $user_id){
		$this->delete(['user_id'=>$user_id]);
		if(count($espacios) > 0){
			foreach ($espacios as $key => $value) {
				$data = [
					'espacio_id'=>$value,
					'user_id'=>$user_id,
					'update_user_id'=>$this->user->id,
				];
				$this->create($data);
			}
		}
	}

	public function is_baned($user_id,$espacio_id)
	{
		$rs = $this->db->get_where('baneos',
			[
				'user_id'=>$user_id,
				'espacio_id'=>$espacio_id
			]
		);
		if($rs->num_rows() > 0){
			return true;
		}
		return false;
	}

	public function get_array($data){
		$array = [];	
		foreach ($data->result() as $key => $value) {
			$array[] = $value->espacio_id;
		}
		return $array;	
	}
	
}
