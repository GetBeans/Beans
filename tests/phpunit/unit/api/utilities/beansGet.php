<?php
/**
 * Tests for beans_get()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansGet
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansGet extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_get() should find the needle when an array is given.
	 */
	public function test_should_find_needle_when_array_given() {
		$haystack = array(
			'post_type'       => 'foo',
			'number_of_posts' => 5,
		);

		$this->assertSame( $haystack['number_of_posts'], beans_get( 'number_of_posts', $haystack ) );
		$this->assertSame( 'foo', beans_get( 'post_type', $haystack ) );
	}

	/**
	 * Test beans_get() should return default value when an array is given.
	 */
	public function test_should_return_default_when_array_given() {
		$haystack = array(
			'post_type'       => 'foo',
			'number_of_posts' => 5,
		);

		$this->assertSame( 'published', beans_get( 'post_status', $haystack, 'published' ) );
		$this->assertFalse( beans_get( 'post_status', $haystack, false ) );
		$this->assertNull( beans_get( 'post_status', $haystack ) );
	}

	/**
	 * Test beans_get() should find the needle when an object is given.
	 */
	public function test_should_find_needle_when_object_given() {
		$haystack = (object) array(
			'post_type'       => 'foo',
			'number_of_posts' => 5,
		);

		$this->assertSame( $haystack->number_of_posts, beans_get( 'number_of_posts', $haystack ) );
		$this->assertSame( 'foo', beans_get( 'post_type', $haystack ) );
	}

	/**
	 * Test beans_get() should return default value when an object is given.
	 */
	public function test_should_return_default_when_object_given() {
		$haystack = (object) array(
			'post_type'       => 'foo',
			'number_of_posts' => 5,
		);

		$this->assertSame( 'published', beans_get( 'post_status', $haystack, 'published' ) );
		$this->assertFalse( beans_get( 'post_status', $haystack, false ) );
		$this->assertNull( beans_get( 'post_status', $haystack ) );
	}

	/**
	 * Test beans_get() should return default value when a literal (hard coded) value is given.
	 */
	public function test_should_return_default_when_literal_given() {
		$this->assertNull( beans_get( 'foo', 'bar' ) );
		$this->assertSame( 'foo', beans_get( 'foo', 10, 'foo' ) );
		$this->assertFalse( beans_get( 10, 'Testing is fun!', false ) );
	}

	/**
	 * Test beans_get() should find the needle when the array's index is given.
	 */
	public function test_should_find_needle_when_index_given() {
		$this->assertEquals( 'bar', beans_get( 0, 'bar', 10 ) );

		$data = array(
			'red',
			'white',
			'foo' => 'baz',
			'green',
		);
		$this->assertEquals( 'red', beans_get( 0, $data ) );
		$this->assertEquals( 'white', beans_get( 1, $data ) );
		$this->assertEquals( 'green', beans_get( 2, $data ) );
	}

	/**
	 * Test beans_get() should return the value from the $_GET superglobal.
	 */
	public function test_should_get_value_from_get_superglobal() {
		$_GET['beans'] = 'Testing is fun!';
		$this->assertSame( 'Testing is fun!', beans_get( 'beans' ) );
	}
}
