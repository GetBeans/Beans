<?php
/**
 * Tests for the remove() method of _Beans_Attribute.
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use _Beans_Attribute;
use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansAttribute_Remove
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansAttribute_Remove extends HTML_Test_Case {

	/**
	 * Test _Beans_Attribute::remove() should return the original attributes when the target attribute does not exist, meaning there's
	 * nothing to remove in the given attributes.
	 */
	public function test_should_return_original_attributes_when_target_attribute_does_not_exist() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			// Check that the attribute does not exist before we run the test.
			$this->assertArrayNotHasKey( 'data-test', $markup['attributes'] );

			// Run the remove.
			$actual = ( new _Beans_Attribute( $beans_id, 'data-test', 'test' ) )->remove( $markup['attributes'] );

			// Check that the original attributes are returned.
			$this->assertSame( $markup['attributes'], $actual );
		}
	}

	/**
	 * Test _Beans_Attribute::remove() should remove the attribute when the given value is null.
	 */
	public function test_should_remove_attribute_when_value_is_null() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name = key( $markup['attributes'] );

			// Run the remove.
			$actual = ( new _Beans_Attribute( 'beans_test_post', $name ) )->remove( $markup['attributes'] );

			// Check that the attribute is removed.
			$this->assertArrayNotHasKey( $name, $actual );

			// Check that only that attribute is affected.
			$expected = $markup['attributes'];
			unset( $expected[ $name ] );
			$this->assertSame( $expected, $actual );
		}
	}

	/**
	 * Test _Beans_Attribute::remove() should remove the given value from the attribute.
	 */
	public function test_should_remove_the_given_value_from_attribute() {
		$attributes = [
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		];

		// Run the remove.
		$actual = ( new _Beans_Attribute( 'beans_post', 'class', 'uk-panel-box' ) )->remove( $attributes );

		// Check that it removed only that attribute value.
		$this->assertNotContains( 'uk-panel-box', $actual['class'] );
		$this->assertSame( 'uk-article  category-beans', $actual['class'] );

		// Check that only the class attribute is affected.
		$expected          = $attributes;
		$expected['class'] = 'uk-article  category-beans';
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test _Beans_Attribute::remove() should return an empty array when an empty array is given. Why? There is nothing to remove, as there are
	 * no attributes.
	 */
	public function test_should_return_empty_array_when_empty_array_given() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name  = key( $markup['attributes'] );
			$value = current( $markup['attributes'] );

			// Run the remove.
			$actual = ( new _Beans_Attribute( $beans_id, $name, $value ) )->remove( [] );

			// Check that an empty array is returned.
			$this->assertSame( [], $actual );
		}
	}
}
