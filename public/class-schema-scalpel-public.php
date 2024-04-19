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
	 * Class variable.
	 *
	 * @var string $schema_scalpel Plugin name.
	 */
	private $schema_scalpel;

	/**
	 * Class variable.
	 *
	 * @var string $schema_scalpel Plugin version.
	 */
	private $version;

	/**
	 * Class constructor.
	 *
	 * @param string $schema_scalpel Plugin name.
	 * @param string $version Plugin version.
	 */
	public function __construct( $schema_scalpel, $version ) {
		$this->schema_scalpel = $schema_scalpel;
		$this->version        = $version;
	}

	/**
	 * CSS stylesheet registration.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->schema_scalpel, SCHEMA_SCALPEL_DIRECTORY . '/public/css/schema-scalpel-public.css', array(), $this->version, 'all' );
	}

	/**
	 * JS registration.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->schema_scalpel, SCHEMA_SCALPEL_DIRECTORY . '/public/js/schema-scalpel-public.js', array(), $this->version, false );
	}

	/**
	 * Return page title.
	 *
	 * @param string $url Page URL.
	 */
	private function get_page_title( $url ) {
		return get_the_title( url_to_postid( $url ) );
	}

	/**
	 * Format and output breadcrumb schema.
	 *
	 * @param string $root_domain Site address.
	 * @param array  $breadcrumbs Breadcrumb data.
	 */
	private function format_breadcrumbs( string $root_domain, array $breadcrumbs ) {
		$crumb_list = '';
		$crumb_path = '';
		if ( false !== $breadcrumbs[0] ) :
			$i      = 0;
			$length = count( $breadcrumbs );
			while ( $i < $length ) :
				$pos           = $i + 1;
				$crumb_path   .= '/' . $breadcrumbs[ $i ];
				$bc_page_title = self::get_page_title( $root_domain . $crumb_path ) ? self::get_page_title( $root_domain . $crumb_path ) : wp_get_document_title();

				$crumb_list .= <<<CRUMBS
{
    "@type": "ListItem",
    "position": "{$pos}",
    "item": {
        "@type": "WebPage",
        "@id": "{$root_domain}{$crumb_path}",
        "name": "{$bc_page_title}"
    }
}
CRUMBS;
				( count( $breadcrumbs ) === $i + 1 ) ? '' : $crumb_list .= ',';
				++$i;
			endwhile;
		endif;
		return $crumb_list;
	}

	/**
	 * Format and output schema script tags.
	 *
	 * @param string $html HTML.
	 * @param string $s Schema.
	 */
	private function format_schema_html( $html, $s ) {
		printf( '%s%s</script>', sanitize_post( $html, 'js' ), sanitize_post( $s, 'js' ) );
	}

	/**
	 * Format and output schema.
	 */
	public function enqueue_inline_scripts() {
		/**
		 * IGNORE SCHEMA IF POST/PAGE OR ADMIN PAGE
		 */
		global $post;
		if ( ! $post || is_admin() || is_customize_preview() ) :
			return;
		endif;
		/**
		 *
		 */

		global $wpdb;
		$current_page_results = array();
		$site_title           = get_bloginfo( 'name' );
		$page_id              = get_the_ID();
		/**
		 * CHECK DATABASE EXCLUSIONS
		 */
		$current_excluded_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'exclude';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
		foreach ( $current_excluded_results as $key => $value ) :
			if ( in_array( $page_id, $current_excluded_results[ $key ] ) ) :
				return;
			endif;
		endforeach;

		$root_domain        = get_site_url();
		$path               = sanitize_url( $_SERVER['REQUEST_URI'] );
		$wp_title           = wp_title( '|', false, 'right' );
		$page_title         = ( $wp_title ) ? html_entity_decode( $wp_title ) : ( ( get_the_title() ) ? html_entity_decode( get_the_title() ) : ( ( the_title_attribute() ) ? html_entity_decode( the_title_attribute() ) : $site_title ) );
		$schema_script_html = '<script class="schema-scalpel-seo" type="application/ld+json">';
		$search_query_param = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key='search_param';", $wpdb->prefix . 'scsc_settings' ) );
		$search_key         = $search_query_param[0]->setting_value;

		/**
		 * WEBSITE
		 */
		$website_schema = <<<WEBSITE
{
    "@context": "http://schema.org/",
    "@id": "{$root_domain}/#website",
    "@type": "WebSite",
    "url": "{$root_domain}/",
    "name": "{$site_title}",
    "potentialAction": [
        {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{$root_domain}/?{$search_key}={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    ]
}
WEBSITE;

		/**
		 * WEBPAGE
		 */
		$webpage_schema = <<<WEBPAGE
{
    "@context": "http://schema.org/",
    "@id": "{$root_domain}{$path}#webpage",
    "@type": "WebPage",
    "url": "{$root_domain}{$path}",
    "name": "{$page_title}"
}
WEBPAGE;

		/**
		 * BREADCRUMBS
		 */
		$crumb         = strtok( $path, '/' );
		$breadcrumbs[] = $crumb;

		while ( false !== $crumb ) :
			global $breadcrumbs;
			$breadcrumbs[] = $crumb;
			$crumb         = strtok( '/' );
		endwhile;

		$list_elements = self::format_breadcrumbs( $root_domain, $breadcrumbs );

		$breadcrumb_schema = <<<BREAD
{
    "@context": "https://schema.org/",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {$list_elements}
    ]
}
BREAD;

		/**
		 * INJECT GLOBAL SCHEMA
		 */
		$global_schema = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE schema_type = 'global';", $wpdb->prefix . 'scsc_custom_schemas' ), ARRAY_A );
		foreach ( $global_schema as $key => $value ) :
			$schema = str_replace( '&apos;', "'", html_entity_decode( unserialize( $value['custom_schema'] ) ) );
			self::format_schema_html( $schema_script_html, $schema );
		endforeach;

		/**
		 * INJECT POST SCHEMA
		 */
		if ( is_single() ) :
			$current_page_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE post_id = '$page_id' AND schema_type = 'posts';", $wpdb->prefix . 'scsc_custom_schemas' ), ARRAY_A );
			foreach ( $current_page_results as $key => $value ) :
				$schema = str_replace( '&apos;', "'", html_entity_decode( unserialize( $value['custom_schema'] ) ) );
				self::format_schema_html( $schema_script_html, $schema );
				error_log( print_r( $page_id, true ) );
			endforeach;
		endif;

		/**
		 * INJECT PAGE SCHEMA
		 */
		if ( ! is_front_page() && is_home() ) :
			$blog_page_id         = get_option( 'page_for_posts' );
			$current_page_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE post_id = %s AND schema_type = 'pages';", $wpdb->prefix . 'scsc_custom_schemas', $blog_page_id ), ARRAY_A );
		else :
			$current_page_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE post_id = %s AND schema_type = 'pages';", $wpdb->prefix . 'scsc_custom_schemas', $page_id ), ARRAY_A );
		endif;

		foreach ( $current_page_results as $key => $value ) :
			$schema = str_replace( '&apos;', "'", html_entity_decode( unserialize( $value['custom_schema'] ) ) );
			self::format_schema_html( $schema_script_html, $schema );
		endforeach;

		/**
		 * INJECT HOMEPAGE SCHEMA
		 */
		if ( '/' === $path ) :
			$home_schema = $wpdb->get_results( $wpdb->prepare( "SELECT custom_schema FROM %1s WHERE schema_type = 'homepage';", $wpdb->prefix . 'scsc_custom_schemas' ), ARRAY_A );
			foreach ( $home_schema as $key => $value ) :
				$schema = str_replace( '&apos;', "\'", html_entity_decode( unserialize( $value['custom_schema'] ) ) );
				self::format_schema_html( $schema_script_html, $schema );
			endforeach;
		endif;

		/**
		 * INJECT DEFAULT SCHEMA
		 */
		$check_website_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'website_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
		if ( '1' === $check_website_setting[0]['setting_value'] ) :
			self::format_schema_html( $schema_script_html, $website_schema );
		endif;

		$check_webpage_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'webpage_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
		if ( '1' === $check_webpage_setting[0]['setting_value'] ) :
			self::format_schema_html( $schema_script_html, $webpage_schema );
		endif;

		$check_bc_setting = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE setting_key = 'breadcrumb_schema';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
		if ( '/' !== $path && '1' === $check_bc_setting[0]['setting_value'] ) :
			self::format_schema_html( $schema_script_html, $breadcrumb_schema );
		endif;
	}
}
