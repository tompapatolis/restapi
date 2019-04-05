<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct() {
        parent::__construct();        
        $this->load->model('mod_rest');       
    }  

public function index() {
	$ip_data = $this->mod_rest->ip2location('87.146.133.96');
	$data = array(
		'msg' 	  	   => 'api.tomsnews.net &rarr; up and running',
		'country' 	   => $ip_data[0],
		'country_code' => $ip_data[1],
		'flag_code'    => $this->mod_rest->getFlag($ip_data[1])
	);
	$this->load->view('output', $data);
}
	
} // END Main
