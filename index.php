<?php
/*
Plugin Name: WP SlimStat ShortCodes
Plugin URI: http://wordpress.org/plugins/wp-slimstat-shortcodes/
Description: Adds support for shortcodes to WP SlimStat
Version: 2.5.1
Author: Camu
Author URI: http://slimstat.getused.to.it
*/

class wp_slimstat_shortcodes{

	/**
	 * Attaches functions to hooks
	 */
	public static function init() {
		if (class_exists('wp_slimstat')){
			// These filters replace the metatag with the actual HTML code
			add_shortcode('slimstat', array(__CLASS__, 'slimstat_shortcode'), 15);
			
			// TODO: display chart on site
			// add_shortcode('slimstat-chart', array(__CLASS__, 'slimstat_chart'), 15);
		}
	}
	// end init

	public static function add_table_alias(&$_item, $key){
		$_item = wp_slimstat_db::get_table_alias($_item).'.'.$_item;
	}

	/**
	 * Handles the shortcode to get recent and popular data
	 */
	public static function slimstat_shortcode($_params = '', $_content = ''){
		// Include the library to retrieve the information from the database
		if (!file_exists(WP_PLUGIN_DIR."/wp-slimstat/admin/view/wp-slimstat-db.php")){
			return false;
		}

		include_once(WP_PLUGIN_DIR."/wp-slimstat/admin/view/wp-slimstat-db.php");

		// This function can be associated to both the new shortcode syntax with square brackets, or the old one using HTML comments
		if (!is_array($_params)){
			return false;
		}
		
		
		// Look for required fields
		if (empty($_params['f']) || empty($_params['w'])){
			return '<!-- slimstat shortcode error: missing parameter -->';
		}

		$custom_where = '';
		if (!empty($_params['lf'])){
			if (strpos($_params['lf'], 'WHERE:') !== false){
				$custom_where = html_entity_decode(substr($_params['lf'], 6), ENT_QUOTES, 'UTF-8');
				$custom_where = str_replace('NOW()', date_i18n('U'), $custom_where);
				wp_slimstat_db::init();
			}
			else{
				wp_slimstat_db::init(html_entity_decode($_params['lf'], ENT_QUOTES, 'UTF-8'));
			}
		}
		else{
			wp_slimstat_db::init();
		}
		
		$separator = !empty($_params['s'])?$_params['s']:', ';

		switch($_params['f']){
			// Custom SQL: use the lf param to retrieve the data; no syntax check is done!
			case 'custom':
				if (!empty($custom_where)){
					return wp_slimstat::$wpdb->query($custom_where);
				}
				break;

			case 'count':
			case 'count-all':
				return wp_slimstat_db::count_records($custom_where, $_params['w'], true, ($_params['f'] == 'count'));
				break;

			case 'recent':
			case 'popular':
				$function = 'get_'.$_params['f'];

				// What columns to include?
				$columns_to_list = array($_params['w']);
				if (!empty($_params['lc'])){
					if ($_params['lc'] == '*'){
						$columns_to_list = array_keys($results[0]);
					}
					else{
						$columns_to_list = wp_slimstat::string_to_array($_params['lc']);
					}
				}
				
				// Some columns are 'special' and need be removed from the list
				$columns_to_join = array_diff($columns_to_list, array('count', 'hostname', 'post_link'));

				// The special value 'post_list' requires the permalink to be generated
				if (in_array('post_link', $columns_to_list)){
					$columns_to_join[] = 'resource';
				}

				// Add table aliases to columns
				array_walk($columns_to_join, array(__CLASS__, 'add_table_alias'));

				// Retrieve the data
				$results = wp_slimstat_db::$function(wp_slimstat_db::get_table_alias($_params['w']).'.'.$_params['w'], $custom_where, ', '.implode(', ', $columns_to_join));

				// No data? No problem!
				if (empty($results)){
					return false;
				}

				// Format results
				$content = '';
				foreach($results as $a_result){
					$content .= '<li>';

					foreach($columns_to_list as $a_column){
						$content .= "<span class='col-$a_column'>";
						
						$permalinks_enabled = get_option('permalink_structure');
						$clean_resource = empty($permalinks_enabled)?$a_result['resource']:strtok($a_result['resource'], '?');
						
						switch($a_column){
							case 'post_link':
								$post_id = url_to_postid($clean_resource);
								if ($post_id > 0)
									$content .= "<a href='$clean_resource'>".get_the_title($post_id).'</a>';
								else 
									$content .= $clean_resource;
								break;

							case 'dt':
								$content .= date_i18n(wp_slimstat_db::$date_time_format, $a_result['dt']);
								break;

							case 'hostname':
								$content .= gethostbyaddr($a_result['ip']);
								break;

							case 'ip':
								$content .= long2ip($a_result['ip']);
								break;

							case 'count':
								$content .= $a_result['counthits'];
								break;

							default:
								$content .= $a_result[$a_column];
								break;
						}
						$content .= '</span>'.$separator;
					}
					$content = substr($content, 0, strrpos($content, $separator)).'</li>';
				}
				return "<ul class='slimstat-shortcode {$_params['f']}-{$_params['w']}'>$content</ul>";
				break;

			default:
				break;
		}
		
		return false;

	}
	// end slimstat_shortcode
}
// end of class declaration

// Bootstrap
if (function_exists('add_action')){
	add_action('plugins_loaded', array('wp_slimstat_shortcodes', 'init'), 15);
}