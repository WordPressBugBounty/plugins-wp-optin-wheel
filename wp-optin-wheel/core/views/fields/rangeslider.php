<?php
/** @var \MABEL_WOF_LITE\Core\Models\Range_Option $option */
if(!defined('ABSPATH')){ die; }
?>

<input
	style="opacity: 0;"
	class="mabel-formm-element"
	name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
	type="range"
	min="<?php echo esc_attr( $option->min ) ?>"
	max="<?php echo esc_attr( $option->max ) ?>"
	step="<?php echo esc_attr( $option->step ) ?>"
	value="<?php echo esc_attr( $option->value ) ?>"
	<?php
    // Output is already properly escaped within the function.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $option->get_extra_data_attributes(); 
    ?>
/>

<?php
	if(isset($option->extra_info))
		echo '<div class="p-t-1 extra-info">' . esc_html( $option->extra_info ) .'</div>';
?>

