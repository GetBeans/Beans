<?php
/**
 * View file for the post meta template reload script.
 *
 * @package Beans\Framework\API\Post_Meta
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

?>

<script type="text/javascript">
	(function ($) {
		$(document).ready(function () {
			$('#page_template').data('beans-pre', $('#page_template').val());
			$('#page_template').change(function () {
				var save = $('#save-action #save-post'),
					meta = JSON.parse('<?php echo wp_json_encode( $_beans_post_meta_conditions ); ?>');

				if (-1 === $.inArray($(this).val(), meta) && -1 === $.inArray($(this).data('beans-pre'), meta)) {
					return;
				}

				if (save.length === 0) {
					save = $('#publishing-action #publish');
				}

				$(this).data('beans-pre', $(this).val());
				save.trigger('click');
				$('#wpbody-content').fadeOut();
			});
		});
	})(jQuery);
</script>
