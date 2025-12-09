<?php

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Drop tables on uninstallation.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */
class Schema_Scalpel_Uninstaller {

	/**
	 * Uninstallation procedure.
	 */
	public static function uninstall() {
		global $wpdb;
		$is_delete = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'delete_on_uninstall';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A )[0]['setting_value'];

		if ( '1' === $is_delete ) {
			$schemas = $wpdb->prefix . 'scsc_custom_schemas';
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %1s;', $schemas ) );
			$settings = $wpdb->prefix . 'scsc_settings';
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %1s;', $settings ) );
		}

		flush_rewrite_rules();
	}
}
