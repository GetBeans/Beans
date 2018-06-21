<?php
/**
 * Tests for _beans_get_term_meta()
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use Beans\Framework\Tests\Integration\API\Term_Meta\Includes\Term_Meta_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-term-meta-test-case.php';

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansGetTermMeta extends Term_Meta_Test_Case {

	/**
	 * Test beans_get_term_meta() should return false when no default given and term meta does not exist.
	 */
	public function test_should_return_false_when_no_default_given_and_term_meta_does_not_exist() {
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
	public function test_should_return_default_when_default_given_and_term_meta_does_not_exist() {
		$default_term_id  = $this->factory()->category->create();
		$provided_term_id = $this->factory()->category->create();
		$this->go_to( ( '?cat=' . $default_term_id ) );

		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback', $provided_term_id ) );
	}

	/**
	 * Test beans_get_term_meta() should return term's meta value when it exists.
	 */
	public function test_should_return_term_meta_when_it_exists() {
		$default_term_id = $this->factory()->category->create();
		update_option( "beans_term_{$default_term_id}_beans_layout", 'sp-c' );
		$provided_term_id = $this->factory()->category->create();
		update_option( "beans_term_{$provided_term_id}_beans_layout", 'c-sp' );
		$this->go_to( ( '?cat=' . $default_term_id ) );

		$this->assertSame( 'sp-c', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
		$this->assertSame( 'c-sp', beans_get_term_meta( 'beans_layout', 'default_fallback', $provided_term_id ) );
	}

	/**
	 * Test beans_get_term_meta() should return default when given, tag_ID set but term meta does not exist.
	 */
	public function test_should_return_default_when_given_tag_id_set_but_term_meta_does_not_exist() {
		$_GET['tag_ID'] = 2;

		$this->assertSame( 'default_fallback', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
	}

	/**
	 * Test beans_get_term_meta() should return meta term's value when given, tag_ID is set and meta exists.
	 */
	public function test_should_return_term_meta_when_given_tag_id_set_and_term_meta_exist() {
		$_GET['tag_ID'] = 3;
		update_option( 'beans_term_3_beans_layout', 'sp-c' );

		$this->assertSame( 'sp-c', beans_get_term_meta( 'beans_layout', 'default_fallback' ) );
	}
}
