<?php
/**
 * Handles uninstallation process for Schema Scalpel plugin.
 *
 * @link       https://schemascalpel.com
 * @package    Schema_Scalpel
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! defined( 'SCHEMA_SCALPEL_PREFIX' ) ) {
	define( 'SCHEMA_SCALPEL_PREFIX', 'scsc_' );
}

global $wpdb;

$settings_table = $wpdb->prefix . SCHEMA_SCALPEL_PREFIX . 'settings';

// Check if settings table exists.
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $settings_table ) ) !== $settings_table ) {
	return;
}

// Get delete_on_uninstall setting.
$is_delete = $wpdb->get_var(
	$wpdb->prepare(
		'SELECT setting_value FROM %i WHERE setting_key = %s',
		$settings_table,
		'delete_on_uninstall'
	)
);

if ( '1' === $is_delete ) {
	// Drop custom schemas table.
	$schemas_table = $wpdb->prefix . 'scsc_custom_schemas';
	$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $schemas_table ) );

	// Drop settings table.
	$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $settings_table ) );
}

// Flush rewrite rules.
flush_rewrite_rules();
