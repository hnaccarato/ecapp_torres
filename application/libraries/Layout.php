<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Layout{

    public $obj;
    public $layout;
    public $folder;
    public $edificio;
    public $unidad_id;



    public function __construct($layout = "layout", $folder="show"){
        $this->obj =& get_instance();
        $this->setLayout($layout);
        $this->setFolder($folder);
    }

    public function setLayout($layout){
      $this->layout = $layout;
    }    

    public function setEdificio($edificio_id = false){
        if($edificio_id){
            $where['edificios.id'] = $edificio_id;
            $rs = $this->obj->edificios_model->read($where);
            if ($rs->num_rows() == 1) {
                $this->edificio = $rs->row();
            }
           
        }

    }
    
    public function setUnidad($unidad_id = false){
        $user = $this->obj->ion_auth->user()->row();
        if($unidad_id){
            $this->obj->db->where('unidades.id',$unidad_id);
            $rs = $this->obj->edificios_model->my_unidad($user->id);
            if ($rs->num_rows() == 1) {
                $this->edificio = $rs->row();
                $this->unidad_id = $unidad_id;
            }
        }
    }
    
    public function setFolder($folder){
      $this->folder = $folder;
    }

    public function view($view, $data=null, $return=false){
        
        $view = $this->folder . '/' . $view;
       
        $controllers = $this->obj->router->fetch_class();

        $loadedData = array();
        
        $loadedData['edificio'] = $this->edificio;
        $loadedData['user'] = $this->obj->ion_auth->user()->row();
        $loadedData['content_for_layout'] = $this->obj->load->view($view,$data,true);

        if ($controllers == 'propietarios' || $controllers == 'inquilinos' ){

            $this->obj->event_model->set_building($this->obj->edificio_id);
            $this->obj->event_model->set_user_id($this->obj->user->id);
            $this->obj->event_model->set_unity($this->unidad_id);

            $loadedData['event'] = $this->obj->event_model->get_event_now();

        }

        if (!$this->obj->ion_auth->is_admin())
            $loadedData['messenges'] = $this->obj->consultas_model->get_active();

        if($return){
            $output = $this->obj->load->view($this->layout, $loadedData, true);
            return $output;
        }else{
            $this->obj->load->view($this->layout, $loadedData, false);
        }
        
    }
}
?>