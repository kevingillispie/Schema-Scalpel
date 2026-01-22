<?php
/**
 * User settings.
 *
 * @link       https://schemascalpel.com/
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin/partials
 */

namespace SchemaScalpel;

use HTML_Refactory;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Helper functions.
 */
function starts_with( $haystack, $needle ) {
	$length = strlen( $needle );
	return substr( $haystack, 0, $length ) === $needle;
}

/**
 * Get database exclusions.
 */
global $wpdb;
$current_excluded_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'exclude';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
$database_exclusions      = array();
foreach ( $current_excluded_results as $key => $value ) {
	array_push( $database_exclusions, $current_excluded_results[ $key ]['setting_value'] );
}

/**
 * Update settings on save.
 */

$all_params;
if ( isset( $_POST['save'] ) ) {
	$all_params = array();
	foreach ( $_POST as $key => $value ) {
		$all_params[] = array(
			sanitize_key( $key ),
			is_scalar( $value ) ? sanitize_text_field( $value ) : $value,
		);
	}

	/**
	 * Website.
	 */
	foreach ( $all_params as $key => $value ) {
		if ( 'enable_website' === $all_params[ $key ][0] ) {
			global $wpdb;
			$has_ws_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'website_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
			if ( ! $has_ws_setting[0] ) {
				$wpdb->insert(
					$wpdb->prefix . 'scsc_settings',
					array(
						'setting_key'   => 'website_schema',
						'setting_value' => $all_params[ $key ][1],
					)
				);
			} else {
				$wpdb->update( $wpdb->prefix . 'scsc_settings', array( 'setting_value' => $all_params[ $key ][1] ), array( 'setting_key' => 'website_schema' ) );
			}
		}
	}

	/**
	 * Webpage.
	 */
	foreach ( $all_params as $key => $value ) {
		if ( 'enable_webpage' === $all_params[ $key ][0] ) {
			global $wpdb;
			$has_wp_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'webpage_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
			if ( ! $has_wp_setting[0] ) {
				$wpdb->insert(
					$wpdb->prefix . 'scsc_settings',
					array(
						'setting_key'   => 'webpage_schema',
						'setting_value' => $all_params[ $key ][1],
					)
				);
			} else {
				$wpdb->update( $wpdb->prefix . 'scsc_settings', array( 'setting_value' => $all_params[ $key ][1] ), array( 'setting_key' => 'webpage_schema' ) );
			}
		}
	}

	/**
	 * Breadcrumbs.
	 */
	foreach ( $all_params as $key => $value ) {
		if ( 'enable_breadcrumbs' === $all_params[ $key ][0] ) {
			global $wpdb;
			$has_bc_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'breadcrumb_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
			if ( ! $has_bc_setting[0] ) {
				$wpdb->insert(
					$wpdb->prefix . 'scsc_settings',
					array(
						'setting_key'   => 'breadcrumb_schema',
						'setting_value' => $all_params[ $key ][1],
					)
				);
			} else {
				$wpdb->update( $wpdb->prefix . 'scsc_settings', array( 'setting_value' => $all_params[ $key ][1] ), array( 'setting_key' => 'breadcrumb_schema' ) );
			}
		}
	}

	/**
	 * Yoast setting.
	 */
	foreach ( $all_params as $key => $value ) {
		if ( 'disable_yoast' === $all_params[ $key ][0] ) {
			global $wpdb;
			$has_yoast_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'yoast_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
			if ( ! $has_yoast_setting[0] ) {
				$wpdb->insert(
					$wpdb->prefix . 'scsc_settings',
					array(
						'setting_key'   => 'yoast_schema',
						'setting_value' => $all_params[ $key ][1],
					)
				);
			} else {
				$wpdb->update( $wpdb->prefix . 'scsc_settings', array( 'setting_value' => $all_params[ $key ][1] ), array( 'setting_key' => 'yoast_schema' ) );
			}
		}
	}

	/**
	 * AIOSEO setting.
	 */
	foreach ( $all_params as $key => $value ) {
		if ( 'disable_aio' === $all_params[ $key ][0] ) {
			global $wpdb;
			$has_aio_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'aio_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
			if ( ! $has_aio_setting[0] ) {
				$wpdb->insert(
					$wpdb->prefix . 'scsc_settings',
					array(
						'setting_key'   => 'aio_schema',
						'setting_value' => $all_params[ $key ][1],
					)
				);
			} else {
				$wpdb->update( $wpdb->prefix . 'scsc_settings', array( 'setting_value' => $all_params[ $key ][1] ), array( 'setting_key' => 'aio_schema' ) );
			}
		}
	}

	/**
	 * Rank Math setting.
	 */
	foreach ( $all_params as $key => $value ) {
		if ( 'disable_rankmath' === $all_params[ $key ][0] ) {
			global $wpdb;
			$has_rankmath_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'rankmath_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
			if ( ! $has_rankmath_setting[0] ) {
				$wpdb->insert(
					$wpdb->prefix . 'scsc_settings',
					array(
						'setting_key'   => 'rankmath_schema',
						'setting_value' => $all_params[ $key ][1],
					)
				);
			} else {
				$wpdb->update( $wpdb->prefix . 'scsc_settings', array( 'setting_value' => $all_params[ $key ][1] ), array( 'setting_key' => 'rankmath_schema' ) );
			}
		}
	}

	/**
	 * Get user-selected exclusions.
	 */
	$user_selected_exclusions = array();
	foreach ( $all_params as $key => $value ) {
		if ( starts_with( $all_params[ $key ][0], 'exclude_' ) ) {
			array_push( $user_selected_exclusions, substr( $all_params[ $key ][0], 8, strlen( $all_params[ $key ][0] ) ) );
		}
	}

	/**
	 * Add new exclusion.
	 */
	foreach ( $user_selected_exclusions as $key => $value ) {
		if ( ! in_array( $user_selected_exclusions[ $key ], $database_exclusions ) ) {
			$wpdb->insert(
				$wpdb->prefix . 'scsc_settings',
				array(
					'setting_key'   => 'exclude',
					'setting_value' => $user_selected_exclusions[ $key ],
				)
			);
		}
	}

	/**
	 * Delete old exclusion.
	 */
	foreach ( $database_exclusions as $key => $value ) {
		if ( ! in_array( $database_exclusions[ $key ], $user_selected_exclusions ) ) {
			$wpdb->delete(
				$wpdb->prefix . 'scsc_settings',
				array(
					'setting_key'   => 'exclude',
					'setting_value' => $database_exclusions[ $key ],
				)
			);
		}
	}

	/**
	 * Delete data on uninstall.
	 */
	foreach ( $all_params as $key => $value ) {
		if ( 'delete_on_uninstall' === $all_params[ $key ][0] ) {
			global $wpdb;
			$has_bc_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'delete_on_uninstall';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
			if ( ! $has_bc_setting[0] ) {
				$wpdb->insert(
					$wpdb->prefix . 'scsc_settings',
					array(
						'setting_key'   => 'delete_on_uninstall',
						'setting_value' => $all_params[ $key ][1],
					)
				);
			} else {
				$wpdb->update( $wpdb->prefix . 'scsc_settings', array( 'setting_value' => $all_params[ $key ][1] ), array( 'setting_key' => 'delete_on_uninstall' ) );
			}
		}
	}

	/**
	 * Set `urlTemplate` search parameter.
	 */
	foreach ( $all_params as $key => $value ) {
		if ( 'search_param' === $all_params[ $key ][0] ) {
			global $wpdb;
			$has_sp_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE setting_key = 'search_param';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
			if ( ! $has_sp_setting[0] ) {
				$wpdb->insert(
					$wpdb->prefix . 'scsc_settings',
					array(
						'setting_key'   => 'search_param',
						'setting_value' => $all_params[ $key ][1],
					)
				);
			} else {
				$wpdb->update( $wpdb->prefix . 'scsc_settings', array( 'setting_value' => $all_params[ $key ][1] ), array( 'setting_key' => 'search_param' ) );
			}
		}
	}
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc_settings">';
	exit;
}

