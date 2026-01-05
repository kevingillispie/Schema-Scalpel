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
<<<<<<< Updated upstream
 * Core plugin class.
=======
 * Admin functionality for Schema Scalpel.
 *
 * Handles registration of the metabox, AJAX handlers for creating, saving, and deleting
 * custom schemas, and provides a static method for bulk-generating BlogPosting schema.
>>>>>>> Stashed changes
 *
 * @since 1.0
 */
class SCSC_Admin {

	/**
<<<<<<< Updated upstream
	 * Class variable.
	 *
	 * @var string $schema_scalpel
=======
	 * The plugin slug/name.
	 *
	 * @var string
>>>>>>> Stashed changes
	 */
	private $schema_scalpel;

	/**
<<<<<<< Updated upstream
	 * Class variable.
	 *
	 * @var string $version
=======
	 * The plugin version number.
	 *
	 * @var string
>>>>>>> Stashed changes
	 */
	private $version;

	/**
<<<<<<< Updated upstream
	 * Class constructor.
	 *
	 * @param string $schema_scalpel Plugin name.
	 * @param string $version Plugin version.
=======
	 * Constructor.
	 *
	 * Sets plugin properties and hooks AJAX actions and metabox registration.
	 *
	 * @since 1.0
	 *
	 * @param string $schema_scalpel Plugin name/slug.
	 * @param string $version        Current plugin version.
>>>>>>> Stashed changes
	 */
	public function __construct( $schema_scalpel, $version ) {
		$this->schema_scalpel = $schema_scalpel;
		$this->version        = $version;
<<<<<<< Updated upstream
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/scsc-metabox.php';
		// Register AJAX handlers for the metabox.
		add_action( 'wp_ajax_scsc_save_metabox_schema', array( $this, 'handle_save_schema' ) );
		add_action( 'wp_ajax_scsc_create_metabox_schema', array( $this, 'handle_create_schema' ) );
		add_action( 'wp_ajax_scsc_delete_metabox_schema', array( $this, 'handle_delete_schema' ) );
=======

		// Load metabox rendering functions.
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/scsc-metabox.php';

		// Register AJAX handlers for metabox actions.
		add_action( 'wp_ajax_scsc_save_metabox_schema', array( $this, 'handle_save_schema' ) );
		add_action( 'wp_ajax_scsc_create_metabox_schema', array( $this, 'handle_create_schema' ) );
		add_action( 'wp_ajax_scsc_delete_metabox_schema', array( $this, 'handle_delete_schema' ) );

		// Register the custom metabox on post and page screens.
>>>>>>> Stashed changes
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
	}

	/**
<<<<<<< Updated upstream
	 * Register stylesheets.
	 */
	public function enqueue_styles() {
		if ( stripos( get_current_screen()->id, 'page_scsc' ) > -1 ) :
			wp_enqueue_style( $this->schema_scalpel . '-bootstrap', plugins_url( '/admin/css/bootstrap.min.css', SCHEMA_SCALPEL_PLUGIN ), array(), $this->version, 'all' );
			wp_enqueue_style( $this->schema_scalpel . '-prism', plugins_url( '/admin/css/prism.css', SCHEMA_SCALPEL_PLUGIN ), array(), $this->version, 'all' );
			wp_enqueue_style( $this->schema_scalpel . '-admin', plugins_url( '/admin/css/scsc-admin.css', SCHEMA_SCALPEL_PLUGIN ), array(), $this->version, 'all' );
		endif;
	}

