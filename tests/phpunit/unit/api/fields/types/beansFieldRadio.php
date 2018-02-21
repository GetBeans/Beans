<?php
/**
 * Tests for beans_field_radio()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldRadio
 *
 * @package Beans\Framework\Tests\Unit\API\Fields
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansFieldRadio extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/radio.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_radio', 'beans_field_radio' );
	}

	/**
	 * Test beans_field() should render a radio field with image options.
	 */
	public function test_should_render_a_radio_field_with_image_options() {
		$field = $this->merge_field_with_default( array(
			'id'      => 'beans_layout',
			'label'   => 'Layout',
			'type'    => 'radio',
			'default' => 'default_fallback',
			'options' => array(
				'default_fallback' => 'Use Default Layout',
				'c'                => 'http://example.com/images/layouts/c.png',
				'c_sp'             => 'http://example.com/images/layouts/c_sp.png',
				'sp_c'             => 'http://example.com/images/layouts/sp_c.png',
			),
		) );

		ob_start();
		beans_field_radio( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<fieldset>
    <label class="">
        <input type="radio" name="beans_fields[beans_layout]" value="default_fallback" checked='checked' />
        Use Default Layout</label>
    <label class="bs-has-image">
        <img src="http://example.com/images/layouts/c.png" />
        <input type="radio" name="beans_fields[beans_layout]" value="c" />
    </label>
    <label class="bs-has-image">
        <img src="http://example.com/images/layouts/c_sp.png" />
        <input type="radio" name="beans_fields[beans_layout]" value="c_sp" />
    </label>
    <label class="bs-has-image">
        <img src="http://example.com/images/layouts/sp_c.png" />
        <input type="radio" name="beans_fields[beans_layout]" value="sp_c" />
    </label>
</fieldset>
EOB;

		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
