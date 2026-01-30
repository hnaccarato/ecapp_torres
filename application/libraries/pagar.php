<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Vendor\mercadopago;

class pagar{
    
    public $config;    
    
    public function __construct(){
        
        //get singleton instance.
        $CI = &get_instance();        

        //setting config
    }

    public function set_mp($config){
        $mode 							= $config['mode'];
        $this->config['client_id'] 		= $config['ci'];
        $this->config['client_secret'] 	= $config['cs'];
        $this->config['public_key'] 	= $config['public_key_'.$mode];
        $this->config['access_token'] 	= $config['access_token_'.$mode];
      	MercadoPago\SDK::setAccessToken('TEST-3406884711206892-090313-372e6531be9ea4e5c10586c3fb6fb9c6-467717552');
	
    }

    
    public function create_preference($preference_data)
    {
    	
    	$preference = new MercadoPago\Preference();
    	$item = new MercadoPago\Item();
    	$item->title = 'Mi producto';
    	$item->quantity = 1;
    	$item->unit_price = 75.56;
    	$preference->items = array($item);
    	$preference->save();
    	return $preference;
		  
    }

    
    
}