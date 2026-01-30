<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Expensa_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}


	public function create($data){

		if($this->db->insert('expensas', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}	

	public function get($edificio_id, $fecha){
		$this->db->select('expensas.*, unidades.name as unidad, unidades.departamento as departamento');
		$this->db->join('unidades','expensas.unidad_id = unidades.id');
		$rs = $this->db->get_where('expensas',
			array(
				'expensas.edificio_id'=>$edificio_id,
				'expensas.fecha <='=>$fecha
				)
		);
		
		if($rs){
			$expensas = $rs->result();
			return $expensas;
		}

		return false;
	}


}	