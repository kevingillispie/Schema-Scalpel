<?php
/**
 * Class for outputting client-side data.
 *
 * @link       https://schemascalpel.com
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/public
 * @author     Kevin Gillispie
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that outputs saved schema (no caching - always fresh).
 */
class SCSC_Public {

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	private string $schema_scalpel;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private string $version;

	/**
	 * Class constructor.
	 *
	 * @param string $schema_scalpel Plugin name.
	 * @param string $version        Plugin version.
	 */
	public function __construct( string $schema_scalpel, string $version ) {
		$this->schema_scalpel = $schema_scalpel;
		$this->version        = $version;
	}

	/**
	 * Return the page title for a given URL.
	 *
	 * @param string $url Page URL.
	 *
	 * @return string
	 */
	private function get_page_title( string $url ): string {
		return get_the_title( url_to_postid( $url ) );
	}

	/**
	 * Build and return breadcrumb schema as JSON.
	 *
	 * @param string $root_domain Site address.
	 * @param array  $breadcrumbs Breadcrumb data.
	 *
	 * @return string JSON-encoded breadcrumb schema.
	 */
	private function format_breadcrumbs( string $root_domain, array $breadcrumbs ): string {
		$items = array();

		if ( isset( $breadcrumbs[0] ) && false !== $breadcrumbs[0] ) {
			$i          = 0;
			$crumb_path = '';
			$length     = count( $breadcrumbs );

			while ( $i < $length ) {
				$crumb_path .= '/' . $breadcrumbs[ $i ];
				$page_title  = $this->get_page_title( $root_domain . $crumb_path );

				if ( ! $page_title ) {
					$page_title = wp_get_document_title();
				}

				$page_title = sanitize_text_field( $page_title );

				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'item'     => array(
						'@type' => 'WebPage',
						'@id'   => $root_domain . $crumb_path . '/',
						'name'  => $page_title,
					),
				);

				++$i;
			}
		}

