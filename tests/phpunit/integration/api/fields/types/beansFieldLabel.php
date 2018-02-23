<?php
/**
 * Tests for beans_field_label()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldLabel
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansFieldLabel extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/field.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_label', 'beans_field_label' );
		beans_remove_action( 'beans_field_description', 'beans_field_description' );
	}

	/**
	 * Test beans_field_label() should not render the field's label when none is given.
	 */
	public function test_should_not_render_field_label_when_none_given() {
		$field = $this->merge_field_with_default( array(
			'id'      => 'beans_text_test',
			'type'    => 'text',
			'default' => '',
		) );

		$this->assertNull( beans_field_label( $field ) );

		$field['label'] = '';
		$this->assertNull( beans_field_label( $field ) );
	}

	/**
	 * Test beans_field_label() should render the field's label.
	 */
	public function test_should_render_field_label() {
		$field = $this->merge_field_with_default( array(
			'id'      => 'beans_text_test',
			'type'    => 'text',
			'label'   => 'Testing the text field.',
			'default' => '',
		) );

		ob_start();
		beans_field_label( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<label id="beans_text_test">Testing the text field.</label>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
