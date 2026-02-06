<?php
/** @var \MABEL_WOF_LITE\Core\Models\Choicepicker_Option $option */
if(!defined('ABSPATH')){die;}
$id = $option->name === null ? $option->id : $option->name;
?>
<div class="mabel-mc-wrapper" data-id="<?php echo esc_attr( $id ) ?>">

	<input
		type="hidden"
		name="<?php echo esc_attr( $id ) ?>"
		value="<?php echo esc_attr( $option->values_to_key_list() ) ?>"
		class="mabel-formm-element"
		<?php
        // Output is already properly escaped within the function.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $option->get_extra_data_attributes();
        ?>
	/>

	<div class="mabel-mc-chosen">
		<em class="infotext" style="<?php if(!empty($option->value)) echo 'display:none'; ?>">
			<?php esc_html_e("Choose from the items below", 'wp-optin-wheel' ); ?>
		</em>

	</div>

	<div class="mabel-mc-options">

		<?php
			foreach($option->possible_values as $title => $options){
				echo '<span class="mabel-mc-title">' . wp_kses_post( $title ) . '</span>';
				foreach($options as $key => $value) {
					echo '<span class="mabel-mc-option" data-id="' . esc_attr( $key ) . '">' . ( empty($value) ? 'n/a' : wp_kses_post( $value) ) . '</span>';
				}
			}
		?>
	</div>
</div>

<?php
if(isset($option->extra_info))
	echo '<div class="p-t-1 extra-info">' . wp_kses_post( $option->extra_info ) .'</div>';
?>