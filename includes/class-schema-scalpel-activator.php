<?php

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


/**
 * Class called upon activation.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */
class Schema_Scalpel_Activator {

	/**
	 * Create database tables.
	 */
	public static function db_tables_initializer() {
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

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $custom_schema_table ) ) != $custom_schema_table ) :
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

		/**
		 * If table exists, this prevents double entries of initial settings:
		 */
		$has_active_tab_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'active_tab';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_active_tab_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'active_tab',
					'setting_value' => 'homepage',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => 'homepage' ), array( 'setting_key' => 'active_tab' ) );
		endif;

		$has_active_page_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'active_page';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_active_page_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'active_page',
					'setting_value' => '-1',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => '-1' ), array( 'setting_key' => 'active_page' ) );
		endif;

		$has_active_post_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'active_post';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_active_post_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'active_post',
					'setting_value' => '-1',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => '-1' ), array( 'setting_key' => 'active_post' ) );
		endif;

		$has_website_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'website_schema';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_website_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'website_schema',
					'setting_value' => '1',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => '1' ), array( 'setting_key' => 'website_schema' ) );
		endif;

		$has_webpage_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'webpage_schema';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_webpage_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'webpage_schema',
					'setting_value' => '1',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => '1' ), array( 'setting_key' => 'webpage_schema' ) );
		endif;

		$has_breadcrumb_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'breadcrumb_schema';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_breadcrumb_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'breadcrumb_schema',
					'setting_value' => '1',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => '1' ), array( 'setting_key' => 'breadcrumb_schema' ) );
		endif;

		$has_search_param_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'search_param';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_search_param_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'search_param',
					'setting_value' => 's',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => 's' ), array( 'setting_key' => 'search_param' ) );
		endif;

		$has_yoast_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'yoast_schema';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_yoast_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'yoast_schema',
					'setting_value' => '1',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => '1' ), array( 'setting_key' => 'yoast_schema' ) );
		endif;

		$has_aio_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'aio_schema';", $schema_settings_table ), ARRAY_A );
		if ( ! isset( $has_aio_setting[0] ) ) :
			$wpdb->insert(
				$schema_settings_table,
				array(
					'setting_key'   => 'aio_schema',
					'setting_value' => '1',
				)
			);
		else :
			$wpdb->update( $schema_settings_table, array( 'setting_value' => '1' ), array( 'setting_key' => 'aio_schema' ) );
		endif;
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
