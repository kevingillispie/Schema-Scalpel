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
	?>

	<div style="display: flex; justify-content: center; margin-bottom: 2rem;">
		<img src="<?php echo esc_url( plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . '/admin/images/schema-scalpel-logo.svg' ); ?>" alt="<?php esc_attr_e( 'Schema Scalpel Logo', 'schema-scalpel' ); ?>" style="max-width: 300px;">
	</div>

	<div class="scsc-metabox-tabs">
		<h2 class="nav-tab-wrapper">
			<a href="#scsc-tab-schema" class="nav-tab nav-tab-active"><?php esc_html_e( 'Schema', 'schema-scalpel' ); ?></a>
			<a href="#scsc-tab-examples" class="nav-tab"><?php esc_html_e( 'Examples', 'schema-scalpel' ); ?></a>
		</h2>
	</div>

	<div class="scsc-metabox">

		<div id="scsc-tab-schema" class="scsc-tab-content" style="display: block;">
			<h3 style="margin: 0 0 12px 0;">
				<?php esc_html_e( 'Structured Data Editor', 'schema-scalpel' ); ?>
			</h3>

			<p class="description" style="margin-bottom: 16px;">
				<?php esc_html_e( 'Add, edit, or delete JSON-LD schema below. Changes are saved immediately.', 'schema-scalpel' ); ?>
			</p>

			<!-- CREATE NEW -->
			<div class="scsc-section scsc-create" style="margin-bottom: 20px; padding: 12px; background: #f9f9f9; border: 1px solid #ddd;">
				<h4 style="margin: 0 0 12px 0;">+ <?php esc_html_e( 'Add New Schema', 'schema-scalpel' ); ?></h4>
				<textarea
					name="scsc_new_schema"
					rows="8"
					class="widefat"
					placeholder='<?php esc_attr_e( 'Example: {"@context":"https://schema.org","@type":"FAQPage","mainEntity":[...]}', 'schema-scalpel' ); ?>'></textarea>
				<p style="margin: 8px 0 0 0;">
					<button type="button" class="button button-primary scsc-create-new" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">
						<?php
						/* translators: %s: capitalized post type name */
						printf( esc_html__( 'Create as %s Schema', 'schema-scalpel' ), esc_html( ucfirst( $post_type ) ) );
						?>
					</button>
					<button type="button" class="button scsc-create-new-global">
						<?php esc_html_e( 'Create as Global Schema', 'schema-scalpel' ); ?>
					</button>
				</p>
			</div>

			<!-- EXISTING SCHEMAS -->
			<?php if ( empty( $schemas ) ) : ?>
				<p><em><?php esc_html_e( 'No schema found for this post.', 'schema-scalpel' ); ?></em></p>
			<?php else : ?>
				<?php
				$previous_type = '';
				foreach ( $schemas as $item ) :
					// Insert separator only once before the first global schema.
					if ( 'global' === $item['schema_type'] && 'global' !== $previous_type ) {
						echo '<hr style="margin: 2rem 0; border: none; border-top: 2px solid #ccc;" />';
					}

					$label = ( 'global' === $item['schema_type'] ) ? 'GLOBAL' : strtoupper( $post_type );
					$color = ( 'global' === $item['schema_type'] ) ? '#d63638' : '#2271b1';

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
					?>
					<div class="scsc-section" style="margin-bottom: 16px; padding: 12px; background: #fff; border: 1px solid #ddd;">
						<div style="margin-bottom: 12px;">
							<strong style="color: <?php echo esc_attr( $color ); ?>;">
								<?php echo esc_html( $label ); ?> Schema
							</strong>
							<span style="color: #666; font-size: 12px;">
								— <?php esc_html_e( 'Updated:', 'schema-scalpel' ); ?> <?php echo esc_html( $item['updated'] ); ?>
							</span>
						</div>

						<textarea
							name="scsc_schema[<?php echo esc_attr( (string) $item['id'] ); ?>]"
							rows="14"
							class="widefat scsc-schema-textarea"
							data-id="<?php echo esc_attr( (string) $item['id'] ); ?>"
							spellcheck="false"><?php echo esc_textarea( $pretty_json ); ?></textarea>

						<p style="margin: 8px 0 0 0;">
							<button type="button" class="button button-primary scsc-save" data-id="<?php echo esc_attr( (string) $item['id'] ); ?>">
								<?php esc_html_e( 'Save Changes', 'schema-scalpel' ); ?>
							</button>
							<span class="scsc-status" style="margin-left: 10px; font-style: italic; color: green; display: none;"><?php esc_html_e( 'Saved.', 'schema-scalpel' ); ?></span>
							<button type="button" class="button button-link-delete scsc-delete" style="float: right;" data-id="<?php echo esc_attr( (string) $item['id'] ); ?>">
								<?php esc_html_e( 'Delete', 'schema-scalpel' ); ?>
							</button>
						</p>
					</div>

					<?php
					$previous_type = $item['schema_type'];
				endforeach;
				?>
			<?php endif; ?>
		</div>

		<div id="scsc-tab-examples" class="scsc-tab-content" style="display: none;">
			<h3 style="margin: 0 0 12px 0;">
				<?php esc_html_e( 'Schema Examples', 'schema-scalpel' ); ?>
			</h3>

			<p class="description" style="margin-bottom: 16px;">
				<?php esc_html_e( 'Copy and paste these examples into the schema editor.', 'schema-scalpel' ); ?>
			</p>
			<?php
			// Load the examples array.
			include_once SCHEMA_SCALPEL_DIRECTORY . '/admin/vars/examples.php';

			if ( ! empty( $schema_examples ) && is_array( $schema_examples ) ) :
				foreach ( $schema_examples as $key => $json ) :
