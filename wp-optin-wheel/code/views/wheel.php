<?php
	/** @var \MABEL_WOF_LITE\Code\Models\Wheel_Model $model */
	use MABEL_WOF_LITE\Core\Common\Managers\Config_Manager;
	use MABEL_WOF_LITE\Code\Services\Wheel_service;

?>

<style>
	<?php if($model->bgpattern != 'none'){ ?>
		.wof-wheel[data-id="<?php echo $model->id ?>"]:after{<?php echo $model->get_background() ?>}
	<?php } ?>
</style>

<div
	style="transform:translateX(-110%)"
	data-seq="<?php echo esc_attr(Wheel_service::get_sequence($model)) ?>"
	class="<?php echo esc_attr($model->classes()) ?>"
	<?php echo $model->data_attributes() ?>
>
	<div class="wof-wrapper">
		<div class="wof-close-wrapper">
			<a class="wof-close wof-fgcolor" href="#">
				<?php echo $model->has_setting('close_text') ? $model->close_text : __('Close', Config_Manager::$slug) ?>
			</a>
		</div>
		<div class="wof-inner-wrapper">

			<div class="wof-left">
				<div class="wof-left-inner">
					<div class="wof-pointer">
						<svg width="100%" height="100%" viewBox="0 0 273 147"><g><path <?php echo $model->has_setting('pointer_color') ? 'fill="'.$model->pointer_color.'"' : 'class="wof-pointer-color"'; ?> d="M196.3 0h10.5l1 .25c10.06 1.9 19.63 5.06 28.1 10.93 11.28 7.55 19.66 18.43 25.12 30.78 1.9 6.4 4.06 12.23 4 19.04-.1 5.3.3 10.7-.34 15.97-2.18 14.1-9.08 27.46-19.38 37.33-10.03 10-23.32 16.4-37.33 18.4-4.95.54-10 .3-14.97.3-6.4-.02-13.06-2.82-19.2-4.68-54.98-17.5-109.95-35.08-164.96-52.5C4.7 74.7 2.14 73.33 0 69.5v-6.26c1.47-1.93 2.94-3.95 5.34-4.77C64.47 39.78 123.84 20.77 183 2c4.3-1.15 8.9-1.2 13.3-2z"/><path opacity=".25" d="M261.02 41.96c6.74 9.2 10.54 20.04 11.98 31.3V88c-1.9 14.78-8.25 28.63-18.78 39.24-11 11.34-25.83 18.16-41.52 19.78h-12.65c-3.8-.6-7.57-1.4-11.22-2.63C132.4 126.43 76 108.37 19.55 90.5c-3.4-1.22-8.1-1.62-10.12-4.94-2.2-3.14-1.5-6.3-.6-9.73 55.02 17.4 110 35 164.97 52.5 6.14 1.85 12.8 4.65 19.2 4.66 4.97 0 10.02.24 14.97-.3 14-2 27.3-8.4 37.33-18.4 10.3-9.87 17.2-23.24 19.38-37.33.63-5.27.23-10.66.34-15.97.06-6.8-2.1-12.64-4-19.04v.01z"/><ellipse stroke="null" ry="25" rx="25" cy="65" cx="199.124" stroke-opacity="null" stroke-width="null" fill="#fff"/></g></svg>
					</div>
					<div class="shadow-inner"></div>
					<div class="shadow-outer"></div>
					<div class="shadow-small"></div>
					<div class="wof-wheel-container wof-spinning">
						<div class="wof-wheel-bg" style="transform: rotate(15deg)"></div>
						<?php $start_degrees = 0; $ctr= 1; ?>
						<?php foreach($model->slices as $slice) { ?>
							<div class="wof-slice" data-slice="<?php echo esc_attr($ctr) ?>" style="transform:rotate(<?php echo esc_attr($start_degrees) ?>deg) translate(0px, -50%)"><?php echo wp_strip_all_tags($slice->label); ?></div>
							<?php $start_degrees += 30; $ctr++; ?>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="wof-right">
				<div class="wof-right-inner">
					<div class="wof-title wof-fgcolor">
						<?php echo wp_kses_post($model->title) ?>
					</div>
					<?php if($model->has_setting('explainer')){ ?>
						<div class="wof-explainer wof-fgcolor">
							<?php echo wp_kses_post($model->explainer) ?>
						</div>
					<?php } ?>
					<div class="wof-form-wrapper">
						<div class="wof-error" style="display: none;"></div>
						<input type="email" data-wof-required="email" class="wof-email" name="wof-email" placeholder="<?php echo esc_attr($model->setting_or_default('email_placeholder', __('Your email',Config_Manager::$slug))) ?>" />
						<div class="wof-form-fields"></div>
						<button	class="wof-btn-submit wof-color-2" type="submit">
							<span><?php echo esc_html($model->setting_or_default('button_text',__('Try your luck',Config_Manager::$slug))) ?></span>
							<div class="wof-loader" style="display: none;">
								<div class="b1"></div>
								<div class="b2"></div>
								<div></div>
							</div>
						</button>
						<div class="wof-response"></div>
					</div>
					<?php if($model->has_setting('disclaimer')) { ?>
						<div class="wof-disclaimer wof-fgcolor">
							<?php echo wp_kses_post($model->disclaimer) ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>