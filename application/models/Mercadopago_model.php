<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mercadopago_model extends CI_Model
{

	public $edificio_id;
	public $access_mp = FALSE; 

	public function set_edificio($edificio_id){
		$this->edificio_id = $edificio_id;
	}

	public function get_mp(){
		$this->db->select('`mode`,
			`ci`,
			`cs`,
			`public_key_sandbox`,
			`access_token_sandbox`,
			`public_key_production`,
			`access_token_production`,
			`porcentaje`');

		$rs = $this->db->get_where('edificios',array('id'=>$this->edificio_id));
		$c=0;
		
		if($rs->num_rows()){
			$value = $rs->row_array();
			if(!empty($value->mode)){
				$this->access_mp = TRUE;
			}	
				
			return $value;
			
		}
	}


}