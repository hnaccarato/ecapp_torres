<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Legales_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('legales', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('legales', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('legales')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}

		$this->db->select('legales.*',
			'edificios.nombre as edificio');
		$this->db->join('edificios','edificios.id = legales.edificio_id');

		return $this->db->get('legales');
	}

	public function get_tipo($legal_id = false){
		
		if(!$legal_id){
			if($this->session->has_userdata('legal_id')){
				$legal_id = $this->session->userdata('legal_id');
			}else{
				redirect('administrador/legales_list/'.SEGUROS);
			}	
		}

		$rs = $this->db->get_where('tipo_legal',array('id'=>$legal_id));
		if($rs){
			return $rs->row();
		}else{
			return true;
		}
	}

	/*
	* function to add new nota*/

	function my_files($legal_id){
		$rs = $this->db->select('id,name,file')
		->where('legal_id',$legal_id)
		->get('file_legales');
		if($rs->num_rows() > 0)
			return $rs;
		else
			return false;
	}
	
}