		$breadcrumb_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);

		return wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS );
	}

	/**
	 * Build a JSON-LD script tag.
	 *
	 * @param string $json JSON-LD payload.
	 * @return string
	 */
	private function format_schema_html( string $json ): string {
		return wp_get_inline_script_tag(
			$json,
			array(
				'class' => 'schema-scalpel',
				'type'  => 'application/ld+json',
			)
		);
	}

	/**
	 * Output inline schema markup (always fresh - no caching).
	 */
	public function enqueue_inline_scripts() {
		global $post;

		if ( is_admin() || is_customize_preview() ) {
			return;
		}

		global $wpdb;

		$site_title     = sanitize_text_field( get_bloginfo( 'name' ) );
		$page_id        = get_the_ID();
		$schema_table   = $wpdb->prefix . 'scsc_custom_schemas';
		$settings_table = $wpdb->prefix . 'scsc_settings';

		/**
		 * Check database exclusions.
		 */
		$excluded_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key = 'exclude'", $settings_table ),
			ARRAY_A
		);

		$excluded_ids = array();
		if ( ! empty( $excluded_results ) ) {
			$excluded_ids = array_map( 'absint', wp_list_pluck( $excluded_results, 'setting_value' ) );

			if ( $page_id && in_array( $page_id, $excluded_ids, true ) ) {
				return;
			}

			if ( is_home() || ( is_front_page() && 'posts' === get_option( 'show_on_front' ) ) ) {
				$blog_page_id = absint( get_option( 'page_for_posts' ) );
				if ( $blog_page_id > 0 && in_array( $blog_page_id, $excluded_ids, true ) ) {
					return;
				}
			}
		}

		$root_domain = get_site_url();
		global $wp;
		$current_path = $wp->request;
		$path         = $current_path ? '/' . $current_path : '/';
		$page_title   = sanitize_text_field( wp_get_document_title() );

		$search_param_results = $wpdb->get_var(
			$wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key = 'search_param'", $settings_table )
		);
		$search_key           = $search_param_results ? $search_param_results : 's';

		// Build default schemas
		$website_schema = wp_json_encode(
			array(
				'@context'        => 'https://schema.org',
				'@id'             => $root_domain . '/#website',
				'@type'           => 'WebSite',
				'url'             => $root_domain . '/',
				'name'            => $site_title,
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => array(
						'@type'       => 'EntryPoint',
						'urlTemplate' => $root_domain . '/?' . $search_key . '={search_term_string}',
					),
					'query-input' => 'required name=search_term_string',
				),
			),
			JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
		);

		$webpage_schema = wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@id'      => $root_domain . $path . '#webpage',
				'@type'    => 'WebPage',
				'url'      => $root_domain . $path,
				'name'     => $page_title,
			),
			JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
		);

		$breadcrumbs       = array_filter( explode( '/', trim( $path, '/' ) ) );
		$breadcrumb_schema = $this->format_breadcrumbs( $root_domain, $breadcrumbs );

		// Build output
		$output = '';

		// Inject global schema.
		$global_schema = $wpdb->get_results(
			$wpdb->prepare( "SELECT custom_schema FROM %i WHERE schema_type = 'global'", $schema_table ),
			ARRAY_A
		);

		foreach ( $global_schema as $row ) {
			$schema = maybe_unserialize( $row['custom_schema'] );
			if ( is_string( $schema ) ) {
				$schema  = html_entity_decode( $schema );
				$schema  = str_replace( '&apos;', "'", $schema );
				$output .= $this->format_schema_html( $schema );
			}
		}

		// Inject post/page schema.
		if ( is_single() ) {
			$results = $wpdb->get_results(
				$wpdb->prepare( "SELECT custom_schema FROM %i WHERE post_id = %d AND schema_type = 'posts'", $schema_table, $page_id ),
				ARRAY_A
			);
		} elseif ( is_page() && ! is_front_page() ) {
			$results = $wpdb->get_results(
				$wpdb->prepare( "SELECT custom_schema FROM %i WHERE post_id = %d AND schema_type = 'pages'", $schema_table, $page_id ),
				ARRAY_A
			);
		} elseif ( is_home() && ! is_front_page() ) {
			$target_id = absint( get_option( 'page_for_posts' ) );
			if ( 0 < $target_id ) {
				$results = $wpdb->get_results(
					$wpdb->prepare( "SELECT custom_schema FROM %i WHERE post_id = %d AND schema_type = 'pages'", $schema_table, $target_id ),
					ARRAY_A
				);
			} else {
				$results = array();
			}
		} else {
			$results = array();
		}

		foreach ( $results as $row ) {
			$schema = maybe_unserialize( $row['custom_schema'] );
			if ( is_string( $schema ) ) {
				$schema  = html_entity_decode( $schema );
				$schema  = str_replace( '&apos;', "'", $schema );
				$output .= $this->format_schema_html( $schema );
			}
		}

		// Inject homepage schema.
		if ( is_front_page() ) {
			$home_id         = get_the_ID();
			$all_home_schema = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT custom_schema FROM %i 
                    WHERE schema_type = 'homepage' 
                    OR (schema_type = 'pages' AND post_id = %d)",
					$schema_table,
					$home_id
				),
				ARRAY_A
			);

			foreach ( $all_home_schema as $row ) {
				$schema = maybe_unserialize( $row['custom_schema'] );
				if ( is_string( $schema ) ) {
					$schema  = html_entity_decode( $schema );
					$schema  = str_replace( '&apos;', "'", $schema );
					$output .= $this->format_schema_html( $schema );
				}
			}
		}

		// Inject default schemas based on settings.
		$website_enabled = $wpdb->get_var(
			$wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key = 'website_schema'", $settings_table )
		);

		if ( '1' === $website_enabled ) {
			$output .= $this->format_schema_html( $website_schema );
		}

		$webpage_enabled = $wpdb->get_var(
			$wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key = 'webpage_schema'", $settings_table )
		);

		if ( '1' === $webpage_enabled ) {
			$output .= $this->format_schema_html( $webpage_schema );
		}

		$breadcrumb_enabled = $wpdb->get_var(
			$wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key = 'breadcrumb_schema'", $settings_table )
		);

		if ( '/' !== $path && '1' === $breadcrumb_enabled ) {
			$output .= $this->format_schema_html( $breadcrumb_schema );
		}

		// Output the schema (always fresh).
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
