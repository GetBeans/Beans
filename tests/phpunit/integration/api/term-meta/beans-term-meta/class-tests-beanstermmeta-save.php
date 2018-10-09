<?php
/**
 * Tests for the save() method of Beans_Term_Meta.
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Term_Meta;

use Beans\Framework\Tests\Integration\API\Term_Meta\Includes\Term_Meta_Test_Case;
use Beans_Term_Meta;

require_once BEANS_THEME_DIR . '/lib/api/term-meta/class-beans-term-meta.php';
require_once BEANS_THEME_DIR . '/lib/api/term-meta/functions-admin.php';
require_once dirname( __DIR__ ) . '/includes/class-term-meta-test-case.php';

/**
 * Class Tests_BeanTermMeta_Save
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansTermMeta_Save extends Term_Meta_Test_Case {

	/**
	 * Tests Beans_Term_Meta::save() should return the term_id when nonce is invalid.
	 */
	public function test_should_return_term_id_when_nonce_is_invalid() {
		$term_meta = new Beans_Term_Meta( 'tm-beans' );

		$this->assertEquals( 753, $term_meta->save( 753 ) );
	}

	/**
	 * Tests Beans_Term_Meta::save() should return the term_id when fields are falsey.
	 */
	public function test_should_return_term_id_when_fields_are_falsey() {
		// Setup a valid nonce but no fields.
		$_POST['beans_term_meta_nonce'] = wp_create_nonce( 'beans_term_meta_nonce' );

		$term_meta = new Beans_Term_Meta( 'tm-beans' );

		$this->assertEquals( 1234, $term_meta->save( 1234 ) );
	}

	/**
	 * Tests Beans_Term_Meta::save() should save term meta in the database.
	 */
	public function test_should_save_term_meta_in_db() {
		// Setup a valid nonce and fields.
		$_POST['beans_term_meta_nonce'] = wp_create_nonce( 'beans_term_meta_nonce' );
		$_POST['beans_fields']          = static::$test_data['fields'];

		$term_meta = new Beans_Term_Meta( 'tm-beans' );

		$this->assertNull( $term_meta->save( 1234 ) );
		$this->assertSame( static::$test_data['fields'][0], get_option( 'beans_term_1234_0' ) );
	}

}
