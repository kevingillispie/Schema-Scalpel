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

global $wpdb, $prefix;
$prefix  = $wpdb->prefix;
$results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s;', $prefix . 'scsc_custom_schemas' ), ARRAY_A );

echo '<main class="container mt-5 ms-0">';

$scalpel = new HTML_Refactory(
	'img',
	array(
		'src'   => plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/scalpel_title.svg',
		'class' => array( 'mt-n4' ),
	),
);

$h1 = new HTML_Refactory(
	'h1',
	array(),
	'User Tools ',
	$scalpel
);

echo new HTML_Refactory(
	'header',
	array(),
	'',
	$h1
);

echo new HTML_Refactory(
	'hr',
	array(),
);

echo '<div style="max-height:500px;overflow-y:auto;">';

echo new HTML_Refactory(
	'h2',
	array(),
	'Database Contents'
);

echo new HTML_Refactory(
	'p',
	array(),
	'This table lists all of your custom schema available for export.'
);

echo '<table id="excluded_schema" class="table table-dark">';

$th        = '';
$col_names = array( 'ID', 'Created On', 'Modified On', 'Schema Type', 'Post ID', 'Schema' );
foreach ( $col_names as $name ) {
	$th .= new HTML_Refactory(
		'th',
		array(
			'scope' => 'col',
		),
		$name
	);
}

$tr = new HTML_Refactory(
	'tr',
	array(),
	'',
	$th
);

echo new HTML_Refactory(
	'thead',
	array(),
	'',
	$tr
);

echo '<tbody>';

if ( $results ) {

	$col_data = array( 'id', 'created', 'updated', 'schema_type', 'post_id', 'trunked' );

	foreach ( $results as $key => $value ) {

		if ( 'example' !== $results[ $key ]['schema_type'] ) {

			$no_cereal = unserialize( $results[ $key ]['custom_schema'] );
			$no_cereal = str_replace( '&quot;', '"', $no_cereal );
			$trunked   = ( strlen( $no_cereal ) > 100 ) ? substr( $no_cereal, 0, 100 ) . ' . . .' : $no_cereal;
			$cols      = '';


			foreach ( $col_data as $col_key => $col_value ) {
				$tag_name = ( 0 === $col_key ) ? 'th' : 'td';
				$attr     = ( 0 === $col_key ) ? array( 'scope' => 'row' ) : array( 'data-empty' => '' );
				$cols    .= new HTML_Refactory(
					$tag_name,
					$attr,
					( 'trunked' === $col_value ) ? $trunked : $results[ $key ][ $col_value ]
				);
			}


			echo new HTML_Refactory(
				'tr',
				array(),
				'',
				$cols
			);
		}
	}
}

echo '</tbody></table></div>';

echo new HTML_Refactory(
	'hr',
	array()
);

echo new HTML_Refactory(
	'h2',
	array(),
	'Export Tool'
);

echo '<fieldset class="d-flex flex-column justify-content-between bg-light border rounded p-3 mt-5">';

echo new HTML_Refactory(
	'legend',
	array(
		'class' => array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ),
		'style' => 'width:auto',
	),
	'SQL:'
);


$code = new HTML_Refactory(
	'code',
	array(
		'class' => array( 'rounded' ),
	),
	esc_html( $prefix )
);

echo new HTML_Refactory(
	'p',
	array( 'class' => array( 'h5' ) ),
	'Current WordPress Database Prefix: ',
	$code
);

echo new HTML_Refactory(
	'p',
	array(),
	"Use this tool to export your schema as a SQL query. Simply copy and paste the code below into your database's query editor and run it."
);

echo new HTML_Refactory(
	'hr',
	array( 'class' => array( 'w-100' ) ),
);

$italic = new HTML_Refactory(
	'i',
	array(),
	'Type the prefix of the WordPress database to which the schema is being transferred:'
);

echo new HTML_Refactory(
	'label',
	array( 'for' => 'wpdb_prefix' ),
	'',
	$italic
);

echo new HTML_Refactory(
	'input',
	array(
		'id'          => 'wpdb_prefix',
		'type'        => 'text',
		'placeholder' => esc_attr( $prefix ),
	)
);

echo new HTML_Refactory(
	'button',
	array(
		'id'    => 'copy-sql',
		'class' => array( 'btn', 'btn-primary', 'py-2', 'mt-2' ),
	),
	'Copy SQL to Clipboard'
);

if ( $results ) {

	$code_tags = '';
	foreach ( $results as $key => $value ) {
		if ( $results[ $key ]['schema_type'] != 'example' ) {
			$escaped = htmlentities( $results[ $key ]['custom_schema'] );
			$span    = new HTML_Refactory(
				'span',
				array( 'class' => array( 'wpdb-prefix' ) ),
				esc_html( $prefix )
			);

			$code_tags .= new HTML_Refactory(
				'code',
				array( 'class' => array( 'd-inline-block', 'py-2', 'px-1', 'w-100' ) ),
				'INSERT INTO ' . $span . 'scsc_custom_schemas (`schema_type`, `post_id`, `custom_schema`) VALUES (' . esc_html( $results[ $key ]['schema_type'] ) . ', ' . esc_html( $results[ $key ]['post_id'] ) . ', ' . esc_html( $escaped ),
			) . new HTML_Refactory(
				'br',
				array(),
				'',
				'',
				true
			);
		}
	}

	echo new HTML_Refactory(
		'pre',
		array( 'class' => array( 'rounded' ) ),
		'',
		$code_tags
	);

}

echo '</fieldset></main>';

add_action(
	'admin_footer',
	function () {
		global $prefix;
		echo '<script>';
		/**
		 * Load order matters.
		 */
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/prism.js';
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/bootstrap.min.js';
		echo '</script>';
		echo <<<SCRIPTS
        <script>
            function updatePrefix(el) {
                let newPrefix = (el.value == "") ? "{$prefix}" : el.value;
                document.querySelectorAll('.wpdb-prefix').forEach(prefix => {
                    prefix.innerText = newPrefix;
                });
            }

            function copySchema(sib) {
                navigator.clipboard.writeText(sib.innerText);
            }

            ! function (){
                document.getElementById('copy-sql').addEventListener('click', (e)=>{
                    copySchema(e.target.nextElementSibling);
                });
                document.getElementById('wpdb_prefix').addEventListener('keyup', (e) => {
                    updatePrefix(e.target);
                });
            }();
        </script>
SCRIPTS;
	}
);
