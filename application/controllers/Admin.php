<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	private $user;

	public function __construct(){
		parent::__construct();
		$this->layout->setFolder('admin');
		$this->layout->setLayout('admin/layout');
		$this->load->library('googlemaps');
		$this->load->library('send_email');
		$this->user = $this->ion_auth->user()->row();
		if (!$this->ion_auth->is_admin()){
			redirect('accseslog');
		}
		//$this->send_email->set_edificio_id($this->edificio_id);
	}

	public function index(){
		$this->edificios_list();
		//$this->layout->view('index');
	}


	public function edificios_create(){
		
		if(!empty($_POST)){

			if(isset($_POST['nombre'])){
				$data['nombre'] = $this->input->post('nombre');
			}

			if(isset($_POST['direccion'])){
				$data['direccion'] = $this->input->post('direccion');
			}

			if(isset($_POST['description'])){
				$data['description'] = $this->input->post('description');
			}

			if(isset($_POST['position'])){
				$position = $this->input->post('position');
				$position = str_replace('(','',$position);
				$position = str_replace(')','',$position);
				$data['position'] = $position;
			}

			if(isset($_POST['telefono'])){
				$data['telefono'] = $this->input->post('telefono');
			}

			if(isset($_POST['imagen'])){
				$data['imagen'] = $this->input->post('imagen');
			}

			if(isset($_POST['empresa_id'])){
				$data['empresa_id'] = $this->input->post('empresa_id');
			}			

			if(isset($_POST['categoria_id'])){
				$data['categoria_id'] = $this->input->post('categoria_id');
			}
			
			$data['mode'] = $this->input->post('mode',true);
			$data['ci'] = $this->input->post('ci',true);
			$data['cs'] = $this->input->post('cs',true);
			$data['public_key_sandbox'] = $this->input->post('public_key_sandbox',true);
			$data['access_token_sandbox'] =$this->input->post('access_token_sandbox',true);
			$data['public_key_production'] = $this->input->post('public_key_production',true);
			$data['access_token_production'] = $this->input->post('access_token_production',true);
			$data['porcentaje'] = $this->input->post('porcentaje',true);

			if(!empty($_FILES['imagen']['name'])){
				$config['upload_path'] = BASEPATH.'../upload/edificios/';
				$config['allowed_types'] = 'xlsx|docx|pdf|gif|jpg|png|jpeg';
				$file = $_FILES['imagen']['name'];
				$file_data = pathinfo($file);
				$name_file = $this->toAscii($file_data['filename']);
				$filename =  $name_file.'.'.$file_data['extension'];
				$config['file_name'] = $filename;
				
				if($this->upload($config,'imagen')){ 
					$data['imagen'] = $filename;
				}
			}

			if(isset($_POST['cod_color'])){
				$data['cod_color'] = $this->input->post('cod_color');
			}

			if(!$this->edificios_model->create($data)){
				redirect('admin/edificios_error');
			}

			redirect('admin/edificios_list');
		}
		/*Google Maps*/
		/*
			$config['center'] = '-34.5767733,-58.4588453';
			$config['zoom'] = 15;
			$config['places'] = TRUE;
			$config['placesAutocompleteInputID'] = 'tex_map';
			$config['placesAutocompleteBoundsMap'] = TRUE; // set results biased towards the maps viewport
			$config['placesAutocompleteOnChange'] = 'maker = createMarker_map({ map: map, 
														position:this.getPlace().geometry.location,
														draggable:true
													});
													map.setCenter(this.getPlace().geometry.location);
													map.setZoom(15);
													set_position(this.getPlace().geometry.location);
													google.maps.event.addListener(maker, "dragend", function(event){
														set_position(event.latLng);
													});';

			$this->googlemaps->initialize($config);
			$data['map'] = $this->googlemaps->create_map();
		/*Fin google Maps*/ 
		$data['categorias'] = $this->db->get_where('categorias');
		$data['empresas'] = $this->empresas_model->get_all_empresas();

		$this->layout->view('edificios/create',$data);
	
	}


	public function edificios_read(){

		$config = $this->config->item('pagination');
		$params['limit'] = 10; 
        $params['offset'] = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        $config['base_url'] = site_url('admin/edificios_read?');
		$order_type = 'DESC';
		$order_by = 'edificios.id';
		$search = false;

		if($_POST){
			$order_by = $this->input->post('order_by');
			$order_type = $this->input->post('order_type');
			$search = $this->input->post('search');
		}
		

		$this->db->order_by($order_by, $order_type);

		if($search != ''){

			$searchables = array("edificios.direccion");

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
					
				}
			}
		}

		$data['registers']  = $this->edificios_model->read(false,$params);
		
		$this->db->order_by($order_by, $order_type);
		
		if($search != ''){

			if(isset($searchables) && count($searchables) > 0){
				$first_run = true;
				foreach($searchables as $searchable){
					if($first_run){
						$this->db->like($searchable, $search);
						$first_run = false;
					}else{
						$this->db->or_like($searchable, $search);
					}
					
				}
			}
		}

        $config['total_rows'] = $this->edificios_model->count_all()->num_rows();
        $this->pagination->initialize($config);
		$this->load->view('admin/edificios/read', $data);

	}

	public function edificios_list(){
		
		$this->layout->view('edificios/list');
	}

	public function edificios_excel(){

		$excelables = array("nombre",
							"direccion",
							"empresa",
							"plan");
		if(isset($excelables) && count($excelables) > 0){
			$filename = 'report_'.date('Y-m-d').'.xls';
			$objPHPExcel = new PHPExcel();

			
			$order_type = 'DESC';
			$order_by = 'edificios.id';
			$search = '';

			if($_GET){
				$limit = $this->input->get('limit');
				$order_by = $this->input->get('order_by');
				$order_type = $this->input->get('order_type');
				$search = urldecode($this->input->get('search'));
				$this->db->limit($limit);
			}

			$this->db->order_by($order_by, $order_type);
			if($search != ''){

				$searchables = array("edificios.nombre",
					"edificios.direccion",
					"empresas.nombre",
					"categorias.nombre");

				if(isset($searchables) && count($searchables) > 0){
					$first_run = true;
					foreach($searchables as $searchable){
						if($first_run){
							$this->db->like($searchable, $search);
							$first_run = false;
						}else{
							$this->db->or_like($searchable, $search);
						}
						
					}
				}
			}

			$rows =$this->edificios_model->read()->result_array();
			$first = true;

			foreach($excelables as $key => $excelable){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).(1), $excelable);
		
			}

			foreach ($rows as $row_key => $row){
				foreach($excelables as $key => $excelable){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($key).($row_key+2), $row[$excelable]);
				}
			}     

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			header('Cache-Control: max-age=1');
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header ('Cache-Control: cache, must-revalidate'); 
			header ('Pragma: public'); 
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			return true; 
		}
	}

	public function edificios_update($primary_key_value){
		
		$where['edificios.id'] = $primary_key_value;
		if(!empty($_POST)){

			if(isset($_POST['nombre'])){
				$data['nombre'] = $this->input->post('nombre');
			}

			if(isset($_POST['direccion'])){
				$data['direccion'] = $this->input->post('direccion');
			}

			if(isset($_POST['telefono'])){
				$data['telefono'] = $this->input->post('telefono');
			}

			if(isset($_POST['description'])){
				$data['description'] = $this->input->post('description');
			}			

			if(isset($_POST['empresa_id'])){
				$data['empresa_id'] = $this->input->post('empresa_id');
			}			

			if(isset($_POST['categoria_id'])){
				$data['categoria_id'] = $this->input->post('categoria_id');
			}
			
			$data['mode'] = $this->input->post('mode',true);
			$data['ci'] = $this->input->post('ci',true);
			$data['cs'] = $this->input->post('cs',true);
			$data['public_key_sandbox'] = $this->input->post('public_key_sandbox',true);
			$data['access_token_sandbox'] =$this->input->post('access_token_sandbox',true);
			$data['public_key_production'] = $this->input->post('public_key_production',true);
			$data['access_token_production'] = $this->input->post('access_token_production',true);
			$data['porcentaje'] = $this->input->post('porcentaje',true);


			if(isset($_POST['position'])){

				$position = $this->input->post('position');
				$position = str_replace('(','',$position);
				$position = str_replace(')','',$position);
				$data['position'] = $position;
				
			}

			if(!empty($_FILES['imagen']['name'])){

				$config['upload_path'] = BASEPATH.'../upload/edificios/';
				$config['allowed_types'] = 'xlsx|docx|pdf|gif|jpg|png|jpeg';
				$file = $_FILES['imagen']['name'];
				$file_data = pathinfo($file);
				$name_file = $this->toAscii($file_data['filename']);
				$filename =  $name_file.'.'.$file_data['extension'];
				$config['file_name'] = $filename;

				if($this->upload($config,'imagen')){ 
					$data['imagen'] = $filename;
				}

			}

			if(isset($_POST['cod_color'])){
				$data['cod_color'] = $this->input->post('cod_color');
			}

			if(!$this->edificios_model->update($data, $where)){
				redirect('admin/edificios_error');
			}

			redirect('admin/edificios_list');
		}

		$data['values'] = $this->edificios_model->read($where)->row();
		/*Google Maps*/
/*
			$config['center'] = '-34.5767733,-58.4588453';
			$config['zoom'] = 15;
			$config['places'] = TRUE;

			if(!empty($data['values']->position)){
				$config['center'] = $data['values']->position;
				$marker['position'] = $data['values']->position;
				$marker['draggable'] = true;
				$marker['ondragend'] = 'set_position(event.latLng);';
				$this->googlemaps->add_marker($marker);
			}

			$config['placesAutocompleteInputID'] = 'tex_map';
			$config['placesAutocompleteBoundsMap'] = TRUE; // set results biased towards the maps viewport
			$config['placesAutocompleteOnChange'] = 'maker = createMarker_map({ map: map, 
														position:this.getPlace().geometry.location,
														draggable:true
													});
													map.setCenter(this.getPlace().geometry.location);
													map.setZoom(15);
													set_position(this.getPlace().geometry.location);
													google.maps.event.addListener(maker, "dragend", function(event){
														set_position(event.latLng);
													});';

			$this->googlemaps->initialize($config);
			$data['map'] = $this->googlemaps->create_map();
		/*Fin google Maps*/
		$data['categorias'] = $this->db->get_where('categorias');
		$data['empresas'] = $this->empresas_model->get_all_empresas();
		
		$this->layout->view('edificios/update', $data);
	}

	public function edificios_delete($primary_key_value){

		$where['id'] = $primary_key_value;
		
		if(!$this->edificios_model->delete($where)){
			redirect('admin/edificios_error');
		}

		redirect('admin/edificios_list');

	}

