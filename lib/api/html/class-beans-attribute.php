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
	 * Add the given attribute(s).
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Array of attributes to add.
	 *
	 * @return array
	 */
	public function add( $attributes ) {

		if ( ! isset( $attributes[ $this->attribute ] ) ) {
			$attributes[ $this->attribute ] = $this->value;
		} else {
			$attributes[ $this->attribute ] = $attributes[ $this->attribute ] . ' ' . $this->value;
		}

		return $attributes;
	}

	/**
	 * Add the given attribute(s).
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Array of attributes to add.
	 *
	 * @return array
	 */
	public function replace( $attributes ) {

		if ( $this->new_value ) {

			if ( isset( $attributes[ $this->attribute ] ) ) {
				$attributes[ $this->attribute ] = str_replace( $this->value, $this->new_value, $attributes[ $this->attribute ] );
			} else {
				$attributes[ $this->attribute ] = $this->new_value;
			}
		} else {
			$attributes[ $this->attribute ] = $this->value;
		}

		return $attributes;
	}

	/**
	 * Remove the given attribute(s).
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Array of attributes to remove.
	 *
	 * @return array
	 */
	public function remove( $attributes ) {

		if ( ! isset( $attributes[ $this->attribute ] ) ) {
			return $attributes;
		}

		if ( is_null( $this->value ) ) {
			unset( $attributes[ $this->attribute ] );
		} else {
			$attributes[ $this->attribute ] = str_replace( $this->value, '', $attributes[ $this->attribute ] );
		}

		return $attributes;
	}
}
