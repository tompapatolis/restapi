<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mod_rest extends CI_Model{

    function __construct() {        
    }

function ip2location($ip) {
    if(!$this->system_core->isValidIP($ip)) {return array('Undefined','');}

    $ipno = ip2long($ip);
    $q_string = "SELECT country_name, country_code FROM ip2loc_ipv4 WHERE $ipno >= ip_from AND $ipno <= ip_to LIMIT 1";
    $query = $this->db->query($q_string);
    if($query->num_rows()>0) {
        return array($query->row()->country_name,$query->row()->country_code);
    }
    else {return array('Undefined','');}
}

function getFlag($countrycode) {
    $path = './media/flags/' . strtolower($countrycode) . '.svg';

    if (file_exists($path)) {
        $data = file_get_contents($path);
        $base64 = 'data:image/svg+xml;base64,' . base64_encode($data);
    } else {
        $base64 = '';
    }
    
    return $base64;
}

} // END Mod_rest

