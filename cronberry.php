<?php

/**
 * Plugin Name: Cronberry
 * Plugin URI: http://my-plugin.com
 * Author: Cronberry
 * Author URI: http://robi.me
 * Version: 1.0.0
 * Text Domain: my-plugin
 * Description: Cronberry plugin to for data submission.
 */
if (!defined('ABSPATH')) : exit();
endif;

/**
 * Define plugin constants
 */
define('CRONBERRY_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('CRONBERRY_URL', trailingslashit(plugins_url('/', __FILE__)));




function create_cronberry_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'cronberry_integration';
    $sql = "CREATE TABLE `$table_name` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `session_id` text DEFAULT NULL,
        `cart_id` text DEFAULT NULL,
        `name` varchar(255) DEFAULT NULL,
        `email` varchar(255) DEFAULT NULL,
        `mobile` varchar(255) DEFAULT NULL,
        `cart_add_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `productnames` varchar(255) DEFAULT NULL,
        `productquantity` int(5) DEFAULT NULL,
        `orderid` varchar(255) DEFAULT NULL,
        `order_date` varchar(255) DEFAULT NULL,
        `orderstatus` varchar(255) DEFAULT NULL,
        `city` varchar(255) DEFAULT NULL,
        `postcode` varchar(255) DEFAULT NULL,
        `amount` varchar(255) DEFAULT NULL,
        `web_fcm_token` text DEFAULT NULL,
        `status` int(1) DEFAULT 0,
        `message` varchar(255) DEFAULT NULL,
        `otherData` varchar(3000) DEFAULT NULL,
        `add_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
        `update_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function drop_cronberry_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cronberry_integration';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}

function cronberry_add_cron()
{

    if (!wp_next_scheduled('cronberry_push_data_hook')) {
        wp_schedule_event(time(), '10_minutes', 'cronberry_push_data_hook');
    }
}
function cronberry_remove_cron()
{
    wp_clear_scheduled_hook('cronberry_push_data_hook');
}

function cronberry_activate()
{
    create_cronberry_table();
    cronberry_add_cron();
}

function cronberry_deactivate()
{
    drop_cronberry_table();
    cronberry_remove_cron();
}

if (is_admin()) {
    register_activation_hook(__FILE__, 'cronberry_activate');
    register_deactivation_hook(__FILE__, 'cronberry_deactivate');
}


/**
 * Include integration
 */
require_once CRONBERRY_PATH . '/inc/integration.php';

/**
 * Include Settings Page
 */

require_once CRONBERRY_PATH . '/inc/settings/settings.php';
require_once CRONBERRY_PATH . '/inc/settings/firebase.php';



/**
 * Include Hooks Page
 */
require_once CRONBERRY_PATH . '/inc/hooks/hooks.php';

/**
 * Include ajx Page
 */
require_once CRONBERRY_PATH . '/inc/ajax.php';

require_once CRONBERRY_PATH . '/uninstall.php';
