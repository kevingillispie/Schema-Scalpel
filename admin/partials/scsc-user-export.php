<?php
/**
 * Export schema as SQL tool.
 *
 * @link       https://schemascalpel.com/
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin/partials
 */

namespace SchemaScalpel;

use HTML_Refactory;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $wpdb;
$prefix     = $wpdb->prefix;
$table_name = $prefix . 'scsc_custom_schemas';
$results    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name}" ), ARRAY_A );

$header = ( new HTML_Refactory( 'header' ) )
	->attr( 'class', array( 'mt-3', 'mb-2', 'd-flex', 'justify-content-center' ) )
	->child(
		( new HTML_Refactory( 'img' ) )
			->attr( 'src', plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/schema-scalpel-logo.svg' )
			->attr( 'width', '300' )
			->attr( 'height', 'auto' )
			->attr( 'style', 'z-index:99' )
			->attr( 'alt', 'Schema Scalpel Logo' )
			->render()
	)
	->render();

$scalpel_icon = ( new HTML_Refactory( 'img' ) )
	->attr( 'src', esc_url( plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/scalpel_title.svg' ) )
	->attr( 'class', array( 'mt-n4', 'position-absolute' ) )
	->render();

$page_title = ( new HTML_Refactory( 'div' ) )
	->child(
		( new HTML_Refactory( 'h1' ) )
			->text( 'User Tools ' )
			->child( $scalpel_icon )
			->render()
	)
	->render();

// Table header.
$th_tags   = '';
$col_names = array( 'ID', 'Created On', 'Modified On', 'Schema Type', 'Post ID', 'Schema' );

foreach ( $col_names as $name ) {
	$th_tags .= ( new HTML_Refactory( 'th' ) )
		->attr( 'scope', 'col' )
		->text( $name )
		->render();
}

$table_head = ( new HTML_Refactory( 'thead' ) )
	->child(
		( new HTML_Refactory( 'tr' ) )
			->child( $th_tags )
			->render()
	)
	->render();

$table_rows = '';

if ( $results ) {
	foreach ( $results as $row ) {
		if ( 'example' === ( $row['schema_type'] ?? '' ) ) {
			continue;
		}

		// Safely unserialize and prepare truncated display.
		$json_string = '';
		if ( ! empty( $row['custom_schema'] ) ) {
			$unserialized = unserialize( $row['custom_schema'] );
			if ( false !== $unserialized ) {
				$json_string = str_replace( '&quot;', '"', $unserialized );
			}
		}
		$truncated = strlen( $json_string ) > 100
			? substr( $json_string, 0, 100 ) . ' . . .'
			: $json_string;

		$cells   = '';
		$columns = array(
			'id'          => $row['id'] ?? '',
			'created'     => $row['created'] ?? '',
			'updated'     => $row['updated'] ?? '',
			'schema_type' => $row['schema_type'] ?? '',
			'post_id'     => $row['post_id'] ?? '(global)',
			'trunked'     => $truncated,
		);

		foreach ( $columns as $key => $value ) {
			// Ensure $value is always a string.
			$value = (string) $value;

			$cell = ( new HTML_Refactory( $key === 'id' ? 'th' : 'td' ) )
				->text( $value );

			if ( 'id' === $key ) {
				$cell->attr( 'scope', 'row' );
			}

			$cells .= $cell->render();
		}

		$table_rows .= ( new HTML_Refactory( 'tr' ) )
			->child( $cells )
			->render();
	}
}

$table_body = ( new HTML_Refactory( 'tbody' ) )
	->child( $table_rows )
	->render();

$db_contents_table = ( new HTML_Refactory( 'table' ) )
	->attr( 'id', 'excluded_schema' )
	->attr( 'class', array( 'table', 'table-dark' ) )
	->child( $table_head )
	->child( $table_body )
	->render();

$table_container = ( new HTML_Refactory( 'div' ) )
	->attr( 'style', 'max-height:500px;overflow-y:auto;z-index:99' )
	->attr( 'class', array( 'bg-light', 'border', 'rounded p-3', 'position-relative' ) )
	->child( $db_contents_table )
	->render();

$fieldset = ( new HTML_Refactory( 'fieldset' ) )
	->attr( 'class', array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-5' ) )
	->child(
		( new HTML_Refactory( 'legend' ) )
			->attr( 'class', array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ) )
			->attr( 'style', 'width:auto' )
			->text( 'SQL:' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'p' ) )
			->attr( 'class', array( 'h5' ) )
			->child( 'Current WordPress Database Prefix: ' )
			->child(
				( new HTML_Refactory( 'code' ) )
					->attr( 'class', array( 'rounded' ) )
					->text( $prefix )
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'p' ) )
			->text( "Use this tool to export your schema as a SQL query. Simply copy and paste the code below into your database's query editor and run it." )
			->render()
	)
	->child( ( new HTML_Refactory( 'hr' ) )->attr( 'class', array( 'w-100' ) )->render() )
	->child(
		( new HTML_Refactory( 'label' ) )
			->attr( 'for', 'wpdb_prefix' )
			->child(
				( new HTML_Refactory( 'i' ) )
					->text( 'Type the prefix of the WordPress database to which the schema is being transferred:' )
					->render()
			)
			->render()
	)
	->child(
		( new HTML_Refactory( 'input' ) )
			->attr( 'id', 'wpdb_prefix' )
			->attr( 'type', 'text' )
			->attr( 'placeholder', esc_attr( $prefix ) )
			->render()
	)
	->child(
		( new HTML_Refactory( 'button' ) )
			->attr( 'id', 'copy-sql' )
			->attr( 'class', array( 'btn', 'btn-primary', 'py-2', 'mt-2' ) )
			->text( 'Copy SQL to Clipboard' )
			->render()
	)
	->child(
		// Only add the <pre> block if there are actual schemas to export.
		( function () use ( $results, $prefix ) {
			$inserts = '';
			foreach ( $results as $row ) {
				if ( 'example' === ( $row['schema_type'] ?? '' ) ) {
					continue;
				}

				$schema_type = $row['schema_type'] ?? '';
				$post_id     = $row['post_id'] ?? '';
				$raw_schema  = $row['custom_schema'] ?? '';

				// Only process if there's actual schema data.
				if ( '' === $raw_schema ) {
					continue;
				}

				$unserialized = unserialize( $raw_schema );
				if ( $unserialized === false ) {
					continue; // skip corrupted data.
				}

				$escaped = htmlentities( $unserialized, ENT_QUOTES, 'UTF-8' );

				$inserts .= ( new HTML_Refactory( 'code' ) )
					->attr( 'class', array( 'd-inline-block', 'py-2', 'px-1', 'w-100' ) )
					->child( 'INSERT INTO `' )
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr( 'class', array( 'wpdb-prefix' ) )
						->text( $prefix )
						->render()
					)
					->child( "scsc_custom_schemas` (`schema_type`, `post_id`, `custom_schema`) VALUES ('" )
					->child( esc_html( $schema_type ) )
					->child( "', '" )
					->child( esc_html( $post_id ) )
					->child( "', '" )
					->child( esc_html( $escaped ) )
					->child( "');" )
					->render()
					. ( new HTML_Refactory( 'br' ) )->render();
			}

			if ( '' === $inserts ) {
				return ( new HTML_Refactory( 'p' ) )
					->attr( 'class', array( 'text-muted', 'mt-3' ) )
					->text( 'No custom schemas found to export.' )
					->render();
			}

			return ( new HTML_Refactory( 'pre' ) )
				->attr( 'class', array( 'rounded', 'mt-3' ) )
				->child( $inserts )
				->render();
		} )()
	)
	->render();