global $wpdb;

/**
* Get pages.
*/
$all_pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE post_type='page' ORDER BY post_title ASC;", $wpdb->prefix . 'posts' ), ARRAY_A );

/**
* Get posts.
*/
$all_posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE post_type='post' ORDER BY post_title ASC;", $wpdb->prefix . 'posts' ), ARRAY_A );

/**
* Get website setting.
*/
$is_website_enabled = 1;
$ws_setting         = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='website_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
if ( '0' === $ws_setting[0]['setting_value'] ) {
	$is_website_enabled = 0;
}

$search_query_param = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='search_param';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
$search_key         = $search_query_param[0]['setting_value'];

/**
* Get webpage setting.
*/
$is_webpage_enabled = 1;
$wp_setting         = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='webpage_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
if ( '0' === $wp_setting[0]['setting_value'] ) {
	$is_webpage_enabled = 0;
}

/**
* Get breadcrumb setting.
*/
$are_breadcrumbs_enabled = 1;
$bc_setting              = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='breadcrumb_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
if ( '0' === $bc_setting[0]['setting_value'] ) {
	$are_breadcrumbs_enabled = 0;
}

/**
* Get Yoast setting.
*/
$is_yoast_disabled = 1;
$yoast_setting     = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='yoast_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
if ( '0' === $yoast_setting[0]['setting_value'] ) {
	$is_yoast_disabled = 0;
}

/**
* Get AIOSEO setting.
*/
$is_aio_disabled = 1;
$aio_setting     = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='aio_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
if ( '0' === $aio_setting[0]['setting_value'] ) {
	$is_aio_disabled = 0;
}

/**
* Get Rank Math setting.
*/
$is_rankmath_disabled = 1;
$rankmath_setting     = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='rankmath_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
if ( '0' === $rankmath_setting[0]['setting_value'] ) {
	$is_rankmath_disabled = 0;
}

/**
* Get data delete setting.
*/
$is_data_deleted = 0;
$delete_setting  = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='delete_on_uninstall';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
if ( '1' === $delete_setting[0]['setting_value'] ) {
	$is_data_deleted = 1;
}

$example_clarification = "<p><em>Your site's information will be substituted in the appropriate</em> <code style='color:black'>{&quot;key&quot;: &quot;value&quot;}</code> <em>pairs below.</em></p>";

$if_yoast = 'disabled';
foreach ( get_plugins() as $key => $value ) {
	if ( stripos( $value['TextDomain'], 'wordpress-seo' ) > -1 ) {
		$if_yoast = '';
		break;
	}
}

$if_aio = 'disabled';
foreach ( get_plugins() as $key => $value ) {
	if ( stripos( $value['TextDomain'], 'all-in-one-seo-pack' ) > -1 ) {
		$if_aio = '';
		break;
	}
}

