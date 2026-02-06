<?php
/** @var \MABEL_WOF_LITE\Core\Models\Text_Option $option */
use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;
if(!defined('ABSPATH')){
	die;
}

?>

<?php if(!$option->is_textarea) { ?>

	<input
		class="widefat mabel-form-element"
		type="text"
		name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
		value="<?php if( ! empty( $option->value ) ) echo esc_attr($option->value) ?>"
		placeholder="<?php echo esc_attr( $option->placeholder ) ?>"
	    <?php echo !empty($option->dependency) ? 'data-dependency="' . esc_attr(json_encode($option->dependency)) . '"':''; ?>
		<?php
        // Output is already properly escaped within the function.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $option->get_extra_data_attributes(); 
        ?>
	/>
<?php }else { ?>
	<textarea
		class="widefat mabel-form-element"
		name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
		placeholder="<?php echo esc_attr( $option->placeholder ) ?>"
		<?php echo ! empty($option->dependency) ? 'data-dependency="' . esc_attr(json_encode($option->dependency)) . '"':''; ?>
		<?php
        // Output is already properly escaped within the function.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $option->get_extra_data_attributes();
        ?>
	><?php if( ! empty( $option->value ) ) echo esc_attr($option->value);?></textarea>
<?php
}
	$option->display_help();
	if(isset($option->extra_info))
		echo '<div class="p-t-1 extra-info">' . esc_html($option->extra_info) .'</div>';
?>
