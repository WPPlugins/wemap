<?php
/*
Plugin Name: Wemap
Plugin URI: https://getwemap.com
Description: Plugin wemap
Version: 0.6.2
Author: Wemap
Author URI: https://getwemap.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function get_table_name(){
    global $wpdb;
    $table = $wpdb->prefix . 'id_post_pinpoint';
    return $table;
}

define( 'WEMAP_PINPOINT_TABLE', get_table_name() );
define( 'WEMAP_API_URL', 'https://api.getwemap.com' );
define( 'WEMAP_LIVEMAP_URL', 'https://livemap.getwemap.com/iframe.php?' );

function wemap_autoloader($name) {
    if (getenv('WEMAP_TEST_ENVIRONMENT') && $name == 'Connect_To_Serv') {
        require plugin_dir_path(__FILE__) . '../tests/mock/Connect_To_Serv.php';
        return;
    }

    $file = plugin_dir_path(__FILE__) . 'class/' . $name . '.php';
    if (is_file($file)){
        require $file;
    }
}

function wemap_uninstall() {
    global $wpdb;
    $wpdb->query('DROP TABLE IF EXISTS '. WEMAP_PINPOINT_TABLE .';');
}

function wemap_activation(){
    global $wpdb;

    if ($wpdb->get_var("show tables like '". WEMAP_PINPOINT_TABLE ."';") != WEMAP_PINPOINT_TABLE) {
        $sql = 'CREATE TABLE '. WEMAP_PINPOINT_TABLE .' (
            id int NOT NULL AUTO_INCREMENT,
            id_post int NOT NULL,
            id_pinpoint int NOT NULL,
            UNIQUE KEY id (id)
            );';
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

register_activation_hook( __FILE__, 'wemap_activation');
register_uninstall_hook(__FILE__, 'wemap_uninstall');

spl_autoload_register('wemap_autoloader');

$plugin_wemap = new Admin_Wemap('0.6.2', 'Wemap');
?>
