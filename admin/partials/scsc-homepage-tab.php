<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) exit();

?>
<div class="d-flex flex-column">
    <h2>Homepage Schema</h2>
    <p class="alert alert-info mb-0" role="alert">Create schema to appear on the main page of your site.</p>

    <div id="homepage_schema">
        <fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-5">
            <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">Current:</legend>
            <div id="current_homepage_schema">
                <?php

                global $wpdb;
                $homepageID = get_option('page_on_front');
                $get_schema = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'homepage';";
                $results = $wpdb->get_results($get_schema, ARRAY_A);
                if ($results) :
                    foreach ($results as $key => $value) :
                        $wet_cereal = unserialize($results[$key]['custom_schema']);

                        ?>
                        <pre class="w-100 rounded language-json" onclick="editSchemaCodeBlock('homepage', '', this.dataset.id, event)" data-id="<?= sanitize_text_field($results[$key]['id']); ?>" data-schema="<?= esc_html($wet_cereal); ?>"></pre>
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