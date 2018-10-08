<?php
/**
 * Tests for the __construct() method of Beans_Term_Meta.
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Term_Meta;

use Beans\Framework\Tests\Integration\API\Term_Meta\Includes\Term_Meta_Test_Case;
use Beans_Term_Meta;

require_once BEANS_THEME_DIR . '/lib/api/term-meta/class-beans-term-meta.php';
require_once dirname( __DIR__ ) . '/includes/class-term-meta-test-case.php';

/**
 * Class Tests_BeansTermMeta_Construct
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansTermMeta_Construct extends Term_Meta_Test_Case {

	/**
	 * Test __construct() should set the correct hooks when the class is instantiated.
	 */
	public function test_should_set_correct_hooks_when_class_is_instantiated() {
		$_GET['taxonomy'] = 'sample-taxonomy';

		// First instantiation sets all hooks.
		$term_meta1 = new Beans_Term_Meta( 'tm-beans' );

		$this->assertEquals( 10, has_action( 'sample-taxonomy_edit_form', [ $term_meta1, 'render_nonce' ] ) );
		$this->assertEquals( 10, has_action( 'edit_term', [ $term_meta1, 'save' ] ) );
		$this->assertEquals( 10, has_action( 'delete_term', [ $term_meta1, 'delete' ] ) );
		$this->assertEquals( 10, has_action( 'sample-taxonomy_edit_form_fields', [ $term_meta1, 'render_fields' ] ) );

		// Subsequent instantiation sets only {$taxonomy}_edit_form_fields hook.
		$term_meta2 = new Beans_Term_Meta( 'tm-beans-child' );
		$this->assertFalse( has_action( 'sample-taxonomy_edit_form', [ $term_meta2, 'render_nonce' ] ) );
		$this->assertFalse( has_action( 'edit_term', [ $term_meta2, 'save' ] ) );
		$this->assertFalse( has_action( 'delete_term', [ $term_meta2, 'delete' ] ) );
		$this->assertEquals( 10, has_action( 'sample-taxonomy_edit_form_fields', [ $term_meta2, 'render_fields' ] ) );
	}
}
