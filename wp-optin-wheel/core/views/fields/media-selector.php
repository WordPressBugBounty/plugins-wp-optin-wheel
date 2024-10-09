<?php
/** @var \MABEL_WOF_LITE\Core\Models\MediaSelector_Option $option */
if(!defined('ABSPATH')){
	die;
}
$id = $option->name === null ? $option->id : $option->name;
?>
<div class="mabel-media-selector" data-for="<?php echo esc_attr($id); ?>">

	<div class="mabel-media-preview" style="<?php if(empty($option->value)) echo 'display:none;'; ?>">
		<img src="<?php echo esc_attr($option->value); ?>">
	</div>

	<a class="mabel-btn" href="#">
		<?php echo esc_html($option->button_text); ?>
	</a>

	<input
		type="hidden"
		name="<?php echo esc_attr($id); ?>"
		value="<?php echo esc_attr($option->value); ?>"
		class="mabel-formm-element"
	/>

	<?php
	if(isset($option->extra_info))
		echo '<div class="p-t-1 extra-info">' . esc_html($option->extra_info) .'</div>';
	?>
</div>
