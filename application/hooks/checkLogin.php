<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//comentario

class checkLogin{

    private $ci; 

    function  __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('ion_auth');
        $this->ci->load->library('my_style');
        $this->ci->my_style->load_company_by_host(APP_URL);
    }

    function user_is_logged_in(){
        
        $this->ci->load->library('ion_auth');
        $class = $this->ci->router->fetch_class();
        $method = $this->ci->router->fetch_method(); 
        if($class != 'auth'){
            if($this->ci->ion_auth->is_admin()){

                if($class != 'admin')
                    redirect('admin');
                return true;

            }else{

                /* if(!$this->ci->ion_auth->check_password()){
                redirect('auth/change_password');
                return true;
                die();
                }*/

                if($this->ci->session->userdata('unidad_id'))
                    return true;

                if($this->ci->ion_auth->get_users_groups()->num_rows() > 1){

                    if(empty($this->ci->session->userdata('selection'))){
                        $data['groups'] = $this->ci->ion_auth->get_users_groups();
                        echo   $this->ci->load->view('auth/load_groups',$data,true);
                        die();
                    }

                }else{

                    if($this->ci->ion_auth->in_group(ADMINISTRADOR)){

                        if($class != 'administrador')
                            redirect('administrador');
                        return true;
                    }

                    if($this->ci->ion_auth->in_group(PROPIETARIO)){
                        if($class != 'propietarios')
                            redirect('propietarios');
                        return true;

                    }

                    if($this->ci->ion_auth->in_group(INQUILINO)){
                        if($class != 'inquilinos')
                            redirect('inquilinos');
                        return true;

                    }          

                    if($this->ci->ion_auth->in_group(INTENDENTE)){
                        if($class != 'intendente')
                            redirect('intendente');
                        return true;

                    }            

                    if($this->ci->ion_auth->in_group(SEGURIDAD)){
                        if($class != 'seguridad')
                            redirect('seguridad');
                        return true;

                    }

                }
            }

        }

    }


}

?>