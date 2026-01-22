<?php
/**
 * Schema Scalpel – Post Editor Metabox (Full Create/Edit/Delete)
 *
 * This file renders the metabox for managing custom JSON-LD schemas on posts/pages.
 *
 * @link       https://schemascalpel.com/
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/admin/partials
 * @since   2.0.0
 */

namespace SchemaScalpel;

use HTML_Refactory;

/**
 * Renders the Schema Scalpel metabox in the post editor.
 *
 * This function outputs the complete metabox UI, including:
 * - Existing post-specific and global schemas
 * - A form to create new schemas
 * - Editing and deletion capabilities via AJAX
 * - An "Examples" tab with copyable schema templates
 *
 * The metabox is registered via add_meta_box() and WordPress passes the current
 * WP_Post object as the only parameter.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param WP_Post $post The current post object.
 *
 * @return void
 */
function scsc_render_metabox_preview( $post ) {
	global $wpdb;
	$post      = get_post( $post );
	$post_id   = $post->ID;
	$table     = $wpdb->prefix . 'scsc_custom_schemas';
	$post_type = get_post_type( $post_id );

	// Add nonce for security (verified on save via AJAX).
	wp_nonce_field( 'scsc_metabox_save', 'scsc_metabox_nonce', true, true );

	// Fetch all relevant schema rows.
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT id, schema_type, post_id, custom_schema, updated
            FROM {$table}
            WHERE (post_id = %d OR schema_type = 'global')
            ORDER BY schema_type DESC, created DESC",
			$post_id
		),
		ARRAY_A
	);

	$schemas = array();

	foreach ( $rows as $row ) {
		$data = maybe_unserialize( $row['custom_schema'] );

		$schemas[] = array(
			'id'          => (int) $row['id'],
			'schema_type' => $row['schema_type'],
			'post_id'     => (int) $row['post_id'],
			'data'        => $data,
			'updated'     => $row['updated'],
		);
	}

	$logo = ( new HTML_Refactory( 'div' ) )
		->attr( 'style', 'display:flex;justify-content:center;margin-bottom:2rem' )
		->child(
			( new HTML_Refactory( 'img' ) )
				->attr( 'src', esc_url( plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . '/admin/images/schema-scalpel-logo.svg' ) )
				->attr( 'alt', esc_attr( 'Schema Scalpel Logo', 'schema-scalpel' ) )
				->attr( 'style', 'max-width:300px;z-index:99' )
				->render()
		)
		->render();
	echo $logo;

	$metabox_tabs = ( new HTML_Refactory( 'div' ) )
		->attr( 'class', array( 'scsc-metabox-tabs' ) )
		->attr( 'style', 'position:relative;z-index:1' )
		->child(
			( new HTML_Refactory( 'h2' ) )
				->attr( 'class', array( 'nav-tab-wrapper' ) )
				->child(
					( new HTML_Refactory( 'a' ) )
						->attr( 'href', '#scsc-tab-schema' )
						->attr( 'class', array( 'nav-tab', 'nav-tab-active' ) )
						->child( esc_html( __( 'Schema', 'schema-scalpel' ) ) )
						->render()
				)
				->child(
					( new HTML_Refactory( 'a' ) )
						->attr( 'href', '#scsc-tab-examples' )
						->attr( 'class', array( 'nav-tab' ) )
						->child( esc_html( __( 'Examples', 'schema-scalpel' ) ) )
						->render()
				)
				->render()
		)
		->render();
	echo $metabox_tabs;

	?>

	<div class="scsc-metabox" style="position:relative;z-index:99">
		<?php

		$hamburger_menu = ( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'scsc-nav-container' ) )
			->attr( 'style', 'position:absolute;right:2rem' )
			->child(
				( new HTML_Refactory( 'button' ) )
					->attr( 'class', array( 'hamburger-toggle' ) )
					->attr( 'aria-label', 'Toggle Menu' )
					->child(
						( new HTML_Refactory( 'svg' ) )
							->attr( 'xmlns', 'http://www.w3.org/2000/svg' )
							->attr( 'width', '16' )
							->attr( 'height', '16' )
							->attr( 'fill', 'currentColor' )
							->attr( 'class', array( 'bi', 'bi-list' ) )
							->attr( 'viewbox', '0 0 16 16' )
							->child(
								( new HTML_Refactory( 'path' ) )
									->attr( 'fill-rule', 'evenodd' )
									->attr( 'd', 'M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5' )
									->render()
							)
							->render()
					)
					->render()
			)
			->child(
				( new HTML_Refactory( 'div' ) )
					->attr( 'class', array( 'scsc-menu' ) )
					->child(
						( new HTML_Refactory( 'div' ) )
							->child(
								( new HTML_Refactory( 'a' ) )
									->attr( 'href', '/wp-admin/admin.php?page=scsc' )
									->child( esc_html( 'Schema Scalpel&nbsp;' ) )
									->child(
										( new HTML_Refactory( 'strong' ) )
										->child( 'Dashboard' )
									)
									->render()
							)
							->child(
								( new HTML_Refactory( 'hr' ) )->render()
							)
							->child(
								( new HTML_Refactory( 'a' ) )
									->attr( 'href', '/wp-admin/admin.php?page=scsc_settings' )
									->child( esc_html( 'Schema Scalpel&nbsp;' ) )
									->child(
										( new HTML_Refactory( 'strong' ) )
											->child( 'Settings' )
											->render()
									)
									->render()
							)
							->child(
								( new HTML_Refactory( 'hr' ) )->render()
							)
							->child(
								( new HTML_Refactory( 'a' ) )
									->attr( 'href', '/wp-admin/admin.php?page=scsc_export' )
									->child( esc_html( 'Schema Scalpel&nbsp;' ) )
									->child(
										( new HTML_Refactory( 'strong' ) )
											->child( 'Export' )
											->render()
									)
									->render()
							)
							->render()
					)
					->render()
			)
			->render();
		echo $hamburger_menu;

		?>
		<div id="scsc-tab-schema" class="scsc-tab-content" style="display: block;">
			<?php

			echo ( new HTML_Refactory( 'h3' ) )
				->attr( 'style', 'margin:0 0 12px 0' )
				->child( esc_html( __( 'Structured Data Editor', 'schema-scalpel' ) ) )
				->render();

			$edit_instructions = ( new HTML_Refactory( 'p' ) )
				->attr( 'class', array( 'description' ) )
				->attr( 'style', 'margin-bottom:16px' )
				->child( esc_html( __( 'Add, edit, or delete JSON-LD schema below. Changes are saved immediately.', 'schema-scalpel' ) ) )
				->render();
			echo $edit_instructions;

			// CREATE NEW.
			$create_schema = ( new HTML_Refactory( 'div' ) )
				->attr( 'class', array( 'scsc-section', 'scsc-create' ) )
				->attr( 'style', 'margin-bottom:20px;padding:12px;background:#f9f9f9;border:1px solid #ddd;' )
				->child(
					( new HTML_Refactory( 'h4' ) )
						->attr( 'style', 'margin: 0 0 12px 0;' )
						->child( '+' . esc_html( __( 'Add New Schema', 'schema-scalpel' ) ) )
						->render()
				)
				->child(
					( new HTML_Refactory( 'textarea' ) )
						->attr( 'name', 'scsc_new_schema' )
						->attr( 'rows', '8' )
						->attr( 'class', array( 'widefat' ) )
						->attr( 'placeholder', esc_attr( 'Example: {"@context":"https://schema.org","@type":"FAQPage","mainEntity":[...]}', 'schema-scalpel' ) )
						->render()
				)
				->child(
					( new HTML_Refactory( 'p' ) )
						->attr( 'style', 'margin: 8px 0 0 0;' )
						->child(
							( new HTML_Refactory( 'button' ) )
								->attr( 'type', 'button' )
								->attr( 'class', array( 'button', 'button-primary', 'scsc-create-new' ) )
								->attr( 'data-post-id', esc_attr( (string) $post_id ) )
								->child(
									esc_html__( 'Create as ' . esc_html( ucfirst( $post_type ) . ' Schema', 'schema-scalpel' ) )
								)
								->render()
						)
						->child(
							( new HTML_Refactory( 'button' ) )
								->attr( 'type', 'button' )
								->attr( 'class', array( 'button', 'scsc-create-new-global' ) )
								->attr( 'style', 'margin-left:3px' )
								->child( esc_html( __( 'Create as Global Schema', 'schema-scalpel' ) ) )
								->render()
						)
						->render()
				)
				->render();
			echo $create_schema;

			// EXISTING SCHEMAS.
			if ( empty( $schemas ) ) :

				echo ( new HTML_Refactory( 'p' ) )
					->child(
						( new HTML_Refactory( 'em' ) )
							->child( esc_html( __( 'No schema found for this post.', 'schema-scalpel' ) ) )
							->render()
					)
					->render();

			else :

				$previous_type = '';
				foreach ( $schemas as $item ) :
					// Insert separator only once before the first global schema.
					if ( 'global' === $item['schema_type'] && 'global' !== $previous_type ) {
						echo ( new HTML_Refactory( 'hr' ) )
							->attr( 'style', 'margin:2rem 0;border:none;border-top:2px solid #ccc' )
							->render();
					}

					$label = ( 'global' === $item['schema_type'] ) ? 'GLOBAL' : strtoupper( $post_type );

					$data = maybe_unserialize( $item['data'] );

					if ( is_string( $data ) ) {
						$data    = html_entity_decode( $data, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
						$decoded = json_decode( $data, true );
						if ( JSON_ERROR_NONE === json_last_error() ) {
							$data = $decoded;
						}
					}

					$pretty_json = wp_json_encode(
						$data,
						JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
					);

					// Remove trailing commas before closing brackets/objects.
					$pretty_json = preg_replace( '/,\s*([\]}])/m', '$1', $pretty_json );
					$label_color = ( 'global' === $item['schema_type'] ) ? '#d63638' : '#2271b1';

					$existing_schema = ( new HTML_Refactory( 'div' ) )
						->attr( 'class', array( 'scsc-section' ) )
						->attr( 'style', 'margin-bottom:16px;padding:12px;background:#fff;border:1px solid #ddd' )
						->child(
							( new HTML_Refactory( 'div' ) )
								->attr( 'style', 'margin-bottom: 12px' )
								->child(
									( new HTML_Refactory( 'strong' ) )
										->attr( 'style', 'color:' . $label_color )
										->child( $label . '&nbsp;Schema' )
										->render()
								)
								->child(
									( new HTML_Refactory( 'span' ) )
										->attr( 'style', 'color:#666;font-size:12px' )
										->child( '—' . esc_html( __( 'Updated:', 'schema-scalpel' ) ) . esc_html( $item['updated'] ) )
										->render()
								)
								->render()
						)
						->child(
							( new HTML_Refactory( 'textarea' ) )
								->attr( 'name', 'scsc_schema[' . esc_attr( (string) $item['id'] ) . ']' )
								->attr( 'rows', '14' )
								->attr( 'class', array( 'widefat', 'scsc-schema-textarea' ) )
								->attr( 'data-id', (string) $item['id'] )
								->attr( 'spellcheck', 'false' )
								->child( esc_textarea( $pretty_json ) )
								->render()
						)
						->child(
							( new HTML_Refactory( 'p' ) )
								->attr( 'style', 'margin: 8px 0 0 0;' )
								->child(
									( new HTML_Refactory( 'button' ) )
										->attr( 'type', 'button' )
										->attr( 'class', array( 'button', 'button-primary', 'scsc-save' ) )
										->attr( 'data-id', (string) $item['id'] )
										->child( esc_html( __( 'Save Changes', 'schema-scalpel' ) ) )
										->render()
								)
								->child(
									( new HTML_Refactory( 'span' ) )
										->attr( 'class', array( 'scsc-status' ) )
										->attr( 'style', 'margin-left:10px;font-style:italic;color:green;display:none' )
										->child( esc_html( __( 'Saved.', 'schema-scalpel' ) ) )
										->render()
								)
								->child(
									( new HTML_Refactory( 'button' ) )
										->attr( 'type', 'button' )
										->attr( 'class', array( 'button', 'button-link-delete', 'scsc-delete' ) )
										->attr( 'style', 'float: right;' )
										->attr( 'data-id', esc_attr( (string) $item['id'] ) )
										->child( esc_html( __( 'Delete', 'schema-scalpel' ) ) )
										->render()
								)
								->render()
						)
						->render();
					echo $existing_schema;

					$previous_type = $item['schema_type'];
				endforeach;
			endif;
			?>
		</div>

		<div id="scsc-tab-examples" class="scsc-tab-content" style="display: none;">
			<?php

			echo ( new HTML_Refactory( 'h3' ) )
				->attr( 'style', 'margin:0 0 12px 0' )
				->child( esc_html( __( 'Schema Examples', 'schema-scalpel' ) ) )
				->render();

			echo ( new HTML_Refactory( 'p' ) )
				->attr( 'class', array( 'description' ) )
				->attr( 'style', 'margin-bottom:16px' )
				->child( esc_html( __( 'Copy and paste these examples into the schema editor.', 'schema-scalpel' ) ) )
				->render();

			// Load the examples array.
			include_once SCHEMA_SCALPEL_DIRECTORY . '/admin/vars/examples.php';

			if ( ! empty( $schema_examples ) && is_array( $schema_examples ) ) :
				foreach ( $schema_examples as $key => $json ) :
					$decoded = json_decode( $json );
					if ( null === $decoded ) {
						continue;
					}
					$pretty_json = wp_json_encode(
						$decoded,
						JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
					);

					// Human-readable title.
					$title = ucwords( str_replace( array( '-', '_' ), ' ', $key ) );

					$safe_for_attr = htmlspecialchars( $pretty_json, ENT_QUOTES, 'UTF-8' );
					$scsc_example  = ( new HTML_Refactory( 'div' ) )
						->attr( 'class', array( 'scsc-example' ) )
						->attr( 'style', 'margin-bottom:30px;padding:20px;background:#f9f9f9;border:1px solid #ddd;border-radius:8px' )
						->child(
							( new HTML_Refactory( 'h4' ) )
								->attr( 'style', 'margin0 0 12px 0;color:#2271b1' )
								->child( esc_html( $title ) . '&nbsp;Schema' )
								->render()
						)
						->child(
							( new HTML_Refactory( 'pre' ) )
								->attr( 'class', array( 'scsc-example-preview' ) )
								->child( esc_textarea( $pretty_json ) )
								->render()
						)
						->child(
							( new HTML_Refactory( 'p' ) )
								->attr( 'style', 'margin:12px 0 0 0' )
								->child(
									( new HTML_Refactory( 'button' ) )
										->attr( 'type', 'button' )
										->attr( 'class', array( 'button', 'button-secondary', 'scsc-copy-example' ) )
										->attr( 'data-json', $safe_for_attr )
										->child( esc_html( __( 'Copy to Editor', 'schema-scalpel' ) ) )
										->render()
								)
								->render()
						)
						->render();

					echo $scsc_example;

				endforeach;
			else :
				echo ( new HTML_Refactory( 'p' ) )
					->child(
						( new HTML_Refactory( 'em' ) )
							->child( esc_html( __( 'No examples available.', 'schema-scalpel' ) ) )
							->render()
					)->render();
			endif;

			?>
		</div>
	</div>

	<script>
		function removeIllegalCharacters(v) {
			if (typeof v !== 'string') {
				console.warn('removeIllegalCharacters(v) expected string, got:', v);
				return false;
			}

			let w, x;
			v = v.trim();

			if (v.match(/{/gm) == null || v.match(/}/gm) == null) {
				alert("MISSING CHARACTER: An error in formatting has been detected.\nPlease review your schema.");
				return false;
			}

			if (v.match(/{/gm).length < v.match(/}/gm).length && v.substring(v.length - 2, v.length) === "}}") {
				v = v.substring(0, v.length - 1);
			}

			do {
				w = v;

				if (v.substring(0, 2) === "{{") {
					v = v.substring(1, v.length);
				}

				v = v
					.replace(/(\n|\r|\r\n|\n\r|\t)/g, "")
					.replace(/ {/, "{")
					.replace(/{ /, "{")
					.replace(/,}/g, "}")
					.replace(/} /g, "}")
					.replace(/\[ /g, "[")
					.replace(/ \[/g, "[")
					.replace(/,\]/g, "]")
					.replace(/ ]/g, "]")
					.replace(/] /g, "]")
					.replace(/{}/g, "")
					.replace(/\[\]/g, "")
					.replace(/{\[/g, "")
					.replace(/  /g, " ")
					.replace(/ ,/g, ",")
					.replace(/; /g, ";")
					.replace(/::/g, ":");

				x = v;

			} while (x !== w);

			if (w[w.length - 1] === ",") {
				w = w.substring(0, w.length - 1);
			}

			const formatted = finalFormattingCheck(correctBracketCount(w));
			if (!formatted) return false;

			return formatted
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&apos;');
		}

		function correctBracketCount(w) {
			let leftBracketCount = 0;
			let rightBracketCount = 0;

			for (let i = 0; i < w.length; i++) {
				if (w[i] === "{") leftBracketCount++;
				else if (w[i] === "}") rightBracketCount++;
			}

			if (leftBracketCount < rightBracketCount) {
				return correctBracketCount(w.substring(0, w.length - 1));
			}

			if (leftBracketCount > rightBracketCount) {
				return correctBracketCount(w + "}");
			}

			return w;
		}

		function finalFormattingCheck(theSchema) {
			try {
				JSON.parse(theSchema);
				return theSchema;
			} catch (e) {
				alert("There is an error in your schema. Please review.\n\n" + e.message);
				return false;
			}
		}

		jQuery(document).ready(function($) {
			$('.nav-tab-wrapper a').on('click', function(e) {
				e.preventDefault();
				const target = $(this).attr('href');
				$('.nav-tab').removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
				$('.scsc-tab-content').hide();
				$(target).show();
			});

			$('.scsc-save').on('click', function() {
				const btn = $(this);
				const id = btn.data('id');
				const textarea = $('textarea[name="scsc_schema[' + id + ']"]');
				const rawJson = textarea.val();

				const formatted = removeIllegalCharacters(rawJson);
				if (!formatted) return;

				$.post(ajaxurl, {
					action: 'scsc_save_metabox_schema',
					nonce: $('#scsc_metabox_nonce').val(),
					schema_id: id,
					schema_json: formatted
				}, function(resp) {
					if (resp.success) {
						btn.next('.scsc-status').fadeIn().delay(1500).fadeOut();
					} else {
						alert('Error: ' + (resp.data || 'Unknown error'));
					}
				});
			});

			$('.scsc-create-new, .scsc-create-new-global').on('click', function() {
				const rawJson = $('textarea[name="scsc_new_schema"]').val();
				const formatted = removeIllegalCharacters(rawJson);
				if (!formatted) return;

				const isGlobal = $(this).hasClass('scsc-create-new-global') ? 1 : 0;

				$.post(ajaxurl, {
					action: 'scsc_create_metabox_schema',
					nonce: $('#scsc_metabox_nonce').val(),
					post_id: '<?php echo esc_js( $post_id ); ?>',
					post_type: '<?php echo esc_js( $post_type ); ?>s',
					schema_json: formatted,
					is_global: isGlobal
				}, function(resp) {
					if (resp.success) {
						location.reload();
					} else {
						alert('Error: ' + (resp.data || 'Unknown error'));
					}
				});
			});

			// Delete
			$('.scsc-delete').on('click', function() {
				if (!confirm('Delete this schema entry permanently?')) return;
				const id = $(this).data('id');
				$.post(ajaxurl, {
					action: 'scsc_delete_metabox_schema',
					nonce: $('#scsc_metabox_nonce').val(),
					schema_id: id
				}, function() {
					location.reload();
				});
			});

			$('.scsc-copy-example').on('click', function() {
				const escapedJson = $(this).attr('data-json');
				const temp = document.createElement('textarea');
				temp.innerHTML = escapedJson;
				const json = temp.value;

				$('textarea[name="scsc_new_schema"]').val(json);

				$('.nav-tab-wrapper a[href="#scsc-tab-schema"]').trigger('click');
				$('html, body').animate({
					scrollTop: $('.scsc-create').offset().top - 300
				}, 500);

				alert('Example copied! Now click “Create as [...]” to add it.');
			});

			// Auto-save on main post save
			const { select, subscribe, dispatch } = wp.data;
			let wasSaving = false;

			const unsubscribe = subscribe(() => {
				const isSaving      = select('core/editor').isSavingPost();
				const isAutosaving  = select('core/editor').isAutosavingPost();
				const hasFinished   = select('core/editor').didPostSaveRequestSucceed();

				if (wasSaving && !isSaving && hasFinished && !isAutosaving) {
					console.log('Main post saved successfully → auto-saving Schema Scalpel schemas...');

					$('.scsc-save').each(function() {
						const $btn = $(this);
						const id   = $btn.data('id');

						const $textarea = $('textarea[name="scsc_schema\\[' + id + '\\]"]');
						if ($textarea.length === 0) {
							console.warn('Textarea not found for schema id:', id);
							return;
						}

						const rawJson = $textarea.val().trim();
						const formatted = removeIllegalCharacters(rawJson);

						if (!formatted) {
							console.warn('Invalid schema format for id:', id, '- skipping auto-save');
							return;
						}

						$.post(ajaxurl, {
							action:     'scsc_save_metabox_schema',
							nonce:      $('#scsc_metabox_nonce').val(),
							schema_id:  id,
							schema_json: formatted
						}, function(resp) {
							if (resp.success) {
								$btn.next('.scsc-status')
									.text('Auto-saved.')
									.fadeIn()
									.delay(1800)
									.fadeOut();
							} else {
								console.error('Auto-save failed for schema', id, resp);
								dispatch('core/notices').createNotice(
									'error',
									'Schema Scalpel: Could not auto-save one schema. Please check manually.',
									{ isDismissible: true }
								);
							}
						});
					});
				}

				wasSaving = isSaving;
			});
		});

		document.querySelector('.hamburger-toggle').addEventListener('click', function() {
			document.querySelector('.scsc-menu').classList.toggle('active');
		});
	</script>

	<style>
		.scsc-menu {
			display: none;
			position: absolute;
			right: 0;
			padding: 1rem;
			background-color: whitesmoke;
			border: 1px solid lightgray;
			border-radius: 6px;
			-webkit-box-shadow: 0 3px 5px rgba(0,0,0,.2);
					box-shadow: 0 3px 5px rgba(0,0,0,.2);
			width: 12rem;
		}
		
		.scsc-menu.active {
			display: block;
		}
		
		.scsc-menu:hover {
			background-color: white;
		}

		.scsc-menu a {
			text-decoration: none;
		}

		.hamburger-toggle {
			padding: .5rem .5rem .25rem;
			background-color: rgba(192, 192, 192, .2);
			border: 1px solid #ddd;
			border-radius: 4px;
			cursor: pointer;
		}
		
		.scsc-metabox-tabs {
			max-width: 900px;
			margin: 0 auto;
		}

		.scsc-metabox-tabs .nav-tab-wrapper {
			border: none;
			padding: 0 !important;
		}

		.scsc-metabox-tabs .nav-tab-wrapper a.nav-tab-active,
		.nav-tab-active {
			background-color: whitesmoke;
			border-color: whitesmoke;
		}
		
		.scsc-metabox-tabs .nav-tab {
			border-top-right-radius: 5px;
			border-top-left-radius: 5px;
		}
		
		#scsc_schema_preview .inside {
			background-color: rgba(95, 77, 147, .6);
			background: -o-linear-gradient(45deg, rgb(227, 118, 130) 15%, rgb(95, 77, 147) 85%);
			background: linear-gradient(45deg, rgb(227, 118, 130) 15%, rgb(95, 77, 147) 85%);
			padding: 4rem 0;
			overflow-x: hidden;
		}

		#scsc_schema_preview .scsc-metabox {
			max-width: 900px;
			margin: 0 auto;
			padding: 2rem;
			background-color: whitesmoke;
			border-radius: 10px;
			-webkit-box-shadow: 0 3px 30px rgb(0, 0, 0, .3);
					box-shadow: 0 3px 30px rgb(0, 0, 0, .3);
		}

		#scsc_schema_preview .scsc-metabox textarea.widefat {
			font-family: Consolas, Monaco, monospace;
			font-size: 13px;
		}

		#scsc_schema_preview .scsc-section {
			border-radius: 8px;
		}

		#scsc_schema_preview textarea,
		.scsc-example-preview {
			font-family: Consolas, Monaco, 'Courier New', monospace !important;
			font-size: 13.5px;
			line-height: 1.5;
			-moz-tab-size: 2;
				-o-tab-size: 2;
				tab-size: 2;
			background: rgb(45, 45, 45);
			color: #f8c555;
			border: 1px solid #444;
			padding: 12px;
			border-radius: 6px;
			resize: vertical;
		}
		
		#scsc_schema_preview textarea::-webkit-input-placeholder {
			font-style: italic;
			color: #7ec699;
		}
		
		#scsc_schema_preview textarea::-moz-placeholder {
			font-style: italic;
			color: #7ec699;
		}
		
		#scsc_schema_preview textarea:-ms-input-placeholder {
			font-style: italic;
			color: #7ec699;
		}
		
		#scsc_schema_preview textarea::-ms-input-placeholder {
			font-style: italic;
			color: #7ec699;
		}
		
		#scsc_schema_preview textarea::placeholder {
			font-style: italic;
			color: #7ec699;
		}

		.scsc-example-preview {
			border: none;
			white-space: break-spaces;
		}

		.scsc-tab-content {
			padding: 20px 0;
		}
	</style>
	<?php
	echo '<script>';
	require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/anime.js';
	echo '</script><script>';
	require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/js/scsc-schema-animation.js';
	echo '</script>';
}