$main = ( new HTML_Refactory( 'main' ) )
	->attr( 'class', array( 'container', 'my-5' ) )
	->child( $page_title )
	->child( ( new HTML_Refactory( 'hr' ) )->render() )
	->child(
		( new HTML_Refactory( 'h2' ) )
			->text( 'Database Contents' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'p' ) )
			->text( 'This table lists all of your custom schema available for export.' )
			->render()
	)
	->child( $table_container )
	->child( ( new HTML_Refactory( 'hr' ) )->render() )
	->child(
		( new HTML_Refactory( 'h2' ) )
			->text( 'Export Tool' )
			->render()
	)
	->child( $fieldset )
	->render();

echo ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'container', 'pt-3' ) )
	->child( $header )
	->child( $main )
	->render();

// Admin footer scripts.
add_action(
	'admin_footer',
	function () use ( $prefix ) {
		echo '<script>';
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/prism.js';
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/bootstrap.min.js';
		echo '</script>';

		echo <<<SCRIPTS
<script>
    function updatePrefix(el) {
        let newPrefix = (el.value === "") ? "{$prefix}" : el.value;
        document.querySelectorAll('.wpdb-prefix').forEach(prefix => {
            prefix.innerText = newPrefix;
        });
    }

    function copySchema(sib) {
        navigator.clipboard.writeText(sib.innerText);
    }

    !function() {
        const copyBtn = document.getElementById('copy-sql');
        const prefixInput = document.getElementById('wpdb_prefix');

        if (copyBtn) {
            copyBtn.addEventListener('click', (e) => {
                const pre = document.querySelector('fieldset pre');
                if (pre) {
                    navigator.clipboard.writeText(pre.innerText);
                }
            });
        }

        if (prefixInput) {
            prefixInput.addEventListener('keyup', (e) => {
                updatePrefix(e.target);
            });
        }
    }();
</script>
<script>
SCRIPTS;
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/anime.js';
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/scsc-schema-animation.js';
		echo '</script>';
	}
);
