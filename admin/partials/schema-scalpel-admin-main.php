<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    exit('First of all, how dare you!');
endif;

/**
 *
 * @link       https://schemascalpel.com/
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin/partials
 */

global $wpdb;
$custom_schemas_table = $wpdb->prefix . "scsc_custom_schemas";
if (isset($_GET["create"])) :
    $custom_schema = sanitize_text_field($_GET["create"]);
    $wpdb->insert($custom_schemas_table, array("custom_schema" => serialize($custom_schema), "schema_type" => sanitize_text_field($_GET["schemaType"]), "post_id" => sanitize_text_field($_GET["postID"])));
endif;

if (isset($_GET["update"])) :
    $custom_schema = sanitize_text_field($_GET["schema"]);
    $wpdb->update($custom_schemas_table, array("custom_schema" => serialize($custom_schema)), array("id" => sanitize_text_field($_GET["update"])));
endif;

if (isset($_GET["delete"])) :
    $wpdb->delete($custom_schemas_table, array("id" => sanitize_text_field($_GET["delete"])));
endif;

/**
 * GET/SET TABS
 */
$settings_table = $wpdb->prefix . "scsc_settings";
if (isset($_GET['active_page'])) :
    $page = sanitize_text_field($_GET['active_page']);
    $wpdb->update($settings_table, array("setting_value" => "$page"), array("setting_key" => "active_page"));
    exit();
endif;

if (isset($_GET['active_post'])) :
    $post = sanitize_text_field($_GET['active_post']);
    $wpdb->update($settings_table, array("setting_value" => "$post"), array("setting_key" => "active_post"));
    exit();
endif;

if (sanitize_text_field($_GET['page']) == 'scsc' && !isset($_GET['set_tab'])) :
    $tab_query = "SELECT setting_value FROM $settings_table WHERE setting_key = 'active_tab';";
    $result = $wpdb->get_results($tab_query, ARRAY_A);
    $tab = $result[0]['setting_value'];
?>
    <meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=<?php echo sanitize_text_field($tab); ?>">
<?php
endif;

if (isset($_GET['update_tab'])) :
    $tab = sanitize_text_field($_GET['update_tab']);
    $wpdb->update($settings_table, array("setting_value" => "$tab"), array("setting_key" => "active_tab"));
?>
    <meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=<?php echo sanitize_text_field($tab); ?>">
<?php
endif;
/**
 * 
 */

?>

<main class="container mt-5 ms-0">
    <header class="mb-2">
        <img src="<?php echo plugin_dir_url(SCHEMA_SCALPEL_PLUGIN); ?>admin/images/schema-scalpel-logo.svg" width="300" height="auto" />
    </header>
    <hr />
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-link" id="nav-homepage-tab" data-toggle="tab" href="#nav-homepage" role="tab" aria-controls="nav-homepage" aria-selected="true">Home</a>
            <a class="nav-link" id="nav-global-tab" data-toggle="tab" href="#nav-global" role="tab" aria-controls="nav-global" aria-selected="true">Global</a>
            <a class="nav-link" id="nav-pages-tab" data-toggle="tab" href="#nav-pages" role="tab" aria-controls="nav-pages" aria-selected="false">Pages</a>
            <a class="nav-link" id="nav-posts-tab" data-toggle="tab" href="#nav-posts" role="tab" aria-controls="nav-posts" aria-selected="false">Posts</a>
            <a class="nav-link ms-auto" id="nav-examples-tab" data-toggle="tab" href="#nav-examples" role="tab" aria-controls="nav-examples" aria-selected="false">Examples</a>
        </div>
    </nav>
    <?php
    /**
     * ADMIN TABS begin
     */
    ?>
    <div class="tab-content border border-top-0 p-5" id="nav-tabContent">
        <div class="tab-pane fade show" id="nav-homepage" role="tabpanel" aria-labelledby="nav-homepage-tab">
            <?php
            include_once("scsc-homepage-tab.php");
            ?>
        </div>
        <div class="tab-pane fade show" id="nav-global" role="tabpanel" aria-labelledby="nav-global-tab">
            <?php
            include_once("scsc-global-tab.php");
            ?>
        </div>
        <div class="tab-pane fade" id="nav-pages" role="tabpanel" aria-labelledby="nav-pages-tab">
            <?php
            include_once("scsc-pages-tab.php");
            ?>
        </div>
        <div class="tab-pane fade" id="nav-posts" role="tabpanel" aria-labelledby="nav-posts-tab">
            <?php
            include_once("scsc-posts-tab.php");
            ?>
        </div>
        <div class="tab-pane fade" id="nav-examples" role="tabpanel" aria-labelledby="nav-examples-tab">
            <?php
            include_once("scsc-examples-tab.php");
            ?>
        </div>
    </div>
    <p class="alert alert-warning mt-4" role="alert">NOTE: <em>This plugin <a href="/wp-admin/admin.php?page=scsc_settings#disable_yoast_schema">automatically overrides Yoast's and AIOSEO's schema</a> and injects your customized schema to better fit your SEO objectives.</em></p>
    <hr />
    <?php
    /**
     * ADMIN TABS end
     */

    /**
     * SCHEMA BLOCK-EDIT MODAL
     */
    ?>
    <div class="modal fade" id="schemaBlockEditModal" tabindex="-1" aria-labelledby="schemaBlockEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="schemaBlockEditModalLabel">Modal title</h5>
                </div>
                <div class="modal-body">
                    <pre><textarea id="schemaTextareaEdit" class="w-100" style="max-height:70vh;height:100vw;"></textarea></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeSchemaTextareaEditModal(event)">Cancel</button>
                    <button id="schemaBlockEditSave" type="button" class="btn btn-primary" data-id="" onclick="updateCurrentSchema(this.dataset.id, 'block', event)">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    /**
     * 
     */

    /**
     * SPINNER
     */
    ?>
    <div id="tab_spinner" class="d-none fixed-top w-100 h-100 mx-auto" style="background-color:rgba(0,0,0,.3)">
        <div class="d-flex justify-content-center">
            <div style="margin-top: 50vh;" class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</main>

<script>
    <?php
    /**
     * GET LAST ACTIVE PAGE AND POST SELECTIONS
     */
    $result_page = $wpdb->get_results("SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='active_page';", ARRAY_A);
    $_page = $result_page[0]['setting_value'];

    $result_post = $wpdb->get_results("SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='active_post';", ARRAY_A);
    $_post = $result_post[0]['setting_value'];
    /**
     * 
     */

    /**
     * LOAD ORDER MATTERS!
     */
    include_once(SCHEMA_SCALPEL_DIRECTORY . '/admin/js/schema-scalpel-admin-main.js');
    include_once(SCHEMA_SCALPEL_DIRECTORY . '/admin/js/prism.js');
    include_once(SCHEMA_SCALPEL_DIRECTORY . '/admin/js/bootstrap.min.js');
    ?>
</script>