$if_rankmath = 'disabled';
foreach ( get_plugins() as $key => $value ) {
	if ( stripos( $value['TextDomain'], 'rank-math' ) > -1 ) {
		$if_rankmath = '';
		break;
	}
}

$default_setting_label_html = ( new HTML_Refactory( 'small' ) )
	->attr( 'class', array( 'text-secondary' ) )
	->child(
		( new HTML_Refactory( 'i' ) )
			->text( '&nbsp;(default)' )
			->render()
	)
	->render();

$header = ( new HTML_Refactory( 'header' ) )
	->attr( 'class', array( 'mt-3', 'mb-2', 'd-flex', 'justify-content-center' ) )
	->child(
		( new HTML_Refactory( 'img' ) )
			->attr( 'src', plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/schema-scalpel-logo.svg' )
			->attr( 'width', '300' )
			->attr( 'height', 'auto' )
			->attr( 'style', 'z-index:99' )
			->attr( 'alt', 'Schema Scalpel Logo' )
			->render()
	)
	->render();

$scalpel_icon = ( new HTML_Refactory( 'img' ) )
	->attr( 'class', array( 'mt-n4' ) )
	->attr( 'src', esc_url( plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/scalpel_title.svg' ) )
	->render();

$h1 = ( new HTML_Refactory( 'h1' ) )
	->text( 'User Settings&nbsp;' )
	->child( $scalpel_icon )
	->render();

$notice = ( new HTML_Refactory( 'p' ) )
	->attr( 'class', array( 'alert', 'alert-primary' ) )
	->attr( 'role', 'alert' )
	->text( 'The default settings for this plugin allow it to perform at its best. However, you may encounter an edge case that requires modification of the default settings.' )
	->child(
		( new HTML_Refactory( 'br' ) )->render()
	)
	->child( 'NOTE:&nbsp;' )
	->child(
		( new HTML_Refactory( 'em' ) )
			->text( 'This plugin ' )
			->child(
				( new HTML_Refactory( 'a' ) )
					->attr( 'href', esc_url( '/wp-admin/admin.php?page=scsc_settings#disable_yoast_schema' ) )
					->text( 'automatically overrides Yoast, AIOSEO, and Rank Math schema' )
					->render()
			)
			->child( ' and injects your customized schema to better fit your SEO objectives.' )
			->render()
	)
	->render();

$page_title = ( new HTML_Refactory( 'header' ) )
	->child( $h1 )
	->child( $notice )
	->render();


$website_option = ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'd-flex', 'flex-column', 'mt-3', 'mb-0', 'ps-4', 'py-3', 'radio-border-left', 'bg-white' ) )
	->child(
		// Enable WebSite option.
		( new HTML_Refactory( 'label' ) )
			->attr( 'for', 'enable_website' )
			->child(
				( new HTML_Refactory( 'input' ) )
					->attr( 'id', 'enable_website' )
					->attr( 'type', 'radio' )
					->attr( 'name', 'enable_website' )
					->attr( 'value', '1' )
					->attr( 'checked', ( 1 === $is_website_enabled ) )
					->render()
			)
			->child(
				( new HTML_Refactory( 'strong' ) )
					->text( 'Enable ' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'color:black;border-radius:3px' )
					->text( 'WebSite' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'small' ) )
					->attr( 'class', array( 'text-secondary' ) )
					->child(
						( new HTML_Refactory( 'i' ) )
							->text( '&nbsp;(default)' )
							->render()
					)
					->render()
			)
			->render()
	)
	->child(
		// Disable WebSite option.
		( new HTML_Refactory( 'label' ) )
			->attr( 'for', 'disable_website' )
			->child(
				( new HTML_Refactory( 'input' ) )
					->attr( 'id', 'disable_website' )
					->attr( 'type', 'radio' )
					->attr( 'name', 'enable_website' )
					->attr( 'value', '0' )
					->attr( 'checked', ( 0 === $is_website_enabled ) )
					->render()
			)
			->child(
				( new HTML_Refactory( 'strong' ) )
					->text( 'Disable ' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'color:black;border-radius:3px' )
					->text( 'WebSite' )
					->render()
			)
			->render()
	)
	->render();

$search_parameter = ( new HTML_Refactory( 'label' ) )
	->attr( 'class', array( 'd-flex', 'flex-column', 'mt-3' ) )
	->attr( 'for', 'search_param' )
	->child(
		// Label text: "urlTemplate search parameter key:".
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'mb-2' ) )
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'color:black;border-radius:3px' )
					->text( 'urlTemplate' )
					->render()
			)
			->child( ' search parameter key:' )
			->render()
	)
	->child(
		// Input field + lock button.
		( new HTML_Refactory( 'div' ) )
			->child(
				( new HTML_Refactory( 'input' ) )
					->attr( 'id', 'search_param' )
					->attr( 'class', array( 'me-2' ) )
					->attr( 'name', 'search_param' )
					->attr( 'type', 'text' )
					->attr( 'placeholder', 'Current: ' . sanitize_text_field( $search_key ) )
					->attr( 'disabled', true )
					->render()
			)
			->child(
				// Lock button.
				( new HTML_Refactory( 'div' ) )
					->attr( 'id', 'search_param_lock' )
					->attr( 'class', array( 'btn', 'btn-outline-danger' ) )
					->child(
						// SVG lock icon.
						( new HTML_Refactory( 'svg' ) )
							->attr( 'id', 'lock-icon' )
							->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
							->attr( 'width', '16' )
							->attr( 'height', '16' )
							->attr( 'fill', 'currentColor' )
							->attr( 'viewBox', '0 0 16 16' )
							->child(
								( new HTML_Refactory( 'path' ) )
									->attr( 'd', 'M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z' )
									->render()
							)
							->render()
					)
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'div' ) )
			->child(
				( new HTML_Refactory( 'i' ) )
					->attr( 'style', 'font-size:.75em' )
					->child( 'Note: The default WordPress search parameter key is the letter ' )
					->child(
						( new HTML_Refactory( 'code' ) )
							->attr( 'style', 'border-radius:3px' )
							->text( 's' )
							->render()
					)
					->child( '.' )
					->render()
			)
			->render()
	)
	->render();

