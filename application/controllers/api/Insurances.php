<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Insurances extends REST_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->model('Recibos_model');
        $this->load->model('consultas_model');
        $this->load->model('seguro_edificio_model');
	
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    	$method = $_SERVER['REQUEST_METHOD'];
    	$this->load->library('Authorization_Token');
        if(!$this->verify_post()) {
            die();
        }
    }

/*
*
* revisar endpoint
*
*
*/
    /**
    * @get : http://{{URL}}/api/Insurances/getInsurances
    *
    * @param int buildingId
    * 
    * @return json array();
    *  
    */ 
	public function getInsurances_get()
	{
		$order_type = 'DESC';
		$order_by = 'seguros.id';
		$buildingId = $this->input->get('buildingId');

		$registers = $this->db->get_where('seguros', array('edificio_id' => $buildingId))->result_array();

		foreach($registers as $keys => $value) {
		   $registers[$keys]['file'] = base_url('upload/seguros/'.$registers[$keys]['file']);
		}


		if ($registers)
		{
		    $this->response($registers, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
		    $this->response([
		    'status' => FALSE,
		        'message' => 'No registers were found'
		    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
	}

	public function verify_post()
	{  
	    $headers = $this->input->request_headers(); 
	    if (isset($headers['Bearer'])) {
	        $decodedToken = $this->authorization_token->validateToken($headers['Bearer']);
	        if ($decodedToken) {
	            return true;
	        }
	    }
	    else {
	        return FALSE;
	    }
	      
	}

}	