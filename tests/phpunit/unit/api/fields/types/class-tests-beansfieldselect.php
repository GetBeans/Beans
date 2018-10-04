<?php
/**
 * Tests for beans_field_select()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldSelect
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansFieldSelect extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/select.php';
	}

	/**
	 * Test beans_field_checkbox() should render the select field.
	 */
	public function test_should_render_select_field() {
		$field = $this->merge_field_with_default(
			[
				'id'      => 'beans_compile_all_scripts_mode',
				'type'    => 'select',
				'default' => 'aggressive',
				'options' => [
					'aggressive' => 'Aggressive',
					'standard'   => 'Standard',
				],
			]
		);

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_select( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<select id="beans_compile_all_scripts_mode" name="beans_fields[beans_compile_all_scripts_mode]" >
	<option value="aggressive" selected='selected'>Aggressive</option>
	<option value="standard">Standard</option>
</select>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_select() should render the select field with attributes when given.
	 */
	public function test_should_render_select_field_with_attributes_when_given() {
		$field = $this->merge_field_with_default(
			[
				'id'         => 'beans_compile_all_scripts_mode',
				'type'       => 'select',
				'default'    => 'standard',
				'options'    => [
					'aggressive' => 'Aggressive',
					'standard'   => 'Standard',
				],
				'attributes' => [
					'style' => 'margin: -3px 0 0 -8px;',
				],
			]
		);

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_select( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<select id="beans_compile_all_scripts_mode" name="beans_fields[beans_compile_all_scripts_mode]" style="margin: -3px 0 0 -8px;">
	<option value="aggressive">Aggressive</option>
	<option value="standard" selected='selected'>Standard</option>
</select>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
