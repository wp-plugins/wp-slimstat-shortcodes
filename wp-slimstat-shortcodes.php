<?php
/*
Plugin Name: WP SlimStat ShortCodes
Plugin URI: http://lab.duechiacchiere.it/index.php?topic=2.msg2#post_shortcodes
Description: Adds support for shortcodes to WP SlimStat
Version: 1.2
Author: Camu
Author URI: http://www.duechiacchiere.it/
*/

// Avoid direct access to this piece of code
if (__FILE__ == $_SERVER['SCRIPT_FILENAME'] ) {
  header('Location: /');
  exit;
}

// Load localization strings
load_plugin_textdomain('countries-languages', WP_PLUGIN_DIR .'/wp-slimstat/lang', '/wp-slimstat/lang');

// We rely on WP SlimStat Views Library
if (file_exists(WP_PLUGIN_DIR."/wp-slimstat/view/wp-slimstat-view.php"))
	require_once(WP_PLUGIN_DIR."/wp-slimstat/view/wp-slimstat-view.php");
else
	return false;

class wp_slimstat_shortcodes {

	/**
	 * Constructor -- Sets things up.
	 */
	public function __construct() {
		$this->list_shortcodes = array(
			
		);
		
		// These filter replace the metatag with the actual HTML code
		add_filter('the_content', array( &$this,'replace_shortcodes') );
		add_filter('widget_text', array( &$this,'replace_shortcodes') );
	}
	// end __construct

