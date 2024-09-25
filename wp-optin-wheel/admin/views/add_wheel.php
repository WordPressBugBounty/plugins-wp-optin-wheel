<div class="step-tracker-wrapper" id="add-wheel"
 data-slices="<?php  echo htmlspecialchars(json_encode($data['slices']), ENT_QUOTES, 'UTF-8'); ?>">
	<ul class="step-tracker steps-5">
		<li class="step current">
			<span></span><h2><?php _e('Theme', $slug); ?></h2>
		</li>
		<li class="step">
			<span></span><h2><?php _e('Chances', $slug); ?></h2>
		</li>
		<li class="step">
			<span></span><h2><?php _e('Slices', $slug); ?></h2>
		</li>
		<li class="step">
			<span></span><h2><?php _e('Form builder', $slug); ?></h2>
		</li>
		<li class="step">
			<span></span><h2><?php _e('Settings', $slug); ?></h2>
		</li>
	</ul>
	<div class="step-tracker-content">
		<div data-step="1" class="t-c p-t-5">
			<div class="wof-error error-mail-lists">
				Before you can create wheels, please enter your MailChimp API key in the "email integration" tab.
			</div>
			<div class="wof-step-1" style="display: none;">
				<div class="wof-theme-wrapper">
					<?php
						foreach($data['themes'] as $id => $title) {
							?>
							<div class="wof-theme">
								<div><?php _e($title) ?></div>
								<label for="wof-theme-<?php _e($id) ?>">
									<img src="<?php _e($data['base_url'].'/admin/img/wheel-'.$id.'.png') ?>" />
								</label>
								<input class="skip-save" <?php echo $id === 'vintage'?'checked':''; ?> name="wof-wheel-theme" type="radio" value="<?php _e($id) ?>" id="wof-theme-<?php _e($id) ?>" />
							</div>
							<?php
						}
					?>
					<div class="pro-option-teaser">
						<div style="padding-bottom: 15px;text-align: center;"><b>Build your own unlimited themes</b> or enjoy pre-built themes (including <b>seasonal</b> themes like "Christmas") in premium.</div>
						<div class="wof-theme">
							<img src="<?php _e($data['base_url'].'/admin/img/wheel-black-and-white.png') ?>" />
						</div>
						<div class="wof-theme">
							<img src="<?php _e($data['base_url'].'/admin/img/wheel-alt-blue.png') ?>" />
						</div>
						<div class="wof-theme">
							<img src="<?php _e($data['base_url'].'/admin/img/wheel-blue.png') ?>" />
						</div>
					</div>
				</div>
				<div class="m-t-5 t-c">
					<button class="mabel-btn-next-step mabel-btn">Next</button>
					<button class="btn-save-wheel mabel-btn btn-save-when-editing" style="display: none;">Save</button>
				</div>
			</div>
		</div>

		<div data-step="2" class="skip-save p-t-5" style="display: none;">
			<table class="form-table">
				<?php
				foreach($data['chance_settings'] as $o) {
					echo '<tr>';
					if(!empty($o->title))
						echo '<th scope="row">'.$o->title.'</th>';
					echo '<td>';
					\MABEL_WOF_LITE\Core\Common\Html::option($o);
					echo '</td></tr>';
				}
				?>
			</table>

			<div class="p-t-5 t-c">
				<button class="mabel-btn-prev-step mabel-btn mabel-secondary">Back</button>
				<button class="mabel-btn-next-step mabel-btn">Next</button>
				<button class="btn-save-wheel mabel-btn btn-save-when-editing" style="display: none;">Save</button>
			</div>
		</div>

		<div data-step="3" class="skip-save p-t-5" style="display: none;">
			<p>A wheel has 12 slices. Below you can define each slice in detail.</p>
			<table class="form-table wof-slice-wrapper m-t-5">
				<thead>
					<th style="width:45px;"></th>
					<th><?php _e('Type', $slug) ?></th>
					<th><?php _e('Text', $slug) ?></th>
					<th>
						<span class="wof-value-title"><?php _e('Value', $slug) ?></span>
						<span class="wof-wc-title" style="display: none;"><?php _e('Discount', $slug) ?></span>
					</th>
					<th style="width:135px;"><?php _e('Chance', $slug) ?></th>
					<th style="width:100px;">&nbsp;</th>
				</thead>
				<tbody></tbody>
			</table>
			<div class="wof-total">
				Chance total: <span class="wof-total-percentage"></span> %</th>
			</div>
			<p class="msg-bad msg-incorrect-percentage" style="display: none;">
				<?php _e("The total sum of chance should be 100. Please double check and adjust accordingly.") ?>
			</p>
			<div class="p-t-5 t-c">
				<button class="mabel-btn-prev-step mabel-btn mabel-secondary">Back</button>
				<button class="mabel-btn mabel-btn-next-step ">Next</button>
				<button class="btn-save-wheel mabel-btn btn-save-when-editing" style="display: none;">Save</button>
			</div>
		</div>

		<div data-step="4" class="skip-save p-t-5" style="display: none;">
			<div class="form-builder-for-lists">
				<div class="wof-info-bubble">
					Build your opt-in form here. This is what the user needs to fill out before playing or seeing their prize.
					The premium version allows to add more fields.
				</div>
				<?php
					\MABEL_WOF_LITE\Core\Common\Html::option($data['form_builder_for_lists']);
				?>
			</div>
			<div class="p-t-5 t-c">
				<button class="mabel-btn-prev-step mabel-btn mabel-secondary">Back</button>
				<button class="mabel-btn-next-step mabel-btn">Next</button>
				<button class="btn-save-wheel mabel-btn btn-save-when-editing" style="display: none;">Save</button>
			</div>
		</div>

		<div data-step="5" class="skip-save p-t-5" style="display: none;">
			<table class="form-table">
				<?php
				foreach($data['settings'] as $o) {
					echo '<tr>';
					if(!empty($o->title))
						echo '<th scope="row">'.$o->title.'</th>';
					echo '<td>';
					\MABEL_WOF_LITE\Core\Common\Html::option($o);
					echo '</td></tr>';
				}
				?>
			</table>

			<div class="p-t-5 t-c">
				<button class="mabel-btn-prev-step mabel-btn mabel-secondary">Back</button>
				<button class="btn-save-wheel mabel-btn">Save</button>
			</div>
		</div>

		<div class="t-c p-t-5" data-final-step style="display: none;">
				<b><?php _e("All done! Your wheel of fortune is now live.", $slug); ?></b>
			<div class="p-t-5 t-c">
				<button class="btn-start-over mabel-btn"><?php _e('Add new wheel', $slug); ?></button>
			</div>
		</div>
	</div>
