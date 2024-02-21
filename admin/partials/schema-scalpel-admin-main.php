<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) exit();

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

if (isset($_GET["generate"])) :
    $post_type = sanitize_text_field($_GET["schemaPostType"]);
    $author_type = sanitize_text_field($_GET["schemaAuthorType"]);
    Schema_Scalpel_Admin::generate_blogposting_schema($post_type, $author_type);
    echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=posts">';
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
    <meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=<?= sanitize_text_field($tab); ?>">
<?php
endif;

if (isset($_GET['update_tab'])) :
    $tab = sanitize_text_field($_GET['update_tab']);
    $wpdb->update($settings_table, array("setting_value" => "$tab"), array("setting_key" => "active_tab"));
?>
    <meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=<?= sanitize_text_field($tab); ?>">
<?php
endif;
/**
 * 
 */

?>
<div class="row pt-3">
    <header class="mb-2">
        <img src="<?= plugin_dir_url(SCHEMA_SCALPEL_PLUGIN); ?>admin/images/schema-scalpel-logo.svg" width="300" height="auto" />
    </header>
    <hr />
    <main class="container col-8 ms-0">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-link" id="nav-homepage-tab" data-toggle="tab" href="#nav-homepage" role="tab" aria-controls="nav-homepage" aria-selected="true">Home Page</a>
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
        <div class="modal fade" id="schemaBlockEditModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg mt-5">
                <div class="modal-content">
                    <div class="modal-body">
                        <pre><textarea id="schemaTextareaEdit" class="w-100" style="max-height:70vh;height:100vw;"></textarea></pre>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button id="schemaBlockEditDelete" class="btn btn-danger px-5" data-id="" onclick="deleteCurrentSchema(this.dataset.id, event)">Delete</button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeSchemaTextareaEditModal(event)">Cancel</button>
                            <button id="schemaBlockEditSave" type="button" class="btn btn-primary" data-id="" onclick="updateCurrentSchema(this.dataset.id, 'block', event)">Save changes</button>
                        </div>
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
    <?php

    if (!empty($_GET['set_tab']) && $_GET['set_tab'] == 'posts') :

    ?>
        <aside class="col-4">
            <p class="h2"><i class="badge bg-success">New!</i></p>
            <h2>Generate <code>BlogPosting</code> Schema</h2>
            <p class="lead mt-3">Automatically create Google-recommended schema for <strong>all</strong> of your blog posts... <span class="fst-italic">at one time!</span></p>
            <div style="border-left:4px solid var(--bs-info-border-subtle);padding:10px;background-color:var(--bs-info-bg-subtle)">
                <p class="mb-2">Instead of spending precious hours (<span class="fst-italic">days, even!</span>) copy-and-pasting schema into each and every blog post, use this tool to auto-generate schema for your blog posts.</p>
                <p class="mb-2"><strong>Note:</strong> This will <strong>NOT</strong> overwrite any schema you've already added to a blog post. In fact, it will skip any post with preexisting schema just to be safe.</p>
                <p class="mb-0 fst-italic">Use the defaults for standard blog posts.</p>
            </div>

            <form class="container mt-3 py-3 border">
                <input type="hidden" name="page" value="scsc" />
                <input type="hidden" name="set_tab" value="posts" />
                <input type="hidden" name="generate" value="post_schema" />

                <h3 class="h5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-1-square-fill me-1" viewBox="0 0 16 16">
                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm7.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z" />
                    </svg>
                    Schema <code>@type</code>
                </h3>
                <div class="d-flex flex-column bg-secondary-subtle mb-0 ps-4 py-3 radio-border-left">
                    <div class="form-check">
                        <input class="form-check-input mt-1" type="radio" name="schemaPostType" id="schemaPostType1" value="BlogPosting" data-label="BlogPosting" checked>
                        <label class="form-check-label" for="schemaPostType1">
                            <div class="d-flex flex-row">
                                <pre class="m-0">BlogPosting</pre>&nbsp;<small class="text-secondary">(default)</small>
                            </div>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input mt-1" type="radio" name="schemaPostType" id="schemaPostType2" value="Article" data-label="Article">
                        <label class="form-check-label" for="schemaPostType2">
                            <pre class="m-0">Article</pre>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input mt-1" type="radio" name="schemaPostType" id="schemaPostType3" value="NewsArticle" data-label="NewsArticle">
                        <label class="form-check-label" for="schemaPostType3">
                            <pre class="m-0">NewsArticle</pre>
                        </label>
                    </div>
                </div>
                <h3 class="h5 mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-2-square-fill me-1" viewBox="0 0 16 16">
                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm4.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306" />
                    </svg>
                    Schema <code>author</code>
                </h3>
                <div class="d-flex flex-column bg-secondary-subtle mb-0 ps-4 py-3 radio-border-left">
                    <div class="form-check">
                        <input class="form-check-input mt-1" type="radio" name="schemaAuthorType" id="schemaAuthorType1" value="Person" data-label="Person" checked>
                        <label class="form-check-label" for="schemaAuthorType1">
                            <div class="d-flex flex-row">
                                <pre class="m-0">Person</pre>&nbsp;<small class="text-secondary">(default)</small>
                            </div>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input mt-1" type="radio" name="schemaAuthorType" id="schemaAuthorType2" value="Organization" data-label="Organization">
                        <label class="form-check-label" for="schemaAuthorType2">
                            <pre class="m-0">Organization</pre>
                        </label>
                    </div>
                </div>
                <div style="border-left:4px solid var(--bs-info-border-subtle);padding:10px;background-color:var(--bs-info-bg-subtle)">
                <p class="mb-2"><strong>Before clicking the Generate Post Schema button...</strong></p>
                    <p class="mb-2"><i>Ensure that you have set each blog post's author in the <a href="/wp-admin/edit.php">Posts</a> tab of your WordPress installation to whomever it is that you want to appear in the schema. (This can be done as a bulk action, if necessary.)</i></p>
                    <p class="mb-0"><i>Also, double check that, in the author's <a href="/wp-admin/users.php">User Profile</a>, has the first and last name fields filled out.</i></p>
                </div>
                <div class="mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-3-square-fill me-1" viewBox="0 0 16 16">
                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z" />
                    </svg>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Generate Post Schema</button>
                <hr>
                <h3 class="h5 mt-3">Example Schema</h3>
                <pre class="w-100 rounded language-json">
<code>{</code>
<code>  "@context":"https://schema.org",</code>
<code id="schemaPostType">  "@type":"BlogPosting",</code>
<code>  "headline":"[Post Title]",</code>
<code>  "image":[</code>
<code>      "[URL to Thumbnail]"</code>
<code>  ],</code>
<code>  "datePublished":"[Publish Date (GMT)]",</code>
<code>  "dateModified":"[Modified Date (GMT)]",</code>
<code> "author":[</code>
<code>      {</code>
<code id="schemaAuthorType">          "@type":"Person",</code>
<code>          "name":"[Post Author Name]",</code>
<code>          "url":"[URL to Author's Site]"</code>
<code>      }</code>
<code>  ]</code>
<code>}</code>
</pre>
            </form>

            <hr style="height:5px" class="border border-secondary bg-light rounded" />
        </aside>
    <?php
    endif;
    ?>
</div>
<script>
    <?php
    /**
     * GET LAST ACTIVE PAGE / POST SELECTIONS
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