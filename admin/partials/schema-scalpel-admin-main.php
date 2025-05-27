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
if ( isset( $_GET['create'] ) ) {
	$custom_schema = sanitize_text_field( wp_unslash( $_GET['create'] ) );
	$wpdb->insert(
		$custom_schemas_table,
		array(
			'custom_schema' => serialize( $custom_schema ),
			'schema_type'   => sanitize_text_field( wp_unslash( $_GET['schemaType'] ) ),
			'post_id'       => sanitize_text_field( wp_unslash( $_GET['postID'] ) ),
		)
	);
}

if ( isset( $_GET['generate'] ) ) {
	$schema_post_type = sanitize_text_field( wp_unslash( $_GET['schemaPostType'] ) );
	$author_type      = sanitize_text_field( wp_unslash( $_GET['schemaAuthorType'] ) );
	Schema_Scalpel_Admin::generate_blogposting_schema( $schema_post_type, $author_type );
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=posts">';
}

if ( isset( $_GET['update'] ) ) {
	$custom_schema = sanitize_text_field( wp_unslash( $_GET['schema'] ) );
	$wpdb->update( $custom_schemas_table, array( 'custom_schema' => serialize( $custom_schema ) ), array( 'id' => sanitize_text_field( $_GET['update'] ) ) );
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
	$post = sanitize_text_field( wp_unslash( $_GET['active_post'] ) );
	$wpdb->update( $settings_table, array( 'setting_value' => "$post" ), array( 'setting_key' => 'active_post' ) );
	exit();
}

if ( 'scsc' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) && ! isset( $_GET['set_tab'] ) ) {
	$result    = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key = 'active_tab';", $settings_table ), ARRAY_A );
	$tab_param = $result[0]['setting_value'];
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=' . sanitize_text_field( $tab_param ) . '">';
}

if ( isset( $_GET['update_tab'] ) ) {
	$tab_param = sanitize_text_field( wp_unslash( $_GET['update_tab'] ) );
	$wpdb->update( $settings_table, array( 'setting_value' => "$tab_param" ), array( 'setting_key' => 'active_tab' ) );
	echo '<meta http-equiv="refresh" content="0;url=/wp-admin/admin.php?page=scsc&set_tab=' . sanitize_text_field( $tab_param ) . '">';
}

