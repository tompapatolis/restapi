<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {

	function __construct(){
		parent::__construct();
	}

   /* Display Function */
   public function display($view,$data=array()) {
        $CI       = & get_instance();
        $CI->load->view('inc/html_header');                
        $CI->load->view($view, $data);        
        $CI->load->view('inc/html_footer');
    }

} //MY_Loader