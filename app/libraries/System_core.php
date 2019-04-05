<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class System_core {

function check_admin() {
    $CI =& get_instance();
    if(!$CI->session->userdata('isAdmin')){redirect(base_url());}
}

function time_elapsed_string($ptime) {
    $CI =& get_instance();
    $timezone_server = $CI->config->item('cfg_timezone_server');    
    date_default_timezone_set($timezone_server);
    $etime = time() - strtotime($ptime);
    if ($etime < 1) {return '0 seconds';}

    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    $a_plural = array( 'year'   => 'years',
                       'month'  => 'months',
                       'day'    => 'days',
                       'hour'   => 'hours',
                       'minute' => 'minutes',
                       'second' => 'seconds'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}

function auto_link_text($text) {
    $output= "";

    // Autolink URLs
    $reg_exUrl = "/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/";
    if(preg_match($reg_exUrl, $text)) {
        $output = preg_replace($reg_exUrl, '<a href="${1}" target="_blank">${1}</a> ', $text);
    } else {$output = $text;}
    // Autolink Hashtags
    $reg_exHashtag = "/(#\w+)/u";
    if(preg_match($reg_exHashtag, $output)) {
        $output = preg_replace($reg_exHashtag, '<a class="hashtag" href='.site_url("core/hashtag").'?hashtag=&&&&&${1}>${1}</a> ', $output);
        $output = str_replace("&&&&&#","",$output);
    } else {$output = $output;}
    return $output;

}

function number_format_thousands($n,$decimals=0) {
    return number_format($n, $decimals, ',', '.');
}

function convertAvatar64($avatar) {
    if ($avatar=='no_avatar.png'){$avatar='';}
    $avatar_path = base_url()."uploads/avatars/" . "tn_" . $avatar;
    $exists      = file_exists('./uploads/avatars/' . 'tn_' . $avatar);

    if ( $avatar != '' && $exists) {
        $avatar_type   = pathinfo($avatar_path, PATHINFO_EXTENSION);
        $avatar_data   = file_get_contents($avatar_path);
        $avatar_base64 = 'data:image/' . $avatar_type . ';base64,' . base64_encode($avatar_data);
    } else {$avatar_base64='';}

    return $avatar_base64;
}

function format_date($date) {
    $CI =& get_instance();
    $timezone_server = $CI->config->item('cfg_timezone_server'); 
    date_default_timezone_set($timezone_server);
    setlocale(LC_TIME, 'greek');
    $the_date = strtotime($date);
    return date(DATE_RFC1123, $the_date);    
}

function formatToGreekDate($date){
    //Expected date format yyyy-mm-dd hh:MM:ss
    $greekMonths = array('Ιανουαρίου','Φεβρουαρίου','Μαρτίου','Απριλίου','Μαΐου','Ιουνίου','Ιουλίου','Αυγούστου','Σεπτεμβρίου','Οκτωβρίου','Νοεμβρίου','Δεκεμβρίου');
    $greekdays = array('Δευτέρα','Τρίτη','Τετάρτη','Πέμπτη','Παρασκευή','Σάββατο','Κυριακή');
    $time = strtotime($date);
    $newformat = date('Y-m-d',$time);
    return $greekdays[date('N', strtotime($newformat))-1].' '. date('j', strtotime($newformat)).' '.$greekMonths[date('m', strtotime($newformat))-1]. ' '. date('Y', strtotime($newformat));
}

function extractKeyWords($string) {
  mb_internal_encoding('UTF-8');
  $stopwords = array();
  $string = preg_replace('/[\pP]/u', ' ', trim(preg_replace('/\s\s+/iu', '', mb_strtolower($string))));
  $matchWords = array_filter(explode(' ',$string) , function ($item) use ($stopwords) { return !($item == '' || in_array($item, $stopwords) || mb_strlen($item) <= 5 || is_numeric($item));});
  $wordCountArr = array_count_values($matchWords);
  arsort($wordCountArr);
  $arr = array_keys(array_slice($wordCountArr, 0, 5));
  return implode(",",$arr);
}

function substr_word($body,$maxlength){
    $body = strip_tags($body);
    if (mb_strlen($body)<$maxlength) return $body;
    $body = mb_substr($body, 0, $maxlength);
    $rpos = mb_strrpos($body,' ');
    if ($rpos>0) $body = mb_substr($body, 0, $rpos);
    return $body;
}

function shorten_string($string, $wordsreturned) {
    $string = $this->mb_strip_tags($string);
    $retval = $string;
    $array = explode(" ", $string);
    if (count($array)<=$wordsreturned) {$retval = $string;}
    else {
        array_splice($array, $wordsreturned);
        $retval = implode(" ", $array)." ...";
    }
    return $retval;
}

function number_format_short( $n, $precision = 1 ) {
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;
}

function clear_string($str) {
    $str = strip_tags($str);
    return $str;
}

function filename2title($string) {
    $new_string = strip_tags($string);
    $new_string = pathinfo($new_string, PATHINFO_FILENAME);
    $new_string = str_replace("_", " ", $new_string);
    $new_string = str_replace("-", " ", $new_string);
    $new_string = str_replace("+", " ", $new_string);
    $new_string = ucwords($new_string);
    return $new_string;
}

function deleteFiles($path){
    $files = glob($path.'*'); // get all file names
    foreach($files as $file){ // iterate files
      if(is_file($file))
        unlink($file); // delete file
    }   
}

function phpinfo_array() {
    $this->CI =& get_instance();
    $this->CI->load->database();
    $info_arr = array(
        'PHP Version'       => phpversion(),
        'Zend Version'      => zend_version(),
        'Loaded Extensions' => implode(", ", get_loaded_extensions()),
        'Database Platform' => $this->CI->db->platform(),
        'Database Version'  => $this->CI->db->version()
    );
    return $info_arr;
} //phpinfo_array


function merge_arrays_key($array1,$array2,$key) {
    foreach($array1 as &$value1) {
        foreach ($array2 as $value2) {
            if($value1[$key] == $value2[$key]) {
                $value1 = array_merge($value1, $value2);
            }
        }
    }
    return $array1;
}

function diffMonth($from, $to) {
    $fromYear = date("Y", strtotime($from));
    $fromMonth = date("m", strtotime($from));
    $toYear = date("Y", strtotime($to));
    $toMonth = date("m", strtotime($to));
    if ($fromYear == $toYear) {return ($toMonth-$fromMonth)+1;}
    else {return (12-$fromMonth)+1+$toMonth;}
}

function countFiles() {
    $fi = iterator_count(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(FCPATH, \FilesystemIterator::SKIP_DOTS)));
    return $this->number_format_thousands($fi);
}

function myucfirst($str) {
    mb_internal_encoding('UTF-8');
    $fc = mb_strtoupper(mb_substr($str, 0, 1));
    return $fc.mb_substr($str, 1);
}

function array_group_by(array $arr, $key) : array
{
    if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
        trigger_error('array_group_by(): The key should be a string, an integer, a float, or a function', E_USER_ERROR);
    }
    $isFunction = !is_string($key) && is_callable($key);
    // Load the new array, splitting by the target key
    $grouped = [];
    foreach ($arr as $value) {
        $groupKey = null;
        if ($isFunction) {
            $groupKey = $key($value);
        } else if (is_object($value)) {
            $groupKey = $value->{$key};
        } else {
            $groupKey = $value[$key];
        }
        $grouped[$groupKey][] = $value;
    }
    // Recursively build a nested grouping if more parameters are supplied
    // Each grouped array value is grouped according to the next sequential key
    if (func_num_args() > 2) {
        $args = func_get_args();
        foreach ($grouped as $groupKey => $value) {
            $params = array_merge([$value], array_slice($args, 2, func_num_args()));
            $grouped[$groupKey] = call_user_func_array('array_group_by', $params);
        }
    }
    return $grouped;
}//array_group_by

