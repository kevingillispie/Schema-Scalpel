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

$all_pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE post_type='page' ORDER BY post_title ASC;", $wpdb->prefix . 'posts' ), ARRAY_A );

$schema_results_pages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE schema_type='pages';", $wpdb->prefix . 'scsc_custom_schemas' ), ARRAY_A );
$existing_schema_ids  = array();

foreach ( $schema_results_pages as $key => $value ) :
	array_push( $existing_schema_ids, $schema_results_pages[ $key ]['post_id'] );
endforeach;

echo '<div class="mb-5">';

echo ( new HTML_Refactory( 'h2' ) )
	->text( 'Page Schema' )
	->render();

$list_of_pages = '';

$menu_of_pages = ( new HTML_Refactory( 'li' ) )
	->attr( 'class', array( 'dropdown-item' ) )
	->child(
		( new HTML_Refactory( 'input' ) )
			->attr( 'id', $tab_name . '_filter' )
			->attr( 'class', array( 'form-control' ) )
			->attr( 'type', 'text' )
			->attr( 'name', 'filter-pages' )
			->attr( 'placeholder', 'Type to filter pages...' )
			->render()
	)
	->render();

foreach ( $all_pages as $key => $value ) {

	if ( get_option( 'page_on_front' ) !== $all_pages[ $key ]['ID'] ) {

		$classes = '';
		if ( in_array( $all_pages[ $key ]['ID'], $existing_schema_ids ) ) {
			$classes = 'fw-bold';
		}
		$page_id        = $all_pages[ $key ]['ID'];
		$page_title     = $all_pages[ $key ]['post_title'];
		$menu_of_pages .= ( new HTML_Refactory( 'li' ) )
			->attr( 'class', array( 'dropdown-item', sanitize_html_class( $classes ) ) )
			->attr( 'data-value', sanitize_text_field( $page_id ) )
			->attr( 'data-index', $key )
			->attr( 'data-title', $page_title )
			->attr( 'data-type', 'page' )
			->text( sanitize_text_field( $page_id ) . ': ' . sanitize_text_field( $page_title ) )
			->render();
	}
}

$list_id = esc_attr( $tab_name ) . '_list';

echo ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'btn-group', 'w-100' ) )
	->child(
		( new HTML_Refactory( 'button' ) )
		->attr( 'class', array( 'btn', 'btn-primary', 'dropdown-toggle' ) )
		->attr( 'type', 'button' )
		->attr( 'data-bs-toggle', 'dropdown' )
		->attr( 'aria-expanded', 'false' )
		->child( 'SELECT PAGE' )
		->render()
	)
	->child(
		( new HTML_Refactory( 'ul' ) )
		->attr( 'id', $list_id )
		->attr( 'class', array( 'dropdown-menu', 'w-100' ) )
		->child( $menu_of_pages )
		->render()
	)
	->render();

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
		$formatted_pre_tags .= ( new HTML_Refactory( 'pre' ) )
			->attr( 'class', array( 'w-100', 'rounded', 'd-none', 'post-id-' . $wp_post_id, 'edit-block', 'language-json' ) )
			->attr( 'data-id', sanitize_text_field( $schema_results_pages[ $key ]['id'] ) )
			->attr( 'data-post-id', sanitize_text_field( $wp_post_id ) )
			->attr( 'data-schema', esc_html( $no_cereal ) )
			->render();

	endforeach;

endif;

echo ( new HTML_Refactory( 'fieldset' ) )
	->attr( 'class', array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-5' ) )
	->child(
		( new HTML_Refactory( 'legend' ) )
			->attr( 'class', array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ) )
			->attr( 'style', 'width:auto' )
			->text( 'Current:' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'id', 'current_pages_schema' )
			->child( $formatted_pre_tags )
			->render()
	)
	->render();

$current_partial_name = __FILE__;
require 'scsc-create-new-schema.php';

echo '</div></div>';
