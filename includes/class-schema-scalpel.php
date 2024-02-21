<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) exit();

/**
 * @link       https://schemascalpel.com
 *
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
        $this->version = SCHEMA_SCALPEL_VERSION;
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
        add_action('admin_head', function () {
            echo <<<STYLE
            <style>img[src*="menu_icon.svg"]{padding: 0 !important;}</style>
            STYLE;
        });
    }

    function admin_index_page()
    {
        require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-admin-main.php';
    }

    function user_settings_page()
    {
        require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-user-settings.php';
    }

    function user_export_page()
    {
        require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-user-export.php';
    }

    function add_admin_pages()
    {
        add_action( "admin_head", function() {
            echo '<style class="scsc-admin">.toplevel_page_scsc img {margin-top:6px}</style>';
        });
        
        add_menu_page(
            'Schema Scalpel Plugin',
            'Schema Scalpel',
            'manage_options',
            'scsc',
            array($this, 'admin_index_page'),
            plugin_dir_url(SCHEMA_SCALPEL_PLUGIN) . 'admin/images/menu_icon.svg',
            100
        );

        add_submenu_page(
            __('scsc', SCHEMA_SCALPEL_TEXT_DOMAIN),
            __('Add New / Edit', SCHEMA_SCALPEL_TEXT_DOMAIN),
            __('Add New / Edit', SCHEMA_SCALPEL_TEXT_DOMAIN),
            'manage_options',
            SCHEMA_SCALPEL_TEXT_DOMAIN,
            array($this, 'admin_index_page'),
        );

        add_submenu_page(
            __('scsc', SCHEMA_SCALPEL_TEXT_DOMAIN),
            'Schema Scalpel | Settings',
            'Settings',
            'manage_options',
            SCHEMA_SCALPEL_SLUG . 'settings',
            array($this, 'user_settings_page'),
            1
        );

        add_submenu_page(
            'scsc',
            'Schema Scalpel | Export',
            'Export',
            'manage_options',
            SCHEMA_SCALPEL_SLUG . 'export',
            array($this, 'user_export_page'),
            2
        );
    }
}
