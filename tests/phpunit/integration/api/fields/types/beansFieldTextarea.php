<?php
/**
 * Tests for beans_field_textarea()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldTextarea
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   integration-tests
 * @group   api
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
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_textarea', 'beans_field_textarea' );
	}

	/**
	 * Test beans_field_textarea() should render the textarea field.
	 */
	public function test_should_render_text_field() {
		$field          = $this->merge_field_with_default( array(
			'id'      => 'beans_textarea_test',
			'type'    => 'textarea',
			'default' => '',
		) );
		$field['value'] = 'Testing the textarea field.';

		ob_start();
		beans_field_textarea( $field );
		$html = ob_get_clean();

		// Run the test.
		$this->assertSame( '<textarea name="beans_fields[beans_textarea_test]" >Testing the textarea field.</textarea>', $html );
	}

	/**
	 * Test beans_field_textarea() should render the textarea field with attributes when given.
	 */
	public function test_should_render_text_field_with_attributes_when_given() {
		$field          = $this->merge_field_with_default( array(
			'id'         => 'beans_textarea_test',
			'type'       => 'textarea',
			'default'    => '',
			'attributes' => array(
				'data-test' => 'foo',
			),
		) );
		$field['value'] = 'Testing the textarea field with attributes.';

		ob_start();
		beans_field_textarea( $field );
		$html = ob_get_clean();

		// Run the test.
		$this->assertSame( '<textarea name="beans_fields[beans_textarea_test]" data-test="foo">Testing the textarea field with attributes.</textarea>', $html );
	}
}
