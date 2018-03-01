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
 * @group   api-term-meta
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
			->andReturn( (object) array( 'post_id' => 1 ) ); // return an object with no term_id.
		Monkey\Functions\expect( 'get_option' )->never();

		$this->assertFalse( beans_get_term_meta( 'beans_layout' ) );
	}

	/**
	 * Test beans_get_term_meta() should return default when given and queried object has a term_id but term meta does not exist.
	 */
	public function test_should_return_default_when_default_given_and_queried_obj_has_term_id_but_term_meta_not_set() {
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) array( 'term_id' => 1 ) );
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'default_fallback' )
			->twice()
			->andReturn( 'default_fallback' );

		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback', 1 ) );
	}

	/**
	 * Test beans_get_term_meta() should return meta term's value when queried object has a term_id and meta for that id exists.
	 */
	public function test_should_return_term_meta_when_queried_object_has_term_id_and_meta_is_set() {
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) array( 'term_id' => 1 ) );
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'default_fallback' )
			->twice()
			->andReturn( 'c-sp' );

		$this->assertSame( 'c-sp', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
		$this->assertSame( 'c-sp', beans_get_term_meta( 'beans_layout', 'default_fallback', 1 ) );
	}

	/**
	 * Test beans_get_term_meta() should return default when given and term_id is tag_ID but term meta does not exist.
	 */
	public function test_should_return_default_when_default_given_and_term_id_is_tag_id_but_term_meta_not_set() {
		$_GET['tag_ID'] = 1;
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) array( 'post_id' => 1 ) );
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'default_fallback' )
			->once()
			->andReturn( 'default_fallback' );

		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
	}

	/**
	 * Test beans_get_term_meta() should return meta term's value when term_id is tag_ID and term meta for that id exists.
	 */
	public function test_should_return_term_meta_when_default_given_and_term_id_is_tag_id_and_term_meta_exists() {
		$_GET['tag_ID'] = 1;
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) array( 'post_id' => 1 ) );
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'default_fallback' )
			->once()
			->andReturn( 'c-sp' );

		$this->assertSame( 'c-sp', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
	}

}
