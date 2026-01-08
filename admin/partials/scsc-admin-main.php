<?php
/**
 * Primary UI for plugin admins.
 *
 * @link       https://schemascalpel.com/
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin/partials
 */

namespace SchemaScalpel;

use Dom\HTMLElement;
use HTML_Refactory;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $wpdb;
$custom_schemas_table = $wpdb->prefix . 'scsc_custom_schemas';

if ( isset( $_POST['create'] ) ) {

	$custom_schema = sanitize_text_field( wp_unslash( $_POST['create'] ) );
	$schema_type   = isset( $_POST['schemaType'] ) ? sanitize_text_field( wp_unslash( $_POST['schemaType'] ) ) : '';
	$post_id_num   = isset( $_POST['postID'] ) ? absint( wp_unslash( $_POST['postID'] ) ) : 0;

	// Validate inputs.
	if ( ! empty( $custom_schema ) && ! empty( $schema_type ) && $post_id_num > 0 ) {
		$wpdb->insert(
			$custom_schemas_table,
			array(
				'custom_schema' => serialize( $custom_schema ),
				'schema_type'   => $schema_type,
				'post_id'       => $post_id_num,
			),
			array(
				'%s', // custom_schema (serialized string).
				'%s', // schema_type (string).
				'%d', // post_id (integer).
			)
		);
	}
}

if ( isset( $_GET['generate'] ) ) {
	$update_type      = sanitize_text_field( wp_unslash( $_GET['updateType'] ) );
	$schema_post_type = sanitize_text_field( wp_unslash( $_GET['schemaPostType'] ) );
	$author_type      = sanitize_text_field( wp_unslash( $_GET['schemaAuthorType'] ) );
	$keywords         = isset( $_GET['keywords'] ) ? sanitize_text_field( wp_unslash( $_GET['keywords'] ) ) : false;
	SCSC_Admin::generate_blogposting_schema( $update_type, $schema_post_type, $author_type, $keywords );
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=posts">';
}

if ( isset( $_POST['update'] ) ) {
	$update_id     = absint( wp_unslash( $_POST['update'] ) );
	$custom_schema = isset( $_POST['schema'] ) ? sanitize_text_field( wp_unslash( $_POST['schema'] ) ) : '';

	// Validate inputs.
	if ( $update_id > 0 && ! empty( $custom_schema ) ) {
		$wpdb->update(
			$custom_schemas_table,
			array(
				'custom_schema' => serialize( $custom_schema ),
			),
			array(
				'id' => $update_id,
			),
			array(
				'%s', // custom_schema (serialized string).
			),
			array(
				'%d', // id (integer).
			)
		);
	}
}

if ( isset( $_GET['delete'] ) ) {
	$wpdb->delete( $custom_schemas_table, array( 'id' => sanitize_text_field( wp_unslash( $_GET['delete'] ) ) ) );
}

/**
 * GET/SET TABS
 */
$settings_table = $wpdb->prefix . 'scsc_settings';
if ( isset( $_GET['active_page'] ) ) {
	$active_page = sanitize_text_field( wp_unslash( $_GET['active_page'] ) );
	$wpdb->update( $settings_table, array( 'setting_value' => "$active_page" ), array( 'setting_key' => 'active_page' ) );
	exit();
}

if ( isset( $_GET['active_post'] ) ) {
	$active_post = sanitize_text_field( wp_unslash( $_GET['active_post'] ) );
	$wpdb->update( $settings_table, array( 'setting_value' => "$active_post" ), array( 'setting_key' => 'active_post' ) );
	exit();
}

if ( 'scsc' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) && ! isset( $_GET['set_tab'] ) ) {
	$result    = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key = 'active_tab';", $settings_table ), ARRAY_A );
	$tab_param = $result[0]['setting_value'];
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=' . sanitize_text_field( $tab_param ) . '">';
}

if ( isset( $_GET['update_tab'] ) ) {
	$tab_param = sanitize_text_field( wp_unslash( $_GET['update_tab'] ) );
	$wpdb->update( $settings_table, array( 'setting_value' => "$tab_param" ), array( 'setting_key' => 'active_tab' ) );
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=' . sanitize_text_field( $tab_param ) . '">';
}

