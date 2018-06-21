<?php
/**
 * Tests for beans_field_text()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldText
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansFieldText extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/text.php';
	}

	/**
	 * Test beans_field_text() should render the text field.
	 */
	public function test_should_render_text_field() {
		$field          = $this->merge_field_with_default( [
			'id'      => 'beans_text_test',
			'type'    => 'text',
			'default' => '',
		] );
		$field['value'] = 'Testing the text field.';

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_text( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<input id="beans_text_test" type="text" name="beans_fields[beans_text_test]" value="Testing the text field." >
EOB;
		// Run the test.
		$this->assertSame( $expected, $html );
	}

	/**
	 * Test beans_field_text() should render the text field with attributes when given.
	 */
	public function test_should_render_text_field_with_attributes_when_given() {
		$field          = $this->merge_field_with_default( [
			'id'         => 'beans_text_test',
			'type'       => 'text',
			'default'    => '',
			'attributes' => [
				'data-test' => 'foo',
			],
		] );
		$field['value'] = 'Testing the text field with attributes.';

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_text( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<input id="beans_text_test" type="text" name="beans_fields[beans_text_test]" value="Testing the text field with attributes." data-test="foo">
EOB;
		// Run the test.
		$this->assertSame( $expected, $html );
	}
}
