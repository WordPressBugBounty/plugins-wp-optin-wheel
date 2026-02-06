<?php
/** @var \MABEL_WOF_LITE\Code\Models\Wheels_VM $model */
?>

<div class="wof-overlay" style="display: none;"></div>

<div class="wof-wheels" data-wof-nonce="<?php echo esc_attr( wp_create_nonce('wof-nonce') ) ?>">
	<?php
		foreach($model->wheels as $wheel) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo \MABEL_WOF_LITE\Core\Common\Html::view('wheel', $wheel);
		}
	?>
</div>
<div class="wof-mobile-check"></div>
<div class="wof-tablet-check"></div>