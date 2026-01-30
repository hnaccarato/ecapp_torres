<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class pagos_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function create($data){
		
		if($this->db->insert('pagos_users', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('pagos_users', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$rs = $this->read($where);
		if(!$rs->num_rows())
			return false;
		$data = $rs->row();

		$this->db->where(array('id'=>$data->id));
		if($this->db->delete('pagos_users')){
			return true;
		}else{
			return false;
		}
	}	

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->select('
			pagos_users.*,
			unidades.name as unidad,
			unidades.departamento as departamento,
			edificios.nombre as edificio,
			edificios.id as edificio_id,
			recibos.titulo as titulo,
			recibos.fecha as expensas,
			estados.id as estado_id,
			estados.nombre as estado,
			users.first_name as nombre,
			users.last_name as apellido,
			users.email as email,
			pagos_users.fecha as fecha_pago,
			pagos_users.file as comprobante,
			pagos_users.descripcion as detalle
		');
		$this->db->join('recibos', 'recibos.id = pagos_users.recibo_id');
		$this->db->join('edificios', 'recibos.edificio_id = edificios.id');
		$this->db->join('users', 'pagos_users.user_id = users.id');
		$this->db->join('unidades', 'pagos_users.unidad_id = unidades.id');
		$this->db->join('estados', 'pagos_users.estado_id = estados.id');
		$this->db->group_by('pagos_users.id');
		$this->db->where('pagos_users.active',TRUE);	
		return $this->db->get('pagos_users');
	}

	public function getPayments($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->distinct();

		$this->db->select('
			pagos_users.*,
			unidades.name as unidad,
			unidades.departamento as departamento,
			edificios.nombre as edificio,
			edificios.id as edificio_id,
			recibos.titulo as titulo,
			recibos.fecha as expensas,
			estados.id as estado_id,
			estados.nombre as estado,
			users.first_name as nombre,
			users.last_name as apellido,
			users.email as email,
			pagos_users.fecha as fecha_pago,
			pagos_users.file as comprobante,
			pagos_users.descripcion as detalle
		');

		
		$this->db->join('recibos', 'recibos.id = pagos_users.recibo_id');
		$this->db->join('edificios', 'recibos.edificio_id = edificios.id');
		$this->db->join('users', 'pagos_users.user_id = users.id');
		$this->db->join('users_unidad', 'users_unidad.user_id = users.id');
		$this->db->join('unidades', 'users_unidad.unidad_id = unidades.id');
		$this->db->join('estados', 'pagos_users.estado_id = estados.id');
		$this->db->order_by('pagos_users.id','DESC');

		// $this->db->group_by('id');
		$this->db->where('pagos_users.active',TRUE);	
		return 	$this->db->get('pagos_users');
	}

	public function get($id){
		$rs = $this->read(array('pagos_users.id'=>$id));
		if($rs->num_rows() > 0)
			return $rs->row();
		else
			return false;
	}


	public function pending($unidad_id,$edificio_id, $select = false) {	
		
		if ($select) {
			$this->db->select($select);
		}else {
			$this->db->select('recibos.*');
		}

		$this->db->join('edificios', 'recibos.edificio_id = edificios.id');
		$this->db->where("recibos.id NOT IN ('SELECT recibo_id FROM pagos_users where unidad_id = $unidad_id and active = 1')");
		$this->db->where('recibos.edificio_id',$edificio_id);
		$this->db->where('recibos.pendiente_pago',true);
		return $this->db->get('recibos');
	}	

	public function done($unidad_id,$edificio_id) {	

		$sql = "SELECT *
		FROM `recibos`
		WHERE `recibos`.`id` NOT IN (
		    SELECT DISTINCT `recibo_id`
		    FROM `pagos_users`
		    WHERE `unidad_id` = $unidad_id AND `active` = 1
		) AND `recibos`.`edificio_id` = '$edificio_id' AND `recibos`.`pendiente_pago` = 1 ";

		return $this->db->query($sql);
	}

	public function last_pending($unidad_id,$edificio_id){	
		$this->db->select('recibos.*');
		$this->db->join('edificios', 'recibos.edificio_id = edificios.id');
		$this->db->where("recibos.id NOT IN (SELECT recibo_id FROM pagos_users where unidad_id = $unidad_id and active = 1)");
		$this->db->where('recibos.edificio_id',$edificio_id);
		$this->db->where('recibos.pendiente_pago',true);
		$this->db->order_by('recibos.id','DESC');
		$this->db->limit(1);
		return $this->db->get('recibos');
	}
	public function checked($id,$edificio_id){
		$rs = $this->read(array('pagos_users.id'=>$id,'edificios.id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}

	
}