function getfilesInFolder($folder) {
    $fileList = glob($folder . '/*');
    usort($fileList, function($a,$b){return filemtime($b) - filemtime($a);});

    $files = array();
    foreach($fileList as $filename){
        if(is_file($filename)){array_push($files,basename($filename));}   
    }
    return $files;
}

function BackupDB($path) {
    $CI =& get_instance();
    $CI->load->dbutil();
    $ignore = array('stats_bak','ip2loc_ipv4');
    $prefs = array('format' => 'zip', 'ignore' => $ignore);        
    $backup = $CI->dbutil->backup($prefs);
    $CI->load->helper('file');
    $timezone_owner = $CI->config->item('cfg_timezone_owner');
    date_default_timezone_set($timezone_owner);
    write_file($path.'/db_'.date("d-m-Y (G.i.s)").'.zip', $backup);
    return "done";
}

function findPicsInHTML($html) {
    $img_array = array();
    $doc = new DOMDocument();
    @$doc->loadHTML($html);
    $tags = $doc->getElementsByTagName('img');

    foreach ($tags as $tag) {
           $path = $tag->getAttribute('src');
           array_push($img_array,basename($path));
    }
    return $img_array;
}

function mb_strip_tags($str) {
   return strip_tags(trim(html_entity_decode($str, ENT_QUOTES, 'UTF-8'), "\xc2\xa0"));
}

function count_words($str) {
    $str = $this->mb_strip_tags($str);
    return preg_match_all('~[\p{L}\'\-\xC2\xAD]+~u', $str);
}

function formatToGreekDatetime($date){
    $greekMonths = array('Ιανουαρίου','Φεβρουαρίου','Μαρτίου','Απριλίου','Μαΐου','Ιουνίου','Ιουλίου','Αυγούστου','Σεπτεμβρίου','Οκτωβρίου','Νοεμβρίου','Δεκεμβρίου');
    $greekdays = array('Δευτέρα','Τρίτη','Τετάρτη','Πέμπτη','Παρασκευή','Σάββατο','Κυριακή');
    $str_date  = strtotime($date);
    $newformat = date('Y-m-d H:i:s',$str_date);
    $nf_str    = strtotime($newformat);
    $a_en      = array('am','pm');
    $a_gr      = array('πμ','μμ');
    return $greekdays[date('N', $nf_str)-1].' '. date('j', $nf_str).' '.$greekMonths[date('m', $nf_str)-1]. ' '.str_replace($a_en,$a_gr,date('Y, g:i:s a', $nf_str));
}

function pastSince($date_str) {
    $date1 = new DateTime($date_str);
    $date2 = $date1->diff(new DateTime());
    $pastSince  = $date2->y .' χρόνια, ';
    $pastSince .= $date2->m .' μήνες, ';
    $pastSince .= $date2->d .' μέρες, ';
    $pastSince .= $date2->h .' ώρες, ';
    $pastSince .= $date2->i .' λεπτά και ';
    $pastSince .= $date2->s .' δευτερόλεπτα';
    return $pastSince;
}

function daysSince($date_str) {
    $date1 = new DateTime($date_str);
    $date2 = $date1->diff(new DateTime());
    return $date2->days;
}

function isValidIP($ip) {
    // Validate an IPv4 IP address
   if ( false === filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
       return false;
   } else {return true;}
}

} // END System_core