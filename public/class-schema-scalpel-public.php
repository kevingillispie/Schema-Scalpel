<?php

namespace SchemaScalpel;

/**
 *
 * @link       https://schemascalpel.com
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/public
 */

/**
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/public
 * @author     Kevin Gillispie
 */

// If this file is called directly, EJECT EJECT EJECT!
if (!defined('ABSPATH')) exit;

class Schema_Scalpel_Public
{

    /**
     * The ID of this plugin.
     *
     * @access   private
     * @var      string    $schema_scalpel    The ID of this plugin.
     */
    private $schema_scalpel;

    /**
     * The version of this plugin.
     *
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $schema_scalpel       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($schema_scalpel, $version)
    {

        $this->schema_scalpel = $schema_scalpel;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->schema_scalpel, SCHEMA_SCALPEL_DIRECTORY . '/public/css/schema-scalpel-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->schema_scalpel, SCHEMA_SCALPEL_DIRECTORY . '/public/js/schema-scalpel-public.js', array(), $this->version, false);
    }

    public function enqueue_inline_scripts()
    {
        /**
         * IGNORE SCHEMA IF POST/PAGE OR ADMIN PAGE
         */
        global $post;
        if (!$post || is_admin() || is_customize_preview()) :
            return;
        endif;
        /**
         * 
         */

        $site_title = get_bloginfo('name');
        $page_id = get_the_ID();
        /**
         * CHECK DATABASE EXCLUSIONS
         */
        global $wpdb;
        $current_excluded_results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'exclude';", ARRAY_A);
        foreach ($current_excluded_results as $key => $value) :
            if (in_array($page_id, $current_excluded_results[$key])) :
                return;
            endif;
        endforeach;
        /**
         * 
         */
        $root_domain = get_site_url();
        $path = sanitize_url($_SERVER["REQUEST_URI"]);
        $wpTitle = wp_title('|', false, 'right');
        $page_title = ($wpTitle) ? html_entity_decode($wpTitle) : ((get_the_title()) ? html_entity_decode(get_the_title()) : ((the_title_attribute()) ? html_entity_decode(the_title_attribute()) : $site_title));
        $schema_script_html = "<script class=\"schema-scalpel-seo\" type=\"application/ld+json\">";
        $search_query_param = $wpdb->get_results("SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='search_param';");
        $search_key = $search_query_param[0]->setting_value;

        /**
         * WEBSITE
         */
        $website_schema = <<<WEBSITE
    {
        "@context": "http://schema.org/",
        "@id": "{$root_domain}/#website",
        "@type": "WebSite",
        "url": "{$root_domain}/",
        "name": "{$site_title}",
        "potentialAction": [
            {
                "@type": "SearchAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "{$root_domain}/?{$search_key}={search_term_string}"
                },
                "query-input": "required name=search_term_string"
            }
        ]
    }
WEBSITE;

        /**
         * WEBPAGE
         */
        $webpage_schema = <<<WEBPAGE
    {
        "@context": "http://schema.org/",
        "@id": "{$root_domain}{$path}#webpage",
        "@type": "WebPage",
        "url": "{$root_domain}{$path}",
        "name": "{$page_title}"
    }
WEBPAGE;

        /**
         * BREADCRUMBS
         */
        $crumb = strtok($path, "/");
        $breadcrumbs[] = $crumb;

        while ($crumb !== false) :
            global $breadcrumbs;
            $breadcrumbs[] = $crumb;
            $crumb = strtok("/");
        endwhile;

        function get_page_title($url)
        {
            return get_the_title(url_to_postid($url));
        }

        function format_breadcrumbs($root_domain, $breadcrumbs)
        {
            $crumb_list = '';
            $crumb_path = '';
            if ($breadcrumbs[0] != false) :
                $i = 0;
                while ($i < count($breadcrumbs)) :
                    $pos = $i + 1;
                    $crumb_path .= '/' . $breadcrumbs[$i];
                    $bc_page_title = get_page_title($root_domain . $crumb_path) ? get_page_title($root_domain . $crumb_path) : wp_get_document_title();

                    $crumb_list .= <<<CRUMBS
{
    "@type": "ListItem",
    "position": "{$pos}",
    "item": {
        "@type": "WebPage",
        "@id": "{$root_domain}{$crumb_path}",
        "name": "{$bc_page_title}"
    }
}
CRUMBS;
                    ($i + 1 == count($breadcrumbs)) ? '' : $crumb_list .= ',';
                    $i++;
                endwhile;
            endif;
            return $crumb_list;
        }

        $listElements = format_breadcrumbs($root_domain, $breadcrumbs);

        $breadcrumb_schema = <<<BREAD
    {
        "@context": "https://schema.org/",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {$listElements}
        ]
    }
BREAD;

        function format_schema_html($html, $s) {
            printf("%s%s</script>", $html, $s);
        }
        /**
         * INJECT GLOBAL SCHEMA
         */
        $global_schema = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'global';", ARRAY_A);
        foreach ($global_schema as $key => $value) :
            $schema = str_replace("&apos;", "'", html_entity_decode(unserialize($value['custom_schema'])));
            format_schema_html($schema_script_html, $schema);
        endforeach;

        /**
         * INJECT PAGE SCHEMA
         */
        $current_page_results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE post_id = '$page_id';", ARRAY_A);
        foreach ($current_page_results as $key => $value) :
            $schema = str_replace("&apos;", "'", html_entity_decode(unserialize($value['custom_schema'])));
            format_schema_html($schema_script_html, $schema);
        endforeach;

        /**
         * INJECT HOMEPAGE SCHEMA
         */
        if ($path == "/") :
            $home_schema = $wpdb->get_results("SELECT custom_schema FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'home';", ARRAY_A);
            foreach ($home_schema as $key => $value) :
                $schema = str_replace("&apos;", "\'", html_entity_decode(unserialize($value['custom_schema'])));
                format_schema_html($schema_script_html, $schema);
            endforeach;
        endif;

        /**
         * INJECT DEFAULT SCHEMA
         */
        $check_website_setting = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'website_schema';", ARRAY_A);
        if ($check_website_setting[0]['setting_value'] == '1') :
            format_schema_html($schema_script_html, $website_schema);
        endif;

        $check_webpage_setting = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'webpage_schema';", ARRAY_A);
        if ($check_webpage_setting[0]['setting_value'] == '1') :
            format_schema_html($schema_script_html, $webpage_schema);
        endif;

        $check_bc_setting = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'breadcrumb_schema';", ARRAY_A);
        if ($path != "/" && $check_bc_setting[0]['setting_value'] == '1') :
            format_schema_html($schema_script_html, $breadcrumb_schema);
        endif;
        /**
         * 
         */
    }
}
