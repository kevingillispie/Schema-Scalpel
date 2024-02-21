<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) exit();

/**
 * HELPER FUNCTION
 */
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

/**
 * GET DATABASE EXCLUSIONS
 */
global $wpdb;
$current_excluded_query = "SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'exclude';";
$current_excluded_results = $wpdb->get_results($current_excluded_query, ARRAY_A);
$database_exclusions = [];
foreach ($current_excluded_results as $key => $value) :
    array_push($database_exclusions, $current_excluded_results[$key]['setting_value']);
endforeach;

/**
 * UPDATE SETTINGS ON SAVE
 */

$all_params;
if (isset($_GET['save'])) :
    $params = parse_url(sanitize_url($_SERVER['REQUEST_URI']), PHP_URL_QUERY);
    $params = explode('&', $params);

    foreach ($params as $key => $value) :
        $all_params[] = explode('=', $value);
    endforeach;

    /**
     * WEBSITE
     */

    foreach ($all_params as $key => $value) :
        if ($all_params[$key][0] == "enable_website") :
            global $wpdb;
            $check_ws_query = "SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'website_schema';";
            $has_ws_setting = $wpdb->get_results($check_ws_query, ARRAY_A);
            if (!$has_ws_setting[0]) :
                $wpdb->insert($wpdb->prefix . "scsc_settings", array("setting_key" => "website_schema", "setting_value" => $all_params[$key][1]));
            else :
                $wpdb->update($wpdb->prefix . "scsc_settings", array("setting_value" => $all_params[$key][1]), array("setting_key" => "website_schema"));
            endif;
        endif;
    endforeach;

    /**
     * WEBPAGE
     */

    foreach ($all_params as $key => $value) :
        if ($all_params[$key][0] == "enable_webpage") :
            global $wpdb;
            $check_wp_query = "SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'webpage_schema';";
            $has_wp_setting = $wpdb->get_results($check_wp_query, ARRAY_A);
            if (!$has_wp_setting[0]) :
                $wpdb->insert($wpdb->prefix . "scsc_settings", array("setting_key" => "webpage_schema", "setting_value" => $all_params[$key][1]));
            else :
                $wpdb->update($wpdb->prefix . "scsc_settings", array("setting_value" => $all_params[$key][1]), array("setting_key" => "webpage_schema"));
            endif;
        endif;
    endforeach;

    /**
     * BREADCRUMBS
     */

    foreach ($all_params as $key => $value) :
        if ($all_params[$key][0] == "enable_breadcrumbs") :
            global $wpdb;
            $check_bc_query = "SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'breadcrumb_schema';";
            $has_bc_setting = $wpdb->get_results($check_bc_query, ARRAY_A);
            if (!$has_bc_setting[0]) :
                $wpdb->insert($wpdb->prefix . "scsc_settings", array("setting_key" => "breadcrumb_schema", "setting_value" => $all_params[$key][1]));
            else :
                $wpdb->update($wpdb->prefix . "scsc_settings", array("setting_value" => $all_params[$key][1]), array("setting_key" => "breadcrumb_schema"));
            endif;
        endif;
    endforeach;

    /**
     * YOAST SETTING
     */
    foreach ($all_params as $key => $value) :
        if ($all_params[$key][0] == "disable_yoast") :
            global $wpdb;
            $check_yoast_query = "SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'yoast_schema';";
            $has_yoast_setting = $wpdb->get_results($check_yoast_query, ARRAY_A);
            if (!$has_yoast_setting[0]) :
                $wpdb->insert($wpdb->prefix . "scsc_settings", array("setting_key" => "yoast_schema", "setting_value" => $all_params[$key][1]));
            else :
                $wpdb->update($wpdb->prefix . "scsc_settings", array("setting_value" => $all_params[$key][1]), array("setting_key" => "yoast_schema"));
            endif;
        endif;
    endforeach;

    foreach ($all_params as $key => $value) :
        if ($all_params[$key][0] == "disable_aio") :
            global $wpdb;
            $check_aio_query = "SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'aio_schema';";
            $has_aio_setting = $wpdb->get_results($check_aio_query, ARRAY_A);
            if (!$has_aio_setting[0]) :
                $wpdb->insert($wpdb->prefix . "scsc_settings", array("setting_key" => "aio_schema", "setting_value" => $all_params[$key][1]));
            else :
                $wpdb->update($wpdb->prefix . "scsc_settings", array("setting_value" => $all_params[$key][1]), array("setting_key" => "aio_schema"));
            endif;
        endif;
    endforeach;

    /**
     * GET USER-SELECTED EXCLUSIONS 
     */
    $user_selected_exclusions = [];
    foreach ($all_params as $key => $value) :
        if (startsWith($all_params[$key][0], 'exclude_')) :
            array_push($user_selected_exclusions, substr($all_params[$key][0], 8, strlen($all_params[$key][0])));
        endif;
    endforeach;

    /**
     * ADD NEW EXCLUSION
     */
    foreach ($user_selected_exclusions as $key => $value) :
        if (!in_array($user_selected_exclusions[$key], $database_exclusions)) :
            $wpdb->insert($wpdb->prefix . "scsc_settings", array("setting_key" => "exclude", "setting_value" => $user_selected_exclusions[$key]));
        endif;
    endforeach;

    /**
     * DELETE OLD EXCLUSION
     */
    foreach ($database_exclusions as $key => $value) :
        if (!in_array($database_exclusions[$key], $user_selected_exclusions)) :
            $wpdb->delete($wpdb->prefix . "scsc_settings", array("setting_key" => "exclude", "setting_value" => $database_exclusions[$key]));
        endif;
    endforeach;

    /**
     * SET urlTemplate SEARCH PARAMETER
     */
    foreach ($all_params as $key => $value) :
        if ($all_params[$key][0] == "search_param") :
            global $wpdb;
            $check_sp_query = "SELECT * FROM {$wpdb->prefix}scsc_settings WHERE setting_key = 'search_param';";
            $has_sp_setting = $wpdb->get_results($check_sp_query, ARRAY_A);
            if (!$has_sp_setting[0]) :
                $wpdb->insert($wpdb->prefix . "scsc_settings", array("setting_key" => "search_param", "setting_value" => $all_params[$key][1]));
            else :
                $wpdb->update($wpdb->prefix . "scsc_settings", array("setting_value" => $all_params[$key][1]), array("setting_key" => "search_param"));
            endif;
        endif;
    endforeach;