/******************** *Users ***********************/

public function user_read(){
	$this->layout->view('user_read');	
}




/*******************     Empresas   **********************/

	public function empresa__list(){
		$this->layout->view('empresa/index');
	}

	
	public function empresas_read()
	 {
	     $list = $this->empresas_model->get_datatables();
	  //   echo $this->db->last_query();
	     $data = array();
	     $no = $_POST['start'];
	     foreach ($list as $e) {
	         $no++;
	         $row = array();
	         $row[] = $e->id;
	         $row[] = $e->nombre;           
	           $row[] = "<a href=\"".site_url('admin/empresa_update/'.$e->id)."\" class=\"btn btn-info btn-xs\"><span class=\"fa fa-pencil\"></span> Edit</a> 
	                       <a href=\"".site_url('admin/empresa_delete/'.$e->id)."\" class=\"btn btn-danger btn-xs\"><span class=\"fa fa-trash\"></span> Delete</a>";
	         $data[] = $row;
	     }
	
	     $output = array(
	                     "draw" => $_POST['draw'],
	                     "recordsTotal" => $this->empresas_model->count_all(),
	                     "recordsFiltered" => $this->empresas_model->count_filtered(),
	                     "data" => $data,
	             );
	     //output to json format
	     echo json_encode($output);
	 }

    public function empresa__create()
    {   
        $this->load->library('form_validation');

		$this->form_validation->set_rules('nombre','Nombre','required');
		$this->form_validation->set_rules('color','Color','required');
		
		if($this->form_validation->run())     
        {   
           
            $params = array(
				'nombre' => $this->input->post('nombre',TRUE),
				'color' => $this->input->post('color',TRUE),
				'color_icono' => $this->input->post('color_icono',TRUE),
				'url' => $this->input->post('color',TRUE),
            );
            

            if(!empty($_FILES['logo']['name'])){

				$config['upload_path'] = BASEPATH.'../upload/empresa/logo/';
				$config['allowed_types'] = 'xlsx|docx|pdf|gif|jpg|png|jpeg';
				$file = $_FILES['logo']['name'];
				$file_data = pathinfo($file);
				$name_file = $this->toAscii($file_data['filename']);
				$filename =  $name_file.'.'.$file_data['extension'];
				$config['file_name'] = $filename;
				
				if($this->upload($config,'logo')){ 
					$params['logo'] = $filename;
				}

			}            

			if(!empty($_FILES['logo_login']['name'])){

				$config['upload_path'] = BASEPATH.'../upload/empresa/logo/';
				$config['allowed_types'] = 'xlsx|docx|pdf|gif|jpg|png|jpeg';
				$file = $_FILES['logo_login']['name'];
				$file_data = pathinfo($file);
				$name_file = $this->toAscii($file_data['filename']);
				$filename =  $name_file.'.'.$file_data['extension'];
				$config['file_name'] = $filename;
				
				if($this->upload($config,'logo_login')){ 
					$params['logo_login'] = $filename;
				}

			}




            $empresa_id = $this->empresas_model->add_empresa($params);
            redirect('admin/empresa__list');
        }
        else
        {            
          
            $this->layout->view('empresa/add');
        }
    }  

    /*
     * Editing a empresa
     */
    public function empresa_update($id)
    {   
        // check if the empresa exists before trying to edit it
        $data['empresa'] = $this->empresas_model->get_empresa($id);
        
        if(isset($data['empresa']['id']))
        {
            $this->load->library('form_validation');

			$this->form_validation->set_rules('nombre','Nombre','required');
			$this->form_validation->set_rules('color','Color','required');
		
			if($this->form_validation->run())     
            {   
                
                $params = array(
					'nombre' => $this->input->post('nombre',TRUE),
					'color' => $this->input->post('color',TRUE),
					'color_icono' => $this->input->post('color_icono',TRUE),
					'url' => $this->input->post('url',true),
                );

                if(!empty($_FILES['logo']['name'])){

    				$config['upload_path'] = BASEPATH.'../upload/empresa/logo/';
    				$config['allowed_types'] = 'xlsx|docx|pdf|gif|jpg|png|jpeg';
    				$file = $_FILES['logo']['name'];
    				$file_data = pathinfo($file);
    				$name_file = $this->toAscii($file_data['filename']);
    				$filename =  $name_file.'.'.$file_data['extension'];
    				$config['file_name'] = $filename;
    				
    				if($this->upload($config,'logo')){ 
    					$params['logo'] = $filename;
    				}

    			}            

    			if(!empty($_FILES['logo_login']['name'])){

    				$config['upload_path'] = BASEPATH.'../upload/empresa/logo/';
    				$config['allowed_types'] = 'xlsx|docx|pdf|gif|jpg|png|jpeg';
    				$file = $_FILES['logo_login']['name'];
    				$file_data = pathinfo($file);
    				$name_file = $this->toAscii($file_data['filename']);
    				$filename =  $name_file.'.'.$file_data['extension'];
    				$config['file_name'] = $filename;
    				
    				if($this->upload($config,'logo_login')){ 
    					$params['logo_login'] = $filename;
    				}

    			}


                $this->empresas_model->update_empresa($id,$params);            
                redirect('admin/empresa__list');
            }
            else
            {
                $this->layout->view('empresa/edit',$data);
            }
        }
        else
            show_error('The empresa you are trying to edit does not exist.');
    } 


    /*
     * Deleting empresa
     */

    public function empresa_delete($id)
    {
        $empresa = $this->empresas_model->get_empresa($id);

        // check if the empresa exists before trying to delete it
        if(isset($empresa['id']))
        {
            $this->empresas_model->delete_empresa($id);
            redirect('admin/empresa__list');
        }
        else
            show_error('The empresa you are trying to delete does not exist.');
    }



/*******************Fin de Empresas**********************/
	public function upload($config,$imput_name)
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



	public function toAscii($str, $replace=array(), $delimiter='-') {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring = $characters[rand(0, strlen($characters))];
        }
        return md5($randstring);

	}

	public function get_name(){

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
		    $randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;

	}


}