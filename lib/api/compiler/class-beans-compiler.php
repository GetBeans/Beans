<?php
/**
 * This class compiles and minifies CSS, LESS and JS.
 *
 * @package Beans\Framework\API\Compiler
 *
 * @since   1.5.0
 */

/**
 * Compiles and minifies CSS, LESS and JS.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Compiler
 */
final class _Beans_Compiler {

	/**
	 * Compiler's runtime configuration parameters.
	 *
	 * @var array
	 */
	private $config;

	/**
	 * Cache dir.
	 *
	 * @var string
	 */
	private $dir;

	/**
	 * Cache url.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * The fragment currently being processed.
	 *
	 * @var string
	 */
	private $current_fragment;

	/**
	 * The compiled content.
	 *
	 * @var string
	 */
	private $compiled_content;

	/**
	 * Compiled content's filename.
	 *
	 * @var string
	 */
	private $filename;

	/**
	 * Create a new Compiler.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Moved config initializer & compile tasks out of constructor.
	 *
	 * @param array $config Runtime configuration parameters for the Compiler.
	 */
	public function __construct( array $config ) {
		$this->config = $this->init_config( $config );
		$this->dir    = beans_get_compiler_dir( is_admin() ) . $this->config['id'];
		$this->url    = beans_get_compiler_url( is_admin() ) . $this->config['id'];
	}

	/**
	 * Run the compiler.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function run_compiler() {
		// Modify the WP Filesystem method.
		add_filter( 'filesystem_method', array( $this, 'modify_filesystem_method' ) );

		$this->set_fragments();
		$this->set_filename();

		if ( ! $this->cache_file_exist() ) {
			$this->filesystem();
			$this->maybe_make_dir();
			$this->combine_fragments();
			$this->cache_file();
		}

		$this->enqueue_file();

		// Keep it safe and reset the WP Filesystem method.
		remove_filter( 'filesystem_method', array( $this, 'modify_filesystem_method' ) );
	}

	/**
	 * Callback to set the WP Filesystem method.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function modify_filesystem_method() {
		return 'direct';
	}

	/**
	 * Initialise the WP Filesystem.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|void
	 */
	public function filesystem() {

		// If the WP_Filesystem is not already loaded, load it.
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
		}

		// If the WP_Filesystem is not initialized or is not set to WP_Filesystem_Direct, then initialize it.
		if ( $this->is_wp_filesystem_direct() ) {
			return true;
		}

		// Initialize the filesystem.
		$response = WP_Filesystem();

		// If the filesystem did not initialize, then generate a report and exit.
		if ( true !== $response || ! $this->is_wp_filesystem_direct() ) {
			return $this->kill();
		}

