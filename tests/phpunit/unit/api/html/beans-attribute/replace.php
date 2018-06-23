<?php
/**
 * Tests for the replace() method of _Beans_Attribute.
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
 * Class Tests_BeansAttribute_Replace
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansAttribute_Replace extends HTML_Test_Case {

	/**
	 * Test _Beans_Attribute::replace() should replace an existing attribute value.
	 */
	public function test_should_replace_existing_attribute_value() {
		$attributes = [
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		];

		$instance = new _Beans_Attribute( 'beans_post', 'class', 'uk-panel-box', 'beans-test' );

		// Check that the attribute does not contain the new value.
		$this->assertNotContains( 'beans-test', $attributes['class'] );

		// Run the replace.
		$actual = $instance->replace( $attributes );

		// Check that the attribute is added with the given value.
		$this->assertContains( 'beans-test', $actual['class'] );
		$this->assertNotContains( 'uk-panel-box', $actual['class'] );
	}

	/**
	 * Test _Beans_Attribute::replace() should replace (overwrite) all attribute's values with the new value when the target value is
	 * empty (null, empty string, etc.).
	 */
	public function test_should_overwrite_attribute_values_with_new_value() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name = key( $markup['attributes'] );

			// Check when both value and new value are null.
			$actual = ( new _Beans_Attribute( 'beans_post', $name ) )->replace( $markup['attributes'] );
			$this->assertNull( $actual[ $name ] );

			// Check when the value is null.
			$actual = ( new _Beans_Attribute( 'beans_post', $name, null, '' ) )->replace( $markup['attributes'] );
			$this->assertSame( '', $actual[ $name ] );

			// Check when the value is an empty string.
			$actual = ( new _Beans_Attribute( 'beans_post', $name, '', 'foo' ) )->replace( $markup['attributes'] );
			$this->assertSame( 'foo', $actual[ $name ] );

			// Check when the target value is false.
			$actual = ( new _Beans_Attribute( 'beans_post', $name, false, 'foo' ) )->replace( $markup['attributes'] );
			$this->assertSame( 'foo', $actual[ $name ] );
		}
	}

	/**
	 * Test _Beans_Attribute::replace() should add the attribute when it does not exists in the given attributes.
	 */
	public function test_should_add_attribute_when_it_does_not_exist() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {

			$instance = new _Beans_Attribute( $beans_id, 'data-test', 'foo', 'beans-test' );

			// Check that the attribute does not exist.
			$this->assertArrayNotHasKey( 'data-test', $markup['attributes'] );

			// Run the replace.
			$actual = $instance->replace( $markup['attributes'] );

			// Check that the new attribute is added.
			$this->assertArrayHasKey( 'data-test', $actual );
			$this->assertSame( 'beans-test', $actual['data-test'] );

			// Check that only the data-test attribute is affected.
			$expected              = $markup['attributes'];
			$expected['data-test'] = 'beans-test';
			$this->assertSame( $expected, $actual );
		}
	}

	/**
	 * Test _Beans_Attribute::replace() should add the attribute when an empty array is given.
	 */
	public function test_should_add_attribute_when_an_empty_array_given() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name   = key( $markup['attributes'] );
			$value  = current( $markup['attributes'] );
			$actual = ( new _Beans_Attribute( $beans_id, $name, $value, 'beans-test' ) )->replace( [] );

			// Check that it did add the attribute.
			$this->assertArrayHasKey( $name, $actual );
			$this->assertSame( [ $name => 'beans-test' ], $actual );
		}
	}
}
