<?php
/**
 * Tests for the render_fields() methods of _Beans_Term_Meta.
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Term_Meta;

use Beans\Framework\Tests\Integration\API\Term_Meta\Includes\Term_Meta_Test_Case;
use _Beans_Term_Meta;

require_once BEANS_THEME_DIR . '/lib/api/term-meta/class-beans-term-meta.php';
require_once BEANS_THEME_DIR . '/lib/api/term-meta/functions-admin.php';
require_once dirname( __DIR__ ) . '/includes/class-term-meta-test-case.php';

/**
 * Class Tests_BeanTermMeta_RenderFields
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansTermMeta_RenderFields extends Term_Meta_Test_Case {

	/**
	 * Tests _Beans_Term_Meta::render_fields() should render field html.
	 */
	public function test_should_render_field_html() {
		// Register Beans actions to render fields.
		beans_add_smart_action( 'beans_field_radio', 'beans_field_radio' );
		beans_add_smart_action( 'beans_field_checkbox', 'beans_field_checkbox' );
		beans_add_smart_action( 'beans_field_text', 'beans_field_text' );
		beans_add_smart_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
		beans_add_smart_action( 'beans_field_wrap_append_markup', 'beans_field_description' );

		// Set WP to Edit Category screen.
		set_current_screen( 'edit' );
		$_POST['taxonomy'] = 'category';

		// Register test fields.
		beans_register_term_meta( static::$test_data['fields'], 'category', 'tm-beans' );

		// Call the render_fields() method and capture the output.
		$term_meta = new _Beans_Term_Meta( 'tm-beans' );
		ob_start();
		$term_meta->render_fields();
		$html = ob_get_clean();

		// Check output is as expected.
		$beans_theme_url = BEANS_THEME_URL;
		$expected_html   = <<<FIELDSHTML
<tr class="form-field">
	<th scope="row"></th>
	<td>
		<div class="bs-field-wrap bs-radio term_meta">
			<div class="bs-field-inside">
				<div class="bs-field bs-radio">
					<fieldset class="bs-field-fieldset">
						<legend class="bs-field-legend">Layout</legend>
							<label class="" for="beans_layout_test_default_fallback">
								<input id="beans_layout_test_default_fallback" type="radio" name="beans_fields[beans_layout_test]" value="default_fallback" checked='checked' /> 	Use Default Layout
							</label>
							<label class="bs-has-image" for="beans_layout_test_c">
								<span class="screen-reader-text">Option for c</span>
								<img src="{$beans_theme_url}lib/admin/assets/images/layouts/c.png" alt="Option for c" />
								<input id="beans_layout_test_c" class="screen-reader-text" type="radio" name="beans_fields[beans_layout_test]" value="c" />
							</label>
							<label class="bs-has-image" for="beans_layout_test_c_sp">
								<span class="screen-reader-text">Option for c_sp</span>
								<img src="{$beans_theme_url}lib/admin/assets/images/layouts/c_sp.png" alt="Option for c_sp" />
								<input id="beans_layout_test_c_sp" class="screen-reader-text" type="radio" name="beans_fields[beans_layout_test]" value="c_sp" />
							</label>
							<label class="bs-has-image" for="beans_layout_test_sp_c">
								<span class="screen-reader-text">Option for sp_c</span>
								<img src="{$beans_theme_url}lib/admin/assets/images/layouts/sp_c.png" alt="Option for sp_c" />
								<input id="beans_layout_test_sp_c" class="screen-reader-text" type="radio" name="beans_fields[beans_layout_test]" value="sp_c" />
							</label>
					</fieldset>
				</div>
			</div>
		</div>
	</td>
</tr>
<tr class="form-field">
	<th scope="row">
		<label for="beans_checkbox_test">Checkbox Test Field Label</label>	</th>
	<td>
		<div class="bs-field-wrap bs-checkbox term_meta">
			<div class="bs-field-inside">
				<div class="bs-field bs-checkbox">
					<input type="hidden" value="0" name="beans_fields[beans_checkbox_test]" />
					<input id="beans_checkbox_test" type="checkbox" name="beans_fields[beans_checkbox_test]" value="1" />
					<span class="bs-checkbox-label">Enable the checkbox test</span>
				</div>
			</div>
		</div>
	</td>
</tr>
<tr class="form-field">
	<th scope="row"></th>
	<td>
		<div class="bs-field-wrap bs-text term_meta">
			<div class="bs-field-inside">
				<div class="bs-field bs-text">
					<input id="beans_text_test" type="text" name="beans_fields[beans_text_test]" value="Testing the text field." >
				</div>
			</div>
		</div>
		<p class="bs-field-description description">Sample Text Field Description</p>
	</td>
</tr>
FIELDSHTML;
		$this->assertSame( $this->format_the_html( $expected_html ), $this->format_the_html( $html ) );
	}
}
