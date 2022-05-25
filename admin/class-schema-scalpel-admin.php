<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    exit('First of all, how dare you!');
endif;

/**
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin
 * @author     Kevin Gillispie
 */
class Schema_Scalpel_Admin
{
    /**
     * PLUGIN ID
     */
    private $schema_scalpel;

    /**
     * PLUGIN VERSION
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($schema_scalpel, $version)
    {
        $this->schema_scalpel = $schema_scalpel;
        $this->version = $version;
    }

    /**
     * REGISTER STYLESHEETS FOR ADMIN DASHBOARD
     */
    public function enqueue_styles()
    {
        if (stripos(get_current_screen()->id, "page_scsc") > -1) :
            wp_enqueue_style($this->schema_scalpel . "-bootstrap", plugins_url('/admin/css/bootstrap.min.css', SCHEMA_SCALPEL_PLUGIN), array(), $this->version, 'all');
            wp_enqueue_style($this->schema_scalpel . "-prism", plugins_url('/admin/css/prism.css', SCHEMA_SCALPEL_PLUGIN), array(), $this->version, 'all');
            wp_enqueue_style($this->schema_scalpel . "-admin", plugins_url('/admin/css/schema-scalpel-admin.css', SCHEMA_SCALPEL_PLUGIN), array(), $this->version, 'all');
        endif;
    }

    public function enqueue_scripts() {
        // placeholder 
    }
}
