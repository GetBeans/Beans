<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Brain\Monkey\Functions;
use Beans\BeansFramework\Tests\Beans_Test_Case;

class Tests_Functions extends Beans_Test_Case {

	protected $dir;

	protected function setUp() {
		parent::setUp();
		$this->dir = __DIR__ . '/removeDir';
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	/***********************************************
	 * Tests for beans_render_function()
	 **********************************************/

	public function test_beans_render_function_bails_for_noncallable() {
		$this->assertNull( beans_render_function( 'this-callback-does-not-exist' ) );
	}

	public function test_beans_render_function() {
		$this->assertEquals( 'You called me!', beans_render_function( function () {
			echo 'You called me!';
		} ) );

		Functions\when( 'callback_for_render_function' )
			->justEcho( 'foo' );
		$this->assertSame( 'foo', beans_render_function( 'callback_for_render_function', 'foo' ) );

		$callback = function ( $foo, $bar, $baz ) {
			echo "{$foo} {$bar} {$baz}";
		};
		$this->assertSame( 'foo bar baz', beans_render_function( $callback, 'foo', 'bar', 'baz' ) );

		$callback = function ( $array, $baz ) {
			echo join( ' ', $array ) . ' ' . $baz;
		};
		$this->assertSame(
			'foo bar baz',
			beans_render_function( $callback, array( 'foo', 'bar' ), 'baz' )
		);

		$callback = function ( $object ) {
			$this->assertObjectHasAttribute( 'foo', $object );
			echo $object->foo;
		};
		$this->assertSame(
			'beans',
			beans_render_function( $callback, (object) array( 'foo' => 'beans' ) )
		);
	}

	/***********************************************
	 * Tests for beans_render_function_array()
	 **********************************************/

	public function test_beans_render_function_array_bails_for_noncallable() {
		$this->assertEmpty( beans_render_function_array( 'this-callback-does-not-exist' ) );
	}

	public function test_beans_render_function_array() {
		$this->assertEquals( 'You called me!', beans_render_function_array( function () {
			echo 'You called me!';
		} ) );

		$callback = function ( $foo, $bar, $baz ) {
			echo "{$foo} {$bar} {$baz}";
		};
		$this->assertSame(
			'foo bar baz',
			beans_render_function_array( $callback, array( 'foo', 'bar', 'baz' ) )
		);

		$callback = function ( $array, $baz ) {
			$this->assertCount( 2, $array );
			echo join( ' ', $array ) . ' ' . $baz;
		};
		$this->assertSame(
			'foo bar baz',
			beans_render_function_array( $callback, array( array( 'foo', 'bar' ), 'baz' ) )
		);

		$callback = function ( $array1, $array2 ) {
			$this->assertCount( 2, $array1 );
			$this->assertArrayHasKey( 'bar', $array1 );
			$this->assertCount( 1, $array2 );
			$this->assertArrayHasKey( 'baz', $array2 );
			echo $array1['foo'];
		};
		$this->assertSame(
			'oof',
			beans_render_function_array(
				$callback,
				array(
					array(
						'foo' => 'oof',
						'bar' => 'rab',
					),
					array( 'baz' => 'zab' ),
				)
			)
		);
	}

	/***********************************************
	 * Tests for beans_path_to_url()
	 **********************************************/

	public function test_beans_path_to_url_bails_out() {
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

	public function test_beans_path_to_url_converts() {
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

	public function test_beans_path_to_url_converts_with_trailingslash() {
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

	public function test_beans_path_to_url_removes_subfolder() {
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

	public function test_beans_path_to_url_readds_tilde() {
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

	public function test_beans_path_to_url_for_multisite() {
		$path = 'wp-content/themes';
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 10 );

		// MU Subdirectory
		$url = 'http://example.com';
		Functions\expect( 'get_blog_details' )->once()->andReturn( (object) array( 'path' => '/shop/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/' );
		$this->assertSame( "{$url}/shop/{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'get_blog_details' )->once()->andReturn( (object) array( 'path' => '/shop/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}/shop/{$path}", beans_path_to_url( $path, true ) );

		// MU Subdomain
		$url = 'http://shop.example.com';
		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( $url );
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );

		Functions\expect( 'site_url' )->once()->andReturn( $url . '/foo' );
		$this->assertSame( "{$url}/{$path}", beans_path_to_url( $path, true ) );
	}

	/***********************************************
	 * Tests for beans_sanitize_path()
	 **********************************************/

	public function test_beans_sanitize_path_for_filesystem() {
		$this->assertSame( BEANS_TESTS_DIR, beans_sanitize_path( BEANS_TESTS_DIR ) );
		$this->assertSame( __DIR__, beans_sanitize_path( __DIR__ ) );
		$this->assertSame( BEANS_TESTS_DIR, beans_sanitize_path( __DIR__ . '/../../../' ) );
		$this->assertSame( BEANS_TESTS_DIR . '/bootstrap.php', beans_sanitize_path( __DIR__ . '/../../../bootstrap.php' ) );
	}

	public function test_beans_sanitize_path_removes_windows_drive() {
		$this->assertSame( '/Program Files/tmp/', beans_sanitize_path( 'C:\Program Files\tmp\\' ) );
		$this->assertSame( '/Foo/bar/baz/index.txt', beans_sanitize_path( 'D:\Foo\bar\baz\index.txt' ) );
	}

	/***********************************************
	 * Tests for beans_path_to_url()
	 **********************************************/

	public function test_beans_url_to_path_bails_out_for_external_url() {
		Functions\expect( 'site_url' )->andReturn( 'http://getbeans.io' );
		$this->assertSame(
			'http://www.example.com/image.png',
			beans_url_to_path( 'http://www.example.com/image.png' )
		);
		$this->assertSame( 'http://www.getbeans.com', beans_url_to_path( 'http://www.getbeans.com' ) );
		$this->assertSame( 'ftp://foo.com', beans_url_to_path( 'ftp://foo.com' ) );
		$this->assertSame(
			'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ%3D%3D',
			beans_url_to_path( 'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ%3D%3D' )
		);
	}

	// Issue #65. Tests that prove the issue and validate the solution.
	public function test_beans_url_to_path_bails_out_external_url_with_internal_path() {
		Functions\expect( 'site_url' )->once()->andReturn( 'http://getbeans.io' );

		$this->assertSame(
			'http://example.com/cool-stuff-at-getbeans.io',
			beans_url_to_path( 'http://example.com/cool-stuff-at-getbeans.io' )
		);

		Functions\expect( 'site_url' )->once()->andReturn( 'https://www.getbeans.io' );
		$this->assertSame(
			'http://example.com/cool-stuff-at-www.getbeans.io',
			beans_url_to_path( 'http://example.com/cool-stuff-at-www.getbeans.io' )
		);

		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.getbeans.io' );
		$this->assertSame(
			'http://example.com/cool-stuff-at-shop.getbeans.io',
			beans_url_to_path( 'http://example.com/cool-stuff-at-shop.getbeans.io' )
		);
	}

	public function test_beans_url_to_path_converts() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->twice()->andReturn( 'https://example.com' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://example.com', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://example.com/', true ) );

		Functions\expect( 'site_url' )->twice()->andReturn( 'http://foo.com/' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://foo.com', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://foo.com/', true ) );

		Functions\expect( 'site_url' )->andReturn( 'https://example.com' );
		$this->assertSame(
			ABSPATH . 'foo/bar/index.php',
			beans_url_to_path( 'https://example.com/foo/bar/index.php', true )
		);

		$this->assertSame(
			ABSPATH . 'foo/bar/',
			beans_url_to_path( 'https://example.com/foo/bar/', true )
		);

		$this->assertSame(
			ABSPATH . 'foo/bar/baz/',
			beans_url_to_path( 'https://example.com/foo/bar/baz/', true )
		);
	}

	public function test_beans_url_to_path_remove_subfolder() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://example.com/blog/' );

		$this->assertSame(
			ABSPATH . 'blog/',
			beans_url_to_path( 'https://example.com/blog/', true )
		);

		$this->assertSame(
			ABSPATH . 'blog/foo/bar/',
			beans_url_to_path( 'https://example.com/blog/foo/bar/', true )
		);

		$this->assertSame(
			ABSPATH . 'blog/foo/bar/baz/',
			beans_url_to_path( 'https://example.com/blog/foo/bar/baz/', true )
		);
	}

	public function test_beans_url_to_path_deeply_removes_subfolder() {
		define( 'WP_SITEURL', 'https://example.com/blog/foo/' );
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://example.com/blog/' );

		$this->assertSame(
			ABSPATH . 'blog/',
			beans_url_to_path( 'https://example.com/blog/', true )
		);

		$this->assertSame(
			ABSPATH . 'blog/foo/bar/',
			beans_url_to_path( 'https://example.com/blog/foo/bar/', true )
		);

		$this->assertSame(
			ABSPATH . 'blog/foo/bar/baz/',
			beans_url_to_path( 'https://example.com/blog/foo/bar/baz/', true )
		);
	}

	// Issue #63. Tests that proved the issue.
	public function test_beans_url_to_path_converts_relative_urls() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://example.com' );

		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( '//example.com', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'example.com', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'example.com/', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'www.example.com/', true ) );

		// The following ones do not pass.
		$this->assertSame( ABSPATH . 'foo', beans_url_to_path( 'example.com/foo', true ) );
		$this->assertSame( ABSPATH . 'foo/', beans_url_to_path( 'example.com/foo/', true ) );
	}

	public function test_beans_url_to_path_removes_tilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://foo.com' );

		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://foo.com/~subdomain', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://foo.com/~subdomain/', true ) );
		$this->assertSame( ABSPATH . 'foo', beans_url_to_path( 'https://foo.com/~subdomain/foo', true ) );
		$this->assertSame( ABSPATH . 'foo/', beans_url_to_path( 'https://foo.com/~subdomain/foo/', true ) );

		// @TODO-Check with Thierry on this behavior.
		$this->assertSame( ABSPATH . 'bar/~subdomain/foo/', beans_url_to_path( 'https://foo.com/bar/~subdomain/foo/', true ) );
	}

	public function test_beans_url_to_path_for_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		// MU Subdirectory
		Functions\expect( 'get_blog_details' )->once()->andReturn( (object) array( 'path' => '/shop/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( 'http://example.com/shop/' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://example.com/shop/', true ) );

		Functions\expect( 'get_blog_details' )->once()->andReturn( (object) array( 'path' => '/shop/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( 'http://example.com/shop/image.png' );
		$this->assertSame( ABSPATH . 'image.png', beans_url_to_path( 'http://example.com/shop/image.png', true ) );

		// MU Subdomain
		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.example.com' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://shop.example.com', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.example.com/image.jpg' . '/foo' );
		$this->assertSame( ABSPATH . 'image.jpg', beans_url_to_path( 'http://shop.example.com/image.jpg', true ) );
	}

	/***********************************************
	 * Tests for beans_get()
	 **********************************************/

	public function test_beans_get_returns_default() {
		$this->assertEquals( 10, beans_get( 'foo', 'bar', 10 ) );
		$this->assertNull( beans_get( 'foo', array( 'oof' => 'found me' ) ) );
		$this->assertNull( beans_get( 'foo', array( 10, 'bar', 'baz' ) ) );
		$this->assertFalse( beans_get( 'foo', (object) array( 'bar', 'baz' ), false ) );
	}

	public function test_beans_get_finds_needle() {
		$this->assertEquals( 'bar', beans_get( 0, 'bar', 10 ) );
		$this->assertEquals( 'found me', beans_get( 'foo', array( 'foo' => 'found me' ), 10 ) );
		$this->assertEquals( 'red', beans_get( 0, array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );
		$this->assertEquals( 'zab', beans_get( 'baz', array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );

		$this->assertEquals( 'found me', beans_get( 'foo', (object) array( 'foo' => 'found me' ), 10 ) );
		$this->assertEquals( 'red', beans_get( 0, (object) array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );
		$this->assertEquals( 'zab', beans_get( 'baz', (object) array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );
	}

	/***********************************************
	 * Tests for beans_path_to_url()
	 **********************************************/

	public function test_beans_multi_array_key_exists_throws_error() {
		$this->expectException( \TypeError::class );
		$this->assertTrue( beans_multi_array_key_exists( 0, 'bar' ) );
	}

	public function test_beans_multi_array_key_exists_returns_true() {
		$this->assertTrue( beans_multi_array_key_exists( 'oof', array( 'oof' => 'found me' ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 1, array( 10, 'bar', 'baz' ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 'blue', array( 'green' => 'grass', 'blue' => 'sky' ) ) );
	}

	public function test_beans_multi_array_key_exists_returns_false() {
		$this->assertFalse( beans_multi_array_key_exists( 'foo', array( 'oof' => 'found me' ) ) );
		$this->assertFalse( beans_multi_array_key_exists( 'bar', array( 10, 'bar', 'baz' ) ) );
		$this->assertFalse( beans_multi_array_key_exists( 'red', array( 'green' => 'grass', 'blue' => 'sky' ) ) );
	}

	public function test_beans_multi_array_key_exists_handles_multidimensional_arrays() {
		$this->assertTrue( beans_multi_array_key_exists( 'zab', array( 'bar', array( 'zab' => 'foo' ) ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 'wordpress', array(
				'bar',
				'skill' => array(
					'javascript' => true,
					'php'        => true,
					'sql'        => true,
					'wordpress'  => true,
				),
			) )
		);
	}

	/***********************************************
	 * Tests for beans_count_recursive()
	 **********************************************/

	public function test_beans_count_recursive_returns_zero() {
		$this->assertEquals( 0, beans_count_recursive( 'not-an-array' ) );
		$this->assertEquals( 0, beans_count_recursive( new \stdClass() ) );
		$this->assertEquals( 0, beans_count_recursive( 10 ) );
	}

	public function test_beans_count_recursive_depth_of_one() {
		$this->assertEquals( 2, beans_count_recursive( array( 'green' => 'grass', 'blue' => 'sky' ), 1 ) );
		$this->assertEquals( 3, beans_count_recursive( array( 10, 'bar', 'baz' ), 1 ) );
		$this->assertEquals( 1, beans_count_recursive( array( 'hi there' ), 1 ) );
	}

	public function test_beans_count_recursive_nonnumeric_depth() {
		$this->assertEquals( 1, beans_count_recursive( array( 'oof' => 'found me' ), 'hi' ) );
		$this->assertEquals( 3, beans_count_recursive( array( 10, 'bar', 'baz' ), null ) );
		$this->assertEquals( 2, beans_count_recursive( array( 'green' => 'grass', 'blue' => 'sky' ) ) );
		$this->assertEquals( 10, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ) ) );
	}

	public function test_beans_count_recursive_works() {
		$this->assertEquals( 6, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ), 1 ) );
		$this->assertEquals( 15, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ), 2 ) );
		$this->assertEquals( 18, beans_count_recursive( array(
			array( 1, 2, ),
			3,
			array(
				4,
				5,
				array( 6, 7, 8, 9, array( 10, 11 ) ),
			),
		), 3 ) );
	}
}
