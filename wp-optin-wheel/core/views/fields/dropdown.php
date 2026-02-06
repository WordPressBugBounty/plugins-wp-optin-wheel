<?php
/** @var \MABEL_WOF_LITE\Core\Models\Dropdown_Option $option */
if(!defined('ABSPATH')){
	die;
}

$has_pre_text = false;

if(isset($option->pre_text)) {
	echo '<span>' . esc_html( $option->pre_text ) . '</span>';
	$has_pre_text = true;
}
?>

<select
	class="widefat mabel-form-element"
	<?php echo $has_pre_text ? 'style="padding:0 10px;width:auto;"' : ''; ?>
	name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
	<?php echo ! empty($option->dependency) ? 'data-dependency="' . esc_attr(json_encode($option->dependency)) . '"':''; ?>
	<?php
    // Output is already properly escaped within the function.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $option->get_extra_data_attributes();
    ?>
>
	<?php
		foreach($option->options as $key=>$value){
			$selected = $key == $option->value;
			echo '<option ' . ($selected?'selected':'') .' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
		}
	?>
</select>

<?php
if(isset($option->post_text))
	echo '<span>' . esc_html( $option->post_text ) . '</span>';
?>

<?php

if(isset($option->extra_info))
	echo '<div class="p-t-1 extra-info">' . esc_html( $option->extra_info ) . '</div>';
