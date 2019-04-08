<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mod_stats extends CI_Model{

    function __construct() {
        $this->load->library('user_agent');
    }

function trackVisitor($post_id=-1) {

/**
 * id, ip, os, browser, post_id, page_slug, url, mobile, robot, country, country_code, created
 * ALTER TABLE `stats_tomsnews`
   DROP `browser_ver`,
   DROP `referral`,
   DROP `languages`;
 */

    $location    = $this->ip2location($this->input->ip_address());
    $insert_data = array(
        'ip'           => $this->input->ip_address(),
        'os'           => $this->agent->platform(),
        'browser'      => $this->agent->browser(),
        'browser_ver'  => $this->agent->version(),
        'post_id'      => $post_id,
        'url'          => current_url(),
        'referral'     => ($this->agent->is_referral()?$this->agent->referrer():''),
        'mobile'       => $this->agent->mobile(),
        'robot'        => $this->agent->robot(),
        'languages'    => implode(", ", $this->agent->languages()),
        'country'      => $location[0],
        'country_code' => $location[1]
    );
    $this->db->insert("stats", $insert_data);
}

/**
 * Statistics v2
 */

function getStats2($table) {
    //* Users Now
    $q = "
        SELECT COUNT(distinct ip) AS cnt
        FROM $table
        WHERE created >= (NOW() - INTERVAL 10 MINUTE)
        ";
    $query = $this->db->query($q);
    $stats['users_now'] = $query->row()->cnt;

    //* Daily Hits
    $q = "
        SELECT COUNT(id) AS cnt, DATE_FORMAT(created, '%d %M %Y') AS thedate
        FROM $table
        GROUP BY DATE(created)
        ORDER BY created DESC
        LIMIT 15
        ";
    $query = $this->db->query($q);
    $stats['daily_hits'] = $query->result_array();

    //* Daily Visitors
    $q = "
        SELECT DATE_FORMAT(created, '%d %M %Y') AS thedate, COUNT(DISTINCT ip) as cnt
        FROM $table
        GROUP BY DATE(created)
        ORDER BY created DESC
        LIMIT 15
        ";
    $query = $this->db->query($q);
    $stats['daily_visitors'] = $query->result_array();

    //* Hourly Visitors
    $timezone_dif = 6;
    $q = "
        SELECT HOUR(DATE_ADD(created, INTERVAL $timezone_dif HOUR)) AS thehour, COUNT(DISTINCT ip) as cnt
        FROM $table
        GROUP BY thehour
        ";     
    $query = $this->db->query($q);
    $stats['hourly_visitors'] = $query->result_array();
    $hourly_sum = array_sum(array_column($stats['hourly_visitors'],'cnt'));
    foreach($stats['hourly_visitors'] as &$hourly_item) {
        $hourly_item['perc'] = round($hourly_item['cnt']*100 / $hourly_sum,2);
    }

    //* Hits Today
    $q = "
        SELECT COUNT(id) AS cnt
        FROM $table
        WHERE DATE(created)=CURDATE()
        ";
    $query = $this->db->query($q);
    $stats['hits_today'] = $query->row()->cnt;

    //* Visitors Today
    $q = "
        SELECT COUNT(DISTINCT ip) AS cnt
        FROM $table
        WHERE DATE(created)=CURDATE()
        ";
    $query = $this->db->query($q);    
    $stats['visitors_today'] = $query->row()->cnt;

    //* Mobile Users
    $q = "
        SELECT ROUND((SELECT COUNT(id) FROM $table WHERE mobile<>'') / 
        (SELECT COUNT(id) FROM $table)*100,1) AS cnt
        ";
    $query = $this->db->query($q);
    $stats['mobile_users'] = $query->row()->cnt;

    //* Ios Devices
    $q = "
        SELECT ROUND((SELECT COUNT(id) FROM $table WHERE mobile='Apple iPhone' OR mobile='iPad') / 
        (SELECT COUNT(id) FROM $table WHERE mobile<>'')*100,1) AS cnt
        ";
    $query = $this->db->query($q);
    $stats['ios_devices'] = $query->row()->cnt;

    //* Countries
    $q = "
        SELECT country, country_code, count(DISTINCT ip) as cnt
        FROM $table
        WHERE country <> 'Undefined' AND country <> '0'
        GROUP BY country
        ORDER BY cnt DESC
        ";     
    $query = $this->db->query($q);
    $stats['countries'] = $query->result_array();   
    $sum_countries = array_sum(array_column($stats['countries'],'cnt'));
    foreach($stats['countries'] as &$item) {
        $item['perc'] = round($item['cnt']*100 / $sum_countries,2);
    }

    //* PHP Info
    $stats['php_info'] = $this->system_core->phpinfo_array();

    //* Popular Posts

    //* Popular Pages

    //* Average Visitors
    $q = "
        SELECT DATE_FORMAT(created, '%d %M %Y') AS thedate, COUNT(DISTINCT ip) as cnt
        FROM $table
        GROUP BY DATE(created)
        ORDER BY created DESC
        LIMIT 15 OFFSET 1
        ";
    $query = $this->db->query($q);
    $daily_visitors = $query->result_array();
    $sumall = array_sum(array_column($daily_visitors,'cnt'));

    $stats['avg_visitors_day']   = ceil($sumall / 15);
    $stats['avg_visitors_month'] = ceil($stats['avg_visitors_day']*30);
    $stats['avg_visitors_year']  = ceil($stats['avg_visitors_day']*365);

    $stats['avg_visitors_day']   = $this->system_core->number_format_thousands($stats['avg_visitors_day']);
    $stats['avg_visitors_month'] = $this->system_core->number_format_thousands($stats['avg_visitors_month']);
    $stats['avg_visitors_year']  = $this->system_core->number_format_thousands($stats['avg_visitors_year']);

    //* Growth
    $q = "
        SELECT DATE_FORMAT(created, '%d %M %Y') AS thedate, COUNT(DISTINCT ip) as cnt
        FROM $table
        GROUP BY DATE(created)
        ORDER BY created DESC
        LIMIT 15 OFFSET 16
        ";
    $query = $this->db->query($q);
    $old_daily_visitors = $query->result_array();
    $old_sumall = array_sum(array_column($old_daily_visitors,'cnt'));   
    if($old_sumall != 0) {
        $stats['growth'] = round(($sumall - $old_sumall)*100 / $old_sumall,2);
    } else {$stats['growth'] = 0;}

    //* Return Stats
    return $stats;
}

function getStats() {
    $stats = array();
    
    // Daily Hits
    $q_string  = "SELECT COUNT(id) AS cnt, DATE_FORMAT(created, '%d %M %Y') AS thedate FROM stats GROUP BY DATE(created) ORDER BY created DESC LIMIT 15";
    $query    = $this->db->query($q_string);
    $stats['daily_hits'] = $query->result_array();

    // Daily Visitors
    $q_string  = "SELECT DATE_FORMAT(created, '%d %M %Y') AS thedate, COUNT(DISTINCT ip) as cnt FROM stats GROUP BY DATE(created) ORDER BY created DESC LIMIT 15";
    $query    = $this->db->query($q_string);
    $stats['daily_visitors'] = $query->result_array();

    // Hourly Visitors
    $timezone_dif = $this->config->item('cfg_timezone_interval');
    $q_string = "
        SELECT HOUR(DATE_ADD(created, INTERVAL $timezone_dif HOUR)) AS thehour,
        COUNT(DISTINCT ip) as cnt
        FROM stats
        GROUP BY thehour";     
    $query = $this->db->query($q_string);
    $stats['hourly_visitors'] = $query->result_array();
    $hourly_sum = array_sum(array_column($stats['hourly_visitors'],'cnt'));
    foreach($stats['hourly_visitors'] as &$hourly_item) {
        $hourly_item['perc'] = round($hourly_item['cnt']*100 / $hourly_sum,2);
    } // END for each  

    // Users Online
    $q_string  = "SELECT COUNT(distinct ip) as cnt from stats WHERE created >= (NOW() - INTERVAL 5 MINUTE)";
    $query    = $this->db->query($q_string);
    $stats['users_online'] = $query->row_array();

    // Cron Data
    $stats['cron'] = $this->system_db->get_table('cron');

    // Mobile Users
    $q_string  = "SELECT ROUND(
      (SELECT COUNT(id) FROM stats WHERE mobile<>'') / 
      (SELECT COUNT(id) FROM stats)*100,1) AS cnt";
    $query    = $this->db->query($q_string);
    $stats['mobile_users'] = $query->row_array();

    //Hits Today
    $q_string  = "SELECT COUNT(id) AS cnt FROM stats WHERE DATE(created)=CURDATE()";
    $query    = $this->db->query($q_string);    
    $stats['hits_today'] = $query->row_array();

    //Visitors Today
    $q_string  = "SELECT COUNT(DISTINCT ip) AS cnt FROM stats WHERE DATE(created)=CURDATE()";
    $query    = $this->db->query($q_string);    
    $stats['visitors_today'] = $query->row_array();

    // Ios Devices
    $q_string  = "SELECT ROUND(
      (SELECT COUNT(id) FROM stats WHERE mobile='Apple iPhone' OR mobile='iPad') / 
      (SELECT COUNT(id) FROM stats WHERE mobile<>'')*100,1) AS cnt";
    $query    = $this->db->query($q_string);
    $stats['ios_devices'] = $query->row_array();    

    // Popular Posts
    $q_string  = "SELECT id, title, hits FROM posts WHERE status=1 ORDER BY hits DESC LIMIT 20";
    $query    = $this->db->query($q_string);
    $stats['popular_posts'] = $query->result_array();
    
    // Popular Pages
    $q_string  = "SELECT title, slug, hits FROM pages WHERE status=1 ORDER BY hits DESC LIMIT 20";
    $query    = $this->db->query($q_string);
    $stats['popular_pages'] = $query->result_array();

    // Total Posts
    $stats['total_posts'] = $this->system_db->countRecords('posts');

    // Total Pages
    $stats['total_pages'] = $this->system_db->countRecords('pages');    

    // PHP Info
    $stats['php_info'] = $this->system_core->phpinfo_array();

    // AVG Visitors
    $q_string  = "SELECT DATE_FORMAT(created, '%d %M %Y') AS thedate, COUNT(DISTINCT ip) as cnt FROM stats GROUP BY DATE(created) ORDER BY created DESC LIMIT 15 OFFSET 1";
    $query    = $this->db->query($q_string);
    $daily_visitors = $query->result_array();
    $sumall = 0;
    foreach ($daily_visitors as $item) {$sumall += $item['cnt'];}
    $stats['avg_visitors_day']   = ceil($sumall / 15);
    $stats['avg_visitors_month'] = ceil($stats['avg_visitors_day']*30);
    $stats['avg_visitors_year']  = ceil($stats['avg_visitors_day']*365);

    $stats['avg_visitors_day']   = $this->system_core->number_format_thousands($stats['avg_visitors_day']);
    $stats['avg_visitors_month'] = $this->system_core->number_format_thousands($stats['avg_visitors_month']);
    $stats['avg_visitors_year']  = $this->system_core->number_format_thousands($stats['avg_visitors_year']);

    // Growth
    $q_string  = "SELECT DATE_FORMAT(created, '%d %M %Y') AS thedate, COUNT(DISTINCT ip) as cnt FROM stats GROUP BY DATE(created) ORDER BY created DESC LIMIT 15 OFFSET 16";    
    $query    = $this->db->query($q_string);
    $old_daily_visitors = $query->result_array();
    $old_sumall = array_sum(array_column($old_daily_visitors,'cnt'));   
    if($old_sumall != 0) {
        $stats['growth'] = round(($sumall - $old_sumall)*100 / $old_sumall,2);
    } else {
        $stats['growth'] = 0;
    }

    // Countries
    $q_string = "
        SELECT country, country_code, count(DISTINCT ip) as cnt
        FROM stats
        WHERE country <> 'Undefined' AND country <> '0'
        GROUP BY country
        ORDER BY cnt DESC
        ";     
    $query = $this->db->query($q_string);
    $stats['countries'] = $query->result_array();   
    $sum_countries = array_sum(array_column($stats['countries'],'cnt'));
    foreach($stats['countries'] as &$item) {
        $item['perc'] = round($item['cnt']*100 / $sum_countries,2);
    } // END for each  
    
    return $stats;
}

/* ip2location */
function update_ip2location() {
    $q_string = "SELECT id,ip FROM stats WHERE country='0'";
    $query    = $this->db->query($q_string);
    $result   = $query->result_array();

    foreach ($result as $item) {
        $insert_array = $this->ip2location($item['ip']);
        $this->db->set('country', $insert_array[0]);
        $this->db->set('country_code', $insert_array[1]);
        $this->db->where('id', $item['id']);
        $this->db->update('stats');
    }
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

} // END Mod_stats