$webpage_option = ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'd-flex', 'flex-column', 'mt-3', 'mb-0', 'ps-4', 'py-3', 'radio-border-left', 'bg-white' ) )
	->child(
		// Enable WebPage option.
		( new HTML_Refactory( 'label' ) )
			->attr( 'for', 'enable_webpage' )
			->child(
				( new HTML_Refactory( 'input' ) )
					->attr( 'id', 'enable_webpage' )
					->attr( 'type', 'radio' )
					->attr( 'name', 'enable_webpage' )
					->attr( 'value', '1' )
					->attr( 'checked', ( 1 === $is_webpage_enabled ) )
					->render()
			)
			->child(
				( new HTML_Refactory( 'strong' ) )
					->text( 'Enable ' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'color:black;border-radius:3px' )
					->text( 'WebPage' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'small' ) )
					->attr( 'class', array( 'text-secondary' ) )
					->child(
						( new HTML_Refactory( 'i' ) )
							->text( '&nbsp;(default)' )
							->render()
					)
					->render()
			)
			->render()
	)
	->child(
		// Disable WebSite option.
		( new HTML_Refactory( 'label' ) )
			->attr( 'for', 'disable_webpage' )
			->child(
				( new HTML_Refactory( 'input' ) )
					->attr( 'id', 'disable_webpage' )
					->attr( 'type', 'radio' )
					->attr( 'name', 'enable_webpage' )
					->attr( 'value', '0' )
					->attr( 'checked', ( 0 === $is_webpage_enabled ) )
					->render()
			)
			->child(
				( new HTML_Refactory( 'strong' ) )
					->text( 'Disable ' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'color:black;border-radius:3px' )
					->text( 'WebPage' )
					->render()
			)
			->render()
	)
	->render();

$breadcrumb_option = ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'd-flex', 'flex-column', 'mt-3', 'mb-0', 'ps-4', 'py-3', 'radio-border-left', 'bg-white' ) )
	->child(
		// Enable BreadcrumbList option.
		( new HTML_Refactory( 'label' ) )
			->attr( 'for', 'enable_breadcrumbs' )
			->child(
				( new HTML_Refactory( 'input' ) )
					->attr( 'id', 'enable_breadcrumbs' )
					->attr( 'type', 'radio' )
					->attr( 'name', 'enable_breadcrumbs' )
					->attr( 'value', '1' )
					->attr( 'checked', ( 1 === $are_breadcrumbs_enabled ) )
					->render()
			)
			->child(
				( new HTML_Refactory( 'strong' ) )
					->text( 'Enable&nbsp;' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'color:black;border-radius:3px' )
					->text( 'BreadcrumbList' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'small' ) )
					->attr( 'class', array( 'text-secondary' ) )
					->child(
						( new HTML_Refactory( 'i' ) )
							->text( '&nbsp;(default)' )
							->render()
					)
					->render()
			)
			->render()
	)
	->child(
		// Disable BreadcrumbList option.
		( new HTML_Refactory( 'label' ) )
			->attr( 'for', 'disable_breadcrumbs' )
			->child(
				( new HTML_Refactory( 'input' ) )
					->attr( 'id', 'disable_breadcrumbs' )
					->attr( 'type', 'radio' )
					->attr( 'name', 'enable_breadcrumbs' )
					->attr( 'value', '0' )
					->attr( 'checked', ( 0 === $are_breadcrumbs_enabled ) )
					->render()
			)
			->child(
				( new HTML_Refactory( 'strong' ) )
					->text( 'Disable&nbsp;' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'color:black;border-radius:3px' )
					->text( 'BreadcrumbList' )
					->render()
			)
			->render()
	)
	->render();

$yoast_message = '';

if ( 'disabled' === $if_yoast ) {

	$yoast_message = ( new HTML_Refactory( 'pre' ) )
		->attr( 'class', array( 'mb-0', 'rounded', 'language-js' ) )
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;YOAST not detected.' )
				->render()
		)
			->child( ( new HTML_Refactory( 'br' ) )->render() )
			->child(
				( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&gt;&nbsp;Nothing to worry about here.' )
				->render()
			)
			->child( ( new HTML_Refactory( 'br' ) )->render() )
			->child(
				( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&gt;&gt;&nbsp;Happy schema-ing!' )
				->render()
			)
			->render();

} else {

	$yoast_message = ( new HTML_Refactory( 'pre' ) )
		->attr( 'class', array( 'mb-0', 'rounded', 'language-js' ) )
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;YOAST has been detected...' )
				->render()
		)
		->child( ( new HTML_Refactory( 'br' ) )->render() )
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( "&gt;&nbsp;It is recommended that YOAST's schema feature remain disabled." )
				->render()
		)
		->child( ( new HTML_Refactory( 'br' ) )->render() )
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;All other YOAST features will be unaffected.' )
				->render()
		)
		->render();

}

