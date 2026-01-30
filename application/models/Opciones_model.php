<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class opciones_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('opciones', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('opciones', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('opciones')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}



		return $this->db->get('opciones');
	}	

	public function get($opcion_id){
		return $this->db->get_where('opciones',array('id'=>$opcion_id))->row();
	}


	public function votos($propuesta_id){
		$sql="SELECT opciones.*,(SELECT COUNT(*) FROM votaciones 
        WHERE votaciones.opcion_id = opciones.id ) as voto  FROM opciones 
        where opciones.propuesta_id = $propuesta_id order by opciones.id asc";
        $rs = $this->db->query($sql);
        return $rs;
	}

	public function total_votos($propuesta_id){
		$sql ="SELECT COUNT(*)  as votos FROM votaciones WHERE propuesta_id = $propuesta_id";
        $rs = $this->db->query($sql);
        if($rs->num_rows() > 0 ){
        	return $rs;
        }else
        	return false;
	}

	
}
