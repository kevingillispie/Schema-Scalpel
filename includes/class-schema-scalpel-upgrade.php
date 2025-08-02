<?php
/**
 * Handles database upgrades for Schema Scalpel plugin.
 *
 * @package SchemaScalpel
 * @since 1.6.0
 * @author Schema Scalpel Team
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages database schema upgrades for the Schema Scalpel plugin.
 *
 * @since 1.6.0
 */
class Schema_Scalpel_Upgrade {

	/**
	 * Current database version.
	 *
	 * @var string
	 */
	const DB_VERSION = '1.6.0';

	/**
	 * Setting key for storing the database version in wp_scsc_settings.
	 *
	 * @var string
	 */
	const DB_VERSION_KEY = 'db_version';

	/**
	 * Checks and updates the wp_scsc_custom_schemas table column types if needed.
	 *
	 * @since 1.6.0
	 * @return void
	 */
	public static function upgrade() {
		global $wpdb;

		if ( ! isset( $wpdb ) ) {
			return;
		}

		$table_name     = $wpdb->prefix . 'scsc_custom_schemas';
		$settings_table = $wpdb->prefix . 'scsc_settings';

		// Check if settings table exists.
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $settings_table ) ) !== $settings_table ) {
			return;
		}

		// Get current database version from wp_scsc_settings.
		$current_db_version = '0.0.0';
		$version_query      = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT setting_value FROM %i WHERE setting_key = %s',
				$settings_table,
				self::DB_VERSION_KEY
			)
		);
		if ( null !== $version_query ) {
			$current_db_version = $version_query;
		}

		if ( version_compare( $current_db_version, self::DB_VERSION, '<' ) ) {
			// Check if custom schemas table exists.
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
				return;
			}

			// Get column information.
			$columns = $wpdb->get_results(
				$wpdb->prepare(
					'SHOW COLUMNS FROM %i WHERE Field IN (%s, %s)',
					$table_name,
					'id',
					'custom_schema'
				)
			);

			$id_type            = '';
			$custom_schema_type = '';
			foreach ( $columns as $column ) {
                // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- MySQL returns Field and Type, cannot rename.
				if ( 'id' === $column->Field ) {
                    // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- MySQL returns Type, cannot rename.
					$id_type = strtolower( $column->Type );
                // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- MySQL returns Field and Type, cannot rename.
				} elseif ( 'custom_schema' === $column->Field ) {
                    // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- MySQL returns Type, cannot rename.
					$custom_schema_type = strtolower( $column->Type );
				}
			}

			$needs_update  = false;
			$alter_queries = array();

			// Check and prepare update for id column.
			if ( $id_type && 'bigint unsigned' !== $id_type ) {
				$alter_queries[] = 'MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT';
				$needs_update    = true;
			}

			// Check and prepare update for custom_schema column.
			if ( $custom_schema_type && 'mediumblob' !== $custom_schema_type ) {
				$alter_queries[] = 'MODIFY COLUMN custom_schema MEDIUMBLOB NOT NULL';
				$needs_update    = true;
			}

			// Execute ALTER TABLE if needed.
			if ( $needs_update ) {
				$alter_sql = $wpdb->prepare(
					'ALTER TABLE %i ' . implode( ', ', $alter_queries ),
					$table_name
				);
				$wpdb->query( $alter_sql );

				if ( empty( $wpdb->last_error ) ) {
					// Update or insert database version in wp_scsc_settings.
					$existing_version = $wpdb->get_var(
						$wpdb->prepare(
							'SELECT setting_key FROM %i WHERE setting_key = %s',
							$settings_table,
							self::DB_VERSION_KEY
						)
					);

					if ( $existing_version ) {
						$wpdb->update(
							$settings_table,
							array(
								'setting_value' => self::DB_VERSION,
								'updated'       => \current_time( 'mysql' ),
							),
							array( 'setting_key' => self::DB_VERSION_KEY ),
							array( '%s', '%s' ),
							array( '%s' )
						);
					} else {
						$wpdb->insert(
							$settings_table,
							array(
								'setting_key'   => self::DB_VERSION_KEY,
								'setting_value' => self::DB_VERSION,
								'updated'       => \current_time( 'mysql' ),
							),
							array( '%s', '%s', '%s' )
						);
					}

					\add_action(
						'admin_notices',
						function () {
							?>
							<div class="notice notice-success is-dismissible">
								<p><?php printf( \esc_html__( 'Schema Scalpel database schema updated to version %s.', 'schema-scalpel' ), \esc_html( self::DB_VERSION ) ); ?></p>
							</div>
							<?php
						}
					);
				} else {
					\add_action(
						'admin_notices',
						function () use ( $wpdb ) {
							?>
							<div class="notice notice-error is-dismissible">
								<p><?php printf( \esc_html__( 'Schema Scalpel database schema update failed: %s', 'schema-scalpel' ), \esc_html( $wpdb->last_error ) ); ?></p>
							</div>
							<?php
						}
					);
				}
			} else {
				// Update version even if no changes were needed.
				$existing_version = $wpdb->get_var(
					$wpdb->prepare(
						'SELECT setting_key FROM %i WHERE setting_key = %s',
						$settings_table,
						self::DB_VERSION_KEY
					)
				);

				if ( $existing_version ) {
					$wpdb->update(
						$settings_table,
						array(
							'setting_value' => self::DB_VERSION,
							'updated'       => \current_time( 'mysql' ),
						),
						array( 'setting_key' => self::DB_VERSION_KEY ),
						array( '%s', '%s' ),
						array( '%s' )
					);
				} else {
					$wpdb->insert(
						$settings_table,
						array(
							'setting_key'   => self::DB_VERSION_KEY,
							'setting_value' => self::DB_VERSION,
							'updated'       => \current_time( 'mysql' ),
						),
						array( '%s', '%s', '%s' )
					);
				}
			}
		}
	}
}
