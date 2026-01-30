<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Unidad_archivos_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('unidad_archivos', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('unidad_archivos', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('unidad_archivos')){
			return true;
		}else{
			return false;
		}
	}

	public function read() {
		$anio = date('Y');
		$mes = date('m');
		$this->db->select('unidad_archivos.*,
			unidades.id as unidad_id,
			unidades.departamento,
			unidades.name as unidad');
		$this->db->join('unidades','unidad_archivos.unidad_id = unidades.id');
        $this->db->where('YEAR(unidad_archivos.date)', $anio);
        $this->db->where('MONTH(unidad_archivos.date)', $mes);
		return $this->db->get('unidad_archivos');
	}

}