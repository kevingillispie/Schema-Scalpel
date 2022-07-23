<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    exit('First of all, how dare you!');
endif;

$set_tab;
$tab_name = ["home", "global", "pages", "posts"];
$index = "";
$post_id = "-1";
if (isset($current_partial_name)) :
    foreach ($tab_name as $key => $value) :
        if (strpos($current_partial_name, $value)) :
            $index = $key;
            break;
        endif;
    endforeach;
endif;

if ($tab_name[$index] != "global" && $tab_name[$index] != "home") :
    $post_id = "this.dataset.id";
endif;

if (isset($_GET['set_tab'])):
    $set_tab = sanitize_key($_GET['set_tab']);
else:
    $set_tab = "home";
endif;
?>

<fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-5">
    <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">Create:</legend>
    <div class="d-flex flex-row justify-content-between">
        <button id="<?php echo sanitize_text_field($tab_name[$index]); ?>_edit_schema_code_block" type="button" class="edit-block-button btn bg-success text-white px-5" onclick="editSchemaCodeBlock('<?php echo sanitize_text_field($set_tab); ?>', 'new', this.dataset.id, event)" data-bs-toggle="modal" data-bs-target="#schemaBlockEditModal" data-id="<?php echo sanitize_text_field(($set_tab == "home" || $set_tab == "global") ? "-1": ""); ?>" <?php echo sanitize_text_field(($tab_name[$index] != "global" && $tab_name[$index] != "home") ? 'disabled="true"' : null); ?>>Edit Code Block</button>
        <div class="d-flex flex-row">
            <div class="insert-line-label btn-secondary py-2 px-3 rounded-left text-white text-center">Insert Line After</div>
            <input type="text" id="<?php echo sanitize_text_field($tab_name[$index]); ?>_add_new_line_after" placeholder="Line #" class="rounded-0 border-secondary bg-light" style="width: 60px" />
            <button id="<?php echo sanitize_text_field($tab_name[$index]); ?>_add_new_line" class="btn btn-secondary rounded-0" style="border-top-right-radius:3px!important;border-bottom-right-radius:3px!important" onClick="addLine('new', '<?php echo sanitize_text_field($tab_name[$index]); ?>', event)">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                </svg>
            </button>
        </div>
    </div>
    <div id="<?php echo sanitize_text_field($tab_name[$index]); ?>_new_schema_editor" class="d-flex flex-row">
        <pre id="<?php echo sanitize_text_field($tab_name[$index]); ?>_schema_preview" class="language-json rounded w-100" data-id="new" data-create="new">
<code class="d-inline-block w-100" onmouseenter="addEditButtons(this)" onmouseleave="removeEditButtons(this)">{</code>
<code class="d-inline-block w-100" onmouseenter="addEditButtons(this)" onmouseleave="removeEditButtons(this)"></code>
<code class="d-inline-block w-100" onmouseenter="addEditButtons(this)" onmouseleave="removeEditButtons(this)">}</code>
</pre>
    </div>
    <div>
        <button id="<?php echo sanitize_text_field($tab_name[$index]); ?>_schema_create" class="btn btn-primary py-2 px-5" type="submit" onclick="createNewSchema(<?php echo esc_html('&apos;' . sanitize_text_field($set_tab) . '&apos;,' . $post_id); ?>)" <?php echo sanitize_text_field(($tab_name[$index] != "global" && $tab_name[$index] != "home") ? 'disabled="true"' : ''); ?>>Create</button>
    </div>
</fieldset>