<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class send_email{

    private $edificio_id;
    private $unidad_id;
    private $template;
    private $contact = array();
    private $ci;
   // private $edificio_id;

    public function  __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('email');
        $this->ci->load->library('session');
        if(isset($this->ci->edificio_id))
            $this->edificio_id = $this->ci->edificio_id;
    }

    public function set_edificio_id($edificio_id){
        $this->edificio_id = $edificio_id;
    }

    public function new_expensas($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$data['expensa']->edificio_id))->row();
        $this->set_template('new_expensa',$data);

        $this->subject = 'Nueva expensa cargada';
        $array_emails = array_chunk($email, 4);            
            foreach($array_emails as $key => $values){
                if(is_array($values)){
                    $filters = array('seguridadtorreoro2835@gmail.com', 'consorcio.oro2835@gmail.com');
                    $values = array_diff($values, $filters);  
                    $this->send($values);
                }else{
                    return false;
                }
            }

    
    }   

    public function new_circular($email,$data){

        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$data['circular']->edificio_id))->row();
        $this->set_template('new_circular',$data);

        $this->subject = 'Nueva Circular Cargada';
        $array_emails = array_chunk($email, 4);            
        foreach($array_emails as $key => $values){
            if(is_array($values)){
                $this->send($values);
            }else{
                return false;
            }
        }
    
    }    

    public function new_welcome($email,$edificio){
        $data['edificio'] =$this->ci->db->get_where('edificios',array('id'=>$edificio))->row();
        $data['text'] = "<p class='text-center'>
                Sea Bienvenido al sistema de gestion de Consorcios
                <a href='".base_url()."' target='_blank'>Click Aquí</a>
            </p>";
        $this->set_template('new_welcome',$data);
        $this->subject = 'Building apps';
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }    

    public function new_pago($data){
        $email = array();
        
        $this->set_template('new_pago',array());
        $this->subject = 'Nuevo pago de expensa';
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }  

    public function new_notificar($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$this->edificio_id))->row();
        $this->set_template('new_notificar',$data);
        $this->subject = $data['subject'];
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }      

    public function new_asamblea($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$data['edificio_id']))->row();
        $this->set_template('new_asamblea',$data);

        $this->subject = 'Nueva asamblea cargada';
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }        

    public function new_consulta($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$this->edificio_id))->row();
        $this->set_template('new_consultas',$data);
        $this->subject = "Nueva consulta cargada";
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }      

    public function new_notifica_pago($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$this->edificio_id))->row();
        $this->set_template('new_notifica_pago',$data);
        $this->subject = "Nueva informacion de pago";
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }

    public function new_pago_comprobante($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$this->edificio_id))->row();
        $this->set_template('new_pago',$data);
        $this->subject = "Nueva pago ".$data['pago']->unidad;
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }        

    public function new_recibo($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$this->edificio_id))->row();
        $this->set_template('new_recibo',$data);
        $this->subject = "Recibo de Pago";
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }      

    public function new_respuesta($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$this->edificio_id))->row();
        $this->set_template('new_respuesta',$data);
        $this->subject = "Nueva respuesta";
        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }      

    public function new_register($email,$data){
        $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$this->edificio_id))->row();
        $this->set_template('new_register',$data);
        $this->subject = "Registro para ingreso al sistema de ".$data['edificio']->nombre;
        $this->send(array($email));
    }    

    public function new_reserva($email,$data){

         $data['edificio'] = $this->ci->edificios_model->read(array('edificios.id'=>$this->edificio_id))->row();
        $this->set_template('new_reserva',$data);
        $this->subject = 'Nueva reserva '.$data['espacio']->nombre_espacio;
        
        if(!empty($data['espacio']->email_notifica))
            array_push($email,$data['espacio']->email_notifica);
        $email_administradores = $this->ci->users_model->get_my_adminstrador($this->edificio_id);
        $email = array_merge($email ,$email_administradores);

        if(is_array($email)){
            $this->send($email);
        }else{
            return false;
        }
    
    }

    private function send($email){
    // solo para local host
     //   return true;

        
        try{
            if (!is_array($email)) {
                var_dump($email);
                return false;
            }
            $filterd_emails = array();
            foreach($email as $email){
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $error['Other'][] =  $email;
                }else{
                    $filtere_emails[] = $email;
                }
            }

            $config = array();
            //$config['protocol'] = 'mail';
            $config['smtp_host'] = '82.29.169.21';
            $config['smtp_user'] = 'info@ecapp.torresdebuenosaires.com';
            $config['smtp_pass'] = 'fagfrIxc'; 
            $config['smtp_crypto'] = 'tls';
            $config['smtp_port'] = 587;


            $this->ci->email->initialize($config);
            $message = $this->template;
            $this->ci->email->set_newline("\r\n");
            $this->ci->email->from('info@ecapp.torresdebuenosaires.com', EMAIL_FROMT);
            $this->ci->email->bcc($filtere_emails);
            $this->ci->email->subject($this->subject);
            $this->ci->email->message($message);

            if (!$this->ci->email->send()) {
                throw new Exception($this->ci->email->print_debugger());
                die('entro aca');
            } else {
                return true;
            }  

            $this->ci->email->clear();
        }catch(Exception $e){
            $this->ci->session->set_flashdata('error_message', "Error al envio no se pudo notificar a ningun propietario / Inquilino" );
        }

    }

    public function set_template($vista,$data){
        $this->template = $this->ci->load->view('email/'.$vista,$data,true); 
    }


    public function eliminar_email($emails, $filters) {
        // Definimos la función de devolución de llamada
        $callback = function($email) use ($emails_a_eliminar) {
            return !in_array($email, $emails_a_eliminar);
        };
        
        // Filtramos el array utilizando la función de devolución de llamada
        $nuevos_emails = array_filter($emails, $callback);
        
        // Devolvemos el nuevo array de correos electrónicos sin los correos electrónicos eliminados
        return $nuevos_emails;
    }


    
}