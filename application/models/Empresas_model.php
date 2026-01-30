<?php
 
class Empresas_model extends CI_Model
{

    var $column_order = array(null,'empresas.nombre');            
    var $column_search = array('empresas.id','empresas.nombre'); 

    function __construct()
    {
        parent::__construct();
    }




    /*
    * Get Datatables
    */

    function get_datatables()
    {
        $this->_get_datatables_query();
        if(isset($_POST['length'])){
            if($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        
        $query = $this->db->get();
        return $query->result();
    }


    private function _get_datatables_query()
    {
        $this->db->from('empresas');
    
        $i = 0;
     
        foreach ($this->column_search as $item)
        {
            if(isset($_POST['search']['value']))
            {
                 
                if($i===0)
                {
                    $this->db->group_start(); 
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
    
                if(count($this->column_search) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $i++;
        }
         
        if(isset($_POST['order']))
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }


    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function count_all($active = true)
    {
        $this->db->from('empresas');
        return $this->db->count_all_results();
    }
    

    /*
    * Fin de Datatable
    */
    
    /*
     * Get empresa by id
     */
    function get_empresa($id)
    {
        return $this->db->get_where('empresas',array('id'=>$id))->row_array();
    }
    
    /*
     * Get all empresas count
     */

    function get_all_empresas_count()
    {
        $this->db->from('empresas');
        return $this->db->count_all_results();
    }
        
    /*
     * Get all empresas
     */
    function get_all_empresas($params = array())
    {
        $this->db->order_by('id', 'desc');
        if(isset($params) && !empty($params))
        {
            $this->db->limit($params['limit'], $params['offset']);
        }
        return $this->db->get('empresas')->result_array();
    }
        
    /*
     * function to add new empresa
     */
    function add_empresa($params)
    {
        $this->db->insert('empresas',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update empresa
     */
    function update_empresa($id,$params)
    {
        $this->db->where('id',$id);
        return $this->db->update('empresas',$params);
    }
    
    /*
     * function to delete empresa
     */
    function delete_empresa($id)
    {
        return $this->db->delete('empresas',array('id'=>$id));
    }

    /*my empresa by edificio_id*/

    function my_empresa($edificio_id){

        $this->db->select('empresas.*');
        $this->db->join('edificios','edificios.empresa_id = empresas.id');
        $this->db->group_by('empresas.id');
        
        $rs =  $this->db->get_where('empresas',array('edificios.id'=>$edificio_id));
        
        if($rs->num_rows() > 0){
            return  $rs->row_array();
        }else{
            return FALSE;
        }

    }    

    function my_url($url){
       
        $this->db->select('empresas.*');
        $rs =  $this->db->get_where('empresas',array('empresas.url'=>$url));

        if($rs->num_rows() > 0){
            return  $rs->row_array();
        }else{
            return FALSE;
        }
    }
    
    
}