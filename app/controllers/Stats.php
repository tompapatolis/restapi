<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stats extends CI_Controller {

	function __construct() {
        parent::__construct();
        $this->load->helper('easy_urls');
        $this->load->model('mod_stats');
    }  

public function index() {
	$data = $this->mod_stats->getStats('stats_tomsnews');
	$this->load->display('stats', $data);
}
	
} // END Stats
