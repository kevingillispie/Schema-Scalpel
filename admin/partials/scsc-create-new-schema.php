<?php
/**
 * Create new schema tab.
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

global $set_tab, $tab_label, $index;
$tab_label  = array( 'homepage', 'global', 'pages', 'posts' );
$index      = '';
$wp_post_id = '-1';

if ( isset( $current_partial_name ) ) {
	foreach ( $tab_label as $key => $value ) {
		if ( strpos( $current_partial_name, $value ) ) {
			$index = $key;
			break;
		}
	}
}

if ( 'global' !== $tab_label[ $index ] && 'homepage' !== $tab_label[ $index ] ) {
	$wp_post_id = 'this.dataset.id';
}

if ( isset( $_GET['set_tab'] ) ) {
	$set_tab = \sanitize_key( $_GET['set_tab'] );
} else {
	$set_tab = 'homepage';
}

echo ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'mt-3' ) )
	->child(
		( new HTML_Refactory( 'button' ) )
			->attr( 'id', sanitize_text_field( $tab_label[ $index ] ) . '_create_schema_code_block' )
			->attr( 'type', 'button' )
			->attr( 'class', array( 'edit-block-button', 'btn', 'bg-success', 'text-white', 'px-5' ) )
			->attr( 'data-bs-toggle', 'modal' )
			->attr( 'data-bs-target', '#schemaBlockCreateModal' )
			->attr( 'data-id', sanitize_text_field( ( 'homepage' === $set_tab || 'global' === $set_tab ) ? '-1' : '' ) )
			->attr( sanitize_text_field( ( 'global' !== $tab_label[ $index ] && 'homepage' !== $tab_label[ $index ] ) ? 'disabled="true"' : null ), '' )
			->text( 'Create New Schema' )
			->render()
	)
	->render();
