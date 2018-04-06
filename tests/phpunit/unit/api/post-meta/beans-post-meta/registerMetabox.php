<?php
/**
 * registerMetabox.php
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;
use _Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-beans-post-meta-test-case.php';

class Tests_BeansPostMeta_RegisterMetabox extends Beans_Post_Meta_Test_Case {

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
