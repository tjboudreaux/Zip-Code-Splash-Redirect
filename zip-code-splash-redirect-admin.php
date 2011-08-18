<?php
require_once "pagination.class.php";

if ('zip-code-splash-redirect-admin.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
    die ('Please do not access this admin file directly. Thanks!');
}
 
if (!is_admin()) {die('This plugin is only accessible from the WordPress Dashboard.');}

if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if (!defined('WP_ADMIN_URL')) define('WP_ADMIN_URL', get_option('siteurl') . '/wp-admin');
if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');

add_action('admin_menu', 'zip_code_splash_redirect_admin_actions');



/**
 * 
 *
 * @return void
 * @author Travis Boudreaux
 */    
function zip_code_splash_redirect_admin_actions()
{
    $title = "Zip Code Splash Redirect";
    add_action( 'admin_menu', 'zip_code_splash_redirect_admin' );

    if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __($title), __($title), 'manage_options', 'zip_code_splash_redirect_admin', 'zip_code_splash_redirect_admin');
}   


function zip_code_splash_redirect_admin()
{
    if ($_POST) {
        $is_valid = validate_zip_code_form_submission();
        if ($is_valid) {
            if (is_new_splashpage()) {
                //create_splash_page_and_zip_code_records($title, $url, $zip_codes = array());
            } else {
                //update_splash_page_and_zip_code_records($id, $title, $url, $zip_codes = array());
            }
        }
        require_once "views/admin/form.php";
    } elseif ($_REQUEST['action'] == "form") {
        require_once "views/admin/form.php";
    } else {
        require_once "views/admin/list.php";
    }
        
}


function zip_code_paginated_records()
{
    global $wpdb, $splash_pages_table_name, $zip_codes_table_name;
    
    $pagination_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) FROM $splash_pages_table_name"));

    if($pagination_count > 0) {
        //get current page
        $this_page = ($_GET['p'] && $_GET['p'] > 0)? (int) $_GET['p'] : 1;
        //Records per page
        $per_page = 25;
        //Total Page
        $total_page = ceil($pagination_count/$per_page);

        //initiate the pagination variable
        $pag = new pagination();
        //Set the pagination variable values
        $pag->Items($pagination_count);
        $pag->limit($per_page);
        $pag->target("admin.php?page=itgdb_iwifm_admin_show_form");
        $pag->currentPage($this_page);

        //Done with the pagination
        //Now get the entries
        //But before that a little anomaly checking
        $list_start = ($this_page - 1)*$per_page;
        if($list_start >= $pagination_count)  //Start of the list should be less than pagination count
            $list_start = ($pagination_count - $per_page);
        if($list_start < 0) //list start cannot be negative
            $list_start = 0;
        $list_end = ($this_page * $per_page) - 1;

        //Get the data from the database
        $records = $wpdb->get_results($wpdb->prepare("SELECT *, GROUP_CONCAT(z.zipcode, '') as zipcodes 
                                                    	FROM `wp_zip_code_splash_redirect_splash_pages` sp 
                                                    	LEFT JOIN `wp_zip_code_splash_redirect_splash_pages_zip_codes` z ON z.splash_page_id = sp.id 
                                                    	GROUP BY z.splash_page_id 
                                                    	ORDER BY `title` DESC 	        
                                                        LIMIT %d, %d", $list_start, $list_end));
        
        return array('pagination' => $pag,
                     'results'    => $records,
                     'pagination_count' => $pagination_count);
    }
}

function validate_zip_code_form_submission() {
    if ($_POST) {
        
    } else {
        return NULL;
    }
}

function is_new_splash_page() {
    
}

function create_splash_page_and_zip_code_records($title, $url, $zip_codes = array()) {
    
}

function update_splash_page_and_zip_code_records($id, $title, $url, $zip_codes = array()) {
    
}

function zip_code_form_submission_values() {

    if ($_REQUEST['action'] == "form" && $_GET['splash_page_id']) {
        
    } elseif ($_REQUEST['action'] == "form" && $_POST['splash_page_id']) {
        
    } elseif ($_REQUEST['action'] == "form" && $_POST['create']) {
        
    } else {
        return array('title' => '',
                     'url'   => '',
                     'zipcodes' => '70517, 50407, 40584');
    }
}