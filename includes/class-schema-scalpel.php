<?php
/**
 * Plugin core initialization class.
 *
 * @link       https://schemascalpel.com
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init class.
 *
 * @since 1.0
 */
class Schema_Scalpel {

	/**
	 * Variable initialized as Schema_Scalpel_Loader class.
	 *
	 * @access   protected
	 * @var      Schema_Scalpel_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Variable initialized with plugin name.
	 *
	 * @access   protected
	 * @var      string    $schema_scalpel    The string used to uniquely identify this plugin.
	 */
	protected $schema_scalpel;

	/**
	 * Variable initialized with plugin version.
	 *
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Constructor to initialize the plugin.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->version        = SCHEMA_SCALPEL_VERSION;
		$this->schema_scalpel = 'schema-scalpel';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->register();
	}

	/**
	 * Load plugin dependencies.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function load_dependencies() {
		// Include the loader class.
		require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-loader.php';

		// Include the admin class.
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/class-schema-scalpel-admin.php';

		// Include the public class.
		require_once SCHEMA_SCALPEL_DIRECTORY . '/public/class-schema-scalpel-public.php';

		$this->loader = new Schema_Scalpel_Loader();
	}

	/**
	 * Prepare any styles and scripts.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Schema_Scalpel_Admin( $this->get_schema_scalpel(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'plugin_action_links_' . \plugin_basename( SCHEMA_SCALPEL_PLUGIN ), $this, 'add_plugin_action_links' );
	}

	/**
	 * Prepare any public scripts.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function define_public_hooks() {
		$plugin_public = new Schema_Scalpel_Public( $this->get_schema_scalpel(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_inline_scripts' );
	}

	/**
	 * Run the plugin loader.
	 *
	 * @since 1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Initialize class variable with plugin name.
	 *
	 * @since 1.0
	 * @return string    The name of the plugin.
	 */
	public function get_schema_scalpel() {
		return $this->schema_scalpel;
	}

	/**
	 * Initializes class variable with loader class.
	 *
	 * @since 1.0
	 * @return Schema_Scalpel_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Initialize class variable with plugin version number.
	 *
	 * @since 1.0
	 * @return string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register any admin page styles/scripts.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function register() {
		\add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		\add_action(
			'admin_head',
			function () {
				echo <<<STYLE
            <style>img[src*="menu_icon.svg"]{padding: 0 !important;}</style>
            STYLE;
			}
		);
	}

	/**
	 * Load plugin's admin page.
	 *
	 * @since 1.0
	 */
	public function admin_index_page() {
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-admin-main.php';
	}

	/**
	 * Load plugin's settings page.
	 *
	 * @since 1.0
	 */
	public function user_settings_page() {
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-user-settings.php';
	}

	/**
	 * Load plugin's export page.
	 *
	 * @since 1.0
	 */
	public function user_export_page() {
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-user-export.php';
	}

	/**
	 * Load plugin's admin menu.
	 *
	 * @since 1.0
	 */
	public function add_admin_pages() {
		$default_page_title = __( SCHEMA_SCALPEL_TEXT_DOMAIN, SCHEMA_SCALPEL_TEXT_DOMAIN );
		\add_action(
			'admin_head',
			function () {
				echo '<style class="scsc-admin">.toplevel_page_scsc img[src*="menu_icon.svg"] {margin-top:6px}</style>';
			}
		);

		\add_menu_page(
			__( 'Schema Scalpel Plugin', SCHEMA_SCALPEL_TEXT_DOMAIN ),
			'Schema Scalpel',
			'manage_options',
			SCHEMA_SCALPEL_TEXT_DOMAIN,
			array( $this, 'admin_index_page' ),
			plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/menu_icon.svg',
			100
		);

		\add_submenu_page(
			$default_page_title,
			__( 'Schema Scalpel | Add New / Edit', SCHEMA_SCALPEL_TEXT_DOMAIN ),
			__( 'Add New / Edit', SCHEMA_SCALPEL_TEXT_DOMAIN ),
			'manage_options',
			SCHEMA_SCALPEL_TEXT_DOMAIN,
			array( $this, 'admin_index_page' )
		);

		\add_submenu_page(
			$default_page_title,
			__( 'Schema Scalpel | Settings', SCHEMA_SCALPEL_TEXT_DOMAIN ),
			__( 'Settings', SCHEMA_SCALPEL_TEXT_DOMAIN ),
			'manage_options',
			SCHEMA_SCALPEL_SLUG . 'settings',
			array( $this, 'user_settings_page' ),
			1
		);

		\add_submenu_page(
			$default_page_title,
			__( 'Schema Scalpel | Export', SCHEMA_SCALPEL_TEXT_DOMAIN ),
			__( 'Export', SCHEMA_SCALPEL_TEXT_DOMAIN ),
			'manage_options',
			SCHEMA_SCALPEL_SLUG . 'export',
			array( $this, 'user_export_page' ),
			2
		);
	}

	/**
	 * Add action links to the Plugins admin page.
	 *
	 * @since 1.6
	 * @param array $links Existing plugin action links.
	 * @return array Modified action links with Settings link added.
	 */
	public function add_plugin_action_links( $links ) {
		$schema_url    = \admin_url( 'admin.php?page=' . SCHEMA_SCALPEL_TEXT_DOMAIN );
		$schema_link   = '<a href="' . \esc_url( $schema_url ) . '">' . \esc_html__( 'View All', SCHEMA_SCALPEL_TEXT_DOMAIN ) . '</a>';
		$settings_url  = \admin_url( 'admin.php?page=scsc_settings' );
		$settings_link = '<a href="' . \esc_url( $settings_url ) . '">' . \esc_html__( 'Settings', SCHEMA_SCALPEL_TEXT_DOMAIN ) . '</a>';

		// Add Settings link before other links.
		array_unshift( $links, $schema_link, $settings_link );

		return $links;
	}
}
