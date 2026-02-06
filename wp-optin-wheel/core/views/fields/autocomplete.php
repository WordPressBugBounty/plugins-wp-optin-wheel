<?php
	/** @var \MABEL_WOF_LITE\Core\Models\Autocomplete_Option $option */
?>
<div class="mabel-autocomplete-wrapper <?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>" data-action="<?php echo esc_attr( $option->ajax_action ) ?>">
	<input type="hidden"
	       name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
	       value="<?php echo esc_attr( $option->value ) ?>"
		<?php
        // Output is already properly escaped within the function.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $option->get_extra_data_attributes(); 
        ?>
	/>
	<div>
	<input
		style="background:white;border:none;width:50px;margin:0;padding:0;"
		type="text"
		placeholder="Search..."
		class="mabel-formm-element"
	/>
	</div>
</div>

<?php
if(isset($option->extra_info))
	echo '<div class="p-t-1 extra-info">' . wp_kses_post( $option->extra_info ) .'</div>';
?>
