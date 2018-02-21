<?php
/**
 * Renders the image field type.
 *
 * @package Beans\Framework\API\Fields\Types
 */

beans_add_smart_action( 'beans_field_enqueue_scripts_image', 'beans_field_image_assets' );
/**
 * Enqueued assets required by the beans image field.
 *
 * @since 1.0.0
 */
function beans_field_image_assets() {
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'beans-field-media', BEANS_API_URL . 'fields/assets/js/media' . BEANS_MIN_CSS . '.js', array( 'jquery' ), BEANS_VERSION );
}

beans_add_smart_action( 'beans_field_image', 'beans_field_image' );
/**
 * Echo image field type.
 *
 * @since 1.0.0
 *
 * @param array $field      {
 *      For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 * @type mixed  $value      The image's or images' ID.
 * @type string $name       The field name value.
 * @type array  $attributes An array of attributes to add to the field. The array key defines the attribute name and
 *       the array value defines the attribute value. Default is array.
 * @type mixed  $default    The default value. Default is false.
 * @type string $is_multiple   Set to true to enable multiple images (gallery). Default is false.
 * }
 */
function beans_field_image( array $field ) {
	// Set the images variable and add placeholder to the array.
	$images = array_merge( (array) $field['value'], array( 'placeholder' ) );
	$is_multiple = beans_get( 'multiple', $field );
	$link_text   = _n( 'Add Image', 'Add Images', ( $is_multiple ? 2 : 1 ), 'tm-beans' );

	// If this is a single image and it already exists, then hide the "add image" hyperlink.
	$hide_add_link = ! $is_multiple && is_numeric( $field['value'] ) ? 'style="display: none"' : '';

	?>
	<a href="#" class="bs-add-image button button-small" <?php echo $hide_add_link; ?>><?php echo esc_html( $link_text ); ?></a>
	<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="">
	<div class="bs-images-wrap" data-multiple="<?php echo esc_attr( $is_multiple ); ?>">
		<?php foreach ( $images as $id ) :

			// Stop here if the id is false.
			if ( ! $id ) {
				continue;
			}

			$class = '';
			$img = wp_get_attachment_image_src( $id, 'thumbnail' );

			$attributes = array_merge( array(
				'class' => 'image-id',
				'type'  => 'hidden',
				'name'  => $is_multiple ? $field['name'] . '[]' : $field['name'], // Return single value if not multiple.
				'value' => $id,
			), $field['attributes'] );

			// Set placeholder.
			if ( 'placeholder' == $id ) {

				$class = 'bs-image-template';
				$attributes = array_merge( $attributes, array( 'disabled' => 'disabled', 'value' => false ) );

			}

			?>
			<div class="bs-image-wrap <?php echo esc_attr( $class ); ?>">
				<input <?php echo beans_esc_attributes( $attributes ); ?> />
				<img src="<?php echo esc_url( beans_get( 0, $img ) ); ?>">
				<div class="bs-toolbar">
					<?php if ( $is_multiple ) : ?>
						<a href="#" class="dashicons dashicons-menu"></a>
					<?php endif; ?>
					<a href="#" class="dashicons dashicons-edit"></a>
					<a href="#" class="dashicons dashicons-post-trash"></a>
				</div>
			</div>

		<?php endforeach; ?>
	</div>
	<?php
}
