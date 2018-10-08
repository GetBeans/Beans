<?php
/**
 * Tests for the ok_to_save() method of Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Post_Meta_Test_Case;
use Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-post-meta-test-case.php';

/**
 * Class Tests_BeansPostMeta_OkToSave
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_OkToSave extends Post_Meta_Test_Case {

	/**
	 * Test Beans_Post_Meta::ok_to_save() should return false when nonce check fails.
	 */
	public function test_ok_to_save_should_return_false_when_nonce_check_fails() {
		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( false );
		$this->assertFalse( $post_meta->ok_to_save( 456, [ [ 'id' => 'beans_test_slider' ] ] ) );
	}

	/**
	 * Test Beans_Post_Meta::ok_to_save() should return false when `edit post` user permissions are not met.
	 */
	public function test_ok_to_save_should_return_false_when_user_permissions_not_met() {
		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )
			->once()
			->with( 'edit_post', 456 )
			->andReturn( false );
		$this->assertFalse( $post_meta->ok_to_save( 456, [ [ 'id' => 'beans_test_slider' ] ] ) );
	}

	/**
	 * Test Beans_Post_Meta::ok_to_save() should return false when post meta has no fields.
	 */
	public function test_ok_to_save_should_return_false_when_no_fields() {
		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		$this->assertFalse( $post_meta->ok_to_save( 456, [] ) );
	}

	/**
	 * Test Beans_Post_Meta::ok_to_save() should return true when all conditions for saving are met.
	 */
	public function test_ok_to_save_should_return_true_when_all_conditions_met() {
		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		$this->assertTrue( $post_meta->ok_to_save( 456, [ [ 'id' => 'beans_test_slider' ] ] ) );
	}
}
