<?php
/** @var \MABEL_WOF_LITE\Core\Models\Text_Option $option */
if(!defined('ABSPATH')){
	die;
}

$has_pre_text = false;

if(isset($option->pre_text)) {
	echo '<span>' . esc_html($option->pre_text) . '</span>';
	$has_pre_text = true;
}
?>

<input
	class="<?php echo $has_pre_text ? '' : 'widefat'; ?> mabel-form-element"
	style="<?php echo $has_pre_text? 'width:100px;' : ''; ?>"
	type="number"
	name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
	value="<?php if( ! empty( $option->value ) ) echo esc_attr($option->value) ?>"
	<?php echo !empty($option->dependency) ? 'data-dependency="' . esc_attr(json_encode($option->dependency)) . '"':''; ?>
	<?php
    // Output is already properly escaped within the function.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $option->get_extra_data_attributes(); 
    ?>
/>
<?php

if(isset($option->post_text))
	echo '<span>' . esc_html($option->post_text) . '</span>';

if(isset($option->extra_info))
	echo '<div class="p-t-1 extra-info">' . esc_html($option->extra_info) .'</div>';

?>
