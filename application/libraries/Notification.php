<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notification {
    
    public $ci;
    public $email;
    public $title;
    public $body;
    public $click_action  = 'FCM_PLUGIN_ACTIVITY';
    public $sound = "default";


   /* require_once (BASEPATH.'vendor/firebase/token-generator/src/TokenException.php');
    require_once (BASEPATH.'vendor/firebase/token-generator/src/TokenGenerator.php');
*/
    public function  __construct() {
        $this->ci =& get_instance();
        $this->email = $this->ci->load->library('email');
    }
    

    public function get_token(){
        
        echo BASEPATH.'vendor/firebase/token-generator/src/TokenException.php';
        die();

    }
   


    public function send_push_movile($to,$msg_payload = false,$data = false)
    {
        
        $serverKey = 'AAAAWVZM_2A:APA91bGczskB8vut_qR8YF2vrvowNjL5lBZAKAcljK5R6XKd9WZWiAeeBLkBv_rnypGhwJHsSQOBz41p4sfs_E7_jTzItbGvdfEXgpKp_5iwA7KsFyMXIKhhJeavFbKVeGh3XLRUb89M'; 

        $deviceIds = $to;
        
        $message = array(
            'title' => $this->title,
            'body' => $this->body,
        );

        $data = array(
            'registration_ids' => $deviceIds,
            'notification' => $message,
        );

        $dataJson = json_encode($data);

        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: key=' . $serverKey,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode == 200) {
            echo 'Notificaciones enviadas exitosamente.';
        } else {
            echo 'Error al enviar las notificaciones. Código HTTP: ' . $httpCode;
        }

    }

}
