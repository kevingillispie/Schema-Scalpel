<?php
/**
 * Schema Scalpel – Uninstaller.
 *
 * Handles cleanup when the plugin is deleted (if the "delete data on uninstall" option is enabled).
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie <kevin@schemascalpel.com>
 * @since      1.0.0
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class responsible for removing plugin data on uninstall.
 */
final class SCSC_Uninstaller {

	/**
	 * Perform uninstallation tasks.
	 *
	 * Drops the plugin's custom database tables if the user has chosen to delete all data
	 * when uninstalling the plugin, then flushes rewrite rules.
	 *
	 * This method is intended to be called from a separate uninstall.php file via:
	 *     SCSC_Uninstaller::uninstall();
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function uninstall(): void {
		global $wpdb;

		// Retrieve the "delete on uninstall" setting.
		$setting = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key = %s",
				'delete_on_uninstall'
			),
			ARRAY_A
		);

		// Bail early if the setting is not enabled.
		if ( ! $setting || '1' !== $setting['setting_value'] ) {
			flush_rewrite_rules();
			return;
		}

		// Define table names (with prefix).
		$table_schemas  = $wpdb->prefix . 'scsc_custom_schemas';
		$table_settings = $wpdb->prefix . 'scsc_settings';

		// Drop tables if they exist.
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_schemas ) );  // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_settings ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// Flush rewrite rules to clean up any registered rules.
		flush_rewrite_rules();
	}
}
