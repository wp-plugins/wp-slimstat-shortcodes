<?php
/*
Plugin Name: WP SlimStat ShortCodes
Plugin URI: http://lab.duechiacchiere.it/index.php?topic=2.msg2#post_shortcodes
Description: Adds support for shortcodes to WP SlimStat
Version: 1.0
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
require_once(WP_PLUGIN_DIR."/wp-slimstat/view/wp-slimstat-view.php");

class wp_slimstat_shortcodes {

	// Function: __construct
	// Description: Constructor -- Sets things up.
	// Input: none
	// Output: none
	public function __construct() {
		$this->list_shortcodes = array(
			
		);
	}
	// end __construct
	
	// Function: _generate_metrics
	// Description: Retrieves the information from the database
	// Input: method to call, filters
	// Output: the matching value(s)
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
			case 'count_bots';
				$result = $wp_slimstat_view->count_bots();
				break;
			case 'count_direct_visits';
				$result = $wp_slimstat_view->count_direct_visits();
				break;
			case 'count_exit_pages';
				$result = $wp_slimstat_view->count_exit_pages();
				break;
			case 'count_new_visitors';
				$result = $wp_slimstat_view->count_new_visitors();
				break;
			case 'count_pages_referred';
				$result = $wp_slimstat_view->count_pages_referred();
				break;
			case 'count_raw_data';
				$result = $wp_slimstat_view->count_raw_data();
				break;
			case 'count_recent_404_pages';
				$result = $wp_slimstat_view->count_recent_404_pages();
				break;
			case 'count_recent_browsers';
				$result = $wp_slimstat_view->count_recent_browsers();
				break;
			case 'count_referred_from_internal';
				$result = $wp_slimstat_view->count_referred_from_internal();
				break;
			case 'count_referers';
				$result = $wp_slimstat_view->count_referers();
				break;
			case 'count_search_engines';
				$result = $wp_slimstat_view->count_search_engines();
				break;
			case 'count_total_pageviews';
				$result = $wp_slimstat_view->count_total_pageviews();
				break;
			case 'count_unique_ips';
				$result = $wp_slimstat_view->count_unique_ips();
				break;	
			case 'count_unique_referers';
				$result = $wp_slimstat_view->count_unique_referers();
				break;
			case 'get_browsers';
				$temp_array = $wp_slimstat_view->get_browsers();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['browser']} <span class='slimstat-second-column'>{$a_row['version']}</span> <span class='slimstat-third-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_data_size';
				$result = $wp_slimstat_view->get_data_size();
				break;
			case 'get_details_recent_visits';
				$temp_array = $wp_slimstat_view->get_details_recent_visits();
				foreach($temp_array as $a_row){
					$platform_translated = __($a_row['platform'],'countries-languages');
					$language_translated = __('l-'.$a_row['language'],'countries-languages');
					$country_translated = __('c-'.$a_row['country'],'countries-languages');
					$result .= "<li>{$a_row['ip']} <span class='slimstat-second-column'>$language_translated</span> 
						<span class='slimstat-third-column'>$country_translated</span>
						<span class='slimstat-fourth-column'>{$a_row['domain']}{$a_row['referer']}</span>
						<span class='slimstat-fifth-column'>{$a_row['resource']}</span>
						<span class='slimstat-sixth-column'>{$a_row['browser']}</span>
						<span class='slimstat-seventh-column'>{$a_row['searchterms']}</span>
						<span class='slimstat-eighth-column'>$platform_translated</span>
						<span class='slimstat-nineth-column'>{$a_row['customdatetime']}</span></li>\n";
				}
				break;
			case 'get_other_referers';
				$temp_array = $wp_slimstat_view->get_other_referers();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['domain']} <span class='slimstat-second-column'>{$a_row['referer']}</span> <span class='slimstat-third-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_raw_data';
				$temp_array = $wp_slimstat_view->get_raw_data();
				foreach($temp_array as $a_row){
					$platform_translated = __($a_row['platform'],'countries-languages');
					$language_translated = __('l-'.$a_row['language'],'countries-languages');
					$country_translated = __('c-'.$a_row['country'],'countries-languages');
					$result .= "<li>{$a_row['ip']} <span class='slimstat-second-column'>$language_translated</span> 
						<span class='slimstat-third-column'>$country_translated</span>
						<span class='slimstat-fourth-column'>{$a_row['domain']}</span>
						<span class='slimstat-fifth-column'>{$a_row['resource']}</span>
						<span class='slimstat-sixth-column'>{$a_row['browser']} {$a_row['version']}</span>
						<span class='slimstat-seventh-column'>{$a_row['searchterms']}</span>
						<span class='slimstat-eighth-column'>$platform_translated</span>
						<span class='slimstat-nineth-column'>{$a_row['resolution']}</span>
						<span class='slimstat-tenth-column'>{$a_row['customdatetime']}</span></li>\n";
				}
				break;
			case 'get_recent_404_pages';
				$temp_array = $wp_slimstat_view->get_recent_404_pages();
				foreach($temp_array as $a_row){
					$language_translated = __('l-'.$a_row['language'],'countries-languages');
					$country_translated = __('c-'.$a_row['country'],'countries-languages');
					$result .= "<li>{$a_row['ip']} <span class='slimstat-second-column'>$language_translated</span> 
						<span class='slimstat-third-column'>$country_translated</span>
						<span class='slimstat-fourth-column'>{$a_row['domain']}</span>
						<span class='slimstat-fifth-column'>{$a_row['resource']}</span>
						<span class='slimstat-sixth-column'>{$a_row['customdatetime']}</span></li>\n";
				}
				break;
			case 'get_recent_bouncing_pages';
				$temp_array = $wp_slimstat_view->get_recent_bouncing_pages();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['resource']} <span class='slimstat-second-column'>{$a_row['domain']}</span> 
						<span class='slimstat-third-column'>{$a_row['customdatetime']}</span></li>\n";
				}
				break;
			case 'get_recent_browsers';
				$temp_array = $wp_slimstat_view->get_recent_browsers();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['browser']} <span class='slimstat-second-column'>{$a_row['version']}</span> 
						<span class='slimstat-third-column'>{$a_row['css_version']}</span>
						<span class='slimstat-fourth-column'>{$a_row['resource']}</span>
						<span class='slimstat-fifth-column'>{$a_row['country']}</span>
						<span class='slimstat-sixth-column'>{$a_row['customdatetime']}</span></li>\n";
				}
				break;
			case 'get_recent_downloads';
				$temp_array = $wp_slimstat_view->get_recent_downloads();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['outbound_resource']} <span class='slimstat-second-column'>{$a_row['customdatetime']}</span></li>\n";
				}
				break;
			case 'get_recent_internal_searches';
				$temp_array = $wp_slimstat_view->get_recent_internal_searches();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['searchterms']} <span class='slimstat-second-column'>{$a_row['customdatetime']}</span></li>\n";
				}
				break;	
			case 'get_recent_keywords_pages';
				$temp_array = $wp_slimstat_view->get_recent_keywords_pages();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['searchterms']} <span class='slimstat-second-column'>{$a_row['resource']}</span> 
						<span class='slimstat-third-column'>{$a_row['domain']}</span>
						<span class='slimstat-fourth-column'>{$a_row['referer']}</span></li>\n";
				}
				break;
			case 'get_recent_outbound';
				$temp_array = $wp_slimstat_view->get_recent_outbound();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['long_outbound']} <span class='slimstat-second-column'>{$a_row['resource']}</span> 
						<span class='slimstat-third-column'>{$a_row['ip']}</span>
						<span class='slimstat-fourth-column'>{$a_row['searchterms']}</span></li>\n";
				}
				break;
			case 'get_top_browsers_by_operating_system';
				$temp_array = $wp_slimstat_view->get_top_browsers_by_operating_system();
				foreach($temp_array as $a_row){
					$platform_translated = __($a_row['platform'],'countries-languages');
					$result .= "<li>{$a_row['browser']} <span class='slimstat-second-column'>{$a_row['version']}</span> 
						<span class='slimstat-third-column'>{$platform_translated}</span>
						<span class='slimstat-fourth-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_top_exit_pages';
				$temp_array = $wp_slimstat_view->get_top_exit_pages();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['resource']} <span class='slimstat-second-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_top_operating_systems';
				$temp_array = $wp_slimstat_view->get_top_operating_systems();
				foreach($temp_array as $a_row){
					$platform_translated = __($a_row['platform'],'countries-languages');
					$result .= "<li>$platform_translated <span class='slimstat-second-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_top_screenres';
				$temp_array = $wp_slimstat_view->get_top_screenres();
				foreach($temp_array as $a_row){
					$result .= "<li>{$a_row['resolution']} <span class='slimstat-second-column'>{$a_row['count']}</span></li>\n";
				}
				break;
			case 'get_top_search_engines';
				$temp_array = $wp_slimstat_view->get_top_search_engines();
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

// These filter replace the metatag with the actual HTML code
add_filter('the_content', array( &$wp_slimstat_shortcodes,'replace_shortcodes') );
add_filter('widget_text', array( &$wp_slimstat_shortcodes,'replace_shortcodes') );
?>