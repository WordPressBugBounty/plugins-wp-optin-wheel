<div class="wof-nonce" data-nonce="<?php echo esc_attr(wp_create_nonce('wof_data_nonce')) ?>"></div>
<div class="wof-all-wheels-wrapper">
	<span class="wof-no-results" style="display:inline-block;padding-bottom: 20px;"><?php esc_html_e("You didn't create any wheels yet.", 'wp-optin-wheel' ); ?></span>

	<div class="wof-wheels-list"></div>

	<div class="pro-option-teaser">
        <?php echo wp_kses_post( __( '<b>Need statistics?</b> Views & optin statistics are available in the Pro version.', 'wp-optin-wheel' ) ) ?>
	</div>
</div>


<script id="tpl-wof-wheels-list" type="text/x-dot-template">
	{{~ it.wheels :value}}
	<div data-id="{{=value.id}}" class="image-tile">
		<div class="tile-header" style="background-image: url('<?php echo esc_attr($data['base_url']) ?>/admin/img/wheel-{{=value.theme}}.png')">
			<span class="tag-id">
				{{? value.name}}
					{{=value.name}} ({{=value.id}})
				{{??}}
					{{=value.id}}
				{{?}}
			</span>
		</div>
		<div class="tile-footer">
			<div>
				<?php esc_html_e( 'Active', 'wp-optin-wheel' ) ?> <input type="checkbox" name="active" {{! (value.active == 1) ? ' checked="checked" ' : '' }} class="skip-save wof-toggle-active" data-wheel="{{=value.id}}" />
			</div>
			<ul>
				<li>
					<a href="#" title="Edit wheel" class="wof-edit-wheel" data-wheel="{{=value.id}}"><i class="dashicons dashicons-edit"></i></a>
				</li>
				<li>
					<a href="#" title="Delete wheel" class="wof-delete-wheel" data-wheel="{{=value.id}}"><i class="dashicons dashicons-trash"></i></a>
				</li>
				<li>
					<a href="#" class="wof-duplicate-wheel" title="Duplicate wheel" data-wheel="{{=value.id}}"><i class="dashicons dashicons-images-alt2"></i></a>
				</li>
			</ul>
		</div>
	</div>
	{{~}}
</script>