// Start HTML output.
$header = ( new HTML_Refactory( 'header' ) )
	->attr( 'class', 'mb-2' )
	->child(
		( new HTML_Refactory( 'img' ) )
			->attr( 'src', plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/schema-scalpel-logo.svg' )
			->attr( 'width', '300' )
			->attr( 'height', 'auto' )
			->attr( 'alt', 'Schema Scalpel Logo' )
			->render()
	)
	->render();

$nav_tabs      = array();
$scsc_loc_tabs = array( 'Home Page', 'Global', 'Pages', 'Posts', 'Examples' );
foreach ( $scsc_loc_tabs as $key => $name ) {
	$formatted_name = str_replace( ' ', '', strtolower( $name ) );
	$classes        = array( 'nav-link' );
	if ( count( $scsc_loc_tabs ) - 1 === $key ) {
		$classes[] = 'ms-auto';
	}

	$nav_tabs[] = ( new HTML_Refactory( 'a' ) )
		->attr( 'class', $classes )
		->attr( 'id', "nav-$formatted_name-tab" )
		->attr( 'href', "#nav-$formatted_name" )
		->attr( 'data-toggle', 'tab' )
		->attr( 'role', 'tab' )
		->attr( 'aria-controls', "nav-$formatted_name" )
		->attr( 'aria-selected', 'true' )
		->child( esc_html( $name ) )
		->render();
}

$nav = ( new HTML_Refactory( 'nav' ) )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'nav', 'nav-tabs' ) )
			->attr( 'id', 'nav-tab' )
			->attr( 'role', 'tablist' )
			->child( implode( '', $nav_tabs ) )
			->render()
	)
	->render();

$tab_content = '';
$tab_panes   = array( 'homepage', 'global', 'pages', 'posts', 'examples' );
foreach ( $tab_panes as $pane ) {
	ob_start();
	require_once "scsc-$pane-tab.php";
	$content      = ob_get_clean();
	$tab_content .= ( new HTML_Refactory( 'div' ) )
		->attr( 'class', array( 'tab-pane', 'fade', 'homepage' === $pane ? 'show' : '' ) )
		->attr( 'id', "nav-$pane" )
		->attr( 'role', 'tabpanel' )
		->attr( 'aria-labelledby', "nav-$pane-tab" )
		->child( $content )
		->render();
}

$alert = ( new HTML_Refactory( 'p' ) )
	->attr( 'class', array( 'alert', 'alert-warning', 'mt-4' ) )
	->attr( 'role', 'alert' )
	->child(
		'NOTE: ' .
		( new HTML_Refactory( 'em' ) )
			->text( 'This plugin ' )
			->child(
				( new HTML_Refactory( 'a' ) )
					->attr( 'href', '/wp-admin/admin.php?page=scsc_settings#disable_yoast_schema' )
					->text( "automatically overrides Yoast's and AIOSEO's schema" )
					->render()
			)
			->child( ' and injects your customized schema to better fit your SEO objectives.' )
			->render()
	)
	->render();

$modal_edit = ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'modal', 'fade' ) )
	->attr( 'id', 'schemaBlockEditModal' )
	->attr( 'tabindex', '-1' )
	->attr( 'aria-hidden', 'true' )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'modal-dialog', 'modal-lg', 'mt-5' ) )
			->child(
				( new HTML_Refactory( 'div' ) )
					->attr( 'class', 'modal-content' )
					->child(
						( new HTML_Refactory( 'div' ) )
							->attr( 'class', 'modal-body' )
							->child(
								( new HTML_Refactory( 'pre' ) )
									->child(
										( new HTML_Refactory( 'textarea' ) )
											->attr( 'id', 'schemaTextareaEdit' )
											->attr( 'class', 'w-100' )
											->attr( 'style', 'max-height:70vh;height:100vw' )
											->render()
									)
									->render()
							)
							->child(
								( new HTML_Refactory( 'div' ) )
									->attr( 'class', array( 'modal-footer', 'justify-content-between' ) )
									->child(
										( new HTML_Refactory( 'button' ) )
											->attr( 'id', 'schemaBlockEditDeleteButton' )
											->attr( 'class', array( 'btn', 'btn-danger', 'px-5' ) )
											->attr( 'data-id', '' )
											->text( 'Delete' )
											->render()
									)
									->child(
										( new HTML_Refactory( 'div' ) )
											->child(
												( new HTML_Refactory( 'button' ) )
													->attr( 'id', 'schemaBlockCancelButton' )
													->attr( 'type', 'button' )
													->attr( 'class', array( 'btn', 'btn-warning', 'me-2' ) )
													->attr( 'data-bs-dismiss', 'modal' )
													->text( 'Cancel' )
													->render()
											)
											->child(
												( new HTML_Refactory( 'button' ) )
													->attr( 'id', 'schemaBlockEditSaveButton' )
													->attr( 'type', 'button' )
													->attr( 'class', array( 'btn', 'btn-success' ) )
													->attr( 'data-id', '' )
													->text( 'Save Changes' )
													->render()
											)
											->render()
									)
									->render()
							)
							->render()
					)
					->render()
			)
			->render()
	)
	->render();

