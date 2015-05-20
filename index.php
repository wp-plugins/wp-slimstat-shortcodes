<?php
/*
Plugin Name: WP SlimStat ShortCodes
Plugin URI: http://wordpress.org/plugins/wp-slimstat-shortcodes/
Description: This plugin has been discontinued. Shortcodes are now available directly in Slimstat 4
Version: 2.6
Author: Camu
Author URI: http://slimstat.getused.to.it
*/

function wp_slimstat_shortcodes_discontinued(){
	echo '<div class="updated slimstat-notice" style="padding:10px"><span>WP Slimstat Shortcodes has been discontinued. Its functionality is now available directly in Slimstat 4. Please uninstall the add-on to make this message disappear.</span></div>';
}
add_action('admin_notices', 'wp_slimstat_shortcodes_discontinued', 10);
