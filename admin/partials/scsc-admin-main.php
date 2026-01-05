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

use HTML_Refactory;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $wpdb;
$custom_schemas_table = $wpdb->prefix . 'scsc_custom_schemas';

if ( isset( $_POST['create'] ) ) {

	$custom_schema = \sanitize_text_field( \wp_unslash( $_POST['create'] ) );
	$schema_type   = isset( $_POST['schemaType'] ) ? \sanitize_text_field( \wp_unslash( $_POST['schemaType'] ) ) : '';
	$post_id_num   = isset( $_POST['postID'] ) ? absint( \wp_unslash( $_POST['postID'] ) ) : 0;

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
	$update_type      = \sanitize_text_field( \wp_unslash( $_GET['updateType'] ) );
	$schema_post_type = \sanitize_text_field( \wp_unslash( $_GET['schemaPostType'] ) );
	$author_type      = \sanitize_text_field( \wp_unslash( $_GET['schemaAuthorType'] ) );
	$keywords         = isset( $_GET['keywords'] ) ? \sanitize_text_field( \wp_unslash( $_GET['keywords'] ) ) : false;
	SCSC_Admin::generate_blogposting_schema( $update_type, $schema_post_type, $author_type, $keywords );
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=posts">';
}

if ( isset( $_POST['update'] ) ) {
	$update_id     = absint( \wp_unslash( $_POST['update'] ) );
	$custom_schema = isset( $_POST['schema'] ) ? \sanitize_text_field( \wp_unslash( $_POST['schema'] ) ) : '';

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
	$wpdb->delete( $custom_schemas_table, array( 'id' => \sanitize_text_field( \wp_unslash( $_GET['delete'] ) ) ) );
}

/**
 * GET/SET TABS
 */
$settings_table = $wpdb->prefix . 'scsc_settings';
if ( isset( $_GET['active_page'] ) ) {
	$active_page = \sanitize_text_field( \wp_unslash( $_GET['active_page'] ) );
	$wpdb->update( $settings_table, array( 'setting_value' => "$active_page" ), array( 'setting_key' => 'active_page' ) );
	exit();
}

if ( isset( $_GET['active_post'] ) ) {
	$active_post = \sanitize_text_field( \wp_unslash( $_GET['active_post'] ) );
	$wpdb->update( $settings_table, array( 'setting_value' => "$active_post" ), array( 'setting_key' => 'active_post' ) );
	exit();
}

if ( 'scsc' === \sanitize_text_field( \wp_unslash( $_GET['page'] ) ) && ! isset( $_GET['set_tab'] ) ) {
	$result    = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key = 'active_tab';", $settings_table ), \ARRAY_A );
	$tab_param = $result[0]['setting_value'];
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=' . \sanitize_text_field( $tab_param ) . '">';
}

if ( isset( $_GET['update_tab'] ) ) {
	$tab_param = \sanitize_text_field( \wp_unslash( $_GET['update_tab'] ) );
	$wpdb->update( $settings_table, array( 'setting_value' => "$tab_param" ), array( 'setting_key' => 'active_tab' ) );
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=' . \sanitize_text_field( $tab_param ) . '">';
}

// Start HTML output.
$header = new HTML_Refactory(
	'header',
	array( 'class' => array( 'mb-2' ) ),
	'',
	new HTML_Refactory(
		'img',
		array(
			'src'    => plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/schema-scalpel-logo.svg',
			'width'  => '300',
			'height' => 'auto',
			'alt'    => 'Schema Scalpel Logo',
		)
	)
);

$nav_tabs      = array();
$scsc_loc_tabs = array( 'Home Page', 'Global', 'Pages', 'Posts', 'Examples' );
foreach ( $scsc_loc_tabs as $key => $name ) {
	$formatted_name = str_replace( ' ', '', strtolower( $name ) );
	$classes        = array( 'nav-link' );
	if ( count( $scsc_loc_tabs ) - 1 === $key ) {
		$classes[] = 'ms-auto';
	}
	$nav_tabs[] = new HTML_Refactory(
		'a',
		array(
			'class'         => $classes,
			'id'            => "nav-$formatted_name-tab",
			'href'          => "#nav-$formatted_name",
			'data-toggle'   => 'tab',
			'role'          => 'tab',
			'aria-controls' => "nav-$formatted_name",
			'aria-selected' => 'true',
		),
		esc_html( $name )
	);
}
$nav = new HTML_Refactory(
	'nav',
	array(),
	'',
	new HTML_Refactory(
		'div',
		array(
			'class' => array( 'nav', 'nav-tabs' ),
			'id'    => 'nav-tab',
			'role'  => 'tablist',
		),
		'',
		implode( '', $nav_tabs )
	)
);

