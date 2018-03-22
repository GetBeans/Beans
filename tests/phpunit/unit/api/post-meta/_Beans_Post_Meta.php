<?php
/**
 * _Beans_Post_Meta.php
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\Test_Case;
use _Beans_Post_Meta;
use Brain\Monkey;

class Tests_Beans_Post_Meta extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/post-meta/class.php',
			'api/fields/functions.php',
			'api/utilities/functions.php',
		) );

		$this->setup_common_wp_stubs();
	}

	/**
	 * Test _Beans_Post_Meta::nonce() should output correct nonce html.
	 */
	public function test_nonce_should_echo_nonce_input_html() {
		Monkey\Functions\expect( 'wp_create_nonce' )->once()->with( 'beans_post_meta_nonce' )->andReturn( '123456' );
		$expected_html_output = '<input type="hidden" name="beans_post_meta_nonce" value="123456" />';

		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		ob_start();
		$post_meta->nonce();
		$actual_output = ob_get_clean();

		$this->assertEquals( $expected_html_output, $actual_output );
	}

	/**
	 * Test _Beans_Post_Meta::register_metabox() should register an appropriate metabox.
	 */
	public function test_register_metabox_should_register_metabox() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'add_meta_box' )
			->once()
			->with( 'tm-beans', 'Post Options', array( $post_meta, 'metabox_content' ), 'post', 'normal', 'high' )
			->andReturn( $wp_meta_boxes['post']['normal']['high']['1'] = array(
				'id'            => 1,
				'title'         => 'Post Options',
				'callback'      => array( $this, 'metabox_content' ),
				'callback_args' => null
			) );

		$post_meta->register_metabox( 'post' );

		$this->assertEquals(
			$wp_meta_boxes['post']['normal']['high'][1],
			array(
				'id'            => 1,
				'title'         => 'Post Options',
				'callback'      => array( $this, 'metabox_content' ),
				'callback_args' => null
			) );
	}

	/**
	 * Test _Beans_Post_Meta::metabox_content() should output post meta fields markup.
	 */
	public function test_metabox_content_should_return_fields_markup() {
		$field = array(
			'id'      => 'beans_layout',
			'label'   => 'Layout',
			'type'    => 'radio',
			'context' => 'tm-beams',
			'default' => 'default_fallback',
			'options' => 'options html from layout options callback',
		);

		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'beans_get_fields' )
			->once()
			->with( 'post_meta', 'tm-beans' )
			->andReturn( array( $field ) );
		Monkey\Functions\expect( 'beans_field' )->once()->with( $field )->andReturnUsing( function () {
			echo 'beans_field_html';
		} );

		ob_start();
		$post_meta->metabox_content( 74 );
		$output = ob_get_clean();

		$this->assertEquals( 'beans_field_html', $output );
	}

	/**
	 * Test _Beans_Post_Meta::save() is false when doing autosave.
	 */
	public function test_save_should_return_false_when_doing_autosave() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'beans_doing_autosave' )->once()->andReturn( true );
		$this->assertFalse( $post_meta->save( 256 ) );
	}

	/**
	 * Test _Beans_Post_Meta::save() returns post_id when ok_to_save() is false.
	 */
	public function test_save_should_return_post_id_when_not_ok_to_save() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'beans_doing_autosave' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( false );
		$this->assertEquals( 256, $post_meta->save( 256 ) );
	}

	/**
	 * Test _Beans_Post_Meta::save() runs update_post_meta() and return null when ok_to_save() is true.
	 */
	public function test_save_should_run_update_post_meta_and_return_null_when_ok_to_save() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$fields    = array( 'beans_post_test_field' => 'beans_test_post_field_value' );

		Monkey\Functions\expect( 'beans_doing_autosave' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_post' )->andReturn( $fields );
		Monkey\Functions\expect( 'update_post_meta' )
			->once()
			->with( 256, 'beans_post_test_field', 'beans_test_post_field_value' )
			->andReturn( true );;
		$this->assertnull( $post_meta->save( 256 ) );
	}

	/**
	 * Test _Beans_Post_Meta::save_attachment() doesn't update post meta when doing autosave.
	 */
	public function test_save_attachment_should_not_update_post_meta_when_doing_autosave() {
		$post_meta  = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$attachment = array( 'ID' => 543 );

		Monkey\Functions\expect( 'beans_doing_autosave' )->once()->andReturn( true );
		Monkey\Functions\expect( 'update_post_meta' )->never();
		$this->assertEquals( $attachment, $post_meta->save_attachment( $attachment ) );
	}

	/**
	 * Test _Beans_Post_Meta::save_attachment() runs update_post_meta() and returns attachment when ok_to_save() is true.
	 */
	public function test_save_attachment_should_run_update_post_meta_and_return_attachment_when_ok_to_save() {
		$post_meta  = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$attachment = array( 'ID' => 543 );
		$fields     = array( 'beans_post_test_field' => 'beans_test_post_field_value' );

		Monkey\Functions\expect( 'beans_doing_autosave' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_post' )->andReturn( $fields );
		Monkey\Functions\expect( 'update_post_meta' )
			->once()
			->with( 543, 'beans_post_test_field', 'beans_test_post_field_value' )
			->andReturn( true );;
		$this->assertEquals( $attachment, $post_meta->save_attachment( $attachment ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false with unverified nonce.
	 */
	public function test_ok_to_save_should_return_false_when_unverified_nonce() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( false );
		$this->assertFalse( $post_meta->ok_to_save( 456, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false with invalid user permissions.
	 */
	public function test_ok_to_save_should_return_false_when_user_cannot_edit() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )
			->once()
			->with( 'edit_post', 456 )
			->andReturn( false );
		$this->assertFalse( $post_meta->ok_to_save( 456, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false when post meta has no fields.
	 */
	public function test_ok_to_save_should_return_false_when_fields_empty() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		$this->assertFalse( $post_meta->ok_to_save( 456, array() ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is true when all conditions for saving are met.
	 */
	public function test_ok_to_save_should_return_true_when_all_conditions_met() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		$this->assertTrue( $post_meta->ok_to_save( 456, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}
}
