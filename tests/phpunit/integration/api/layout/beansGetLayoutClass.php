<?php
/**
 * Tests for beans_get_layout_class()
 *
 * @package Beans\Framework\Tests\Integration\API\Layout
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Layout;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Class Tests_BeansGetLayoutClass
 *
 * @package Beans\Framework\Tests\Integration\API\Layout
 * @group   api
 * @group   api-layout
 */
class Tests_BeansGetLayoutClass extends Test_Case {

	/**
	 * Test post's ID.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		$this->post_id = self::factory()->post->create();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {

		// Reset the Beans' sidebars.
		beans_do_register_widget_areas();

		parent::tearDown();
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
		// Go to the post's web page, i.e. to set up is_singular().
		update_post_meta( $this->post_id, 'beans_layout', $layout_id );
		$this->go_to( get_permalink( $this->post_id ) );
		$this->assertTrue( is_singular() );

		foreach ( $test_parameters as $parameters ) {

			// Unregister the sidebar primary.
			if ( ! $parameters['sidebar_primary'] ) {
				unregister_sidebar( 'sidebar_primary' );
			}

			// Unregister the sidebar secondary.
			if ( ! $parameters['sidebar_secondary'] ) {
				unregister_sidebar( 'sidebar_secondary' );
			}

			foreach ( $parameters['expected'] as $id => $expected ) {
				$this->assertSame( $expected, beans_get_layout_class( $id ) );
			}

			// Reset the sidebars.
			beans_do_register_widget_areas();
		}
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "c".
	 */
	public function test_should_return_classes_when_layout_is_c() {
		$test_parameters = [
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			],
		];

		$this->run_the_tests( $test_parameters, 'c' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "c_sp".
	 */
	public function test_should_return_classes_when_layout_is_c_sp() {
		$test_parameters = [
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4',
					'sidebar_primary'   => 'uk-width-medium-1-4',
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4',
					'sidebar_primary'   => 'uk-width-medium-1-4',
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			],
		];

		$this->run_the_tests( $test_parameters, 'c_sp' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "sp_c".
	 */
	public function test_should_return_classes_when_layout_is_sp_c() {
		$test_parameters = [
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-3-4',
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-3-4',
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			],
		];

		$this->run_the_tests( $test_parameters, 'sp_c' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "c_ss".
	 */
	public function test_should_return_classes_when_layout_is_c_ss() {
		$test_parameters = [
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => 'uk-width-medium-1-4',
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			],
		];

		$this->run_the_tests( $test_parameters, 'c_ss' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "c_sp_ss".
	 */
	public function test_should_return_classes_when_layout_is_c_sp_ss() {
		$test_parameters = [
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4',
					'sidebar_primary'   => 'uk-width-medium-1-4',
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-2-4',
					'sidebar_primary'   => 'uk-width-medium-1-4',
					'sidebar_secondary' => 'uk-width-medium-1-4',
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			],
		];

		$this->run_the_tests( $test_parameters, 'c_sp_ss' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "ss_c".
	 */
	public function test_should_return_classes_when_layout_is_ss_c() {
		$test_parameters = [
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => 'uk-width-medium-1-4 uk-pull-3-4',
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			],
		];

		$this->run_the_tests( $test_parameters, 'ss_c' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "sp_ss_c".
	 */
	public function test_should_return_classes_when_layout_is_sp_ss_c() {
		$test_parameters = [
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-3-4',
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-2-4 uk-push-2-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-2-4',
					'sidebar_secondary' => 'uk-width-medium-1-4 uk-pull-2-4',
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			],
		];

		$this->run_the_tests( $test_parameters, 'sp_ss_c' );
	}

	/**
	 * Test beans_get_default_layout() should return classes when the layout is "sp_c_ss".
	 */
	public function test_should_return_classes_when_layout_is_sp_c_ss() {
		$test_parameters = [
			[
				'expected'          => [
					'content'           => 'uk-width-medium-4-4',
					'sidebar_primary'   => null,
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => false,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-3-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-3-4',
					'sidebar_secondary' => null,
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => false,
			],
			[
				'expected'          => [
					'content'           => 'uk-width-medium-2-4 uk-push-1-4',
					'sidebar_primary'   => 'uk-width-medium-1-4 uk-pull-2-4',
					'sidebar_secondary' => 'uk-width-medium-1-4',
				],
				'sidebar_primary'   => true,
				'sidebar_secondary' => true,
			],
		];

		$this->run_the_tests( $test_parameters, 'sp_c_ss' );
	}
}
