<?php
/**
 * Plugin Name:       Schema Scalpel
 * Plugin URI:        https://schemascalpel.com/
 * Description:       A simple plugin to customize your site's schema on a per-page basis.
 * Version:           1.4.7
 * Author:            Kevin Gillispie
 * Author URI:        https://kevingillispie.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       schema-scalpel
 *
 * Schema Scalpel lets you customize your site's schema on a per-page basis.
 * Copyright (C) 2021 - 2025 Kevin Gillispie
 *
 * @link              https://schemascalpel.com
 * @package           Schema_Scalpel
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

define( 'SCHEMA_SCALPEL_VERSION', '1.4.7' );
define( 'SCHEMA_SCALPEL_TEXT_DOMAIN', 'scsc' );
define( 'SCHEMA_SCALPEL_SLUG', 'scsc_' );
define( 'SCHEMA_SCALPEL_PLUGIN', __FILE__ );
define( 'SCHEMA_SCALPEL_DIRECTORY', untrailingslashit( dirname( SCHEMA_SCALPEL_PLUGIN ) ) );

require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-html-refactory.php';
require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-activator.php';
require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-deactivator.php';
require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-uninstaller.php';


register_activation_hook( SCHEMA_SCALPEL_PLUGIN, array( __NAMESPACE__ . '\\Schema_Scalpel_Activator', 'activate' ) );
register_deactivation_hook( SCHEMA_SCALPEL_PLUGIN, array( __NAMESPACE__ . '\\Schema_Scalpel_Deactivator', 'deactivate' ) );
register_uninstall_hook( SCHEMA_SCALPEL_PLUGIN, array( __NAMESPACE__ . '\\Schema_Scalpel_Uninstaller', 'uninstall' ) );


require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel.php';

/**
 * Run the plugin!
 */
function run_schema_scalpel() {
	$plugin = new Schema_Scalpel();
	$plugin->run();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\run_schema_scalpel' );

global $wpdb;
$settings_table = $wpdb->prefix . 'scsc_settings';
$table_checked  = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $settings_table ) );
if ( isset( $table_checked ) && $table_checked === $settings_table ) :
	/**
	 * TURN OFF YOAST SCHEMA GENERATOR
	 */
	$yoast_setting = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key='yoast_schema';", $settings_table ), ARRAY_A );
	if ( isset( $yoast_setting[0] ) && '1' === $yoast_setting[0]['setting_value'] ) :
		add_filter( 'wpseo_json_ld_output', '__return_false' );
	endif;

	/**
	 * TURN OFF AIO SEO SCHEMA
	 */
	$aio_setting = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key='aio_schema';", $settings_table ), ARRAY_A );
	if ( isset( $aio_setting[0] ) && '1' === $aio_setting[0]['setting_value'] ) :
		add_filter(
			'aioseo_schema_output',
			function ( $graphs ) {
				foreach ( $graphs as $index => $graph ) :
					unset( $graphs[ $index ] );
			endforeach;
				return $graphs;
			}
		);
	endif;
endif;