?>
<div class="row pt-3">
		<?php

		echo new HTML_Refactory(
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

		echo new HTML_Refactory(
			'hr',
		);

		echo '<main class="container col-8 ms-0"><nav>';


			$scsc_loc_tabs             = array( 'Home Page', 'Global', 'Pages', 'Posts', 'Examples' );
			$attr_value_formatted_name = array();
			$classes                   = array( 'nav-link' );
			$tab_links                 = '';

		foreach ( $scsc_loc_tabs as $key => $name ) {

			$attr_value_formatted_name[] = str_replace( ' ', '', strtolower( $name ) );
		}

		foreach ( $scsc_loc_tabs as $key => $name ) {

			if ( count( $scsc_loc_tabs ) - 1 === $key ) {
				$classes[] = 'ms-auto';
			}
			$tab_links .= new HTML_Refactory(
				'a',
				array(
					'class'         => $classes,
					'id'            => 'nav-' . $attr_value_formatted_name[ $key ] . '-tab',
					'href'          => esc_url( '#nav-' . $attr_value_formatted_name[ $key ] ),
					'data-toggle'   => 'tab',
					'role'          => 'tab',
					'aria-controls' => 'nav-' . $attr_value_formatted_name[ $key ],
					'aria-selected' => 'true',
				),
				esc_html( $name )
			);
		}

			echo new HTML_Refactory(
				'div',
				array(
					'class' => array( 'nav', 'nav-tabs' ),
					'id'    => 'nav-tab',
					'role'  => 'tablist',
				),
				'',
				$tab_links
			);

			?>
		</nav>
		<div class="tab-content border border-top-0 p-5" id="nav-tabContent">
			<div class="tab-pane fade show" id="nav-homepage" role="tabpanel" aria-labelledby="nav-homepage-tab">
				<?php
				require_once 'scsc-homepage-tab.php';
				?>
			</div>
			<div class="tab-pane fade show" id="nav-global" role="tabpanel" aria-labelledby="nav-global-tab">
				<?php
				require_once 'scsc-global-tab.php';
				?>
			</div>
			<div class="tab-pane fade" id="nav-pages" role="tabpanel" aria-labelledby="nav-pages-tab">
				<?php
				require_once 'scsc-pages-tab.php';
				?>
			</div>
			<div class="tab-pane fade" id="nav-posts" role="tabpanel" aria-labelledby="nav-posts-tab">
				<?php
				require_once 'scsc-posts-tab.php';
				?>
			</div>
			<div class="tab-pane fade" id="nav-examples" role="tabpanel" aria-labelledby="nav-examples-tab">
				<?php
				require_once 'scsc-examples-tab.php';
				?>
			</div>
		</div>
		<?php

		echo new HTML_Refactory(
			'p',
			array(
				'class' => array( 'alert', 'alert-warning', 'mt-4' ),
				'role'  => 'alert',
			),
			'NOTE: ' . new HTML_Refactory(
				'em',
				array(),
				'This plugin ' . new HTML_Refactory(
					'a',
					array( 'href' => '/wp-admin/admin.php?page=scsc_settings#disable_yoast_schema' ),
					"automatically overrides Yoast's and AIOSEO's schema"
				) . ' and injects your customized schema to better fit your SEO objectives.'
			)
		);

		echo new HTML_Refactory(
			'hr'
		);
		/**
		 * ADMIN TABS end
		 */

		/**
		 * SCHEMA BLOCK-EDIT MODAL
		 */
		echo new HTML_Refactory(
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
						) . new HTML_Refactory(
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
							) . new HTML_Refactory(
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
								) . new HTML_Refactory(
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

		/**
		 * SCHEMA BLOCK-EDIT MODAL
		 */
		echo new HTML_Refactory(
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
						) . new HTML_Refactory(
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
								) . new HTML_Refactory(
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


		/**
		 * SPINNER
		 */

		echo new HTML_Refactory(
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
					new HTML_Refactory(
						'span',
						array( 'class' => array( 'visually-hidden' ) ),
						'Loading...'
					)
				)
			)
		);

		echo '</main>';

		if ( ! empty( $_GET['set_tab'] ) && 'posts' === $_GET['set_tab'] ) :

			?>
		<aside class="col-4">

			<?php
			echo new HTML_Refactory(
				'p',
				array( 'class' => array( 'h2' ) ),
				'',
				new HTML_Refactory(
					'i',
					array( 'class' => array( 'badge', 'bg-success' ) ),
					'New!'
				)
			);

			echo new HTML_Refactory(
				'h2',
				array(),
				'Generate ' . new HTML_Refactory(
					'code',
					array(),
					'BlogPosting'
				) . ' Schema'
			);

			echo new HTML_Refactory(
				'p',
				array( 'class' => array( 'lead', 'mt-3' ) ),
				'Automatically create Google-recommended schema for ' . new HTML_Refactory(
					'strong',
					array(),
					'all'
				) . ' of your blog posts... ' . new HTML_Refactory(
					'span',
					array( 'class' => array( 'fst-italic' ) ),
					'at one time!'
				)
			);

			echo new HTML_Refactory(
				'div',
				array( 'style' => 'border-left:4px solid var(--bs-info-border-subtle);padding:10px;background-color:var(--bs-info-bg-subtle)' ),
				'',
				new HTML_Refactory(
					'p',
					array( 'class' => array( 'mb-2' ) ),
					'Instead of spending precious hours (' . new HTML_Refactory(
						'span',
						array( 'class' => array( 'fst-italic' ) ),
						'days, even!'
					) . ') copy-and-pasting schema into each and every blog post, use this tool to auto-generate schema for your blog posts.'
				) . new HTML_Refactory(
					'p',
					array( 'class' => array( 'mb-2' ) ),
					new HTML_Refactory(
						'strong',
						array(),
						'Note:'
					) . ' This will ' . new HTML_Refactory(
						'strong',
						array(),
						'NOT'
					) . " overwrite any schema you've already added to a blog post. In fact, it will skip any post with preexisting schema just to be safe."
				) . new HTML_Refactory(
					'p',
					array( 'class' => array( 'mb-0', 'fst-italic' ) ),
					'Use the defaults for standard blog posts.'
				)
			);

			echo '<form class="container mt-3 py-3 border">';

				echo new HTML_Refactory(
					'input',
					array(
						'type'  => 'hidden',
						'name'  => 'page',
						'value' => 'scsc',
					)
				);

				echo new HTML_Refactory(
					'input',
					array(
						'type'  => 'hidden',
						'name'  => 'set_tab',
						'value' => 'posts',
					)
				);

				echo new HTML_Refactory(
					'input',
					array(
						'type'  => 'hidden',
						'name'  => 'generate',
						'value' => 'post_schema',
					)
				);

				echo new HTML_Refactory(
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
							'class'   => array( 'bi', 'bi-1-square-fill', 'me-1' ),
							'viewBox' => '0 0 16 16',
						),
						'',
						new HTML_Refactory(
							'path',
							array( 'd' => 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm7.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z' )
						)
					) . ' Schema ' . new HTML_Refactory(
						'code',
						array(),
						'@type'
					)
				);

			?>
				<div class="d-flex flex-column bg-secondary-subtle mb-0 ps-4 py-3 radio-border-left">
					<?php
					echo new HTML_Refactory(
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
						) . new HTML_Refactory(
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
								new HTML_Refactory(
									'pre',
									array( 'class' => array( 'm-0' ) ),
									'BlogPosting'
								) . '&nbsp;' . new HTML_Refactory(
									'small',
									array( 'class' => array( 'text-secondary' ) ),
									'(default)'
								)
							)
						)
					);

					echo new HTML_Refactory(
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
						) . new HTML_Refactory(
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
								new HTML_Refactory(
									'pre',
									array( 'class' => array( 'm-0' ) ),
									'Article'
								)
							)
						)
					);

					echo new HTML_Refactory(
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
						) . new HTML_Refactory(
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
								new HTML_Refactory(
									'pre',
									array( 'class' => array( 'm-0' ) ),
									'NewsArticle'
								)
							)
						)
					);
					?>
				</div>
				<?php

				echo new HTML_Refactory(
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
						new HTML_Refactory(
							'path',
							array( 'd' => 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm4.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306' )
						)
					) . ' Schema ' . new HTML_Refactory(
						'code',
						array(),
						'author'
					)
				);

				?>
				<div class="d-flex flex-column bg-secondary-subtle mb-0 ps-4 py-3 radio-border-left">
					<div class="form-check">
						<input class="form-check-input mt-1" type="radio" name="schemaAuthorType" id="schemaAuthorType1" value="Person" data-label="Person" checked>
						<label class="form-check-label" for="schemaAuthorType1">
							<div class="d-flex flex-row">
								<pre class="m-0">Person</pre>&nbsp;<small class="text-secondary">(default)</small>
							</div>
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input mt-1" type="radio" name="schemaAuthorType" id="schemaAuthorType2" value="Organization" data-label="Organization">
						<label class="form-check-label" for="schemaAuthorType2">
							<pre class="m-0">Organization</pre>
						</label>
					</div>
				</div>

				<div style="border-left:4px solid var(--bs-info-border-subtle);padding:10px;background-color:var(--bs-info-bg-subtle)">
				<p class="mb-2"><strong>Before clicking the Generate Post Schema button...</strong></p>
					<p class="mb-2"><i>Ensure that you have set each blog post's author in the <a href="/wp-admin/edit.php">Posts</a> tab of your WordPress installation to whomever it is that you want to appear in the schema. (This can be done as a bulk action, if necessary.)</i></p>
					<p class="mb-0"><i>Also, double check that, in the author's <a href="/wp-admin/users.php">User Profile</a>, has the first and last name fields filled out.</i></p>
				</div>
			<?php

			echo new HTML_Refactory(
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
					new HTML_Refactory(
						'path',
						array( 'd' => 'M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318' )
					)
				) . new HTML_Refactory(
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
					new HTML_Refactory(
						'path',
						array( 'd' => 'M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z' )
					)
				)
			);

			echo new HTML_Refactory(
				'button',
				array(
					'type'  => 'submit',
					'class' => array( 'btn', 'btn-primary', 'mt-3' ),
				),
				'Generate Post Schema'
			);

			echo new HTML_Refactory(
				'hr'
			);

			echo new HTML_Refactory(
				'h3',
				array( 'class' => array( 'h5', 'mt-3' ) ),
				'Example Schema'
			);

			?>
				<pre class="w-100 rounded language-json">
