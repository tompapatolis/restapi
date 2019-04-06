<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mod_rest extends CI_Model{

    function __construct() {}

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

function callAPI($method, $url, $data){
   // https://www.weichieprojects.com/blog/curl-api-calls-with-php/
    if ($data) {$json_data = json_encode($data);}    
    $curl = curl_init();

    switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($json_data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($json_data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);                              
         break;
      default:
         if ($json_data)
            $url = sprintf("%s?%s", $url, http_build_query($json_data));
    }

    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'APIKEY: 123',
      'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
}

function processAPI($request=NULL,$json_obj=NULL) {

    switch ($request) {
        case 'country':
            $ip     = $json_obj['ip'];
            $ip_data = $this->mod_rest->ip2location($ip);

            $output = array(
                'country'      => $ip_data[0],
                'country_code' => $ip_data[1]
            );
            break;
        case 'flag':
            $country_code = $json_obj['country_code'];
            $output = array(
                'flag' => $this->mod_rest->getFlag($country_code)
            );      
            break;
        default:
            $output = array('message' => 'nothing to return');
            break;
    }
    return $output;

}

} // END Mod_rest

