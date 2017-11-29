<?php
/**
 * Tests for beans_url_to_path()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey\Functions;

/**
 * Class Tests_BeansUrlToPath
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansUrlToPath extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_url_to_path() bails out for an external URL.
	 */
	public function test_should_bail_out_when_external_url() {
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

		$this->assertSame( 'http://8.8.8.8', beans_url_to_path( 'http://8.8.8.8' ) );
		$this->assertSame( 'http://8000:8.8.8.8', beans_url_to_path( 'http://8000:8.8.8.8' ) );
	}

	/**
	 * Test beans_url_to_path() bails out for an external URL that has an internal path, i.e.
	 * meaning the site's URL is within the URL's path.
	 *
	 * @ticket 65
	 */
	public function test_should_bail_out_when_external_url_with_internal_path() {
		Functions\expect( 'site_url' )->twice()->andReturn( 'http://getbeans.io' );
		$this->assertSame(
			'http://example.com/cool-stuff-at-getbeans.io',
			beans_url_to_path( 'http://example.com/cool-stuff-at-getbeans.io' )
		);
		$this->assertSame(
			'http://8.8.8.8/cool-stuff-at-shop.getbeans.io',
			beans_url_to_path( 'http://8.8.8.8/cool-stuff-at-shop.getbeans.io' )
		);

		Functions\expect( 'site_url' )->twice()->andReturn( 'https://www.getbeans.io' );
		$this->assertSame(
			'http://example.com/cool-stuff-at-www.getbeans.io',
			beans_url_to_path( 'http://example.com/cool-stuff-at-www.getbeans.io' )
		);
		$this->assertSame(
			'http://8.8.8.8/cool-stuff-at-shop.getbeans.io',
			beans_url_to_path( 'http://8.8.8.8/cool-stuff-at-shop.getbeans.io' )
		);

		Functions\expect( 'site_url' )->twice()->andReturn( 'http://shop.getbeans.io' );
		$this->assertSame(
			'http://example.com/cool-stuff-at-shop.getbeans.io',
			beans_url_to_path( 'http://example.com/cool-stuff-at-shop.getbeans.io' )
		);
		$this->assertSame(
			'http://8.8.8.8/cool-stuff-at-shop.getbeans.io',
			beans_url_to_path( 'http://8.8.8.8/cool-stuff-at-shop.getbeans.io' )
		);
	}

	/**
	 * Test beans_url_to_path() converts the URL.
	 */
	public function test_should_convert_domain_url() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->twice()->andReturn( 'https://example.com' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://example.com', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://example.com/', true ) );

		Functions\expect( 'site_url' )->twice()->andReturn( 'http://foo.com/' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://foo.com', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://foo.com/', true ) );

		Functions\expect( 'site_url' )->times( 3 )->andReturn( 'https://example.com' );
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

	/**
	 * Test beans_url_to_path() should converts IP addresses.
	 */
	public function test_should_convert_ip_address() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->twice()->andReturn( 'https://8.8.8.8' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://8.8.8.8', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://8.8.8.8/', true ) );

		Functions\expect( 'site_url' )->twice()->andReturn( 'https://8.8.8.8/' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://8.8.8.8', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://8.8.8.8/', true ) );

		Functions\expect( 'site_url' )->andReturn( 'https://8.8.8.8' );
		$this->assertSame(
			ABSPATH . 'foo/bar/index.php',
			beans_url_to_path( 'https://8.8.8.8/foo/bar/index.php', true )
		);
		$this->assertSame(
			ABSPATH . 'foo/bar/',
			beans_url_to_path( 'https://8.8.8.8/foo/bar/', true )
		);
		$this->assertSame(
			ABSPATH . 'foo/bar/baz/',
			beans_url_to_path( 'https://8.8.8.8/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() converts for domain and removes the subfolder.
	 */
	public function test_should_convert_domain_and_removes_subfolder() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->times( 3 )->andReturn( 'https://example.com/blog/' );

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

	/**
	 * Test beans_url_to_path() converts for IP address and removes the subfolder.
	 */
	public function test_should_convert_ip_and_removes_subfolder() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://8.8.8.8/blog/' );

		$this->assertSame(
			ABSPATH . 'blog/',
			beans_url_to_path( 'https://8.8.8.8/blog/', true )
		);

		$this->assertSame(
			ABSPATH . 'blog/foo/bar/',
			beans_url_to_path( 'https://8.8.8.8/blog/foo/bar/', true )
		);

		$this->assertSame(
			ABSPATH . 'blog/foo/bar/baz/',
			beans_url_to_path( 'https://8.8.8.8/blog/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() converts for domain and removes all subfolders.
	 */
	public function test_should_convert_domain_and_removes_subfolders() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->times( 3 )->andReturn( 'https://example.com/blog/' );

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

	/**
	 * Test beans_url_to_path() converts for domain and removes all subfolders.
	 */
	public function test_should_convert_ip_and_removes_subfolders() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://8.8.8.8/blog/foo' );

		$this->assertSame(
			ABSPATH . 'blog/',
			beans_url_to_path( 'https://8.8.8.8/blog/', true )
		);

		$this->assertSame(
			ABSPATH . 'blog/foo/bar/',
			beans_url_to_path( 'https://8.8.8.8/blog/foo/bar/', true )
		);

		$this->assertSame(
			ABSPATH . 'blog/foo/bar/baz/',
			beans_url_to_path( 'https://8.8.8.8/blog/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() converts a relative URL.
	 *
	 * @ticket 63
	 */
	public function test_should_convert_relative_urls() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->times( 6 )->andReturn( 'https://example.com' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( '//example.com', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'example.com', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'example.com/', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'www.example.com/', true ) );
		$this->assertSame( ABSPATH . 'foo', beans_url_to_path( 'example.com/foo', true ) );
		$this->assertSame( ABSPATH . 'foo/', beans_url_to_path( 'example.com/foo/', true ) );
	}


	/**
	 * Test beans_url_to_path() converts a relative URL.
	 *
	 * @ticket 63
	 */
	public function test_should_convert_relative_ip_address() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->times( 6 )->andReturn( 'https://8.8.8.8' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( '//8.8.8.8', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( '8.8.8.8', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( '8.8.8.8/', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'www.8.8.8.8/', true ) );
		$this->assertSame( ABSPATH . 'foo', beans_url_to_path( '8.8.8.8/foo', true ) );
		$this->assertSame( ABSPATH . 'foo/', beans_url_to_path( '8.8.8.8/foo/', true ) );
	}

	/**
	 * Test beans_url_to_path() converts the domain URL and removes the tilde.
	 */
	public function test_should_convert_url_and_remove_tilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->andReturn( 'https://foo.com' );

		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://foo.com/~subdomain', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://foo.com/~subdomain/', true ) );
		$this->assertSame( ABSPATH . 'foo', beans_url_to_path( 'https://foo.com/~subdomain/foo', true ) );
		$this->assertSame( ABSPATH . 'foo/', beans_url_to_path( 'https://foo.com/~subdomain/foo/', true ) );
		$this->assertSame( ABSPATH . 'bar/~subdomain/foo/', beans_url_to_path( 'https://foo.com/bar/~subdomain/foo/', true ) );
	}

	/**
	 * Test beans_url_to_path() converts the IP address and removes the tilde.
	 */
	public function test_should_convert_ip_and_remove_tilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->andReturn( 'https://255.255.255.255' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://255.255.255.255/~subdomain', true ) );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'https://255.255.255.255/~subdomain/', true ) );
		$this->assertSame( ABSPATH . 'foo', beans_url_to_path( 'https://255.255.255.255/~subdomain/foo', true ) );
		$this->assertSame( ABSPATH . 'foo/', beans_url_to_path( 'https://255.255.255.255/~subdomain/foo/', true ) );
		$this->assertSame( ABSPATH . 'bar/~subdomain/foo/', beans_url_to_path( 'https://255.255.255.255/bar/~subdomain/foo/', true ) );
	}

	/**
	 * Test beans_url_to_path() converts domain for subdirectory multisite.
	 */
	public function test_converts_domain_for_subdirectory_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/shop/' ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://example.com/shop/' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://example.com/shop/', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://example.com/shop/image.png' );
		$this->assertSame( ABSPATH . 'image.png', beans_url_to_path( 'http://example.com/shop/image.png', true ) );
	}

	/**
	 * Test beans_url_to_path() converts IP address for subdirectory multisite.
	 */
	public function test_converts_ip_for_subdirectory_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/shop/' ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://8.8.8.8/shop/' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://8.8.8.8/shop/', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://50.50.50.50/shop/image.png' );
		$this->assertSame( ABSPATH . 'image.png', beans_url_to_path( 'http://50.50.50.50/shop/image.png', true ) );
	}

	/**
	 * Test beans_url_to_path() converts domain for subdomain multisite.
	 */
	public function test_converts_domain_for_subdomain_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.example.com' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://shop.example.com', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.example.com/image.jpg/foo' );
		$this->assertSame( ABSPATH . 'image.jpg', beans_url_to_path( 'http://shop.example.com/image.jpg', true ) );
	}

	/**
	 * Test beans_url_to_path() converts IP Address for subdomain multisite.
	 */
	public function test_converts_ip_for_subdomain_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.255.147.55.10' );
		$this->assertSame( rtrim( ABSPATH, '/' ), beans_url_to_path( 'http://shop.255.147.55.10', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.1.2.3.4/image.jpg/foo' );
		$this->assertSame( ABSPATH . 'image.jpg', beans_url_to_path( 'http://shop.1.2.3.4/image.jpg', true ) );
	}
}
