<?php
/**
 * Tests for the save() method of _Beans_Term_Meta.
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Term_Meta;

use Beans\Framework\Tests\Unit\API\Term_Meta\Includes\Term_Meta_Test_Case;
use _Beans_Term_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-term-meta-test-case.php';

/**
 * Class Tests_BeansTermMeta_Save
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansTermMeta_Save extends Term_Meta_Test_Case {

	/**
	 * Test _Beans_Term_Meta::save() should return term_ID when doing_ajax.
	 */
	public function test_should_return_term_id_when_doing_ajax() {
		Monkey\Functions\expect( '_beans_doing_ajax' )->once()->andReturn( true );

		$term_meta = new _Beans_Term_Meta( 'tm-beans' );

		$this->assertEquals( 357, $term_meta->save( 357 ) );
	}

	/**
	 * Test _Beans_Term_Meta::save() should return term_ID when nonce is invalid.
	 */
	public function test_should_return_term_id_when_nonce_is_invalid() {
		Monkey\Functions\expect( '_beans_doing_ajax' )->once()->andReturn( false );
		Monkey\Functions\expect( 'beans_post' )
			->once()
			->with( 'beans_term_meta_nonce' )
			->andReturnFirstArg();
		Monkey\Functions\expect( 'wp_verify_nonce' )
			->once()
			->with( 'beans_term_meta_nonce', 'beans_term_meta_nonce' )
			->andReturn( false );

		$term_meta = new _Beans_Term_Meta( 'tm-beans' );

		$this->assertEquals( 753, $term_meta->save( 753 ) );
	}

	/**
	 * Test _Beans_Term_Meta::save() should return term_ID when fields are falsey.
	 */
	public function test_should_return_term_id_when_fields_are_falsey() {
		Monkey\Functions\expect( '_beans_doing_ajax' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_verify_nonce' )
			->once()
			->with( 'beans_term_meta_nonce', 'beans_term_meta_nonce' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_post' )
			->once()
			->with( 'beans_term_meta_nonce' )
			->andReturnFirstArg()
			->andAlsoExpectIt()
			->once()
			->with( 'beans_fields' )
			->andReturn( false );

		$term_meta = new _Beans_Term_Meta( 'tm-beans' );

		$this->assertEquals( 1234, $term_meta->save( 1234 ) );
	}

	/**
	 * Test _Beans_Term_Meta::save() should return null when fields are updated.
	 */
	public function test_should_return_null_when_fields_are_updated() {
		Monkey\Functions\when( 'stripslashes_deep' )->justReturn( 'sample-value' );
		Monkey\Functions\expect( '_beans_doing_ajax' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_verify_nonce' )
			->once()
			->with( 'beans_term_meta_nonce', 'beans_term_meta_nonce' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_post' )
			->once()
			->with( 'beans_term_meta_nonce' )
			->andReturnFirstArg()
			->andAlsoExpectIt()
			->once()
			->with( 'beans_fields' )
			->andReturn( static::$test_data );
		Monkey\Functions\expect( 'update_option' )
			->once()
			->with( 'beans_term_1234_sample-field', 'sample-value' )
			->andReturn( true );

		$term_meta = new _Beans_Term_Meta( 'tm-beans' );

		$this->assertNull( $term_meta->save( 1234 ) );
	}
}
