<?php
/**
 * Example schema tab.
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

require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/vars/examples.php';

echo '<div class="d-flex flex-column">';

echo ( new HTML_Refactory( 'h2' ) )
	->text( 'Example Schema' )
	->render();

echo ( new HTML_Refactory( 'p' ) )
	->attr( 'class', array( 'alert', 'alert-info', 'mb-0' ) )
	->attr( 'role', 'alert' )
	->text( "Get a jumpstart on your website's schema!" )
	->render();

echo ( new HTML_Refactory( 'p' ) )
	->attr( 'class', array( 'lead', 'mt-3' ) )
	->text( 'Simply copy and paste any one of these schema types into the schema editor (i.e.' )
	->child(
		( new HTML_Refactory( 'a' ) )
			->attr( 'href', esc_url( '/wp-admin/admin.php?page=scsc&set_tab=homepage' ) )
			->text( 'Home Page' )
			->render()
	)
	->child( ', ' )
	->child(
		( new HTML_Refactory( 'a' ) )
			->attr( 'href', esc_url( '/wp-admin/admin.php?page=scsc&set_tab=global' ) )
			->text( 'Global' )
			->render()
	)
	->child( ', etc) of your choice.' )
	->render();

echo ( new HTML_Refactory( 'h5' ) )
	->attr( 'class', array( 'mt-5', 'fw-bold' ) )
	->text( '1. Select Schema Type:' )
	->render();

$li_tags = '';
if ( $schema_examples ) {
	$tab_count = 0;
	foreach ( $schema_examples as $key => $value ) {
		$schema_type = ucwords( $key, ' ' );

		$li_tags .= ( new HTML_Refactory( 'li' ) )
			->attr( 'class', array( 'nav-item', 'example-items', 'noselect', 'm-0' ) )
			->attr( 'data-schema-type', esc_html( $schema_type ) )
			->child(
				( new HTML_Refactory( 'div' ) )
					->attr( 'class', array( 'nav-link', 'example-nav', ( ( 0 === $tab_count ) ? sanitize_text_field( 'active' ) : '' ) ) )
					->attr( 'aria-current', 'page' )
					->text( $schema_type )
					->render()
			)
			->render();

		++$tab_count;
	}
}

echo ( new HTML_Refactory( 'ul' ) )
	->attr( 'class', array( 'nav', 'nav-pills', 'bg-light', 'p-3', 'rounded', 'border' ) )
	->child( $li_tags )
	->render();

echo ( new HTML_Refactory( 'h5' ) )
	->attr( 'class', array( 'mt-5', 'fw-bold' ) )
	->text( '2. Copy Schema:' )
	->render();

echo '<div id="example_fieldsets" class="editor-container d-flex flex-column">';

if ( $schema_examples ) {
	$fieldset_count = 0;
	foreach ( $schema_examples as $key => $value ) {
		$schema_type = ucwords( $key, ' ' );
		$style       = ( intval( $fieldset_count ) === 0 ) ? '' : esc_html( 'display:none!important' );

		echo ( new HTML_Refactory( 'fieldset' ) )
			->attr( 'class', array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'noselect' ) )
			->attr( 'data-schema-type', esc_html( $schema_type ) )
			->attr( 'style', $style )
			->child(
				( new HTML_Refactory( 'legend' ) )
					->attr( 'class', array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ) )
					->attr( 'style', 'width:auto' )
					->text( $schema_type . ':' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'button' ) )
					->attr( 'class', array( 'btn', 'btn-primary', 'py-2', 'copy-example' ) )
					->attr( 'data-bs-toggle', 'modal' )
					->attr( 'data-bs-target', '#jsonCopyModal' )
					->text( 'Copy to Clipboard' )
					->render()
			)
			->child(
				( new HTML_Refactory( 'div' ) )
					->attr( 'id', sanitize_text_field( 'example_schema_' . $schema_type ) )
					->child(
						( new HTML_Refactory( 'pre' ) )
							->attr( 'class', array( 'w-100', 'rounded', 'language-json' ) )
							->attr( 'data-id', $key )
							->attr( 'data-schema', esc_html( $value ) )
							->render()
					)
					->render()
			)
			->render();

		++$fieldset_count;
	}
}

echo '</div></div>';

// Modal block.
echo ( new HTML_Refactory( 'div' ) )
	->attr( 'class', array( 'mt-5', 'modal', 'fade' ) )
	->attr( 'id', 'jsonCopyModal' )
	->attr( 'tabindex', '-1' )
	->attr( 'aria-labelledby', 'jsonCopyModalLabel' )
	->attr( 'aria-hidden', 'true' )
	->child(
		( new HTML_Refactory( 'div' ) )
			->attr( 'class', array( 'modal-dialog', 'mt-5' ) )
			->child(
				( new HTML_Refactory( 'div' ) )
					->attr( 'class', array( 'modal-content' ) )
					->child(
						( new HTML_Refactory( 'div' ) )
						->attr( 'class', array( 'modal-header', 'border-0' ) )
						->child(
							( new HTML_Refactory( 'button' ) )
								->attr( 'id', 'close-copy-modal' )
								->attr( 'type', 'button' )
								->attr( 'class', array( 'btn-close' ) )
								->attr( 'data-bs-dismiss', 'modal' )
								->attr( 'aria-label', 'Close' )
								->render()
						)
						->render()
					)
					->child(
						( new HTML_Refactory( 'div' ) )
							->attr( 'class', array( 'modal-body', 'border-0' ) )
							->child(
								( new HTML_Refactory( 'h2' ) )
									->attr( 'class', array( 'pb-4', 'text-center' ) )
									->text( 'JSON Copied!' )
									->render()
							)
							->render()
					)
					->render()
			)
			->render()
	)
	->render();

add_action(
	'admin_footer',
	function () {
		echo <<<SCRIPTS
<script>
    function copySchema(sib) {
        navigator.clipboard.writeText(sib.innerText).then(() => {
            setTimeout(() => {document.getElementById('close-copy-modal').click();}, 1250);
        });
    }

    document.querySelectorAll('.copy-example').forEach(btn => {
        btn.addEventListener('click', (e) => {
            copySchema(e.target.nextElementSibling);
        });
    });
</script>

<script>
    var exampleTabs = document.querySelectorAll(".example-items");
    exampleTabs.forEach(tab => {
        tab.addEventListener("click", function() {
            tabToggler(this);
            fieldsetToggler(this);
        })
    })

    function tabToggler(el) {
        let exTabs = document.querySelectorAll(".example-items");
        exTabs.forEach(et => {
            et.children[0].classList.remove("active");
        })
        el.children[0].classList.add("active");
    }

    function fieldsetToggler(el) {
        let fieldsetSchemaTypes = document.querySelectorAll("fieldset[data-schema-type]");
        let exTabs = document.querySelectorAll(".example-items");
        fieldsetSchemaTypes.forEach(fst => {
            fst.setAttribute("style", "display:none!important");
            if (fst.dataset.schemaType == el.dataset.schemaType) {
                fst.removeAttribute("style");
            }
        })
    }
</script>
SCRIPTS;
	}
);
