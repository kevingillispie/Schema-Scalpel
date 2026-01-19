<?php
/**
 * Posts schema tab.
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

$tab_name = 'posts';

$all_posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE post_type='post' ORDER BY post_title ASC;", $wpdb->prefix . 'posts' ), ARRAY_A );

$schema_results_posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE schema_type='posts';", $wpdb->prefix . 'scsc_custom_schemas' ), ARRAY_A );
$existing_schema_ids  = array();

foreach ( $schema_results_posts as $key => $value ) :
	array_push( $existing_schema_ids, $schema_results_posts[ $key ]['post_id'] );
endforeach;

echo '<div class="mb-5">';

echo ( new HTML_Refactory( 'h2' ) )
	->text( 'Post Schema' )
	->render();

$list_of_posts = '';

$menu_of_posts = ( new HTML_Refactory( 'li' ) )
	->attr( 'class', array( 'dropdown-item' ) )
	->child(
		( new HTML_Refactory( 'input' ) )
			->attr( 'id', $tab_name . '_filter' )
			->attr( 'class', 'form-control' )
			->attr( 'type', 'text' )
			->attr( 'name', 'filter-posts' )
			->attr( 'placeholder', 'Type to filter posts...' )
			->render()
	)
	->render();

foreach ( $all_posts as $key => $value ) :

	$classes = '';
	if ( true === in_array( $all_posts[ $key ]['ID'], $existing_schema_ids ) ) :
		$classes = 'fw-bold';
	endif;

	$blog_post_id    = $all_posts[ $key ]['ID'];
	$blog_post_title = $all_posts[ $key ]['post_title'];
	if ( stripos( strtolower( $blog_post_title ), 'auto draft' ) > -1 ) :
		continue;
	endif;

	$menu_of_posts .= ( new HTML_Refactory( 'li' ) )
		->attr( 'class', array( 'dropdown-item', sanitize_html_class( $classes ) ) )
		->attr( 'data-value', sanitize_text_field( $blog_post_id ) )
		->attr( 'data-index', $key )
		->attr( 'data-title', $blog_post_title )
		->attr( 'data-type', 'post' )
		->text( sanitize_text_field( $blog_post_id ) . ': ' . sanitize_text_field( $blog_post_title ) )
		->render();

endforeach;

$list_id = esc_attr( $tab_name ) . '_list';

echo ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'btn-group', 'w-100' ) )
	->child(
		( new HTML_Refactory( 'button' ) )
		->attr( 'class', array( 'btn', 'btn-primary', 'dropdown-toggle' ) )
		->attr( 'type', 'button' )
		->attr( 'data-bs-toggle', 'dropdown' )
		->attr( 'aria-expanded', 'false' )
		->child( 'SELECT POST' )
		->render()
	)
	->child(
		( new HTML_Refactory( 'ul' ) )
		->attr( 'id', $list_id )
		->attr( 'class', array( 'dropdown-menu', 'w-100' ) )
		->child( $menu_of_posts )
		->render()
	)
	->render();

echo '<div id="' . esc_attr( $tab_name ) . '_schema">';

global $wpdb;
$get_schema  = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'posts';";
$post_schema = '';
if ( $schema_results_posts ) :
	foreach ( $schema_results_posts as $key => $value ) :
		$wp_post_id   = $schema_results_posts[ $key ]['post_id'];
		$no_cereal    = unserialize( $schema_results_posts[ $key ]['custom_schema'] );
		$post_schema .= ( new HTML_Refactory( 'pre' ) )
			->attr( 'class', array( 'w-100', 'rounded', 'language-json', 'd-none', 'edit-block', 'post-id-' . esc_attr( $wp_post_id ) ) )
			->attr( 'data-id', esc_attr( $schema_results_posts[ $key ]['id'] ) )
			->attr( 'data-post-id', esc_attr( $wp_post_id ) )
			->attr( 'data-schema', $no_cereal )
			->render();
	endforeach;
endif;

echo ( new HTML_Refactory( 'fieldset' ) )
	->attr( 'class', array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-3' ) )
	->child(
		( new HTML_Refactory( 'legend' ) )
			->attr( 'class', array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ) )
			->attr( 'style', 'width:auto' )
			->text( 'Current:' )
			->render()
	)
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'id', 'current_' . esc_attr( $tab_name ) . '_schema' )
			->child( $post_schema )
			->render()
	)
	->render();


$current_partial_name = __FILE__;
require 'scsc-create-new-schema.php';

echo '</div></div>';

add_action(
	'admin_footer',
	function () {
		echo <<<SCRIPT
        <script class="scsc-footer">
            document.addEventListener("DOMContentLoaded", () => {
                var schemaPostTypeBtns = document.querySelectorAll('input[name="schemaPostType"]');
                var schemaAuthorTypeBtns = document.querySelectorAll('input[name="schemaAuthorType"]');
                
                schemaPostTypeBtns.forEach(btn => {
                    btn.addEventListener("change", () => {
                        let postTypeExample = document.querySelector("#schemaPostType span.token.string");
                        postTypeExample.innerText = '"' + btn.dataset.label + '"';
                    });
                });
                
                schemaAuthorTypeBtns.forEach(btn => {
                    btn.addEventListener("change", () => {
                        let schemaAuthorTypeExample = document.querySelector("#schemaAuthorType span.token.string");
                        schemaAuthorTypeExample.innerText = '"' + btn.dataset.label + '"';
                    });
                });
            });
        </script>
SCRIPT;
	}
);
