<?php

if ( ! defined( 'ABSPATH' ) ) {
	$bean_wp_tests_dir = getenv( 'WP_TESTS_DIR' );
	if ( ! $bean_wp_tests_dir ) {
		$bean_wp_tests_dir = '/tmp/wordpress-tests-lib/';
	}

	$beans_wp_config_file_path = dirname( dirname( $bean_wp_tests_dir ) );
	if ( ! file_exists( $beans_wp_config_file_path . '/wp-tests-config.php' ) ) {
		// Support the config file from the root of the develop repository.
		if ( basename( $beans_wp_config_file_path ) === 'phpunit' && basename( dirname( $beans_wp_config_file_path ) ) === 'tests' ) {
			$beans_wp_config_file_path = dirname( dirname( $beans_wp_config_file_path ) );
		}
	}
	$beans_wp_config_file_path .= '/wp-tests-config.php';
	require_once $beans_wp_config_file_path;

	unset( $bean_wp_tests_dir, $beans_wp_config_file_path );
}

if ( ! function_exists( 'wp_normalize_path' ) ) {
	function wp_normalize_path( $path ) {
		$path = str_replace( '\\', '/', $path );
		$path = preg_replace( '|(?<=.)/+|', '/', $path );
		if ( ':' === substr( $path, 1, 1 ) ) {
			$path = ucfirst( $path );
		}

		return $path;
	}
}
