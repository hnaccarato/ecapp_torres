<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class espacios_model extends CI_Model
{

	private $rol = PROPIETARIO;

    public function __construct()
	{
		parent::__construct();
	}

    public function set_rol($rol){
        $this->rol = $rol;
    }

    public function create($data){

		if($this->db->insert('espacios', $data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
		
	}

    public function get($espacio_id){
        $rs =  $this->db->get_where('espacios',array('id'=>$espacio_id));
        return $rs->row();

    }    


    public function get_espacios_baned($edificio_id,$array_baned){
        $this->db->where(
            array(
                'edificio_id'=>$edificio_id,
                'active'=>true,

            )
        );
        if($array_baned)
            $this->db->where_not_in('id',$array_baned);
        $rs = $this->db->get('espacios');

        return $rs->result();

    }

	public function update($data, $where){

		$this->db->where($where);

		if($this->db->update('espacios', $data)){
			return true;
		}else{
			return false;
		}
		
	}

	public function delete($where){
		$this->db->where($where);
		if($this->db->delete('espacios')){
			return true;
		}else{
			return false;
		}
	}

	public function read($where=false){
		if($where !== false){
			$this->db->where($where);
		}
		$this->db->select("espacios.*,
							edificios.nombre as edificio, espacios.max_invitados as capa_total");
		$this->db->join('edificios', 'espacios.edificio_id = edificios.id');
        
        if($this->rol == PROPIETARIO)
            $this->db->where('espacios.active',TRUE);
		return $this->db->get('espacios');
	}

	public function checked($id,$edificio_id){
		$rs = $this->db->get_where('espacios',array('id'=>$id,'edificio_id'=>$edificio_id));
		if($rs->num_rows() > 0){
			return true;
		}else{
			return false;
		}

	}

    public function reservados(){
        $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        
        $this->db->select('reservas.id ,
            reservas.estado_id ,
            users.email,
            if(reservas.turno_id > 0,turnos_espacios.importe,periodo_espacios.importe) as importe,
            if(reservas.estado_id = 1,"Pendiente","Aprobado") as estado,
            espacios.id as espacio_id,
            espacios.nombre_espacio,
            espacios.foto_espacio,
            espacios.autorizacion,
            reservas.invitados as total_ivitados,
            users.id as user_id,
            users.first_name,
            users.last_name,
            reservas.dia_calendario as date,
            reservas.hora_reserva as desde,
            reservas.hora_hasta as hasta,
            reservas.id as reserva_id,
            reservas.timestamp as cuando,
            unidades.name as unidad,
            unidades.departamento as departamento,
            unidades.id as unidad_id');
       
        $this->db->join('users','users.id = reservas.user_id');
        $this->db->join('unidades','unidades.id = reservas.unidad_id');
        $this->db->join('espacios','reservas.espacio_id = espacios.id');
        $this->db->join('periodo_espacios','periodo_espacios.id = reservas.periodo_id','left');
        $this->db->join('turnos_espacios','turnos_espacios.id = reservas.turno_id','left');
        $this->db->group_by('reservas.reserva_hash');
        $this->db->where('reservas.activo',TRUE);
        $rs = $this->db->get('reservas');
        return $rs;
    }

    public function reservadosApi(){

        $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $this->db->select('
            reservas.id ,
            reservas.estado_id ,
        	users.email,
            if(reservas.turno_id > 0,turnos_espacios.importe,periodo_espacios.importe) as importe,
            espacios.nombre_espacio,
            espacios.foto_espacio,
            espacios.autorizacion,
            users.id as user_id,
            users.first_name,
            users.last_name,
            reservas.dia_calendario as date,
            reservas.hora_reserva as desde,
            reservas.hora_hasta as hasta,
            reservas.id as reserva_id,
            reservas.reserva_hash as reserva_hash,
            reservas.timestamp as cuando,
            unidades.name as unidad,
            unidades.departamento as departamento,
            unidades.id as unidad_id');
        $this->db->join('users_unidad','users_unidad.unidad_id = reservas.unidad_id');
        $this->db->join('users','users.id = users_unidad.user_id');
        $this->db->join('unidades','unidades.id = users_unidad.unidad_id');
        $this->db->join('espacios','reservas.espacio_id = espacios.id');
        $this->db->join('periodo_espacios','periodo_espacios.id = reservas.periodo_id','left');
        $this->db->join('turnos_espacios','turnos_espacios.id = reservas.turno_id','left');
        $this->db->group_by('reservas.reserva_hash');
        $rs = $this->db->get('reservas');
        return $rs;
    }

    public function rechasados(){
        $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $this->db->select('
			reservas.id ,
        	users.email,
            if(reservas_rechazados.turno_id > 0,turnos_espacios.importe,periodo_espacios.importe) as importe,
            espacios.nombre_espacio,
            espacios.foto_espacio,
            espacios.autorizacion,
            users.id as user_id,
            users.first_name,
            reservas.invitados as total_ivitados,
            users.last_name,
            reservas.dia_calendario as date,
            reservas_rechazados.hora_reserva as desde,
            reservas_rechazados.hora_hasta as hasta,
            reservas_rechazados.timestamp as cuando,
            unidades.name as unidad,
            unidades.departamento as departamento,
            unidades.id as unidad_id');
        $this->db->join('users_unidad','users_unidad.unidad_id = reservas_rechazados.unidad_id');
        $this->db->join('users','users.id = users_unidad.user_id');
        $this->db->join('unidades','unidades.id = users_unidad.unidad_id');
        $this->db->join('reservas','reservas.id = reservas_rechazados.reserva_id');
        $this->db->join('espacios','reservas.espacio_id = espacios.id');
        $this->db->join('periodo_espacios','periodo_espacios.id = reservas_rechazados.periodo_id','left');
        $this->db->join('turnos_espacios','turnos_espacios.id = reservas_rechazados.turno_id','left');
		$this->db->group_by( array('reservas.id','users.id') );

        $rs = $this->db->get('reservas_rechazados');
        return $rs;
    }

        private function reservas($espacio_id){
            $rs = $this->db->select('id,permiso_reserva,ilim_permiso_reserva')
            ->where('id',$espacio_id)
            ->get('espacios');
            if($rs->num_rows() > 0 ){
                $espacio = $rs->row();
                return $espacio;
            }
        }

        public function super_reservas($espacio_id){
            $espacio = $this->reservas($espacio_id);
            if($espacio->permiso_reserva == 1 && $espacio->ilim_permiso_reserva == 1){
                return TRUE;
            }else{
                return false;
            }
        }

        public function reservas_habilitado($espacio_id){
            $espacio = $this->reservas($espacio_id);
            if($espacio->permiso_reserva == 1){
                return TRUE;
            }else{
                return false;
            }
        }

	
}
