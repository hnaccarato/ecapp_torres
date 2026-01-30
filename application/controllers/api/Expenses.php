<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';


class Expenses extends REST_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->model('Recibos_model');
        $this->load->model('Pagos_model');
        $this->load->model('Expensa_model');
        $this->load->model('Unidad_archivos_model');
        
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        $method = $_SERVER['REQUEST_METHOD'];
        $this->load->library('Authorization_Token');
        if(!$this->verify_post()) {
            die();
        }
    }

    public function test_get() {
        echo "test";
    }

    /**
    * @get : http://{{URL}}/api/Expenses/getUserExpensesByBuilding
    *
    * @param int buildingId
    * @return json array();
    *  
    */ 
    public function getUserExpensesByBuilding_get(){

   //     $this->verifySecurityToken();
        $buildingId = $this->input->get('buildingId');
        $where = "edificio_id = $buildingId";
        $order_by = "recibos.id DESC";
        $this->db->like('recibos.fecha', date("Y"));
        $expenses = $this->Recibos_model->read($where,$order_by)->result();

        if ($expenses)
        {
            $this->response($expenses, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No expenses were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
    * @get : http://{{URL}}/api/Expenses/getOutgoingsByExpense
    *
    * @param int expenseId
    * 
    * @return json array();
    *  
    */ 
    public function getOutgoingsByExpense_get()
    {

        $expenseId = $this->input->get('expenseId');

        $this->db->select('tipo_gastos.name as tipo, gastos.*');
        $this->db->join('tipo_gastos','gastos.tipo_gasto_id = tipo_gastos.id');
        $this->db->order_by('gastos.tipo_gasto_id');
        $outgoings= $this->db->get_where('gastos',array('gastos.recibo_id'=>$expenseId))->result();

        if ($outgoings)
        {
            $this->response($outgoings, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No my expenses were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
    * @get : http://{{URL}}/api/Expenses/getPendingExpenses
    *
    * @param int unidadId
    * @param int buildingId
    * 
    * @return json array();
    *  
    */ 
    public function getPendingExpenses_get()
    {
        $fecha_actual = date('Y-m-d');
        $fecha_anterior = date('Y-m-d', strtotime('-12 months'));

        $unidadId = $this->input->get('unidadId');
        $buildingId = $this->input->get('buildingId');

        $sql = "SELECT *
        FROM `recibos`
        WHERE `recibos`.`id` NOT IN (
            SELECT DISTINCT `recibo_id`
            FROM `pagos_users`
            WHERE `unidad_id` = $unidadId AND `active` = 1
        )   
            AND `recibos`.`fecha` BETWEEN '$fecha_anterior' and '$fecha_actual' 
            AND `recibos`.`edificio_id` = '$buildingId' 
            AND `recibos`.`pendiente_pago` = 1 
            AND `recibos`.`estado_id` = ".ENVIADO." ORDER BY `recibos`.`fecha` desc";

        $data['pending'] =  $this->db->query($sql)->result();

        $this->db->where('unidades.id', $unidadId);
        $data['cuenta_corriente'] = $this->Unidad_archivos_model->read()->row(); 



        if ($data['pending'])
        {
            $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No Pending expenses were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    /**
    * @get : http://{{URL}}/api/Expenses/uploadFile
    *
    * @param string dir
    * 
    * @return json array();
    *  
    */ 
    public function uploadFile_get(){

        $uploadfilename = $_FILES['file'];
        $uploadDir = $this->input->get('dir');

        if(!$uploadDir){
            $uploadDir = 'comprobante';
        }

        $tempFile = $uploadfilename['tmp_name'];

        $fileExtension = $this->getFileExtension($uploadfilename['name']);
        $fileName = $this->toAscii().'.'.$fileExtension;

        move_uploaded_file($tempFile, BASEPATH.'../upload/'.$uploadDir.'/'.$fileName);

        if ($fileName)
        {
            $this->response($fileName, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No upload were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
    /**
    * @get : http://{{URL}}/api/Expenses/myExpenses/
    *
    * @param int  $buildingId
    * @param date $date
    * @param int  $unidadId
    * 
    * @return json array();
    *  
    */ 
    public function myExpenses_get()
    {
        $unidadId = $this->input->get('unidadId');
        $dete = $this->input->get('dete');
        $buildingId = $this->input->get('buildingId');
        $this->db->where('expensas.unidad_id',$unidadId);
        $expense = $this->Expensa_model->get($buildingId,$dete);
        if ($expense)
        {
            $this->response($expense, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No my expense were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function toAscii() {
        return uniqid();
    }

    public function getFileExtension($fileName){
        $splitted = explode(".",$fileName);

        return $splitted[count($splitted)-1];
    }

    public function verifySecurityToken()
    {
        $token = $this->input->get('token');
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