<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Brain\Monkey\Functions;
use Beans\BeansFramework\Tests\BeansTestCase;

class F_UrlToPathTest extends BeansTestCase {

	protected function setUp() {
		parent::setUp();
		// Make sure the file is loaded first.
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	public function testNotInternalUrlBailsOut() {
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
	public function testNotInternalUrlBailsOutWonky() {
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

	public function testConverts() {
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

	public function testRemoveSubfolder() {
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

	public function testDeepRemoveSubfolder() {
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
	public function testWithOutHttp() {
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

	// Issue #66.  Tests that proved the issue.
	public function testRemovesTilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://foo.com' );

		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://foo.com/~subdomain', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://foo.com/~subdomain/', true ) );
		$this->assertSame( ABSPATH . 'foo', beans_url_to_path( 'https://foo.com/~subdomain/foo', true ) );
		$this->assertSame( ABSPATH . 'foo/', beans_url_to_path( 'https://foo.com/~subdomain/foo/', true ) );

		// @TODO-Check with Thierry on this behavior.
		$this->assertSame( ABSPATH . 'bar/~subdomain/foo/', beans_url_to_path( 'https://foo.com/bar/~subdomain/foo/', true ) );
	}


	public function testMultisite() {
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
}
