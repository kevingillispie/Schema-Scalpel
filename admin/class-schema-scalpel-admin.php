<?php
/**
 * Plugin admin page class.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin
 * @author     Kevin Gillispie
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Core plugin class.
 *
 * @since 1.0
 */
class Schema_Scalpel_Admin {

	/**
	 * Class variable.
	 *
	 * @var string $schema_scalpel
	 */
	private $schema_scalpel;

	/**
	 * Class variable.
	 *
	 * @var string $version
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
	 * Register stylesheets.
	 */
	public function enqueue_styles() {
		if ( stripos( \get_current_screen()->id, 'page_scsc' ) > -1 ) :
			\wp_enqueue_style( $this->schema_scalpel . '-bootstrap', \plugins_url( '/admin/css/bootstrap.min.css', SCHEMA_SCALPEL_PLUGIN ), array(), $this->version, 'all' );
			\wp_enqueue_style( $this->schema_scalpel . '-prism', \plugins_url( '/admin/css/prism.css', SCHEMA_SCALPEL_PLUGIN ), array(), $this->version, 'all' );
			\wp_enqueue_style( $this->schema_scalpel . '-admin', \plugins_url( '/admin/css/schema-scalpel-admin.css', SCHEMA_SCALPEL_PLUGIN ), array(), $this->version, 'all' );
		endif;
	}

	/**
	 * For possible future scripts.
	 */
	public function enqueue_scripts() {
		// placeholder.
	}

	/**
	 * Create and save blogposting schema.
	 *
	 * @param string $update_type Identifies which posts to update ('Missing', 'Replace', or a post ID like '691').
	 * @param string $post_type Schema type (e.g., 'BlogPosting').
	 * @param string $author_type Author schema type (e.g., 'Person').
	 * @param string $keywords Keyword behavior ('false', 'Placeholder', or 'Tags').
	 */
	public static function generate_blogposting_schema( $update_type, $post_type, $author_type, $keywords = 'false' ) {
		global $wpdb;
		$site_url  = \home_url(); // Get the site’s base URL dynamically.
		$author_id = \esc_url( trailingslashit( $site_url ) . '#' . strtolower( $author_type ) ); // Generic Person @id.

		// Determine which post IDs to process based on $update_type.
		$post_ids = array();
		if ( 'Missing' === $update_type || 'Replace' === $update_type ) {
			$args     = array(
				'numberposts' => -1,
				'fields'      => 'ids',
			);
			$post_ids = \get_posts( $args );
		} elseif ( is_numeric( $update_type ) && \get_post( $update_type ) ) {
			$post_ids = array( (int) $update_type ); // Single post ID.
		}

		// Get existing schema post IDs.
		$existing_post_schema_result = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT(post_id) FROM %1s WHERE schema_type='posts'", $wpdb->prefix . 'scsc_custom_schemas' ) );
		$existing_post_schema_ids    = array();
		foreach ( $existing_post_schema_result as $key => $value ) {
			$existing_post_schema_ids[] = $value->post_id;
		}

		foreach ( $post_ids as $p_id ) {
			// Skip posts with existing schema unless $update_type is 'Replace' or post ID is specified.
			if ( 'Missing' === $update_type && in_array( $p_id, $existing_post_schema_ids ) ) {
				continue;
			}

			$post                  = \get_post( $p_id, \ARRAY_A );
			$post_title            = $post['post_title']; // Escaped for JSON.
			$post_permalink        = \esc_url( \get_permalink( $p_id ) ); // Post URL.
			$post_thumbnail_url    = \get_the_post_thumbnail_url( $p_id, 'full' ); // Featured image URL.
			$post_thumbnail_schema = ( ! empty( $post_thumbnail_url ) ) ? '"image":"' . \esc_url( $post_thumbnail_url ) . '",' : '';
			$post_published_time   = \get_post_time( 'Y-m-d\TH:i:s', true, $p_id, true ) . '+00:00';
			$post_modified_time    = \get_post_modified_time( 'Y-m-d\TH:i:s', true, $p_id, true ) . '+00:00';
			$post_excerpt          = \wp_trim_words( \get_the_excerpt( $p_id ), 30, '...' ); // Excerpt as description.
			$author_name           = \get_the_author_meta( 'display_name', $post['post_author'] ); // Author display name.
			$author_url            = \get_the_author_meta( 'user_url', $post['post_author'] );
			if ( empty( $author_url ) ) {
				$author_url = $site_url; // Author URL or site URL fallback.
			}
			$post_keywords_schema = '';

			// Handle keywords based on $keywords parameter.
			if ( 'Placeholder' === $keywords ) {
				$post_keywords_schema = '"keywords":[],';
			} elseif ( 'Tags' === $keywords ) {
				$tags = \wp_get_post_tags( $p_id, array( 'fields' => 'names' ) );
				if ( ! empty( $tags ) ) {
					$post_keywords_schema = '"keywords":[' . implode(
						',',
						array_map(
							function ( $tag ) {
								return '"' . $tag . '"';
							},
							$tags
						)
					) . '],';
				}
			}

			$blog_posting = <<<BLOGPOSTING
{&quot;@context&quot;:&quot;https://schema.org&quot;,&quot;@type&quot;:&quot;{$post_type}&quot;,&quot;@id&quot;:&quot;{$post_permalink}#blogposting&quot;,&quot;headline&quot;:&quot;{$post_title}&quot;,&quot;url&quot;:&quot;{$post_permalink}&quot;,{$post_thumbnail_schema}&quot;datePublished&quot;:&quot;{$post_published_time}&quot;,&quot;dateModified&quot;:&quot;{$post_modified_time}&quot;,&quot;author&quot;:[{&quot;@type&quot;:&quot;{$author_type}&quot;,&quot;name&quot;:&quot;{$author_name}&quot;,&quot;url&quot;:&quot;{$author_url}&quot;}],&quot;publisher&quot;:[{&quot;@type&quot;:&quot;{$author_type}&quot;,&quot;name&quot;:&quot;{$author_name}&quot;,&quot;url&quot;:&quot;{$author_url}&quot;}],&quot;description&quot;:&quot;{$post_excerpt}&quot;,{$post_keywords_schema}&quot;mainEntityOfPage&quot;:{&quot;@id&quot;:&quot;{$post_permalink}&quot;}}
BLOGPOSTING;

			// If replacing or updating existing schema, delete old schema first.
			if ( 'Replace' === $update_type || ( is_numeric( $update_type ) && in_array( $p_id, $existing_post_schema_ids ) ) ) {
				$wpdb->delete(
					$wpdb->prefix . 'scsc_custom_schemas',
					array(
						'post_id'     => $p_id,
						'schema_type' => 'posts',
					)
				);
			}

			// Insert new schema.
			$wpdb->insert(
				$wpdb->prefix . 'scsc_custom_schemas',
				array(
					'custom_schema' => serialize( $blog_posting ),
					'schema_type'   => 'posts',
					'post_id'       => $p_id,
				)
			);
		}
	}
}