$modal_create = ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'modal', 'fade' ) )
	->attr( 'id', 'schemaBlockCreateModal' )
	->attr( 'tabindex', '-1' )
	->attr( 'aria-hidden', 'true' )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'modal-dialog', 'modal-lg', 'mt-5' ) )
			->child(
				( new HTML_Refactory( 'div' ) )
					->attr( 'class', array( 'modal-content' ) )
					->child(
						( new HTML_Refactory( 'div' ) )
							->attr( 'class', array( 'modal-body' ) )
							->child(
								( new HTML_Refactory( 'pre' ) )
									->child(
										( new HTML_Refactory( 'textarea' ) )
											->attr( 'id', 'schemaTextareaCreate' )
											->attr( 'class', array( 'w-100' ) )
											->attr( 'style', 'max-height:70vh;height:100vw' )
											->render()
									)
									->render()
							)
							->child(
								( new HTML_Refactory( 'div' ) )
									->attr( 'class', array( 'modal-footer', 'justify-content-end' ) )
									->child(
										( new HTML_Refactory( 'div' ) )
											->child(
												( new HTML_Refactory( 'button' ) )
													->attr( 'id', 'schemaBlockCancelButton' )
													->attr( 'type', 'button' )
													->attr( 'class', array( 'btn', 'btn-warning', 'me-2' ) )
													->attr( 'data-bs-dismiss', 'modal' )
													->text( 'Cancel' )
													->render()
											)
											->child(
												( new HTML_Refactory( 'button' ) )
													->attr( 'id', 'schemaBlockSave' )
													->attr( 'type', 'button' )
													->attr( 'class', array( 'btn', 'btn-success' ) )
													->attr( 'data-id', '' )
													->text( 'Save Changes' )
													->render()
											)
											->render()
									)
									->render()
							)
							->render()
					)
					->render()
			)
			->render()
	)
	->render();

$spinner = ( new HTML_Refactory( 'div' ) )
	->attr( 'id', 'tab_spinner' )
	->attr( 'class', array( 'd-none', 'fixed-top', 'w-100', 'h-100', 'mx-auto' ) )
	->attr( 'style', 'background-color:rgba(0,0,0,.3)' )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'd-flex', 'justify-content-center' ) )
			->child(
				( new HTML_Refactory( 'div' ) )
					->attr( 'style', 'margin-top:50vh' )
					->attr( 'class', array( 'spinner-border', 'text-danger' ) )
					->attr( 'role', 'status' )
					->child(
						( new HTML_Refactory( 'span' ) )
							->attr( 'class', array( 'visually-hidden' ) )
							->text( 'Loading...' )
							->render()
					)
					->render()
			)
			->render()
	)
	->render();

