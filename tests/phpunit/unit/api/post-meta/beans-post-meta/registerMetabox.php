<?php
/**
 * Tests the register_metabox method of _Beans_Post_Meta.
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;
use _Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_Beans_Post_Meta_Metabox_Content
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Register_Metabox extends Beans_Post_Meta_Test_Case {

	/**
	 * Test _Beans_Post_Meta::register_metabox() should register an appropriate metabox.
	 */
	public function test_register_metabox_should_register_metabox() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'add_meta_box' )
			->once()
			->with( 'tm-beans', 'Post Options', array( $post_meta, 'metabox_content' ), 'post', 'normal', 'high' )
			->andReturn( $wp_meta_boxes['post']['normal']['high']['1'] = array(
				'id'            => 1,
				'title'         => 'Post Options',
				'callback'      => array( $this, 'metabox_content' ),
				'callback_args' => null
			) );

		$post_meta->register_metabox( 'post' );

		$this->assertEquals(
			$wp_meta_boxes['post']['normal']['high'][1],
			array(
				'id'            => 1,
				'title'         => 'Post Options',
				'callback'      => array( $this, 'metabox_content' ),
				'callback_args' => null
			) );
	}
}
