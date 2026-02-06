<?php
/** @var \MABEL_WOF_LITE\Core\Models\Checkbox_Option $option */
if(!defined('ABSPATH')){
	die;
}

?>
<input type="hidden" name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>" value="false" class="skip-dependency" />
<input
	class="mabel-form-element"
	type="checkbox"
	name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
	value="true"
	<?php if(in_array($option->value, ['true','1',true], true)) echo ' checked '; ?>
	id="ckb-<?php echo esc_attr( $option->id ) ?>"
	<?php echo !empty($option->dependency) ? 'data-dependency="' . esc_attr(json_encode($option->dependency)) . '"':''; ?>
	<?php 
    // Output is already properly escaped within the function.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $option->get_extra_data_attributes(); 
    ?>
/>

<label for="ckb-<?php echo esc_attr( $option->id ) ?>">
	<?php echo esc_html($option->label); ?>
</label>

<?php
if(isset($option->extra_info))
	echo '<div class="p-t-1 extra-info">' . wp_kses_post( $option->extra_info ) .'</div>';
?>
