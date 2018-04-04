<?php
/**
 * Tests for _beans_standardize_radio_image()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansStandardizeRadioImage
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansStandardizeRadioImage extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/radio.php';
	}

	/**
	 * Test _beans_standardize_radio_image() should standardize when only the image's source is given.
	 */
	public function test_should_standardize_when_only_src_given() {
		$test_data = array(
			'c'    => 'http://example.com/images/layouts/c.png',
			'c_sp' => 'http://example.com/images/layouts/c_sp.png',
			'sp_c' => 'http://example.com/images/layouts/sp_c.png',
		);

		foreach ( $test_data as $value => $radio ) {
			$expected = array(
				'src'                => $radio,
				'alt'                => "Option for {$value}",
				'screen_reader_text' => "Option for {$value}",
			);
			$this->assertSame( $expected, _beans_standardize_radio_image( $value, $radio ) );
		}
	}

	/**
	 * Test _beans_standardize_radio_image() should standardize when the image's alt value is not given.
	 */
	public function test_should_standardize_when_alt_is_not_given() {
		$test_data = array(
			'c'    => array(
				'src'                => 'http://example.com/images/layouts/c.png',
				'screen_reader_text' => 'Content Only Layout',
			),
			'c_sp' => array(
				'src'                => 'http://example.com/images/layouts/c_sp.png',
				'screen_reader_text' => 'Content + Sidebar Primary Layout',
			),
			'sp_c' => array(
				'src'                => 'http://example.com/images/layouts/c_sp.png',
				'screen_reader_text' => 'Sidebar Primary + Content Layout',
			),
		);

		foreach ( $test_data as $value => $radio ) {
			$expected = array(
				'src'                => $radio['src'],
				'alt'                => $radio['screen_reader_text'],
				'screen_reader_text' => $radio['screen_reader_text'],
			);
			$this->assertSame( $expected, _beans_standardize_radio_image( $value, $radio ) );
		}
	}

	/**
	 * Test _beans_standardize_radio_image() should standardize when the image's `screen_reader_text` is not given.
	 */
	public function test_should_standardize_when_screen_reader_text_is_not_given() {
		$test_data = array(
			'c'    => array(
				'src' => 'http://example.com/images/layouts/c.png',
				'alt' => 'Content Only Layout',
			),
			'c_sp' => array(
				'src' => 'http://example.com/images/layouts/c_sp.png',
				'alt' => 'Content + Sidebar Primary Layout',
			),
			'sp_c' => array(
				'src' => 'http://example.com/images/layouts/c_sp.png',
				'alt' => 'Sidebar Primary + Content Layout',
			),
		);

		foreach ( $test_data as $value => $radio ) {
			$expected                       = $radio;
			$expected['screen_reader_text'] = $radio['alt'];
			$this->assertSame( $expected, _beans_standardize_radio_image( $value, $radio ) );
		}
	}

	/**
	 * Test _beans_standardize_radio_image() should standardize when all of the image's parameters are given.
	 */
	public function test_should_standardize_when_all_parameters_given() {
		$test_data = array(
			'c'    => array(
				'src'                => 'c.png',
				'alt'                => 'Content Only Layout',
				'screen_reader_text' => 'Option to select the Content Only Layout',
			),
			'c_sp' => array(
				'src'                => 'c_sp.png',
				'alt'                => 'Content + Sidebar Primary Layout',
				'screen_reader_text' => 'Option to select the Content + Sidebar Primary Layout',
			),
			'sp_c' => array(
				'src'                => 'c_sp.png',
				'alt'                => 'Sidebar Primary + Content Layout',
				'screen_reader_text' => 'Option to select the Sidebar Primary + Content Layout',
			),
		);

		foreach ( $test_data as $value => $radio ) {
			$this->assertSame( $radio, _beans_standardize_radio_image( $value, $radio ) );
		}
	}
}
