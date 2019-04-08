<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Framework extends CI_Controller {

	function __construct() {
        parent::__construct();
        $this->load->helper('easy_urls');
    }  

public function index() {
	$this->load->display('buttons');
}
	
} // END Stats
