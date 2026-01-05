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

$default_setting_label_html = new HTML_Refactory(
	'small',
	array( 'class' => array( 'text-secondary' ) ),
	'',
	new HTML_Refactory(
		'i',
		array(),
		'&nbsp;(default)'
	)
);

echo '<main class="container mt-5 ms-0">';

$scalpel_icon = new HTML_Refactory(
	'img',
	array(
		'class' => array( 'mt-n4' ),
		'src'   => esc_url( plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/scalpel_title.svg' ),
	)
);

$h1 = new HTML_Refactory(
	'h1',
	array(),
	'User Settings ',
	$scalpel_icon
);

$alert1 = new HTML_Refactory(
	'p',
	array(
		'class' => array( 'alert', 'alert-primary' ),
		'role'  => 'alert',
	),
	esc_html( 'The default settings for this plugin allow it to perform at its best. However, you may encounter an edge case that requires modification of the default settings.' )
);

$alert2 = new HTML_Refactory(
	'p',
	array(
		'class' => array( 'alert', 'alert-warning', 'mt-4' ),
		'role'  => 'alert',
	),
	'NOTE: ' . new HTML_Refactory(
		'em',
		array(),
		'This plugin ' . new HTML_Refactory(
			'a',
			array( 'href' => esc_url( '/wp-admin/admin.php?page=scsc_settings#disable_yoast_schema' ) ),
			esc_html( 'automatically overrides Yoast, AIOSEO, and Rank Math schema' )
		),
		' and injects your customized schema to better fit your SEO objectives.'
	)
);

echo new HTML_Refactory(
	'header',
	array(),
	'',
	$h1 . $alert1 . $alert2
);

echo '<div><form method="post" action="" class="settings-page-form">';

wp_nonce_field( 'scsc_save_settings', 'scsc_settings_nonce' );

echo new HTML_Refactory(
	'input',
	array(
		'type'  => 'hidden',
		'name'  => 'page',
		'value' => 'scsc_settings',
	)
);

echo new HTML_Refactory(
	'input',
	array(
		'type'  => 'hidden',
		'name'  => 'save',
		'value' => 'save',
	)
);

echo new HTML_Refactory(
	'h3',
	array( 'class' => array( 'mt-5', 'mb-0' ) ),
	'Enable Default&nbsp;' . new HTML_Refactory(
		'code',
		array(),
		'Website'
	) . '&nbsp;Schema'
);

echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'd-flex', 'flex-column', 'mb-0', 'ps-4', 'py-3', 'radio-border-left' ) ),
	'',
	new HTML_Refactory(
		'label',
		array( 'for' => 'enable_website' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'    => 'enable_website',
				'type'  => 'radio',
				'name'  => 'enable_website',
				'value' => '1',
				( ( 1 === $is_website_enabled ) ? 'checked' : '' ) => '',
			),
			'',
			''
		) . new HTML_Refactory( 'strong', array(), 'Enable ' ) . new HTML_Refactory( 'code', array( 'style' => 'color:black' ), 'WebSite' ) . wp_kses_post( wp_slash( $default_setting_label_html ) )
	) . new HTML_Refactory(
		'label',
		array( 'for' => 'disable_website' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'    => 'disable_website',
				'type'  => 'radio',
				'name'  => 'enable_website',
				'value' => '0',
				( ( 0 === $is_website_enabled ) ? 'checked' : '' ) => '',
			),
			'',
			''
		) . new HTML_Refactory( 'strong', array(), 'Disable ' ) . new HTML_Refactory( 'code', array( 'style' => 'color:black' ), 'WebSite' )
	)
);

echo new HTML_Refactory(
	'fieldset',
	array( 'class' => array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-4', 'noselect' ) ),
	'',
	new HTML_Refactory(
		'legend',
		array(
			'class' => array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ),
			'style' => 'wdith:auto',
		),
		esc_html( 'WebSite Format Example:' )
	) . wp_kses( $example_clarification, 'post' ) . new HTML_Refactory(
		'pre',
		array(
			'id'          => 'website_example',
			'class'       => array( 'mb-0', 'rounded', 'language-json' ),
			'data-schema' => "{&quot;@context&quot;:&quot;http://schema.org/&quot;,&quot;@id&quot;:&quot;https://example.com/#website&quot;,&quot;@type&quot;:&quot;WebSite&quot;,&quot;url&quot;:&quot;https://example.com/&quot;,&quot;name&quot;:&quot;WebSite Name&quot;,&quot;potentialAction&quot;:[{&quot;@type&quot;:&quot;SearchAction&quot;,&quot;target&quot;:{&quot;@type&quot;:&quot;EntryPoint&quot;,&quot;urlTemplate&quot;:&quot;https://example.com/?{$search_key}={search_term_string}&quot;},&quot;query-input&quot;:&quot;required name=search_term_string&quot;}]}",
		)
	)
);

