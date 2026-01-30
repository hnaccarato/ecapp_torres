<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class permisos{


	private $not_basico = array('inquilinos','inquilinos_read','inquilinos_create','inquilino_excel','inquilino_update','inquilino_delete','propuestas_list','propuestas_excel','propuestas_read','propuestas_create','propuestas_update','espacios_list','espacios_reservar','espacios_load','espacios_update','espacios_create','add_turnos','add_periodo','coger_hora','espacios_read','espacios_excel','espacios_delete','espacios_active','get_reservas','espacios_informes','reservados','rechasados','espacios_informes_read','rechasados_excel','reservados_excel',);	

	private $not_medio = array('espacios_list','espacios_reservar','espacios_load','espacios_update','espacios_create','add_turnos','add_periodo','coger_hora','espacios_read','espacios_excel','espacios_delete','espacios_active','get_reservas','espacios_informes','reservados','rechasados','espacios_informes_read','rechasados_excel','reservados_excel',);



	function check(){
		$CI =& get_instance();
		$CI->load->library('ion_auth');
		$CI->load->library('session');
		

		if($CI->ion_auth->is_admin()){
			return true;
		}

		$class = $CI->router->fetch_class();
		$method = $CI->router->fetch_method(); 
		$edificio_id = $CI->session->userdata('edificio_id');
		if($class == 'auth' || $class == 'api' )
			return true;

		$category = $CI->edificios_model->get_category($edificio_id);		
		
		if($category == 3){
			return true;
		}		

		if($category == 2){
			if (in_array($method, $this->not_medio)){
				redirect($class.'/nuevo_producto');
				die();
			}
		}		

		if($category == 1){
			if (in_array($method, $this->not_basico)){
				redirect($class.'/nuevo_producto');
				die();
			}
		}



	


	}



}