$tab_content = '';
$tab_panes   = array( 'homepage', 'global', 'pages', 'posts', 'examples' );
foreach ( $tab_panes as $pane ) {
	ob_start();
	require_once "scsc-$pane-tab.php";
	$content      = ob_get_clean();
	$tab_content .= new HTML_Refactory(
		'div',
		array(
			'class'           => array( 'tab-pane', 'fade', 'homepage' === $pane ? 'show' : '' ),
			'id'              => "nav-$pane",
			'role'            => 'tabpanel',
			'aria-labelledby' => "nav-$pane-tab",
		),
		'',
		$content
	);
}

$alert = new HTML_Refactory(
	'p',
	array(
		'class' => array( 'alert', 'alert-warning', 'mt-4' ),
		'role'  => 'alert',
	),
	'NOTE: ' . new HTML_Refactory(
		'em',
		array(),
		'This plugin ' .
		new HTML_Refactory(
			'a',
			array( 'href' => '/wp-admin/admin.php?page=scsc_settings#disable_yoast_schema' ),
			"automatically overrides Yoast's and AIOSEO's schema"
		) .
		' and injects your customized schema to better fit your SEO objectives.'
	)
);

$modal_edit = new HTML_Refactory(
	'div',
	array(
		'class'       => array( 'modal', 'fade' ),
		'id'          => 'schemaBlockEditModal',
		'tabindex'    => '-1',
		'aria-hidden' => 'true',
	),
	'',
	new HTML_Refactory(
		'div',
		array( 'class' => array( 'modal-dialog', 'modal-lg', 'mt-5' ) ),
		'',
		new HTML_Refactory(
			'div',
			array( 'class' => array( 'modal-content' ) ),
			'',
			new HTML_Refactory(
				'div',
				array( 'class' => array( 'modal-body' ) ),
				'',
				new HTML_Refactory(
					'pre',
					array(),
					'',
					new HTML_Refactory(
						'textarea',
						array(
							'id'    => 'schemaTextareaEdit',
							'class' => array( 'w-100' ),
							'style' => 'max-height:70vh;height:100vw',
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'modal-footer', 'justify-content-between' ) ),
					'',
					new HTML_Refactory(
						'button',
						array(
							'id'      => 'schemaBlockEditDeleteButton',
							'class'   => array( 'btn', 'btn-danger', 'px-5' ),
							'data-id' => '',
						),
						'Delete'
					) .
					new HTML_Refactory(
						'div',
						array(),
						'',
						new HTML_Refactory(
							'button',
							array(
								'id'              => 'schemaBlockCancelButton',
								'type'            => 'button',
								'class'           => array( 'btn', 'btn-warning', 'me-2' ),
								'data-bs-dismiss' => 'modal',
							),
							'Cancel'
						) .
						new HTML_Refactory(
							'button',
							array(
								'id'      => 'schemaBlockEditSaveButton',
								'type'    => 'button',
								'class'   => array( 'btn', 'btn-success' ),
								'data-id' => '',
							),
							'Save Changes'
						)
					)
				)
			)
		)
	)
);

$modal_create = new HTML_Refactory(
	'div',
	array(
		'class'       => array( 'modal', 'fade' ),
		'id'          => 'schemaBlockCreateModal',
		'tabindex'    => '-1',
		'aria-hidden' => 'true',
	),
	'',
	new HTML_Refactory(
		'div',
		array( 'class' => array( 'modal-dialog', 'modal-lg', 'mt-5' ) ),
		'',
		new HTML_Refactory(
			'div',
			array( 'class' => array( 'modal-content' ) ),
			'',
			new HTML_Refactory(
				'div',
				array( 'class' => array( 'modal-body' ) ),
				'',
				new HTML_Refactory(
					'pre',
					array(),
					'',
					new HTML_Refactory(
						'textarea',
						array(
							'id'    => 'schemaTextareaCreate',
							'class' => array( 'w-100' ),
							'style' => 'max-height:70vh;height:100vw',
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'modal-footer', 'justify-content-end' ) ),
					'',
					new HTML_Refactory(
						'div',
						array(),
						'',
						new HTML_Refactory(
							'button',
							array(
								'id'              => 'schemaBlockCancelButton',
								'type'            => 'button',
								'class'           => array( 'btn', 'btn-warning', 'me-2' ),
								'data-bs-dismiss' => 'modal',
							),
							'Cancel'
						) .
						new HTML_Refactory(
							'button',
							array(
								'id'      => 'schemaBlockSave',
								'type'    => 'button',
								'class'   => array( 'btn', 'btn-success' ),
								'data-id' => '',
							),
							'Save Changes'
						)
					)
				)
			)
		)
	)
);