echo new HTML_Refactory(
	'label',
	array(
		'class' => array( 'd-flex', 'flex-column', 'mt-3' ),
		'for'   => 'search_param',
	),
	'',
	new HTML_Refactory(
		'div',
		array( 'class' => array( 'mb-2' ) ),
		new HTML_Refactory(
			'code',
			array( 'style' => 'color:black' ),
			'urlTemplate'
		) . '&nbsp;search parameter key:'
	) . new HTML_Refactory(
		'div',
		array(),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'          => 'search_param',
				'class'       => array( 'me-2' ),
				'name'        => 'search_param',
				'type'        => 'text',
				'placeholder' => 'Current: ' . sanitize_text_field( $search_key ),
				'disabled'    => '',
			)
		) . new HTML_Refactory(
			'div',
			array(
				'id'    => 'search_param_lock',
				'class' => array( 'btn', 'btn-outline-danger' ),
			),
			'',
			new HTML_Refactory(
				'svg',
				array(
					'id'      => 'lock-icon',
					'xmlns'   => 'http://www.w3.org/2000/svg',
					'width'   => '16',
					'height'  => '16',
					'fill'    => 'currentColor',
					'viewBox' => '0 0 16 16',
				),
				'',
				new HTML_Refactory(
					'path',
					array( 'd' => 'M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z' )
				)
			)
		)
	) . new HTML_Refactory(
		'div',
		array(),
		'',
		new HTML_Refactory(
			'i',
			array( 'style' => 'font-size:.75em' ),
			'Note: The default WordPress search parameter key is the letter ' . new HTML_Refactory( 'code', array(), 's' ) . '.'
		)
	)
);

echo new HTML_Refactory(
	'hr',
	array(
		'style' => 'height:5px',
		'class' => array( 'border', 'bg-light', 'rounded' ),
	),
	'',
	''
);

echo new HTML_Refactory(
	'h3',
	array( 'class' => array( 'mt-5', 'mb-0' ) ),
	'Enable Default ' . new HTML_Refactory( 'code', array(), 'WebPage' ) . ' Schema'
);


echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'd-flex', 'flex-column', 'mb-3', 'ps-4', 'py-3', 'radio-border-left' ) ),
	'',
	new HTML_Refactory(
		'label',
		array( 'for' => 'enable_webpage' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'    => 'enable_webpage',
				'type'  => 'radio',
				'name'  => 'enable_webpage',
				'value' => '1',
				( ( $is_webpage_enabled == 1 ) ? 'checked' : '' ) => '',
			),
			'',
			''
		) . new HTML_Refactory( 'strong', array(), 'Enable&nbsp;' ) . new HTML_Refactory( 'code', array( 'style' => 'color:black' ), 'WebPage' ) . wp_kses_post( wp_slash( $default_setting_label_html ) )
	) . new HTML_Refactory(
		'label',
		array( 'for' => 'disable_webpage' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'    => 'disable_webpage',
				'type'  => 'radio',
				'name'  => 'enable_webpage',
				'value' => '0',
				( ( $is_webpage_enabled == 0 ) ? 'checked' : '' ) => '',
			),
			'',
			''
		) . new HTML_Refactory( 'strong', array(), 'Disable&nbsp;' ) . new HTML_Refactory( 'code', array( 'style' => 'color:black' ), 'WebPage' )
	)
);

