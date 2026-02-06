<?php
	use \MABEL_WOF_LITE\Core\Common\Html;
	/** @var \MABEL_WOF_LITE\Core\Models\Container_Option $option */
?>
<div
	class="mabel-accordion mabel-form-element"
	name="<?php echo esc_attr( $option->name === null ? $option->id : $option->name ) ?>"
	<?php echo !empty($option->dependency) ? 'data-dependency="' . esc_attr(json_encode($option->dependency)) . '"':''; ?>
>
	<button class="mabel-accordion-btn"><?php echo esc_html( $option->button_text ) ?></button>
	<div style="display: none;">
		<table class="form-table">
			<?php
				foreach($option->options as $o)
				{
					echo '<tr>';
					if(!empty($o->title))
						echo '<th scope="row">' . wp_kses_post( $o->title ) . '</th>';
					echo '<td '.(empty($o->title) ? 'colspan="2"' : '').'>';
						Html::option( $o );
					echo '</td>';
				}
			?>
		</table>
	</div>
</div>