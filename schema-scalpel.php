<?php

namespace SchemaScalpel;

/**
 * @link              https://schemascalpel.com
 * @package           Schema_Scalpel
 *
 * @wordpress-plugin
 * Plugin Name:       Schema Scalpel
 * Plugin URI:        https://schemascalpel.com/
 * Description:       A simple plugin to customize your site's schema on a per-page basis.
 * Version:           1.3.1
 * Author:            Kevin Gillispie
 * Author URI:        https://kevingillispie.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       schema-scalpel
 * 
 * Schema Scalpel lets you customize your site's schema on a per-page basis.
 * Copyright (C) 2021 - 2024 Kevin Gillispie
 */

if (!defined('ABSPATH')) exit();

define('SCHEMA_SCALPEL_VERSION', '1.3.1');
define('SCHEMA_SCALPEL_TEXT_DOMAIN', 'scsc');
define('SCHEMA_SCALPEL_SLUG', 'scsc_');
define('SCHEMA_SCALPEL_PLUGIN', __FILE__);
define('SCHEMA_SCALPEL_DIRECTORY', untrailingslashit(dirname(SCHEMA_SCALPEL_PLUGIN)));


require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-activator.php';
require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-deactivator.php';
require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-uninstaller.php';


register_activation_hook(SCHEMA_SCALPEL_PLUGIN, [__NAMESPACE__ . '\\Schema_Scalpel_Activator', 'activate']);
register_deactivation_hook(SCHEMA_SCALPEL_PLUGIN, [__NAMESPACE__ . '\\Schema_Scalpel_Deactivator', 'deactivate']);
register_uninstall_hook(SCHEMA_SCALPEL_PLUGIN, [__NAMESPACE__ . '\\Schema_Scalpel_Uninstaller', 'uninstall']);


require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel.php';

function run_schema_scalpel()
{
    $plugin = new Schema_Scalpel();
    $plugin->run();
}
add_action('plugins_loaded', __NAMESPACE__ . '\\run_schema_scalpel');

global $wpdb;
$settings_table = $wpdb->prefix . "scsc_settings";
$prepped = $wpdb->prepare("SHOW TABLES LIKE %s", $settings_table);
$table_checked = $wpdb->get_var($prepped);
if (isset($table_checked) && $table_checked == $settings_table) :
    /**
     * TURN OFF YOAST SCHEMA GENERATOR
     */

    $y_query = "SELECT setting_value FROM $settings_table WHERE setting_key='yoast_schema';";
    $yoast_setting = $wpdb->get_results($y_query, ARRAY_A);
    if (isset($yoast_setting[0]) && $yoast_setting[0]['setting_value'] == 1) :
        add_filter('wpseo_json_ld_output', '__return_false');
    endif;

    /**
     * 
     */

    /**
     * TURN OFF AIO SEO SCHEMA
     */

    $aio_query = "SELECT setting_value FROM $settings_table WHERE setting_key='aio_schema';";
    $aio_setting = $wpdb->get_results($aio_query, ARRAY_A);
    if (isset($aio_setting[0]) && $aio_setting[0]['setting_value'] == 1) :
        add_filter('aioseo_schema_output', function ($graphs) {
            foreach ($graphs as $index => $graph) :
                unset($graphs[$index]);
            endforeach;
            return $graphs;
        });
    endif;

    /**
     * 
     */
endif;
