<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Calendario_model extends CI_MODEL
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }


    /**
     *cogemos los datos de la tabla calendario y la colocamos en cada día añadiendo una clase
     *del calendario
     * @access public
     * @param $month - int número de mes
     * @param $year - int número de año
     * @return array - información del estado de cada día del mes para pintar el calendario
     */

    function get_calendar_data($year, $month,$espacio_id) {
        $this->db->where('espacio_id',$espacio_id);
        $query = $this->db->select('*')->from('calendario')->like('fecha', "$year-$month", 'after')->get();
        $cal_data = array();

        foreach ($query->result() as $row) {

            $index = ltrim(substr($row->fecha, 8, 2), '0');
            $cal_data[$index] = $row->estado;

        }
        return $cal_data;

    }

     /**
     *hacemos que el usuario pueda coger hora a través del calendario con varios intervalos de horas
     * @access public
     * @param $dia_calendario - string fecha pulsada en el calendario formato 2013/09/01
     * @param $month - string número de mes
     * @param $year - int número de año
     * @return html - calendario con la información de la base de datos
     */

    public function generar_calendario($year, $month,$espacio_id) {


        if(!$this->check_view($year, $month, $espacio_id))
            return "<p>No está permitido reservar este mes</p>";



        $class = $this->router->fetch_class();
        $conf_calendar = array('show_next_prev' => TRUE, 'next_prev_url' => base_url( $class.'/espacios_reservar'), 'start_day' => 'lunes', 'template' => '  
                                      
               {table_open}<table border="0" cellpadding="0" cellspacing="0" class="calendario">{/table_open}

               {heading_row_start}<tr id="head_links">{/heading_row_start}
            
               {heading_previous_cell}<th class="previo"><a href="{previous_url}"><</a></th>{/heading_previous_cell}
               {heading_title_cell}<th class="fecha_actual" colspan="{colspan}">{heading}</th>{/heading_title_cell}
               {heading_next_cell}<th class="siguiente"><a href="{next_url}">></a></th>{/heading_next_cell} 
            
               {heading_row_end}</tr>{/heading_row_end}
               
               {week_row_start}<tr>{/week_row_start}
               {week_day_cell}<td class="dias_semana">{week_day}</td>{/week_day_cell}
               {week_row_end}</tr>{/week_row_end}
               
               {cal_row_start}<tr>{/cal_row_start}
               {cal_cell_start}<td class="dia">{/cal_cell_start}   
            
               {cal_cell_content}<div id=oc_{content} class="otro_dia {content}">{day}</div>{/cal_cell_content}
               {cal_cell_content_today}<div class="highlight {content}">{day}</div>{/cal_cell_content_today}
                   
               {cal_cell_no_content}<div class="highlight {content}">{day}</div>{/cal_cell_no_content}
               {cal_cell_no_content_today}<div class="highlight {content}">{day}</div>{/cal_cell_no_content_today}
                   
               {cal_cell_blank} {/cal_cell_blank}
                   
               {cal_cell_end}</td>{/cal_cell_end}
               {cal_row_end}</tr>{/cal_row_end}
            
               {table_close}</table>{/table_close}             
        ');

        $this->load->library('calendar', $conf_calendar);

        $calendar_data = $this->get_calendar_data($year, $month,$espacio_id);

        return $this->calendar->generate($year, $month, $calendar_data);

    }

    
     /**
     * @access public
     * @param $month - int número de mes
     * @param $year - int número de año
     * @return array - total días del mes
     */

    public function get_total_days($month, $year) 
    {
        //array con los días de cada mes del año
        $days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

        //si no es un mes salimos de la función
        if ($month < 1 OR $month > 12) 
        {
            return;
        }

        //Si es febrero y es año bisiesto tiene 29 días
        if ($month == 2) 
        {
            if ($year % 400 == 0 OR ($year % 4 == 0 AND $year % 100 != 0)) 
            {
                return 29;
            }
        }
        //devolvemos un array con los días del mes
        return $days_in_month[$month - 1];
    }

    /**
     *al situarnos en un mes comprobamos si existen los datos de ese mes en la base de datos
     *si no existen los introducimos, en otro caso no hacemos nada
     * @access public
     * @param $month - int número de mes
     * @param $year - int número de año
     */


    public function insert_calendario($month, $year, $espacio_id) 
    {

        $dias_total_mes = $this ->get_total_days($month, $year);

        $this->db->where('espacio_id',$espacio_id);
        $this->db->like('fecha', "$year-$month", 'after');
        $query = $this->db->get('calendario');
        if ($query->num_rows() == 0) 
        {

            for ($i = 1; $i <= $dias_total_mes; $i++) 
            {
                //obtenemos cada uno de los días del mes
                $weekDay = date('w', strtotime(date($year . "-" . $month . "-" . $i)));


                //chequeo los dias habilitados para el espcios
                $dias_habilitados = $this->get_dias_habilitados($espacio_id);


                if (!in_array($weekDay, $dias_habilitados )) 
                {

                    $data[$i] = array('fecha' => $year . "-" . $month . "-" . $i, 'comentario' => '', 'estado' => 'fest', 'espacio_id'=>$espacio_id);
                } 

                //comprobamos si es una fecha anterior a la actual
                else if ($month == date('m') && $i < date('d')) 
                {

                    $data[$i] = array('fecha' => $year . "-" . $month . "-" . $i, 'comentario' => '', 'estado' => 'ant', 'espacio_id'=>$espacio_id);

                } 
                //días posteriores al día actual
                else 
                {

                    $data[$i] = array('fecha' => $year . "-" . $month . "-" . $i, 'comentario' => '', 'estado' => '', 'espacio_id'=>$espacio_id);
 
                }

                //insertamos los días del mes en la base de datos
                $this->db->insert('calendario', $data[$i]);

            }
        }
    }


    /**
     *al pulsar en cualquier fecha del calendario comprobamos si existe en la tabla reservas
     *si no existe creamos las horas para esa fecha en la tabla reservas
     * @access public
     * @param $day - int número de dia
     * @param $month - int número de mes
     * @param $year - int número de añoreservas
     */


    public function insert_horas($year, $month, $day, $espacio_id) 
    {
     
        $this->db->where('espacio_id',$espacio_id);
        $this->db->where('dia_calendario', $year . "-" . $month . "-" . $day);
        $query = $this->db->get('reservas');

        if ($query->num_rows() < 1) {

            $pe_query  = $this->get_periodos($espacio_id); 
         
            if($pe_query->num_rows()){

                foreach ($pe_query->result() as $row){
                    if(intval($row->id) > 0 ){
                        for ($i=1; $i <= $row->cantidad ; $i++) { 
                           $data= array(
                               'dia_calendario' => $year . "-" . $month . "-" . $day, 
                               'hora_reserva' => $row->desde, 
                               'hora_hasta' => $row->hasta, 
                               'periodo_id' => $row->id, 
                               'estado' => 'libre',
                               'espacio_id'=>$espacio_id
                           );

                           $this->db->insert('reservas', $data);
                        }
                    }
                }        

            }else{
                    $rs_turno = $this->get_turnos($espacio_id);
                    if($rs_turno->num_rows()){

                        $espacio = $this->db->get_where('espacios',array('id'=>$espacio_id))->row();

                        $start=strtotime('2019-06-10'.$espacio->init_hora);
                        $end=strtotime('2019-06-10'.$espacio->fin_hora);
                        
                        if($start >= $end){
                             $end = strtotime ( '+24 hour' , strtotime('2019-06-10'.$espacio->fin_hora) ) ;
                        }
                        
                        for ($i=$start; $i<=$end ;$i = strtotime($espacio->periodo,$i))
                        {
                            
                            $data[]= array(
                                'dia_calendario' => $year . "-" . $month . "-" . $day, 
                                'hora_reserva' => date('H:i:s',$i), 
                                'comentario_reserva' => '', 
                                'estado' => 'libre',
                                'espacio_id'=>$espacio_id
                            );
                          
                        }
                        $this->db->insert_batch('reservas', $data);
                    }
                    // echo "Error cominarce con el administrador";
             }

        }

        return TRUE;
    }


    /**
    *comprobamos si hay horas disponibles para ese día en concreto
    * @access public
    * @param $day - int número de dia
    * @param $month - int número de mes
    * @param $year - int número de año
    */

    public function horas_seleccionadas($year, $month, $day, $espacio_id,$turno =false) 
    {
        if($turno){
            $this->db->select("r1.id,r1.hora_reserva, DATE_FORMAT(ADDTIME(CONCAT(r1.dia_calendario,' ',r1.hora_reserva), '$turno'), '%H:%i:%S') as hasta");
            $this->db->where("(( select count(*) FROM reservas r2 
                where r2.hora_reserva >= r1.hora_reserva and  
                r2.hora_reserva < ADDTIME(r1.hora_reserva, '$turno')  
                AND r2.estado = 'ocupado' 
                AND r2.espacio_id = $espacio_id
                AND r2.dia_calendario ='$year-$month-$day') = 0 
            )");
        }else{
             $this->db->select("r1.id,r1.dia_calendario,r1.hora_reserva,r1.hora_hasta,r1.periodo_id");
             $this->db->group_by("r1.hora_reserva");
        }
        
        $this->db->where('r1.espacio_id',$espacio_id);
        $this->db->where('r1.dia_calendario', $year . "-" . $month . "-" . $day);
        $this->db->where('r1.estado','libre');
        $this->db->order_by('r1.hora_reserva','ASC');
        $query = $this->db->get('reservas r1');

        //si hay horas disponibles las devolvemos
        if ($query -> num_rows() > 0) 
        {
            return $query->result();

        }else{
            return false;
        }
    }



    /**
     *hacemos que el usuario pueda coger hora a través del calendario con varios intervalos de horas
     * @access public
     * @param $dia_calendario - string fecha pulsada en el calendario formato 2013/09/01
     * @param $month - string número de mes
     * @param $year - int número de año
     */

    public function nueva_reserva($reserva_id,$data,$dia_calendario, $hora ,$espacio_id,$turno = false,$edificio_id = FALSE,$user=false,$cantidad = 1) 
    {

        if(isset($data['periodo_id'])){
            if($data['periodo_id'] == 0)
                return FALSE;
        }        

        if(isset($data['turno_id'])){
            if($data['turno_id'] == 0)
                return FALSE;
        }

        if($data['unidad_id'] == 0 ){
            return false;
        }

        if(!$this->check_dia($espacio_id,$dia_calendario,$edificio_id)){
            return false;
        }

        if ($this->ion_auth->in_group(ADMINISTRADOR)){
            
            if($this->session->userdata('controller')){
                $active_controller = $this->session->userdata('controller');
                if($active_controller == ADMINISTRADOR ){
                    $super = $this->espacios_model->super_reservas($espacio_id);
                    
                    if(!$super){
                    
                       if(!$this->permisos($espacio_id,$dia_calendario,$data['unidad_id'],$edificio_id)){
                           return false;
                       } 
                    } 
                }
            }

        }else{
            if(!$this->permisos($espacio_id,$dia_calendario,$data['unidad_id'],$edificio_id)){
                return false;
            }
        }

        if($turno){
            $this->db->where("hora_reserva  >=", $hora);
            $this->db->where("hora_reserva <","ADDTIME('$hora', '$turno')",false);
            $data['hora_hasta'] = date_fix($hora,$turno);
        }else{
            if($reserva_id){
                $this->db->where("id", $reserva_id);
            }
        }

        $this->db->where('espacio_id',$espacio_id);
        $this->db->where("dia_calendario", $dia_calendario);

        $data['reserva_hash'] = md5(uniqid('', true).date("Y-m-d H:i:s"));
        $data['activo'] = TRUE;
        $query = $this->db->update("reservas",$data);
///echo $this->db->last_query();
///die();
        if($query){
            
            $disponibles = $this->disponibilidad($dia_calendario,$espacio_id,$turno);


            if($disponibles == 0){
                $calendar['estado'] = 'clouse';
                $this->db->where('espacio_id',$espacio_id);
                $this->db->where('fecha', $dia_calendario);
                $this->db->update('calendario', $calendar);
             //   return TRUE;
            }
           // return TRUE;
        }

        $this->check_asock($espacio_id,$dia_calendario,$data['reserva_hash'],$data['unidad_id'],$data['estado_id'],$user);
        if(!empty($data['reserva_hash']))
            return $data['reserva_hash']; 
        else
            return FALSE;


    }

    public function existe_reserva($dia,$hora,$espacio_id,$estado,$reserva_id=false){
       
        $this->db->where("dia_calendario", $dia);
        $this->db->where("hora_reserva", $hora);
        $this->db->where('espacio_id',$espacio_id);
        $this->db->where("estado",$estado);
        if($reserva_id){
            $this->db->where("id",$reserva_id);
        }
        $rs =  $this->db->get('reservas');
        if($rs->num_rows()){
            return TRUE;
        }else{
            return FALSE;
        }

    }


    private function get_dias_habilitados($espacios_id){
        $rs = $this->db->get_where('espacios',array('id'=>$espacios_id));
        if($rs->num_rows()){
            $espacio = $rs->row();
            return json_decode($espacio->dias);
        }
    }


    public function get_periodos($espacio_id){
        $this->db->order_by('periodo_espacios.desde','ASC');
        $rs = $this->db->get_where('periodo_espacios',array('espacio_id'=>$espacio_id,'active'=>TRUE));
        return $rs;
    }
    public function get_turnos($espacio_id){

        $rs = $this->db->get_where('turnos_espacios',array('espacio_id'=>$espacio_id,'active'=>TRUE));
        return $rs;
    }

    public function get_periodo($id){

        $rs = $this->db->get_where('periodo_espacios',array('id'=>$id,'active'=>TRUE));
        if($rs->num_rows()){
            return $rs->row();
        }
        return false;
    }

    public function get_turno($id){
        $rs = $this->db->get_where('turnos_espacios',array('id'=>$id,'active'=>TRUE));
        if($rs->num_rows()){
            return $rs->row();
        }
        return false;
    }

    public function delete_periodo($data = false){
        $this->db->update('periodo_espacios',array('active'=>FALSE),$data);
    }

   public function delete_turno($data = false){
        $this->db->update('turnos_espacios',array('active'=>FALSE),$data);
    }

    public function insert_periodo($data){
        //$this->db->replace('periodo_espacios',$data);
        $id = intval($data['id']);

        if($id > 0){
            $this->db->where('id', $id);
            $insert['importe']  = $data['importe'] ;
            /*No se puedo eliminar mas los horarios*/
            $insert['active'] = TRUE;
            $this->db->update('periodo_espacios', $insert);
        }else{
            $insert['espacio_id'] = $data['espacio_id'];
            $insert['desde'] = $data['desde'];
            $insert['hasta']  = $data['hasta'] ;
            $insert['importe']  = $data['importe'] ;
            $insert['name_cant'] = $data['name_cant'];
            $insert['cantidad'] = $data['cantidad'];
            $insert['active'] = $data['active'];
            $insert['user_id']  = $data['user_id'] ;
            $this->db->insert('periodo_espacios',$insert);
        }

    }

    public function insert_turno($data){

        $id = intval($data['id']);


        if($id > 0){
            $this->db->where('id', $id);
            $insert['importe']  = $data['importe'] ;
            /*No se puedo eliminar mas los horarios*/
            $insert['active'] = TRUE;
            //$insert['active'] = $data['active'];
            $this->db->update('turnos_espacios', $insert);
        }else{

            $insert['espacio_id'] = $data['espacio_id'];
            $insert['identificacion']  = $data['identificacion'] ;
            $insert['turno'] = $data['turno'];
            $insert['importe']  = $data['importe'] ;
            $insert['active'] = $data['active'];
            $insert['user_id']  = $data['user_id'] ;
            $this->db->insert('turnos_espacios',$insert);
        }
        
    }

    public function get_reservas_turno($espacios_id ,$fecha = false){
        $this->db->select('reservas.id,reservas.dia_calendario,reservas.hora_reserva,reservas.hora_hasta, 
            ADDTIME(TIME_FORMAT(reservas.hora_reserva,turnos_espacios.turno), "%H") as hasta,
            turnos_espacios.identificacion,unidades.name,unidades.departamento ');
        $this->db->join('users_unidad','users_unidad.unidad_id = reservas.unidad_id');
        $this->db->join('unidades','unidades.id = users_unidad.unidad_id');
        $this->db->join('turnos_espacios','turnos_espacios.id = reservas.turno_id');
        $this->db->order_by('reservas.hora_reserva','asc');
        if($fecha)
            $this->db->where('reservas.dia_calendario',$fecha);

        $this->db->group_by('reservas.reserva_hash');
        $rs = $this->db->get_where('reservas',array('reservas.espacio_id'=>$espacios_id));
        return $rs;
    } 

    public function get_reservas_periodo($espacios_id ,$fecha = false){
        $this->db->select('reservas.id,reservas.dia_calendario,reservas.hora_reserva,reservas.hora_hasta,
            unidades.name,unidades.departamento ');
        $this->db->join('users_unidad','users_unidad.unidad_id = reservas.unidad_id');
        $this->db->join('unidades','unidades.id = users_unidad.unidad_id');
        $this->db->join('periodo_espacios','periodo_espacios.id = reservas.periodo_id');
        $this->db->order_by('reservas.hora_reserva','asc');
        if($fecha)
            $this->db->where('reservas.dia_calendario',$fecha);

        $this->db->group_by('reservas.reserva_hash');
        $rs = $this->db->get_where('reservas',array('reservas.espacio_id'=>$espacios_id));
        return $rs;
    }

    public function get_reservas($espacios_id ,$fecha = false){
        $this->db->select('users.email,
            users.first_name,
            users.last_name,
            reservas.id as reserva_id,
            unidades.id as unidad_id');
        $this->db->join('users_unidad','users_unidad.id = reservas.unidad_id');
        $this->db->join('users','users.id = users_unidad.user_id');
        $this->db->join('unidades','unidades.id = users_unidad.unidad_id');
        if($fecha)
            $this->db->where('reservas.dia_calendario',$fecha);

        $rs = $this->db->get_where('reservas',array('reservas.espacio_id'=>$espacios_id));
        return $rs;
    }

    public function disponibilidad($dia_calendario,$espacio_id,$turno = false){
        $rs = $this->db->get_where('reservas',array('dia_calendario'=>$dia_calendario,
            'espacio_id'=>$espacio_id,'estado'=>'libre'));
        $num = intval($rs->num_rows());
        return $num;
    }

    private function check_view($year, $month,$espacio_id){

        $date1 = new DateTime(date("Y-m-d"));
        $date2 = new DateTime($year."-".$month."-01");
        $diff = $date1->diff($date2);
        $espcio = $this->get_espacio($espacio_id);
        if($espcio){
            if($diff->m <= $espcio->max_meses){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }                         
       
    }

    private function get_espacio($espacio_id){

        $rs = $this->db->get_where('espacios',array('id'=>$espacio_id));

        if($rs->num_rows() > 0){
            $espacios = $rs->row();
            return $espacios;
        }else{
            return false;
        }

    }

    public function desactivar_dia($espacios_id ,$fecha){

        $data['estado'] = 'libre';
        $data['unidad_id'] = 0;
        $data['importe'] = '';
        $data['reserva_hash'] = 0;
        $data['estado_id'] = 0;

        $this->db->where('espacio_id',$espacios_id);
        $this->db->where('dia_calendario',$fecha);
        $rs = $this->db->update('reservas',$data);
        if($rs){
            $this->db->where('espacio_id',$espacios_id);
            $this->db->where('fecha',$fecha);
            $this->db->update('calendario',array('estado'=>'rech'));
        }

    }

    public function get_reserva($id){

        $rs = $this->db->get_where('reservas',array('id'=>$id));
        if($rs){
            return $rs->row();
        }
        return false;
    }

    public function rechazar_reserva($reserva_hash, $edificio_id ,$unidad_id,$user = false){

        $this->db->select('reservas.*,`unidades`.`edificio_id`');
        $this->db->join('unidades','unidades.id = reservas.unidad_id');

        $rs = $this->db->get_where('reservas',array('reservas.reserva_hash'=>$reserva_hash,
            'reservas.unidad_id'=>$unidad_id));

   /*     echo $this->db->last_query();
        die('asd');*/
        
        if($rs->num_rows()){
  
            foreach ($rs->result() as $reserva) {
                

                if( $edificio_id  == $reserva->edificio_id){

                    if($this->reserva_check_hash($reserva)){

                      //  return TRUE;
                    }else{
                        $data['estado'] = 'libre';
                        $data['unidad_id'] = 0;
                    //    $data['periodo_id'] = 0;
                        $data['turno_id'] = 0;
                        $data['importe'] = 0;
                        $data['reserva_hash'] = 0;
                        $data['estado_id'] = 0;
                        $data['activo'] = 0;
                        $this->db->where('id',$reserva->id);
                        $this->db->update('reservas',$data);
                        $this->add_rechazado($reserva,$user);
                        $this->limpiar($reserva->espacio_id,$reserva->dia_calendario);
                    }

                }

            }

            return TRUE;
        }
        return FALSE;
    }


    /* cambiio el  hash si existe  para que no pueda ser eliminado por otra persna que tenga cerrado el dia */
    
    private function reserva_check_hash($reserva){

        if(empty($reserva->old_hash))
            return FALSE;
            
        $rs = $this->db->get_where('reservas',array('reservas.reserva_hash'=>$reserva->old_hash));
     
        if($rs->num_rows() > 0){
     
            // $reserva = $rs->row();
            
            $data['reserva_hash'] = $reserva->old_hash;
            $data['old_hash'] = '';

            $this->db->where('id',$reserva->id);
            $this->db->update('reservas',$data);
            return TRUE;


        }else{
            return FALSE;
        }

    }


    private function limpiar($espacios_id,$fecha){

        $this->db->where('espacio_id',$espacios_id);
        $this->db->where('fecha',$fecha);
        $this->db->update('calendario',array('estado'=>''));
        return TRUE;
    }

    private function add_rechazado($data,$user = false){
        if(!$user)
            $user_id = $this->user->id;
        else
            $user_id = $user;

        $insert['reserva_id'] = $data->id;
        $insert['user_id'] = $user_id;
        $insert['unidad_id'] = $data->unidad_id;
        $insert['turno_id'] = $data->turno_id;
        $insert['periodo_id'] = $data->periodo_id;
        $insert['hora_reserva'] = $data->hora_reserva;
        $insert['hora_hasta'] = $data->hora_hasta;
        $this->db->insert('reservas_rechazados',$insert);
        return TRUE;
    }


    public function permisos($espacio_id,$dia_calendario,$unidad_id, $edificio_id = FALSE){

        $rs = $this->db->get_where('espacios',array('id'=>$espacio_id));
        $unidad_id = intval($unidad_id);

        if($rs->num_rows()){
           
            $espacio = $rs->row();
            
            if(!$edificio_id)
                $edificio_id = $this->edificio_id;

            if($espacio->edificio_id == $edificio_id){
                
                /*Solo chequeo que si la torre puede hacer la reserva de dicho espacio , esto no lo puede hacer el administrador , por mas que tenga poderes de super reservas*/

                $acept_torre = $this->check_torre($espacio_id,$unidad_id);

                $dias = intval($espacio->max_meses);
                $max = intval($espacio->max);
                $periodo = intval($espacio->periodo_permitido);
                $bloqueado = intval($espacio->bloqueado);
                
                $proxima_reserva = $this->diferencia_dias(date("Y-m-d"),$dia_calendario);
                $num_reservas = $this->num_reservas($espacio_id,$unidad_id,$espacio->tiempo_espera);
                $periodo_permitido = $this->check_periodo($espacio_id,$unidad_id,$periodo,$dia_calendario,$max);
                
                /*verifico el dia para ver si esta en el tiempo permitido para hacer una reserva*/
              //  $check_block = $this->check_block($bloqueado,$dia_calendario);
               



                if(!$acept_torre){
                    $mensaje = "La Unidad seleccionada no está permitida para dicha reserva";
                    if (!$this->session->userdata('error_api_set')) {
                         echo $mensaje;
                    } 
                    $this->session->set_userdata('error_message_reservation', $mensaje);
                    return FALSE;
                }
                
                if($dias <= $proxima_reserva){
                    $mensaje = "No está permitida la reserva con tanta anticipación";
                    if (!$this->session->userdata('error_api_set')) {
                         echo $mensaje;
                    } 
                    $this->session->set_userdata('error_message_reservation', $mensaje);
                    return FALSE;
                }            

                if($max <= $num_reservas){
                    $mensaje = "Supero el máximo de reservas permitidas<br/> La reserva del ".$dia_calendario." no está permitida";
                    if (!$this->session->userdata('error_api_set')) {
                         echo $mensaje;
                    } 
                    $this->session->set_userdata('error_message_reservation', $mensaje);
                    return FALSE;
                }


                if(intval($proxima_reserva) < intval($bloqueado)){
                    $mensaje =  "Tiene que esperar $bloqueado dias para hacer dicha reserva <br/>
                    |La reserva del ".$dia_calendario." no está permitida";
                    if (!$this->session->userdata('error_api_set')) {
                         echo $mensaje;
                    } 
                    $this->session->set_userdata('error_message_reservation', $mensaje);
                    return FALSE;
                }

                if(!$periodo_permitido){
                    $mensaje =  "Reserva No permitida";
                    if (!$this->session->userdata('error_api_set')) {
                         echo $mensaje;
                    } 
                    $this->session->set_userdata('error_message_reservation', $mensaje);
                    return FALSE;
                }                
                

                return TRUE;
            }else{
                $mensaje =  "Reserva No permitida";
                if (!$this->session->userdata('error_api_set')) {
                     echo $mensaje;
                } 
                $this->session->set_userdata('error_message_reservation', $mensaje);
                return FALSE;
            }
        }

        return FALSE;
    }

    private function check_torre($espacio_id,$unidad_id){
        $rs = $this->db->get_where('espacio_torre',array('espacio_id'=>$espacio_id));
        if($rs->num_rows()){
            $torre = $this->db->get_where('unidades', array('id'=>$unidad_id));
            if($torre){
                $torre_id = $torre->row()->torre_id;
                foreach ($rs->result() as $value) {
                    if($value->torre_id == $torre_id){
                        return TRUE;
                    }
                }
                return FALSE;
            }else{
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }


    private function check_periodo($espacio_id,$unidad_id,$periodo,$dia_calendario,$max){
        /* 1 activo,2 Anual,3 Mensual, 4 Anual */

        if($periodo == ACTIVAS){
            return TRUE;
        }

        if($periodo == ANUAL){

           $query = $this->db->query("SELECT COUNT(DISTINCT `reserva_hash`) as num , count(YEAR(`dia_calendario`)) as year,
           `dia_calendario` 
           FROM `reservas` 
           WHERE `estado` 
           LIKE 'ocupado' 
           AND `unidad_id` = $unidad_id 
           AND `espacio_id` = $espacio_id 
           AND YEAR(`dia_calendario`) = YEAR(now())");
           $row = $query->row();

           if (isset($row)){
               if($row->num < $max){
                   return TRUE;
               }else{
                   echo "Supero el máximo de reservas permitidas en el Año<br/>";
                   return  FALSE;
               }

           }
        }

        if($periodo == MENSUAL){
           $query = $this->db->query("SELECT COUNT(DISTINCT `reserva_hash`) as num ,count(MONTH(`dia_calendario`)) as mes,
           `dia_calendario` 
           FROM `reservas` 
           WHERE `estado` 
           LIKE 'ocupado' 
           AND `unidad_id` = $unidad_id 
           AND `espacio_id` = $espacio_id 
           AND MONTH(`dia_calendario`) = MONTH($dia_calendario) 
           AND YEAR(`dia_calendario`) = YEAR(now())");
           $row = $query->row();

           if (isset($row)){
               if($row->num < $max){
                   return TRUE;
               }else{
                   echo "Supero el máximo de reservas permitidas en el Mes<br/>";
                   return  FALSE;
               }

           }
        }

        if($periodo == SEMANAL){
            $query = $this->db->query("SELECT COUNT(DISTINCT `reserva_hash`) as num ,
                count(WEEK(`dia_calendario`)) as semana,
            `dia_calendario` 
            FROM `reservas` 
            WHERE `estado` 
            LIKE 'ocupado' 
            AND `unidad_id` = $unidad_id 
            AND `espacio_id` = $espacio_id 
            AND WEEK(`dia_calendario`) = WEEK('$dia_calendario') 
            AND YEAR(`dia_calendario`) = YEAR(now())");

            $row = $query->row();
            if (isset($row)){
                if($row->num < $max){
                    return TRUE;
                }else{
                    echo " $row->semana Supero el máximo de reservas permitidas en la Semana<br/>";
                    return  FALSE;
                }

            }
            
        }

        return  FALSE;
    }

 /*   private function num_reservas($espacio_id ,$unidad_id){
        
        //date_default_timezone_get();
        $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        date_default_timezone_set('America/Argentina/Buenos_Aires'); 
        $fecha = new DateTime('NOW');
        $data = array(
            'unidad_id'=>$unidad_id,
            'espacio_id'=>$espacio_id,
            'dia_calendario >='=>$fecha->format('Y-m-d'),
         //   'hora_hasta >='=>$fecha->format('H:i:s')
        );

        $this->db->group_by('reserva_hash');
        $rs = $this->db->get_where('reservas',$data);
       //  echo $this->db->last_query();
        return $rs->num_rows();
    }*/


    private function num_reservas($espacio_id ,$unidad_id,$tiempo_espera){
        
        //date_default_timezone_get();
        $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        date_default_timezone_set('America/Argentina/Buenos_Aires'); 
        $fecha = new DateTime('NOW');
        $data = array(
            'unidad_id'=>$unidad_id,
            'espacio_id'=>$espacio_id,
            'dia_calendario >='=>$fecha->format('Y-m-d'),
        );

        if($tiempo_espera == 1)
            $data['hora_hasta >='] = $fecha->format('H:i:s');


        $this->db->group_by('reserva_hash');
        $rs = $this->db->get_where('reservas',$data);
       // echo $this->db->last_query();
        
        return $rs->num_rows();
    }



    private  function diferencia_dias($inicio, $fin)
    {    
        $inicio = strtotime($inicio);
        $fin = strtotime($fin);
        $dif = $fin - $inicio;
        $diasFalt = (( ( $dif / 60 ) / 60 ) / 24);
        $dias = ceil($diasFalt);
        return $dias;
    }

    private function check_asock($espacio_id,$dia_calendario,$hash,$unidad_id,$estado_id,$user=FALSE){
        
        $rs = $this->db->get_where('espacios',array('id'=>$espacio_id));

        if($rs){
            $espacio = $rs->row();

            if($espacio->asoc_to > 0){

                for ($i=1; $i <= $espacio->asoc_to; $i++) { 
                    $nuevo_dia = date('Y-m-d',strtotime($dia_calendario."+ $i days"));
                    $this->clouse_espacio($espacio->id,$nuevo_dia,$hash,$unidad_id,$estado_id,$user);
                }                        
            }
           
            if($espacio->asoc_from > 0){
                for ($i=1; $i <= $espacio->asoc_from; $i++) { 
                    $nuevo_dia = date('Y-m-d',strtotime($dia_calendario."- $i days"));
                    $this->clouse_espacio($espacio->id,$nuevo_dia,$hash,$unidad_id,$estado_id,$user);
                }
            }

            if($espacio->asoc_espacio_id >0){
                $this->clouse_espacio($espacio->asoc_espacio_id,$dia_calendario,$hash,$unidad_id,$estado_id,$user);
            }

            

        }

    }


    private function clouse_espacio($espacio_id,$dia_calendario,$hash,$unidad_id,$estado_id,$user=false){
       
        if(!$user)
            $user_id = $this->user->id;
        else
            $user_id = $user;

        $year = date("Y", strtotime($dia_calendario));
        $month = date("m", strtotime($dia_calendario));
        $day = date("d", strtotime($dia_calendario));

        if($this->insert_horas($year, $month, $day, $espacio_id)){

            $rs = $this->db->get_where('reservas',array('espacio_id'=>$espacio_id,
                'estado'=>'ocupado',
                'activo'=>0,
                'dia_calendario'=>$dia_calendario));

            if($rs->num_rows() > 0){
                $reserva = $rs->row();
                $data['old_hash'] = $reserva->reserva_hash;
            }


            $this->db->where('dia_calendario',$dia_calendario);
            $this->db->where('espacio_id',$espacio_id);

            $data['estado'] = "ocupado";
            $data['reserva_hash'] = $hash;
            $data['unidad_id'] = $unidad_id;
            $data['user_id'] = $user_id;
            $data['estado_id'] = $estado_id;

            $rs = $this->db->update('reservas',$data); 

            if($rs){
                $calendar['estado'] = 'clouse';
                $this->db->where('espacio_id',$espacio_id);
                $this->db->where('fecha', $dia_calendario);
                $this->db->update('calendario', $calendar);
             //   return TRUE;
            }
        }

    }
    
    public function check_date($fecha_escogida ,$espacio_id){
        $rs = $this->db->get_where('calendario',array('espacio_id'=>$espacio_id,
            'estado'=>'clouse',
            'fecha'=>$fecha_escogida));
        if($rs->num_rows() > 0){
            return false;
        }

        return TRUE;
    }

    
    private function check_dia($espacio_id,$dia_calendario ,$edificio_id = FALSE){

        if(!$edificio_id)
            $edificio_id = $this->edificio_id;

        $espacio = $this->db->get_where('espacios',array('id'=>$espacio_id,'edificio_id'=>$edificio_id))->row();
        if($espacio){
            $dias_activos = json_decode($espacio->dias);
            $dia = date("w", strtotime($dia_calendario));
            if( in_array($dia,$dias_activos) ){
                return TRUE;
            }else{
                echo "Este día no está permitido";
                return FALSE;
            }  
        }
        
    }

    public function get_disponibles($reserva_id,$limit = false){
        $rs = $this->db->get_where('reservas',array('id'=>$reserva_id));
        if($rs){
            $data = $rs->row();
            
            $where = array(
                'dia_calendario'=>(isset($data->dia_calendario))? $data->dia_calendario:null,
                'hora_reserva'=>(isset($data->hora_reserva))? $data->hora_reserva:null,
                'periodo_id'=>(isset($data->periodo_id))? $data->periodo_id:null,
                'espacio_id'=>(isset($data->espacio_id))? $data->espacio_id:null,
                'estado'=>'libre',
            );

            if($limit){
                $ers = $this->db->get_where('espacios',array('id'=>$data->espacio_id))->row();

                $num_actual = $this->num_reservas($data->espacio_id ,$this->unidad_id, false);

                $limit = $ers->max - intval($num_actual);
               // die($limit);
                $this->db->limit($limit);
            }
            
            $load_data = $this->db->get_where('reservas',$where);
            return $load_data;
        }
    }

    public function get_number_periodo($reserva_id,$limit = false){
        $load_data = $this->get_disponibles($reserva_id,$limit);
        if($load_data){
            return $load_data->num_rows();
        }
    }


    /**
    * api 
    */   


    public function get_days($year,$month,$espacio_id){
        $this->db->where('espacio_id',$espacio_id);
        $query = $this->db->select('*')->from('calendario')->like('fecha', "$year-$month", 'after')->get();
        return $query;
    }


    public function get_reservas_turno_api($espacios_id ,$fecha = false){
        $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $this->db->select('reservas.*, 
            ADDTIME(reservas.hora_reserva,turnos_espacios.turno) as hasta,
            turnos_espacios.identificacion,unidades.name,unidades.departamento ');
        $this->db->join('users_unidad','users_unidad.unidad_id = reservas.unidad_id');
        $this->db->join('unidades','unidades.id = users_unidad.unidad_id');
        $this->db->join('turnos_espacios','turnos_espacios.id = reservas.turno_id');
        $this->db->order_by('reservas.hora_reserva','asc');
        if($fecha)
            $this->db->where('reservas.dia_calendario',$fecha);

        $this->db->group_by('reservas.reserva_hash');
        $rs = $this->db->get_where('reservas',array('reservas.espacio_id'=>$espacios_id));
        return $rs;
    } 

    public function get_reservas_periodo_api($espacios_id ,$fecha = false){

            $this->db->query(" SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

            $this->db->select('reservas.*, 
                unidades.name,unidades.departamento ');
            $this->db->join('users_unidad','users_unidad.unidad_id = reservas.unidad_id');
            $this->db->join('unidades','unidades.id = users_unidad.unidad_id');
            $this->db->join('periodo_espacios','periodo_espacios.id = reservas.periodo_id');
            $this->db->order_by('reservas.hora_reserva','asc');
            if($fecha)
                $this->db->where('reservas.dia_calendario',$fecha);

            $this->db->group_by('reservas.reserva_hash');
            $rs = $this->db->get_where('reservas',array('reservas.espacio_id'=>$espacios_id));
            return $rs;
    }


    /**
    *comprobamos si hay horas disponibles para ese día en concreto para la api
    * @access public
    * @param $day - int número de dia
    * @param $month - int número de mes
    * @param $year - int número de año
    */

    public function horas_seleccionadas_api($year, $month, $day, $espacio_id,$turno =false) 
    {
        if($turno){
            $this->db->select("r1.id, r1.dia_calendario, r1.hora_reserva, DATE_FORMAT(ADDTIME(CONCAT(r1.dia_calendario, ' ', r1.hora_reserva), '$turno'), '%H:%i:%S') as hora_hasta, r1.periodo_id");
             $this->db->group_by("r1.hora_reserva");
        }else{
             $this->db->select("r1.id,r1.dia_calendario,r1.hora_reserva,r1.hora_hasta,r1.periodo_id");
             $this->db->group_by("r1.hora_reserva");
        }
        
        $this->db->where('r1.espacio_id',$espacio_id);
        $this->db->where('r1.dia_calendario', $year . "-" . $month . "-" . $day);
        $this->db->where('r1.estado','libre');
        $this->db->order_by('r1.hora_reserva','ASC');
        $query = $this->db->get('reservas r1');

        //si hay horas disponibles las devolvemos
        if ($query -> num_rows() > 0) 
        {
            return $query->result();

        }else{
            return false;
        }
    }

}
//end calendario_model