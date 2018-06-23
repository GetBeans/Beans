<?php
/**
 * Tests for the add() method of _Beans_Attribute.
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use _Beans_Attribute;
use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansAttribute_Add
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansAttribute_Add extends HTML_Test_Case {

	/**
	 * Test _Beans_Attribute::add() should add the attribute when it does not exist in the given attributes.
	 */
	public function test_should_add_the_attribute_when_does_not_exist() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			// Check that the attribute does not exist before we add it.
			$this->assertArrayNotHasKey( 'data-test', $markup['attributes'] );

			$actual = ( new _Beans_Attribute( $beans_id, 'data-test', 'foo' ) )->add( $markup['attributes'] );

			// Check that the attribute is added with the given value.
			$expected              = $markup['attributes'];
			$expected['data-test'] = 'foo';
			$this->assertSame( $expected, $actual );
		}
	}

	/**
	 * Test _Beans_Attribute::add() should add the attribute when an empty array is given.
	 */
	public function test_should_add_the_attribute_when_empty_array_given() {

		foreach ( array_keys( static::$test_attributes ) as $beans_id ) {
			$actual = ( new _Beans_Attribute( $beans_id, 'data-test', 'foo' ) )->add( [] );

			$this->assertSame( [ 'data-test' => 'foo' ], $actual );
		}
	}

	/**
	 * Test _Beans_Attribute::add() should add the value to an existing attribute's values.
	 */
	public function test_should_add_value_to_existing_attribute_values() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name = key( $markup['attributes'] );

			// This test seems silly as we just grabbed the key above.  But it's here to make a point that the attribute exists.
			$this->assertArrayHasKey( $name, $markup['attributes'] );

			$actual = ( new _Beans_Attribute( $beans_id, $name, 'beans-test' ) )->add( $markup['attributes'] );

			// Check that the given value is appended to the end of the existing attribute.
			$expected           = $markup['attributes'];
			$expected[ $name ] .= ' beans-test';
			$this->assertSame( $expected, $actual );
		}
	}
}
