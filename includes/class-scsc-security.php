<?php
/**
 * Security and Nonce Utilities for Schema Scalpel
 *
 * Centralized class providing all nonce generation, verification,
 * and authorization helper methods.
 *
 * @package    SchemaScalpel
 * @since      2.0.0
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SCSC_Security
 *
 * Handles nonce creation, verification, URL/form helpers, and
 * combined security checks (nonce + capability).
 *
 * @since 2.0.0
 */
final class SCSC_Security {

	/**
	 * Default capability required for admin actions.
	 *
	 * @var string
	 */
	private const DEFAULT_CAPABILITY = 'manage_options';

	/**
	 * Default nonce field name.
	 *
	 * @var string
	 */
	private const DEFAULT_NONCE_FIELD = 'nonce';

	/**
	 * Generate a plugin-specific nonce.
	 *
	 * @since 2.0.0
	 *
	 * @param string|int $action Custom action name (e.g., 'update_schema_5').
	 * @return string            The nonce token.
	 */
	public static function create_nonce( $action ) {
		$action = (string) $action;
		return wp_create_nonce( SCHEMA_SCALPEL_SLUG . $action );
	}

	/**
	 * Output a hidden nonce field (for forms).
	 * Echoes the field directly.
	 *
	 * @since 2.0.0
	 *
	 * @param string $action   Action name (without prefix).
	 * @param string $name     Field name (default '_wpnonce').
	 * @param bool   $referer  Whether to include referer field (default true).
	 * @return void
	 */
	public static function nonce_field( $action, $name = '_wpnonce', $referer = true ) {
		wp_nonce_field( SCHEMA_SCALPEL_SLUG . $action, $name, $referer, true );
	}

	/**
	 * Create a full nonced URL.
	 *
	 * @since 2.0.0
	 *
	 * @param string $url     Base URL.
	 * @param string $action  Action name (without prefix).
	 * @return string         Nonced URL.
	 */
	public static function nonce_url( $url, $action ) {
		return wp_nonce_url( $url, SCHEMA_SCALPEL_SLUG . $action, '_wpnonce' );
	}

	/**
	 * Verify a plugin-specific nonce.
	 *
	 * Returns validity status; optionally terminates on failure.
	 *
	 * @since 2.0.0
	 *
	 * @param string $action      Action name (without prefix).
	 * @param string $query_arg   Nonce field name (default '_wpnonce').
	 * @param bool   $die_on_fail Whether to die() on failure (default true).
	 * @return int|false          1 or 2 if valid, false if invalid.
	 */
	public static function verify_nonce( $action, $query_arg = self::DEFAULT_NONCE_FIELD, $die_on_fail = true ) {
		if ( empty( $_REQUEST[ $query_arg ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $die_on_fail ) {
				wp_nonce_ays( SCHEMA_SCALPEL_SLUG . $action );
				exit;
			}
			return false;
		}

		$nonce       = (string) wp_unslash( $_REQUEST[ $query_arg ] );
		$full_action = SCHEMA_SCALPEL_SLUG . $action;

		$valid = wp_verify_nonce( $nonce, $full_action );

		if ( ! $valid && $die_on_fail ) {
			check_admin_referer( $full_action, $query_arg );
		}

		return $valid;
	}

	/**
	 * AJAX-friendly nonce verification.
	 *
	 * Terminates with JSON error on failure.
	 *
	 * @since 2.0.0
	 *
	 * @param string      $action Action name (without prefix).
	 * @param string|null $nonce  Nonce value (defaults to $_POST['nonce'] or '_ajax_nonce').
	 * @return void
	 */
	public static function check_ajax_nonce( $action, $nonce = null ) {
		if ( null === $nonce ) {
			$nonce = $_POST['_ajax_nonce'] ?? $_POST['nonce'] ?? '';
		}

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_ajax_referer( SCHEMA_SCALPEL_SLUG . $action, $nonce );
	}

	/**
	 * Verify nonce and capability; terminate with error on failure.
	 *
	 * @since 2.0.0
	 *
	 * @param string      $action     Action name (without prefix).
	 * @param string|null $capability Capability required (default: 'manage_options').
	 * @param string|null $nonce_field Nonce field name (default: 'nonce').
	 * @return void
	 */
	public static function verify_or_die( $action, $capability = null, $nonce_field = null ) {
		$capability  = $capability ?? self::DEFAULT_CAPABILITY;
		$nonce_field = $nonce_field ?? self::DEFAULT_NONCE_FIELD;

		$valid = self::verify_nonce( $action, $nonce_field, false );

		if ( ! $valid ) {
			wp_die(
				esc_html__( 'Invalid security token. Please try again.', 'schema-scalpel' ),
				esc_html__( 'Security Error', 'schema-scalpel' ),
				array( 'response' => 403 )
			);
		}

		if ( ! current_user_can( $capability ) ) {
			wp_die(
				esc_html__( 'Insufficient permissions.', 'schema-scalpel' ),
				esc_html__( 'Access Denied', 'schema-scalpel' ),
				array( 'response' => 403 )
			);
		}
	}

	/**
	 * Verify nonce and capability, return WP_Error on failure.
	 *
	 * @since 2.0.0
	 *
	 * @param string      $action     Action name (without prefix).
	 * @param string|null $capability Capability required.
	 * @param string|null $nonce_field Nonce field name.
	 * @return true|\WP_Error
	 */
	public static function check( $action, $capability = null, $nonce_field = null ) {
		$capability  = $capability ?? self::DEFAULT_CAPABILITY;
		$nonce_field = $nonce_field ?? self::DEFAULT_NONCE_FIELD;

		$valid = self::verify_nonce( $action, $nonce_field, false );

		if ( ! $valid ) {
			return new \WP_Error( 'invalid_nonce', __( 'Invalid security token.', 'schema-scalpel' ) );
		}

		if ( ! current_user_can( $capability ) ) {
			return new \WP_Error( 'no_permission', __( 'Insufficient permissions.', 'schema-scalpel' ) );
		}

		return true;
	}
}
