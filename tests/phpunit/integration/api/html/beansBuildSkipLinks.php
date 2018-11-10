<?php
/**
 * Tests for beans_build_skip_links()
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansBuildSkipLinks
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansBuildSkipLinks extends HTML_Test_Case {

	/**
	 * Test beans_build_skip_links() should not output sidebar skip links when the full-width layout ('c') is selected.
	 */
	public function test_should_not_output_sidebar_skip_links_when_layout_c_selected() {
		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c' );

		$expected = <<<EOB
<ul class="beans-skip-links">
<li ><a href="#beans-content" class="screen-reader-shortcut">Skip to the content.</a></li>
</ul>
EOB;
		ob_start();
		beans_build_skip_links();
		$actual = ob_get_clean();

		$this->assertContains( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}

	/**
	 * Test beans_build_skip_links() should output content and primary sidebar skip links.
	 */
	public function test_should_output_content_primary_sidebar_skip_links() {
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )
			->once()
			->with( 'c_sp' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )
			->once()
			->with( 'c_sp' )
			->andReturn( false );

		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_sp';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_sp' );

		$expected = <<<EOB
<ul class="beans-skip-links">
<li ><a href="#beans-content" class="screen-reader-shortcut">Skip to the content.</a></li>
<li ><a href="#beans-primary-sidebar" class="screen-reader-shortcut">Skip to the primary sidebar.</a></li>
</ul>
EOB;
		ob_start();
		beans_build_skip_links();
		$actual = ob_get_clean();

		$this->assertContains( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}

	/**
	 * Test beans_build_skip_links() should output content and secondary sidebar skip links.
	 */
	public function test_should_output_content_secondary_sidebar_skip_links() {
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )
			->once()
			->with( 'c_ss' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )
			->once()
			->with( 'c_ss' )
			->andReturn( true );

		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_ss';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_ss' );

		$expected = <<<EOB
<ul class="beans-skip-links">
<li ><a href="#beans-content" class="screen-reader-shortcut">Skip to the content.</a></li>
<li ><a href="#beans-secondary-sidebar" class="screen-reader-shortcut">Skip to the secondary sidebar.</a></li>
</ul>
EOB;
		ob_start();
		beans_build_skip_links();
		$actual = ob_get_clean();

		$this->assertContains( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}


	/**
	 * Test beans_build_skip_links() should output content and both sidebar skip links.
	 */
	public function test_should_output_content_both_sidebar_skip_links() {
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )
			->once()
			->with( 'c_sp_ss' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )
			->once()
			->with( 'c_sp_ss' )
			->andReturn( true );

		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_sp_ss';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_sp_ss' );

		$expected = <<<EOB
<ul class="beans-skip-links">
<li ><a href="#beans-content" class="screen-reader-shortcut">Skip to the content.</a></li>
<li ><a href="#beans-primary-sidebar" class="screen-reader-shortcut">Skip to the primary sidebar.</a></li>
<li ><a href="#beans-secondary-sidebar" class="screen-reader-shortcut">Skip to the secondary sidebar.</a></li>
</ul>
EOB;
		ob_start();
		beans_build_skip_links();
		$actual = ob_get_clean();

		$this->assertContains( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}

	/**
	 * Test beans_build_skip_links() should output content and only the primary sidebar skip links when layout is 'c_sp_ss' but secondry sidebar is not active.
	 */
	public function test_should_output_content_only_primary_sidebar_skip_links_when_secondary_sidebar_not_active() {
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )
			->once()
			->with( 'c_sp_ss' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )
			->once()
			->with( 'c_sp_ss' )
			->andReturn( false );

		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_sp_ss';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_sp_ss' );

		$expected = <<<EOB
<ul class="beans-skip-links">
<li ><a href="#beans-content" class="screen-reader-shortcut">Skip to the content.</a></li>
<li ><a href="#beans-primary-sidebar" class="screen-reader-shortcut">Skip to the primary sidebar.</a></li>
</ul>
EOB;
		ob_start();
		beans_build_skip_links();
		$actual = ob_get_clean();

		$this->assertContains( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}

	/**
	 * Test beans_build_skip_links() should output content and only the secondary sidebar skip links when layout is 'c_sp_ss' but primary sidebar is not active.
	 */
	public function test_should_output_content_only_secondary_sidebar_skip_links_when_primary_sidebar_not_active_() {
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )
			->once()
			->with( 'c_sp_ss' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )
			->once()
			->with( 'c_sp_ss' )
			->andReturn( true );

		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_sp_ss';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_sp_ss' );

		$expected = <<<EOB
<ul class="beans-skip-links">
<li ><a href="#beans-content" class="screen-reader-shortcut">Skip to the content.</a></li>
<li ><a href="#beans-secondary-sidebar" class="screen-reader-shortcut">Skip to the secondary sidebar.</a></li>
</ul>
EOB;
		ob_start();
		beans_build_skip_links();
		$actual = ob_get_clean();

		$this->assertContains( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}

	/**
	 * Test beans_build_skip_links() should output primary navigation, content and both sidebar skip links.
	 */
	public function test_should_output_primary_navigation_content_and_both_sidebar_skip_links() {
		Monkey\Functions\expect( 'has_nav_menu' )
			->once()
			->with( 'primary' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )
			->once()
			->with( 'c_sp_ss' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )
			->once()
			->with( 'c_sp_ss' )
			->andReturn( true );

		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_sp_ss';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_sp_ss' );

		$expected = <<<EOB
<ul class="beans-skip-links">
<li ><a href="#beans-primary-navigation" class="screen-reader-shortcut">Skip to the primary navigation.</a></li>
<li ><a href="#beans-content" class="screen-reader-shortcut">Skip to the content.</a></li>
<li ><a href="#beans-primary-sidebar" class="screen-reader-shortcut">Skip to the primary sidebar.</a></li>
<li ><a href="#beans-secondary-sidebar" class="screen-reader-shortcut">Skip to the secondary sidebar.</a></li>
</ul>
EOB;
		ob_start();
		beans_build_skip_links();
		$actual = ob_get_clean();

		$this->assertContains( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}

	/**
	 * Test beans_build_skip_links() should return false when no skip links because beans_skip_links_list filter unsets the array.
	 */
	public function test_should_return_false_when_filter_unsets_the_array_of_skip_links() {
		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_sp';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_sp' );

		add_filter(
			'beans_skip_links_list',
			function( $skip_links ) {
				unset( $skip_links['beans-content'], $skip_links['beans-primary-sidebar'] );
				return $skip_links;
			}
		);

		$this->assertFalse( beans_build_skip_links() );
	}

	/**
	 * Test beans_build_skip_links() should return false when beans_skip_links_list filter returns null.
	 */
	public function test_should_return_false_when_filter_returns_null() {
		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c' );

		add_filter( 'beans_skip_links_list', '__return_null' );
		$this->assertFalse( beans_build_skip_links() );
	}

}
