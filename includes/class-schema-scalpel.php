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
	 * Variable initialized as SCSC_Loader class.
	 *
	 * @access   protected
	 * @var      SCSC_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		$this->schema_scalpel = SCHEMA_SCALPEL_TEXT_DOMAIN;

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
		require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-scsc-loader.php';

		// Include the admin class.
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/class-scsc-admin.php';

		// Include the public class.
		require_once SCHEMA_SCALPEL_DIRECTORY . '/public/class-scsc-public.php';

		$this->loader = new SCSC_Loader();
	}

	/**
	 * Prepare any styles and scripts.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new SCSC_Admin( $this->get_schema_scalpel(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( SCHEMA_SCALPEL_PLUGIN ), $this, 'add_plugin_action_links' );
	}

	/**
	 * Prepare any public scripts.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function define_public_hooks() {
		$plugin_public = new SCSC_Public( $this->get_schema_scalpel(), $this->get_version() );

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
	 * @return SCSC_Loader    Orchestrates the hooks of the plugin.
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
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		add_action(
			'admin_head',
			function () {
				echo <<<STYLE
            <style>img[src*="menu_icon.svg"]{padding: 6px 0 0 !important;}</style>
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
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/scsc-admin-main.php';
	}

	/**
	 * Load plugin's settings page.
	 *
	 * @since 1.0
	 */
	public function user_settings_page() {
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/scsc-user-settings.php';
	}

	/**
	 * Load plugin's export page.
	 *
	 * @since 1.0
	 */
	public function user_export_page() {
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/scsc-user-export.php';
	}

	/**
	 * Load plugin's admin menu.
	 *
	 * @since 1.0
	 */
	public function add_admin_pages() {
		$text_domain        = SCHEMA_SCALPEL_TEXT_DOMAIN;
		$slug               = SCHEMA_SCALPEL_SLUG;
		$default_page_title = __( 'Schema Scalpel Plugin', $text_domain );

		add_menu_page(
			$default_page_title,
			'Schema Scalpel',
			'manage_options',
			$slug,
			array( $this, 'admin_index_page' ),
			plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/menu_icon.svg',
			100
		);

		add_submenu_page(
			$slug,
			__( 'Schema Scalpel | Add New / Edit', $text_domain ),
			__( 'Add New / Edit', $text_domain ),
			'manage_options',
			$slug,
			array( $this, 'admin_index_page' )
		);

		add_submenu_page(
			$slug,
			__( 'Schema Scalpel | Settings', $text_domain ),
			__( 'Settings', $text_domain ),
			'manage_options',
			$slug . '_settings',
			array( $this, 'user_settings_page' ),
			1
		);

		add_submenu_page(
			$slug,
			__( 'Schema Scalpel | Export', $text_domain ),
			__( 'Export', $text_domain ),
			'manage_options',
			$slug . '_export',
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
		$text_domain   = SCHEMA_SCALPEL_TEXT_DOMAIN;
		$schema_url    = admin_url( 'admin.php?page=' . SCHEMA_SCALPEL_SLUG );
		$schema_link   = '<a href="' . esc_url( $schema_url ) . '">' . esc_html__( 'View All', $text_domain ) . '</a>';
		$settings_url  = admin_url( 'admin.php?page=' . SCHEMA_SCALPEL_SLUG . '_settings' );
		$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', $text_domain ) . '</a>';
		$export_url    = admin_url( 'admin.php?page=' . SCHEMA_SCALPEL_SLUG . '_export' );
		$export_link   = '<a href="' . esc_url( $export_url ) . '">' . esc_html__( 'Export', $text_domain ) . '</a>';

		// Add Settings link before other links.
		array_unshift( $links, $schema_link, $settings_link, $export_link );

		return $links;
	}
}
