<?php
/**
 * Tests for beans_get_post_meta()
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use Beans\Framework\Tests\Integration\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansGetPostMeta extends Beans_Post_Meta_Test_Case {

	/**
	 * Test beans_get_post_meta() should return the default when the post_id cannot be resolved.
	 */
	public function test_should_return_default_when_post_id_cannot_be_resolved() {
		$this->assertFalse( beans_get_post_meta( 'beans_layout' ) );
		$this->assertSame( 'default_fallback', beans_get_post_meta( 'beans_layout', 'default_fallback' ) );
	}

	/**
	 * Test beans_get_post_meta() should return the default when the post meta does not exist.
	 */
	public function test_should_return_default_when_post_meta_does_not_exist() {
		$post_id = self::factory()->post->create( [ 'post_title' => 'Hello Beans' ] );
		update_post_meta( $post_id, 'foo', 'foo' );

		$this->assertFalse( beans_get_post_meta( 'beans_layout', false, $post_id ) );
		$this->assertSame( '', beans_get_post_meta( 'beans_layout', '', $post_id ) );
		$this->assertSame( 'c', beans_get_post_meta( 'beans_layout', 'c', $post_id ) );
	}

	/**
	 * Test beans_get_post_meta() should get the post ID when none is provided.
	 */
	public function test_should_get_post_id_when_none_is_provided() {
		$post_id      = self::factory()->post->create( [ 'post_title' => 'Hello Beans' ] );
		$_GET['post'] = $post_id;
		$this->assertSame( 'c', beans_get_post_meta( 'beans_layout', 'c' ) );
		unset( $_GET['post'] );

		$this->go_to( get_permalink( $post_id ) );
		$this->assertSame( 'c_sp', beans_get_post_meta( 'beans_layout', 'c_sp' ) );
	}

	/**
	 * Test beans_get_post_meta() should return the post's meta value when all conditions are met.
	 */
	public function test_should_return_post_meta_value() {
		$post_id = self::factory()->post->create( [ 'post_title' => 'Hello Beans' ] );
		update_post_meta( $post_id, 'beans_layout', 'c_sp' );
		$this->assertSame( 'c_sp', beans_get_post_meta( 'beans_layout', false, $post_id ) );

		$_GET['post'] = $post_id;
		update_post_meta( $post_id, 'beans_layout', 'default_fallback' );
		$this->assertSame( 'default_fallback', beans_get_post_meta( 'beans_layout', 'c' ) );
		unset( $_GET['post'] );

		update_post_meta( $post_id, 'beans_layout', 'sp_c' );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertSame( 'sp_c', beans_get_post_meta( 'beans_layout' ) );
	}
}