?>
    <meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc_settings">
<?php
    exit;
endif;

/**
 * 
 */

global $wpdb;

/**
 * GET PAGES
 */
$page_query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='page' ORDER BY post_title ASC;";
$all_pages = $wpdb->get_results($page_query, ARRAY_A);

/**
 * GET POSTS
 */
$post_query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='post' ORDER BY post_title ASC;";
$all_posts = $wpdb->get_results($post_query, ARRAY_A);

/**
 * GET WEBSITE SETTING
 */
$is_website_enabled = 1;
$ws_query = "SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='website_schema';";
$ws_setting = $wpdb->get_results($ws_query, ARRAY_A);
if ($ws_setting[0]['setting_value'] == 0) :
    $is_website_enabled = 0;
endif;

$sp_query = "SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='search_param';";
$search_query_param = $wpdb->get_results($sp_query, ARRAY_A);
$search_key = $search_query_param[0]['setting_value'];

/**
 * GET WEBPAGE SETTING
 */
$is_webpage_enabled = 1;
$wp_query = "SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='webpage_schema';";
$wp_setting = $wpdb->get_results($wp_query, ARRAY_A);
if ($wp_setting[0]['setting_value'] == 0) :
    $is_webpage_enabled = 0;
endif;

/**
 * GET BREADCRUMB SETTING
 */
$are_breadcrumbs_enabled = 1;
$bc_query = "SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='breadcrumb_schema';";
$bc_setting = $wpdb->get_results($bc_query, ARRAY_A);
if ($bc_setting[0]['setting_value'] == 0) :
    $are_breadcrumbs_enabled = 0;
endif;

/**
 * GET YOAST SETTING
 */
$is_yoast_disabled = 1;
$y_query = "SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='yoast_schema';";
$yoast_setting = $wpdb->get_results($y_query, ARRAY_A);
if ($yoast_setting[0]['setting_value'] == 0) :
    $is_yoast_disabled = 0;
endif;

/**
 * GET AIO SETTING
 */
$is_aio_disabled = 1;
$aio_query = "SELECT setting_value FROM {$wpdb->prefix}scsc_settings WHERE setting_key='aio_schema';";
$aio_setting = $wpdb->get_results($aio_query, ARRAY_A);
if ($aio_setting[0]['setting_value'] == 0) :
    $is_aio_disabled = 0;
endif;

$example_clarification = '<p><em>Your site\'s information will be substituted in the appropriate</em> <code style="color:black">{"key": "value"}</code> <em>pairs below.</em></p>';

