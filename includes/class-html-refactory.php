<?php

/**
 * Class for properly generating HTML output.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 * @since      1.4
 */
class HTML_Refactory {

	/**
	 * Element tag name.
	 *
	 * @var string $tagname
	 */
	private string $tagname;

	/**
	 * Element attributes, if any.
	 *
	 * @var array $attributes
	 */
	private array $attributes;

	/**
	 * Text content, if any.
	 *
	 * @var string $inner_text
	 */
	private string $inner_text;

	/**
	 * Child elements, if any.
	 *
	 * @var string $child_elements
	 */
	private string $child_elements;

	/**
	 * Is element self-closing.
	 *
	 * @var bool $is_self_closing
	 */
	private bool $is_self_closing;

	/**
	 * Tag that should not be removed during sanitization.
	 *
	 * @var array $allowed_tags
	 */
	private array $allowed_tags;

	/**
	 * Final string.
	 *
	 * @var string $result
	 */
	private string $result;

	/**
	 * Class constructor.
	 *
	 * @access public
	 * @param string $name Tag name of element.
	 * @param array  $attrs Element HTML attributes.
	 * @param string $text Inner text.
	 * @param string $children Child elements to be added.
	 * @param bool   $is_self_closing Whether the new element will be self-closing.
	 */
	public function __construct( string $name = 'div', array $attrs = array(), string $text = '', string $children = '', bool $is_self_closing = false ) {
		$this->tagname         = $name;
		$this->attributes      = $attrs;
		$this->inner_text      = $text;
		$this->child_elements  = $children;
		$this->is_self_closing = $is_self_closing;
		$this->allowed_tags    = array( 'option', 'select' );
		$this->result          = $this->assemble_html();
	}

	/**
	 * Outputs
	 */
	public function __toString() {
		return (string) $this->result;
	}

	/**
	 * String assembler.
	 */
	public function assemble_html() {
		$tag = '<' . $this->tagname . $this->format_attributes() . ( false === $this->is_self_closing ? '>' . $this->inner_text . $this->child_elements . '</' . $this->tagname . '>' : '/>' );
		preg_match( '/<([^ ]+)/', $tag, $is_match );
		return ( 0 <= array_search( $is_match[1], $this->allowed_tags ) ? $tag : wp_kses_post( $tag ) );
	}

	/**
	 * Add HTML attributes.
	 */
	private function format_attributes(): string {
		$attrs = array();
		foreach ( $this->attributes as $attr => $value ) {
			if ( 'class' === $attr ) {
				$attrs[] = $attr . '="' . $this->format_class_attribute( $value ) . '"';
			} elseif ( 1 === preg_match( '/^(?:https?:\/\/(?:www\.)?[a-zA-Z0-9-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?)|(?:\/[^\s]*)$/', $value ) && false === strstr( $attr, 'schema' ) ) {
				$attrs[] = $attr . '="' . $this->format_url_attribute( $value ) . '"';
			} else {
				$attrs[] = $attr . '="' . esc_attr( $value ) . '"';
			}
		}
		return ' ' . implode( ' ', $attrs );
	}

	/**
	 * Format and return classes.
	 *
	 * @param array $value Array of classes.
	 */
	private function format_class_attribute( $value ): string {
		return esc_attr( implode( ' ', $value ) );
	}

	/**
	 * Formate URL attribute.
	 *
	 * @param string $value URL as string.
	 */
	private function format_url_attribute( $value ): string {
		return esc_url( $value );
	}
}
