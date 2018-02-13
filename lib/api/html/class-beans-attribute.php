<?php
/**
 * This class provides the means to add, replace, and remove a HTML attribute and its value(s).
 *
 * @package Beans\Framework\API\Actions
 *
 * @since   1.5.0
 */

/**
 * Control a HTML attribute.
 *
 * @since   1.0.0
 * @since   1.5.0 Changed class name.
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\HTML
 */
final class _Beans_Attribute {

	/**
	 * The markup ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Name of the HTML attribute.
	 *
	 * @var string
	 */
	private $attribute;

	/**
	 * Value of the HTML attribute.
	 *
	 * If set to '' will display the attribute value as empty (e.g. class=""). Setting it to 'false' will only display
	 * the attribute name (e.g. data-example). Setting it to 'null' will not display anything.
	 *
	 * @var string
	 */
	private $value;

	/**
	 * Attribute replacement value.
	 *
	 * If set to '' will display the attribute value as empty (e.g. class=""). Setting it to 'false' will only display
	 * the attribute name (e.g. data-example). Setting it to 'null' will not display anything.
	 *
	 * @var string
	 */
	private $new_value;

	/**
	 * _Beans_Attributes constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $id              The markup ID.
	 * @param string      $attribute       Name of the HTML attribute.
	 * @param string|null $value           Optional.  Value of the HTML attribute.
	 * @param string|null $new_value       Optional. Attribute replacement value, which is a query string or an array of
	 *                                     attributes.
	 */
	public function __construct( $id, $attribute, $value = null, $new_value = null ) {
		$this->id        = $id;
		$this->attribute = $attribute;
		$this->value     = $value;
		$this->new_value = $new_value;
	}

	/**
	 * Initialize by registering the attribute filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $method Method to register as the callback for this filter.
	 *
	 * @return void
	 */
	public function init( $method ) {
		beans_add_filter( $this->id . '_attributes', array( $this, $method ) );
	}

	/**
	 * Add a value to an existing attribute or add a new attribute.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Array of HTML markup attributes.
	 *
	 * @return array
	 */
	public function add( array $attributes ) {

		if ( $this->has_attribute( $attributes ) ) {
			$attributes[ $this->attribute ] .= ' ' . $this->value;
		} else {
			$attributes[ $this->attribute ] = $this->value;
		}

		return $attributes;
	}

	/**
	 * Replace the attribute's value. If the attribute does not exist, it is added with the new value.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Allows replacement of all values.
	 *
	 * @param array $attributes Array of HTML markup attributes.
	 *
	 * @return array
	 */
	public function replace( array $attributes ) {

		if ( $this->has_attribute( $attributes ) && ! empty( $this->value ) ) {
			$attributes[ $this->attribute ] = $this->replace_value( $attributes[ $this->attribute ] );
		} else {
			$attributes[ $this->attribute ] = $this->new_value;
		}

		return $attributes;
	}

	/**
	 * Remove a specific value from the attribute or remove the entire attribute.
	 *
	 * When the attribute value to remove is null, the attribute is removed; else, the value is removed.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Array of HTML markup attributes.
	 *
	 * @return array
	 */
	public function remove( array $attributes ) {

		if ( ! $this->has_attribute( $attributes ) ) {
			return $attributes;
		}

		if ( is_null( $this->value ) ) {
			unset( $attributes[ $this->attribute ] );
		} else {
			$attributes[ $this->attribute ] = $this->replace_value( $attributes[ $this->attribute ] );
		}

		return $attributes;
	}

	/**
	 * Checks if the attribute exists in the given attributes.
	 *
	 * @since 1.5.0
	 *
	 * @param array $attributes Array of HTML markup attributes.
	 *
	 * @return bool
	 */
	private function has_attribute( array $attributes ) {
		return isset( $attributes[ $this->attribute ] );
	}

	/**
	 * Replace the attribute's value.
	 *
	 * @since 1.5.0
	 *
	 * @param string $value The current attribute's value.
	 *
	 * @return string
	 */
	private function replace_value( $value ) {
		return str_replace( $this->value, $this->new_value, $value );
	}
}