$aioseo_message = '';

if ( 'disabled' === $if_aio ) {

	$aioseo_message = ( new HTML_Refactory( 'pre' ) )
		->attr( 'class', array( 'mb-0', 'rounded', 'language-js' ) )
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;AIOSEO not detected.' )
				->render()
		)
		->child(
			( new HTML_Refactory( 'br' ) )
				->render()
		)
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&gt;&nbsp;Nothing to worry about here.' )
				->render()
		)
		->child(
			( new HTML_Refactory( 'br' ) )
				->render()
		)
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&gt;&gt;&nbsp;Happy schema-ing!' )
				->render()
		)
		->render();

} else {

	$aioseo_message = ( new HTML_Refactory( 'pre' ) )
		->attr( 'class', array( 'mb-0', 'rounded', 'language-js' ) )
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;AIOSEO has been detected...' )
				->render()
		)
		->child(
			( new HTML_Refactory( 'br' ) )
				->render()
		)
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( "&gt;&nbsp;It is recommended that AIOSEO's schema feature remain disabled." )
				->render()
		)
		->child(
			( new HTML_Refactory( 'br' ) )
				->render()
		)
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;All other AIOSEO features will be unaffected.' )
				->render()
		)
		->render();

}

$rankmath_message = '';

if ( 'disabled' === $if_rankmath ) {

	$rankmath_message = ( new HTML_Refactory( 'pre' ) )
		->attr( 'class', array( 'mb-0', 'rounded', 'language-js' ) )
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;RANK MATH not detected.' )
				->render()
		)
		->child(
			( new HTML_Refactory( 'br' ) )
				->render()
		)
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&gt;&nbsp;Nothing to worry about here.' )
				->render()
		)
		->child(
			( new HTML_Refactory( 'br' ) )
				->render()
		)
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&gt;&gt;&nbsp;Happy schema-ing!' )
				->render()
		)
		->render();

} else {

	$rankmath_message = ( new HTML_Refactory( 'pre' ) )
		->attr( 'class', array( 'mb-0', 'rounded', 'language-js' ) )
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;RANK MATH has been detected...' )
				->render()
		)
		->child(
			( new HTML_Refactory( 'br' ) )
				->render()
		)
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( "&gt;&nbsp;It is recommended that RANK MATH's schema feature remain disabled." )
				->render()
		)
		->child(
			( new HTML_Refactory( 'br' ) )
				->render()
		)
		->child(
			( new HTML_Refactory( 'code' ) )
				->attr( 'class', array( 'language-js' ) )
				->raw_text( '&gt;&nbsp;All other RANK MATH features will be unaffected.' )
				->render()
		)
		->render();

}



$th_tags   = '';
$col_names = array( 'Page ID', 'Page Title', 'Page URL', 'Exclude?' );
foreach ( $col_names as $key => $name ) {
	// $th_tags .= new HTML_Refactory(
	// 'th',
	// array( 'scope' => 'col' ),
	// $name
	// );
	$th_tags .= ( new HTML_Refactory( 'th' ) )
		->attr( 'scope', 'col' )
		->text( $name )
		->render();
}

$table_head = ( new HTML_Refactory( 'thead' ) )
	->child(
		( new HTML_Refactory( 'tr' ) )
			->child( $th_tags )
			->render()
	)
	->render();

if ( $all_pages ) {

	$url     = get_site_url();
	$tr_tags = '';

	foreach ( $all_pages as $key => $value ) {
		$is_excluded = in_array( $all_pages[ $key ]['ID'], $database_exclusions );
		$text_color  = $is_excluded ? '' : 'text-secondary';
		$checked     = $is_excluded ? true : false;

		$tr_tags .= ( new HTML_Refactory( 'tr' ) )
			->attr( 'class', $text_color ? array( $text_color ) : null )
			->child(
				( new HTML_Refactory( 'th' ) )
					->attr( 'scope', 'row' )
					->text( $all_pages[ $key ]['ID'] )
					->render()
			)
				->child(
					( new HTML_Refactory( 'td' ) )
					->text( $all_pages[ $key ]['post_title'] )
					->render()
				)
				->child(
					( new HTML_Refactory( 'td' ) )
					->child(
						( new HTML_Refactory( 'a' ) )
							->attr( 'href', sanitize_url( $url . '/' . $all_pages[ $key ]['post_name'] ) )
							->attr( 'target', '_blank' )
							->text( sanitize_url( $url . '/' . $all_pages[ $key ]['post_name'] ) )
							->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'td' ) )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'type', 'checkbox' )
							->attr( 'id', 'post_num_' . sanitize_text_field( $all_pages[ $key ]['ID'] ) )
							->attr( 'name', 'exclude_' . sanitize_text_field( $all_pages[ $key ]['ID'] ) )
							->attr( 'checked', $checked ) // boolean attribute.
							->attr( 'data-key', $key )
							->render()
					)
					->render()
				)
				->render();
	}
}

$table_body = ( new HTML_Refactory( 'tbody' ) )
	->child( $tr_tags )
	->render();

$table_container = ( new HTML_Refactory( 'div' ) )
	->attr( 'style', 'max-height:500px;overflow-y:scroll' )
	->child(
		( new HTML_Refactory( 'table' ) )
			->attr( 'id', 'excluded_schema' )
			->attr( 'class', array( 'table', 'table-dark' ) )
			->child( $table_head )
			->child( $table_body )
			->render()
	)
	->render();

