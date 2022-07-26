<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    // If this file is called directly, EJECT EJECT EJECT!
    exit('First of all, how dare you!');
endif;

/**
 *
 * @link       https://schemascalpel.com
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 */

/**
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */

class Schema_Scalpel
{

    /**
     * @access   protected
     * @var      Schema_Scalpel_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * @access   protected
     * @var      string    $schema_scalpel    The string used to uniquely identify this plugin.
     */
    protected $schema_scalpel;

    /**
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;


    public function __construct()
    {
        if (defined('SCHEMA_SCALPEL_VERSION')) {
            $this->version = SCHEMA_SCALPEL_VERSION;
        } else {
            $this->version = '1.2.5.4';
        }
        $this->schema_scalpel = 'schema-scalpel';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->register();
    }

    /**
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-loader.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/class-schema-scalpel-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once SCHEMA_SCALPEL_DIRECTORY . '/public/class-schema-scalpel-public.php';

        $this->loader = new Schema_Scalpel_Loader();
    }

    /**
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Schema_Scalpel_Admin($this->get_schema_scalpel(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Schema_Scalpel_Public($this->get_schema_scalpel(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_inline_scripts');
    }

    public function run()
    {
        $this->loader->run();
    }

    /**
     * @return    string    The name of the plugin.
     */
    public function get_schema_scalpel()
    {
        return $this->schema_scalpel;
    }

    /**
     * @return    Schema_Scalpel_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    private function register()
    {
        add_action('admin_menu', array($this, 'add_admin_pages'));
    }

    function admin_index_page()
    {
        require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-admin-main.php';
    }

    function user_settings_page()
    {
        require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-user-settings.php';
    }

    function user_tools_page()
    {
        require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-user-tools.php';
    }

    function add_admin_pages()
    {
        add_menu_page('Schema Scalpel Plugin', 'Schema Scalpel', 'manage_options', 'scsc', array($this, 'admin_index_page'), plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/scalpel_menu.svg', 100);

        add_submenu_page('scsc', 'Schema Scalpel | Settings', 'Settings', 'manage_options', 'scsc_settings', array($this, 'user_settings_page'), 1);

        add_submenu_page('scsc', 'Schema Scalpel | Tools', 'Tools', 'manage_options', 'scsc_tools', array($this, 'user_tools_page'), 2);
    }
}