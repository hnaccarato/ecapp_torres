<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class edificios_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){

		if($this->db->insert('edificios', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('edificios', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('edificios')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false,$params = false){
		$this->db->select('edificios.*,empresas.nombre as empresa,categorias.nombre as categoria');
		if($params){
			if(isset($params) && !empty($params))
			{
			    $this->db->limit($params['limit'], $params['offset']);
			}
		}


		if($where !== false){
			$this->db->where($where);
		}
		$this->db->join('empresas','empresas.id = edificios.empresa_id','left');
		$this->db->join('categorias','categorias.id = edificios.categoria_id','left');

		return $this->db->get('edificios');
	}


	public function count_all($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		return $this->db->get('edificios');
	}


	public function my_edificios($user_id){
		$this->db->join('edificios','edificios.id = users_edificio.edificio_id');
		$this->db->group_by('edificios.id');
		$rs = $this->db->get_where('users_edificio',array('users_edificio.user_id'=>$user_id));
		return $rs;
	}

	public function my_edificio_by_unidad($unidad_id){
		$user_id = $this->session->userdata('user_id');
		if(!empty($user_id)){
			$this->db->where('unidades.id',$unidad_id);
			$rs = $this->my_unidad($user_id);
			if($rs->num_rows() == 1){
				return $rs->row();
			}
			return false;
		}

		return false;
	}


	public function my_unidad($user_id){
		$this->db->select('unidades.*,
			users_unidad.user_id as user_id,
			edificios.id as edificio_id,
			edificios.imagen as imagen,
			edificios.nombre as edificio_nombre,
			edificios.direccion as edificio_direccion,
			edificios.telefono as edificio_telefono');
		$this->db->join('unidades','unidades.id = users_unidad.unidad_id');
		$this->db->join('edificios','edificios.id = unidades.edificio_id');
		$this->db->group_by('unidades.id');
		$rs = $this->db->get_where('users_unidad',array('users_unidad.user_id'=>$user_id));
		return $rs;
	}

	public function get_array_my_edificios($user_id){
		$rs = $this->my_edificios($user_id);
		$edificios = array();
		foreach ($rs->result() as $value) {
			$edificios[] = $value->edificio_id;
		}
		return $edificios;
	}
	

	public function add_edificios($user_id,$data){
		if(count($data) < 1 )
			return false;

		$this->db->delete('users_edificio',array('user_id'=>$user_id));
		foreach ($data as $key => $value) {
			$insert['user_id'] = $user_id;
			$insert['edificio_id'] =  $value;
			
			$rs = $this->db->get_where('users_edificio',array('user_id'=>$insert['user_id'],'edificio_id'=>$insert['edificio_id']));

			if(!$rs->num_rows()){
				$this->db->insert('users_edificio',$insert);
			}
		}
	}

	public function add_unidades($user_id,$edificio_id,$data){
		$this->db->delete('unidades',array('user_id'=>$user_id));
		foreach ($data as $key => $value) {
			$insert['user_id'] = $user_id;
			$insert['edificio_id'] = $edificio_id;
			$insert['name'] =  $value;
			$this->db->insert('unidades',$insert);
		}
	}

	public function get_edificio_by_hash($hash){
		$this->db->where('hash',$hash);
		$rs = $this->read();
		if($rs->num_rows()){
			return $rs->row();
		}else{
			return false;
		}
	}

	public function get_category($edificio_id){
		$this->db->select('categoria_id');
		$rs = $this->db->get_where('edificios',array('id'=>$edificio_id));
		if($rs->num_rows()){
			$category = $rs->row();
			return $category->categoria_id;
		}else{
			return false;
		}
	}
/*
	private function reservas($edificio_id){
		$rs = $this->db->select('id,permiso_reserva,ilim_permiso_reserva')
		->where('id',$edificio_id)
		->get('edificios');
		if($rs->num_rows() > 0 ){
		    $edificio = $rs->row();
		    return $edificio;
		}
	}

	public function super_reservas($edificio_id){
		$edificio = $this->reservas($edificio_id);
        if($edificio->permiso_reserva === 1 && $edificio->ilim_permiso_reserva === 1){
            return TRUE;
        }else{
        	return false;
        }
	}

	public function reservas_habilitado($edificio_id){
		$edificio = $this->reservas($edificio_id);
        if($edificio->permiso_reserva === 1){
            return TRUE;
        }else{
        	return false;
        }
	}
*/
}