<<<<<<< Updated upstream
<<<<<<< Updated upstream
					// Decode and re-encode for pretty + safe JSON.
					$decoded = json_decode( $json );
					if ( null === $decoded ) {
						continue; // Skip invalid JSON.
=======
					$decoded = json_decode( $json );
					if ( null === $decoded ) {
						continue;
>>>>>>> Stashed changes
=======
					$decoded = json_decode( $json );
					if ( null === $decoded ) {
						continue;
>>>>>>> Stashed changes
					}
					$pretty_json = wp_json_encode(
						$decoded,
						JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
					);

<<<<<<< Updated upstream
<<<<<<< Updated upstream
					// For data-json attribute: we need the raw pretty string, safely escaped for HTML attribute
					// esc_attr() on pretty_json is safe because it contains no < > & etc., only valid JSON chars.
					$attr_json = esc_attr( $pretty_json );

=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
					// Human-readable title.
					$title = ucwords( str_replace( array( '-', '_' ), ' ', $key ) );
					?>
					<div class="scsc-example" style="margin-bottom: 30px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px;">
						<h4 style="margin: 0 0 12px 0; color: #2271b1;">
							<?php echo esc_html( $title ); ?> Schema
						</h4>
						<pre class="scsc-example-preview"><?php echo esc_textarea( $pretty_json ); ?></pre>
						<p style="margin: 12px 0 0 0;">
							<?php
<<<<<<< Updated upstream
<<<<<<< Updated upstream
							$pretty_json = wp_json_encode(
								$decoded,
								JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
							);

							// This preserves newlines and safely escapes for HTML attribute
							$safe_for_attr = htmlspecialchars( $pretty_json, ENT_QUOTES, 'UTF-8' );
							?>

=======
=======
>>>>>>> Stashed changes

							$safe_for_attr = htmlspecialchars( $pretty_json, ENT_QUOTES, 'UTF-8' );

							?>
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
							<button type="button" class="button button-secondary scsc-copy-example" 
									data-json="<?php echo $safe_for_attr; ?>">
								<?php esc_html_e( 'Copy to Editor', 'schema-scalpel' ); ?>
							</button>
						</p>
					</div>
								<?php
				endforeach;
			else :
				?>
				<p><em><?php esc_html_e( 'No examples available.', 'schema-scalpel' ); ?></em></p>
				<?php
			endif;
			?>
		</div>

	</div>

	<script>
		function removeIllegalCharacters(v) {
			let w, x;
			v = v.trim();

			if (v.match(/{/gm) == null || v.match(/}/gm) == null) {
				alert("MISSING CHARACTER: An error in formatting has been detected.\nPlease review your schema.");
				return false;
			}

			if (v.match(/{/gm).length < v.match(/}/gm).length && v.substring(v.length - 2, v.length) == "}}") {
				v = v.substring(0, v.length - 1);
			}

			do {
				w = v;

				if (v.substring(0, 2) == "{{") v = v.substring(1, v.length);

				v = v.replace(/(\n|\r|\r\n|\n\r|\t)/g, "")
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

			if (w[w.length - 1] == ",") w = w.substring(0, w.length - 1);

			let formatted = finalFormattingCheck(correctBracketCount(w));
			if (!formatted) return false;

<<<<<<< Updated upstream
<<<<<<< Updated upstream
			// EXACTLY like admin dashboard: replace quotes with HTML entities
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
			formatted = formatted.replace(/"/g, '&quot;').replace(/'/g, '&apos;');
			return formatted;
		}

		function correctBracketCount(w) {
			let leftBracketCount = 0, rightBracketCount = 0;
			for (let i = 0; i < w.length; i++) {
				if (w[i] == "{") leftBracketCount++;
				else if (w[i] == "}") rightBracketCount++;
			}
			if (leftBracketCount < rightBracketCount) {
				return correctBracketCount(w.substring(0, w.length - 1));
			} else if (leftBracketCount > rightBracketCount) {
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
<<<<<<< Updated upstream
<<<<<<< Updated upstream
			// Tab switching
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
			$('.nav-tab-wrapper a').on('click', function(e) {
				e.preventDefault();
				var target = $(this).attr('href');
				$('.nav-tab').removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
				$('.scsc-tab-content').hide();
				$(target).show();
			});

<<<<<<< Updated upstream
<<<<<<< Updated upstream
			// Save existing schema
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
			$('.scsc-save').on('click', function() {
				var btn = $(this);
				var id = btn.data('id');
				var textarea = $('textarea[name="scsc_schema[' + id + ']"]');
				var rawJson = textarea.val();

				var formatted = removeIllegalCharacters(rawJson);
				if (!formatted) return;

				$.post(ajaxurl, {
					action: 'scsc_save_metabox_schema',
					nonce: $('#scsc_metabox_nonce').val(),
					schema_id: id,
<<<<<<< Updated upstream
<<<<<<< Updated upstream
					schema_json: formatted  // Now entity-encoded and cleaned exactly like dashboard
=======
					schema_json: formatted
>>>>>>> Stashed changes
=======
					schema_json: formatted
>>>>>>> Stashed changes
				}, function(resp) {
					if (resp.success) {
						btn.next('.scsc-status').fadeIn().delay(1500).fadeOut();
					} else {
						alert('Error: ' + (resp.data || 'Unknown error'));
					}
				});
			});

<<<<<<< Updated upstream
<<<<<<< Updated upstream
			// Create new schema (post or global)
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
			$('.scsc-create-new, .scsc-create-new-global').on('click', function() {
				var rawJson = $('textarea[name="scsc_new_schema"]').val();
				var formatted = removeIllegalCharacters(rawJson);
				if (!formatted) return;

				var isGlobal = $(this).hasClass('scsc-create-new-global') ? 1 : 0;

				$.post(ajaxurl, {
					action: 'scsc_create_metabox_schema',
					nonce: $('#scsc_metabox_nonce').val(),
					post_id: '<?php echo esc_js( $post_id ); ?>',
					post_type:  '<?php echo esc_js( $post_type ); ?>s',
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
				var id = $(this).data('id');
				$.post(ajaxurl, {
					action: 'scsc_delete_metabox_schema',
					nonce: $('#scsc_metabox_nonce').val(),
					schema_id: id
				}, function() {
					location.reload();
				});
			});

			$('.scsc-copy-example').on('click', function() {
				var escapedJson = $(this).attr('data-json');
<<<<<<< Updated upstream
<<<<<<< Updated upstream

				// Properly unescape HTML entities and preserve formatting
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
				var temp = document.createElement('textarea');
				temp.innerHTML = escapedJson;
				var json = temp.value;

				$('textarea[name="scsc_new_schema"]').val(json);

				$('.nav-tab-wrapper a[href="#scsc-tab-schema"]').trigger('click');
				$('html, body').animate({
					scrollTop: $('.scsc-create').offset().top - 300
				}, 500);

				alert('Example copied! Now click “Create as [...]” to add it.');
			});
		});
	</script>

	<style>
		.scsc-metabox-tabs {
			max-width: 900px;
			margin: 0 auto;
		}

		.scsc-metabox-tabs .nav-tab-wrapper {
			border: none;
			padding: 0 !important;
		}

		.scsc-metabox-tabs .nav-tab-wrapper a.nav-tab-active {
			background-color: whitesmoke;
			border-color: whitesmoke;
		}
		
		.scsc-metabox-tabs .nav-tab {
			border-top-right-radius: 5px;
			border-top-left-radius: 5px;
		}
		
		#scsc_schema_preview .inside {
			background-color: rgba(95, 77, 147, .6);
			background: linear-gradient(45deg, rgb(227, 118, 130) 15%, rgb(95, 77, 147) 85%);
			padding: 4rem 0;
		}

		#scsc_schema_preview .scsc-metabox {
			max-width: 900px;
			margin: 0 auto;
			padding: 2rem;
			background-color: whitesmoke;
			border-radius: 10px;
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
			tab-size: 2;
			background: rgb(45, 45, 45);
			color: #f8c555;
			border: 1px solid #444;
			padding: 12px;
			border-radius: 6px;
			resize: vertical;
		}
		
		#scsc_schema_preview textarea::placeholder {
			font-style: italic;
			color: #7ec699;
		}

		.scsc-example-preview {
			border: none;
		}

		.scsc-tab-content {
			padding: 20px 0;
		}
	</style>
	<?php
}