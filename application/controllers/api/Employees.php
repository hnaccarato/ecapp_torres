<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Employees extends REST_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->model('encargado_model');
        
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
 		$this->load->library('Authorization_Token');
    	$method = $_SERVER['REQUEST_METHOD'];
    	$this->load->library('Authorization_Token');
       	if(!$this->verify_post()) {
            die();
        }
    }

    /**
    * @get : http://{{URL}}/api/Employees/getEmployees
    *
    * @param int buildingId
    *
    * @return json array();
    *  
    */ 
	public function getEmployees_get()
	{
		$order_type = 'DESC';
		$order_by = 'encargado.id';
		$search = false;
		
		$buildingId = $this->input->get('buildingId');
		$this->db->order_by($order_by, $order_type);
		$this->db->where('encargado.edificio_id',$buildingId);
		$data = $this->encargado_model->read()->result_array();

		if ($data)
		{
			$this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => 200,
				'message' => 'No Employees were found'
			], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
		}

	}	

	/***
	 * 
	 * 
	 * arreglar esto  			 
	 *  pasar imagenes a base 64
	 * 
	 * 
	 */

	/**
	* @get : http://{{URL}}/api/Employees/getEmployee
	*
	* @param int buildingId
	* @param int employeeId
	*
	* @return json array();
	*  
	*/ 
	public function getEmployee_get()
	{
		$order_type = 'DESC';
		$order_by = 'encargado.id';
		$search = false;
		
		$buildingId = $this->input->get('buildingId');
		$employeeId = $this->input->get('employee');

		$this->db->order_by($order_by, $order_type);
		$this->db->where('encargado.id',$employeeId);
		$this->db->where('encargado.edificio_id',$buildingId);
		$data = $this->encargado_model->read()->row();


		if ($data)
		{
			$this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => 200,
				'message' => 'No Employees were found'
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