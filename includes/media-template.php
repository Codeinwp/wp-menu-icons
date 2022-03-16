<script type="text/html" id="tmpl-menu-icons-item-field-preview-font">
	<i class="_icon {{'fa' == data.type ? '' : data.type}} {{ data.icon }}"></i>
</script>

<script type="text/html" id="tmpl-menu-icons-item-field-preview-image">
	<img src="{{ data.url }}" class="_icon" />
</script>

<script type="text/html" id="tmpl-menu-icons-item-field-preview-svg">
	<img src="{{ data.url }}" class="_icon" />
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-font-before">
	<a href="#">
		<i class="_icon {{ data.type }} {{ data.icon }} _{{ data.position }}"
			style="font-size:{{ data.font_size }}em; vertical-align:{{ data.vertical_align }};"
		></i>
		<span>{{ data.title }}</span>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-font-after">
	<a href="#">
		<span>{{ data.title }}</span>
		<i class="_icon {{ data.type }} {{ data.icon }} _{{ data.position }}"
			style="font-size:{{ data.font_size }}em; vertical-align:{{ data.vertical_align }};"
		></i>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-font-hide_label">
	<a href="#">
		<i class="_icon {{ data.type }} {{ data.icon }} _{{ data.position }}"
			style="font-size:{{ data.font_size }}em; vertical-align:{{ data.vertical_align }};"
		></i>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-image-before">
	<a href="#">
		<img src="{{ data.url }}"
			alt="{{ data.alt }}"
			class="_icon {{ data.type }} _{{ data.position }}"
			style="vertical-align:{{ data.vertical_align }};"
		/>
		<span>{{ data.title }}</span>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-image-after">
	<a href="#">
		<span>{{ data.title }}</span>
		<img src="{{ data.url }}"
			alt="{{ data.alt }}"
			class="_icon {{ data.type }} _{{ data.position }}"
			style="vertical-align:{{ data.vertical_align }};"
		/>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-image-hide_label">
	<a href="#">
		<img src="{{ data.url }}"
			alt="{{ data.alt }}"
			class="_icon {{ data.type }} _{{ data.position }}"
			style="vertical-align:{{ data.vertical_align }};"
		/>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-svg-before">
	<a href="#">
		<img src="{{ data.url }}"
			alt="{{ data.alt }}"
			class="_icon _{{data.type}}"
			style="width:{{data.svg_width}}em;vertical-align:{{ data.vertical_align }}"
			/>
		<span>{{ data.title }}</span>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-svg-after">
	<a href="#">
		<span>{{ data.title }}</span>
		<img src="{{ data.url }}"
			alt="{{ data.alt }}"
			class="_icon _{{data.type}}"
			style="width:{{data.svg_width}}em;vertical-align:{{ data.vertical_align }}"
			/>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-item-sidebar-preview-svg-hide_label">
	<a href="#">
		<img src="{{ data.url }}"
			alt="{{ data.alt }}"
			class="_icon _{{data.type}}"
			style="width:{{data.svg_width}}em;vertical-align:{{ data.vertical_align }}"
			/>
	</a>
</script>

<script type="text/html" id="tmpl-menu-icons-settings-field-text">
	<span>{{ data.label }}</span>
	<input type="text" data-setting="{{ data.id }}" value="{{ data.value }}" />
</script>

<script type="text/html" id="tmpl-menu-icons-settings-field-number">
	<span>{{ data.label }}</span>
	<input type="number" min="{{ data.attributes.min }}" step="{{ data.attributes.step }}" data-setting="{{ data.id }}" value="{{ data.value }}" />
	<# if ( data.description ) { #><em>{{ data.description }} </em><# } #>
</script>

<script type="text/html" id="tmpl-menu-icons-settings-field-select">
	<span>{{ data.label }}</span>
	<select data-setting="{{ data.id }}">
		<# _.each( data.choices, function( choice ) { #>
			<# if ( data.value === choice.value ) { #>
				<option selected="selected" value="{{ choice.value }}">{{ choice.label }}</option>
			<# } else { #>
				<option value="{{ choice.value }}">{{ choice.label }}</option>
			<# } #>
		<# } ); #>
	</select>
</script>

<?php do_action( 'menu_icons_js_templates' );
