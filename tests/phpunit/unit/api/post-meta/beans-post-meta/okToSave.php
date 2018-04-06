<?php
/**
 * Tests the ok_to_save method of _Beans_Post_Meta.
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;
use _Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_Beans_Post_Meta_Ok_To_Save
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Ok_To_Save extends Beans_Post_Meta_Test_Case {

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false with unverified nonce.
	 */
	public function test_ok_to_save_should_return_false_when_unverified_nonce() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( false );
		$this->assertFalse( $post_meta->ok_to_save( 456, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false with invalid user permissions.
	 */
	public function test_ok_to_save_should_return_false_when_user_cannot_edit() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )
			->once()
			->with( 'edit_post', 456 )
			->andReturn( false );
		$this->assertFalse( $post_meta->ok_to_save( 456, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false when post meta has no fields.
	 */
	public function test_ok_to_save_should_return_false_when_fields_empty() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		$this->assertFalse( $post_meta->ok_to_save( 456, array() ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is true when all conditions for saving are met.
	 */
	public function test_ok_to_save_should_return_true_when_all_conditions_met() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		$this->assertTrue( $post_meta->ok_to_save( 456, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}
}
