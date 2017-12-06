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
	 * Sanitized ABSPATH - to ensure it works on both OSX and Windows.
	 *
	 * @var string
	 */
	protected $abspath;

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
		if ( is_null( $this->abspath ) ) {
			$this->abspath = beans_sanitize_path( ABSPATH ) . '/';
		}
	}

	/**
	 * Test beans_url_to_path() should bail out for an external URL.
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
	 * Test beans_url_to_path() should bail out for an external URL that has an internal path, i.e.
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
	 * Test beans_url_to_path() should convert the domain URL.
	 */
	public function test_should_convert_domain_url() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->twice()->andReturn( 'https://example.com' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://example.com', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://example.com/', true ) );

		Functions\expect( 'site_url' )->twice()->andReturn( 'http://foo.com/' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'http://foo.com', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'http://foo.com/', true ) );

		Functions\expect( 'site_url' )->times( 3 )->andReturn( 'https://example.com' );
		$this->assertSame(
			$this->abspath . 'foo/bar/index.php',
			beans_url_to_path( 'https://example.com/foo/bar/index.php', true )
		);
		$this->assertSame(
			$this->abspath . 'foo/bar/',
			beans_url_to_path( 'https://example.com/foo/bar/', true )
		);
		$this->assertSame(
			$this->abspath . 'foo/bar/baz/',
			beans_url_to_path( 'https://example.com/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() should convert the IP address.
	 */
	public function test_should_convert_ip_address() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->twice()->andReturn( 'https://8.8.8.8' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://8.8.8.8', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://8.8.8.8/', true ) );

		Functions\expect( 'site_url' )->twice()->andReturn( 'https://8.8.8.8/' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://8.8.8.8', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://8.8.8.8/', true ) );

		Functions\expect( 'site_url' )->andReturn( 'https://8.8.8.8' );
		$this->assertSame(
			$this->abspath . 'foo/bar/index.php',
			beans_url_to_path( 'https://8.8.8.8/foo/bar/index.php', true )
		);
		$this->assertSame(
			$this->abspath . 'foo/bar/',
			beans_url_to_path( 'https://8.8.8.8/foo/bar/', true )
		);
		$this->assertSame(
			$this->abspath . 'foo/bar/baz/',
			beans_url_to_path( 'https://8.8.8.8/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() should convert for domain URL and remove the subfolder.
	 */
	public function test_should_convert_domain_and_remove_subfolder() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->times( 3 )->andReturn( 'https://example.com/blog/' );

		$this->assertSame(
			$this->abspath . 'blog/',
			beans_url_to_path( 'https://example.com/blog/', true )
		);

		$this->assertSame(
			$this->abspath . 'blog/foo/bar/',
			beans_url_to_path( 'https://example.com/blog/foo/bar/', true )
		);

		$this->assertSame(
			$this->abspath . 'blog/foo/bar/baz/',
			beans_url_to_path( 'https://example.com/blog/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() should convert for IP address and remove the subfolder.
	 */
	public function test_should_convert_ip_and_remove_subfolder() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://8.8.8.8/blog/' );

		$this->assertSame(
			$this->abspath . 'blog/',
			beans_url_to_path( 'https://8.8.8.8/blog/', true )
		);

		$this->assertSame(
			$this->abspath . 'blog/foo/bar/',
			beans_url_to_path( 'https://8.8.8.8/blog/foo/bar/', true )
		);

		$this->assertSame(
			$this->abspath . 'blog/foo/bar/baz/',
			beans_url_to_path( 'https://8.8.8.8/blog/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() should convert for domain URL and remove all subfolders.
	 */
	public function test_should_convert_domain_and_remove_subfolders() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->times( 3 )->andReturn( 'https://example.com/blog/' );

		$this->assertSame(
			$this->abspath . 'blog/',
			beans_url_to_path( 'https://example.com/blog/', true )
		);

		$this->assertSame(
			$this->abspath . 'blog/foo/bar/',
			beans_url_to_path( 'https://example.com/blog/foo/bar/', true )
		);

		$this->assertSame(
			$this->abspath . 'blog/foo/bar/baz/',
			beans_url_to_path( 'https://example.com/blog/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() should convert for IP address and remove all subfolders.
	 */
	public function test_should_convert_ip_and_remove_subfolders() {
		Functions\expect( 'is_main_site' )->andReturn( true );
		Functions\expect( 'site_url' )->andReturn( 'https://8.8.8.8/blog/foo' );

		$this->assertSame(
			$this->abspath . 'blog/',
			beans_url_to_path( 'https://8.8.8.8/blog/', true )
		);

		$this->assertSame(
			$this->abspath . 'blog/foo/bar/',
			beans_url_to_path( 'https://8.8.8.8/blog/foo/bar/', true )
		);

		$this->assertSame(
			$this->abspath . 'blog/foo/bar/baz/',
			beans_url_to_path( 'https://8.8.8.8/blog/foo/bar/baz/', true )
		);
	}

	/**
	 * Test beans_url_to_path() should convert a relative URL.
	 *
	 * @ticket 63
	 */
	public function test_should_convert_relative_url() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->times( 6 )->andReturn( 'https://example.com' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( '//example.com', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'example.com', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'example.com/', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'www.example.com/', true ) );
		$this->assertSame( $this->abspath . 'foo', beans_url_to_path( 'example.com/foo', true ) );
		$this->assertSame( $this->abspath . 'foo/', beans_url_to_path( 'example.com/foo/', true ) );
	}


	/**
	 * Test beans_url_to_path() should convert a relative IP address.
	 *
	 * @ticket 63
	 */
	public function test_should_convert_relative_ip_address() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->times( 6 )->andReturn( 'https://8.8.8.8' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( '//8.8.8.8', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( '8.8.8.8', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( '8.8.8.8/', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'www.8.8.8.8/', true ) );
		$this->assertSame( $this->abspath . 'foo', beans_url_to_path( '8.8.8.8/foo', true ) );
		$this->assertSame( $this->abspath . 'foo/', beans_url_to_path( '8.8.8.8/foo/', true ) );
	}

	/**
	 * Test beans_url_to_path() should convert the domain URL and remove the tilde.
	 */
	public function test_should_convert_url_and_remove_tilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->andReturn( 'https://foo.com' );

		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://foo.com/~subdomain', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://foo.com/~subdomain/', true ) );
		$this->assertSame( $this->abspath . 'foo', beans_url_to_path( 'https://foo.com/~subdomain/foo', true ) );
		$this->assertSame( $this->abspath . 'foo/', beans_url_to_path( 'https://foo.com/~subdomain/foo/', true ) );
		$this->assertSame( $this->abspath . 'bar/~subdomain/foo/', beans_url_to_path( 'https://foo.com/bar/~subdomain/foo/', true ) );
	}

	/**
	 * Test beans_url_to_path() should convert the IP address and remove the tilde.
	 */
	public function test_should_convert_ip_and_remove_tilde() {
		Functions\expect( 'is_main_site' )->andReturn( true );

		Functions\expect( 'site_url' )->andReturn( 'https://255.255.255.255' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://255.255.255.255/~subdomain', true ) );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'https://255.255.255.255/~subdomain/', true ) );
		$this->assertSame( $this->abspath . 'foo', beans_url_to_path( 'https://255.255.255.255/~subdomain/foo', true ) );
		$this->assertSame( $this->abspath . 'foo/', beans_url_to_path( 'https://255.255.255.255/~subdomain/foo/', true ) );
		$this->assertSame( $this->abspath . 'bar/~subdomain/foo/', beans_url_to_path( 'https://255.255.255.255/bar/~subdomain/foo/', true ) );
	}

	/**
	 * Test beans_url_to_path() should convert domain URL for subdirectory multisite.
	 */
	public function test_should_convert_domain_for_subdirectory_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/shop/' ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://example.com/shop/' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'http://example.com/shop/', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://example.com/shop/image.png' );
		$this->assertSame( $this->abspath . 'image.png', beans_url_to_path( 'http://example.com/shop/image.png', true ) );
	}

	/**
	 * Test beans_url_to_path() should convert IP address for subdirectory multisite.
	 */
	public function test_should_convert_ip_for_subdirectory_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/shop/' ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://8.8.8.8/shop/' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'http://8.8.8.8/shop/', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://50.50.50.50/shop/image.png' );
		$this->assertSame( $this->abspath . 'image.png', beans_url_to_path( 'http://50.50.50.50/shop/image.png', true ) );
	}

	/**
	 * Test beans_url_to_path() should convert domain URL for subdomain multisite.
	 */
	public function test_should_convert_domain_for_subdomain_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.example.com' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'http://shop.example.com', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.example.com/image.jpg/foo' );
		$this->assertSame( $this->abspath . 'image.jpg', beans_url_to_path( 'http://shop.example.com/image.jpg', true ) );
	}

	/**
	 * Test beans_url_to_path() should convert IP address for subdomain multisite.
	 */
	public function test_should_convert_ip_for_subdomain_multisite() {
		Functions\expect( 'is_main_site' )->andReturn( false );
		Functions\expect( 'get_current_blog_id' )->andReturn( 5 );

		Functions\expect( 'get_blog_details' )->andReturn( (object) array( 'path' => '/' ) );
		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.255.147.55.10' );
		$this->assertSame( rtrim( $this->abspath, '/' ), beans_url_to_path( 'http://shop.255.147.55.10', true ) );

		Functions\expect( 'site_url' )->once()->andReturn( 'http://shop.1.2.3.4/image.jpg/foo' );
		$this->assertSame( $this->abspath . 'image.jpg', beans_url_to_path( 'http://shop.1.2.3.4/image.jpg', true ) );
	}
}
