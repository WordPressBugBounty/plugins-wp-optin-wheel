<?php
/** @var \MABEL_WOF_LITE\Core\Models\ColorPicker_Option $option */
?>

<input
	type="text"
	name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
	value="<?php if( ! empty( $option->value ) ) echo esc_attr( $option->value ) ?>"
	<?php echo ! empty( $option->dependency ) ? 'data-dependency="' . esc_attr(json_encode($option->dependency)) . '"':''; ?>
	class="color-picker mabel-form-element"
/>

<?php
	if(isset($option->extra_info))
		echo '<div class="p-t-1 extra-info">' . wp_kses_post( $option->extra_info ) .'</div>';
?>