<code>{</code>
<code>  "@context":"https://schema.org",</code>
<code id="schemaPostType">  "@type":"BlogPosting",</code>
<code>  "headline":"[Post Title]",</code>
<code>  "image":[</code>
<code>      "[URL to Thumbnail]"</code>
<code>  ],</code>
<code>  "datePublished":"[Publish Date (GMT)]",</code>
<code>  "dateModified":"[Modified Date (GMT)]",</code>
<code> "author":[</code>
<code>      {</code>
<code id="schemaAuthorType">          "@type":"Person",</code>
<code>          "name":"[Post Author Name]",</code>
<code>          "url":"[URL to Author's Site]"</code>
<code>      }</code>
<code>  ]</code>
<code>}</code>
</pre>
			</form>
			<?php

			echo new HTML_Refactory(
				'hr',
				array(
					'style' => 'height:5px',
					'class' => array( 'border', 'border-secondary', 'bg-light', 'rounded' ),
				)
			);

			echo '</aside>';

	endif;

		echo '</div>';

		\add_action(
			'admin_footer',
			function () {
				echo '<script>';
				/**
				 * GET LAST ACTIVE PAGE / POST SELECTIONS
				 */
				global $wpdb;
				$result_page = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key='active_page';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
				$_page       = $result_page[0]['setting_value'];

				$result_post = $wpdb->get_results( $wpdb->prepare( "SELECT setting_value FROM %1s WHERE setting_key='active_post';", $wpdb->prefix . 'scsc_settings' ), ARRAY_A );
				$_post       = $result_post[0]['setting_value'];

				/**
				 * LOAD ORDER MATTERS!
				 */
				require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/schema-scalpel-admin-main.js';
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
