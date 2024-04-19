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

$schema_results      = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %1s WHERE schema_type='posts';", $wpdb->prefix . 'scsc_custom_schemas' ), ARRAY_A );
$existing_schema_ids = array();

foreach ( $schema_results as $key => $value ) {
	array_push( $existing_schema_ids, $schema_results[ $key ]['post_id'] );
}

echo '<div class="mb-5">';
echo new HTML_Refactory(
	'h2',
	array(),
	esc_html( 'Post Schema' )
);

$options = '';
$options = new HTML_Refactory(
	'option',
	array( 'default' => true ),
	sanitize_text_field( 'Choose a post to view or edit...' )
);

foreach ( $all_posts as $key => $value ) :
	$classes = '';
	if ( in_array( $all_posts[ $key ]['ID'], $existing_schema_ids ) ) :
		$classes = true;
	endif;
	$page_id    = $all_posts[ $key ]['ID'];
	$page_title = $all_posts[ $key ]['post_title'];
	if ( stripos( strtolower( $page_title ), 'auto draft' ) > -1 ) {
		continue;
	}

	$options .= new HTML_Refactory(
		'option',
		array(
			'class' => array( ( $classes ? 'fw-bold' : '' ) ),
			'value' => sanitize_text_field( $page_id ),
		),
		sanitize_text_field( $page_id . ':' . $page_title )
	);

endforeach;

echo new HTML_Refactory(
	'select',
	array(
		'id'    => 'posts_list',
		'class' => array( 'form-select', 'py-2' ),
	),
	'',
	$options
);

echo '<div id="' . esc_attr( $tab_name ) . '_schema">';

global $wpdb;
$get_schema  = "SELECT * FROM {$wpdb->prefix}scsc_custom_schemas WHERE schema_type = 'posts';";
$post_schema = '';
if ( $schema_results ) :
	foreach ( $schema_results as $key => $value ) :
		$wp_post_id   = $schema_results[ $key ]['post_id'];
		$no_cereal    = unserialize( $schema_results[ $key ]['custom_schema'] );
		$post_schema .= new HTML_Refactory(
			'pre',
			array(
				'class'        => array( 'w-100', 'rounded', 'language-json', 'd-none', 'edit-block', 'post-id-' . esc_attr( $wp_post_id ) ),
				'data-id'      => esc_attr( $schema_results[ $key ]['id'] ),
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
