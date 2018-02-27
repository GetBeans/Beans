<?php
/**
 * Tests for beans_get_term_meta()
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 *
 * @since   1.5.0
 */

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansGetTermMeta extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/term-meta/functions.php';
	}

	/**
	 * Test beans_get_term_meta() should return false when default not given and term meta does not exist.
	 */
	public function test_should_return_false_when_no_optional_arguments_given_and_term_meta_not_set() {
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) array( 'term_id' => 1 ) );
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', false )
			->once()
			->andReturn( false );

		// run the function without providing any optional parameters
		$this->assertFalse( beans_get_term_meta( 'beans_layout' ) );
	}

	/**
	 * Test beans_get_term_meta() should return default when given and term meta does not exist.
	 */
	public function test_should_return_default_when_default_given_and_term_meta_not_set() {
		// get_queried_objects() will be called only when term_id is not provided.
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) array( 'term_id' => 1 ) );
		// get_option() will be called both times with and without term_id.
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'c' )
			->twice()
			->andReturn( 'c' );

		// Run the function once without providing the term_id and once with term_id.
		// In both cases, should return the default.
		$this->assertSame( 'c', beans_get_term_meta( 'beans_layout', 'c' ) );
		$this->assertSame( 'c', beans_get_term_meta( 'beans_layout', 'c', 1 ) );
	}

	/**
	 * Test beans_get_term_meta() should return meta term's value when it exists.
	 */

	public function test_should_return_term_meta_when_meta_is_set() {
		// get_queried_objects() will be called only when term_id is not provided.
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) array( 'term_id' => 1 ) );
		// get_option() will be called both times with and without term_id.
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'c' )
			->twice()
			->andReturn( 'r' );

		// Run the function once without providing the term_id and once with term_id.
		// In both cases, should return the term meta.
		$this->assertSame( 'r', beans_get_term_meta( 'beans_layout', 'c' ) );
		$this->assertSame( 'r', beans_get_term_meta( 'beans_layout', 'c', 1 ) );
	}

}
