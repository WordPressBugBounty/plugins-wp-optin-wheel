<div id="wof-custom-field-modal" style="display: none;">
	<table class="form-table wof-custom-field-table">
		<tr>
			<th>Type</th>
			<td>
				<select name="wof-form-builder-type">
					<option value="consent_checkbox">Consent Checkbox</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Label</th>
			<td>
				<input type="text" name="wof-form-builder-placeholder" placeholder="Your label (can contain some HTML)" />
			</td>
		</tr>
		<tr class="formbuilder-add-field-required-row">
			<th>Required</th>
			<td>
				<input type="checkbox" name="wof-form-builder-required" id="wof-add-custom-field-cb"/> <label for="wof-add-custom-field-cb">This field is required</label>
			</td>
		</tr>
	</table>
	<div class="mabel-modal-button-row">
		<a href="#" class="btn-wof-add-custom-field mabel-btn">Add</a>
	</div>
</div>

<table class="form-table wof-styled-table wof-field-builder-table">
	<thead>
	<tr>
		<th colspan="10" style="text-align: right;">
			<a title="Add a custom field to the opt-in form" href="#TB_inline?width=500&height=350&inlineId=wof-custom-field-modal" class="thickbox">
				Add new field
			</a>
		</th>
	</tr>
	<tr>
		<th>Include</th>
		<th>Name</th>
		<th>Label</th>
		<th>Required</th>
	</tr>
	<tr class="form-field-tr" data-field-type="primary_email" data-field-id="primary_email">
		<td><input checked="checked" disabled="" type="checkbox"></td>
		<td><label>Email</label></td>
		<td><input class="widefat mabel-form-element" type="text" name="email_placeholder" placeholder="Your email" data-key="email_placeholder"></td>
		<td><input checked="checked" disabled="" type="checkbox"></td>
	</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<script id="tpl-wof-field-builder-lists-row" type="text/x-dot-template">
	{{~ it.fields :field:index}}
	<tr class="form-field-tr" data-field-type="{{=field.type}}" data-field-id="{{=field.id}}" data-options="{{? field.options}}{{=JSON.stringify(field.options)}}{{?}}">
		<td class="builder-small-col">
			<input {{? field.checked}}checked="checked"{{?}} class="field-include" type="checkbox" />
		</td>
		<td>
			{{=field.title}}
		</td>
		<td>
			<input class="widefat" value="{{!field.placeholder}}" type="text" placeholder="Your label">
		</td>
		<td>
			<input {{? field.disableRequired}}disabled{{?}} {{? field.required}}checked="checked"{{?}} class="field-required" type="checkbox">
		</td>
	</tr>
	{{~}}
</script>