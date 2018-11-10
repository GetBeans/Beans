<?php
/**
 * Tests for beans_build_skip_links().
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
 * Class Tests_BeansBuildSkipLinks
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansBuildSkipLinks extends HTML_Test_Case {

	/**
	 * Test beans_build_skip_links() should call beans_has_primary_sidebar() and beans_has_secondary_sidebar() when the layout is not full-width('c').
	 */
	public function test_should_call_beans_has_primary_sidebar_and_beans_has_secondary_sidebar_when_layout_not_full_width() {
		Monkey\Functions\expect( 'beans_get_layout' )->once()->andReturn( 'c_sp' );
		Monkey\Functions\expect( 'has_nav_menu' )
			->once()
			->with( 'primary' )
			->andReturn( 'true' );
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )
			->once()
			->with( 'c_sp' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )
			->once()
			->with( 'c_sp' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_output_skip_links' )
			->once()
			->andReturn();

		$this->assertNull( beans_build_skip_links() );
	}

	/**
	 * Test beans_build_skip_links() should not call beans_has_primary_sidebar() or beans_has_secondary_sidebar() when the layout is full-width.
	 */
	public function test_should_not_call_beans_has_primary_sidebar_or_beans_has_secondary_sidebar_when_full_width_layout() {
		Monkey\Functions\expect( 'beans_get_layout' )->once()->andReturn( 'c' );
		Monkey\Functions\expect( 'has_nav_menu' )
			->once()
			->with( 'primary' )
			->andReturn( 'true' );
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )->never();
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )->never();

		Monkey\Functions\expect( 'beans_output_skip_links' )
		->once()
		->andReturn();

		$this->assertNull( beans_build_skip_links() );
	}

	/**
	 * Test beans_build_skip_links() should not call beans_output_skip_links() when there are no skip links.
	 */
	public function test_should_not_call_beans_output_skip_links_when_no_skip_links() {
		Monkey\Functions\expect( 'beans_get_layout' )->once()->andReturn( 'c' );
		Monkey\Functions\expect( 'has_nav_menu' )
			->once()
			->with( 'primary' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_has_primary_sidebar' )->never();
		Monkey\Functions\expect( 'beans_has_secondary_sidebar' )->never();

		Monkey\Filters\expectApplied( 'beans_skip_links_list' )
			->once()
			->andReturn( null );

		Monkey\Functions\expect( 'beans_output_skip_links' )->never();

		$this->assertFalse( beans_build_skip_links() );
	}
}
