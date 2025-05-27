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

echo new HTML_Refactory(
	'h2',
	array(),
	\esc_html( 'Example Schema' )
);

echo new HTML_Refactory(
	'p',
	array(
		'class' => array( 'alert', 'alert-info', 'mb-0' ),
		'role'  => 'alert',
	),
	\esc_html( "Get a jumpstart on your website's schema!" )
);

echo new HTML_Refactory(
	'p',
	array( 'class' => array( 'lead', 'mt-3' ) ),
	'Simply copy and paste any one of these schema types into the schema editor (i.e.' . new HTML_Refactory(
		'a',
		array( 'href' => \esc_url( '/wp-admin/admin.php?page=scsc&set_tab=homepage' ) ),
		\esc_html( 'Home Page' )
	) . ', ' . new HTML_Refactory(
		'a',
		array( 'href' => \esc_url( '/wp-admin/admin.php?page=scsc&set_tab=global' ) ),
		\esc_html( 'Global' )
	) . ', etc) of your choice.'
);

echo new HTML_Refactory(
	'h5',
	array( 'class' => array( 'mt-5', 'fw-bold' ) ),
	\esc_html( '1. Select Schema Type:' )
);

$li_tags = '';
if ( $schema_examples ) {
	$tab_count = 0;
	foreach ( $schema_examples as $key => $value ) {
		$schema_type = ucwords( $key, ' ' );

		$li_tags .= new HTML_Refactory(
			'li',
			array(
				'class'            => array( 'nav-item', 'example-items', 'noselect', 'm-0' ),
				'data-schema-type' => \esc_html( $schema_type ),
			),
			'',
			new HTML_Refactory(
				'div',
				array(
					'class'        => array( 'nav-link', 'example-nav', ( ( 0 === $tab_count ) ? \sanitize_text_field( 'active' ) : '' ) ),
					'aria-current' => 'page',
				),
				\esc_html( $schema_type )
			)
		);

		++$tab_count;
	}
}

echo new HTML_Refactory(
	'ul',
	array( 'class' => array( 'nav', 'nav-pills', 'bg-light', 'p-3', 'rounded', 'border' ) ),
	'',
	$li_tags
);

echo new HTML_Refactory(
	'h5',
	array( 'class' => array( 'mt-5', 'fw-bold' ) ),
	\esc_html( '2. Copy Schema:' )
);

echo '<div id="example_fieldsets" class="editor-container d-flex flex-column">';

if ( $schema_examples ) {
	$fieldset_count = 0;
	foreach ( $schema_examples as $key => $value ) {
		$schema_type = ucwords( $key, ' ' );
		$style       = ( intval( $fieldset_count ) === 0 ) ?: \esc_html( 'display:none!important' );

		echo new HTML_Refactory(
			'fieldset',
			array(
				'class'            => array( 'd-flex', 'flex-column', 'justify-content-between', 'bg-light', 'border', 'rounded', 'p-3', 'noselect' ),
				'data-schema-type' => \esc_html( $schema_type ),
				'style'            => $style,
			),
			'',
			new HTML_Refactory(
				'legend',
				array(
					'class' => array( 'px-3', 'pb-1', 'border', 'rounded', 'bg-white' ),
					'style' => 'wdith:auto',
				),
				\esc_html( $schema_type . ':' )
			) . new HTML_Refactory(
				'button',
				array(
					'class' => array( 'btn', 'btn-primary', 'py-2', 'copy-example' ),
				),
				'Copy to Clipboard'
			) . new HTML_Refactory(
				'div',
				array( 'id' => \sanitize_text_field( 'example_schema_' . $schema_type ) ),
				'',
				new HTML_Refactory(
					'pre',
					array(
						'class'       => array( 'w-100', 'rounded', 'language-json' ),
						'data-id'     => $key,
						'data-schema' => \esc_html( $value ),
					)
				)
			)
		);

		++$fieldset_count;
	}
}

echo '</div></div>';

\add_action(
	'admin_footer',
	function () {
		echo <<<SCRIPTS
    <script>
        function copySchema(sib) {
            navigator.clipboard.writeText(sib.innerText);
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
