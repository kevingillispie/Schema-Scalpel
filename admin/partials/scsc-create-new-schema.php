<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) exit();

$set_tab;
$tab_name = ["homepage", "global", "pages", "posts"];
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

if ($tab_name[$index] != "global" && $tab_name[$index] != "homepage") :
    $post_id = "this.dataset.id";
endif;

if (isset($_GET['set_tab'])) :
    $set_tab = sanitize_key($_GET['set_tab']);
else :
    $set_tab = "homepage";
endif;

?>
<div class="mt-3">
    <button id="<?= sanitize_text_field($tab_name[$index]); ?>_edit_schema_code_block" type="button" class="edit-block-button btn bg-success text-white px-5" onclick="editSchemaCodeBlock('<?= sanitize_text_field($set_tab); ?>', 'new', this.dataset.id, event)" data-bs-toggle="modal" data-bs-target="#schemaBlockEditModal" data-id="<?= sanitize_text_field(($set_tab == "homepage" || $set_tab == "global") ? "-1" : ""); ?>" <?= sanitize_text_field(($tab_name[$index] != "global" && $tab_name[$index] != "homepage") ? 'disabled="true"' : null); ?>>Create New Schema</button>
</div>