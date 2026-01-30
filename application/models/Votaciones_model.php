<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class votaciones_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('votaciones', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('votaciones', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('votaciones')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->select('votaciones.id as votacion_id,
				opciones.titulo as opcion,
				opciones.id as opcion_id,
				propuestas.id as propuesta_id');
		$this->db->join('users', 'votaciones.usuario_id = users.id');
		$this->db->join('propuestas', 'votaciones.propuesta_id = propuestas.id');
		$this->db->join('opciones', 'votaciones.opcion_id = opciones.id');

		return $this->db->get('votaciones');
	}

	public function i_voted($propuesta_id){
		$rs = $this->db->get_where('votaciones',array('usuario_id'=>$this->user->id,'propuesta_id'=>$propuesta_id));
		//echo $this->db->last_query();
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}
	}



	
}