	/**
	 * For possible future scripts.
	 */
	public function enqueue_scripts() {
		// Placeholder.
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
		$site_url  = home_url(); // Get the site’s base URL dynamically.
		$author_id = esc_url( trailingslashit( $site_url ) . '#' . strtolower( $author_type ) ); // Generic Person @id.

		// Determine which post IDs to process based on $update_type.
		$post_ids = array();
		if ( 'Missing' === $update_type || 'Replace' === $update_type ) {
			$args     = array(
				'numberposts' => -1,
				'fields'      => 'ids',
			);
			$post_ids = get_posts( $args );
		} elseif ( is_numeric( $update_type ) && get_post( $update_type ) ) {
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
=======
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
>>>>>>> Stashed changes
				continue;
			}

			$post                  = get_post( $p_id, ARRAY_A );
<<<<<<< Updated upstream
			$post_title            = $post['post_title']; // Escaped for JSON.
			$post_permalink        = esc_url( get_permalink( $p_id ) ); // Post URL.
			$post_thumbnail_url    = get_the_post_thumbnail_url( $p_id, 'full' ); // Featured image URL.
			$post_thumbnail_schema = ( ! empty( $post_thumbnail_url ) ) ? '"image":"' . esc_url( $post_thumbnail_url ) . '",' : '';
			$post_published_time   = get_post_time( 'Y-m-d\TH:i:s', true, $p_id, true ) . '+00:00';
			$post_modified_time    = get_post_modified_time( 'Y-m-d\TH:i:s', true, $p_id, true ) . '+00:00';
			$post_excerpt          = wp_trim_words( get_the_excerpt( $p_id ), 30, '...' ); // Excerpt as description.
			$author_name           = get_the_author_meta( 'display_name', $post['post_author'] ); // Author display name.
			$author_url            = get_the_author_meta( 'user_url', $post['post_author'] );
			if ( empty( $author_url ) ) {
				$author_url = $site_url; // Author URL or site URL fallback.
			}
			$post_keywords_schema = '';

			// Handle keywords based on $keywords parameter.
=======
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
>>>>>>> Stashed changes
			if ( 'Placeholder' === $keywords ) {
				$post_keywords_schema = '"keywords":[],';
			} elseif ( 'Tags' === $keywords ) {
				$tags = wp_get_post_tags( $p_id, array( 'fields' => 'names' ) );
				if ( ! empty( $tags ) ) {
<<<<<<< Updated upstream
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

=======
					$escaped_tags         = array_map( 'esc_html', $tags );
					$post_keywords_schema = '"keywords":["' . implode( '","', $escaped_tags ) . '"],';
				}
			}

			// Build the JSON-LD string (HTML-entity encoded for safe storage).
>>>>>>> Stashed changes
			$blog_posting = <<<BLOGPOSTING
{&quot;@context&quot;:&quot;https://schema.org&quot;,&quot;@type&quot;:&quot;{$post_type}&quot;,&quot;@id&quot;:&quot;{$post_permalink}#blogposting&quot;,&quot;headline&quot;:&quot;{$post_title}&quot;,&quot;url&quot;:&quot;{$post_permalink}&quot;,{$post_thumbnail_schema}&quot;datePublished&quot;:&quot;{$post_published_time}&quot;,&quot;dateModified&quot;:&quot;{$post_modified_time}&quot;,&quot;author&quot;:[{&quot;@type&quot;:&quot;{$author_type}&quot;,&quot;name&quot;:&quot;{$author_name}&quot;,&quot;url&quot;:&quot;{$author_url}&quot;}],&quot;publisher&quot;:[{&quot;@type&quot;:&quot;{$author_type}&quot;,&quot;name&quot;:&quot;{$author_name}&quot;,&quot;url&quot;:&quot;{$author_url}&quot;}],&quot;description&quot;:&quot;{$post_excerpt}&quot;,{$post_keywords_schema}&quot;mainEntityOfPage&quot;:{&quot;@id&quot;:&quot;{$post_permalink}&quot;}}
BLOGPOSTING;

<<<<<<< Updated upstream
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
=======
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
>>>>>>> Stashed changes
				array(
					'custom_schema' => serialize( $blog_posting ),
					'schema_type'   => 'posts',
					'post_id'       => $p_id,
<<<<<<< Updated upstream
				)
=======
				),
				array( '%s', '%s', '%d' )
>>>>>>> Stashed changes
			);
		}
	}

	/**
<<<<<<< Updated upstream
	 * Register the Schema Scalpel metabox on post and page edit screens.
=======
	 * Register the Schema Scalpel metabox.
	 *
	 * Adds the custom JSON-LD editor metabox to post and page edit screens.
	 *
	 * @since 1.7
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
=======
	/**
	 * AJAX handler: Save an existing schema entry.
	 *
	 * @since 1.7
	 */
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
		// The incoming $schema_json already has &quot; and &apos; from JS
		// We just need to serialize it
		$to_store = serialize( $schema_json );

		// Optional: double-check it's valid after reversing entities
=======
		// Validate JSON (after reversing entity encoding used during editing).
>>>>>>> Stashed changes
		$test = str_replace( array( '&quot;', '&apos;' ), array( '"', "'" ), $schema_json );
		if ( json_decode( $test ) === null && json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( 'Invalid JSON structure.' );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'scsc_custom_schemas';

		$updated = $wpdb->update(
			$table,
			array(
<<<<<<< Updated upstream
				'custom_schema' => $to_store,
=======
				'custom_schema' => serialize( $schema_json ),
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
=======
	/**
	 * AJAX handler: Create a new schema entry.
	 *
	 * @since 1.7
	 */
>>>>>>> Stashed changes
	public function handle_create_schema() {
		check_ajax_referer( 'scsc_metabox_save', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'Insufficient permissions.' );
		}

		$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$schema_json = isset( $_POST['schema_json'] ) ? wp_unslash( $_POST['schema_json'] ) : '';
		$is_global   = isset( $_POST['is_global'] ) ? (int) $_POST['is_global'] : 0;
<<<<<<< Updated upstream
		$post_type   = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'posts';
=======
		$post_type   = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : 'posts';
>>>>>>> Stashed changes

		if ( empty( $schema_json ) ) {
			wp_send_json_error( 'Schema cannot be empty.' );
		}

<<<<<<< Updated upstream
=======
		// Validate JSON.
>>>>>>> Stashed changes
		$test = str_replace( array( '&quot;', '&apos;' ), array( '"', "'" ), $schema_json );
		if ( json_decode( $test ) === null && json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( 'Invalid JSON.' );
		}

<<<<<<< Updated upstream
		$to_store = serialize( $schema_json );

		global $wpdb;
		$table = $wpdb->prefix . 'scsc_custom_schemas';

		// Use 'global' or the actual post type (post/page)
=======
		global $wpdb;
		$table = $wpdb->prefix . 'scsc_custom_schemas';

>>>>>>> Stashed changes
		$schema_type = $is_global ? 'global' : $post_type;

		$inserted = $wpdb->insert(
			$table,
			array(
<<<<<<< Updated upstream
				'custom_schema' => $to_store,
=======
				'custom_schema' => serialize( $schema_json ),
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
	 * Handle deleting a schema via AJAX.
=======
	 * AJAX handler: Delete a schema entry.
	 *
	 * @since 1.7
>>>>>>> Stashed changes
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
