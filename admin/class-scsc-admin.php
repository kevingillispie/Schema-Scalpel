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
 * Admin functionality for Schema Scalpel.
 *
 * Handles registration of the metabox, AJAX handlers for creating, saving, and deleting
 * custom schemas, and provides a static method for bulk-generating BlogPosting schema.
 *
 * @since 1.0
 */
class SCSC_Admin {

	/**
	 * The plugin slug/name.
	 *
	 * @var string
	 */
	private $schema_scalpel;

	/**
	 * The plugin version number.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * Sets plugin properties and hooks AJAX actions and metabox registration.
	 *
	 * @since 1.0
	 *
	 * @param string $schema_scalpel Plugin name/slug.
	 * @param string $version        Current plugin version.
	 */
	public function __construct( $schema_scalpel, $version ) {
		$this->schema_scalpel = $schema_scalpel;
		$this->version        = $version;

		// Load metabox rendering functions.
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/scsc-metabox.php';

		// Register AJAX handlers for metabox actions.
		add_action( 'wp_ajax_scsc_save_metabox_schema', array( $this, 'handle_save_schema' ) );
		add_action( 'wp_ajax_scsc_create_metabox_schema', array( $this, 'handle_create_schema' ) );
		add_action( 'wp_ajax_scsc_delete_metabox_schema', array( $this, 'handle_delete_schema' ) );

		// Register the custom metabox on post and page screens.
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
	}

