<?php
/**
 * Tests for beans_register_term_meta()
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Term_Meta;

use Beans\Framework\Tests\Integration\API\Term_Meta\Includes\Beans_Term_Meta_Test_Case;
use WP_UnitTestCase;

require_once BEANS_API_PATH . 'term-meta/functions-admin.php';
require_once dirname( __FILE__ ) . '/includes/class-beans-term-meta-test-case.php';

/**
 * Class Tests_BeansRegisterTermMeta
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansRegisterTermMeta extends Beans_Term_Meta_Test_Case {

	/**
	 * Test beans_register_term_meta() should return false when current taxonomy is not concerned.
	 */
	public function test_should_return_false_when_current_taxonomy_not_concerned() {
		$this->assertFalse( beans_register_term_meta( static::$test_data['fields'], [ 'sample-taxonomy' ], 'tm-beans' ) );
	}

	/**
	 * Test beans_register_term_meta() should return false when not is_admin().
	 */
	public function test_should_return_false_when_not_is_admin() {
		$_POST['taxonomy'] = 'sample-taxonomy';

		$this->assertFalse(
			beans_register_term_meta(
				static::$test_data['fields'],
				'sample-taxonomy',
				'tm-beans'
			)
		);
	}

	/**
	 * Test beans_register_term_meta() should return false when term meta fields cannot be registered.
	 */
	public function test_should_return_false_when_term_meta_fields_cannot_be_registered() {
		$_POST['taxonomy'] = 'sample-taxonomy';
		set_current_screen( 'edit' );

		$this->assertFalse( beans_register_term_meta( [], 'sample-taxonomy', 'tm-beans' ) );
	}

	/**
	 * Test beans_register_term_meta() should return true when term meta fields are successfully registered.
	 */
	public function test_should_return_true_when_term_meta_fields_are_successfully_registered() {
		$_POST['taxonomy'] = 'sample-taxonomy';
		set_current_screen( 'edit' );

		$this->assertTrue(
			beans_register_term_meta(
				static::$test_data['fields'],
				'sample-taxonomy',
				'tm-beans'
			)
		);
	}
}