echo new HTML_Refactory(
	'fieldset',
	array( 'class' => array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-4', 'noselect' ) ),
	'',
	new HTML_Refactory(
		'legend',
		array(
			'class' => array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ),
			'style' => 'wdith:auto',
		),
		esc_html( 'WebPage Format Example:' )
	) . wp_kses_post( $example_clarification ) . new HTML_Refactory(
		'pre',
		array(
			'id'          => 'webpage_example',
			'class'       => array( 'mb-0', 'rounded', 'language-json' ),
			'data-schema' => '{&quot;@context&quot;:&quot;http://schema.org/&quot;,&quot;@id&quot;:&quot;https://example.com/pathname#webpage&quot;,&quot;@type&quot;:&quot;WebPage&quot;,&quot;url&quot;:&quot;https://example.com/pathname&quot;,&quot;name&quot;:&quot;Pathname Page Title&quot;}',
		)
	)
);

echo new HTML_Refactory(
	'hr',
	array(
		'style' => 'height:5px',
		'class' => array( 'border', 'bg-light', 'rounded' ),
	),
	'',
	''
);

echo new HTML_Refactory(
	'h3',
	array( 'class' => array( 'mt-5', 'mb-0' ) ),
	'Enable Default&nbsp;' . new HTML_Refactory( 'code', array(), 'BreadcrumbList' ) . '&nbsp;Schema'
);

echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'd-flex', 'flex-row', 'mb-1', 'pt-1' ) ),
	'',
	new HTML_Refactory(
		'svg',
		array(
			'class'   => array( 'bi', 'bi-info-circle', 'text-secondary', 'mt-n1' ),
			'xmlns'   => 'http://www.w3.org/2000/svg',
			'widht'   => '16',
			'height'  => '16',
			'fill'    => 'currentColor',
			'viewBox' => '0 0 16 16',
		),
		'',
		new HTML_Refactory(
			'path',
			array( 'd' => 'M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z' ),
			'',
			''
		) . new HTML_Refactory(
			'path',
			array( 'd' => 'm8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z' ),
			'',
			''
		)
	) . new HTML_Refactory(
		'div',
		array(
			'class' => array( 'text-secondary', 'ms-1' ),
			'style' => 'font-size:10pt;margin-top:-1px',
		),
		'',
		new HTML_Refactory( 'em', array(), 'It is recommended that breadcrumbs remain enabled as they are utilized by Google.' )
	)
);

echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'd-flex', 'flex-column', 'mb-3', 'ps-4', 'py-3', 'radio-border-left' ) ),
	'',
	new HTML_Refactory(
		'label',
		array( 'for' => 'enable_breadcrumbs' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'    => 'enable_breadcrumbs',
				'type'  => 'radio',
				'name'  => 'enable_breadcrumbs',
				'value' => '1',
				( ( 1 === $are_breadcrumbs_enabled ) ? 'checked' : '' ) => '',
			),
			'',
			''
		) . new HTML_Refactory(
			'strong',
			array(),
			'Enable&nbsp;'
		) . new HTML_Refactory(
			'code',
			array( 'style' => 'color:black' ),
			'BreadcrumbList'
		) . wp_kses_post( wp_slash( $default_setting_label_html ) )
	) . new HTML_Refactory(
		'label',
		array( 'for' => 'disable_breadcrumbs' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'    => 'disable_breadcrumbs',
				'type'  => 'radio',
				'name'  => 'enable_breadcrumbs',
				'value' => '0',
				( ( 0 === $are_breadcrumbs_enabled ) ? 'checked' : '' ) => '',
			),
			'',
			''
		) . new HTML_Refactory(
			'strong',
			array(),
			'Disable&nbsp;'
		) . new HTML_Refactory(
			'code',
			array( 'style' => 'color:black' ),
			'BreadcrumbList'
		)
	)
);

echo new HTML_Refactory(
	'fieldset',
	array( 'class' => array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-4', 'noselect' ) ),
	'',
	new HTML_Refactory(
		'legend',
		array(
			'class' => array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ),
			'style' => 'wdith:auto',
		),
		esc_html( 'BreadcrumbList Format Example:' )
	) . wp_kses_post( $example_clarification ) . new HTML_Refactory(
		'pre',
		array(
			'id'          => 'breadcrumb_example',
			'class'       => array( 'mb-0', 'rounded', 'language-json' ),
			'data-schema' => '{&quot;@context&quot;: &quot;https://schema.org/&quot;,&quot;@type&quot;: &quot;BreadcrumbList&quot;,&quot;itemListElement&quot;: [{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;1&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page&quot;,&quot;name&quot;: &quot;Parent Page Title&quot;}},{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;2&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page/child-page&quot;,&quot;name&quot;: &quot;Child Page Title&quot;}},{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;3&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page/child-page/grandchild-page&quot;,&quot;name&quot;: &quot;Grandchild Page Title&quot;}}]}',
		)
	)
);

