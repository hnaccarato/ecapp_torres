<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';


class Legals extends REST_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->model('Legales_model');
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		$this->load->library('Authorization_Token');
    	$method = $_SERVER['REQUEST_METHOD'];
        if(!$this->verify_post()) {
            die();
        }
    }	



    /**
    * @get : http://{{URL}}/api/Legals/getLegals
    *
    * @param int buildingId
    * 
    * @return json array();
    *  
    */ 
    public function getLegals_get()
	{
		$buildingId = $this->input->get('buildingId');
		$this->db->where('legales.edificio_id',$buildingId);
		$registers  = $this->Legales_model->read()->result();
/*		echo $this->db->last_query();
		echo "<pre>";
		die();

*/
		if ($registers)
		{
		    $this->response($registers, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
		    $this->response([
		    'status' => FALSE,
		        'message' => 'No Legals were found'
		    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
	}    

	/**
    * @get : http://{{URL}}/api/Legals/getLegal
    *
    * @param int buildingId
    * @param int legal_id
    * 
    * @return json array();
    *  
    */ 
    public function getLegal_get()
	{
		$buildingId = $this->input->get('buildingId');
		$legal_id = $this->input->get('legal_id');
		$this->db->where('legales.edificio_id', $this->edificio_id);
		$this->db->where('legales.tipo_legal_id', $legal_id);
		$registers  = $this->Legales_model->read()->result();

		if ($registers)
		{
		    $this->response($registers, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
		    $this->response([
		    'status' => FALSE,
		        'message' => 'No Legals were found'
		    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
	}

	/**
	 * @get : http//{{url}}/api/legales/getOrganization 
	 * @param int buildingId
	 *
	 * @return json array(); 
	 */		

	public function getOrganization_get()
	{	
		$buildingId = $this->input->get('buildingId');
		$this->db->where('legales.edificio_id', $buildingId);
		$this->db->where('legales.tipo_legal_id', 7);
		$registers  = $this->Legales_model->read()->result();
	
		if ($registers)
		{
		    $this->response($registers, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
		    $this->response([
		    'status' => FALSE,
		        'message' => 'No Legals were found'
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