	/**
	 * Enqueue admin styles.
	 *
	 * Loads Bootstrap, Prism, and custom admin CSS only on Schema Scalpel admin pages.
	 *
	 * @since 1.0
	 */
	public function enqueue_styles() {
		// Only load styles on Schema Scalpel admin screens (e.g., settings page).
		$current_screen = get_current_screen();

		if ( $current_screen && false !== stripos( $current_screen->id, 'page_scsc' ) ) {
			wp_enqueue_style(
				$this->schema_scalpel . '-bootstrap',
				plugins_url( '/admin/css/bootstrap.min.css', SCHEMA_SCALPEL_PLUGIN ),
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				$this->schema_scalpel . '-prism',
				plugins_url( '/admin/css/prism.css', SCHEMA_SCALPEL_PLUGIN ),
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				$this->schema_scalpel . '-admin',
				plugins_url( '/admin/css/scsc-admin.css', SCHEMA_SCALPEL_PLUGIN ),
				array(),
				$this->version,
				'all'
			);
		}
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * Placeholder for future JavaScript needs.
	 *
	 * @since 1.0
	 */
	public function enqueue_scripts() {
		// Currently unused – kept for future expansion.
	}

	/**
	 * Generate and insert BlogPosting schema for posts.
	 *
	 * Can create schema for all posts, only posts missing schema, replace existing schema,
	 * or target a single post ID.
	 *
	 * @since 1.0
	 *
	 * @param string $update_type Update mode: 'Missing', 'Replace', or a specific post ID (as string).
	 * @param string $post_type   Schema @type for the post (e.g., 'BlogPosting').
	 * @param string $author_type Author @type (e.g., 'Person' or 'Organization').
	 * @param string $keywords    Keyword handling: 'false' (omit), 'Placeholder' (empty array), or 'Tags' (use post tags).
	 */
	public static function generate_blogposting_schema( $update_type, $post_type, $author_type, $keywords = 'false' ) {
		global $wpdb;

		$site_url  = home_url(); // Base site URL.
		$author_id = esc_url( trailingslashit( $site_url ) . '#' . strtolower( $author_type ) );

		// Determine which posts to process.
		$post_ids = array();

		if ( 'Missing' === $update_type || 'Replace' === $update_type ) {
			$post_ids = get_posts(
				array(
					'numberposts' => -1,
					'fields'      => 'ids',
					'post_type'   => 'post',
				)
			);
		} elseif ( is_numeric( $update_type ) && get_post( $update_type ) ) {
			$post_ids = array( (int) $update_type );
		}

		if ( empty( $post_ids ) ) {
			return;
		}

		// Retrieve existing post schema IDs for skip/replace logic.
		$table                    = $wpdb->prefix . 'scsc_custom_schemas';
		$existing_results         = $wpdb->get_results(
			$wpdb->prepare( "SELECT DISTINCT post_id FROM %i WHERE schema_type = 'posts'", $table ),
			ARRAY_A
		);
		$existing_post_schema_ids = wp_list_pluck( $existing_results, 'post_id' );

		foreach ( $post_ids as $p_id ) {
			// Skip posts that already have schema when only adding missing ones.
			if ( 'Missing' === $update_type && in_array( $p_id, $existing_post_schema_ids, true ) ) {
				continue;
			}

			$post                  = get_post( $p_id, ARRAY_A );
			$post_title            = esc_html( $post['post_title'] );
			$post_permalink        = esc_url( get_permalink( $p_id ) );
			$post_thumbnail_url    = get_the_post_thumbnail_url( $p_id, 'full' );
			$post_thumbnail_schema = ! empty( $post_thumbnail_url ) ? '"image":"' . esc_url( $post_thumbnail_url ) . '",' : '';
			$post_published_time   = get_post_time( 'Y-m-d\TH:i:sP', true, $p_id );
			$post_modified_time    = get_post_modified_time( 'Y-m-d\TH:i:sP', true, $p_id );
			$post_excerpt          = esc_html( wp_trim_words( get_the_excerpt( $p_id ), 30, '...' ) );
			$author_name           = esc_html( get_the_author_meta( 'display_name', $post['post_author'] ) );
			$author_url            = esc_url( get_the_author_meta( 'user_url', $post['post_author'] ) );
			if ( empty( $author_url ) ) {
				$author_url = esc_url( $site_url );
			}

			// Keywords handling.
			$post_keywords_schema = '';
			if ( 'Placeholder' === $keywords ) {
				$post_keywords_schema = '"keywords":[],';
			} elseif ( 'Tags' === $keywords ) {
				$tags = wp_get_post_tags( $p_id, array( 'fields' => 'names' ) );
				if ( ! empty( $tags ) ) {
					$escaped_tags         = array_map( 'esc_html', $tags );
					$post_keywords_schema = '"keywords":["' . implode( '","', $escaped_tags ) . '"],';
				}
			}

			// Build the JSON-LD string (HTML-entity encoded for safe storage).
			$blog_posting = <<<BLOGPOSTING
{&quot;@context&quot;:&quot;https://schema.org&quot;,&quot;@type&quot;:&quot;{$post_type}&quot;,&quot;@id&quot;:&quot;{$post_permalink}#blogposting&quot;,&quot;headline&quot;:&quot;{$post_title}&quot;,&quot;url&quot;:&quot;{$post_permalink}&quot;,{$post_thumbnail_schema}&quot;datePublished&quot;:&quot;{$post_published_time}&quot;,&quot;dateModified&quot;:&quot;{$post_modified_time}&quot;,&quot;author&quot;:[{&quot;@type&quot;:&quot;{$author_type}&quot;,&quot;name&quot;:&quot;{$author_name}&quot;,&quot;url&quot;:&quot;{$author_url}&quot;}],&quot;publisher&quot;:[{&quot;@type&quot;:&quot;{$author_type}&quot;,&quot;name&quot;:&quot;{$author_name}&quot;,&quot;url&quot;:&quot;{$author_url}&quot;}],&quot;description&quot;:&quot;{$post_excerpt}&quot;,{$post_keywords_schema}&quot;mainEntityOfPage&quot;:{&quot;@id&quot;:&quot;{$post_permalink}&quot;}}
BLOGPOSTING;

			// Delete existing schema if replacing or updating a single post that already has one.
			if ( 'Replace' === $update_type || ( is_numeric( $update_type ) && in_array( $p_id, $existing_post_schema_ids, true ) ) ) {
				$wpdb->delete(
					$table,
					array(
						'post_id'     => $p_id,
						'schema_type' => 'posts',
					),
					array( '%d', '%s' )
				);
			}

			// Insert the new schema.
			$wpdb->insert(
				$table,
				array(
					'custom_schema' => serialize( $blog_posting ),
					'schema_type'   => 'posts',
					'post_id'       => $p_id,
				),
				array( '%s', '%s', '%d' )
			);
		}
	}

	/**
	 * Register the Schema Scalpel metabox.
	 *
	 * Adds the custom JSON-LD editor metabox to post and page edit screens.
	 *
	 * @since 1.7
	 */
	public function register_metabox() {
		add_meta_box(
			'scsc_schema_preview',
			__( 'Schema Scalpel', 'schema-scalpel' ),
			function ( $post ) {
				scsc_render_metabox_preview( $post );
			},
			array( 'post', 'page' ),
			'normal',
			'high'
		);
	}

	/**
	 * AJAX handler: Save an existing schema entry.
	 *
	 * @since 1.7
	 */
	public function handle_save_schema() {
		check_ajax_referer( 'scsc_metabox_save', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		$schema_id   = isset( $_POST['schema_id'] ) ? absint( $_POST['schema_id'] ) : 0;
		$schema_json = isset( $_POST['schema_json'] ) ? wp_unslash( $_POST['schema_json'] ) : '';

		if ( ! $schema_id || empty( $schema_json ) ) {
			wp_send_json_error( 'Invalid data.' );
		}

		// Validate JSON (after reversing entity encoding used during editing).
		$test = str_replace( array( '&quot;', '&apos;' ), array( '"', "'" ), $schema_json );
		if ( json_decode( $test ) === null && json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( 'Invalid JSON structure.' );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'scsc_custom_schemas';

		$updated = $wpdb->update(
			$table,
			array(
				'custom_schema' => serialize( $schema_json ),
				'updated'       => current_time( 'mysql' ),
			),
			array( 'id' => $schema_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		if ( false !== $updated ) {
			wp_send_json_success();
		} else {
			wp_send_json_error( 'Save failed.' );
		}
	}

	/**
	 * AJAX handler: Create a new schema entry.
	 *
	 * @since 1.7
	 */
	public function handle_create_schema() {
		check_ajax_referer( 'scsc_metabox_save', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$schema_json = isset( $_POST['schema_json'] ) ? wp_unslash( $_POST['schema_json'] ) : '';
		$is_global   = isset( $_POST['is_global'] ) ? (int) $_POST['is_global'] : 0;
		$post_type   = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : 'posts';

		if ( empty( $schema_json ) ) {
			wp_send_json_error( 'Schema cannot be empty.' );
		}

		// Validate JSON.
		$test = str_replace( array( '&quot;', '&apos;' ), array( '"', "'" ), $schema_json );
		if ( json_decode( $test ) === null && json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( 'Invalid JSON.' );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'scsc_custom_schemas';

		$schema_type = $is_global ? 'global' : $post_type;

		$inserted = $wpdb->insert(
			$table,
			array(
				'custom_schema' => serialize( $schema_json ),
				'schema_type'   => $schema_type,
				'post_id'       => $is_global ? 0 : $post_id,
				'created'       => current_time( 'mysql' ),
				'updated'       => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%d', '%s', '%s' )
		);

		if ( $inserted ) {
			wp_send_json_success();
		} else {
			wp_send_json_error( 'Creation failed.' );
		}
	}

	/**
	 * AJAX handler: Delete a schema entry.
	 *
	 * @since 1.7
	 */
	public function handle_delete_schema() {
		check_ajax_referer( 'scsc_metabox_save', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		$schema_id = isset( $_POST['schema_id'] ) ? absint( $_POST['schema_id'] ) : 0;

		if ( ! $schema_id ) {
			wp_send_json_error( 'Invalid ID.' );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'scsc_custom_schemas';

		$deleted = $wpdb->delete(
			$table,
			array( 'id' => $schema_id ),
			array( '%d' )
		);

		if ( $deleted ) {
			wp_send_json_success();
		} else {
			wp_send_json_error( 'Delete failed.' );
		}
	}
}
