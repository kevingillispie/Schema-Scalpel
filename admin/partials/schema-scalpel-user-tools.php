<?php

namespace SchemaScalpel;

if (!defined('ABSPATH')) :
    // If this file is called directly, EJECT EJECT EJECT!
    exit('First of all, how dare you!');
endif;

/**
 *
 * @link       https://schemascalpel.com/

 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin/partials
 */

global $wpdb;
$prefix = $wpdb->prefix;
$get_schema = "SELECT * FROM {$prefix}scsc_custom_schemas;";
$results = $wpdb->get_results($get_schema, ARRAY_A);

/**
 * 
 */

?>
<main class="container mt-5 ms-0">
    <header>
        <h1>User Tools <img src="<?php echo plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ); ?>admin/images/scalpel_title.svg" class="mt-n4" /></h1>
    </header>

    <hr />
    <div style="max-height:500px;overflow-y:auto;">
        <h2>Database Contents</h2>
        <p>This table lists all of your custom schema available for export.</p>
        <table id="excluded_schema" class="table table-dark">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Created On</th>
                    <th scope="col">Modified On</th>
                    <th scope="col">Schema Type</th>
                    <th scope="col">Post ID</th>
                    <th scope="col">Schema</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($results) :
                    foreach ($results as $key => $value) :
                        if ($results[$key]['schema_type'] != "example") :
                            $no_cereal = unserialize($results[$key]['custom_schema']);
                            $trunked = (strlen($no_cereal) > 100) ? substr($no_cereal, 0, 100) . " . . ." : $no_cereal;

                ?>
                            <tr>
                                <th scope="row"><?php echo sanitize_text_field($results[$key]['id']); ?></th>
                                <td><?php echo sanitize_text_field($results[$key]['created']); ?></td>
                                <td><?php echo sanitize_text_field($results[$key]['updated']); ?></td>
                                <td><?php echo sanitize_text_field($results[$key]['schema_type']); ?></td>
                                <td><?php echo sanitize_text_field($results[$key]['post_id']); ?></td>
                                <td><?php echo sanitize_text_field($trunked); ?></td>
                            </tr>
                <?php

                        endif;
                    endforeach;
                endif;

                ?>
            </tbody>
        </table>
    </div>
    <hr>
    <h2>Export Tool</h2>
    <fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-5">
        <legend class="px-3 pb-1 border rounded bg-white" style="width:auto">SQL:</legend>
        <p class="h5">Current WordPress Database Prefix: <code class="rounded"><?php echo sanitize_text_field($prefix); ?></code></p>
        <p>Use this tool to export your schema as a SQL query. Simply copy and paste the code below into your database's query editor and run it.</p>
        <hr class="w-100" />
        <label for="wpdb_prefix"><i>Type the prefix of the WordPress database to which the schema is being transferred:</i></label>
        <input id="wpdb_prefix" type="text" placeholder="<?php echo sanitize_text_field($prefix); ?>" onkeyup="updatePrefix(this)" />
        <button class="btn btn-primary py-2 mt-2" onclick="copySchema(this.nextElementSibling)">Copy SQL to Clipboard</button>
        <?php

        if ($results) :

        ?>
            <pre class="rounded"><?php

                foreach ($results as $key => $value) :
                    if ($results[$key]['schema_type'] != "example") :
                        $escaped = htmlentities($results[$key]['custom_schema']);

                ?><code class="d-inline-block py-2 px-1 w-100">INSERT INTO <span class="wpdb-prefix"><?php echo sanitize_text_field($prefix); ?></span>scsc_custom_schemas (`schema_type`, `post_id`, `custom_schema`) VALUES ('<?php echo sanitize_text_field($results[$key]['schema_type']); ?>', '<?php echo sanitize_text_field($results[$key]['post_id']); ?>', '<?php echo $escaped; ?>');</code><br /><?php

                    endif;
                endforeach;

                        ?>
                </pre>
        <?php

        endif;

        ?>
    </fieldset>
</main>

<script>
    <?php
    /**
     * LOAD ORDER MATTERS!
     */
    include_once( SCHEMA_SCALPEL_DIRECTORY . '/admin/js/prism.js');
    include_once( SCHEMA_SCALPEL_DIRECTORY . '/admin/js/bootstrap.min.js');
    ?>
</script>
<script>
    function updatePrefix(el) {
        let newPrefix = (el.value == "") ? "<?php echo sanitize_text_field($prefix); ?>" : el.value;
        document.querySelectorAll('.wpdb-prefix').forEach(prefix => {
            prefix.innerText = newPrefix;
        });
    }

    function copySchema(sib) {
        navigator.clipboard.writeText(sib.innerText);
    }
</script>