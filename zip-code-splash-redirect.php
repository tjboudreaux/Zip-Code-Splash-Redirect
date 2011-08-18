<?php
/*
Plugin Name: Zip Code Splash Redirect
Plugin URI: http://www.travisboudreaux.com
Description: This plugin is used to create a theme with a form that has a zipcode field,
and queries a database to find a url to redirect to based on the zipcode.
Version: 1.0
Author: Travis Boudreaux <travis@travisboudreaux.com>
Author URI: http://www.travisboudreaux.com

*/

$zip_code_splash_redirect_version = "1.0";

$splash_pages_table_name = $wpdb->prefix . "zip_code_splash_redirect_splash_pages"; 
$zip_codes_table_name = $wpdb->prefix . "zip_code_splash_redirect_splash_pages_zip_codes"; 

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'zip_code_splash_redirect_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'zip_code_splash_redirect_uninstall' );

if ( is_admin() )
	require_once dirname( __FILE__ ) . '/zip-code-splash-redirect-admin.php';

DEFINED("ZIP_CODE_REDIRECT_URL") ||
    DEFINE("ZIP_CODE_REDIRECT_URL", WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__))."redirect.php");

//the redirect for will post to this script and have a hidden action with
//a value of redirect
if ($_POST['action'] == 'zip_code_splash_redirect')
    zip_code_splash_page_process_redirect();
    
/**
 * Runs when the plugin is installed.  Creates two tables, one to hold splash
 * pages, and another to hold zip codes associated w/ splash pages.
 *
 * @return void
 * @author Travis Boudreaux
 */
function zip_code_splash_redirect_install() {
    global $wpdb, $zip_code_splash_redirect_version;
    global $zip_codes_table_name, $splash_pages_table_name;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $table_name = $splash_pages_table_name; 
    $sql = "CREATE TABLE " . $table_name . " (
    	  id mediumint(9) NOT NULL AUTO_INCREMENT,
    	  title VARCHAR(255) DEFAULT '',
    	  url VARCHAR(255) DEFAULT '' NOT NULL,
    	  zipcode VARCHAR(255) DEFAULT '' NOT NULL,
    	  UNIQUE KEY id (id)
    	);";
    dbDelta($sql);

    $table_name = $zip_codes_table_name;
    $sql = file_get_contents("sql/zip_codes.sql");
    // $sql = "CREATE TABLE " . $table_name . " (
    //    splash_page_id mediumint(9) NOT NULL ,
    //    zip_code VARCHAR(5) DEFAULT '' NOT NULL,
    //    PRIMARY KEY (`splash_page_id`,`zip`)
    //  );";
    dbDelta($sql);
    
    add_option("zip_code_splash_redirect_version", $zip_code_splash_redirect_version);
}

/**
 * Runs when the plugin is uninstalled.
 *
 * @return void
 * @author Travis Boudreaux
 */
function zip_code_splash_redirect_uninstall() {}
 
function zip_code_splash_page_display_form()
{
    require_once 'views/form.php';
}
    
function zip_code_splash_page_process_redirect() {
    echo "POSTED HERE"; die;
}