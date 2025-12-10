<?php
/**
 * Class for properly generating HTML output.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 * @since      1.4
 */

/**
 * Safely builds and outputs HTML elements with proper escaping and optional KSES fallback.
 *
 * This utility class allows developers to construct HTML tags in an object-oriented way
 * while ensuring all attributes and content are correctly escaped. Tags explicitly
 * marked as "allowed" bypass wp_kses_post(), while everything else is sanitized
 * through WordPress' standard content filter.
 *
 * Useful for generating dynamic markup (forms, schema, admin UI, etc.) without
 * sacrificing security.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
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
	 */
	public function __construct( string $name = 'div', array $attrs = array(), string $text = '', string $children = '' ) {
		$this->tagname        = $name;
		$this->attributes     = $attrs;
		$this->inner_text     = $text;
		$this->child_elements = $children;
		$this->allowed_tags   = array( 'option', 'select' );
		$this->result         = $this->assemble_html();
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
		$tag = '<' . $this->tagname . $this->format_attributes() . ( ( ! $this->is_void_element() ) ? '>' . $this->inner_text . $this->child_elements . '</' . $this->tagname . '>' : ' />' );
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

	/**
	 * Check for self-closing HTML tag.
	 */
	private function is_void_element() {
		$void_elements = array( 'area', 'base', 'br', 'circle', 'col', 'ellipse', 'embed', 'hr', 'img', 'input', 'line', 'link', 'meta', 'param', 'path', 'polygon', 'polyline', 'rect', 'source', 'track', 'wbr', 'command', 'frame', 'keygen', 'menuitem', 'tref' );
		return in_array( $this->tagname, $void_elements );
	}
}
