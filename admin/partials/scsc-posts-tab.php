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

$all_posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE post_type='post' ORDER BY post_title ASC;", $wpdb->prefix . 'posts' ), ARRAY_A );

$schema_results_posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE schema_type='posts';", $wpdb->prefix . 'scsc_custom_schemas' ), ARRAY_A );
$existing_schema_ids  = array();

foreach ( $schema_results_posts as $key => $value ) :
	array_push( $existing_schema_ids, $schema_results_posts[ $key ]['post_id'] );
endforeach;

echo '<div class="mb-5">';
echo new HTML_Refactory(
	'h2',
	array(),
	esc_attr( 'Post Schema' )
);

$list_of_posts = '';
$menu_of_posts = new HTML_Refactory(
	'li',
	array( 'class' => array( 'dropdown-item' ) ),
	'',
	new HTML_Refactory(
		'input',
		array(
			'id'          => $tab_name . '_filter',
			'class'       => array( 'form-control' ),
			'type'        => 'text',
			'name'        => 'filter-posts',
			'placeholder' => 'Type to filter posts...',
		)
	)
);

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

	$menu_of_posts .= new HTML_Refactory(
		'li',
		array(
			'class'      => array( 'dropdown-item', sanitize_html_class( $classes ) ),
			'data-value' => sanitize_text_field( $blog_post_id ),
			'data-index' => $key,
			'data-title' => $blog_post_title,
			'data-type'  => 'post',
		),
		sanitize_text_field( $blog_post_id ) . ': ' . sanitize_text_field( $blog_post_title )
	);

endforeach;

$list_id = esc_attr( $tab_name ) . '_list';
echo <<<MENU
<div class="btn-group w-100">
    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        SELECT POST
    </button>
    <ul id="$list_id" class="dropdown-menu w-100">
        $menu_of_posts
    </ul>
</div>
MENU;

echo '<div id="' . esc_attr( $tab_name ) . '_schema">';

global $wpdb;
$get_schema  = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'posts';";
$post_schema = '';
if ( $schema_results_posts ) :
	foreach ( $schema_results_posts as $key => $value ) :
		$wp_post_id   = $schema_results_posts[ $key ]['post_id'];
		$no_cereal    = unserialize( $schema_results_posts[ $key ]['custom_schema'] );
		$post_schema .= new HTML_Refactory(
			'pre',
			array(
				'class'        => array( 'w-100', 'rounded', 'language-json', 'd-none', 'edit-block', 'post-id-' . esc_attr( $wp_post_id ) ),
				'data-id'      => esc_attr( $schema_results_posts[ $key ]['id'] ),
				'data-post-id' => esc_attr( $wp_post_id ),
				'data-schema'  => $no_cereal,
			),
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
		array( 'id' => 'current_' . esc_attr( $tab_name ) . '_schema' ),
		'',
		$post_schema
	)
);

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
