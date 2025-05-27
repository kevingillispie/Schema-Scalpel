<?php
/**
 * Run installation process.
 *
 * @link       https://schemascalpel.com
 * @package    Schema_Scalpel
 */

namespace SchemaScalpel;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;
$table = array(
	$wpdb->prefix . 'scsc_settings',
	$wpdb->prefix . 'scsc_custom_schemas',
);

foreach ( $table as $key => $value ) {
	$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %1s', $value ) );
}

flush_rewrite_rules();
