<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Payments extends REST_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->model('Recibos_model');
        $this->load->model('Pagos_model');
		$this->load->model('Unidad_archivos_model');

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
    * @get : http://{{URL}}/api/Payments/getPayments
    *
    * @param int userId
    * @param int unidadId
    * 
    * @return json array();
    *  
    */ 

    public function getPayments_get(){
        
        $userId = $this->input->get('userId');
        $unidadId = $this->input->get('unidadId');

        $where = "pagos_users.user_id = $userId AND pagos_users.unidad_id = $unidadId";
        $payments = $this->Pagos_model->getPayments($where)->result();

        if ($payments)
        {
            $this->response($payments, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
            'status' => FALSE,
                'message' => 'No payments were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    /**
    * @get : http://{{URL}}/api/Payments/getPendingExpenses
    *
    * @param int unidadId
    * @param int buildingId
    * 
    * @return json array();
    *  
    */ 

    public function getPendingExpenses_get(){

        $unidadId = $this->input->get('unidadId');
        $buildingId = $this->input->get('buildingId');
        $select = "recibos.id,recibos.titulo, recibos.pendiente_pago";
        $this->db->where('recibos.estado_id',ENVIADO);
        $pendingExpenses = $this->Pagos_model->pending($unidadId, $buildingId, $select)->result();

        if ($pendingExpenses)
        {
            $this->response($pendingExpenses, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
            'status' => FALSE,
                'message' => 'No pending Expenses were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }


    }


    /**
    * @get : http://{{URL}}/api/Payments/getAllPayments
    *
    * @param int unidadId
    * @param int estadoId
    *
    * @return json array();
    *  
    */ 
    public function getAllPayments_get(){
        $unidadId = $this->input->get('unidadId');
        $buildingId = $this->input->get('buildingId');
        $estadoId = $this->input->get('estadoId');

		if(intval($estadoId
        ) > 0){
			$this->db->where('pagos_users.estado_id',$estadoId);	
		} else {
            $this->db->where_in('pagos_users.estado_id',array(9,11,10,1,3));
        }

		
		$this->db->where('pagos_users.unidad_id',$unidadId);
		$data  = $this->pagos_model->read()->result();
        if ($data)
        {
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
            'status' => FALSE,
                'message' => 'No pending Expenses were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
	}

    /**
    * @post : http://{{URL}}/api/Payments/finishPayment
    *
    * @param int expenses
    * @param <file> file
    * @param string description
    * @param int userId
    * @param int unidadId
    * @param string amount
    * 
    * @return json array();
    *  
    */ 
    public function finishPayment_post()
    {
        $cadena = $this->input->post('expenses');
        $expenses[] =  explode(",", $cadena);
        $data['file'] = $this->input->post('file');
        $data['descripcion'] = $this->input->post('description');
        $data['user_id'] = $this->input->post('userId');
        $data['unidad_id'] = $this->input->post('unidadId');
        $data['estado_id'] = PENDIENTE;
        $data['active'] = TRUE;
        $data['importe'] = $this->input->post('amount');
        $data['fecha'] = date("Y-m-d");
        

        if(!empty($_FILES['file']['name'])){

            $config['upload_path'] = BASEPATH.'../upload/comprobante/';
            $config['allowed_types'] = 'xlsx|txt|doc|xls|docx|pdf|gif|jpg|png|jpeg';
            $file = $_FILES['file']['name'];
            $file_data = pathinfo($file);
            $name_file = uniqid();
            $filename =  $name_file.'.'.$file_data['extension'];
            $config['file_name'] = $filename;
            
            if($this->upload($config,'file')){ 
                $data['file'] = $filename;
            }

        }

        $payments = [];

        foreach ($expenses as $value) {
            $data['recibo_id'] = intval($value);
            $pago_id = $this->pagos_model->create($data);

            $where = 'pagos_users.id = '.$pago_id;
            $payment = $this->pagos_model->getPayments($where)->result();

            $payments[] = $payment;
            // $this->send_pago($pago_id);
        }
        
        if (count($payment) > 0)
        {
            $this->response($payments, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
            'status' => FALSE,
                'message' => 'No payments were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }

    }

    private function upload($config,$imput_name)
    {
            if (!file_exists($config['upload_path'])) {
                mkdir($config['upload_path'], 777, true);
            }
    
            $config['overwrite'] = TRUE;
    
            $this->load->library('upload', $config);
    
            if ( ! $this->upload->do_upload($imput_name))
            {
                echo $this->upload->display_errors();
                die();
                return false;
            }
            else
            {
                return true;
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