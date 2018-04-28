<?php
/** Tests for beans_register_post_meta()
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;
use Brain\Monkey;

require_once dirname( __FILE__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_BeansRegisterPostMeta
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansRegisterPostMeta extends Beans_Post_Meta_Test_Case {

	/**
	 * Test beans_register_post_meta() should return false when no fields.
	 */
	public function test_should_return_false_when_no_fields() {
		$this->assertFalse( beans_register_post_meta( array(), true, 'tm-beans' ) );
	}

	/**
	 * Test beans_register_post_meta() should return false when conditions are false.
	 */
	public function test_should_return_false_when_conditions_are_false() {
		Monkey\Functions\when( '_beans_pre_standardize_fields' )->returnArg();
		Monkey\Functions\expect( '_beans_is_post_meta_conditions' )->once()->andReturn( false );

		$this->assertFalse( beans_register_post_meta( array(
			array(
				'id'    => 'field_id',
				'type'  => 'radio',
				'label' => 'Field Label',
			),
		), false, 'tm-beans' ) );
	}

	/**
	 * Test beans_register_post_meta should return false when not on the admin side.
	 */
	public function test_should_return_false_when_not_is_admin() {
		Monkey\Functions\when( '_beans_pre_standardize_fields' )->returnArg();
		Monkey\Functions\expect( '_beans_is_post_meta_conditions' )->once()->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( false );

		$this->assertFalse( beans_register_post_meta( array(
			array(
				'id'    => 'field_id',
				'type'  => 'radio',
				'label' => 'Field Label',
			),
		), true, 'tm-beans' ) );
	}

	/**
	 * Test beans_register_post_meta() should return false when fields cannot be registered.
	 */
	public function test_should_return_false_when_fields_cannot_be_registered() {
		Monkey\Functions\when( '_beans_pre_standardize_fields' )->returnArg();
		Monkey\Functions\expect( '_beans_is_post_meta_conditions' )->once()->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_register_fields' )
			->once()
			->with( array( 'unregisterable' ), 'post_meta', 'tm-beans' )
			->andReturn( false );

		$this->assertFalse( beans_register_post_meta( array( 'unregisterable' ), true, 'tm-beans' ) );
	}

	/**
	 * Test beans_register_post_meta() should return true when fields are successfully registered.
	 */
	public function test_should_return_true_when_fields_successfully_registered() {
		Monkey\Functions\when( '_beans_pre_standardize_fields' )->returnArg();
		Monkey\Functions\expect( '_beans_is_post_meta_conditions' )->once()->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_register_fields' )
			->once()
			->with( array( 'field_id', 'radio', 'Field Label' ), 'post_meta', 'tm-beans' )
			->andReturn( true );

		$this->assertTrue( beans_register_post_meta( array( 'field_id', 'radio', 'Field Label' ), true, 'tm-beans' ) );
	}
}
