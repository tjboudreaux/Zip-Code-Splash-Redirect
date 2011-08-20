<?php
/*
Plugin Name: Zip Code Splash Redirect
Plugin URI: http://www.travisboudreaux.com
Description: This plugin is used to create a theme with a form that has a zipcode field,and queries a database to find a url to redirect to based on the zipcode.
Version: 1.0.1
Author: Travis Boudreaux <travis@travisboudreaux.com>
Author URI: http://www.travisboudreaux.com

*/

//plugin version number
$zip_code_splash_redirect_version = "1.0.1";

//database table names
global $wpdb,$splash_pages_table_name,$zip_codes_table_name;
$splash_pages_table_name = $wpdb->prefix . "zip_code_splash_redirect_splash_pages"; 
$zip_codes_table_name = $wpdb->prefix . "zip_code_splash_redirect_zip_codes"; 

$list_link = site_url()."/wp-admin/options-general.php?page=zip_code_splash_redirect_admin&action=list";
$form_link = site_url()."/wp-admin/options-general.php?page=zip_code_splash_redirect_admin&action=form"; 
$delete_link = site_url()."/wp-admin/options-general.php?page=zip_code_splash_redirect_admin&action=delete"; 

//clear flash messages array each time.
$_SESSION['zipcode_flash_messages_front'] = array();

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'zip_code_splash_redirect_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'zip_code_splash_redirect_uninstall' );

/* Load the Admin Logic Controller if in admin panel */
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
    $sql_file_path = dirname(__FILE__).DIRECTORY_SEPARATOR."sql".DIRECTORY_SEPARATOR."splash_pages.sql";
    $sql = file_get_contents($sql_file_path);
    $sql = str_replace("{table_name}", $table_name, $sql);
    dbDelta($sql);

    $table_name = $zip_codes_table_name;
    $sql_file_path = dirname(__FILE__).DIRECTORY_SEPARATOR."sql".DIRECTORY_SEPARATOR."zip_codes.sql";
    $sql = file_get_contents($sql_file_path);
    $sql = str_replace("{table_name}", $table_name, $sql);

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

/**
 * Template tag to display the form.
 *
 * @return void
 * @author Travis Boudreaux
 */ 
function zip_code_splash_page_display_form($use_image = false, $submit_value="Submit", $image_path="")
{
    $submit_value = empty($submit_value) ? "Submit" : $submit_value;
    
    require_once 'views/form.php';
}

/**
 * Redirect function for front end form.
 *
 * @return void
 * @author Travis Boudreaux
 */    
function zip_code_splash_page_process_redirect() {
    $zipcode = $_POST['zipcode'];

    //is vip code valid?
    $is_valid_zipcode = (bool)preg_match('/^([0-9]{5}(?:-[0-9]{4})?)*$/', $zipcode);
    
    //does it exist?
    $zipcode_exists = (bool)zipcode_exists($zipcode);
   
    if ($is_valid_zipcode && $zipcode_exists) {
        $closest = find_closest($zipcode);
        if(sizeof($closest) > 0) {
            require_once ABSPATH . "wp-includes/pluggable.php";
            wp_redirect($closest[0]->url);
            exit; die;
        }  else {
            $_SESSION['zipcode_flash_messages_front'][] = "A valid zipcode is required.";
        }

    } else {
        if (!$is_valid_zipcode)
            $_SESSION['zipcode_flash_messages_front'][] = "A valid zipcode is required.";
            
        if (!$zipcode_exists)   
            $_SESSION['zipcode_flash_messages_front'][] = "Zipcode does not exist.";
    }

}

/**
 * DB function to find the closest zipcode
 *
 * @param string $zip_code 
 * @param string $within 
 * @param string $number_to_return 
 * @return void
 * @author Travis Boudreaux
 */
function find_closest($zipcode, $number_to_return = 1)
{
    global $wpdb, $splash_pages_table_name, $zip_codes_table_name;

    $lat_long = $wpdb->get_results($wpdb->prepare("SELECT latitude, longitude 
                                                    FROM $zip_codes_table_name 
                                                    WHERE zipcode = %d LIMIT 1", 
                                                    $zipcode));
    if (sizeof($lat_long) == 1) {

        $latitude = $lat_long[0]->latitude;
        $longitude = $lat_long[0]->longitude;
        $results = $wpdb->get_results($wpdb->prepare("SELECT * ,
        (3957 * 2 *
            atan2(
            sqrt(pow((sin(0.0174*(z.latitude-$latitude)/2)),2) + cos(0.0174*$latitude) * cos(0.0174*z.latitude) * pow((sin(0.0174*(z.longitude-$longitude)/2)),2)),
            sqrt(1-(pow((sin(0.0174*(z.latitude-$latitude)/2)),2) + cos(0.0174*$latitude) * cos(0.0174*z.latitude) * pow((sin(0.0174*(z.longitude-$longitude)/2)),2)))))
        as distance
        FROM $splash_pages_table_name s
        INNER JOIN $zip_codes_table_name z ON z.zipcode = s.zipcode
        ORDER BY distance LIMIT %d", $number_to_return));
    
        return $results;
    }
}        

/**
 * Validation function to see if zipcode exists.
 *
 * @param string $zipcode 
 * @return void
 * @author Travis Boudreaux
 */        
function zipcode_exists($zipcode) {
    global $wpdb, $zip_codes_table_name;
    $results = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) 
                                                    FROM $zip_codes_table_name 
                                                    WHERE zipcode = %d LIMIT 1", 
                                                    $zipcode));
    
    return $results > 0;
}