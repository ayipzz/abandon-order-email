<?php
/**
 * Plugin Name: WooCommerce Abandon Order with Background Worker
 * Plugin URI: http://www.tonjoostudio.com/
 * Description: Send abandoned order follow up email
 * Version: 1
 * Author: tonjoo
 * Author URI: http://todiadiyatmo.com/
 * License: GPLv2
 */
define("PLUGIN_AOE", 'plugin-abandon-order-email');
define("PLUGIN_AOE_PATH", plugin_dir_path(__FILE__));
require_once( PLUGIN_AOE_PATH . 'inc/send-mail.php');

function aoe_cron_schedules($schedules){
     if(!isset($schedules["aoe_search_email"])){
         $schedules["aoe_search_email"] = array(
             'interval' => 5*60,
             'display' => __('Once every 5 minutes'));
     }
     return $schedules;
 }
 add_filter('cron_schedules','aoe_cron_schedules');

if ( !wp_next_scheduled('aoe_add_order_search_job') ) {
    wp_schedule_event( time(), 'aoe_search_email', 'aoe_add_order_search_job' );
}

add_action ( 'aoe_add_order_search_job', 'aoe_add_order_search_job_function' );

function aoe_add_order_search_job_function() {
    // Wordpress Background Worker Installed
    if( function_exists('wp_background_add_job') ) {
        $job = new stdClass();
        $job->function = 'aoe_search_and_send_abandon_order_email';
        wp_background_add_job($job);
    }
    else {
        aoe_search_and_send_abandon_order_email();
    }
}

function aoe_search_and_send_abandon_order_email() {
    if ( ! defined( 'AOE_ABANDON_ORDER_THRESHOLD' ) ) {
        // 30 minutes
    	define( 'AOE_ABANDON_ORDER_THRESHOLD', 300 );
    }

    if ( ! defined( 'AOE_ABANDON_ORDER_EMAIL_TIME' ) ) {
        // 30 minutes
    	define( 'AOE_ABANDON_ORDER_EMAIL_TIME', 1800 );
    }


    // Set post meta to abandon
    global $wpdb;

    $query = 'SELECT ID FROM '.$wpdb->prefix.'posts as posts
            WHERE posts.post_type = "shop_order"
            AND posts.post_status IN ("wc-pending")';

    if( defined('AOE_ABANDON_ORDER_EMAIL_START_DATE') ) {
        $query .= ' AND posts.post_date >= "'.AOE_ABANDON_ORDER_EMAIL_START_DATE.'"';
    }

    $query .= ' AND posts.post_date <= "'.aoe_get_end_date_time(AOE_ABANDON_ORDER_THRESHOLD).'"';

    $query .= ' AND NOT EXISTS (
              SELECT post_id FROM `'.$wpdb->prefix.'postmeta`
               WHERE `'.$wpdb->prefix.'postmeta`.`meta_key` = "aoe_abandoned"
                AND `'.$wpdb->prefix.'postmeta`.`post_id` = posts.ID
            )';


    $orders = $wpdb->get_results($query);

    foreach( $orders as $order_id ) {
        update_post_meta($order_id->ID, 'aoe_abandoned', true);
    }

    // Send Email
    $queryEmail = 'SELECT ID FROM '.$wpdb->prefix.'posts as posts
            WHERE posts.post_type = "shop_order"
            AND posts.post_status IN ("wc-pending")';

    if( defined('AOE_ABANDON_ORDER_EMAIL_START_DATE') ) {
        $queryEmail .= ' AND posts.post_date >= "'.AOE_ABANDON_ORDER_EMAIL_START_DATE.'"';
    }


    $queryEmail .= ' AND posts.post_date <= "'.aoe_get_end_date_time(AOE_ABANDON_ORDER_EMAIL_TIME).'"';

    $queryEmail .= ' AND EXISTS (
              SELECT post_id FROM `'.$wpdb->prefix.'postmeta`
               WHERE `'.$wpdb->prefix.'postmeta`.`meta_key` = "aoe_abandoned"
                AND `'.$wpdb->prefix.'postmeta`.`post_id` = posts.ID
            )';

    $queryEmail .= ' AND NOT EXISTS (
              SELECT post_id FROM `'.$wpdb->prefix.'postmeta`
               WHERE `'.$wpdb->prefix.'postmeta`.`meta_key` = "aoe_abandoned_emailed"
                AND `'.$wpdb->prefix.'postmeta`.`post_id` = posts.ID
            )';

    $abandondedOrdersforEmail = $wpdb->get_results($queryEmail);

    foreach ( $abandondedOrdersforEmail as $orderID ) {

        if( class_exists( 'WP_CLI' ) ) {
            WP_CLI::log('Email sent to '.$orderID->ID);
        }

        aoe_do_send_email( new WC_Order($orderID->ID) );
        update_post_meta($order_id->ID, 'aoe_abandoned_emailed', true);
    }
}

function aoe_get_end_date_time($diff) {

    // $interval = new DateInterval('P'.$diff.'S');

    $date = new DateTime(current_time('Y-m-d H:i:s'));
    $date->modify('-'.$diff.' second');

    $endDate = $date->format('Y-m-d H:i:s');

    return $endDate;
}
