<?php
/**
 * Tests for the render_metabox_content() method of Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta.
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Post_Meta_Test_Case;
use Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-post-meta-test-case.php';

/**
 * Class Tests_BeansPostMeta_RenderMetaboxContent
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_RenderMetaboxContent extends Post_Meta_Test_Case {

	/**
	 * Test Beans_Post_Meta::render_metabox_content() should output post meta fields markup.
	 */
	public function test_metabox_content_should_output_fields_markup() {
		$field = [
			'id'      => 'beans_layout',
			'label'   => 'Layout',
			'type'    => 'radio',
			'context' => 'tm-beams',
			'default' => 'default_fallback',
			'options' => 'options html from layout options callback',
		];

		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		Monkey\Functions\expect( 'beans_get_fields' )
			->once()
			->with( 'post_meta', 'tm-beans' )
			->andReturn( [ $field ] );
		Monkey\Functions\expect( 'beans_field' )->once()->with( $field )->andReturnUsing(
			function () {
					echo 'beans_field_html';
			}
		);

		ob_start();
		$post_meta->render_metabox_content( 74 );
		$output = ob_get_clean();

		$this->assertEquals( 'beans_field_html', $output );
	}
}
