<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) exit();

$page_query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='page' ORDER BY post_title ASC;";
$all_pages = $wpdb->get_results($page_query, ARRAY_A);

$get_schema = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type='pages';";
$results = $wpdb->get_results($get_schema, ARRAY_A);
$existing_schema_ids = [];
foreach ($results as $key => $value) :
    array_push($existing_schema_ids, $results[$key]['post_id']);
endforeach;

?>

<div class="mb-5">
    <h2>Page Schema</h2>
    <select id="pages_list" class="form-select py-2">
        <option>Choose a page to view or edit...</option>
        <?php

        foreach ($all_pages as $key => $value) :
            if ($all_pages[$key]['ID'] !== get_option('page_on_front')) :
                $classes = '';
                if (in_array($all_pages[$key]['ID'], $existing_schema_ids)) :
                    $classes = 'fw-bold';
                endif;

        ?>
                <option class="<?= sanitize_html_class($classes); ?>" value="<?= sanitize_text_field($all_pages[$key]['ID']); ?>"><?= sanitize_text_field($all_pages[$key]['ID']); ?>: <?= sanitize_text_field($all_pages[$key]['post_title']); ?></option>
        <?php

            endif;
        endforeach;

        ?>
    </select>
    <div id="page_schema">
        <fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-5">
            <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">Current:</legend>
            <div id="current_pages_schema">
                <?php

                if ($results) :

                    foreach ($results as $key => $value) :
                        $post_id = $results[$key]['post_id'];
                        $post_title = "";
                        foreach ($all_pages as $k => $v) :
                            if ($all_pages[$k]['ID'] == $post_id) :
                                $post_title = $all_pages[$k]['post_title'];
                            endif;
                        endforeach;
                        $no_cereal = unserialize($results[$key]['custom_schema']);

                ?>
                        <pre class="w-100 rounded d-none language-json" onclick="editSchemaCodeBlock('pages', '', this.dataset.id, event)" data-id="<?= sanitize_text_field($results[$key]['id']); ?>" data-post-id="<?= sanitize_text_field($post_id); ?>" data-schema="<?= esc_html($no_cereal); ?>"></pre>
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