echo new HTML_Refactory(
	'hr',
	array(
		'style' => 'height:5px',
		'class' => array( 'border', 'bg-light', 'rounded' ),
	)
);

echo new HTML_Refactory(
	'h3',
	array(
		'id'    => 'disable_yoast_schema',
		'class' => array( 'mt-3' ),
	),
	esc_html( 'Disable Yoast SEO schema?' )
);

if ( 'disabled' === $if_yoast ) {

	echo new HTML_Refactory(
		'pre',
		array( 'class' => array( 'mb-0', 'rounded', 'language-js' ) ),
		'',
		new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;YOAST not detected.',
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&gt;&nbsp;Nothing to worry about here.'
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&gt;&gt;&nbsp;Happy schema-ing!'
		)
	);

} else {

	echo new HTML_Refactory(
		'pre',
		array( 'class' => array( 'mb-0', 'rounded', 'language-js' ) ),
		'',
		new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;YOAST has been detected...',
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			"&gt;&nbsp;It is recommended that YOAST's schema feature remain disabled."
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;All other YOAST features will be unaffected.'
		)
	);

}

echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'd-flex', 'flex-column', 'mt-3', 'ps-4', 'py-3', 'radio-border-left' ) ),
	'',
	new HTML_Refactory(
		'label',
		array( 'for' => 'enable_yoast' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'                             => 'enable_yoast',
				'type'                           => 'radio',
				'name'                           => 'disable_yoast',
				'value'                          => '0',
				( ( $is_yoast_disabled == 0 ) ? 'checked' : '' ) => '',
				sanitize_text_field( $if_yoast ) => '',
			)
		) . new HTML_Refactory(
			'strong',
			array(),
			'Enable',
		) . '&nbsp;Yoast Schema'
	) . new HTML_Refactory(
		'label',
		array( 'for' => 'disable_yoast' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'                             => 'disable_yoast',
				'type'                           => 'radio',
				'name'                           => 'disable_yoast',
				'value'                          => '1',
				( ( $is_yoast_disabled == 1 ) ? 'checked' : '' ) => '',
				sanitize_text_field( $if_yoast ) => '',
			)
		) . new HTML_Refactory(
			'strong',
			array(),
			'Disable',
		) . '&nbsp;Yoast Schema' . wp_kses_post( $default_setting_label_html )
	)
);

echo new HTML_Refactory(
	'h3',
	array(
		'id'    => 'disable_aio_schema',
		'class' => array( 'mt-3' ),
	),
	esc_html( 'Disable All in One SEO schema?' )
);

if ( 'disabled' === $if_aio ) {

	echo new HTML_Refactory(
		'pre',
		array( 'class' => array( 'mb-0', 'rounded', 'language-js' ) ),
		'',
		new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;AIOSEO not detected.',
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&gt;&nbsp;Nothing to worry about here.'
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&gt;&gt;&nbsp;Happy schema-ing!'
		)
	);

} else {

	echo new HTML_Refactory(
		'pre',
		array( 'class' => array( 'mb-0', 'rounded', 'language-js' ) ),
		'',
		new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;AIOSEO has been detected...',
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			"&gt;&nbsp;It is recommended that AIOSEO's schema feature remain disabled."
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;All other AIOSEO features will be unaffected.'
		)
	);

}

echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'd-flex', 'flex-column', 'mt-3', 'ps-4', 'py-3', 'radio-border-left' ) ),
	'',
	new HTML_Refactory(
		'label',
		array( 'for' => 'enable_aio' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'                           => 'enable_aio',
				'type'                         => 'radio',
				'name'                         => 'disable_aio',
				'value'                        => '0',
				( ( $is_aio_disabled == 0 ) ? 'checked' : '' ) => '',
				sanitize_text_field( $if_aio ) => '',
			)
		) . new HTML_Refactory(
			'strong',
			array(),
			'Enable&nbsp;',
		) . 'AIOSEO Schema'
	) . new HTML_Refactory(
		'label',
		array( 'for' => 'disable_aio' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'                           => 'disable_aio',
				'type'                         => 'radio',
				'name'                         => 'disable_aio',
				'value'                        => '1',
				( ( $is_aio_disabled == 1 ) ? 'checked' : '' ) => '',
				sanitize_text_field( $if_aio ) => '',
			)
		) . new HTML_Refactory(
			'strong',
			array(),
			'Disable&nbsp;',
		) . 'AIOSEO Schema' . wp_kses_post( $default_setting_label_html )
	)
);

