<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

defined('BASEPATH') OR exit('No direct script access allowed');

class Rest_server extends CI_Controller {

    public function index()
    {
        $this->load->helper('url');

        $this->load->view('rest_server');
    }
}
