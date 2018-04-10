<?php
/**
 * Tests for beans_register_post_meta()
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use Beans\Framework\Tests\Integration\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;

require_once BEANS_API_PATH . 'post-meta/functions-admin.php';
require_once dirname( __FILE__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansRegisterPostMeta extends Beans_Post_Meta_Test_Case {

	/**
	 * Test beans_register_post_meta returns false when on front end.
	 */
	public function test_returns_false_when_not_is_admin() {

		$this->assertFalse( beans_register_post_meta( array(
			array(
				'id'    => 'field_id',
				'type'  => 'radio',
				'label' => 'Field Label',
			),
		), true, 'tm-beans' ) );
	}

	/**
	 * Test beans_register_post_meta returns false when conditions are false.
	 */
	public function test_returns_false_when_conditions_are_false() {
		set_current_screen( 'edit' );

		$this->assertFalse( beans_register_post_meta( array(
			array(
				'id'    => 'field_id',
				'type'  => 'radio',
				'label' => 'Field Label',
			),
		), false, 'tm-beans' ) );
	}

	/**
	 * Test beans_register_post_meta returns false when fields cannot be registered.
	 */
	public function test_returns_false_when_fields_are_unregisterable() {
		set_current_screen( 'edit' );

		$this->assertFalse( beans_register_post_meta( array(), true, 'tm-beans' ) );
	}

	/**
	 * Test beans_register_post_meta returns true when post meta is registered.
	 */
	public function test_returns_false_when_post_meta_is_registered() {
		set_current_screen( 'edit' );

		$this->assertTrue( beans_register_post_meta( array(
			array(
				'id'    => 'field_id',
				'type'  => 'radio',
				'label' => 'Field Label',
			),
		), true, 'tm-beans' ) );
	}
}
