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
//register javascript
wp_register_script( 'zip_code_splash_redirect_script', plugins_url('/js/plugin.js', __FILE__) );


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
        global $form_link, $list_link, $delete_link;
        if ($_POST) {

            $is_valid = validate_zip_code_form_submission();
            if (!$is_valid)
                require_once "views/admin/form.php";
        } elseif ($_REQUEST['action'] == "form") {
            require_once "views/admin/form.php";
        } elseif ($_REQUEST['action'] == "delete") {    
            $splash_page_id = $_REQUEST['splash_page_id'];
            if (delete_splash_page($splash_page_id)) {
                $_SESSION['flash_messages'][] = "Splash page deleted successfully.";
            } else {
                $_SESSION['flash_messages'][] = "Unable to delete splash page.";
            }
            
            require_once "views/admin/list.php";
        } else {
            require_once "views/admin/list.php";
        }
        
}

/**
 * Get list of paginated splash pages
 *
 * @return void
 * @author Travis Boudreaux
 */
function splash_page_paginated_records()
{
    global $wpdb, $list_link, $splash_pages_table_name, $zip_codes_table_name;
    
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
        $pag->target($list_link);
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
        $records = $wpdb->get_results($wpdb->prepare("SELECT *
                                                    	FROM `wp_zip_code_splash_redirect_splash_pages` sp 
                                                    	ORDER BY `title` DESC 	        
                                                        LIMIT %d, %d", $list_start, $list_end));
        
        return array('pagination' => $pag,
                     'results'    => $records,
                     'pagination_count' => $pagination_count);
    }
    
    return array('pagination' => NULL,
                 'results'    => array(),
                 'pagination_count' => 0);
    
}

/**
 * Validate the splash page entry.
 *
 * @return void
 * @author Travis Boudreaux
 */
function validate_zip_code_form_submission() {
    global $wpdb, $splash_pages_table_name;
    if ($_POST) {
        $title = $_POST['title'];
        $url = $_POST['url'];
        $zipcode = $_POST['zipcode'];
        
        $has_title   = strlen($title) > 0;
        $has_url     = strlen($url) > 0;
        $has_zipcode = strlen($zipcode) == 5;
        $is_valid_url = (bool)preg_match("/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $url);
        $is_valid_zipcode = (bool)preg_match('/^([0-9]{5}(?:-[0-9]{4})?)*$/', $zipcode);

        $number_of_zipcodes = $wpdb->get_var("SELECT COUNT(*) FROM $splash_pages_table_name WHERE zipcode = $zipcode");
    
        $is_unique_zipcode = $number_of_zipcodes == 0;
        
        if (!$has_title)
            $_SESSION['flash_messages'][] = "Title is required.";

        if (!$has_url)
            $_SESSION['flash_messages'][] = "URL is required.";

        if (!$has_zipcode)
            $_SESSION['flash_messages'][] = "Zipcode is required.";

        if (!$is_valid_url)
            $_SESSION['flash_messages'][] = "URL is not valid. Please correct and resubmit.";

        if (!$is_valid_zipcode)
            $_SESSION['flash_messages'][] = "A valid zip code is required. Please correct and resubmit.";

        if (!$is_unique_zipcode)
            $_SESSION['flash_messages'][] = "A unique zip code is required. Please correct and resubmit.";

        
        return ($has_title && $has_url && $has_zipcode && $is_valid_url && $is_valid_zipcode && $is_unique_zipcode);
            
    } else {
        return false;
    }
}

/**
 * Check to see if it's a new page.
 *
 * @return void
 * @author Travis Boudreaux
 */
function is_new_splash_page() {
    return !isset($_REQUEST['splash_page_id']);
}

/**
 * Create a new record
 *
 * @return void
 * @author Travis Boudreaux
 */
function create_splash_page_and_zip_code_records() {
    global $wpdb, $splash_pages_table_name;
    $values = zip_code_form_submission_values();
    
    if($wpdb->insert( $splash_pages_table_name, $values)) {
        $_SESSION['flash_messages'][] = "Splash Page Created.";
        return true;
    } else {
        $_SESSION['flash_messages'][] = "Splash Page Creation failed. Please update the record and resubmit.";
        return false;
    }
    
    
}

/**
 * Update the db record.
 *
 * @return void
 * @author Travis Boudreaux
 */
function update_splash_page_and_zip_code_records() {
    global $wpdb, $splash_pages_table_name;
    $values = zip_code_form_submission_values();

    $id = $values['id'];     unset($values['id']);
    if($wpdb->update( $splash_pages_table_name, $values, array('id'=>$id))) {
        $_SESSION['flash_messages'][] = "Splash Page Updated.";
        return true;
    } else {
        $_SESSION['flash_messages'][] = "Splash Page Update failed. Please update the record and resubmit.";
        return false;
    }
    
}

/**
 * Delete a splash page
 *
 * @param string $splash_page_id 
 * @return void
 * @author Travis Boudreaux
 */
function delete_splash_page($splash_page_id) {
    global $wpdb, $splash_pages_table_name;
    
    return $wpdb->query($wpdb->prepare("DELETE FROM $splash_pages_table_name 
                                            WHERE id = %d", $splash_page_id));

}

/**
 * Get the values for the form depending on whether it's a new entry, an 
 * entry to update, or an entry that was updated.
 *
 * @return void
 * @author Travis Boudreaux
 */
function zip_code_form_submission_values() {
    global $wpdb, $splash_pages_table_name;

    if ($_REQUEST['action'] == "form" && $_GET['splash_page_id'] && !$_POST) {
        //get a record
        $id = $_GET['splash_page_id'];
        return $wpdb->get_row("SELECT * FROM $splash_pages_table_name WHERE id = $id", ARRAY_A);
        
    } elseif ($_REQUEST['action'] == "form" && $_POST['splash_page_id']) {
        //update a record
        return  array('id'    => $_POST['splash_page_id'],
                     'title' => $_POST['title'],
                     'url'   => $_POST['url'],
                     'zipcode' => $_POST['zipcode']);
        
    } elseif ($_REQUEST['action'] == "form" && $_POST) {
        //create a record
        return array('title' => $_POST['title'],
                     'url'   => $_POST['url'],
                     'zipcode' => $_POST['zipcode']);
        
    } else {
        //blank record
        return array('title' => '',
                     'url'   => 'http://',
                     'zipcode' => '');
    }
}


/**
 * This is where we check to see if a form is posted.  Putting it in the admin
 * header hook, caused issues w/ header redirects failing b/c of prior output.
 * @author Travis Boudreaux
 */
if ($_POST) {

    $is_valid = validate_zip_code_form_submission();
    if ($is_valid) {
        require_once ABSPATH . "wp-includes/pluggable.php";
        if (is_new_splash_page()) {
            $created = create_splash_page_and_zip_code_records();
            if($created) {
                wp_redirect($list_link, $status );
                exit;
            }
            
        } else {
            $updated = update_splash_page_and_zip_code_records();
            if($updated) {
                wp_redirect($list_link, $status );
                exit;
            }
            
        }
        require_once 'views/admin/form.php';
    } else {
        
    }    
}

$_SESSION['flash_messages'] = array();