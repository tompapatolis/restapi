<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class System_db {

    function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

function get_table($table,$columns=NULL,$criteria=NULL,$order_by=NULL,$limit=NULL) {
    $q_string = "SELECT ";
    if(!empty($columns))  { $q_string .= $columns; } else {$q_string .="*";}
    $q_string .= " FROM " . $table;
    if(!empty($criteria)) { $q_string .= " WHERE " . $criteria; }
    if(!empty($order_by)) { $q_string .= " ORDER BY " . $order_by; }
    if(!empty($limit))    { $q_string .= " LIMIT " . $limit; }
    $query   = $this->CI->db->query($q_string);
    $result  = $query->result_array();
    return $result;
}

function get_row($table, $criteria) {
    $q_string  = "SELECT * FROM " . $table . " WHERE " . $criteria;
    $query     = $this->CI->db->query($q_string);
    $result    = $query->row_array();
    return $result;
}

function get_column($table, $return_column,  $criteria=NULL) {
    $q_string  = "SELECT " . $return_column . " FROM " . $table;
    if(!empty($criteria)) { $q_string .= " WHERE " . $criteria; }
    $query     = $this->CI->db->query($q_string);
    $result    = $query->result_array();
    return $result;
}

function get_column_array($table, $return_column,  $criteria=NULL) {
    $q_string  = "SELECT " . $return_column . " FROM " . $table;
    if(!empty($criteria)) { $q_string .= " WHERE " . $criteria; }
    $query     = $this->CI->db->query($q_string);
    $result    = $query->result_array();
    $arr       = array_column($result,$return_column);
    return $arr;
}

function get_value($table, $return_column, $criteria) {
    $q_string  = "SELECT " . $return_column . " FROM " . $table . " WHERE " . $criteria;
    $query     = $this->CI->db->query($q_string);
    if($query->num_rows()>0) {return $query->row()->$return_column;}
    else {return NULL;}
}

function rand_value($table, $return_column, $criteria) {
    $q_string  = "SELECT " . $return_column . " FROM " . $table . " WHERE " . $criteria . " ORDER BY RAND() LIMIT 1";
    $query     = $this->CI->db->query($q_string);
    if($query->num_rows()>0) {return $query->row()->$return_column;}
    else {return '';}    
}

function countRecords($table, $params='') {
    $query_string = "SELECT COUNT(id) AS count FROM " . $table;    
    if ($params!='') {$query_string .= " WHERE " . $params;}
    $result = $this->CI->db->query($query_string)->row_array();
    return $result["count"];
}

function entryExists($table,$column,$value) {
    $q     = "SELECT " . $column . " FROM " . $table . " WHERE " . $column . "='" . $value . "'";
    $query = $this->CI->db->query($q);
    if($query->num_rows()>0) {return TRUE;} else {return FALSE;}
}

function db_date(string $date): string {
    $timestamp = strtotime($date);
    $date_formated = date('Y-m-d H:i:s', $timestamp);
    return $date_formated;
}

function db_now() {
    $CI =& get_instance();
    $timezone_dif = $CI->config->item('cfg_timezone_interval'); 
    $q_string     = "SELECT (NOW() + INTERVAL $timezone_dif HOUR) as db_now";
    $query        = $this->CI->db->query($q_string);
    return $query->row()->db_now;
}

} //END System_db