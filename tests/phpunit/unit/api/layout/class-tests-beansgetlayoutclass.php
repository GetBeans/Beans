<?php
/**
 * Tests for beans_get_layout_class()
 *
 * @package Beans\Framework\Tests\Unit\API\Layout
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Layout;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansGetLayoutClass
 *
 * @package Beans\Framework\Tests\Unit\API\Layout
 * @group   api
 * @group   api-layout
 */
class Tests_BeansGetLayoutClass extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions(
			array(
				'api/layout/functions.php',
				'api/utilities/functions.php',
				'api/post-meta/functions.php',
			)
		);

		Monkey\Functions\when( 'beans_get' )->alias(
			function ( $needle, $haystack ) {

				if ( isset( $haystack[ $needle ] ) ) {
					return $haystack[ $needle ];
				}
			}
		);
	}

	/**
	 * Run the tests for the given set of test parameters.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $test_parameters Array of test parameters.
	 * @param string $layout_id       The layout's ID for this test.
	 *
	 * @return void
	 */
	protected function run_the_tests( array $test_parameters, $layout_id ) {
		Monkey\Functions\when( 'is_singular' )->justReturn( true );
		Monkey\Functions\when( 'beans_get_post_meta' )->justReturn( $layout_id );

		foreach ( $test_parameters as $parameters ) {
			foreach ( $parameters['expected'] as $id => $expected ) {
				Monkey\Functions\expect( 'beans_has_widget_area' )
					->with( 'sidebar_primary' )
					->once()
					->andReturn( $parameters['sidebar_primary'] );

				if ( true === $parameters['sidebar_primary'] ) {
					Monkey\Functions\expect( 'beans_has_widget_area' )
						->with( 'sidebar_secondary' )
						->once()
						->andReturn( $parameters['sidebar_secondary'] );
				}

				Monkey\Filters\expectApplied( 'beans_layout_class_' . $id )
					->with( $expected )
					->once()
					->andReturn( $expected );

				$this->assertSame( $expected, beans_get_layout_class( $id ) );
			}
		}
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "c".
	 */
	public function test_should_return_classes_when_layout_is_c() {
		$test_parameters = array(
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			),
		);

		$this->run_the_tests( $test_parameters, 'c' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "c_sp".
	 */
	public function test_should_return_classes_when_layout_is_c_sp() {
		$test_parameters = array(
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4',
					'sidebar_primary'   => 'uk-width-medium-1-4',
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4',
					'sidebar_primary'   => 'uk-width-medium-1-4',
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			),
		);

		$this->run_the_tests( $test_parameters, 'c_sp' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "sp_c".
	 */
	public function test_should_return_classes_when_layout_is_sp_c() {
		$test_parameters = array(
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-3-4',
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-3-4',
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			),
		);

		$this->run_the_tests( $test_parameters, 'sp_c' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "c_ss".
	 */
	public function test_should_return_classes_when_layout_is_c_ss() {
		$test_parameters = array(
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => 'uk-width-medium-1-4',
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			),
		);

		$this->run_the_tests( $test_parameters, 'c_ss' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "c_sp_ss".
	 */
	public function test_should_return_classes_when_layout_is_c_sp_ss() {
		$test_parameters = array(
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4',
					'sidebar_primary'   => 'uk-width-medium-1-4',
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-2-4',
					'sidebar_primary'   => 'uk-width-medium-1-4',
					'sidebar_secondary' => 'uk-width-medium-1-4',
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			),
		);

		$this->run_the_tests( $test_parameters, 'c_sp_ss' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "ss_c".
	 */
	public function test_should_return_classes_when_layout_is_ss_c() {
		$test_parameters = array(
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => 'uk-width-medium-1-4 uk-pull-3-4',
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			),
		);

		$this->run_the_tests( $test_parameters, 'ss_c' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "sp_ss_c".
	 */
	public function test_should_return_classes_when_layout_is_sp_ss_c() {
		$test_parameters = array(
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-3-4',
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-2-4 uk-push-2-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-2-4',
					'sidebar_secondary' => 'uk-width-medium-1-4 uk-pull-2-4',
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			),
		);

		$this->run_the_tests( $test_parameters, 'sp_ss_c' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "sp_c_ss".
	 */
	public function test_should_return_classes_when_layout_is_sp_c_ss() {
		$test_parameters = array(
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-3-4',
					'sidebar_secondary' => null,
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			),
			array(
				'expected'          => array(
					'content'           => 'uk-width-medium-2-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-2-4',
					'sidebar_secondary' => 'uk-width-medium-1-4',
				),
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			),
		);

		$this->run_the_tests( $test_parameters, 'sp_c_ss' );
	}
}
