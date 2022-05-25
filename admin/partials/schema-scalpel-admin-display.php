<?php

namespace SchemaScalpel;

/**
 *
 * @link       https://schemascalpel.com
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin/partials
 */

$all_pages = get_pages(array(
    'order' => 'ASC'
));
$all_posts = get_posts(array(
    'order' => 'DESC'
));

?>
<style>
    pre::before {
        counter-reset: listing;
    }

    pre code {
        counter-increment: listing;
        padding-left: 0px;
    }

    pre code::before {
        content: counter(listing) " ";
        display: inline-block;
        color: #555;
        margin: 0 10px 0 4px;
        box-sizing: border-box;
    }
</style>

<main class="container mt-5 ms-0">
    <img src="<?php echo plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ); ?>admin/images/schema-scalpel-logo.svg" width="200" />
    <hr>
    <div class="editor-container d-flex flex-column flex-lg-row">
        <div class="d-flex flex-column w-50">
            <div class="mb-5">
                <h2>Pages</h2>
                <select>
                    <option default="true">Choose a page to edit...</option>
                    <?php
                    foreach ($all_pages as $key => $value) {
                        echo esc_html("<option>" . sanitize_text_field($all_pages[$key]->ID) . ": " . sanitize_text_field($all_pages[$key]->post_title) . "</option>");
                    }
                    ?>
                </select>
            </div>
            <div class="mb-5">
                <h2>Posts</h2>
                <select>
                    <option default="true">Choose a post to edit...</option>
                    <?php
                    foreach ($all_posts as $key => $value) {
                        echo esc_html("<option>" . sanitize_text_field($all_posts[$key]->ID) . ": " . sanitize_text_field($all_posts[$key]->post_title) . "</option>");
                    }
                    ?>
                </select>
            </div>
            <div class="d-flex flex-column">
                <h2>Update Globals</h2>
                <div id="current_global_schema">
                    <?php
                    global $wpdb;
                    $get_schema = "SELECT * FROM {$wpdb->prefix} . scsc_custom_schemas;";
                    $results = $wpdb->get_results($get_schema, ARRAY_A);
                    if ($results) {
                        foreach ($results as $key => $value) {
                            echo esc_html(json_decode($results[0]["global_schema"]));
                        }
                    }
                    ?>
                </div>
                <textarea id="global_schema" class="w-75"></textarea>
                <button id="global_schema_update" class="btn btn-primary w-75 mt-3" type="submit">Update</button>
                <div id="request_result"></div>
                <?php

                if (isset($_GET["gs"])) {
                    global $wpdb;
                    $global_schema = sanitize_key($_GET["gs"]);
                    $wpdb->insert($wpdb->prefix . "scsd_custom_schemas", array("global_schema" => json_encode($global_schema)));
                }

                ?>
            </div>
            <script>
                document.getElementById('global_schema_update').addEventListener('click', function() {
                    updateGlobalSchema();
                });

                function updateGlobalSchema() {
                    let request = new XMLHttpRequest();
                    let value = document.getElementById("global_schema").value;
                    let formatted = value.replaceAll(/(\r\n|\n|\r|\t)/gm,"").replaceAll(" ", "");
                    request.onreadystatechange = () => {
                        if (request.readyState == 4) {
                            location.reload();
                        }
                    }
                    request.open("GET", "/wp-admin/admin.php?page=Schema_Scalpel_slug" + "&gs=" + formatted);
                    request.send();
                }
            </script>
        </div>
        <div class="d-flex flex-column w-50">
            <div class="d-flex flex-row justify-content-between">
                <h2>Editor</h2>
                <div class="d-flex flex-column text-right justify-content-center">
                    <p class="mb-0">Currently <span id="editor_action">viewing</span>: <strong id="current_post">Default Schema</strong></p>
                    <p>Title: <span>N/A</span></p>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button id="edit_schema" class="btn btn-primary" type="submit">Edit</button>
                <div>
                    <button id="save_schema" class="btn btn-primary" type="submit">Save</button>
                    <button id="apply_all_schema" class="btn btn-primary" type="submit">Apply to All</button>
                </div>
            </div>
            <form>

                <pre id="current_schema" class="language-json"></pre>

            </form>
        </div>
    </div>
</main>

<?php
include_once(SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-defaults.php');
?>

<script>
    var codeContainer = document.getElementById("current_schema");
    var tabCount = 1;
    var syntax = {
        "array": ["[", "]"],
        "object": ["{", "}"]
    }

    function printJSONLoop(jObject, t) {
        for (const [key, value] of Object.entries(jObject)) {
            if (checkType(value) == 'string') {
                codeContainer.insertAdjacentHTML("beforeend", `<code>${insertTabs(t)}${isNumber(key) ? "": `"${key}": `}"${value}",</code><br>`);
            } else if (checkType(value) == "array") {
                codeContainer.insertAdjacentHTML("beforeend", `<code>${insertTabs(t)}"${key}": ${syntax["array"][0]}</code><br>`);
                t++;
                printJSONLoop(value, t);
                t--;
                codeContainer.insertAdjacentHTML("beforeend", `<code>${insertTabs(t)}${syntax["array"][1]},</code><br>`);
            } else if (checkType(value) == 'object') {
                codeContainer.insertAdjacentHTML("beforeend", `<code>${insertTabs(t)}${isNumber(key) ? "": `"${key}": `}${syntax["object"][0]}</code><br>`);
                t++;
                printJSONLoop(value, t);
                t--;
                codeContainer.insertAdjacentHTML("beforeend", `<code>${insertTabs(t)}${syntax["object"][1]},</code><br>`);
            }
        }
    }

    function isNumber(n) {
        return Number.isInteger(parseInt(n));
    }

    function checkType(thing) {
        if (Array.isArray(thing)) {
            return "array";
        }
        return typeof(thing);
    }

    function insertTabs(count) {
        let t = "";
        for (i = 0; i < count; i++) {
            t += "&#9;";
        }
        return t;
    }
</script>
<script>
    window.onload = () => {
        /**
         * MAIN LOOP
         */
        var queryParam = new URLSearchParams(document.location.search.substring(1));
        var postID = queryParam.get("postID");
        if (!postID) {
            codeContainer.insertAdjacentHTML("beforeend", `<code>// DEFAULT SCHEMA READY TO CUSTOMIZE</code><br>`);
            for (const [key, value] of Object.entries(defaultSchemas)) {
                codeContainer.insertAdjacentHTML("beforeend", `<code>// ${comments[key]}</code><br><code>${syntax["object"][0]}</code><br>`);
                printJSONLoop(value, tabCount);
                codeContainer.insertAdjacentHTML("beforeend", `<code>${syntax["object"][1]}</code><br>`);
            }
        }
        /**
         * 
         */
        <?php
        include_once(SCHEMA_SCALPEL_DIRECTORY . '/admin/js/prism.js');
        ?>
    }
</script>