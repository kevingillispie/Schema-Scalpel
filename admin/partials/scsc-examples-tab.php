<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    // If this file is called directly, EJECT EJECT EJECT!
    exit('First of all, how dare you!');
endif;

global $wpdb;
$get_schema = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'example';";
$results = $wpdb->get_results($get_schema, ARRAY_A);

?>
<div class="d-flex flex-column">
    <h2>Example Schema</h2>
    <p class="alert alert-info mb-0" role="alert">Get a jumpstart on your website's schema!</p>
    <p class="lead mt-3">Simply copy and paste any one of these schema types into the schema editor (i.e. <a href="/wp-admin/admin.php?page=scsc&set_tab=home">Home</a>, <a href="/wp-admin/admin.php?page=scsc&set_tab=global">Global</a>, etc) of your choice.</p>

    <h5 class="mt-5 font-weight-bold">1. Select Schema Type:</h5>
    <ul class="nav nav-pills bg-light p-3 rounded border">
        <?php
        if ($results) :
            $tab_count = 0;
            foreach ($results as $key => $value) :
                $schema_type = json_decode(html_entity_decode(unserialize($results[$key]['custom_schema']), ENT_QUOTES), true)['@type'];
        ?>
                <li class="nav-item example-items noselect m-0" data-schema-type="<?php echo esc_html($schema_type); ?>">
                    <div class="nav-link example-nav <?php echo ($tab_count == "0") ? sanitize_text_field("active") : ""; ?>" aria-current="page"><?php echo esc_html($schema_type); ?></div>
                </li>
        <?php
                $tab_count++;
            endforeach;
        endif;
        ?>
    </ul>
    <h5 class="mt-5 font-weight-bold">2. Copy Schema:</h5>
    <div id="example_fieldsets" class="editor-container d-flex flex-column">

        <?php
        if ($results) :
            $fieldset_count = 0;
            foreach ($results as $key => $value) :
                $schema_type = json_decode(html_entity_decode(unserialize($results[$key]['custom_schema']), ENT_QUOTES), true)['@type'];
        ?>
                <fieldset <?php echo ($fieldset_count == 0) ?: esc_html('style=display:none!important') ?> class="d-flex flex-column justify-content-between bg-light border rounded p-3 noselect" data-schema-type="<?php echo esc_html($schema_type); ?>">
                    <legend class="px-3 pb-1 border rounded bg-white" style="width:auto"><?php echo esc_html($schema_type); ?>:</legend>
                    <button class="btn btn-primary py-2" onclick="copySchema(this.nextElementSibling)">Copy to Clipboard</button>
                    <div id="<?php echo sanitize_text_field("example_schema_" . $schema_type); ?>">
                        <?php
                        $wet_cereal = unserialize($results[$key]['custom_schema']);
                        ?>
                        <pre class="w-100 rounded language-json" data-id="<?php echo sanitize_text_field($results[$key]['id']);?>" data-schema="<?php echo esc_html($wet_cereal); ?>"></pre>
                    </div>
                </fieldset>
        <?php
                $fieldset_count++;
            endforeach;
        endif;
        ?>

    </div>
</div>
<script>
    function copySchema(sib) {
        navigator.clipboard.writeText(sib.innerText);
    }
</script>
<script>
    var exampleTabs = document.querySelectorAll(".example-items");
    exampleTabs.forEach(tab => {
        tab.addEventListener("click", function() {
            tabToggler(this);
            fieldsetToggler(this);
        })
    })

    function tabToggler(el) {
        let exTabs = document.querySelectorAll(".example-items");
        exTabs.forEach(et => {
            et.children[0].classList.remove("active");
        })
        el.children[0].classList.add("active");
    }

    function fieldsetToggler(el) {
        let fieldsetSchemaTypes = document.querySelectorAll("fieldset[data-schema-type]");
        let exTabs = document.querySelectorAll(".example-items");
        fieldsetSchemaTypes.forEach(fst => {
            fst.setAttribute("style", "display:none!important");
            if (fst.dataset.schemaType == el.dataset.schemaType) {
                fst.removeAttribute("style");
            }
        })
    }
</script>