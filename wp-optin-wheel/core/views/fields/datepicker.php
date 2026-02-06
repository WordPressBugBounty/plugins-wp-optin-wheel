<?php
/** @var \MABEL_WOF_LITE\Core\Models\Datepicker_Option $option */
?>

<input
	type="text"
	name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
	value="<?php if( ! empty( $option->value ) ) echo esc_attr($option->value);?>"
	<?php echo !empty($option->dependency) ? 'data-dependency="' . esc_attr(json_encode($option->dependency)) . '"':''; ?>
	class="widefat mabel-date-picker mabel-form-element"
	<?php
    // Output is already properly escaped within the function.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $option->get_extra_data_attributes(); 
    ?>
	data-options="<?php echo esc_attr( json_encode($option->options ) ) ?>"
/>

<?php
if(isset($option->extra_info))
	echo '<div class="p-t-1 extra-info">' . esc_html( $option->extra_info ) .'</div>';
?>
