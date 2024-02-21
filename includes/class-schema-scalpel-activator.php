<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) exit();


/**
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */

class Schema_Scalpel_Activator
{
    public static function db_tables_initializer()
    {
        include_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $custom_schema_table = $wpdb->prefix . "scsc_custom_schemas";
        $custom_schema_sql = "CREATE TABLE $custom_schema_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            schema_type varchar(10) DEFAULT '' NOT NULL,
            post_id bigint(20) NULL,
            custom_schema varchar(10000) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $custom_schema_table)) != $custom_schema_table) :
            dbDelta($custom_schema_sql);
        else :
            /**
             * UPDATE schema_type COLUMN
             */
            $wpdb->update($custom_schema_table, array('schema_type' => 'homepage'), array('schema_type' => 'home'));
        endif;

        $schema_settings_table = $wpdb->prefix . "scsc_settings";
        $settings_sql = "CREATE TABLE $schema_settings_table (
            updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            setting_key varchar(100) DEFAULT '' NOT NULL,
            setting_value varchar(100) DEFAULT '' NOT NULL
        ) $charset_collate;";
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $schema_settings_table)) != $custom_schema_table) :
            dbDelta($settings_sql);
        endif;
        //////////////////////////////////
        /**
         * IF TABLE EXISTS, THIS PREVENTS DOUBLE ENTRIES OF INITIAL SETTINGS:
         */
        $check_active_tab_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'active_tab';";
        $has_active_tab_setting = $wpdb->get_results($check_active_tab_query, ARRAY_A);
        if (!isset($has_active_tab_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'active_tab', 'setting_value' => 'homepage'));
        else :
            $wpdb->update($schema_settings_table, array('setting_value' => 'homepage'), array('setting_key' => 'active_tab'));
        endif;

        $check_active_page_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'active_page';";
        $has_active_page_setting = $wpdb->get_results($check_active_page_query, ARRAY_A);
        if (!isset($has_active_page_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'active_page', 'setting_value' => '-1'));
        else :
            $wpdb->update($schema_settings_table, array('setting_value' => '-1'), array('setting_key' => 'active_page'));
        endif;

        $check_active_post_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'active_post';";
        $has_active_post_setting = $wpdb->get_results($check_active_post_query, ARRAY_A);
        if (!isset($has_active_post_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'active_post', 'setting_value' => '-1'));
        else :
            $wpdb->update($schema_settings_table, array('setting_value' => '-1'), array('setting_key' => 'active_post'));
        endif;

        $check_website_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'website_schema';";
        $has_website_setting = $wpdb->get_results($check_website_query, ARRAY_A);
        if (!isset($has_website_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'website_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "website_schema"));
        endif;

        $check_webpage_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'webpage_schema';";
        $has_webpage_setting = $wpdb->get_results($check_webpage_query, ARRAY_A);
        if (!isset($has_webpage_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'webpage_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "webpage_schema"));
        endif;

        $check_breadcrumb_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'breadcrumb_schema';";
        $has_breadcrumb_setting = $wpdb->get_results($check_breadcrumb_query, ARRAY_A);
        if (!isset($has_breadcrumb_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'breadcrumb_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "breadcrumb_schema"));
        endif;

        $check_search_param_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'search_param';";
        $has_search_param_setting = $wpdb->get_results($check_search_param_query, ARRAY_A);
        if (!isset($has_search_param_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'search_param', 'setting_value' => 's'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => 's'), array("setting_key" => "search_param"));
        endif;

        $check_yoast_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'yoast_schema';";
        $has_yoast_setting = $wpdb->get_results($check_yoast_query, ARRAY_A);
        if (!isset($has_yoast_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'yoast_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "yoast_schema"));
        endif;

        $check_aio_query = "SELECT * FROM $schema_settings_table WHERE setting_key = 'aio_schema';";
        $has_aio_setting = $wpdb->get_results($check_aio_query, ARRAY_A);
        if (!isset($has_aio_setting[0])) :
            $wpdb->insert($schema_settings_table, array('setting_key' => 'aio_schema', 'setting_value' => '1'));
        else :
            $wpdb->update($schema_settings_table, array("setting_value" => '1'), array("setting_key" => "aio_schema"));
        endif;
        /**
         * 
         */
    }
    /**
     * Activates the Scalpel!
     */
    public static function activate()
    {
        global $wpdb;
        if (is_multisite()) :

            $blogids = $wpdb->get_col("SELECT blog_id FROM " . $wpdb->blogs);
            foreach ($blogids as $blog_id) :

                switch_to_blog($blog_id);
                Schema_Scalpel_Activator::db_tables_initializer();
                restore_current_blog();

            endforeach;

        else :
            Schema_Scalpel_Activator::db_tables_initializer();
        endif;
    }
}