	/**
	 * Retrieves the information from the database
	 */
	private function _generate_metrics($_label = '', $_filters = ''){		
		if (empty($_label)) return '';
		
		global $wpdb;
		
		// Reset MySQL timezone settings, our dates and times are recorded using WP settings
		$wpdb->query("SET @@session.time_zone = '+00:00'");
		
		if (is_array($_filters) && !empty($_filters))
			$wp_slimstat_view = new wp_slimstat_view($_filters);
		else 
			$wp_slimstat_view = new wp_slimstat_view();
		
		$result = '';
		$temp_array = array();
		
		switch($_label){
			case 'count_all_visitors':
				$result = $wp_slimstat_view->count_records('t1.visit_id > 0');
				break;
			case 'count_bots':
				$result = $wp_slimstat_view->count_records('visit_id = 0');
				break;
			case 'count_direct_visits':
				$wp_slimstat_view->count_records("domain = ''", "DISTINCT id");
				break;
			case 'count_exit_pages':
				$result = $wp_slimstat_view->count_exit_pages();
				break;
			case 'count_new_visitors':
				$result = $wp_slimstat_view->count_new_visitors();
				break;
			case 'count_pages_referred':
				$result = $wp_slimstat_view->count_records("domain <> ''", "DISTINCT resource");
				break;
			case 'count_raw_data':
				$result = $wp_slimstat_view->count_records('1=1', '*');
				break;
			case 'count_recent_404_pages':
				$result = $wp_slimstat_view->count_records("t1.resource LIKE '[404]%'", "DISTINCT t1.resource");
				break;
			case 'count_recent_browsers':
				$result = $wp_slimstat_view->count_records("tb.browser <> ''", "DISTINCT tb.browser");
				break;
			case 'count_referred_from_internal':
				$result = $wp_slimstat_view->count_records("domain = '{$_SERVER['SERVER_NAME']}'", "DISTINCT resource");
				break;
			case 'count_referers':
				$result = $wp_slimstat_view->count_records("domain <> '{$_SERVER['SERVER_NAME']}' AND domain <> ''", "domain");
				break;
			case 'count_search_engines':
				$result = $wp_slimstat_view->count_records("searchterms <> '' AND domain <> '{$_SERVER['SERVER_NAME']}' AND domain <> ''", "DISTINCT id");
				break;
			case 'count_total_pageviews':
				$result = $wp_slimstat_view->count_records('1=1', '*', false);
				break;
			case 'count_unique_ips':
				$result = $wp_slimstat_view->count_records('1=1', 'DISTINCT ip');
				break;	
			case 'count_unique_referers':
				$result =  $wp_slimstat_view->count_records("domain <> '{$_SERVER['SERVER_NAME']}' AND domain <> ''", "DISTINCT domain");
				break;
			case 'get_browsers':
				$temp_array = $wp_slimstat_view->get_top('tb.browser, tb.version', '', "tb.browser <> ''", 'browsers');
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['browser']} <span class='slimstat-second-column'>{$a_row['version']}</span> <span class='slimstat-third-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_data_size':
				$result = $wp_slimstat_view->get_data_size();
				break;
			case 'get_details_recent_visits':
				$temp_array = $wp_slimstat_view->get_recent('t1.id', 't1.ip, t1.language, t1.resource, t1.searchterms, t1.visit_id, t1.country, t1.domain, t1.referer, tb.browser, tb.version, tb.platform', 't1.visit_id > 0', 'browsers');
				foreach($temp_array as $a_row){
					$a_row['platform'] = __($a_row['platform'],'countries-languages');
					$a_row['language'] = __('l-'.$a_row['language'],'countries-languages');
					$a_row['country'] = __('c-'.$a_row['country'],'countries-languages');
					$a_row['dt'] = date_i18n($wp_slimstat_view->date_time_format, $a_row['dt']);
					$result .= "<li>{$a_row['ip']} <span class='slimstat-second-column'>{$a_row['language']}</span> 
						<span class='slimstat-third-column'>{$a_row['country']}</span>
						<span class='slimstat-fourth-column'>{$a_row['domain']}{$a_row['referer']}</span>
						<span class='slimstat-fifth-column'>{$a_row['resource']}</span>
						<span class='slimstat-sixth-column'>{$a_row['browser']} {$a_row['version']}</span>
						<span class='slimstat-seventh-column'>{$a_row['searchterms']}</span>
						<span class='slimstat-eighth-column'>{$a_row['platform']}</span>
						<span class='slimstat-nineth-column'>{$a_row['dt']}</span></li>\n";
				}
				break;
			case 'get_other_referers':
				$temp_array = $wp_slimstat_view->get_top('t1.domain', 't1.referer', "searchterms = '' AND domain <> '{$_SERVER['SERVER_NAME']}' AND domain <> ''");
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['domain']} <span class='slimstat-second-column'>{$a_row['referer']}</span> <span class='slimstat-third-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_raw_data':
				$temp_array = $wp_slimstat_view->get_recent('t1.resource', 't1.ip, t1.user, t1.language, t1.searchterms, t1.visit_id, t1.country, t1.domain, t1.referer, tb.browser, tb.version, tb.platform', '', 'browsers');
				foreach($temp_array as $a_row){
					$a_row['platform'] = __($a_row['platform'],'countries-languages');
					$a_row['language'] = __('l-'.$a_row['language'],'countries-languages');
					$a_row['country'] = __('c-'.$a_row['country'],'countries-languages');
					$a_row['dt'] = date_i18n($wp_slimstat_view->date_time_format, $a_row['dt']);
					$result .= "<li>{$a_row['ip']} <span class='slimstat-second-column'>{$a_row['language']}</span> 
						<span class='slimstat-third-column'>{$a_row['country']}</span>
						<span class='slimstat-fourth-column'>{$a_row['domain']}{$a_row['referer']}</span>
						<span class='slimstat-fifth-column'>{$a_row['resource']}</span>
						<span class='slimstat-sixth-column'>{$a_row['browser']} {$a_row['version']}</span>
						<span class='slimstat-seventh-column'>{$a_row['searchterms']}</span>
						<span class='slimstat-eighth-column'>{$a_row['platform']}</span>
						<span class='slimstat-nineth-column'>{$a_row['dt']}</span></li>\n";
				}
				break;
			case 'get_recent_404_pages':
				$temp_array = $wp_slimstat_view->get_recent('t1.resource', 't1.ip, t1.language, t1.country, t1.domain, t1.resource', "resource LIKE '[404]%'");
				foreach($temp_array as $a_row){
					$a_row['language'] = __('l-'.$a_row['language'],'countries-languages');
					$a_row['country'] = __('c-'.$a_row['country'],'countries-languages');
					$a_row['dt'] = date_i18n($wp_slimstat_view->date_time_format, $a_row['dt']);
					$result .= "<li>{$a_row['ip']} <span class='slimstat-second-column'>{$a_row['language']}</span> 
						<span class='slimstat-third-column'>{$a_row['country']}</span>
						<span class='slimstat-fourth-column'>{$a_row['domain']}</span>
						<span class='slimstat-fifth-column'>{$a_row['resource']}</span>
						<span class='slimstat-sixth-column'>{$a_row['dt']}</span></li>\n";
				}
				break;
			case 'get_recent_bouncing_pages':
				$temp_array = $wp_slimstat_view->get_recent('t1.resource', 't1.domain', '', '', 'HAVING COUNT(visit_id) = 1');
				foreach($temp_array as $a_row){
					$a_row['dt'] = date_i18n($wp_slimstat_view->date_time_format, $a_row['dt']);
					$result .= "<li>{$a_row['resource']} <span class='slimstat-second-column'>{$a_row['domain']}</span> 
						<span class='slimstat-third-column'>{$a_row['dt']}</span></li>\n";
				}
				break;
			case 'get_recent_downloads':
				$temp_array = $wp_slimstat_view->get_recent_outbound(1);
				foreach($temp_array as $a_row){
					$a_row['dt'] = date_i18n($wp_slimstat_view->date_time_format, $a_row['dt']);
					$result .= "<li>{$a_row['outbound_resource']} <span class='slimstat-second-column'>{$a_row['dt']}</span></li>\n";
				}
				break;
			case 'get_recent_internal_searches':
				$temp_array =  $wp_slimstat_view->get_recent('t1.searchterms', '', "(resource = '__l_s__' OR resource = '')");
				foreach($temp_array as $a_row){
					$a_row['dt'] = date_i18n($wp_slimstat_view->date_time_format, $a_row['dt']);
					$result .= "<li>{$a_row['searchterms']} <span class='slimstat-second-column'>{$a_row['dt']}</span></li>\n";
				}
				break;	
			case 'get_recent_keywords_pages':
				$temp_array = $wp_slimstat_view->get_recent('t1.searchterms', 't1.resource, t1.domain, t1.referer');
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['searchterms']} <span class='slimstat-second-column'>{$a_row['resource']}</span> 
						<span class='slimstat-third-column'>{$a_row['domain']}</span>
						<span class='slimstat-fourth-column'>{$a_row['referer']}</span></li>\n";
				}
				break;
			case 'get_recent_outbound':
				$temp_array = $wp_slimstat_view->get_recent_outbound(0);
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['outbound_resource']} <span class='slimstat-second-column'>{$a_row['resource']}</span> 
						<span class='slimstat-third-column'>{$a_row['ip']}</span></li>\n";
				}
				break;
			case 'get_top_browsers_by_operating_system':
				$temp_array = $wp_slimstat_view->get_top('tb.browser, tb.version, tb.platform', '', "t1.visit_id > 0", 'browsers');
				foreach($temp_array as $a_row){
					$a_row['platform'] = __($a_row['platform'],'countries-languages');
					$result .= "<li>{$a_row['browser']} <span class='slimstat-second-column'>{$a_row['version']}</span> 
						<span class='slimstat-third-column'>{$a_row['platform']}</span>
						<span class='slimstat-fourth-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_top_exit_pages':
				$temp_array = $wp_slimstat_view->get_top('t1.resource', '', "visit_id > 0 AND resource <> '' AND resource <> '__l_s__'");
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['resource']} <span class='slimstat-second-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_top_operating_systems':
				$temp_array = $wp_slimstat_view->get_top('tb.platform', '', '', 'browsers');
				foreach($temp_array as $a_row){
					$a_row['platform'] = __($a_row['platform'],'countries-languages');
					$result .= "<li>{$a_row['platform']} <span class='slimstat-second-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_top_screenres':
				$temp_array = $wp_slimstat_view->get_top('tss.resolution', '', '', 'screenres');
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['resolution']} <span class='slimstat-second-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_top_search_engines':
				$temp_array = $wp_slimstat_view->get_top('t1.domain', '', "searchterms <> '' AND domain <> '{$_SERVER['SERVER_NAME']}'");
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['domain']} <span class='slimstat-second-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			default:
				break;
		}
		
		return $result;
	}
	// end _generate_metrics
	
	// Function: replace_shortcodes
	// Description: Updates the content of a post to replace the placeholder with the actual ad
	// Input: content to manipulate
	// Output: updated content
	public function replace_shortcodes($_content){

		$new_content = $_content;
		$filters = array();
		
		// First of all, let's see if the user has defined any filters
		preg_match_all("/<!--slimstat-filter:([a-z\_]*):([a-z\ ]*):(.*)-->/U", $_content, $matches);
		
		foreach($matches[1] as $a_filter_idx => $a_filter){
			$filters[$a_filter.'-op'] = $matches[2][$a_filter_idx];
			$filters[$a_filter] = $matches[3][$a_filter_idx];
			$new_content = str_replace($matches[0][$a_filter_idx], '', $new_content);
		}
		
		// Let's look for simple shortcodes (no filters)
		preg_match_all("/<!--slimstat:([0-9a-z\_]*)-->/U", $_content, $matches);
		foreach($matches[1] as $a_label_idx => $a_label){
			$metrics = $this->_generate_metrics($a_label, $filters);
			$new_content = str_replace($matches[0][$a_label_idx], $metrics, $new_content);
		}
	
		return $new_content;
	}
	// end replace_shortcodes
}
// end of class declaration

$wp_slimstat_shortcodes = new wp_slimstat_shortcodes();

?>