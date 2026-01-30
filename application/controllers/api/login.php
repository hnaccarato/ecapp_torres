<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Api extends CI_Controller {
	
	private $accessToken;

	public function __construct(){
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->model('users_model');

		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    	$method = $_SERVER['REQUEST_METHOD'];
    	if($method == "OPTIONS") {
    	    die();
    	}
	}


	function login()
	{
		$this->verifySecurityToken();

		$identity = $this->input->post('username');
		$password = $this->input->post('password');

		$response = new \stdClass();

		$correctCredentials = $this->ion_auth->login($identity, $password);

		if($correctCredentials){
			try {
				
				$where = "email =  '$identity' ";
				$user = $this->users_model->read($where)->row();
				$whereUsersGroup = ' users_groups.user_id = '.$user->id;
				$groups = $this->users_groups_model->read($whereUsersGroup)->row();
				$unities = $this->edificios_model->my_unidad($user->id)->result();
				$buildings = [];
	
				foreach ($unities as $unity) {
					$buildings[] = $this->edificios_model->my_edificio_by_unidad($unity->id);
				}
	
				$response->user = $user;
				$response->buildings = $buildings;
				$response->status = true;
				$response->groups = $groups;

			} catch (\Exception $e) {
				$response->status = false;
				$response->message = "Ha ocurrido un error, contacte con el administrador";		
				echo json_encode($response);

			}
		}else{

			$response->status = false;
			$response->message = "Credenciales incorrectas";

		}
		echo json_encode($response);
	}

	
	public function getTrustedPersons()
	{
		$userId = $this->input->post('userId');
		$trustedPersons = $this->users_model->getTrustedPersons($userId)->result();
		echo json_encode($trustedPersons);
	}


	public function addOrUpdateTrustedPerson()
	{
		$name = $this->input->post('name');
		$cell_phone = $this->input->post('cell_phone');
		$userId = $this->input->post('userId');
		$trustedPersonId = $this->input->post('trustedPersonId');

		$response = $this->users_model->addOrUpdateTrustedPerson($name,$cell_phone,$userId,$trustedPersonId);

		echo json_encode($response);
	}

	public function deleteTrustedPerson()
	{
		
		$trustedPersonId = $this->input->post('trustedPersonId');

		$this->users_model->deleteTrustedPerson($trustedPersonId);

	}


	/*
	
		PUSH NOTIFICATIONS 

	*/ 

	public function registerFCMToken()
	{
		$userId = (int) $this->input->post('userId');
		$FCMToken = $this->input->post('token');
		// echo json_encode('si');
		echo json_encode($this->users_model->registerFcmToken($userId,$FCMToken));

	}

	public function sendPushNotifications($to = false,$msg_payload = false,$data = false)
	{
		$apiAccessKey = 'AAAAeWrfkZY:APA91bH8S5uBBeU-heFQBsTY0oo_YSvwrqk-LhvGohI68H3jF9RKP5IOK5QfNB3szPnOojyXfei_R0z4ZB3YbI8ZAS2_zx4KQItEGCujYaFW0_MD6Kv5wUkaZzLqL-c39puUhlJ6Topz';
		
		$msg_payload = array (
			'body' => 'HOLIS',
			'title' => 'ASD',
			"sound" => "default",
			'click_action' => 'FCM_PLUGIN_ACTIVITY'
		);

        $data = array
        (
            "type" => 3,
            "title" => "Challenge Acepted",
            "chellengeId" => 40
        );

        $fields = array
        (  
            'to'    => $to ,
            'notification'  => $msg_payload,
            'data' => $data
        );
        
        $headers = array
        (
            'Authorization: key=' . $apiAccessKey,
            'Content-Type: application/json'
        );
        
        $ch = curl_init();

        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        var_dump($result);
		curl_close( $ch );
		
	}

	public function forgot_password_api(){

		$email = $this->input->post('email');

		$forgotten = $this->ion_auth->forgotten_password($email);

		echo json_encode($forgotten);

	}

	public function doPay()
	{
		$buildingId = $this->input->post('buildingId');
		$this->Mercadopago_model->set_edificio($buildingId);
		$congif = $this->Mercadopago_model->get_mp();
		
		
		
		MercadoPago\SDK::setAccessToken($congif['access_token_production']);

		
		$data = [];
		
		$data['mpData'] = [ 
			'transaction_amount' => (real) $this->input->post('amount'),
			'payer'=> ['email'=>$this->input->post('email')],
			'description' => $this->input->post('description'),
			'payment_method_id' => $this->input->post('payment_method'),
			'token' => $this->input->post('token'),
			'sponsor_id' => (int) 467717552,
			'installments' =>  1

		];
		
		// echo json_encode($congif);
		
		$payment = $this->pay($data,$congif['access_token_sandbox']);

        echo json_encode($payment);

	}

	public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        MercadoPago\SDK::setAccessToken($this->accessToken);

    }

    public function pay($paymentData,$accessToken)
    {

        MercadoPago\SDK::setAccessToken($accessToken);
        
        $customer = null;
        $save = false; 

        $params = $paymentData['mpData'];
        // $save = $paymentData['save'];
        
		$payment = $this->inflatePayment($params);
		
		$payment->save();

        return ['payment' => $payment->id,'error'=>$payment->error,'customer' => $customer];
        
    }

    //asocia tarjeta a un customer

    public function associateCard($payment)
    {
        // \MercadoPago\SDK::setAccessToken($this->accessToken);

        $customer = new MercadoPago\Customer();
        $customer->email = $payment->payer->email;
        $customer->save();

		$card = new MercadoPago\Card();
        $card->token = $payment->token;
        $card->customer_id = $customer->id;
        $card->save();

        return $customer;

    }

    // busca las tarjetas de un customer por el id

    public function findCustomerCards($customerId)
    {
        
        $customer = MercadoPago\Customer::find_by_id($customerId);
        $cards = $customer->cards;
        return $cards;

    }

    public function refund($payment_id)
    {
        
        $payment = \MercadoPago\Payment::find_by_id($payment_id);
        $payment->refund();

        return $payment;
        
    }

    // Asocia todos los parametros al pago.

    public function inflatePayment($data)
    {
        $payment = new MercadoPago\Payment();

		if($data){
			
			foreach ($data as $key => $value) {
				$payment->$key = $value;
            }
            
        }

        return $payment;
    }

    public function getPayment($paymentId)
    {
        $payment = \MercadoPago\Payment::find_by_id($paymentId);
        return $payment;
    }
}