<?php
/**
 * Tests for beans_register_term_meta()
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
 * Class Tests_BeansRegisterTermMeta
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansRegisterTermMeta extends Term_Meta_Test_Case {

	/**
	 * Test beans_register_term_meta() should return false when current taxonomy is not concerned.
	 */
	public function test_should_return_false_when_current_taxonomy_not_concerned() {
		Monkey\Functions\expect( '_beans_is_admin_term' )
			->once()
			->with( [ 'sample-taxonomy' ] )
			->andReturn( false );
		$this->assertFalse( beans_register_term_meta( static::$test_data, [ 'sample-taxonomy' ], 'tm-beans' ) );
	}

	/**
	 * Test beans_register_term_meta() should return false when not is_admin().
	 */
	public function test_should_return_false_when_not_is_admin() {
		Monkey\Functions\expect( '_beans_is_admin_term' )
			->once()
			->with( [ 'sample-taxonomy' ] )
			->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( false );

		$this->assertFalse(
			beans_register_term_meta(
				static::$test_data,
				'sample-taxonomy',
				'tm-beans'
			)
		);
	}

	/**
	 * Test beans_register_term_meta() should return false when fields cannot be registered.
	 */
	public function test_should_return_false_when_fields_cannot_be_registered() {
		Monkey\Functions\expect( '_beans_is_admin_term' )
			->once()
			->with( [ 'sample-taxonomy' ] )
			->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_register_fields' )
			->once()
			->with( static::$test_data, 'term_meta', 'tm-beans' )
			->andReturn( false );

		$this->assertFalse(
			beans_register_term_meta(
				static::$test_data,
				'sample-taxonomy',
				'tm-beans'
			)
		);
	}

	/**
	 * Test beans_register_term_meta() should return true when term meta fields are successfully registered.
	 */
	public function test_should_return_true_when_fields_are_successfully_registered() {
		Monkey\Functions\expect( '_beans_is_admin_term' )
			->once()
			->with( [ 'sample-taxonomy' ] )
			->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_register_fields' )
			->once()
			->with( static::$test_data, 'term_meta', 'tm-beans' )
			->andReturn( true );

		$this->assertTrue(
			beans_register_term_meta(
				static::$test_data,
				'sample-taxonomy',
				'tm-beans'
			)
		);
	}
}
