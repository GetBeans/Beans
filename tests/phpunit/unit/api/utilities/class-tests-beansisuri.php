<?php
/**
 * Tests for _beans_is_uri()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansIsUri
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   api
 * @group   api-utilities
 */
class Tests_BeansIsUri extends Test_Case {

	/**
	 * Test _beans_is_uri() should return false when given input does not start with http, https, //, or data.
	 */
	public function test_should_return_false() {
		$this->assertFalse( _beans_is_uri( __FILE__ ) );
		$this->assertFalse( _beans_is_uri( __DIR__ ) );
		$this->assertFalse( _beans_is_uri( '/blog' ) );
		$this->assertFalse( _beans_is_uri( ':,Hello%2C%20World!' ) );
		$this->assertFalse( _beans_is_uri( ':text/plain;base64,SGVsbG8sIFdvcmxkIQ%3D%3D' ) );
	}

	/**
	 * Test _beans_is_uri() should return true when input starts with http.
	 */
	public function test_should_return_true_when_http() {
		$this->assertTrue( _beans_is_uri( 'http://getbeans.io' ) );
		$this->assertTrue( _beans_is_uri( 'http://www.getbeans.io' ) );
		$this->assertTrue( _beans_is_uri( 'http://getbeans.io/blog/' ) );
		$this->assertTrue( _beans_is_uri( 'http://8.8.8.8' ) );
		$this->assertTrue( _beans_is_uri( 'http://8000:8.8.8.8' ) );
	}

	/**
	 * Test _beans_is_uri() should return true when input starts with https.
	 */
	public function test_should_return_true_when_https() {
		$this->assertTrue( _beans_is_uri( 'https://getbeans.io' ) );
		$this->assertTrue( _beans_is_uri( 'https://www.getbeans.io' ) );
		$this->assertTrue( _beans_is_uri( 'https://getbeans.io/blog/' ) );
		$this->assertTrue( _beans_is_uri( 'http://8.8.8.8' ) );
		$this->assertTrue( _beans_is_uri( 'http://8000:8.8.8.8' ) );
	}

	/**
	 * Test _beans_is_uri() should return true when input is a relative URL.
	 */
	public function test_should_return_true_when_relative_url() {
		$this->assertTrue( _beans_is_uri( '//getbeans.io' ) );
		$this->assertTrue( _beans_is_uri( '//www.getbeans.io' ) );
		$this->assertTrue( _beans_is_uri( '//getbeans.io/blog/' ) );
		$this->assertTrue( _beans_is_uri( '//8.8.8.8' ) );
		$this->assertTrue( _beans_is_uri( '//8000:8.8.8.8' ) );
	}

	/**
	 * Test _beans_is_uri() should return true when Data URI.
	 */
	public function test_should_return_true_when_data_url() {
		$this->assertTrue( _beans_is_uri( 'data:,Hello%2C%20World!' ) );
		$this->assertTrue( _beans_is_uri( 'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ%3D%3D' ) );
	}
}
