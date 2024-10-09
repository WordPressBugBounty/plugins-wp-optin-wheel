<div class="wof-nonce" data-nonce="<?php echo esc_attr(wp_create_nonce('wof_data_nonce')) ?>"></div>
<div class="wof-all-wheels-wrapper">
	<span class="wof-no-results" style="display:inline-block;padding-bottom: 20px;"><?php _e("You didn't create any wheels yet.", \MABEL_WOF_LITE\Core\Common\Managers\Config_Manager::$slug); ?></span>

	<div class="wof-wheels-list"></div>

	<div class="pro-option-teaser">
		<b>Need statistics?</b> Views & optin statistics are available in the premium version.
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
				<?php _e('Active', \MABEL_WOF_LITE\Core\Common\Managers\Config_Manager::$slug) ?> <input type="checkbox" name="active" {{! (value.active == 1) ? ' checked="checked" ' : '' }} class="skip-save wof-toggle-active" data-wheel="{{=value.id}}" />
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