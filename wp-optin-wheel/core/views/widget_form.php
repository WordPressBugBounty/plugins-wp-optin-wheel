<?php

/** @var \MABEL_WOF_LITE\Core\Common\Managers\Widget_Options_Manager $option_manager */

?>

<div class="widget-options">
	<?php
		foreach($option_manager->options as $option)
		{
			/** @var \MABEL_WOF_LITE\Core\Models\Option $option */
			echo '<p>';
				echo '<label>' . wp_kses_post( $option->title ) . '</label>';
				echo '<div>';
					$option_manager->display_field( ['option' => $option] );
				echo '</div>';
			echo '</p>';
		}
	?>
</div>