</div>


<script id="tpl-woc-slice-tr" type="text/x-dot-template">
	{{~ it.slices :value:index}}
	<tr data-idx="{{=index}}">
		<td style="text-align: center;">{{=index+1}}</td>
		<td>
			<select name="wof-slice-type">
				<option {{? value.type == 0}}selected="selected"{{?}} value="0"><?php _e('No Prize', $slug) ?></option>
				<option {{? value.type == 1}}selected="selected"{{?}} value="1"><?php _e('Coupon Code', $slug) ?></option>
				<option {{? value.type == 2}}selected="selected"{{?}} value="2"><?php _e('Link', $slug) ?></option>
				<option disabled><?php _e('WooCommerce - generated coupon (Pro)', $slug) ?></option>
				<option disabled><?php _e('WooCommerce - Free product (Pro)', $slug) ?></option>
				<option disabled><?php _e('WooCommerce - Free shipping (Pro)', $slug) ?></option>
				<option disabled><?php _e('Redirect to link (Pro)', $slug) ?></option>
				<option disabled><?php _e('Custom text or HTML (Pro)', $slug) ?></option>
			</select>
		</td>
		<td>
			<input type="text" value="{{? value.label }}{{!value.label}}{{?}}" name="wof-slice-label" />
		</td>
		<td>
			<span class="td-content">
				<input type="text" value="{{? value.value }}{{!value.value}}{{?}}" name="wof-slice-value" />
			</span>
		</td>
		<td>
			<span class="td-content">
				<input style="width:70px;" type="number" min="0" max="100" value="{{? value.chance }}{{=value.chance}}{{??}}0{{?}}" name="wof-slice-chance" /> %
			</span>
		</td>
		<td>
			<a style="display: none;" class="btn-wc-coupon-settings" data-slice="{{=index+1}}" href="#">More settings</a>
		</td>
	</tr>
	{{~}}
</script>