echo new HTML_Refactory(
	'h3',
	array(
		'id'    => 'disable_rankmath_schema',
		'class' => array( 'mt-3' ),
	),
	esc_html( 'Disable Rank Math schema?' )
);

if ( 'disabled' === $if_rankmath ) {

	echo new HTML_Refactory(
		'pre',
		array( 'class' => array( 'mb-0', 'rounded', 'language-js' ) ),
		'',
		new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;Rank Math not detected.',
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&gt;&nbsp;Nothing to worry about here.'
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&gt;&gt;&nbsp;Happy schema-ing!'
		)
	);

} else {

	echo new HTML_Refactory(
		'pre',
		array( 'class' => array( 'mb-0', 'rounded', 'language-js' ) ),
		'',
		new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;Rank Math has been detected...',
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			"&gt;&nbsp;It is recommended that Rank Math's schema feature remain disabled."
		) . new HTML_Refactory(
			'br',
			array(),
			'',
			''
		) . new HTML_Refactory(
			'code',
			array( 'class' => array( 'language-js' ) ),
			'&gt;&nbsp;All other Rank Math features will be unaffected.'
		)
	);

}

echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'd-flex', 'flex-column', 'mt-3', 'ps-4', 'py-3', 'radio-border-left' ) ),
	'',
	new HTML_Refactory(
		'label',
		array( 'for' => 'enable_rankmath' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'                                => 'enable_rankmath',
				'type'                              => 'radio',
				'name'                              => 'disable_rankmath',
				'value'                             => '0',
				( ( $is_rankmath_disabled == 0 ) ? 'checked' : '' ) => '',
				sanitize_text_field( $if_rankmath ) => '',
			)
		) . new HTML_Refactory(
			'strong',
			array(),
			'Enable&nbsp;',
		) . 'Rank Math Schema'
	) . new HTML_Refactory(
		'label',
		array( 'for' => 'disable_rankmath' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'                                => 'disable_rankmath',
				'type'                              => 'radio',
				'name'                              => 'disable_rankmath',
				'value'                             => '1',
				( ( $is_rankmath_disabled == 1 ) ? 'checked' : '' ) => '',
				sanitize_text_field( $if_rankmath ) => '',
			)
		) . new HTML_Refactory(
			'strong',
			array(),
			'Disable&nbsp;',
		) . 'Rank Math Schema' . wp_kses_post( $default_setting_label_html )
	)
);

echo new HTML_Refactory(
	'hr',
	array(
		'style' => 'height:5px',
		'class' => array( 'border', 'bg-light', 'rounded' ),
	),
	'',
	''
);

echo new HTML_Refactory(
	'h3',
	array(),
	'Pages to Exclude from Displaying Any Schema'
);

echo new HTML_Refactory(
	'p',
	array(),
	'To limit the amount of data passed to search engines, pages may be excluded from displaying any schema.'
);

echo new HTML_Refactory(
	'p',
	array( 'class' => array( 'fst-italic' ) ),
	'In most cases, it will not be necessary to exclude a page.'
);

echo '<div style="max-height:500px;overflow-y:scroll;"><table id="excluded_schema" class="table table-dark">';

$th_tags   = '';
$col_names = array( 'Page ID', 'Page Title', 'Page URL', 'Exclude?' );
foreach ( $col_names as $key => $name ) {
	$th_tags .= new HTML_Refactory(
		'th',
		array( 'scope' => 'col' ),
		$name
	);
}

echo new HTML_Refactory(
	'thead',
	array(),
	'',
	new HTML_Refactory(
		'tr',
		array(),
		'',
		$th_tags
	)
);

