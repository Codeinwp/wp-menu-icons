<?php
/**
 * Font awesome 5 icons support.
 *
 * @package Menu_Icons
 */
final class Menu_Icons_Font_Awesome {

	/**
	 * Font Awesome version
	 *
	 * @access static
	 * @var    string
	 */
	public static $version = '5.15.4';

	/**
	 * Init hooks.
	 */
	public static function init() {
		add_filter( 'icon_picker_icon_type_stylesheet_uri', array( __CLASS__, '_icon_type_stylesheet_uri' ), 10, 3 );
		add_filter( 'icon_picker_fa_items', array( __CLASS__, '_icon_picker_fa_items' ) );
		add_filter( 'icon_picker_font_media_templates', array( __CLASS__, '_icon_picker_font_media_templates' ) );
	}

	/**
	 * Font Awesome's stylesheet.
	 *
	 * @param  string                $stylesheet_uri Icon type's stylesheet URI.
	 * @param  string                $icon_type_id   Icon type's ID.
	 * @param  Icon_Picker_Type_Font $icon_type      Icon type's instance.
	 *
	 * @return string
	 */
	public static function _icon_type_stylesheet_uri( $stylesheet_uri, $icon_type_id, $icon_type ) {
		if ( 'fa' === $icon_type_id ) {
			$url = Menu_Icons::get( 'url' );

			$stylesheet_uri = sprintf(
				"{$url}css/fontawesome/css/all.min.css",
				$icon_type->version
			);
		}

		return $stylesheet_uri;
	}


	/**
	 * Icon picker fontawesome items.
	 *
	 * @param array $icons Icons.
	 * @return array Icons.
	 */
	public static function _icon_picker_fa_items( $icons ) {
		if ( empty( $icons ) ) {
			return $icons;
		}

		$deprecated_icons = array_search( 'fa-tripadvisor', array_column( $icons, 'id' ), true );
		if ( false !== $deprecated_icons ) {
			unset( $icons[ $deprecated_icons ] );
			$icons = array_values( $icons );
		}

		$font_awesome = font_awesome_backward_compatible();
		foreach ( $icons as $key => $icon ) {
			$old_fa_icon = sprintf( 'fa-%s', $icons[ $key ]['id'] );
			if ( array_key_exists( $old_fa_icon, $font_awesome ) ) {
				$icons[ $key ]['id'] = trim( $font_awesome[ $old_fa_icon ] );
			} else {
				$icons[ $key ]['id'] = sprintf( 'fa %s', trim( $icons[ $key ]['id'] ) );
			}
		}

		// Fa5 extra icons support.
		$global_settins = get_option( 'menu-icons', false );
		if ( ! empty( $global_settins['global']['fa5_extra_icons'] ) ) {
			$fa5_extra_icons = $global_settins['global']['fa5_extra_icons'];
			$fa5_extra_icons = explode( ',', $fa5_extra_icons );
			$fa5_extra_icons = array_map( 'trim', $fa5_extra_icons );
			if ( ! empty( $fa5_extra_icons ) ) {
				foreach ( $fa5_extra_icons as $fa5_icon ) {
					$icon_name = explode( '-', $fa5_icon );
					$icon_name = end( $icon_name );
					$icons[]   = array(
						'group' => 'all',
						'id'    => $fa5_icon,
						'name'  => $icon_name,
					);
				}
			}
		}

		return $icons;
	}


	/**
	 * Icon picker font media template.
	 *
	 * @param string $template Media template.
	 * @return string Media template.
	 */
	public static function _icon_picker_font_media_templates( $template ) {
		$templates = array(
			'icon' => '<i class="_icon {{data.type}} {{ data.icon }}"></i>',
			'item' => sprintf(
				'<div class="attachment-preview js--select-attachment">
			<div class="thumbnail">
			<span class="_icon"><i class="{{"fa" == data.type ? "" : data.type}} {{ data.id }}"></i></span>
			<div class="filename"><div>{{ data.name }}</div></div>
			</div>
			</div>
			<a class="check" href="#" title="%s"><div class="media-modal-icon"></div></a>',
				esc_attr__( 'Deselect', 'icon-picker' )
			),
		);

		return $templates;
	}
}
