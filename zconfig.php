<?php
/**
 * @package zconfig
 * @version 1.7
 */
/*
Plugin Name: zconfig
Plugin URI: http://wordpress.org/plugins/zconfig/
Description: Advanced configuration
Author: Zhenyangze
Version: 1.0
Author URI: https://github.com/zhenyangze
**/

define("ZCONFIG_PATH", __DIR__);
require_once(ZCONFIG_PATH . '/class.zconfig.php');
require_once(ZCONFIG_PATH . '/class.zconfig_mysql.php');

add_action('init', ['Zconfig', 'init']);
add_action('init', ['Zconfig', 'registerStatic']);

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once(ZCONFIG_PATH . '/class.zconfig_admin.php');
	add_action( 'init', array( 'Zconfig_Admin', 'init' ) );
} else {
    // 提供api接口
    require_once(ZCONFIG_PATH . '/class.zconfig_api.php');
	add_action( 'init', array( 'Zconfig_Api', 'init' ) );
}

//register_activation_hook(__FILE__ , 'zconfig_install');
if (!function_exists('zconfig_install')) {
    function zconfig_install() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zconfig';
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(200) DEFAULT NULL,
            `code` varchar(200) DEFAULT NULL,
            `comment` varchar(250) DEFAULT NULL,
            `multi` tinyint(4) DEFAULT NULL,
            `template` text,
            `data` mediumtext,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_code` (`code`)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);;
    }
}