$aside_content = '';
if ( ! empty( $_GET['set_tab'] ) && 'posts' === $_GET['set_tab'] ) {

	$aside_content = ( new HTML_Refactory( 'aside' ) )
		->attr( 'class', array( 'col-4' ) )
		->child(
			( new HTML_Refactory( 'h2' ) )
				->child( 'Generate ' )
				->child(
					( new HTML_Refactory( 'code' ) )
					->child( 'BlogPosting' )
					->render()
				)
				->child( ' Schema' )
				->render()
		)
		->child(
			( new HTML_Refactory( 'p' ) )
				->attr(
					'class',
					array(
						0 => 'lead',
						1 => 'mt-3',
					)
				)
				->child( 'Automatically create Google-recommended schema for ' )
				->child(
					( new HTML_Refactory( 'strong' ) )
					->child( 'all' )
					->render()
				)
				->child( ' of your blog posts... ' )
				->child(
					( new HTML_Refactory( 'span' ) )
					->attr(
						'class',
						array(
							0 => 'fst-italic',
						)
					)
					->child( 'at one time!' )
					->render()
				)
				->render()
		)
		->child(
			( new HTML_Refactory( 'div' ) )
			->attr( 'style', 'border-left:4px solid var(--bs-info-border-subtle);padding:10px;background-color:var(--bs-info-bg-subtle)' )
			->child(
				( new HTML_Refactory( 'p' ) )
					->attr(
						'class',
						array(
							0 => 'mb-2',
						)
					)
					->child( 'Instead of spending precious hours (' )
					->child(
						( new HTML_Refactory( 'span' ) )
							->attr(
								'class',
								array(
									0 => 'fst-italic',
								)
							)
							->child( 'days, even!' )
							->render()
					)
					->child( ') copy-and-pasting schema into each and every blog post, use this tool to auto-generate schema for your blog posts.' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'p' ) )
					->attr(
						'class',
						array(
							0 => 'mb-2',
						)
					)
					->child(
						( new HTML_Refactory( 'strong' ) )
						->child( 'Note:' )
						->render()
					)
					->child( ' This will ' )
					->child(
						( new HTML_Refactory( 'strong' ) )
						->child( 'NOT' )
						->render()
					)
					->child( ' overwrite any schema you&#039;ve already added to a blog post. In fact, it will skip any post with preexisting schema just to be safe.' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'p' ) )
					->attr(
						'class',
						array(
							0 => 'mb-0',
							1 => 'fst-italic',
						)
					)
					->child( 'Use the defaults for standard blog posts.' )
					->render()
			)
			->render()
		)
		->child(
			( new HTML_Refactory( 'form' ) )
			->attr(
				'class',
				array(
					0 => 'container',
					1 => 'mt-3',
					2 => 'py-3',
					3 => 'border',
				)
			)
			->child(
				( new HTML_Refactory( 'input' ) )
				->attr( 'type', 'hidden' )
				->attr( 'name', 'page' )
				->attr( 'value', 'scsc' )
				->render()
			)
			->child(
				( new HTML_Refactory( 'input' ) )
				->attr( 'type', 'hidden' )
				->attr( 'name', 'set_tab' )
				->attr( 'value', 'posts' )
				->render()
			)
			->child(
				( new HTML_Refactory( 'input' ) )
				->attr( 'type', 'hidden' )
				->attr( 'name', 'generate' )
				->attr( 'value', 'post_schema' )
				->render()
			)
			->child(
				( new HTML_Refactory( 'h3' ) )
				->attr(
					'class',
					array(
						0 => 'h5',
					)
				)
				->child(
					( new HTML_Refactory( 'svg' ) )
					->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
					->attr( 'width', '28' )
					->attr( 'height', '28' )
					->attr( 'fill', 'currentColor' )
					->attr(
						'class',
						array(
							0 => 'bi',
							1 => 'bi-0-square-fill',
							2 => 'me-1',
						)
					)
					->attr( 'viewbox', '0 0 16 16' )
					->child(
						( new HTML_Refactory( 'path' ) )
						->attr( 'd', 'M8 4.951c-1.008 0-1.629 1.09-1.629 2.895v.31c0 1.81.627 2.895 1.629 2.895s1.623-1.09 1.623-2.895v-.31c0-1.8-.621-2.895-1.623-2.895' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'path' ) )
						->attr( 'd', 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.988 12.158c-1.851 0-2.941-1.57-2.941-3.99V7.84c0-2.408 1.101-3.996 2.965-3.996 1.857 0 2.935 1.57 2.935 3.996v.328c0 2.408-1.101 3.99-2.959 3.99' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'span' ) )
					->child( 'Update Type' )
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'div' ) )
				->attr(
					'class',
					array(
						0 => 'd-flex',
						1 => 'flex-column',
						2 => 'bg-secondary-subtle',
						3 => 'mb-0',
						4 => 'ps-4',
						5 => 'py-3',
						6 => 'radio-border-left',
					)
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr( 'id', 'updateType1' )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'updateType' )
						->attr( 'value', 'Missing' )
						->attr( 'data-label', 'Missing' )
						->attr( 'checked', true )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'updateType1' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'Missing Only' )
								->render()
							)
							->child( ' ' )
							->child(
								( new HTML_Refactory( 'small' ) )
								->attr(
									'class',
									array(
										0 => 'text-secondary',
									)
								)
								->child( '&nbsp;(default)' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr( 'id', 'updateType2' )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'updateType' )
						->attr( 'value', 'Selected' )
						->attr( 'data-label', 'Selected' )
						->attr( 'disabled', true )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'updateType2' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'Selected Post' )
								->render()
							)
							->child( ' ' )
							->child(
								( new HTML_Refactory( 'small' ) )
								->attr( 'id', 'selectedPost' )
								->attr(
									'class',
									array(
										0 => 'text-secondary',
									)
								)
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr( 'id', 'updateType3' )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'updateType' )
						->attr( 'value', 'Replace' )
						->attr( 'data-label', 'Replace' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'updateType3' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'Replace All' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'h3' ) )
				->attr(
					'class',
					array(
						0 => 'h5',
						1 => 'mt-3',
					)
				)
				->child(
					( new HTML_Refactory( 'svg' ) )
					->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
					->attr( 'width', '28' )
					->attr( 'height', '28' )
					->attr( 'fill', 'currentColor' )
					->attr(
						'class',
						array(
							0 => 'bi',
							1 => 'bi-1-square-fill',
							2 => 'me-1',
						)
					)
					->attr( 'viewbox', '0 0 16 16' )
					->child(
						( new HTML_Refactory( 'path' ) )
						->attr( 'd', 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm7.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z' )
						->render()
					)
					->render()
				)
				->child( ' Schema ' )
				->child(
					( new HTML_Refactory( 'code' ) )
					->child( '@type' )
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'div' ) )
				->attr(
					'class',
					array(
						0 => 'd-flex',
						1 => 'flex-column',
						2 => 'bg-secondary-subtle',
						3 => 'mb-0',
						4 => 'ps-4',
						5 => 'py-3',
						6 => 'radio-border-left',
					)
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr( 'id', 'schemaPostType1' )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'schemaPostType' )
						->attr( 'value', 'BlogPosting' )
						->attr( 'data-label', 'BlogPosting' )
						->attr( 'checked', true )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'schemaPostType1' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'BlogPosting' )
								->render()
							)
							->child( ' ' )
							->child(
								( new HTML_Refactory( 'small' ) )
								->attr(
									'class',
									array(
										0 => 'text-secondary',
									)
								)
								->child( '&nbsp;(default)' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr( 'id', 'schemaPostType2' )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'schemaPostType' )
						->attr( 'value', 'Article' )
						->attr( 'data-label', 'Article' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'schemaPostType2' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'Article' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr( 'id', 'schemaPostType3' )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'schemaPostType' )
						->attr( 'value', 'NewsArticle' )
						->attr( 'data-label', 'NewsArticle' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'schemaPostType3' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'NewsArticle' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'h3' ) )
				->attr(
					'class',
					array(
						0 => 'h5',
						1 => 'mt-3',
					)
				)
				->child(
					( new HTML_Refactory( 'svg' ) )
					->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
					->attr( 'width', '28' )
					->attr( 'height', '28' )
					->attr( 'fill', 'currentColor' )
					->attr(
						'class',
						array(
							0 => 'bi',
							1 => 'bi-2-square-fill',
							2 => 'me-1',
						)
					)
					->attr( 'viewbox', '0 0 16 16' )
					->child(
						( new HTML_Refactory( 'path' ) )
						->attr( 'd', 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm4.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306' )
						->render()
					)
					->render()
				)
				->child( ' Schema ' )
				->child(
					( new HTML_Refactory( 'code' ) )
					->child( 'author' )
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'div' ) )
				->attr(
					'class',
					array(
						0 => 'd-flex',
						1 => 'flex-column',
						2 => 'bg-secondary-subtle',
						3 => 'mb-0',
						4 => 'ps-4',
						5 => 'py-3',
						6 => 'radio-border-left',
					)
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'schemaAuthorType' )
						->attr( 'id', 'schemaAuthorType1' )
						->attr( 'value', 'Person' )
						->attr( 'data-label', 'Person' )
						->attr( 'checked', true )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'schemaAuthorType1' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'Person' )
								->render()
							)
							->child( ' ' )
							->child(
								( new HTML_Refactory( 'small' ) )
								->attr(
									'class',
									array(
										0 => 'text-secondary',
									)
								)
								->child( '&nbsp;(default)' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'schemaAuthorType' )
						->attr( 'id', 'schemaAuthorType2' )
						->attr( 'value', 'Organization' )
						->attr( 'data-label', 'Organization' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'schemaAuthorType2' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'Organization' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'h3' ) )
				->attr(
					'class',
					array(
						0 => 'h5',
						1 => 'mt-3',
					)
				)
				->child(
					( new HTML_Refactory( 'svg' ) )
					->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
					->attr( 'width', '28' )
					->attr( 'height', '28' )
					->attr( 'fill', 'currentColor' )
					->attr(
						'class',
						array(
							0 => 'bi',
							1 => 'bi-2-square-fill',
							2 => 'me-1',
						)
					)
					->attr( 'viewbox', '0 0 16 16' )
					->child(
						( new HTML_Refactory( 'path' ) )
						->attr( 'd', 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318' )
						->render()
					)
					->render()
				)
				->child( ' Schema ' )
				->child(
					( new HTML_Refactory( 'code' ) )
					->child( 'keywords' )
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'div' ) )
				->attr(
					'class',
					array(
						0 => 'd-flex',
						1 => 'flex-column',
						2 => 'bg-secondary-subtle',
						3 => 'mb-0',
						4 => 'ps-4',
						5 => 'py-3',
						6 => 'radio-border-left',
					)
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'keywords' )
						->attr( 'id', 'keywords1' )
						->attr( 'value', 'false' )
						->attr( 'data-label', 'None' )
						->attr( 'checked', true )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'keywords1' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'None' )
								->render()
							)
							->child( ' ' )
							->child(
								( new HTML_Refactory( 'small' ) )
								->attr(
									'class',
									array(
										0 => 'text-secondary',
									)
								)
								->child( '&nbsp;(default)' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'keywords' )
						->attr( 'id', 'keywords2' )
						->attr( 'value', 'Placeholder' )
						->attr( 'data-label', 'Placeholder' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'keywords2' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'Set Placeholder' )
								->render()
							)
							->child( ' ' )
							->child(
								( new HTML_Refactory( 'small' ) )
								->attr(
									'class',
									array(
										0 => 'text-secondary',
									)
								)
								->child( '(manually add later)' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'div' ) )
					->attr(
						'class',
						array(
							0 => 'form-check',
						)
					)
					->child(
						( new HTML_Refactory( 'input' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-input',
								1 => 'mt-1',
							)
						)
						->attr( 'type', 'radio' )
						->attr( 'name', 'keywords' )
						->attr( 'id', 'keywords3' )
						->attr( 'value', 'Tags' )
						->attr( 'data-label', 'Tags' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'label' ) )
						->attr(
							'class',
							array(
								0 => 'form-check-label',
							)
						)
						->attr( 'for', 'keywords3' )
						->child(
							( new HTML_Refactory( 'div' ) )
							->attr(
								'class',
								array(
									0 => 'd-flex',
									1 => 'flex-row',
								)
							)
							->child(
								( new HTML_Refactory( 'pre' ) )
								->attr(
									'class',
									array(
										0 => 'm-0',
									)
								)
								->child( 'Use Post Tags as Keywords' )
								->render()
							)
							->render()
						)
						->render()
					)
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'div' ) )
				->attr(
					'class',
					array(
						0 => 'mt-3',
					)
				)
				->child(
					( new HTML_Refactory( 'svg' ) )
					->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
					->attr( 'width', '28' )
					->attr( 'height', '28' )
					->attr( 'fill', 'currentColor' )
					->attr(
						'class',
						array(
							0 => 'bi',
							1 => 'bi-3-square-fill',
							2 => 'me-1',
						)
					)
					->attr( 'viewbox', '0 0 16 16' )
					->child(
						( new HTML_Refactory( 'path' ) )
						->attr( 'd', 'M6.225 9.281v.053H8.85V5.063h-.065c-.867 1.33-1.787 2.806-2.56 4.218' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'path' ) )
						->attr( 'd', 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.519 5.057q.33-.527.657-1.055h1.933v5.332h1.008v1.107H10.11V12H8.85v-1.559H4.978V9.322c.77-1.427 1.656-2.847 2.542-4.265Z' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'svg' ) )
					->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
					->attr( 'width', '28' )
					->attr( 'height', '28' )
					->attr( 'fill', 'currentColor' )
					->attr(
						'class',
						array(
							0 => 'bi',
							1 => 'bi-3-square-fill',
							2 => 'me-1',
						)
					)
					->attr( 'viewbox', '0 0 16 16' )
					->child(
						( new HTML_Refactory( 'path' ) )
						->attr( 'd', 'M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z' )
						->render()
					)
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'button' ) )
				->attr( 'type', 'submit' )
				->attr(
					'class',
					array(
						0 => 'btn',
						1 => 'btn-primary',
						2 => 'mt-3',
					)
				)
				->child( 'Generate Post Schema' )
				->render()
			)
			->child(
				( new HTML_Refactory( 'hr' ) )
				->render()
			)
			->child(
				( new HTML_Refactory( 'h3' ) )
				->attr(
					'class',
					array(
						0 => 'h5',
						1 => 'mt-3',
					)
				)
				->child( 'Example Schema' )
				->render()
			)
			->child(
				( new HTML_Refactory( 'pre' ) )
				->attr(
					'class',
					array(
						0 => 'w-100',
						1 => 'rounded',
						2 => 'language-json',
					)
				)
				->attr( 'tabindex', '0' )
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '{' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;@context&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;https://schema.org&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr( 'id', 'schemaPostType' )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;@type&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;BlogPosting&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;@id&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[URL to post]#blogposting&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;headline&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[Post Title]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;url&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[URL to Post]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;datePublished&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[Publish Date (GMT)]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;dateModified&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[Modified Date (GMT)]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;author&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '[' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr( 'id', 'schemaAuthorType' )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;@type&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;Person&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;name&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[Post Author&#039;s Name]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;url&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[URL to Author&#039;s Site]&quot;' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;]' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;publisher&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '[' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr( 'id', 'schemaPublisherType' )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;@type&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;Person&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;name&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[Post Author&#039;s Name]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;url&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[URL to Author&#039;s Site]&quot;' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;]' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;description&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[Post&#039;s Excerpt]&quot;' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;keywords&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '[' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;[Keyword1]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;[Keyword2]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;[etc.]&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;]' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( ',' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;mainEntityOfPage&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '{' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'property',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;&quot;@id&quot;' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'operator',
							)
						)
						->child( ':' )
						->render()
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'string',
							)
						)
						->child( '&quot;[URL to Post]&quot;' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '&nbsp;&nbsp;&nbsp;&nbsp;}' )
						->render()
					)
					->render()
				)
				->child(
					( new HTML_Refactory( 'br' ) )
					->render()
				)
				->child(
					( new HTML_Refactory( 'code' ) )
					->attr(
						'class',
						array(
							0 => 'language-json',
						)
					)
					->child(
						( new HTML_Refactory( 'span' ) )
						->attr(
							'class',
							array(
								0 => 'token',
								1 => 'punctuation',
							)
						)
						->child( '}' )
						->render()
					)
					->render()
				)
				->render()
			)
			->child(
				( new HTML_Refactory( 'hr' ) )
				->attr( 'style', 'height:5px' )
				->attr(
					'class',
					array(
						0 => 'border',
						1 => 'border-secondary',
						2 => 'bg-light',
						3 => 'rounded',
					)
				)
				->render()
			)
			->render()
		)
		->render();
}

