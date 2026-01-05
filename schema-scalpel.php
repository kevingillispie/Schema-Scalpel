<?php
/**
 * Plugin Name:       Schema Scalpel
 * Plugin URI:        https://schemascalpel.com/
 * Description:       Boost your site’s SEO with Schema Scalpel, a user-friendly plugin for crafting custom schema markup on a per-page basis.
 * Version:           1.7
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Tested up to:      6.8
 * Author:            Kevin Gillispie
 * Author URI:        https://kevingillispie.com
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       schema-scalpel
 *
 * Enhance your site’s structured data with per-page schema control.
 * Copyright (C) 2021 - 2025 Kevin Gillispie
 *
 * @link https://schemascalpel.com
 * @package SchemaScalpel
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SCHEMA_SCALPEL_VERSION', '1.7' );
define( 'SCHEMA_SCALPEL_TEXT_DOMAIN', 'scsc' );
define( 'SCHEMA_SCALPEL_SLUG', 'scsc_' );
define( 'SCHEMA_SCALPEL_PLUGIN', __FILE__ );
define( 'SCHEMA_SCALPEL_DIRECTORY', untrailingslashit( dirname( SCHEMA_SCALPEL_PLUGIN ) ) );

require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-html-refactory.php';
require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-scsc-activator.php';
require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-scsc-deactivator.php';
require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-scsc-upgrade.php';

register_activation_hook( SCHEMA_SCALPEL_PLUGIN, array( __NAMESPACE__ . '\\SCSC_Activator', 'activate' ) );
register_deactivation_hook( SCHEMA_SCALPEL_PLUGIN, array( __NAMESPACE__ . '\\SCSC_Deactivator', 'deactivate' ) );

/**
 * Trigger database upgrade after plugin update or installation.
 *
 * @since 1.6
 * @param WP_Upgrader $upgrader WP_Upgrader instance.
 * @param array       $data     Upgrade data.
 * @return void
 */
function scsc_upgrader_process_complete( $upgrader, $data ) {
	$plugin_basename = plugin_basename( SCHEMA_SCALPEL_PLUGIN );

	if ( 'update' === $data['action'] && 'plugin' === $data['type'] && ! empty( $data['plugins'] ) ) {
		if ( in_array( $plugin_basename, $data['plugins'], true ) ) {
			SCSC_Upgrade::upgrade();
		}
	} elseif ( 'install' === $data['action'] && 'plugin' === $data['type'] && ! empty( $data['plugin'] ) ) {
		if ( $plugin_basename === $data['plugin'] ) {
			SCSC_Upgrade::upgrade();
		}
	}
}
add_action( 'upgrader_process_complete', __NAMESPACE__ . '\\scsc_upgrader_process_complete', 10, 2 );

require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel.php';

/**
 * Run the plugin.
 *
 * @since 1.0
 * @return void
 */
function run_schema_scalpel() {
	$plugin = new Schema_Scalpel();
	$plugin->run();

	// Check plugin version against stored database version.
	global $wpdb;
	$settings_table = $wpdb->prefix . 'scsc_settings';
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $settings_table ) ) === $settings_table ) {
		$stored_db_version = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT setting_value FROM %i WHERE setting_key = %s',
				$settings_table,
				SCSC_Upgrade::DB_VERSION_KEY
			)
		);
		if ( null === $stored_db_version ) {
			$stored_db_version = '0.0.0';
		}
		if ( version_compare( $stored_db_version, SCSC_Upgrade::DB_VERSION, '<' ) ) {
			SCSC_Upgrade::upgrade();
		}
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\run_schema_scalpel' );

global $wpdb;
$settings_table = $wpdb->prefix . 'scsc_settings';
$table_checked  = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $settings_table ) );
if ( isset( $table_checked ) && $table_checked === $settings_table ) {
	// Turn off Yoast schema generator.
	$yoast_setting = $wpdb->get_results( $wpdb->prepare( 'SELECT setting_value FROM %i WHERE setting_key = %s', $settings_table, 'yoast_schema' ), ARRAY_A );
	if ( isset( $yoast_setting[0] ) && '1' === $yoast_setting[0]['setting_value'] ) {
		add_filter( 'wpseo_json_ld_output', '__return_false' );
	}

	// Turn off AIO SEO schema.
	$aio_setting = $wpdb->get_results( $wpdb->prepare( 'SELECT setting_value FROM %i WHERE setting_key = %s', $settings_table, 'aio_schema' ), ARRAY_A );
	if ( isset( $aio_setting[0] ) && '1' === $aio_setting[0]['setting_value'] ) {
		add_filter(
			'aioseo_schema_output',
			function ( $graphs ) {
				foreach ( $graphs as $index => $graph ) {
					unset( $graphs[ $index ] );
				}
				return $graphs;
			}
		);
	}
}
