<?php
/**
 * Safe HTML Builder with fluent interface for WordPress.
 *
 * A secure, fluent HTML builder designed specifically for WordPress admin UI.
 * It properly escapes attributes and content while providing controlled bypass
 * of wp_kses_post() sanitization for trusted top-level tags.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie / Enhanced by Grok
 * @since      1.4
 */

/**
 * This class helps developers create complex admin interfaces without constantly
 * fighting WordPress's aggressive output sanitization, while maintaining security
 * best practices.
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @since      1.4
 */
class HTML_Refactory {

	/**
	 * Tag name of the current element.
	 *
	 * @var string
	 */
	private string $tagname = 'div';

	/**
	 * Attributes of the element (name => value).
	 *
	 * Boolean attributes use true as value. Class attributes may be string or array.
	 *
	 * @var array
	 */
	private array $attributes = array();

	/**
	 * Inner text content (escaped by default).
	 *
	 * @var string
	 */
	private string $inner_text = '';

	/**
	 * Raw child HTML markup.
	 *
	 * @var string
	 */
	private string $child_elements = '';

	/**
	 * Tags that are allowed to bypass wp_kses_post() when used as the top-level tag.
	 *
	 * @var array
	 */
	private array $allowed_tags = array(
		// Form-related (essential for admin UI).
		'form',
		'input',
		'textarea',
		'button',
		'label',
		'fieldset',
		'legend',
		'optgroup',
		'select',
		'option',

		// Common admin UI tags.
		'svg',
		'path',
		'pre',
		'code',
		'hr',
		'aside',
		'main',
		'header',
		'nav',
		'div',
		'p',
		'a',
		'img',
		'strong',
		'em',
		'span',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'ul',
		'ol',
		'li',
		'table',
		'thead',
		'tbody',
		'tr',
		'th',
		'td',
	);

	/**
	 * Cached final HTML string.
	 *
	 * Used to avoid rebuilding the element on multiple renders.
	 *
	 * @var string
	 */
	private string $cached_html = '';

	/**
	 * Constructor.
	 *
	 * Sets the initial tag name.
	 *
	 * @param string $tagname Initial tag name. Defaults to 'div'.
	 */
	public function __construct( string $tagname = 'div' ) {
		$this->tagname = strtolower( $tagname );
	}

	/**
	 * Set the tag name.
	 *
	 * @param string $tagname The new tag name.
	 * @return self Current instance for method chaining.
	 */
	public function tag( string $tagname ): self {
		$this->tagname = strtolower( $tagname );
		$this->clear_cache();

		return $this;
	}

	/**
	 * Add or modify an attribute.
	 *
	 * Supports boolean attributes, array-based class merging, and removal via null/false.
	 *
	 * @param string                 $name  Attribute name.
	 * @param string|array|bool|null $value Attribute value.
	 * @return self Current instance for method chaining.
	 */
	public function attr( string $name, $value ): self {  // phpcs:ignore Generic.CodeAnalysis.UnnecessaryFinalModifier
		$name = strtolower( $name );

		if ( null === $value || false === $value ) {
			unset( $this->attributes[ $name ] );
		} elseif ( true === $value ) {
			$this->attributes[ $name ] = true; // Boolean attribute.
		} elseif ( 'class' === $name && is_array( $value ) ) {
			$existing = $this->attributes['class'] ?? array();

			// Ensure existing classes are in array format.
			if ( ! is_array( $existing ) ) {
				$existing = '' !== $existing ? explode( ' ', $existing ) : array();
			}

			$this->attributes['class'] = array_unique( array_merge( $existing, $value ) );
		} else {
			$this->attributes[ $name ] = $value;
		}

		$this->clear_cache();

		return $this;
	}

	/**
	 * Set inner text content (automatically escaped with esc_html).
	 *
	 * @param string $text Text content.
	 * @return self Current instance for method chaining.
	 */
	public function text( string $text ): self {
		$this->inner_text = esc_html( $text );
		$this->clear_cache();

		return $this;
	}

	/**
	 * Set raw (already safe) inner text.
	 *
	 * Use with extreme caution as this bypasses escaping.
	 *
	 * @param string $text Raw text content.
	 * @return self Current instance for method chaining.
	 */
	public function raw_text( string $text ): self {
		$this->inner_text = $text;
		$this->clear_cache();

		return $this;
	}

	/**
	 * Append raw child HTML.
	 *
	 * Child HTML is assumed to be safe or properly sanitized elsewhere.
	 *
	 * @param string $html Child HTML markup.
	 * @return self Current instance for method chaining.
	 */
	public function child( string $html ): self {
		$this->child_elements .= $html;
		$this->clear_cache();

		return $this;
	}

