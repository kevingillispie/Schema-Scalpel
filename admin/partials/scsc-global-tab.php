<?php
/**
 * Global schema tab.
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

$tab_name = 'global';

echo '<div class="d-flex flex-column">';

echo ( new HTML_Refactory( 'h2' ) )
	->text( ucfirst( $tab_name ) . ' Schema' )
	->render();

echo ( new HTML_Refactory( 'p' ) )
	->attr( 'class', array( 'alert', 'alert-info', 'mb-0' ) )
	->attr( 'role', 'alert' )
	->text( 'Create schema to appear on every page of your site.' )
	->render();

echo '<div id="' . $tab_name . '_schema">';

$legend = ( new HTML_Refactory( 'legend' ) )
	->attr( 'class', array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ) )
	->attr( 'style', 'width:auto' )
	->text( 'Current:' )
	->render();

global $wpdb;
$results       = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i WHERE schema_type = %s;', $wpdb->prefix . 'scsc_custom_schemas', $tab_name ), ARRAY_A );
$rendered_pres = '';

if ( $results ) {
	foreach ( $results as $key => $value ) {
		$wet_cereal     = unserialize( $results[ $key ]['custom_schema'] );
		$rendered_pres .= ( new HTML_Refactory( 'pre' ) )
			->attr( 'class', array( 'position-relative', 'w-100', 'rounded', 'language-json', 'edit-block' ) )
			->attr( 'data-id', $results[ $key ]['id'] )
			->attr( 'data-schema', $wet_cereal )
			->render();
	}
}

$current_schema = ( new HTML_Refactory( 'div' ) )
	->attr( 'id', 'current_' . $tab_name . '_schema' )
	->child( $rendered_pres )
	->render();

echo ( new HTML_Refactory( 'fieldset' ) )
	->attr( 'class', array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'mt-5' ) )
	->child( $legend )
	->child( $current_schema )
	->render();

$current_partial_name = __FILE__;
require 'scsc-create-new-schema.php';

echo '</div></div>';
