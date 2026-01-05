<?php
/**
 * Homepage schema tab.
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

$tab_name = 'homepage';

echo '<div class="d-flex flex-column">';

echo new HTML_Refactory(
	'h2',
	array(),
	esc_html( ucfirst( $tab_name ) . ' Schema' )
);

echo new HTML_Refactory(
	'p',
	array(
		'class' => array( 'alert', 'alert-info', 'mb-0' ),
		'role'  => 'alert',
	),
	esc_html( 'Create schema to appear on the main page of your site.' )
);

echo '<div id="' . esc_attr( $tab_name ) . '_schema">';

global $wpdb;
$table         = $wpdb->prefix . 'scsc_custom_schemas';
$front_page_id = (int) get_option( 'page_on_front' );

if ( $front_page_id > 0 ) {
	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM %i 
             WHERE schema_type = %s 
                OR (schema_type = 'pages' AND post_id = %d)
             ORDER BY id DESC",
			$table,
			$tab_name,
			$front_page_id
		),
		ARRAY_A
	);
} else {
	$results = $wpdb->get_results(
		$wpdb->prepare(
			'SELECT * FROM %i WHERE schema_type = %s ORDER BY id DESC',
			$table,
			$tab_name
		),
		ARRAY_A
	);
}

$rendered_pres = '';

if ( $results ) {
	foreach ( $results as $key => $value ) {
		$wet_cereal     = maybe_unserialize( $results[ $key ]['custom_schema'] );
		$rendered_pres .= new HTML_Refactory(
			'pre',
			array(
				'class'       => array( 'w-100', 'rounded', 'language-json', 'edit-block', 'post-id-0' ),
				'data-id'     => $results[ $key ]['id'],
				'data-schema' => $wet_cereal,
			)
		);
	}
}

echo new HTML_Refactory(
	'fieldset',
	array( 'class' => array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-5' ) ),
	'',
	new HTML_Refactory(
		'legend',
		array(
			'class' => array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ),
			'style' => 'width:auto', // Fixed typo: wdith → width
		),
		esc_html( 'Current:' )
	) . new HTML_Refactory(
		'div',
		array( 'id' => 'current_' . esc_attr( $tab_name ) . '_schema' ),
		'',
		$rendered_pres
	)
);

$current_partial_name = __FILE__;
require 'scsc-create-new-schema.php';

echo '</div></div>';
