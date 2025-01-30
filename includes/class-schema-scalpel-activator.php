<?php
/**
 * Class called upon activation.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


/**
 * Core activation class.
 *
 * @since 1.0
 */
class Schema_Scalpel_Activator {

	/**
	 * Create database tables.
	 */
	private static function db_tables_initializer() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;
		$charset_collate     = $wpdb->get_charset_collate();
		$custom_schema_table = $wpdb->prefix . 'scsc_custom_schemas';
		$custom_schema_sql   = "CREATE TABLE $custom_schema_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            schema_type varchar(10) DEFAULT '' NOT NULL,
            post_id bigint(20) NULL,
            custom_schema varchar(10000) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $custom_schema_table ) ) !== $custom_schema_table ) :
			dbDelta( $custom_schema_sql );
		else :
			/**
			 * Update schema_type column.
			 */
			$wpdb->update( $custom_schema_table, array( 'schema_type' => 'homepage' ), array( 'schema_type' => 'home' ) );
		endif;

		$schema_settings_table = $wpdb->prefix . 'scsc_settings';
		$settings_sql          = "CREATE TABLE $schema_settings_table (
            updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            setting_key varchar(100) DEFAULT '' NOT NULL,
            setting_value varchar(100) DEFAULT '' NOT NULL
        ) $charset_collate;";
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $schema_settings_table ) ) !== $custom_schema_table ) :
			dbDelta( $settings_sql );
		endif;

		$default_settings = array(
			array( 'active_tab', 'homepage' ),
			array( 'active_page', '-1' ),
			array( 'active_post', '-1' ),
			array( 'website_schema', '1' ),
			array( 'webpage_schema', '1' ),
			array( 'breadcrumb_schema', '1' ),
			array( 'search_param', 's' ),
			array( 'yoast_schema', '1' ),
			array( 'aio_schema', '1' ),
			array( 'delete_on_unistall', '0' ),
		);

		foreach ( $default_settings as $pair ) {
			/**
			 * If table exists, this prevents double entries of initial settings:
			 */
			$has_current_setting = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s WHERE setting_key = %s;', $schema_settings_table, $pair[0] ), ARRAY_A );
			if ( ! isset( $has_current_setting[0] ) ) :
				$wpdb->insert(
					$schema_settings_table,
					array(
						'setting_key'   => $pair[0],
						'setting_value' => $pair[1],
					)
				);
			else :
				$wpdb->update( $schema_settings_table, array( 'setting_value' => $pair[1] ), array( 'setting_key' => $pair[0] ) );
			endif;
		}
	}

	/**
	 * Activates the Scalpel!
	 */
	public static function activate() {
		global $wpdb;
		if ( is_multisite() ) :

			$blogids = $wpdb->get_col( 'SELECT blog_id FROM ' . $wpdb->blogs );
			foreach ( $blogids as $blog_id ) :

				switch_to_blog( $blog_id );
				self::db_tables_initializer();
				restore_current_blog();

			endforeach;

		else :
			self::db_tables_initializer();
		endif;
	}
}
