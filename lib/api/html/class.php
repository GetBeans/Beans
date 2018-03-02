<?php
/**
 * Control HTML attributes.
 *
 * @ignore
 *
 * @package API\Html
 */
final class _Beans_Attributes {

	/**
	 * Markup id.
	 *
	 * @type string
	 */
	private $id;

	/**
	 * Attribute type.
	 *
	 * @type string
	 */
	private $attribute;

	/**
	 * Attribute value.
	 *
	 * @type string|array Query string or Array of attributes.
	 */
	private $value;

	/**
	 * Attribute replacement value.
	 *
	 * @type string|array Query string or Array of attributes.
	 */
	private $new_value;

	/**
	 * Constructor.
	 */
	public function __construct( $id, $attribute, $value = null, $new_value = null ) {

		$this->id = $id;
		$this->attribute = $attribute;
		$this->value = $value;
		$this->new_value = $new_value;

	}

	/**
	 * Initialize action.
	 */
	public function init( $action ) {

		beans_add_filter( $this->id . '_attributes', array( $this, $action ) );

	}

	/**
	 * Add attribute.
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
	 * Replace attribute.
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
	 * Remove attribute.
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
