<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    // If this file is called directly, EJECT EJECT EJECT!
    exit('First of all, how dare you!');
endif;

?>
<div class="d-flex flex-column">
    <h2>Global Schema</h2>
    <p class="alert alert-info mb-0" role="alert">Create schema to appear on every page of your site.</p>

    <div id="global_schema">
        <fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-5">
            <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">Current:</legend>
            <div id="current_global_schema">
                <?php

                global $wpdb;
                $get_schema = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'global';";
                $results = $wpdb->get_results($get_schema, ARRAY_A);
                if ($results) :
                    foreach ($results as $key => $value) :
                        $wet_cereal = unserialize($results[$key]['custom_schema']);

                        ?>
                        <pre class="w-100 rounded language-json" data-id="<?php echo sanitize_text_field($results[$key]['id']); ?>" data-schema="<?php echo esc_html($wet_cereal); ?>"></pre>
                        <?php

                    endforeach;
                endif;

                ?>
            </div>
        </fieldset>
        <?php

        $current_partial_name = __FILE__;
        include("scsc-create-new-schema.php");
        $pageID = get_option('page_on_front');
        
        ?>
    </div>
</div>