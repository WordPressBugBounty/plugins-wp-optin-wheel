<?php
/** @var \MABEL_WOF_LITE\Core\Models\Pro_option $option */
?>

<div class="pro-option-teaser">
	<?php echo $option->value ? wp_kses_post( $option->value ) : esc_html( 'This option is available in the Pro version.', 'wp-optin-wheel' ); ?>
</div>