$spinner = new HTML_Refactory(
	'div',
	array(
		'id'    => 'tab_spinner',
		'class' => array( 'd-none', 'fixed-top', 'w-100', 'h-100', 'mx-auto' ),
		'style' => 'background-color:rgba(0,0,0,.3)',
	),
	'',
	new HTML_Refactory(
		'div',
		array( 'class' => array( 'd-flex', 'justify-content-center' ) ),
		'',
		new HTML_Refactory(
			'div',
			array(
				'style' => 'margin-top:50vh',
				'class' => array( 'spinner-border', 'text-danger' ),
				'role'  => 'status',
			),
			'',
			new HTML_Refactory( 'span', array( 'class' => array( 'visually-hidden' ) ), 'Loading...' )
		)
	)
);

$aside_content = '';
if ( ! empty( $_GET['set_tab'] ) && 'posts' === $_GET['set_tab'] ) {
	$aside_content = new HTML_Refactory(
		'aside',
		array( 'class' => array( 'col-4' ) ),
		'',
		new HTML_Refactory(
			'h2',
			array(),
			'Generate ' .
			new HTML_Refactory( 'code', array(), 'BlogPosting' ) . ' Schema'
		) .
		new HTML_Refactory(
			'p',
			array( 'class' => array( 'lead', 'mt-3' ) ),
			'Automatically create Google-recommended schema for ' .
			new HTML_Refactory( 'strong', array(), 'all' ) .
			' of your blog posts... ' .
			new HTML_Refactory( 'span', array( 'class' => array( 'fst-italic' ) ), 'at one time!' )
		) .
		new HTML_Refactory(
			'div',
			array( 'style' => 'border-left:4px solid var(--bs-info-border-subtle);padding:10px;background-color:var(--bs-info-bg-subtle)' ),
			'',
			new HTML_Refactory(
				'p',
				array( 'class' => array( 'mb-2' ) ),
				'Instead of spending precious hours (' .
				new HTML_Refactory( 'span', array( 'class' => array( 'fst-italic' ) ), 'days, even!' ) .
				') copy-and-pasting schema into each and every blog post, use this tool to auto-generate schema for your blog posts.'
			) .
			new HTML_Refactory(
				'p',
				array( 'class' => array( 'mb-2' ) ),
				new HTML_Refactory( 'strong', array(), 'Note:' ) .
				' This will ' .
				new HTML_Refactory( 'strong', array(), 'NOT' ) .
				" overwrite any schema you've already added to a blog post. In fact, it will skip any post with preexisting schema just to be safe."
			) .
			new HTML_Refactory(
				'p',
				array( 'class' => array( 'mb-0', 'fst-italic' ) ),
				'Use the defaults for standard blog posts.'
			)
		) .
		new HTML_Refactory(
			'form',
			array( 'class' => array( 'container', 'mt-3', 'py-3', 'border' ) ),
			'',
			new HTML_Refactory(
				'input',
				array(
					'type'  => 'hidden',
					'name'  => 'page',
					'value' => 'scsc',
				)
			) .
			new HTML_Refactory(
				'input',
				array(
					'type'  => 'hidden',
					'name'  => 'set_tab',
					'value' => 'posts',
				)
			) .
			new HTML_Refactory(
				'input',
				array(
					'type'  => 'hidden',
					'name'  => 'generate',
					'value' => 'post_schema',
				)
			) .
			new HTML_Refactory(
				'h3',
				array( 'class' => array( 'h5' ) ),
				'',
				new HTML_Refactory(
					'svg',
					array(
						'xmlns'   => 'http://www.w3.org/2000/svg',
						'width'   => '28',
						'height'  => '28',
						'fill'    => 'currentColor',
						'class'   => array( 'bi', 'bi-0-square-fill', 'me-1' ),
						'viewBox' => '0 0 16 16',
					),
					'',
					new HTML_Refactory( 'path', array( 'd' => 'M8 4.951c-1.008 0-1.629 1.09-1.629 2.895v.31c0 1.81.627 2.895 1.629 2.895s1.623-1.09 1.623-2.895v-.31c0-1.8-.621-2.895-1.623-2.895' ) ) .
					new HTML_Refactory( 'path', array( 'd' => 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.988 12.158c-1.851 0-2.941-1.57-2.941-3.99V7.84c0-2.408 1.101-3.996 2.965-3.996 1.857 0 2.935 1.57 2.935 3.996v.328c0 2.408-1.101 3.99-2.959 3.99' ) )
				) . '<span>Update Type</span>'
			) .
			// Update Type Radio Buttons.
			new HTML_Refactory(
				'div',
				array( 'class' => array( 'd-flex', 'flex-column', 'bg-secondary-subtle', 'mb-0', 'ps-4', 'py-3', 'radio-border-left' ) ),
				'',
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'id'         => 'updateType1',
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'updateType',
							'value'      => 'Missing',
							'data-label' => 'Missing',
							'checked'    => '',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'updateType1',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'Missing Only' ) .
							'&nbsp;' .
							new HTML_Refactory( 'small', array( 'class' => array( 'text-secondary' ) ), '(default)' )
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'id'         => 'updateType2',
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'updateType',
							'value'      => 'Selected',
							'data-label' => 'Selected',
							'disabled'   => '',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'updateType2',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'Selected Post' ) .
							'&nbsp;' .
							new HTML_Refactory(
								'small',
								array(
									'id'    => 'selectedPost',
									'class' => array( 'text-secondary' ),
								)
							)
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'id'         => 'updateType3',
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'updateType',
							'value'      => 'Replace',
							'data-label' => 'Replace',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'updateType3',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'Replace All' )
						)
					)
				)
			) .
			// Schema @type Section.
			new HTML_Refactory(
				'h3',
				array( 'class' => array( 'h5', 'mt-3' ) ),
				'',
				new HTML_Refactory(
					'svg',
					array(
						'xmlns'   => 'http://www.w3.org/2000/svg',
						'width'   => '28',
						'height'  => '28',
						'fill'    => 'currentColor',
						'class'   => array( 'bi', 'bi-1-square-fill', 'me-1' ),
						'viewBox' => '0 0 16 16',
					),
					'',
					new HTML_Refactory( 'path', array( 'd' => 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm7.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z' ) )
				) . ' Schema ' . new HTML_Refactory( 'code', array(), '@type' )
			) .
			new HTML_Refactory(
				'div',
				array( 'class' => array( 'd-flex', 'flex-column', 'bg-secondary-subtle', 'mb-0', 'ps-4', 'py-3', 'radio-border-left' ) ),
				'',
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'id'         => 'schemaPostType1',
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'schemaPostType',
							'value'      => 'BlogPosting',
							'data-label' => 'BlogPosting',
							'checked'    => '',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'schemaPostType1',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'BlogPosting' ) .
							'&nbsp;' .
							new HTML_Refactory( 'small', array( 'class' => array( 'text-secondary' ) ), '(default)' )
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'id'         => 'schemaPostType2',
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'schemaPostType',
							'value'      => 'Article',
							'data-label' => 'Article',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'schemaPostType2',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'Article' )
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'id'         => 'schemaPostType3',
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'schemaPostType',
							'value'      => 'NewsArticle',
							'data-label' => 'NewsArticle',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'schemaPostType3',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'NewsArticle' )
						)
					)
				)
			) .
			// Schema author Section.
			new HTML_Refactory(
				'h3',
				array( 'class' => array( 'h5', 'mt-3' ) ),
				'',
				new HTML_Refactory(
					'svg',
					array(
						'xmlns'   => 'http://www.w3.org/2000/svg',
						'width'   => '28',
						'height'  => '28',
						'fill'    => 'currentColor',
						'class'   => array( 'bi', 'bi-2-square-fill', 'me-1' ),
						'viewBox' => '0 0 16 16',
					),
					'',
					new HTML_Refactory( 'path', array( 'd' => 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm4.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306' ) )
				) . ' Schema ' . new HTML_Refactory( 'code', array(), 'author' )
			) .
			new HTML_Refactory(
				'div',
				array( 'class' => array( 'd-flex', 'flex-column', 'bg-secondary-subtle', 'mb-0', 'ps-4', 'py-3', 'radio-border-left' ) ),
				'',
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'schemaAuthorType',
							'id'         => 'schemaAuthorType1',
							'value'      => 'Person',
							'data-label' => 'Person',
							'checked'    => '',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'schemaAuthorType1',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'Person' ) .
							'&nbsp;' .
							new HTML_Refactory( 'small', array( 'class' => array( 'text-secondary' ) ), '(default)' )
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'schemaAuthorType',
							'id'         => 'schemaAuthorType2',
							'value'      => 'Organization',
							'data-label' => 'Organization',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'schemaAuthorType2',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'Organization' )
						)
					)
				)
			) .
			// Schema keywords Section.
			new HTML_Refactory(
				'h3',
				array( 'class' => array( 'h5', 'mt-3' ) ),
				'',
				new HTML_Refactory(
					'svg',
					array(
						'xmlns'   => 'http://www.w3.org/2000/svg',
						'width'   => '28',
						'height'  => '28',
						'fill'    => 'currentColor',
						'class'   => array( 'bi', 'bi-2-square-fill', 'me-1' ),
						'viewBox' => '0 0 16 16',
					),
					'',
					new HTML_Refactory( 'path', array( 'd' => 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318' ) )
				) . ' Schema ' . new HTML_Refactory( 'code', array(), 'keywords' )
			) .
			new HTML_Refactory(
				'div',
				array( 'class' => array( 'd-flex', 'flex-column', 'bg-secondary-subtle', 'mb-0', 'ps-4', 'py-3', 'radio-border-left' ) ),
				'',
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'keywords',
							'id'         => 'keywords1',
							'value'      => 'false',
							'data-label' => 'None',
							'checked'    => '',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'keywords1',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'None' ) .
							'&nbsp;' .
							new HTML_Refactory( 'small', array( 'class' => array( 'text-secondary' ) ), '(default)' )
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'keywords',
							'id'         => 'keywords2',
							'value'      => 'Placeholder',
							'data-label' => 'Placeholder',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'keywords2',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'Set Placeholder' ) .
							'&nbsp;' .
							new HTML_Refactory( 'small', array( 'class' => array( 'text-secondary' ) ), '(manually add later)' )
						)
					)
				) .
				new HTML_Refactory(
					'div',
					array( 'class' => array( 'form-check' ) ),
					'',
					new HTML_Refactory(
						'input',
						array(
							'class'      => array( 'form-check-input', 'mt-1' ),
							'type'       => 'radio',
							'name'       => 'keywords',
							'id'         => 'keywords3',
							'value'      => 'Tags',
							'data-label' => 'Tags',
						)
					) .
					new HTML_Refactory(
						'label',
						array(
							'class' => array( 'form-check-label' ),
							'for'   => 'keywords3',
						),
						'',
						new HTML_Refactory(
							'div',
							array( 'class' => array( 'd-flex', 'flex-row' ) ),
							'',
							new HTML_Refactory( 'pre', array( 'class' => array( 'm-0' ) ), 'Use Post Tags as Keywords' )
						)
					)
				)
			) .
			// Generate Button.
			new HTML_Refactory(
				'div',
				array( 'class' => array( 'mt-3' ) ),
				'',
				new HTML_Refactory(
					'svg',
					array(
						'xmlns'   => 'http://www.w3.org/2000/svg',
						'width'   => '28',
						'height'  => '28',
						'fill'    => 'currentColor',
						'class'   => array( 'bi', 'bi-3-square-fill', 'me-1' ),
						'viewBox' => '0 0 16 16',
					),
					'',
					new HTML_Refactory( 'path', array( 'd' => 'M6.225 9.281v.053H8.85V5.063h-.065c-.867 1.33-1.787 2.806-2.56 4.218' ) ) .
					new HTML_Refactory( 'path', array( 'd' => 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.519 5.057q.33-.527.657-1.055h1.933v5.332h1.008v1.107H10.11V12H8.85v-1.559H4.978V9.322c.77-1.427 1.656-2.847 2.542-4.265Z' ) )
				) .
				new HTML_Refactory(
					'svg',
					array(
						'xmlns'   => 'http://www.w3.org/2000/svg',
						'width'   => '28',
						'height'  => '28',
						'fill'    => 'currentColor',
						'class'   => array( 'bi', 'bi-3-square-fill', 'me-1' ),
						'viewBox' => '0 0 16 16',
					),
					'',
					new HTML_Refactory( 'path', array( 'd' => 'M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z' ) )
				)
			) .
			new HTML_Refactory(
				'button',
				array(
					'type'  => 'submit',
					'class' => array( 'btn', 'btn-primary', 'mt-3' ),
				),
				'Generate Post Schema'
			) .
			new HTML_Refactory( 'hr' ) .
			new HTML_Refactory( 'h3', array( 'class' => array( 'h5', 'mt-3' ) ), 'Example Schema' ) .
			'<pre class="w-100 rounded language-json">' .
			'<code>{</code><br>' .
			'<code>  "@context":"https://schema.org",</code><br>' .
			'<code id="schemaPostType">  "@type":"BlogPosting",</code><br>' .
			'<code>  "@id":"[URL to post]#blogposting",</code><br>' .
			'<code>  "headline":"[Post Title]",</code><br>' .
			'<code>  "url":"[URL to Post]",</code><br>' .
			'<code>  "datePublished":"[Publish Date (GMT)]",</code><br>' .
			'<code>  "dateModified":"[Modified Date (GMT)]",</code><br>' .
			'<code>  "author":[</code><br>' .
			'<code>      {</code><br>' .
			'<code id="schemaAuthorType">          "@type":"Person",</code><br>' .
			'<code>          "name":"[Post Author\'s Name]",</code><br>' .
			'<code>          "url":"[URL to Author\'s Site]"</code><br>' .
			'<code>      }</code><br>' .
			'<code>  ],</code><br>' .
			'<code> "publisher":[</code><br>' .
			'<code>      {</code><br>' .
			'<code id="schemaPublisherType">          "@type":"Person",</code><br>' .
			'<code>          "name":"[Post Author\'s Name]",</code><br>' .
			'<code>          "url":"[URL to Author\'s Site]"</code><br>' .
			'<code>      }</code><br>' .
			'<code>  ],</code><br>' .
			'<code>  "description":"[Post\'s Excerpt]"</code><br>' .
			'<code>  "keywords":[</code><br>' .
			'<code>      "[Keyword1]",</code><br>' .
			'<code>      "[Keyword2]",</code><br>' .
			'<code>      "[etc.]",</code><br>' .
			'<code>  ],</code><br>' .
			'<code>  "mainEntityOfPage":{</code><br>' .
			'<code>      "@id":"[URL to Post]"</code><br>' .
			'<code>  }</code><br>' .
			'<code>}</code>' .
			'</pre>' .
			new HTML_Refactory(
				'hr',
				array(
					'style' => 'height:5px',
					'class' => array( 'border', 'border-secondary', 'bg-light', 'rounded' ),
				)
			)
		)
	);
}

$main = new HTML_Refactory(
	'main',
	array( 'class' => array( 'container', 'col-8', 'ms-0' ) ),
	'',
	$nav .
	new HTML_Refactory(
		'div',
		array(
			'class' => array( 'tab-content', 'border', 'border-top-0', 'p-5' ),
			'id'    => 'nav-tabContent',
		),
		'',
		$tab_content
	) .
	$alert .
	new HTML_Refactory( 'hr' ) .
	$modal_edit .
	$modal_create .
	$spinner
);

echo new HTML_Refactory(
	'div',
	array( 'class' => array( 'row', 'pt-3' ) ),
	'',
	$header .
	new HTML_Refactory( 'hr' ) .
	$main .
	$aside_content
);

\add_action(
	'admin_footer',
	function () {
		echo '<script>';
		global $wpdb;
		$result_page = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key='active_page';", $wpdb->prefix . 'scsc_settings' ), \ARRAY_A );
		$_page       = $result_page[0]['setting_value'];
		$result_post = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key='active_post';", $wpdb->prefix . 'scsc_settings' ), \ARRAY_A );
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
