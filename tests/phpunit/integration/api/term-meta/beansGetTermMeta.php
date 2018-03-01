<?php
/**
 * Tests for beans_get_term_meta()
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use WP_UnitTestCase;

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 * @group   integration-tests
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansGetTermMeta extends WP_UnitTestCase {

	/**
	 * Test beans_get_term_meta() should return false when default not given and term meta does not exist.
	 */
	public function test_should_return_false_when_no_optional_arguments_given_and_term_meta_not_set() {
		$this->assertFalse( beans_get_term_meta( 'beans_layout' ) );

		$_GET['tag_ID'] = 1; // a tag_ID is set.
		$this->assertFalse( beans_get_term_meta( 'beans_layout' ) );

		$term_id = $this->factory()->category->create();
		$this->go_to( ( '?cat=' . $term_id ) ); // a term_id is set.

		$this->assertFalse( beans_get_term_meta( 'beans_layout' ) );
	}

	/**
	 * Test beans_get_term_meta() should return default when given and term meta does not exist.
	 */
	public function test_should_return_default_when_default_given_and_term_meta_not_set() {
		$default_term_id  = $this->factory()->category->create();
		$provided_term_id = $this->factory()->category->create();
		$this->go_to( ( '?cat=' . $default_term_id ) );

		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback', $provided_term_id ) );
	}

	/**
	 * Test beans_get_term_meta() should return term's meta value when it exists.
	 */
	public function test_should_return_term_meta_when_meta_is_set() {
		$default_term_id = $this->factory()->category->create();
		update_option( "beans_term_{$default_term_id}_beans_layout", 'sp-c' );
		$provided_term_id = $this->factory()->category->create();
		update_option( "beans_term_{$provided_term_id}_beans_layout", 'c-sp' );
		$this->go_to( ( '?cat=' . $default_term_id ) );

		$this->assertSame( 'sp-c', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
		$this->assertSame( 'c-sp', beans_get_term_meta( 'beans_layout', 'default_fallback', $provided_term_id ) );
	}

	/**
	 * Test beans_get_term_meta() should return default when given and tag_ID set but term meta does not exist.
	 */
	public function test_should_return_default_when_given_and_tag_id_exists_but_term_meta_not_set() {
		$_GET['tag_ID'] = 2;

		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
	}

	/**
	 * Test beans_get_term_meta() should return meta term's value when given and tag_ID set and meta exists.
	 */
	public function test_should_return_term_meta_when_given_and_tag_id_exists_but_term_meta_not_set() {
		$_GET['tag_ID'] = 3;
		update_option( 'beans_term_3_beans_layout', 'sp-c' );

		$this->assertSame( 'sp-c', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
	}
}