$settings_form = ( new HTML_Refactory( 'form' ) )
	->attr( 'method', 'post' )
	->attr( 'action', '' )
	->attr( 'style', 'z-index:99' )
	->attr( 'class', array( 'settings-page-form', 'position-relative' ) )
	->child(
		( new HTML_Refactory( 'input' ) )
			->attr( 'type', 'hidden' )
			->attr( 'id', 'scsc_settings_nonce' )
			->attr( 'name', 'scsc_settings_nonce' )
			->attr( 'value', esc_attr( wp_create_nonce( 'scsc_save_settings' ) ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'input' ) )
			->attr( 'type', 'hidden' )
			->attr( 'name', 'page' )
			->attr( 'value', 'scsc_settings' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'input' ) )
			->attr( 'type', 'hidden' )
			->attr( 'name', 'save' )
			->attr( 'value', 'save' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'hr' ) )
			->attr( 'style', 'height:5px;opacity:1' )
			->attr( 'class', array( 'mt-4', 'border-0', 'bg-white', 'rounded', 'shadow-sm' ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'h3' ) )
			->attr( 'class', array( 'mt-4', 'mb-0' ) )
			->text( 'Enable Default&nbsp;' )
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'border-radius:3px' )
					->text( 'Website' )
					->render()
			)
			->child( '&nbsp;Schema' )
			->render()
	)
	->child( $website_option )
	->child(
		( new HTML_Refactory( 'fieldset' ) )
			->attr( 'class', array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-4', 'noselect' ) )
			->child(
				( new HTML_Refactory( 'legend' ) )
					->attr( 'class', array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ) )
					->attr( 'style', 'width:auto' )
					->text( 'WebSite Format Example:' )
					->render()
			)
			->child( wp_kses( $example_clarification, 'post' ) )
			->child(
				( new HTML_Refactory( 'pre' ) )
					->attr( 'id', 'website_example' )
					->attr( 'class', array( 'mb-0', 'rounded', 'language-json' ) )
					->attr( 'data-schema', "{&quot;@context&quot;:&quot;http://schema.org/&quot;,&quot;@id&quot;:&quot;https://example.com/#website&quot;,&quot;@type&quot;:&quot;WebSite&quot;,&quot;url&quot;:&quot;https://example.com/&quot;,&quot;name&quot;:&quot;WebSite Name&quot;,&quot;potentialAction&quot;:[{&quot;@type&quot;:&quot;SearchAction&quot;,&quot;target&quot;:{&quot;@type&quot;:&quot;EntryPoint&quot;,&quot;urlTemplate&quot;:&quot;https://example.com/?{$search_key}={search_term_string}&quot;},&quot;query-input&quot;:&quot;required name=search_term_string&quot;}]}" )
					->render()
			)
			->render()
	)
	->child( $search_parameter )
	->child(
		( new HTML_Refactory( 'hr' ) )
			->attr( 'style', 'height:5px;opacity:1' )
			->attr( 'class', array( 'mt-4', 'border-0', 'bg-white', 'rounded', 'shadow-sm' ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'h3' ) )
			->attr( 'class', array( 'mt-4', 'mb-0' ) )
			->text( 'Enable Default&nbsp;' )
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'border-radius:3px' )
					->text( 'WebPage' )
					->render()
			)
			->child( ' Schema' )
			->render()
	)
	->child( $webpage_option )
	->child(
		( new HTML_Refactory( 'fieldset' ) )
			->attr( 'class', array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-4', 'noselect' ) )
			->child(
				( new HTML_Refactory( 'legend' ) )
					->attr( 'class', array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ) )
					->attr( 'style', 'width:auto' )
					->text( esc_html( 'WebPage Format Example:' ) )
					->render()
			)
			->child( wp_kses_post( $example_clarification ) )
			->child(
				( new HTML_Refactory( 'pre' ) )
					->attr( 'id', 'webpage_example' )
					->attr( 'class', array( 'mb-0', 'rounded', 'language-json' ) )
					->attr( 'data-schema', '{&quot;@context&quot;:&quot;http://schema.org/&quot;,&quot;@id&quot;:&quot;https://example.com/pathname#webpage&quot;,&quot;@type&quot;:&quot;WebPage&quot;,&quot;url&quot;:&quot;https://example.com/pathname&quot;,&quot;name&quot;:&quot;Pathname Page Title&quot;}' )
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'hr' ) )
			->attr( 'style', 'height:5px;opacity:1' )
			->attr( 'class', array( 'mt-4', 'border-0', 'bg-white', 'rounded', 'shadow-sm' ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'h3' ) )
			->attr( 'class', array( 'mt-4', 'mb-0' ) )
			->text( 'Enable Default&nbsp;' )
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'style', 'border-radius:3px' )
					->text( 'BreadcrumbList' )
					->render()
			)
			->child( '&nbsp;Schema' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'd-flex', 'flex-row', 'mb-1', 'pt-1' ) )
			->child(
				( new HTML_Refactory( 'svg' ) )
					->attr( 'class', array( 'bi', 'bi-info-circle', 'text-secondary', 'mt-n1' ) )
					->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
					->attr( 'width', '16' )
					->attr( 'height', '16' )
					->attr( 'fill', 'currentColor' )
					->attr( 'viewBox', '0 0 16 16' )
					->child(
						( new HTML_Refactory( 'path' ) )
							->attr( 'd', 'M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z' )
							->render()
					)
					->child(
						( new HTML_Refactory( 'path' ) )
							->attr( 'd', 'm8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z' )
							->render()
					)
					->render()
			)
			->child(
				( new HTML_Refactory( 'div' ) )
					->attr( 'class', array( 'text-secondary', 'ms-1' ) )
					->attr( 'style', 'font-size:10pt;margin-top:-1px' )
					->child(
						( new HTML_Refactory( 'em' ) )
							->text( 'It is recommended that breadcrumbs remain enabled as they are utilized by Google.' )
							->render()
					)
					->render()
			)
			->render()
	)
	->child( $breadcrumb_option )
	->child(
		( new HTML_Refactory( 'fieldset' ) )
			->attr( 'class', array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-4', 'noselect' ) )
			->child(
				( new HTML_Refactory( 'legend' ) )
					->attr( 'class', array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ) )
					->attr( 'style', 'width:auto' )
					->text( 'BreadcrumbList Format Example:' )
					->render()
			)
			->child( wp_kses_post( $example_clarification ) )
			->child(
				( new HTML_Refactory( 'pre' ) )
					->attr( 'id', 'breadcrumb_example' )
					->attr( 'class', array( 'mb-0', 'rounded', 'language-json' ) )
					->attr( 'data-schema', '{&quot;@context&quot;: &quot;https://schema.org/&quot;,&quot;@type&quot;: &quot;BreadcrumbList&quot;,&quot;itemListElement&quot;: [{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;1&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page&quot;,&quot;name&quot;: &quot;Parent Page Title&quot;}},{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;2&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page/child-page&quot;,&quot;name&quot;: &quot;Child Page Title&quot;}},{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;3&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page/child-page/grandchild-page&quot;,&quot;name&quot;: &quot;Grandchild Page Title&quot;}}]}' )
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'hr' ) )
			->attr( 'style', 'height:5px' )
			->attr( 'class', array( 'border', 'bg-light', 'rounded' ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'hr' ) )
			->attr( 'style', 'height:5px;opacity:1' )
			->attr( 'class', array( 'mt-1', 'border-0', 'bg-white', 'rounded', 'shadow-sm' ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'h3' ) )
			->attr( 'id', 'disable_yoast_schema' )
			->attr( 'class', array( 'mt-5' ) )
			->text( 'Disable Yoast SEO schema?' )
			->render()
	)
	->child( $yoast_message )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'd-flex', 'flex-column', 'mt-3', 'mb-0', 'ps-4', 'py-3', 'radio-border-left', 'bg-white' ) )
			->child(
				( new HTML_Refactory( 'label' ) )
					->attr( 'for', 'enable_yoast' )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'id', 'enable_yoast' )
							->attr( 'type', 'radio' )
							->attr( 'name', 'disable_yoast' )
							->attr( 'value', '0' )
							->attr( 'checked', ( $is_yoast_disabled == 0 ) )
							->attr( 'disabled', 'disabled' === $if_yoast )
							->render()
					)
					->child( ( new HTML_Refactory( 'strong' ) )->text( 'Enable' )->render() )
					->child( '&nbsp;Yoast Schema' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'label' ) )
					->attr( 'for', 'disable_yoast' )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'id', 'disable_yoast' )
							->attr( 'type', 'radio' )
							->attr( 'name', 'disable_yoast' )
							->attr( 'value', '1' )
							->attr( 'checked', ( $is_yoast_disabled == 1 ) )
							->attr( 'disabled', 'disabled' === $if_yoast )
							->render()
					)
					->child( ( new HTML_Refactory( 'strong' ) )->text( 'Disable' )->render() )
					->child( '&nbsp;Yoast Schema' )
					->child( wp_kses_post( $default_setting_label_html ) )
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'h3' ) )
			->attr( 'id', 'disable_aio_schema' )
			->attr( 'class', array( 'mt-3' ) )
			->text( 'Disable All in One SEO schema?' )
			->render()
	)
	->child( $aioseo_message )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'd-flex', 'flex-column', 'mt-3', 'mb-0', 'ps-4', 'py-3', 'radio-border-left', 'bg-white' ) )
			->child(
				( new HTML_Refactory( 'label' ) )
					->attr( 'for', 'enable_aio' )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'id', 'enable_aio' )
							->attr( 'type', 'radio' )
							->attr( 'name', 'disable_aio' )
							->attr( 'value', '0' )
							->attr( 'checked', ( $is_aio_disabled == 0 ) )
							->attr( 'disabled', 'disabled' === $if_aio )
							->render()
					)
					->child(
						( new HTML_Refactory( 'strong' ) )
							->text( 'Enable&nbsp;' )
							->render()
					)
					->child( 'AIOSEO Schema' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'label' ) )->attr( 'for', 'disable_aio' )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'id', 'disable_aio' )
							->attr( 'type', 'radio' )
							->attr( 'name', 'disable_aio' )
							->attr( 'value', '1' )
							->attr( 'checked', ( $is_aio_disabled == 1 ) )
							->attr( 'disabled', 'disabled' === $if_aio )
							->render()
					)
					->child(
						( new HTML_Refactory( 'strong' ) )
							->text( 'Disable&nbsp;' )
							->render()
					)
					->child( 'AIOSEO Schema' )
					->child( wp_kses_post( $default_setting_label_html ) )
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'h3' ) )
			->attr( 'id', 'disable_rankmath_schema' )
			->attr( 'class', array( 'mt-3' ) )
			->text( 'Disable Rank Math schema?' )
			->render()
	)
	->child( $rankmath_message )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'd-flex', 'flex-column', 'mt-3', 'mb-0', 'ps-4', 'py-3', 'radio-border-left', 'bg-white' ) )
			->child(
				( new HTML_Refactory( 'label' ) )
					->attr( 'for', 'enable_rankmath' )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'id', 'enable_rankmath' )
							->attr( 'type', 'radio' )
							->attr( 'name', 'disable_rankmath' )
							->attr( 'value', '0' )
							->attr( 'checked', ( $is_rankmath_disabled == 0 ) )
							->attr( 'disabled', 'disabled' === $if_rankmath )
							->render()
					)
					->child(
						( new HTML_Refactory( 'strong' ) )
							->text( 'Enable&nbsp;' )
							->render()
					)
					->child( 'Rank Math Schema' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'label' ) )
					->attr( 'for', 'disable_rankmath' )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'id', 'disable_rankmath' )
							->attr( 'type', 'radio' )
							->attr( 'name', 'disable_rankmath' )
							->attr( 'value', '1' )
							->attr( 'checked', ( $is_rankmath_disabled == 1 ) )
							->attr( 'disabled', 'disabled' === $if_rankmath )
							->render()
					)
					->child(
						( new HTML_Refactory( 'strong' ) )
							->text( 'Disable&nbsp;' )
							->render()
					)
					->child( 'Rank Math Schema' )
					->child( wp_kses_post( $default_setting_label_html ) )
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'hr' ) )
			->attr( 'style', 'height:5px;opacity:1' )
			->attr( 'class', array( 'mt-5', 'border-0', 'bg-white', 'rounded', 'shadow-sm' ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'h3' ) )
			->attr( 'class', array( 'mt-5' ) )
			->text( 'Pages to Exclude from Displaying Any Schema' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'p' ) )
			->text( 'To limit the amount of data passed to search engines, pages may be excluded from displaying any schema.' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'p' ) )
			->attr( 'class', array( 'fst-italic' ) )
			->text( 'In most cases, it will not be necessary to exclude a page.' )
			->render()
	)
	->child( $table_container )
	->child(
		( new HTML_Refactory( 'hr' ) )
			->attr( 'style', 'height:5px;opacity:1' )
			->attr( 'class', array( 'mt-5', 'border-0', 'bg-white', 'rounded', 'shadow-sm' ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'h3' ) )
			->attr( 'class', array( 'mt-5' ) )
			->text( 'Delete All Data on Uninstall?' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'p' ) )
			->text( 'If schema data might be needed in the future, do not change this setting.' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'd-flex', 'flex-column', 'mt-3', 'mb-0', 'ps-4', 'py-3', 'radio-border-left', 'bg-white' ) )
			->child(
				( new HTML_Refactory( 'label' ) )
					->attr( 'for', 'save_data' )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'id', 'save_data' )
							->attr( 'type', 'radio' )
							->attr( 'name', 'delete_on_uninstall' )
							->attr( 'value', '0' )
							->attr( 'checked', ( 0 === $is_data_deleted ) )
							->render()
					)
					->child(
						( new HTML_Refactory( 'strong' ) )
							->text( 'Save&nbsp;' )
							->render()
					)
					->child( 'Data' )
					->child( wp_kses_post( $default_setting_label_html ) )
					->render()
			)
			->child(
				( new HTML_Refactory( 'label' ) )
					->attr( 'for', 'delete_data' )
					->child(
						( new HTML_Refactory( 'input' ) )
							->attr( 'id', 'delete_data' )
							->attr( 'type', 'radio' )
							->attr( 'name', 'delete_on_uninstall' )
							->attr( 'value', '1' )
							->attr( 'checked', ( 1 === $is_data_deleted ) )
							->render()
					)
					->child(
						( new HTML_Refactory( 'strong' ) )
							->text( 'Delete ' )
							->render()
					)
					->child( 'Data' )
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'fixed-bottom', 'bg-light', 'p-3', 'mb-4', 'shadow', 'rounded' ) )
			->attr( 'style', 'left:50%;right:inherit;transform:translateX(-50%)' )
			->child(
				( new HTML_Refactory( 'button' ) )
					->attr( 'class', array( 'btn', 'btn-primary', 'px-5', 'py-2' ) )
					->attr( 'type', 'submit' )
					->text( 'Save Settings' )
					->render()
			)
			->render()
	)
	->render();

$main = ( new HTML_Refactory( 'main' ) )
	->attr( 'class', array( 'container', 'my-5' ) )
	->child( $page_title )
	->child( $settings_form )
	->render();

echo ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'container', 'pt-3' ) )
	->child( $header )
	->child( $main )
	->render();

add_action(
	'admin_footer',
	function () {
		echo '<script>';
		/**
		* LOAD ORDER MATTERS!
		*/
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/scsc-user-settings.js';
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/prism.js';
		echo '</script>';
		echo <<<SCRIPTS
    <script>
        document.getElementsByTagName('HEAD')[0].insertAdjacentHTML('beforeend', '<style>.radio-border-left {border-left: 5px solid lightgray;border-top-right-radius:5px;border-bottom-right-radius:5px}</style>');
        document.getElementById('lock-icon').addEventListener('click', (e) => {
            lockUnlock(e.target.parentElement);
        });
        document.querySelectorAll('[id^="post_num_"').forEach(input => {
            input.addEventListener('change', (e)=>{
                pageSelected(e.target.dataset.key)
            });
        });
    </script>
    <script>
SCRIPTS;
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/anime.js';
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/scsc-schema-animation.js';
		echo '</script>';
	}
);
