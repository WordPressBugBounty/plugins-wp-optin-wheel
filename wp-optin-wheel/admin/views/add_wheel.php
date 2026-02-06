<div class="step-tracker-wrapper" id="add-wheel"
 data-slices="<?php echo esc_attr( json_encode( $data['slices'] ) ) ?>">
	<ul class="step-tracker steps-5">
		<li class="step current">
			<span></span><h2><?php esc_html_e('Theme', 'wp-optin-wheel' ); ?></h2>
		</li>
		<li class="step">
			<span></span><h2><?php esc_html_e('Chances', 'wp-optin-wheel' ); ?></h2>
		</li>
		<li class="step">
			<span></span><h2><?php esc_html_e('Slices', 'wp-optin-wheel' ); ?></h2>
		</li>
		<li class="step">
			<span></span><h2><?php esc_html_e('Form builder', 'wp-optin-wheel' ); ?></h2>
		</li>
		<li class="step">
			<span></span><h2><?php esc_html_e('Settings', 'wp-optin-wheel' ); ?></h2>
		</li>
	</ul>
	<div class="step-tracker-content">
		<div data-step="1" class="t-c p-t-5">
			<div class="wof-warn error-mail-lists">
                <?php esc_html_e('Before you can create wheels, please enter your MailChimp API key in the "email integration" tab.', 'wp-optin-wheel' ); ?><br/>
                <?php esc_html_e('Don\'t want to use MailChimp? Consider upgrading to the Pro version.', 'wp-optin-wheel' ); ?>
			</div>
			<div class="wof-step-1" style="display: none;">
				<div class="wof-theme-wrapper">
					<?php
						foreach($data['themes'] as $id => $title) {
							?>
							<div class="wof-theme">
								<div><?php echo esc_html($title) ?></div>
								<label for="wof-theme-<?php echo esc_attr($id) ?>">
									<img src="<?php echo esc_attr($data['base_url'].'/admin/img/wheel-'.$id.'.png') ?>" />
								</label>
								<input class="skip-save" <?php echo $id === 'vintage'?'checked':''; ?> name="wof-wheel-theme" type="radio" value="<?php echo esc_attr($id) ?>" id="wof-theme-<?php echo esc_attr($id) ?>" />
							</div>
							<?php
						}
					?>
					<div class="pro-option-teaser">
						<div style="padding-bottom: 15px;text-align: center;"><?php echo wp_kses_post( __( '<b>Build your own theme in Pro </b> and give each slice any color your like. You can also pick from more pre-built themes (including <b>seasonal</b> themes like "Christmas").', 'wp-optin-wheel' ) ) ?></div>
						<div class="wof-theme">
							<img src="<?php echo esc_attr($data['base_url']).'/admin/img/wheel-black-and-white.png' ?>" />
						</div>
						<div class="wof-theme">
							<img src="<?php echo esc_attr($data['base_url']).'/admin/img/wheel-alt-blue.png' ?>" />
						</div>
						<div class="wof-theme">
							<img src="<?php echo esc_attr($data['base_url']).'/admin/img/wheel-blue.png' ?>" />
						</div>
					</div>
				</div>
				<div class="m-t-5 t-c">
					<button class="mabel-btn-next-step mabel-btn"><?php esc_html_e( 'Next', 'wp-optin-wheel' ); ?></button>
					<button class="btn-save-wheel mabel-btn btn-save-when-editing" style="display: none;"><?php esc_html_e( 'Save', 'wp-optin-wheel' ); ?></button>
				</div>
			</div>
		</div>

		<div data-step="2" class="skip-save p-t-5" style="display: none;">
			<table class="form-table">
				<?php
				foreach( $data['chance_settings'] as $o ) {
					echo '<tr>';
					if( ! empty( $o->title ) )
						echo '<th scope="row">' . esc_html( $o->title ) . '</th>';
					echo '<td>';
					\MABEL_WOF_LITE\Core\Common\Html::option( $o );
					echo '</td></tr>';
				}
				?>
			</table>

			<div class="p-t-5 t-c">
				<button class="mabel-btn-prev-step mabel-btn mabel-secondary"><?php esc_html_e('Back', 'wp-optin-wheel' ); ?></button>
				<button class="mabel-btn-next-step mabel-btn"><?php esc_html_e('Next', 'wp-optin-wheel' ); ?></button>
				<button class="btn-save-wheel mabel-btn btn-save-when-editing" style="display: none;"><?php esc_html_e( 'Save', 'wp-optin-wheel' ); ?></button>
			</div>
		</div>

		<div data-step="3" class="skip-save p-t-5" style="display: none;">
			<p><?php esc_html_e('A wheel has 12 slices. Below you can define each slice in detail.', 'wp-optin-wheel' ); ?></p>
            <div class="pro-option-teaser" style="margin-top:15px">
                <p><?php echo wp_kses_post( __( 'With our Pro version, you can <strong>freely define how many slices</strong> your wheel should have. You can also <strong>limit prizes per slice</strong>!', 'wp-optin-wheel' ) ); ?></p>
            </div>
			<table class="form-table wof-slice-wrapper m-t-5">
				<thead>
					<th style="width:45px;"></th>
					<th><?php esc_html_e('Type', 'wp-optin-wheel' ) ?></th>
					<th><?php esc_html_e('Text', 'wp-optin-wheel' ) ?></th>
					<th>
						<span class="wof-value-title"><?php esc_html_e('Value', 'wp-optin-wheel' ) ?></span>
						<span class="wof-wc-title" style="display: none;"><?php esc_html_e('Discount', 'wp-optin-wheel' ) ?></span>
					</th>
					<th style="width:135px;"><?php esc_html_e('Chance', 'wp-optin-wheel' ) ?></th>
					<th style="width:100px;">&nbsp;</th>
				</thead>
				<tbody></tbody>
			</table>
			<div class="wof-total">
                <?php esc_html_e('Chance total', 'wp-optin-wheel' ); ?>: <span class="wof-total-percentage"></span> %</th>
			</div>
			<p class="msg-bad msg-incorrect-percentage" style="display: none;">
				<?php esc_html_e("The total sum of chance should be 100. Please double check and adjust accordingly.", 'wp-optin-wheel' ) ?>
			</p>
			<div class="p-t-5 t-c">
				<button class="mabel-btn-prev-step mabel-btn mabel-secondary"><?php esc_html_e('Back', 'wp-optin-wheel' ); ?></button>
				<button class="mabel-btn mabel-btn-next-step "><?php esc_html_e('Next', 'wp-optin-wheel' ); ?></button>
				<button class="btn-save-wheel mabel-btn btn-save-when-editing" style="display: none;"><?php esc_html_e('Save', 'wp-optin-wheel' ); ?></button>
			</div>
		</div>

		<div data-step="4" class="skip-save p-t-5" style="display: none;">
			<div class="form-builder-for-lists">
				<div class="wof-info-bubble pro-option-teaser">
                    <?php echo wp_kses_post( __( 'Build your opt-in form here. This is what the user needs to fill out before playing or seeing their prize.<br/>The Pro version allows to add more <strong>fields and field types</strong>.', 'wp-optin-wheel' ) ); ?>
				</div>
				<?php
					\MABEL_WOF_LITE\Core\Common\Html::option($data['form_builder_for_lists']);
				?>
			</div>
			<div class="p-t-5 t-c">
				<button class="mabel-btn-prev-step mabel-btn mabel-secondary"><?php esc_html_e('Back', 'wp-optin-wheel' ); ?></button>
				<button class="mabel-btn-next-step mabel-btn"><?php esc_html_e('Next', 'wp-optin-wheel' ); ?></button>
				<button class="btn-save-wheel mabel-btn btn-save-when-editing" style="display: none;"><?php esc_html_e('Save', 'wp-optin-wheel' ); ?></button>
			</div>
		</div>

		<div data-step="5" class="skip-save p-t-5" style="display: none;">
			<table class="form-table">
				<?php
				foreach($data['settings'] as $o) {
					echo '<tr>';
					if(!empty($o->title))
						echo '<th scope="row">' . esc_html( $o->title ) . '</th>';
					echo '<td>';
					\MABEL_WOF_LITE\Core\Common\Html::option($o);
					echo '</td></tr>';
				}
				?>
			</table>

			<div class="p-t-5 t-c">
				<button class="mabel-btn-prev-step mabel-btn mabel-secondary"><?php esc_html_e('Back', 'wp-optin-wheel' ); ?></button>
				<button class="btn-save-wheel mabel-btn"><?php esc_html_e('Save', 'wp-optin-wheel' ); ?></button>
			</div>
		</div>

		<div class="t-c p-t-5" data-final-step style="display: none;">
				<b><?php esc_html_e( "All done! Your wheel of fortune is now live.", 'wp-optin-wheel' ); ?></b>
			<div class="p-t-5 t-c">
				<button class="btn-start-over mabel-btn"><?php esc_html_e('Add new wheel', 'wp-optin-wheel' ); ?></button>
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
				<option {{? value.type == 0}}selected="selected"{{?}} value="0"><?php esc_html_e('No Prize', 'wp-optin-wheel' ) ?></option>
				<option {{? value.type == 1}}selected="selected"{{?}} value="1"><?php esc_html_e('Coupon Code', 'wp-optin-wheel' ) ?></option>
				<option {{? value.type == 2}}selected="selected"{{?}} value="2"><?php esc_html_e('Link', 'wp-optin-wheel' ) ?></option>
				<option disabled><?php esc_html_e('WooCommerce - generated coupon (Pro)', 'wp-optin-wheel' ) ?></option>
				<option disabled><?php esc_html_e('WooCommerce - Free product (Pro)', 'wp-optin-wheel' ) ?></option>
				<option disabled><?php esc_html_e('WooCommerce - Free shipping (Pro)', 'wp-optin-wheel' ) ?></option>
				<option disabled><?php esc_html_e('Redirect to link (Pro)', 'wp-optin-wheel' ) ?></option>
				<option disabled><?php esc_html_e('Custom text or HTML (Pro)', 'wp-optin-wheel' ) ?></option>
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
			<a style="display: none;" class="btn-wc-coupon-settings" data-slice="{{=index+1}}" href="#"><?php esc_html_e('More settings', 'wp-optin-wheel' ); ?></a>
		</td>
	</tr>
	{{~}}
</script>
