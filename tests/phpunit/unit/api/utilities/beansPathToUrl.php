<?php
/**
 * Tests for beans_path_to_url()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey\Functions;

/**
 * Class Tests_BeansPathToUrl
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansPathToUrl extends Test_Case {

	/**
	 * Relative path to the Beans directory starting from `wp-content`.
	 *
	 * @var string
	 */
	protected $beans_relative_path;

	/**
	 * Relative path to the Unit Tests directory starting from `wp-content`.
	 *
	 * @var string
	 */
	protected $beans_tests_relative_path;

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';

		if ( ! $this->beans_relative_path ) {
			$abspath                         = rtrim( ABSPATH, '/' );
			$this->beans_relative_path       = wp_normalize_path(
				rtrim( str_replace( $abspath, '', BEANS_THEME_DIR ), DIRECTORY_SEPARATOR )
			);
			$this->beans_tests_relative_path = wp_normalize_path(
				str_replace( $abspath, '', BEANS_TESTS_DIR )
			);
		}
	}

	/**
	 * Test beans_path_to_url() should bail out when URL.
	 */
	public function test_should_bail_out_when_url() {
		$this->assertSame( 'http://getbeans.io', beans_path_to_url( 'http://getbeans.io' ) );
		$this->assertSame( 'http://www.getbeans.io', beans_path_to_url( 'http://www.getbeans.io' ) );
		$this->assertSame( 'https://getbeans.io', beans_path_to_url( 'https://getbeans.io' ) );
		$this->assertSame( 'https://www.getbeans.io', beans_path_to_url( 'https://www.getbeans.io' ) );
	}

	/**
	 * Test beans_path_to_url() should bail out when relative URL.
	 */
	public function test_should_bail_out_when_relative_url() {
		$this->assertSame( '//getbeans.io', beans_path_to_url( '//getbeans.io' ) );
		$this->assertSame( '//www.getbeans.io', beans_path_to_url( '//www.getbeans.io' ) );
	}

	/**
	 * Test beans_path_to_url() should bail out when Data URI.
	 */
	public function test_should_bail_out_when_data_url() {
		$this->assertSame( 'data:,Hello%2C%20World!', beans_path_to_url( 'data:,Hello%2C%20World!' ) );
		$this->assertSame(
			'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ%3D%3D',
			beans_path_to_url( 'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ%3D%3D' )
		);
	}

	/**
	 * Test beans_path_to_url() should bail out when IP Address.
	 */
	public function test_should_bail_out_when_ip() {
		$this->assertSame( 'http://8.8.8.8', beans_path_to_url( 'http://8.8.8.8' ) );
		$this->assertSame( 'http://8000:8.8.8.8', beans_path_to_url( 'http://8000:8.8.8.8' ) );
	}

	/**
	 * Test beans_path_to_url() should convert absolute path.
	 */
	public function test_should_convert_absolute_path() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'https://getbeans.io';
		Functions\expect( 'site_url' )->andReturn( $url );
		$url .= $this->beans_tests_relative_path;

		$this->assertSame( "{$url}/api/utilities/beansPathToUrl.php", beans_path_to_url( __FILE__, true ) );
		$this->assertSame( "{$url}/api/utilities", beans_path_to_url( __DIR__, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert relative path.
	 */
	public function test_should_convert_relative_path() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'https://getbeans.io';
		Functions\expect( 'site_url' )->andReturn( $url );

		$path = '/wp-content/themes';
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		$path = $this->beans_relative_path;
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert to IP Address when absolute path.
	 */
	public function test_should_convert_to_ip_when_absolute_path() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'https://8.8.8.8';
		Functions\expect( 'site_url' )->andReturn( $url );
		$url .= $this->beans_tests_relative_path;

		$this->assertSame( "{$url}/api/utilities/beansPathToUrl.php", beans_path_to_url( __FILE__, true ) );
		$this->assertSame( "{$url}/api/utilities", beans_path_to_url( __DIR__, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert to IP Address when relative path.
	 */
	public function test_should_convert_to_ip_when_relative_path() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'https://8.8.8.8';
		Functions\expect( 'site_url' )->andReturn( $url );

		$path = '/wp-content/themes';
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		$path = $this->beans_relative_path;
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert absolute path when domain has a trailing slash.
	 */
	public function test_should_convert_absolute_path_when_trailingslash_domain() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'https://getbeans.io';
		Functions\expect( 'site_url' )->andReturn( $url . '/' );
		$url .= $this->beans_tests_relative_path;

		$this->assertSame( "{$url}/api/utilities/beansPathToUrl.php", beans_path_to_url( __FILE__, true ) );
		$this->assertSame( "{$url}/api/utilities", beans_path_to_url( __DIR__, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert relative path when domain has a trailing slash.
	 */
	public function test_should_convert_relative_path_when_trailingslash_domain() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'https://getbeans.io';
		Functions\expect( 'site_url' )->andReturn( $url . '/' );

		$path = 'wp-content/themes';
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );

		$path = $this->beans_relative_path;
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert absolute path when IP address has a trailing slash.
	 */
	public function test_should_convert_absolute_path_when_ip_trailingslash() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'https://8000:10.127.47.355';
		Functions\expect( 'site_url' )->andReturn( $url . '/' );
		$url .= $this->beans_tests_relative_path;

		$this->assertSame( "{$url}/api/utilities/beansPathToUrl.php", beans_path_to_url( __FILE__, true ) );
		$this->assertSame( "{$url}/api/utilities", beans_path_to_url( __DIR__, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert relative path when IP address has a trailing slash.
	 */
	public function test_should_convert_relative_path_when_ip_trailingslash() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'https://9000:10.127.47.355';
		Functions\expect( 'site_url' )->andReturn( $url . '/' );

		$path = 'wp-content/themes';
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );

		$path = $this->beans_relative_path;
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should remove the domain URL's subfolder.
	 */
	public function test_should_remove_domain_subfolder() {
		$path = '/wp-content/themes';
		Functions\expect( 'is_main_site' )->andReturn( true );

		$url = 'http://example.com';
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo/' );
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo/bar' );
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo/bar/' );
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should remove the IP address' subfolder.
	 */
	public function test_should_remove_ip_subfolder() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		$url  = 'http://8.8.8.8';
		$path = '/wp-content/themes';

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo/' );
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo/bar' );
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo/bar/' );
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should re-add the domain URL's tilde upon conversion.
	 */
	public function test_should_re_add_domain_tilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://getbeans.io' );
		$this->assertSame(
			'https://getbeans.io' . $this->beans_tests_relative_path . '/api/utilities/beansPathToUrl.php',
			beans_path_to_url( __FILE__, true )
		);

		Functions\expect( 'site_url' )->once()->andReturn( 'https://example.com/~subdomain' );
		$this->assertSame( 'https://example.com/~subdomain/foo', beans_path_to_url( 'foo', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://example.com/~subdomain/baz' );
		$this->assertSame( 'https://example.com/~subdomain/foo/bar', beans_path_to_url( 'foo/bar', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://example.com/~subdomain/baz/' );
		$this->assertSame( 'https://example.com/~subdomain/foo/bar/', beans_path_to_url( '/foo/bar/', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://example.com/~subdomain/baz/foobar' );
		$this->assertSame( 'https://example.com/~subdomain/foo/bar', beans_path_to_url( '/foo/bar', true ) );
	}

	/**
	 * Test beans_path_to_url() should re-add the IP address' tilde upon conversion.
	 */
	public function test_should_re_add_ip_tilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://8.8.8.8' );
		$this->assertSame(
			'https://8.8.8.8' . $this->beans_tests_relative_path . '/api/utilities/beansPathToUrl.php',
			beans_path_to_url( __FILE__, true )
		);

		Functions\expect( 'site_url' )->once()->andReturn( 'https://17.17.17.17/~subdomain' );
		$this->assertSame( 'https://17.17.17.17/~subdomain/foo', beans_path_to_url( 'foo', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://20.20.20.20/~subdomain/baz' );
		$this->assertSame( 'https://20.20.20.20/~subdomain/foo/bar', beans_path_to_url( 'foo/bar', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://8.8.8.8/~subdomain/baz/' );
		$this->assertSame( 'https://8.8.8.8/~subdomain/foo/bar/', beans_path_to_url( '/foo/bar/', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://10.10.10.10/~subdomain/baz/foobar' );
		$this->assertSame( 'https://10.10.10.10/~subdomain/foo/bar', beans_path_to_url( '/foo/bar', true ) );
	}

	/**
	 * Test beans_path_to_url() should convert for domain URL's subdirectory multisite.
	 */
	public function test_should_convert_for_domain_subdirectory_multisite() {
		$path = 'wp-content/themes';
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 10 );

		$url = 'http://example.com';
		Functions\expect( 'get_blog_details' )->once()->andReturn( (object) array( 'path' => '/shop/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/' );
		$this->assertSame( "{$url}/shop/{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'get_blog_details' )->once()->andReturn( (object) array( 'path' => '/shop/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}/shop/{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert for IP address' subdirectory multisite.
	 */
	public function test_should_convert_for_ip_subdirectory_multisite() {
		$path = 'wp-content/themes';
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 15 );

		$url = 'http://8.8.8.8';
		Functions\expect( 'get_blog_details' )->once()->andReturn( (object) array( 'path' => '/support/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/' );
		$this->assertSame( "{$url}/support/{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'get_blog_details' )->once()->andReturn( (object) array( 'path' => '/support/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/ip-address' );
		$this->assertSame( "{$url}/support/{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert for domain URL's subdomain multisite.
	 */
	public function test_should_convert_for_domain_subdomain_multisite() {
		$path = 'wp-content/themes';
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 10 );

		$url = 'http://shop.example.com';
		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url );
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );
	}

	/**
	 * Test beans_path_to_url() should convert for IP address' subdomain multisite.
	 */
	public function test_should_convert_for_ip_subdomain_multisite() {
		$path = 'wp-content/themes';
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 10 );

		$url = 'http://shop.8.8.8.8';
		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url );
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );
	}
}
