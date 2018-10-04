<?php
/**
 * Tests for the render_fields() method of _Beans_Term_Meta.
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
 * Class Tests_BeansTermMeta_RenderFields
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansTermMeta_RenderFields extends Term_Meta_Test_Case {

	/**
	 * Tests _beans_term_meta::render_fields() should render fields HTML.
	 */
	public function test_should_render_fields_html() {
		Monkey\Functions\expect( 'beans_remove_action' )
			->once()
			->with( 'beans_field_label' )
			->andReturn();
		Monkey\Functions\expect( 'beans_modify_action_hook' )
			->once()
			->with( 'beans_field_description', 'beans_field_wrap_after_markup' )
			->andReturn();
		Monkey\Functions\expect( 'beans_modify_markup' )
			->once()
			->with( 'beans_field_description', 'p' )
			->andReturn();
		Monkey\Functions\expect( 'beans_add_attribute' )
			->once()
			->with( 'beans_field_description', 'class', 'description' )
			->andReturn();
		Monkey\Functions\expect( 'beans_get_fields' )
			->once()
			->with( 'term_meta', 'tm-beans' )
			->andReturn( static::$test_data );
		Monkey\Functions\expect( 'beans_field_label' )
			->once()
			->with( static::$test_data['sample-field'] )
			->andReturnUsing(
				function () {
						echo 'field-label';
				}
			);
		Monkey\Functions\expect( 'beans_field' )
			->once()
			->with( static::$test_data['sample-field'] )
			->andReturnUsing(
				function () {
						echo 'field-markup';
				}
			);

		$terms_meta = new _Beans_Term_Meta( 'tm-beans' );

		ob_start();
		$terms_meta->render_fields();
		$actual_output = ob_get_clean();

		$this->assertContains( 'field-label', $actual_output );
		$this->assertContains( 'field-markup', $actual_output );
	}
}
