<?php
/**
 * Tests for beans_add_attributes().
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansAddAttributes
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansAddAttributes extends HTML_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setup() {
		parent::setUp();

		Monkey\Functions\when( 'wp_parse_args' )->alias(
			function( $args ) {

				if ( is_array( $args ) ) {
					return $args;
				}

				if ( is_object( $args ) ) {
					return get_object_vars( $args );
				}

				if ( is_string( $args ) ) {
					parse_str( $args, $result );
					return $result;
				}
			}
		);
	}

	/**
	 * Test beans_add_attributes() should return the built attributes string.
	 */
	public function test_should_return_built_attributes_string() {

		foreach ( static::$test_attributes as $id => $config ) {
			Monkey\Functions\expect( 'beans_apply_filters' )
				->with( $id . '_attributes', $config['attributes'] )
				->once()
				->andReturn( $config['attributes'] );

			$expected = $this->convert_attributes_into_html( $config['attributes'] );
			$this->assertSame( $expected, beans_add_attributes( $id, $config['attributes'] ) );
		}
	}

	/**
	 * Test beans_add_attributes() should return an empty string when no attributes are given.
	 */
	public function test_should_return_empty_string_when_no_attributes() {
		Monkey\Functions\expect( 'beans_apply_filters' )
			->with( 'foo_attributes', [] )
			->times( 4 )
			->andReturn( [] );

		$this->assertSame( '', beans_add_attributes( 'foo' ) );
		$this->assertSame( '', beans_add_attributes( 'foo', null ) );
		$this->assertSame( '', beans_add_attributes( 'foo', '' ) );
		$this->assertSame( '', beans_add_attributes( 'foo', false ) );
	}

	/**
	 * Test beans_add_attributes() should pass additional arguments when given.
	 */
	public function test_should_pass_additional_arguments_when_given() {
		$attributes = [ 'class' => 'foo' ];
		Monkey\Functions\expect( 'beans_apply_filters' )
			->with( 'foo_attributes', $attributes, 14, 'hi' )
			->once()
			->andReturn( $attributes );

		$this->assertSame( 'class="foo"', beans_add_attributes( 'foo', $attributes, 14, 'hi' ) );
	}
}
