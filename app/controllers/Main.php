<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct() {
        parent::__construct();        
        $this->load->model('mod_rest');       
    }  

public function index() {
	//109.242.108.77 Greece
	//87.146.133.96 Germany
	
	$data = array('ip' => '109.242.108.77');
	$result = $this->mod_rest->CallAPI('POST','https://api.tomsnews.net/api/country',$data);
	// $data = array('country_code' => 'DE');
	// $result = $this->mod_rest->CallAPI('POST','http://[::1]/projects/restapi/api/flag',$data);
	echo $result;
	//echo "tomsnews.net API v1.100.1";
}

public function api($request=NULL) {
	// Check APIKEY
	$headers = apache_request_headers();
	if (isset($headers['APIKEY'])) {
		$api_key = $headers['APIKEY'];
		if($api_key!='p2vMXw9NKkzAnfl4QRCagDE1t') {
			$output = array('message' => 'invalid APIKEY');
			echo json_encode($output);
			return;
		}
	} else {
		$output = array('message' => 'no APIKEY');
		echo json_encode($output);
		return;		
	}

	$json_str = file_get_contents('php://input');
	$json_obj = json_decode($json_str, TRUE);
	$output   = $this->mod_rest->processAPI($request,$json_obj);	
 	header('Access-Control-Allow-Origin: *');
  	header('Content-Type: application/json');
	echo json_encode($output);
}
	
} // END Main