$if_yoast = "disabled";
foreach (get_plugins() as $key => $value) :
    if (stripos($value['TextDomain'], 'wordpress-seo') > -1) :
        $if_yoast = "";
        break;
    endif;
endforeach;

$if_aio = "disabled";
foreach (get_plugins() as $key => $value) :
    if (stripos($value['TextDomain'], 'all-in-one-seo-pack') > -1) :
        $if_aio = "";
        break;
    endif;
endforeach;

$default_setting_label_html = '<small class="text-secondary"><i>(default)</i></small>';

?>
<style>
    .radio-border-left {
        border-left: 5px solid lightgray;
    }
</style>
<main class="container mt-5 ms-0">
    <header>
        <h1>User Settings <img src="<?= plugin_dir_url(SCHEMA_SCALPEL_PLUGIN); ?>admin/images/scalpel_title.svg" class="mt-n4" /></h1>
        <p class="alert alert-primary" role="alert">The default settings for this plugin allow it to perform at its best. However, you may encounter an edge case that requires modification of the default settings.</p>
        <p class="alert alert-warning mt-4" role="alert">NOTE: <em>This plugin <a href="/wp-admin/admin.php?page=scsc_settings#disable_yoast_schema">automatically overrides Yoast's and AIOSEO's schema</a> and injects your customized schema to better fit your SEO objectives.</em></p>
    </header>
    <div>
        <form>
            <input type="hidden" name="page" value="scsc_settings" />
            <input type="hidden" name="save" value="save" />

            <h3 class="mt-5 mb-0">Enable Default <code>WebSite</code> Schema</h3>
            <div class="d-flex flex-column mb-0 ps-4 py-3 radio-border-left">
                <label for="enable_website">
                    <input id="enable_website" type="radio" name="enable_website" value="1" <?= ($is_website_enabled == 1) ? "checked" : ""; ?> /><strong>Enable</strong> <code style="color:black">WebSite</code> <?= sanitize_text_field($default_setting_label_html); ?>
                </label>
                <label for="disable_website">
                    <input id="disable_website" type="radio" name="enable_website" value="0" <?= ($is_website_enabled == 0) ? "checked" : ""; ?> /><strong>Disable</strong> <code style="color:black">WebSite</code>
                </label>
            </div>
            <fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-4 noselect">
                <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">WebSite Format Example:</legend>
                <?php printf("%s", $example_clarification); ?>
                <pre id="website_example" class="mb-0 rounded language-json" data-schema="{&quot;@context&quot;:&quot;http://schema.org/&quot;,&quot;@id&quot;:&quot;https://example.com/#website&quot;,&quot;@type&quot;:&quot;WebSite&quot;,&quot;url&quot;:&quot;https://example.com/&quot;,&quot;name&quot;:&quot;WebSite Name&quot;,&quot;potentialAction&quot;:[{&quot;@type&quot;:&quot;SearchAction&quot;,&quot;target&quot;:{&quot;@type&quot;:&quot;EntryPoint&quot;,&quot;urlTemplate&quot;:&quot;https://example.com/?<?= esc_html($search_key); ?>={search_term_string}&quot;},&quot;query-input&quot;:&quot;required name=search_term_string&quot;}]}"></pre>
            </fieldset>
            <label class="d-flex flex-column mt-3" for="search_param">
                <div class="mb-2"><code style="color:black">urlTemplate</code> search parameter key:</div>
                <div>
                    <input id="search_param" name="search_param" type="text" placeholder="Current: <?= sanitize_text_field($search_key); ?>" disabled />
                    <div id="search_param_lock" class="btn btn-outline-danger">
                        <svg onclick="lockUnlock(this.parentElement)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                        </svg>
                    </div>
                </div>
                <div><i style="font-size: .75em;">Note: The default WordPress search parameter key is the letter <code>s</code>.</i></div>
            </label>
            <hr style="height:5px" class="border bg-light rounded" />
            <h3 class="mt-5 mb-0">Enable Default <code>WebPage</code> Schema</h3>
            <div class="d-flex flex-column mb-3 ps-4 py-3 radio-border-left">
                <label for="enable_webpage"><input id="enable_webpage" type="radio" name="enable_webpage" value="1" <?= ($is_webpage_enabled == 1) ? "checked" : ""; ?> /><strong>Enable</strong> <code style="color:black">WebPage</code> <?= sanitize_text_field($default_setting_label_html); ?></label>
                <label for="disable_webpage"><input id="disable_webpage" type="radio" name="enable_webpage" value="0" <?= ($is_webpage_enabled == 0) ? "checked" : ""; ?> /><strong>Disable</strong> <code style="color:black">WebPage</code></label>
            </div>
            <fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-4 noselect">
                <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">WebPage Format Example:</legend>
                <?php printf("%s", $example_clarification); ?>
                <pre id="webpage_example" class="mb-0 rounded language-json" data-schema="{&quot;@context&quot;:&quot;http://schema.org/&quot;,&quot;@id&quot;:&quot;https://example.com/pathname#webpage&quot;,&quot;@type&quot;:&quot;WebPage&quot;,&quot;url&quot;:&quot;https://example.com/pathname&quot;,&quot;name&quot;:&quot;Pathname Page Title&quot;}"></pre>
            </fieldset>
            <hr style="height:5px" class="border bg-light rounded" />
            <h3 class="mt-5 mb-0">Enable Default <code>BreadcrumbList</code> Schema</h3>
            <div class="d-flex flex-row mb-1 pt-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle text-secondary mt-n1" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                </svg>
                <div class="text-secondary ms-1" style="font-size:10pt;margin-top:-1px"><em>It is recommended that breadcrumbs remain enabled as they are utilized by Google.</em></div>
            </div>

            <div class="d-flex flex-column mb-3 ps-4 py-3 radio-border-left">
                <label for="enable_breadcrumbs"><input id="enable_breadcrumbs" type="radio" name="enable_breadcrumbs" value="1" <?= ($are_breadcrumbs_enabled == 1) ? "checked" : ""; ?> /><strong>Enable</strong> <code style="color:black">BreadcrumbList</code> <?= sanitize_text_field($default_setting_label_html); ?></label>
                <label for="disable_breadcrumbs"><input id="disable_breadcrumbs" type="radio" name="enable_breadcrumbs" value="0" <?= ($are_breadcrumbs_enabled == 0) ? "checked" : ""; ?> /><strong>Disable</strong> <code style="color:black">BreadcrumbList</code></label>
            </div>
            <fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-4 noselect">
                <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">BreadcrumbList Format Example:</legend>
                <?php printf("%s", $example_clarification); ?>
                <pre id="breadcrumb_example" class="mb-0 rounded language-json" data-schema="{&quot;@context&quot;: &quot;https://schema.org/&quot;,&quot;@type&quot;: &quot;BreadcrumbList&quot;,&quot;itemListElement&quot;: [{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;1&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page&quot;,&quot;name&quot;: &quot;Parent Page Title&quot;}},{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;2&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page/child-page&quot;,&quot;name&quot;: &quot;Child Page Title&quot;}},{&quot;@type&quot;: &quot;ListItem&quot;,&quot;position&quot;:&quot;3&quot;,&quot;item&quot;: {&quot;@type&quot;: &quot;WebPage&quot;,&quot;@id&quot;: &quot;https://example.com/parent-page/child-page/grandchild-page&quot;,&quot;name&quot;: &quot;Grandchild Page Title&quot;}}]}"></pre>
            </fieldset>
            <hr style="height:5px" class="border bg-light rounded" />

            <h3 id="disable_yoast_schema" class="mt-4">Disable Yoast SEO schema?</h3>
            <?php

            if ($if_yoast == "disabled") :

            ?>
                <pre class="mb-0 rounded language-js"><code class="language-js">&gt;&nbsp;YOAST not detected.</code><br><code class="language-js">&gt;&gt;&nbsp;Nothing to worry about here.</code><br><code class="language-js">&gt;&gt;&gt;&nbsp;Happy schema-ing!</code></pre>
            <?php

            else :

            ?>
                <pre class="mb-0 rounded language-js"><code class="language-js">&gt;&nbsp;YOAST has been detected...</code><br><code class="language-js">&gt;&nbsp;It is recommended that YOAST\'s schema feature remain disabled.</code><br><code>&gt;&nbsp;All other YOAST features will be unaffected.</code></pre>
            <?php

            endif;

            ?>
            <div class="d-flex flex-column mt-3 ps-4 py-3 radio-border-left">
                <label for="enable_yoast"><input id="enable_yoast" type="radio" name="disable_yoast" value="0" <?= ($is_yoast_disabled == 0) ? "checked" : ""; ?> <?= sanitize_text_field($if_yoast); ?> /><strong>Enable</strong> Yoast Schema</label>
                <label for="disable_yoast"><input id="disable_yoast" type="radio" name="disable_yoast" value="1" <?= ($is_yoast_disabled == 1) ? "checked" : ""; ?> <?= sanitize_text_field($if_yoast); ?> /><strong>Disable</strong> Yoast Schema <?= sanitize_text_field($default_setting_label_html); ?></label>
            </div>

            <h3 id="disable_aio_schema" class="mt-4">Disable All in One SEO schema?</h3>
            <?php

            if ($if_aio == "disabled") :

            ?>
                <pre class="mb-0 rounded language-js"><code class="language-js">&gt;&nbsp;AIO not detected.</code><br><code class="language-js">&gt;&gt;&nbsp;Nothing to worry about here.</code><br><code class="language-js">&gt;&gt;&gt;&nbsp;Happy schema-ing!</code></pre>
            <?php

            else :

            ?>
                <pre class="mb-0 rounded language-js"><code class="language-js">&gt;&nbsp;AIO SEO has been detected...</code><br><code class="language-js">&gt;&nbsp;It is recommended that AIO SEO\'s schema feature remain disabled.</code><br><code>&gt;&nbsp;All other AIO SEO features will be unaffected.</code></pre>
            <?php

            endif;

            ?>
            <div class="d-flex flex-column mt-3 ps-4 py-3 radio-border-left">
                <label for="enable_aio"><input id="enable_aio" type="radio" name="disable_aio" value="0" <?= ($is_aio_disabled == 0) ? "checked" : ""; ?> <?= sanitize_text_field($if_aio); ?> /><strong>Enable</strong> AIO SEO Schema</label>
                <label for="disable_aio"><input id="disable_aio" type="radio" name="disable_aio" value="1" <?= ($is_aio_disabled == 1) ? "checked" : ""; ?> <?= sanitize_text_field($if_aio); ?> /><strong>Disable</strong> AIO SEO Schema <?= sanitize_text_field($default_setting_label_html); ?></label>
            </div>
            <hr style="height:5px" class="border bg-light rounded" />

            <h3>Pages to Exclude from Displaying Any Schema</h3>
            <p class="font-italic">In most cases, it will not be necessary to exclude a page.</p>
            <div style="max-height:500px;overflow-y:scroll;">
                <table id="excluded_schema" class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">Page ID</th>
                            <th scope="col">Page Title</th>
                            <th scope="col">Page URL</th>
                            <th scope="col">Exclude?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        if ($all_pages) :
                            $url = get_site_url();
                            foreach ($all_pages as $key => $value) :
                                /**
                                 * CHECK FOR EXCLUDED PAGES
                                 */
                                $is_excluded = (in_array($all_pages[$key]['ID'], $database_exclusions)) ? true : false;
                                $text_color = ($is_excluded) ? "" : "text-secondary";
                                $checked = ($is_excluded) ? "checked" : "";

                        ?>
                                <tr class="<?= sanitize_html_class($text_color); ?>">
                                    <th scope="row"><?= sanitize_text_field($all_pages[$key]['ID']); ?></th>
                                    <td><?= sanitize_text_field($all_pages[$key]['post_title']); ?></td>
                                    <td><a href="<?= sanitize_url($url) . '/' . sanitize_title_with_dashes($all_pages[$key]['post_name']); ?>" target="_blank"><?= sanitize_url($url) . '/' . sanitize_title_with_dashes($all_pages[$key]['post_name']); ?></a></td>
                                    <td><input type="checkbox" id="post_num_<?= sanitize_text_field($all_pages[$key]['ID']); ?>" name="exclude_<?= sanitize_text_field($all_pages[$key]['ID']); ?>" <?= sanitize_text_field($checked); ?> onchange="pageSelected('<?= sanitize_text_field($key); ?>')" />
                                    </td>
                                </tr>
                        <?php

                            endforeach;
                        endif;

                        ?>
                    </tbody>
                </table>
            </div>
            <hr class="mb-5" />
            <div class="fixed-bottom bg-light p-3 mb-4 shadow rounded" style="left:50%;right:inherit;transform:translateX(-50%);">
                <button class="btn btn-primary px-5 py-2" type="submit">Save Settings</button>
            </div>
        </form>
    </div>
</main>
<script>
    <?php

    /**
     * LOAD ORDER MATTERS!
     */
    include_once(SCHEMA_SCALPEL_DIRECTORY . '/admin/js/schema-scalpel-user-settings.js');
    include_once(SCHEMA_SCALPEL_DIRECTORY . '/admin/js/prism.js');
    
    ?>
</script>