$main = ( new HTML_Refactory( 'main' ) )
	->attr( 'class', array( 'container', 'col-8', 'ms-0' ) )
	->child( $nav )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'tab-content', 'border', 'border-top-0', 'p-5' ) )
			->attr( 'id', 'nav-tabContent' )
			->child( $tab_content )
			->render()
	)
	->child( $alert )
	->child(
		( new HTML_Refactory( 'hr' ) )
			->render()
	)
	->child( $modal_edit )
	->child( $modal_create )
	->child( $spinner )
	->render();

echo ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'row', 'pt-3' ) )
	->child( $header )
	->child( ( new HTML_Refactory( 'hr' ) )->render() )
	->child( $main )
	->child( $aside_content )
	->render();

add_action(
	'admin_footer',
	function () {
		echo '<script>';
		global $wpdb;
		$result_page = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='active_page';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
		$_page       = $result_page[0]['setting_value'];
		$result_post = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %i WHERE setting_key='active_post';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
		$_post       = $result_post[0]['setting_value'];
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/scsc-admin-main.js';
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/prism.js';
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/bootstrap.min.js';
		echo '</script>';
		echo <<<SCRIPTS
    <script>
        document.getElementById('schemaBlockEditDeleteButton').addEventListener('click', (e)=>{
            deleteCurrentSchema(e.target.dataset.id, event);
        });
        document.getElementById('schemaBlockCancelButton').addEventListener('click', ()=>{
            closeSchemaTextareaEditModal(event);
        });
    </script>
SCRIPTS;
	}
);
