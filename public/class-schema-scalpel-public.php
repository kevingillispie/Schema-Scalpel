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
	exit();
}

/**
 * Class that outputs saved schema.
 */
class Schema_Scalpel_Public {

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
	 * Register CSS stylesheet.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->schema_scalpel,
			SCHEMA_SCALPEL_DIRECTORY . '/public/css/schema-scalpel-public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register JavaScript.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->schema_scalpel,
			SCHEMA_SCALPEL_DIRECTORY . '/public/js/schema-scalpel-public.js',
			array(),
			$this->version,
			false
		);
	}

	/**
	 * Return the page title for a given URL.
	 *
	 * @param string $url Page URL.
	 *
	 * @return string
	 */
	private function get_page_title( $url ) {
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
	private function format_breadcrumbs( string $root_domain, array $breadcrumbs ) {
		$items = array();

		if ( false !== $breadcrumbs[0] ) {
			$i          = 0;
			$crumb_path = '';
			$length     = count( $breadcrumbs );

			while ( $i < $length ) {
				$crumb_path .= '/' . $breadcrumbs[ $i ];
				$page_title  = self::get_page_title( $root_domain . $crumb_path );
				$page_title  = $page_title ? sanitize_text_field( $page_title ) : sanitize_text_field( wp_get_document_title() );

				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'item'     => array(
						'@type' => 'WebPage',
						'@id'   => $root_domain . $crumb_path,
						'name'  => $page_title,
					),
				);
				++$i;
			}
		}

		$breadcrumb_schema = array(
			'@context'        => 'https://schema.org/',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);

		return wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS );
	}

	/**
	 * Output a JSON-LD script tag.
	 *
	 * @param string $html Opening script tag.
	 * @param string $s    JSON-LD payload.
	 */
	private function format_schema_html( $html, $s ) {
		printf( '%s%s</script>', sanitize_post( $html, 'js' ), sanitize_post( $s, 'js' ) );
	}

	/**
	 * Enqueue inline schema markup.
	 */
	public function enqueue_inline_scripts() {
		/**
		 * Bail early if not a front-end page or if in admin/customizer.
		 */
		global $post;
		if ( ! $post || is_admin() || is_customize_preview() ) {
			return;
		}

		global $wpdb;
		$current_page_results = array();
		$site_title           = sanitize_text_field( get_bloginfo( 'name' ) );
		$page_id              = get_the_ID();

		/**
		 * Check database exclusions.
		 */
		$scsc_settings_table      = $wpdb->prefix . 'scsc_settings';
		$current_excluded_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'exclude';", $scsc_settings_table ),
			ARRAY_A
		);
		foreach ( $current_excluded_results as $key => $value ) {
			if ( in_array( $page_id, $current_excluded_results[ $key ], true ) ) {
				return;
			}
		}

		$root_domain        = get_site_url();
		$path               = esc_url_raw( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
		$path               = $path ? $path : '/';
		$wp_title           = wp_title( '|', false, 'right' );
		$page_title         = $wp_title ? sanitize_text_field( $wp_title ) :
			( get_the_title() ? sanitize_text_field( get_the_title() ) :
			( the_title_attribute( array( 'echo' => false ) ) ? sanitize_text_field( the_title_attribute( array( 'echo' => false ) ) ) :
			sanitize_text_field( $site_title ) ) );
		$schema_script_html = '<script class="schema-scalpel-seo" type="application/ld+json">';
		$search_query_param = $wpdb->get_results(
			$wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key='search_param';", $scsc_settings_table )
		);
		$search_key         = $search_query_param[0]->setting_value;
		$schema_table       = $wpdb->prefix . 'scsc_custom_schemas';

		/**
		 * Website schema.
		 */
		$website_schema = wp_json_encode(
			array(
				'@context'        => 'http://schema.org/',
				'@id'             => $root_domain . '/#website',
				'@type'           => 'WebSite',
				'url'             => $root_domain . '/',
				'name'            => $site_title,
				'potentialAction' => array(
					array(
						'@type'       => 'SearchAction',
						'target'      => array(
							'@type'       => 'EntryPoint',
							'urlTemplate' => $root_domain . '/?' . $search_key . '={search_term_string}',
						),
						'query-input' => 'required name=search_term_string',
					),
				),
			),
			JSON_UNESCAPED_SLASHES
		);

		/**
		 * WebPage schema.
		 */
		$webpage_schema = wp_json_encode(
			array(
				'@context' => 'http://schema.org/',
				'@id'      => $root_domain . $path . '#webpage',
				'@type'    => 'WebPage',
				'url'      => $root_domain . $path,
				'name'     => $page_title,
			),
			JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
		);

		/**
		 * Breadcrumb schema.
		 */
		$crumb         = strtok( $path, '/' );
		$breadcrumbs[] = $crumb;

		while ( false !== $crumb ) {
			global $breadcrumbs;
			$breadcrumbs[] = $crumb;
			$crumb         = strtok( '/' );
		}

		$breadcrumb_schema = self::format_breadcrumbs( $root_domain, $breadcrumbs );

		/**
		 * Inject global schema.
		 */
		$global_schema = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM %1s WHERE schema_type = 'global';", $schema_table ),
			ARRAY_A
		);
		foreach ( $global_schema as $value ) {
			$schema = str_replace( '&apos;', "'", html_entity_decode( unserialize( $value['custom_schema'] ) ) );
			self::format_schema_html( $schema_script_html, $schema );
		}

		/**
		 * Inject post schema.
		 */
		if ( is_single() ) {
			$current_page_results = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM %1s WHERE post_id = %d AND schema_type = 'posts';", $schema_table, $page_id ),
				ARRAY_A
			);
			foreach ( $current_page_results as $value ) {
				$schema = str_replace( '&apos;', "'", html_entity_decode( unserialize( $value['custom_schema'] ) ) );
				self::format_schema_html( $schema_script_html, $schema );
			}
		}

		/**
		 * Inject page schema.
		 */
		if ( ! is_front_page() && is_home() ) {
			$blog_page_id         = get_option( 'page_for_posts' );
			$current_page_results = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM %1s WHERE post_id = %d AND schema_type = 'pages';", $schema_table, $blog_page_id ),
				ARRAY_A
			);
		} else {
			$current_page_results = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM %1s WHERE post_id = %d AND schema_type = 'pages';", $schema_table, $page_id ),
				ARRAY_A
			);
		}

		foreach ( $current_page_results as $value ) {
			$schema = str_replace( '&apos;', "'", html_entity_decode( unserialize( $value['custom_schema'] ) ) );
			self::format_schema_html( $schema_script_html, $schema );
		}

		/**
		 * Inject homepage schema.
		 */
		if ( '/' === $path ) {
			$home_schema = $wpdb->get_results(
				$wpdb->prepare( "SELECT custom_schema FROM %1s WHERE schema_type = 'homepage';", $schema_table ),
				ARRAY_A
			);
			foreach ( $home_schema as $value ) {
				$schema = str_replace( '&apos;', "'", html_entity_decode( unserialize( $value['custom_schema'] ) ) );
				self::format_schema_html( $schema_script_html, $schema );
			}
		}

		/**
		 * Inject default schema.
		 */
		$check_website_setting = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'website_schema';", $scsc_settings_table ),
			ARRAY_A
		);
		if ( '1' === $check_website_setting[0]['setting_value'] ) {
			self::format_schema_html( $schema_script_html, $website_schema );
		}

		$check_webpage_setting = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'webpage_schema';", $scsc_settings_table ),
			ARRAY_A
		);
		if ( '1' === $check_webpage_setting[0]['setting_value'] ) {
			self::format_schema_html( $schema_script_html, $webpage_schema );
		}

		$check_bc_setting = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'breadcrumb_schema';", $scsc_settings_table ),
			ARRAY_A
		);
		if ( '/' !== $path && '1' === $check_bc_setting[0]['setting_value'] ) {
			self::format_schema_html( $schema_script_html, $breadcrumb_schema );
		}
	}
}
