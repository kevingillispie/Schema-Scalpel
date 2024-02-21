<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) exit();

$post_query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='post' ORDER BY post_title ASC;";
$all_posts = $wpdb->get_results($post_query, ARRAY_A);

$get_schema = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type='posts';";
$results = $wpdb->get_results($get_schema, ARRAY_A);
$existing_schema_ids = [];
foreach ($results as $key => $value) :
    array_push($existing_schema_ids, $results[$key]['post_id']);
endforeach;

?>
<div class="mb-5">
    <h2>Post Schema</h2>
    <select id="posts_list" class="form-select py-2">
        <option default="true">Choose a post to view or edit...</option>
        <?php

        foreach ($all_posts as $key => $value) :
            $classes = '';
            if (in_array($all_posts[$key]['ID'], $existing_schema_ids)) :
                $classes = true;
            endif;
            $page_id = $all_posts[$key]['ID'];
            $page_title = $all_posts[$key]['post_title'];
            if (stripos(strtolower($page_title), 'auto draft') > -1) continue;

        ?>
            <option <?= ($classes) ? 'class="fw-bold"' : ''; ?> value="<?= sanitize_text_field($page_id); ?>"><?= sanitize_text_field($page_id); ?>: <?= sanitize_text_field($page_title); ?></option>

        <?php

        endforeach;

        ?>
    </select>
    <div id="post_schema">
        <fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-5">
            <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">Current:</legend>
            <div id="current_posts_schema">
                <?php

                global $wpdb;
                $get_schema = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'posts';";
                if ($results) :
                    foreach ($results as $key => $value) :
                        $post_id = $results[$key]["post_id"];
                        $no_cereal = unserialize($results[$key]['custom_schema']);

                ?>
                        <pre class="w-100 rounded d-none language-json" onclick="editSchemaCodeBlock('posts', '', this.dataset.id, event)" data-id="<?= sanitize_text_field($results[$key]['id']); ?>" data-post-id="<?= sanitize_text_field($post_id); ?>" data-schema="<?= sanitize_text_field($no_cereal); ?>">
</pre>
                <?php

                    endforeach;
                endif;

                ?>
            </div>
        </fieldset>
        <?php

        $current_partial_name = __FILE__;
        include("scsc-create-new-schema.php");

        ?>
    </div>
</div>
<?php

add_action("admin_footer", function () {
    echo <<<SCRIPT
    <script class="scsc-footer">
        document.addEventListener("DOMContentLoaded", () => {
            var schemaPostTypeBtns = document.querySelectorAll("input[name=\"schemaPostType\"]");
            var schemaAuthorTypeBtns = document.querySelectorAll("input[name=\"schemaAuthorType\"]");
            var postTypeExample = document.querySelector("#schemaPostType span.token.string");
            var schemaAuthorTypeExample = document.querySelector("#schemaAuthorType span.token.string");
            
            schemaPostTypeBtns.forEach(btn => {
                btn.addEventListener("change", () => {
                    postTypeExample.innerText = '"' + btn.dataset.label + '"';
                })
            })
            schemaAuthorTypeBtns.forEach(btn => {
                btn.addEventListener("change", () => {
                    schemaAuthorTypeExample.innerText = '"' + btn.dataset.label + '"';
                })
            })
        });
    </script>
    SCRIPT;
});