if ( $all_pages ) {

	$url     = get_site_url();
	$tr_tags = '';

	foreach ( $all_pages as $key => $value ) {
		/**
		 * CHECK FOR EXCLUDED PAGES
		 */
		$is_excluded = ( in_array( $all_pages[ $key ]['ID'], $database_exclusions ) ) ? true : false;
		$text_color  = ( $is_excluded ) ? '' : 'text-secondary';
		$checked     = ( $is_excluded ) ? 'checked' : '';

		$tr_tags .= new HTML_Refactory(
			'tr',
			array( 'class' => array( $text_color ) ),
			'',
			new HTML_Refactory(
				'th',
				array( 'scope' => 'row' ),
				$all_pages[ $key ]['ID']
			) . new HTML_Refactory(
				'td',
				array(),
				$all_pages[ $key ]['post_title']
			) . new HTML_Refactory(
				'td',
				array(),
				'',
				new HTML_Refactory(
					'a',
					array(
						'href'   => sanitize_url( $url ) . '/' . sanitize_title_with_dashes( $all_pages[ $key ]['post_name'] ),
						'target' => '_blank',
					),
					sanitize_url( $url ) . '/' . sanitize_title_with_dashes( $all_pages[ $key ]['post_name'] )
				)
			) . new HTML_Refactory(
				'td',
				array(),
				'',
				new HTML_Refactory(
					'input',
					array(
						'type'     => 'checkbox',
						'id'       => 'post_num_' . sanitize_text_field( $all_pages[ $key ]['ID'] ),
						'name'     => 'exclude_' . sanitize_text_field( $all_pages[ $key ]['ID'] ),
						$checked   => '',
						'data-key' => $key,
					)
				)
			)
		);
	}
}

echo new HTML_Refactory(
	'tbody',
	array(),
	'',
	$tr_tags
);

echo '</table></div>';

echo new HTML_Refactory(
	'h3',
	array(),
	'Delete All Data on Uninstall?'
);

echo new HTML_Refactory(
	'p',
	array(),
	'If schema data might be needed in the future, do not change this setting.'
);

echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'd-flex', 'flex-column', 'mt-3', 'ps-4', 'py-3', 'radio-border-left' ) ),
	'',
	new HTML_Refactory(
		'label',
		array( 'for' => 'save_data' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'    => 'save_data',
				'type'  => 'radio',
				'name'  => 'delete_on_uninstall',
				'value' => '0',
				( ( 0 === $is_data_deleted ) ? 'checked' : '' ) => '',
			)
		) . new HTML_Refactory(
			'strong',
			array(),
			'Save&nbsp;',
		) . 'Data' . wp_kses_post( $default_setting_label_html )
	) . new HTML_Refactory(
		'label',
		array( 'for' => 'delete_data' ),
		'',
		new HTML_Refactory(
			'input',
			array(
				'id'    => 'delete_data',
				'type'  => 'radio',
				'name'  => 'delete_on_uninstall',
				'value' => '1',
				( ( 1 === $is_data_deleted ) ? 'checked' : '' ) => '',
			)
		) . new HTML_Refactory(
			'strong',
			array(),
			'Delete ',
		) . 'Data'
	)
);

echo new HTML_Refactory(
	'hr',
	array(
		'class' => array( 'mb-5' ),
	)
);

echo new HTML_Refactory(
	'div',
	array(
		'class' => array( 'fixed-bottom', 'bg-light', 'p-3', 'mb-4', 'shadow', 'rounded' ),
		'style' => 'left:50%;right:inherit;transform:translateX(-50%)',
	),
	'',
	new HTML_Refactory(
		'button',
		array(
			'class' => array( 'btn', 'btn-primary', 'px-5', 'py-2' ),
			'type'  => 'submit',
		),
		esc_html( 'Save Settings' )
	)
);

echo '</form></div></main>';

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
            document.getElementsByTagName('HEAD')[0].insertAdjacentHTML('beforeend', '<style>.radio-border-left {border-left: 5px solid lightgray;}</style>');
            document.getElementById('lock-icon').addEventListener('click', (e) => {
                lockUnlock(e.target.parentElement);
            });
            document.querySelectorAll('[id^="post_num_"').forEach(input => {
                input.addEventListener('change', (e)=>{
                    pageSelected(e.target.dataset.key)
                });
            });
        </script>
SCRIPTS;
	}
);
