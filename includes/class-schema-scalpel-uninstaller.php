<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    exit('First of all, how dare you!');
endif;

/**
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */

class Schema_Scalpel_Uninstaller
{
    public static function uninstall()
    {
        global $wpdb;
        $schemas = $wpdb->prefix . 'scsc_custom_schemas';
        $wpdb->query("DROP TABLE IF EXISTS $schemas;");
        $settings = $wpdb->prefix . 'scsc_settings';
        $wpdb->query("DROP TABLE IF EXISTS $settings;");
        flush_rewrite_rules();
    }
}
