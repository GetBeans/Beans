<?php
/**
 * Tests for beans_field_textarea()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldTextarea
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansFieldTextarea extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/textarea.php';
	}

	/**
	 * Test beans_field_textarea() should render the textarea field.
	 */
	public function test_should_render_textarea_field() {
		$field          = $this->merge_field_with_default( array(
			'id'      => 'beans_textarea_test',
			'type'    => 'textarea',
			'default' => '',
		) );
		$field['value'] = 'Testing the textarea field.';

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_textarea( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<textarea id="beans_textarea_test" name="beans_fields[beans_textarea_test]" >Testing the textarea field.</textarea>
EOB;
		// Run the test.
		$this->assertSame( $expected, $html );
	}

	/**
	 * Test beans_field_textarea() should render the textarea field with attributes when given.
	 */
	public function test_should_render_textarea_field_with_attributes_when_given() {
		$field          = $this->merge_field_with_default( array(
			'id'         => 'beans_textarea_test',
			'type'       => 'textarea',
			'default'    => '',
			'attributes' => array(
				'data-test' => 'foo',
			),
		) );
		$field['value'] = 'Testing the textarea field with attributes.';

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_textarea( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<textarea id="beans_textarea_test" name="beans_fields[beans_textarea_test]" data-test="foo">Testing the textarea field with attributes.</textarea>
EOB;
		// Run the test.
		$this->assertSame( $expected, $html );
	}
}
