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

$all_pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE post_type='page' ORDER BY post_title ASC;", $wpdb->prefix . 'posts' ), \ARRAY_A );

$schema_results_pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE schema_type='pages';", $wpdb->prefix . 'scsc_custom_schemas' ), \ARRAY_A );
$existing_schema_ids  = array();

foreach ( $schema_results_pages as $key => $value ) :
	array_push( $existing_schema_ids, $schema_results_pages[ $key ]['post_id'] );
endforeach;

echo '<div class="mb-5">';
echo new HTML_Refactory(
	'h2',
	array(),
	\esc_html( 'Page Schema' )
);

$list_of_pages = '';
$menu_of_pages = new HTML_Refactory(
	'li',
	array( 'class' => array( 'dropdown-item' ) ),
	'',
	new HTML_Refactory(
		'input',
		array(
			'id'          => $tab_name . '_filter',
			'class'       => array( 'form-control' ),
			'type'        => 'text',
			'name'        => 'filter-pages',
			'placeholder' => 'Type to filter pages...',
		)
	)
);

foreach ( $all_pages as $key => $value ) {

	if ( get_option( 'page_on_front' ) !== $all_pages[ $key ]['ID'] ) {

		$classes = '';
		if ( in_array( $all_pages[ $key ]['ID'], $existing_schema_ids ) ) {
			$classes = 'fw-bold';
		}
		$page_id        = $all_pages[ $key ]['ID'];
		$page_title     = $all_pages[ $key ]['post_title'];
		$menu_of_pages .= new HTML_Refactory(
			'li',
			array(
				'class'      => array( 'dropdown-item', sanitize_html_class( $classes ) ),
				'data-value' => sanitize_text_field( $page_id ),
				'data-index' => $key,
				'data-title' => $page_title,
			),
			sanitize_text_field( $page_id ) . ': ' . sanitize_text_field( $page_title )
		);
	}
}

$list_id = esc_attr( $tab_name ) . '_list';
echo <<<MENU
<div class="btn-group w-100">
    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        SELECT PAGE
    </button>
    <ul id="$list_id" class="dropdown-menu w-100">
        $menu_of_pages
    </ul>
</div>
MENU;

echo '<div id="' . esc_attr( $tab_name ) . '_schema">';

$formatted_pre_tags = '';
if ( $schema_results_pages ) :

	foreach ( $schema_results_pages as $key => $value ) :
		$wp_post_id = $schema_results_pages[ $key ]['post_id'];
		$post_title = '';
		foreach ( $all_pages as $k => $v ) :
			if ( $all_pages[ $k ]['ID'] === $wp_post_id ) :
				$post_title = $all_pages[ $k ]['post_title'];
				endif;
			endforeach;
		$no_cereal           = unserialize( $schema_results_pages[ $key ]['custom_schema'] );
		$formatted_pre_tags .= new HTML_Refactory(
			'pre',
			array(
				'class'        => array( 'w-100', 'rounded', 'd-none', 'post-id-' . $wp_post_id, 'edit-block', 'language-json' ),
				'data-id'      => sanitize_text_field( $schema_results_pages[ $key ]['id'] ),
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
		\esc_html( 'Current:' )
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
