<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Brain\Monkey\Functions;
use Beans\BeansFramework\Tests\BeansTestCase;

class D_PathToUrlTest extends BeansTestCase {

	protected function setUp() {
		parent::setUp();
		// Make sure the file is loaded first.
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	public function testPathToUrlBailsOut() {
		$this->assertSame( 'http://getbeans.io', beans_path_to_url( 'http://getbeans.io' ) );
		$this->assertSame( 'http://www.getbeans.io', beans_path_to_url( 'http://www.getbeans.io' ) );
		$this->assertSame( 'https://getbeans.io', beans_path_to_url( 'https://getbeans.io' ) );
		$this->assertSame( 'https://www.getbeans.io', beans_path_to_url( 'https://www.getbeans.io' ) );
		$this->assertSame( '//getbeans.io', beans_path_to_url( '//getbeans.io' ) );
		$this->assertSame( '//www.getbeans.io', beans_path_to_url( '//www.getbeans.io' ) );
		$this->assertSame( 'data:,Hello%2C%20World!', beans_path_to_url( 'data:,Hello%2C%20World!' ) );
		$this->assertSame(
			'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ%3D%3D',
			beans_path_to_url( 'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ%3D%3D' )
		);
	}

	public function testConverts() {
		$url = 'https://getbeans.io';
		Functions\expect( 'site_url' )->andReturn( $url );
		Functions\expect( 'is_main_site' )->andReturn( true );

		// Absolute path
		$path = __FILE__;
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		$path = __DIR__;
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		// Relative path
		$path = '/wp-content/themes';
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		$path = 'wp-content/themes/tm-beans/';
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );
	}

	public function testConvertsWithTrailingSlash() {
		$url = 'https://getbeans.io';
		Functions\expect( 'site_url' )->andReturn( $url . '/' );
		Functions\expect( 'is_main_site' )->andReturn( true );

		// Absolute path
		$path = __FILE__;
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		$path = __DIR__;
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );

		// Relative path
		$path = 'wp-content/themes';
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );

		$path = '/wp-content/themes/tm-beans/';
		$this->assertSame( "{$url}{$path}", beans_path_to_url( $path, true ) );
	}

	public function testRemovesSubfolder() {
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

	public function testPathToUrlReAddsTilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://getbeans.io' );
		$this->assertSame( 'https://getbeans.io' . __FILE__, beans_path_to_url( __FILE__, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://example.com/~subdomain' );
		$this->assertSame( 'https://example.com/~subdomain/foo', beans_path_to_url( 'foo', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://example.com/~subdomain/baz' );
		$this->assertSame( 'https://example.com/~subdomain/foo/bar', beans_path_to_url( 'foo/bar', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://example.com/~subdomain/baz/' );
		$this->assertSame( 'https://example.com/~subdomain/foo/bar/', beans_path_to_url( '/foo/bar/', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'https://example.com/~subdomain/baz/foobar' );
		$this->assertSame( 'https://example.com/~subdomain/foo/bar', beans_path_to_url( '/foo/bar', true ) );
	}

	public function testMultisite() {
		$path = 'wp-content/themes';
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 10 );

		// MU Subdirectory
		$url = 'http://example.com';
		Functions\expect( 'get_blog_details' )->once()->andReturn( (object)array('path' => '/shop/') );
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/' );
		$this->assertSame( "{$url}/shop/{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'get_blog_details' )->once()->andReturn( (object)array('path' => '/shop/') );
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}/shop/{$path}", beans_path_to_url( $path, true ) );

		// MU Subdomain
		$url = 'http://shop.example.com';
		Functions\expect( 'get_blog_details' )->andReturn( (object)array('path' => '/') );
		Functions\expect( 'site_url' )->once()->andReturn( $url );
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );
	}
}
