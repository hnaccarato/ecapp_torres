<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Circulars extends REST_Controller {


	function __construct()
	{
		parent::__construct();
		$this->load->model('edificios_model');
		$this->load->model('circular_model');

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
	* @get : http://{{URL}}/api/Circulars/getCirculars
	*
	* @param int buildingId
	* @param int statusId
	* @param int limit
	* @param int offset
	*
	* @return json array();
	*  
	*/ 

	public function getCirculars_get()
	{
		$buildingId = $this->input->get('buildingId');
		$statusId = $this->input->get('statusId');
		$limit = $this->input->get('limit');
		$offset = $this->input->get('offset');

		if(!$limit){
			$limit = 20;
		}

		if(!$offset){
			$offset = 0;
		}

		$order_type = 'DESC';
		$order_by = 'id';

		$this->db->limit($limit);
		$this->db->offset($offset);
		$this->db->order_by($order_by, $order_type);

		if($statusId > 0){
			$this->db->where('circular.estado_id ',$statusId);
		}
		$this->db->where('circular.estado_id >',TRUE);
		$this->db->where('circular.edificio_id',$buildingId);
		
		$circulars = $this->circular_model->read()->result();

		if ($circulars)
		{
			$this->response($circulars, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => FALSE,
				'message' => 'No circulars were found'
			], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}

	}


	/**
	* @get : http://{{URL}}/api/Circulars/getCircular
	*
	* @param int buildingId
	* @param int statusId
	* @param int circularId
	*
	* @return json array();
	*  
	*/ 
	public function getCircular_get()
	{
		
		$buildingId = $this->input->get('buildingId');
		$circularId = $this->input->get('circularId');

		$order_type = 'DESC';
		$order_by = 'id';

		$this->db->where('circular.estado_id >',TRUE);
		$this->db->where('circular.id',$circularId);
		$this->db->where('circular.edificio_id',$buildingId);
		
		$circulars = $this->circular_model->read()->row();

		if ($circulars)
		{
			$this->response($circulars, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => FALSE,
				'message' => 'No circulars were found'
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