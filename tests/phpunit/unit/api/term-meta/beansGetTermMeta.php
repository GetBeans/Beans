<?php
/**
 * Tests for beans_get_term_meta()
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Term_Meta;

use Beans\Framework\Tests\Unit\API\Term_Meta\Includes\Term_Meta_Test_Case;
use Brain\Monkey;

require_once dirname( __FILE__ ) . '/includes/class-term-meta-test-case.php';

/**
 * Class Tests_BeansGetTermMeta
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansGetTermMeta extends Term_Meta_Test_Case {

	/**
	 * Test beans_get_term_meta() should return false when no default given and term meta does not exist.
	 */
	public function test_should_return_false_when_no_default_given_and_term_meta_not_set() {
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) [ 'post_id' => 1 ] ); // return an object with no term_id.
		Monkey\Functions\expect( 'get_option' )->never();

		$this->assertFalse( beans_get_term_meta( 'beans_layout' ) );
	}

	/**
	 * Test beans_get_term_meta() should return default when given and queried object has a term_id but term meta does
	 * not exist.
	 */
	public function test_should_return_default_when_default_given_and_queried_obj_has_term_id_but_term_meta_not_set() {
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) [ 'term_id' => 1 ] );
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'default_fallback' )
			->twice()
			->andReturn( 'default_fallback' );

		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback', 1 ) );
	}

	/**
	 * Test beans_get_term_meta() should return meta term's value when queried object has a term_id and meta for that
	 * ID exists.
	 */
	public function test_should_return_term_meta_when_queried_object_has_term_id_and_meta_is_set() {
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) [ 'term_id' => 1 ] );
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
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) [ 'post_id' => 1 ] );
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'tag_ID' )->andReturn( 1 );
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'default_fallback' )
			->once()
			->andReturn( 'default_fallback' );

		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
	}

	/**
	 * Test beans_get_term_meta() should return meta term's value when term_id is tag_ID and term meta for that id
	 * exists.
	 */
	public function test_should_return_term_meta_when_default_given_and_term_id_is_tag_id_and_term_meta_exists() {
		Monkey\Functions\expect( 'get_queried_object' )
			->once()
			->andReturn( (object) [ 'post_id' => 1 ] );
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'tag_ID' )->andReturn( 1 );
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_term_1_beans_layout', 'default_fallback' )
			->once()
			->andReturn( 'c-sp' );

		$this->assertSame( 'c-sp', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
	}
}
