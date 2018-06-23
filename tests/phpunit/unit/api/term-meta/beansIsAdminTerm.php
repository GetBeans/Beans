<?php
/**
 * Tests for _beans_is_admin_term()
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Term_Meta;

use Beans\Framework\Tests\Unit\API\Term_Meta\Includes\Beans_Term_Meta_Test_Case;
use Brain\Monkey;

require_once dirname( __FILE__ ) . '/includes/class-beans-term-meta-test-case.php';

/**
 * Class Tests_BeansIsAdminTerm
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansIsAdminTerm extends Beans_Term_Meta_Test_Case {

	/**
	 * Test _beans_is_admin_term() should return true when taxonomies are boolean true.
	 */
	public function tests_should_return_true_when_taxonomies_are_boolean_true() {
		$this->assertTrue( _beans_is_admin_term( true ) );
	}

	/**
	 * Test _beans_is_admin_term() should return false when current taxonomies cannot be found.
	 */
	public function tests_should_return_false_when_current_taxonomies_not_found() {
		Monkey\Functions\expect( 'beans_get_or_post' )
			->once()
			->with( 'taxonomy' )
			->andReturn( false );

		$this->assertFalse( _beans_is_admin_term( false ) );
	}

	/**
	 * Test _beans_is_admin_term() should return true when current taxonomy is in taxonomy array.
	 */
	public function tests_should_return_true_when_current_taxonomy_in_taxonomy_array() {
		Monkey\Functions\expect( 'beans_get_or_post' )
			->once()
			->with( 'taxonomy' )
			->andReturn( 'selected-taxonomy' );

		$this->assertTrue( _beans_is_admin_term( [ 'selected-taxonomy', 'other-taxonomy' ] ) );
	}

	/**
	 * Test _beans_is_admin_term() should return false when current taxonomy is not in taxonomy array.
	 */
	public function tests_should_return_false_when_current_taxonomy_not_in_taxonomy_array() {
		Monkey\Functions\expect( 'beans_get_or_post' )
			->once()
			->with( 'taxonomy' )
			->andReturn( 'selected-taxonomy' );

		$this->assertFalse( _beans_is_admin_term( [ 'sample-taxonomy', 'other-taxonomy' ] ) );
	}

}