	/**
	 * Add tags to the allowed list (bypassing wp_kses_post() when top-level).
	 *
	 * @param array $tags Array of tag names to allow.
	 * @return self Current instance for method chaining.
	 */
	public function allow( array $tags ): self {
		$lowercase          = array_map( 'strtolower', $tags );
		$this->allowed_tags = array_unique( array_merge( $this->allowed_tags, $lowercase ) );
		$this->clear_cache();

		return $this;
	}

	/**
	 * Convenience method to allow common admin form tags.
	 *
	 * Ensures form-related tags bypass sanitization when used as top-level.
	 *
	 * @return self Current instance for method chaining.
	 */
	public function allow_admin_form_tags(): self {
		return $this->allow(
			array(
				'form',
				'input',
				'textarea',
				'button',
				'label',
				'fieldset',
				'legend',
				'optgroup',
				'select',
				'option',
			)
		);
	}

	/**
	 * Render the element and return the HTML string.
	 *
	 * Uses cached HTML if available.
	 *
	 * @return string Fully built and sanitized HTML.
	 */
	public function render(): string {
		if ( '' === $this->cached_html ) {
			$this->cached_html = $this->assemble_html();
		}

		return $this->cached_html;
	}

	/**
	 * Magic method to allow direct echoing/printing.
	 *
	 * @return string Rendered HTML.
	 */
	public function __toString(): string {
		return $this->render();
	}

	/**
	 * Clear the cached HTML.
	 *
	 * Called whenever the element is modified.
	 */
	private function clear_cache(): void {
		$this->cached_html = '';
	}

	/**
	 * Assemble the final HTML string.
	 *
	 * Handles void elements, attributes, content, and conditional KSES bypassing.
	 *
	 * @return string Assembled HTML.
	 */
	private function assemble_html(): string {
		$attributes = $this->format_attributes();
		$tag        = $this->tagname;

		if ( $this->is_void_element() ) {
			$html = sprintf( '<%s%s />', $tag, $attributes );
		} else {
			$content = $this->inner_text . $this->child_elements;
			$html    = sprintf( '<%1$s%2$s>%3$s</%1$s>', $tag, $attributes, $content );
		}

		// Bypass KSES only if the top-level tag is explicitly allowed.
		if ( in_array( $tag, $this->allowed_tags, true ) ) {
			return $html;
		}

		// Fallback to WordPress's safe default sanitization.
		return wp_kses_post( $html );
	}

	/**
	 * Format all attributes into a string suitable for HTML output.
	 *
	 * @return string Formatted attribute string (with leading space if attributes exist).
	 */
	private function format_attributes(): string {
		if ( array() === $this->attributes ) {
			return '';
		}

		$parts = array();

		foreach ( $this->attributes as $attr => $value ) {
			// Boolean attributes output only the name.
			if ( true === $value ) {
				$parts[] = $attr;
				continue;
			}

			// Skip empty non-class attributes.
			if ( '' === $value && 'class' !== $attr ) {
				continue;
			}

			$escaped = $this->escape_attribute( $attr, $value );
			$parts[] = sprintf( '%s="%s"', $attr, $escaped );
		}

		return $parts ? ' ' . implode( ' ', $parts ) : '';
	}

	/**
	 * Escape a single attribute value based on context.
	 *
	 * @param string $attr  Attribute name.
	 * @param mixed  $value Attribute value.
	 * @return string Escaped value.
	 */
	private function escape_attribute( string $attr, $value ): string {
		if ( 'class' === $attr ) {
			if ( is_array( $value ) ) {
				$value = implode( ' ', array_map( 'sanitize_html_class', $value ) );
			}
			return esc_attr( $value );
		}

		$uri_attrs = array( 'href', 'src', 'action', 'formaction', 'cite', 'poster', 'data', 'srcset' );
		if ( in_array( $attr, $uri_attrs, true ) && is_string( $value ) ) {
			return esc_url( $value, array( 'http', 'https', 'mailto' ) );
		}

		return esc_attr( $value );
	}

	/**
	 * Determine if the current tag is a void (self-closing) element.
	 *
	 * @return bool True if void element, false otherwise.
	 */
	private function is_void_element(): bool {
		$void = array(
			'area',
			'base',
			'br',
			'col',
			'embed',
			'hr',
			'img',
			'input',
			'link',
			'meta',
			'source',
			'track',
			'wbr',
		);

		return in_array( $this->tagname, $void, true );
	}
}
