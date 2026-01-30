<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';


class Questions extends REST_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->model('Recibos_model');
        $this->load->model('consultas_model');
        
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
    * @get : http://{{URL}}/api/Questions/getQuestions
    *
    * @param int userId
    * @param int buildingId
    * @param int limit
    * @param int offset
    * 
    * @return json array();
    *  
    */ 

	public function getQuestions_get()
	{
		$userId = $this->input->get('userId');
		$buildingId = $this->input->get('buildingId');
		$limit = $this->input->get('limit');
		$offset = $this->input->get('offset');

		if(!$limit){
			$limit = 20;
		}

		if(!$offset){
			$offset = 0;
		}

		$order_type = 'DESC';
		$order_by = 'consultas.id';
		$this->db->limit($limit);
		$this->db->offset($offset);
		$this->load->model('consultas_model');
		$this->db->order_by($order_by, $order_type);

		$this->db->where('consultas.usaurio_id',$userId);
		$this->db->where('consultas.edificio_id',$buildingId);
		$questions  = $this->consultas_model->getQuestions()->result();

		if ($questions)
		{
			$this->response($questions, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => FALSE,
				'message' => 'No questions were found'
			], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}

	}

	/**
	* @get : http://{{URL}}/api/Questions/getQuestionsResponses
	*
	* @param int questionId
	* 
	* @return json array();
	*/ 

	public function getQuestionsResponses_get(){

		$questionId = $this->input->get('questionId');
		$responses = $this->consultas_model->get_respuesta($questionId)->result();

		if ($responses)
		{
			$this->response($responses, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => FALSE,
				'message' => 'No responses were found'
			], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
	}

	/**
	* @post : http://{{URL}}/api/Questions/sendResponse
	*
	* @param int questionId
	* @param int userId
	* @param text response
	* 
	* @return json array();
	*/ 
	public function sendResponse_post()
	{
		$data['consulta_id'] = $this->input->post('questionId');
		$data['fecha'] = date('Y-m-d');
		$data['user_id'] = $this->input->post('userId');
		$data['respuesta'] = $this->input->post('response');

		$this->db->insert('respuesta_consultas',$data);
		$data = $this->consultas_model->update(
			array('estado_id'=>ACTIVO),
			array('id'=>$data['consulta_id'])
		);

		if ($data)
		{
			$this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => FALSE,
				'message' => 'No responses were found'
			], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
				
	}

	/**
	* @get : http://{{URL}}/api/Questions/getQuestionsCategories
	* 
	* @return json array();
	*/ 
	public function getQuestionsCategories_get()
	{

		$questionsCategories = $this->tipo_consultas_model->read()->result();
		if ($questionsCategories)
		{
			$this->response($questionsCategories, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => FALSE,
				'message' => 'No questionsCategories were found'
			], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}

	}

	/**
	* @post : http://{{URL}}/api/Questions/createQuestion
	* 
	* @param int buildingId
	* @param int userId
	* @param int category
	* @param text  description
	* @param string title
	* 
	* 
	* @return json array();
	*/ 
	public function createQuestion_post()
	{
		$data['edificio_id'] = $this->input->post('buildingId');
		$data['usaurio_id'] = $this->input->post('userId');
		$data['tipo_consultas_id'] = $this->input->post('category');

		$data['fecha'] = date('Y-m-d');
		$data['descripcion'] = $this->input->post('description');
		$data['estado_id'] = ACTIVO;
		$data['detalle'] = $this->input->post('title');

		$data = $this->consultas_model->create($data);

		if ($data)
		{
			$this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
			$this->response([
			'status' => FALSE,
				'message' => 'No createQuestion were found'
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