<?php
/**
 * Tests for beans_field_slider()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldSlider
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansFieldSlider extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/slider.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_slider', 'beans_field_slider' );
		beans_remove_action( 'beans_field_enqueue_scripts_slider', 'beans_field_enqueue_scripts_slider' );
	}

	/**
	 * Test beans_field_slider() should render the slider field.
	 */
	public function test_should_render_slider_field() {
		$field = $this->merge_field_with_default( array(
			'id'          => 'beans_test_slider',
			'label'       => 'Test Slider',
			'description' => 'Testing the slider',
			'type'        => 'slider',
			'default'     => 0,
			'min'         => 0,
			'max'         => 100,
			'interval'    => 1,
		) );

		ob_start();
		beans_field_slider( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-slider-wrap" slider_min="0" slider_max="100" slider_interval="1">
    <input id="beans_test_slider" type="text" value="0" name="beans_fields[beans_test_slider]" style="display: none;" />
</div>
<span class="bs-slider-value">0</span>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_slider() should render the slider field with unit when given.
	 */
	public function test_should_render_slider_field_with_unit_when_given() {
		$field = $this->merge_field_with_default( array(
			'id'          => 'beans_test_slider',
			'label'       => 'Test Slider',
			'description' => 'Testing the slider',
			'type'        => 'slider',
			'default'     => 0,
			'min'         => 10,
			'max'         => 100,
			'interval'    => 5,
			'unit'        => 'Number of beans',
		) );

		ob_start();
		beans_field_slider( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-slider-wrap" slider_min="10" slider_max="100" slider_interval="5">
    <input id="beans_test_slider" type="text" value="0" name="beans_fields[beans_test_slider]" style="display: none;" />
</div>
<span class="bs-slider-value">0</span>
<span class="bs-slider-unit">Number of beans</span>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_slider() should render the slider with the current value.
	 */
	public function test_should_render_slider_with_current_value() {
		$field          = $this->merge_field_with_default( array(
			'id'          => 'beans_test_slider',
			'label'       => 'Test Slider',
			'description' => 'Testing the slider',
			'type'        => 'slider',
			'default'     => 0,
			'min'         => 10,
			'max'         => 100,
			'interval'    => 5,
		) );
		$field['value'] = 15;

		ob_start();
		beans_field_slider( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-slider-wrap" slider_min="10" slider_max="100" slider_interval="5">
    <input id="beans_test_slider" type="text" value="15" name="beans_fields[beans_test_slider]" style="display: none;" />
</div>
<span class="bs-slider-value">15</span>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_slider() should render the slider field with unit when given.
	 */
	public function test_should_render_slider_field_with_attributes_when_given() {
		$field = $this->merge_field_with_default( array(
			'id'          => 'beans_test_slider',
			'label'       => 'Test Slider',
			'description' => 'Testing the slider',
			'type'        => 'slider',
			'default'     => 1,
			'min'         => 1,
			'max'         => 20,
			'interval'    => 1,
			'unit'        => 'Number of beans',
			'attributes'  => array(
				'data-test' => 'foo',
			),
		) );

		ob_start();
		beans_field_slider( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-slider-wrap" slider_min="1" slider_max="20" slider_interval="1">
    <input id="beans_test_slider" type="text" value="1" name="beans_fields[beans_test_slider]" style="display: none;" data-test="foo"/>
</div>
<span class="bs-slider-value">1</span>
<span class="bs-slider-unit">Number of beans</span>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
