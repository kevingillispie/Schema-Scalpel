<?php

/**
 * Pages schema tab.
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

$tab_name = 'pages';

$all_pages           = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE post_type='page' ORDER BY post_title ASC;", $wpdb->prefix . 'posts' ), ARRAY_A );
$schema_results      = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE schema_type='pages';", $wpdb->prefix . 'scsc_custom_schemas' ), ARRAY_A );
$existing_schema_ids = array();
foreach ( $schema_results as $key => $value ) :
	array_push( $existing_schema_ids, $schema_results[ $key ]['post_id'] );
endforeach;

echo '<div class="mb-5">';
echo new HTML_Refactory(
	'h2',
	array(),
	esc_html( 'Page Schema' )
);

$options = '';
$options = new HTML_Refactory(
	'option',
	array( 'default' => true ),
	sanitize_text_field( 'Choose a page to view or edit...' )
);


foreach ( $all_pages as $key => $value ) {

	if ( $all_pages[ $key ]['ID'] !== get_option( 'page_on_front' ) ) {

		$classes = '';
		if ( in_array( $all_pages[ $key ]['ID'], $existing_schema_ids ) ) {
			$classes = 'fw-bold';
		}
		$options .= new HTML_Refactory(
			'option',
			array(
				'class' => array( sanitize_html_class( $classes ) ),
				'value' => sanitize_text_field( $all_pages[ $key ]['ID'] ),
			),
			sanitize_text_field( $all_pages[ $key ]['ID'] ) . ': ' . sanitize_text_field( $all_pages[ $key ]['post_title'] )
		);

	}
}

echo new HTML_Refactory(
	'select',
	array(
		'id'    => 'pages_list',
		'class' => array( 'form-select', 'py-2' ),
	),
	'',
	$options
);

echo '<div id="pages_schema">';

$formatted_pre_tags = '';
if ( $schema_results ) :

	foreach ( $schema_results as $key => $value ) :
		$wp_post_id = $schema_results[ $key ]['post_id'];
		$post_title = '';
		foreach ( $all_pages as $k => $v ) :
			if ( $all_pages[ $k ]['ID'] == $wp_post_id ) :
				$post_title = $all_pages[ $k ]['post_title'];
				endif;
			endforeach;
		$no_cereal           = unserialize( $schema_results[ $key ]['custom_schema'] );
		$formatted_pre_tags .= new HTML_Refactory(
			'pre',
			array(
				'class'        => array( 'w-100', 'rounded', 'd-none', 'post-id-' . $wp_post_id, 'edit-block', 'language-json' ),
				'data-id'      => sanitize_text_field( $schema_results[ $key ]['id'] ),
				'data-post-id' => sanitize_text_field( $wp_post_id ),
				'data-schema'  => esc_html( $no_cereal ),
			)
		);

	endforeach;

endif;

echo new HTML_Refactory(
	'fieldset',
	array( 'class' => array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-5' ) ),
	'',
	new HTML_Refactory(
		'legend',
		array(
			'class' => array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ),
			'style' => 'width:auto',
		),
		esc_html( 'Current:' )
	) . new HTML_Refactory(
		'div',
		array( 'id' => 'current_pages_schema' ),
		'',
		$formatted_pre_tags
	)
);

$current_partial_name = __FILE__;
require 'scsc-create-new-schema.php';

echo '</div></div>';