		return true;
	}

	/**
	 * Check if the filesystem is set to "direct".
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	private function is_wp_filesystem_direct() {
		return isset( $GLOBALS['wp_filesystem'] ) && is_a( $GLOBALS['wp_filesystem'], 'WP_Filesystem_Direct' );
	}

	/**
	 * Make directory.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Changed access to private.
	 *
	 * @return bool
	 */
	private function maybe_make_dir() {

		if ( ! @is_dir( $this->dir ) ) {  // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- This is a valid use case.
			wp_mkdir_p( $this->dir );
		}

		return is_writable( $this->dir );
	}

	/**
	 * Set class fragments.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function set_fragments() {
		global $_beans_compiler_added_fragments;

		$added_fragments = beans_get( $this->config['id'], $_beans_compiler_added_fragments[ $this->config['format'] ] );

		if ( $added_fragments ) {
			$this->config['fragments'] = array_merge( $this->config['fragments'], $added_fragments );
		}

		/**
		 * Filter the compiler fragment files.
		 *
		 * The dynamic portion of the hook name, $this->config['id'], refers to the compiler id used as a reference.
		 *
		 * @since 1.0.0
		 *
		 * @param array $fragments An array of fragment files.
		 */
		$this->config['fragments'] = apply_filters( 'beans_compiler_fragments_' . $this->config['id'], $this->config['fragments'] );
	}

	/**
	 * Set the filename for the compiled asset.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Renamed method. Changed storage location to $filename property.
	 *
	 * @return void
	 */
	public function set_filename() {
		$hash                = $this->hash( $this->config );
		$fragments_filemtime = $this->get_fragments_filemtime();
		$hash                = $this->get_new_hash( $hash, $fragments_filemtime );
		$this->filename      = $hash . '.' . $this->get_extension();
	}

	/**
	 * Hash the given array.
	 *
	 * @since 1.5.0
	 *
	 * @param array $given_array Given array to be hashed.
	 *
	 * @return string
	 */
	public function hash( array $given_array ) {
		return substr( md5( @serialize( $given_array ) ), 0, 7 ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- Valid use case.
	}

	/**
	 * Checks if the file exists on the filesystem, meaning it's been cached.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function cache_file_exist() {
		$filename = $this->get_filename();

		if ( empty( $filename ) ) {
			return false;
		}

		return file_exists( $filename );
	}

	/**
	 * Get the absolute path of the cached and compiled file.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_filename() {
		if ( isset( $this->filename ) ) {
			return $this->dir . '/' . $this->filename;
		}

		return '';
	}

	/**
	 * Create cached file.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function cache_file() {
		$filename = $this->get_filename();

		if ( empty( $filename ) ) {
			return false;
		}

		// It is safe to access the filesystem because we made sure it was set.
		return $GLOBALS['wp_filesystem']->put_contents( $filename, $this->compiled_content, FS_CHMOD_FILE );
	}

	/**
	 * Enqueue cached file.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Changed access to private.
	 *
	 * @return void|bool
	 */
	private function enqueue_file() {

		// Enqueue CSS file.
		if ( 'style' === $this->config['type'] ) {
			return wp_enqueue_style(
				$this->config['id'],
				$this->get_url(),
				$this->config['dependencies'],
				$this->config['version']
			);
		}

		// Enqueue JS file.
		if ( 'script' === $this->config['type'] ) {
			return wp_enqueue_script(
				$this->config['id'],
				$this->get_url(),
				$this->config['dependencies'],
				$this->config['version'],
				$this->config['in_footer']
			);
		}

		return false;
	}

	/**
	 * Get cached file url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_url() {
		$url = trailingslashit( $this->url ) . $this->filename;

		if ( is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		}

		return $url;
	}

	/**
	 * Get the file extension from the configured "type".
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_extension() {

		if ( 'style' === $this->config['type'] ) {
			return 'css';
		}

		if ( 'script' === $this->config['type'] ) {
			return 'js';
		}
	}

	/**
	 * Combine content of the fragments.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function combine_fragments() {
		$content = '';

		// Loop through fragments.
		foreach ( $this->config['fragments'] as $fragment ) {

			// Stop here if the fragment is empty.
			if ( empty( $fragment ) ) {
				continue;
			}

			$fragment_content = $this->get_content( $fragment );

			// Stop here if no content or content is an html page.
			if ( ! $fragment_content || preg_match( '#^\s*\<#', $fragment_content ) ) {
				continue;
			}

			// Continue processing style.
			if ( 'style' === $this->config['type'] ) {
				$fragment_content = $this->replace_css_url( $fragment_content );
				$fragment_content = $this->add_content_media_query( $fragment_content );
			}

			// If there's content, start a new line.
			if ( $content ) {
				$content .= "\n\n";
			}

			$content .= $fragment_content;
		}

		$this->compiled_content = ! empty( $content ) ? $this->format_content( $content ) : '';
	}

	/**
	 * Get the fragment's content.
	 *
	 * @since 1.5.0
	 *
	 * @param string|callable $fragment The given fragment from which to get the content.
	 *
	 * @return bool|string
	 */
	private function get_content( $fragment ) {
		// Set the current fragment used by other functions.
		$this->current_fragment = $fragment;

		// If the fragment is callable, call it to get the content.
		if ( $this->is_function( $fragment ) ) {
			return $this->get_function_content();
		}

		$content = $this->get_internal_content();

		// Try remote content if the internal content returned false.
		if ( empty( $content ) ) {
			$content = $this->get_remote_content();
		}

		return $content;
	}

	/**
	 * Get internal file content.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool
	 */
	public function get_internal_content() {
		$fragment = $this->current_fragment;

		if ( ! file_exists( $fragment ) ) {

			// Replace URL with path.
			$fragment = beans_url_to_path( $fragment );

			// Stop here if it isn't a valid file.
			if ( ! file_exists( $fragment ) || 0 === @filesize( $fragment ) ) { // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged  -- Valid use case.
				return false;
			}
		}

		// It is safe to access the filesystem because we made sure it was set.
		return $GLOBALS['wp_filesystem']->get_contents( $fragment );
	}

	/**
	 * Get external file content.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool
	 */
	public function get_remote_content() {
		$fragment = $this->current_fragment;

		if ( empty( $fragment ) ) {
			return false;
		}

		// For a relative URL, add http: to it.
		if ( substr( $fragment, 0, 2 ) === '//' ) {
			$fragment = 'http:' . $fragment;
		} elseif ( substr( $fragment, 0, 1 ) === '/' ) { // Add domain if it is local but could not be fetched as a file.
			$fragment = site_url( $fragment );
		}

		$request = wp_remote_get( $fragment );

		if ( is_wp_error( $request ) ) {
			return '';
		}

		// If no content was received and the URL is not https, then convert the URL to SSL and retry.
		if (
			( ! isset( $request['body'] ) || 200 !== $request['response']['code'] ) &&
			( substr( $fragment, 0, 8 ) !== 'https://' )
		) {
			$fragment = str_replace( 'http://', 'https://', $fragment );
			$request  = wp_remote_get( $fragment );

			if ( is_wp_error( $request ) ) {
				return '';
			}
		}

		if ( ( ! isset( $request['body'] ) || 200 !== $request['response']['code'] ) ) {
			return false;
		}

		return wp_remote_retrieve_body( $request );
	}

	/**
	 * Get function content.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool
	 */
	public function get_function_content() {

		if ( ! is_callable( $this->current_fragment ) ) {
			return false;
		}

		return call_user_func( $this->current_fragment );
	}

	/**
	 * Wrap content in query.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Given content to process.
	 *
	 * @return string
	 */
	public function add_content_media_query( $content ) {

		// Ignore if the fragment is a function.
		if ( $this->is_function( $this->current_fragment ) ) {
			return $content;
		}

		$query = parse_url( $this->current_fragment, PHP_URL_QUERY );

		// Bail out if there are no query args or no media query.
		if ( empty( $query ) || false === stripos( $query, 'beans_compiler_media_query' ) ) {
			return $content;
		}

		// Wrap the content in the query.
		return sprintf(
			"@media %s {\n%s\n}\n",
			beans_get( 'beans_compiler_media_query', wp_parse_args( $query ) ),
			$content
		);
	}

	/**
	 * Formal CSS, LESS and JS content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Given content to process.
	 *
	 * @return string
	 */
	public function format_content( $content ) {

		if ( 'style' === $this->config['type'] ) {

			if ( 'less' === $this->config['format'] ) {

				if ( ! class_exists( 'Beans_Lessc' ) ) {
					require_once BEANS_API_PATH . 'compiler/vendors/lessc.php';
				}

				$less    = new Beans_Lessc();
				$content = $less->compile( $content );
			}

			if ( ! _beans_is_compiler_dev_mode() ) {
				return $this->strip_whitespace( $content );
			}

			return $content;
		}

		if ( 'script' === $this->config['type'] && ! _beans_is_compiler_dev_mode() && $this->config['minify_js'] ) {

			if ( ! class_exists( 'JSMin' ) ) {
				require_once BEANS_API_PATH . 'compiler/vendors/js-minifier.php';
			}

			$js_min = new JSMin( $content );

			return $js_min->min();
		}

		return $content;
	}

	/**
	 * Replace CSS URL shortcuts with a valid URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Given content to process.
	 *
	 * @return string
	 */
	public function replace_css_url( $content ) {
		return preg_replace_callback(
			'#url\s*\(\s*[\'"]*?([^\'"\)]+)[\'"]*\s*\)#i',
			array( $this, 'replace_css_url_callback' ),
			$content
		);
	}

	/**
	 * Convert any CSS URL relative paths to absolute URLs.
	 *
	 * @since 1.0.0
	 *
	 * @param array $matches Matches to process, where 0 is the CSS' URL() and 1 is the URI.
	 *
	 * @return string
	 */
	public function replace_css_url_callback( $matches ) {

		// If the URI is absolute, bail out and return the CSS.
		if ( _beans_is_uri( $matches[1] ) ) {
			return $matches[0];
		}

		$base = $this->current_fragment;

		// Separate the placeholders and path.
		$paths = explode( '../', $matches[1] );

		/**
		 * Walk backwards through each of the the fragment's directories, one-by-one. The `foreach` loop
		 * provides us with a performant way to walk the fragment back to its base path based upon the
		 * number of placeholders.
		 */
		foreach ( $paths as $path ) {
			$base = dirname( $base );
		}

		// Make sure it is a valid base.
		if ( '.' === $base ) {
			$base = '';
		}

		// Rebuild the URL and make sure it is valid using the beans_path_to_url function.
		$url = beans_path_to_url( trailingslashit( $base ) . ltrim( end( $paths ), '/\\' ) );

		// Return the rebuilt path converted to an URL.
		return 'url("' . $url . '")';
	}

	/**
	 * Initialize the configuration.
	 *
	 * @since 1.5.0
	 *
	 * @param array $config Runtime configuration parameters for the Compiler.
	 *
	 * @return array
	 */
	private function init_config( array $config ) {
		// Fix dependencies, if "depedencies" is specified.
		if ( isset( $config['depedencies'] ) ) {
			$config['dependencies'] = $config['depedencies'];
			unset( $config['depedencies'] );
		}

		$defaults = array(
			'id'           => false,
			'type'         => false,
			'format'       => false,
			'fragments'    => array(),
			'dependencies' => false,
			'in_footer'    => false,
			'minify_js'    => false,
			'version'      => false,
		);

		return array_merge( $defaults, $config );
	}

	/**
	 * Get the fragments' modification times.
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	private function get_fragments_filemtime() {
		$fragments_filemtime = array();

		foreach ( $this->config['fragments'] as $index => $fragment ) {

			// Skip this one if the fragment is a function.
			if ( $this->is_function( $fragment ) ) {
				continue;
			}

			if ( file_exists( $fragment ) ) {
				$fragments_filemtime[ $index ] = @filemtime( $fragment ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- Valid use case.
			}
		}

		return $fragments_filemtime;
	}

	/**
	 * Get the new hash for the given fragments' modification times.
	 *
	 * @since 1.5.0
	 *
	 * @param string $hash                The original hash to modify.
	 * @param array  $fragments_filemtime Array of fragments' modification times.
	 *
	 * @return string
	 */
	private function get_new_hash( $hash, array $fragments_filemtime ) {

		if ( empty( $fragments_filemtime ) ) {
			return $hash;
		}

		// Set filemtime hash.
		$_hash = $this->hash( $fragments_filemtime );

		$this->remove_modified_files( $hash, $_hash );

		// Set the new hash which will trigger a new compiling.
		return $hash . '-' . $_hash;
	}

	/**
	 * Remove any modified files.  A file is considered modified when:
	 *
	 * 1. It has both a base hash and filemtime hash, separated by '-'.
	 * 2. Its base hash matches the given hash.
	 * 3. Its filemtime hash does not match the given filemtime hash.
	 *
	 * @since 1.5.0
	 *
	 * @param string $hash           Base hash.
	 * @param string $filemtime_hash The filemtime hash (from hashing the fragments).
	 *
	 * @return void
	 */
	private function remove_modified_files( $hash, $filemtime_hash ) {
		$items = beans_scandir( $this->dir );

		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {

			// Skip this one if it's a directory.
			if ( @is_dir( $item ) ) { // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- Valid use case.
				continue;
			}

			// Skip this one if it's not the same type.
			if ( pathinfo( $item, PATHINFO_EXTENSION ) !== $this->get_extension() ) {
				continue;
			}

			// Skip this one if it does not have a '-' in the filename.
			if ( strpos( $item, '-' ) === false ) {
				continue;
			}

			$hash_parts = explode( '-', pathinfo( $item, PATHINFO_FILENAME ) );

			// Skip this one if it does not match the given base hash.
			if ( $hash_parts[0] !== $hash ) {
				continue;
			}

			// Skip this one if it does match the given filemtime's hash.
			if ( $hash_parts[1] === $filemtime_hash ) {
				continue;
			}

			// Clean up other modified files.
			@unlink( $this->dir . '/' . $item );  // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- Valid use case.
		}
	}

	/**
	 * Minify the CSS.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Changed access to private.
	 *
	 * @param string $content Given content to process.
	 *
	 * @return string
	 */
	private function strip_whitespace( $content ) {
		$replace = array(
			'#/\*.*?\*/#s' => '', // Strip comments.
			'#\s\s+#'      => ' ', // Strip excess whitespace.
		);

		$search  = array_keys( $replace );
		$content = preg_replace( $search, $replace, $content );

		// Strip all new lines and tabs.
		$content = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $content );

		$replace = array(
			': '   => ':',
			'; '   => ';',
			' {'   => '{',
			' }'   => '}',
			', '   => ',',
			'{ '   => '{',
			';}'   => '}', // Strip optional semicolons.
			',\n'  => ',', // Don't wrap multiple selectors.
			'\n}'  => '}', // Don't wrap closing braces.
			'}'    => "}\n", // Put each rule on it's own line.
			'\n'   => '', // Remove all line breaks.
			"}\n " => "}\n", // Remove the whitespace at start of each new line.
		);

		$search = array_keys( $replace );

		return trim( str_replace( $search, $replace, $content ) );
	}

	/**
	 * Check if the given fragment is a callable.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Changed access to private.
	 *
	 * @param mixed $fragment Given fragment to check.
	 *
	 * @return bool
	 */
	private function is_function( $fragment ) {
		return ( is_array( $fragment ) || is_callable( $fragment ) );
	}

	/**
	 * Kill it :(
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Changed access to private.
	 *
	 * @return void
	 */
	private function kill() {

		// Send report if set.
		if ( beans_get( 'beans_send_compiler_report' ) ) {
			$this->report();
		}

		$html = beans_output( 'beans_compiler_error_title_text', sprintf(
			'<h2>%s</h2>',
			__( 'Not cool, Beans cannot work its magic :(', 'tm-beans' )
		) );

		$html .= beans_output( 'beans_compiler_error_message_text', sprintf(
			'<p>%s</p>',
			__( 'Your current install or file permission prevents Beans from working its magic. Please get in touch with Beans support. We will gladly get you started within 24 - 48 hours (working days).', 'tm-beans' )
		) );

		$html .= beans_output( 'beans_compiler_error_contact_text', sprintf(
			'<a class="button" href="https://www.getbeans.io/contact/?compiler_report=1" target="_blanc">%s</a>',
			__( 'Contact Beans Support', 'tm-beans' )
		) );

		$html .= beans_output( 'beans_compiler_error_report_text', sprintf(
			'<p style="margin-top: 12px; font-size: 12px;"><a href="' . add_query_arg( 'beans_send_compiler_report', true ) . '">%1$s</a>. %2$s</p>',
			__( 'Send us an automatic report', 'tm-beans' ),
			__( 'We respect your time and understand you might not be able to contact us.', 'tm-beans' )
		) );

		wp_die( wp_kses_post( $html ) );
	}

	/**
	 * Send report.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Changed access to private.
	 *
	 * @return void
	 */
	private function report() {
		// Send report.
		wp_mail(
			'hello@getbeans.io',
			'Compiler error',
			'Compiler error reported by ' . home_url(),
			array(
				'MIME-Version: 1.0' . "\r\n",
				'Content-type: text/html; charset=utf-8' . "\r\n",
				"X-Mailer: PHP \r\n",
				'From: ' . wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . ' < ' . get_option( 'admin_email' ) . '>' . "\r\n",
				'Reply-To: ' . get_option( 'admin_email' ) . "\r\n",
			)
		);

		// Die and display message.
		$message = beans_output(
			'beans_compiler_report_error_text',
			sprintf(
				'<p>%s<p>',
				__( 'Thanks for your contribution by reporting this issue. We hope to hear from you again.', 'tm-beans' )
			)
		);

		wp_die( wp_kses_post( $message ) );
	}

	/**
	 * Set the filename for the compiled asset.
	 *
	 * This method has been replaced with {@see set_filename()}.
	 *
	 * @since      1.0.0
	 * @deprecated 1.5.0.
	 */
	public function set_filname() {
		_deprecated_function( __METHOD__, '1.5.0', 'set_filename' );

		$this->set_filename();
	}

	/**
	 * Get the property's value.
	 *
	 * @since 1.5.0
	 *
	 * @param string $property Name of the property to get.
	 *
	 * @return mixed
	 */
	public function __get( $property ) {

		if ( property_exists( $this, $property ) ) {
			return $this->{$property};
		}
	}
}
