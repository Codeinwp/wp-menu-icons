<?php

/**
 * Front end functionalities
 *
 * @package Menu_Icons
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */
final class Menu_Icons_Front_End {

	/**
	 * Icon types
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    array
	 */
	protected static $icon_types = array();

	/**
	 * Default icon style
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    array
	 */
	protected static $default_style = array(
		'font_size'      => array(
			'property' => 'font-size',
			'value'    => '1.2',
			'unit'     => 'em',
		),
		'vertical_align' => array(
			'property' => 'vertical-align',
			'value'    => 'middle',
			'unit'     => null,
		),
		'svg_width'      => array(
			'property' => 'width',
			'value'    => '1',
			'unit'     => 'em',
		),
	);

	/**
	 * Hidden label class
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    string
	 */
	protected static $hidden_label_class = 'visuallyhidden';


	/**
	 * Add hooks for front-end functionalities
	 *
	 * @since 0.9.0
	 */
	public static function init() {
		$active_types = Menu_Icons_Settings::get( 'global', 'icon_types' );

		if ( empty( $active_types ) ) {
			return;
		}

		foreach ( Menu_Icons::get( 'types' ) as $type ) {
			if ( in_array( $type->id, $active_types, true ) ) {
				self::$icon_types[ $type->id ] = $type;
			}
		}

		/**
		 * Allow themes/plugins to override the hidden label class
		 *
		 * @since  0.8.0
		 * @param  string $hidden_label_class Hidden label class.
		 * @return string
		 */
		self::$hidden_label_class = apply_filters( 'menu_icons_hidden_label_class', self::$hidden_label_class );

		/**
		 * Allow themes/plugins to override default inline style
		 *
		 * @since  0.9.0
		 * @param  array $default_style Default inline style.
		 * @return array
		 */
		self::$default_style = apply_filters( 'menu_icons_default_style', self::$default_style );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_styles' ), 4 );
		add_filter( 'wp_nav_menu_args', array( __CLASS__, '_add_menu_item_title_filter' ) );
		add_filter( 'wp_nav_menu', array( __CLASS__, '_remove_menu_item_title_filter' ) );
	}


	/**
	 * Get nav menu ID based on arguments passed to wp_nav_menu()
	 *
	 * @since  0.3.0
	 * @param  array $args wp_nav_menu() Arguments
	 * @return mixed Nav menu ID or FALSE on failure
	 */
	public static function get_nav_menu_id( $args ) {
		$args = (object) $args;
		$menu = wp_get_nav_menu_object( $args->menu );

		// Get the nav menu based on the theme_location
		if ( ! $menu
			&& $args->theme_location
			&& ( $locations = get_nav_menu_locations() )
			&& isset( $locations[ $args->theme_location ] )
		) {
			$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
		}

		// get the first menu that has items if we still can't find a menu
		if ( ! $menu && ! $args->theme_location ) {
			$menus = wp_get_nav_menus();
			foreach ( $menus as $menu_maybe ) {
				if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
					$menu = $menu_maybe;
					break;
				}
			}
		}

		if ( is_object( $menu ) && ! is_wp_error( $menu ) ) {
			return $menu->term_id;
		} else {
			return false;
		}
	}


	/**
	 * Enqueue stylesheets
	 *
	 * @since   0.1.0
	 * @wp_hook action wp_enqueue_scripts
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
	 */
	public static function _enqueue_styles() {
		// Deregister icon picker plugin font-awesome style and re-register with the new handler to avoid other plugin/theme style handler conflict.
		$wp_styles = wp_styles();
		if ( $wp_styles && isset( $wp_styles->registered['font-awesome'] ) ) {
			$registered = $wp_styles->registered['font-awesome'];
			if ( strpos( $registered->src, Menu_Icons::get( 'url' ) ) !== false ) {
				$wp_styles->remove( 'font-awesome' );
				$registered->ver = Menu_Icons_Font_Awesome::$version;
				$wp_styles->add( 'menu-icon-' . $registered->handle, $registered->src, $registered->deps, $registered->ver, $registered->args );
			}
		}

		foreach ( self::$icon_types as $type ) {
			$stylesheet_id = $type->stylesheet_id;
			if ( 'font-awesome' === $stylesheet_id ) {
				$stylesheet_id = 'menu-icon-' . $stylesheet_id;
			}
			if ( wp_style_is( $stylesheet_id, 'registered' ) ) {
				wp_enqueue_style( $stylesheet_id );
			}
		}

		/**
		 * Allow plugins/themes to override the extra stylesheet location
		 *
		 * @since 0.9.0
		 * @param string $extra_stylesheet_uri Extra stylesheet URI.
		 */
		$extra_stylesheet_uri = apply_filters(
			'menu_icons_extra_stylesheet_uri',
			sprintf( '%scss/extra%s.css', Menu_Icons::get( 'url' ), kucrut_get_script_suffix() )
		);

		wp_enqueue_style(
			'menu-icons-extra',
			$extra_stylesheet_uri,
			false,
			Menu_Icons::VERSION
		);
	}


	/**
	 * Add filter to 'the_title' hook
	 *
	 * We need to filter the menu item title but **not** regular post titles.
	 * Thus, we're adding the filter when `wp_nav_menu()` is called.
	 *
	 * @since   0.1.0
	 * @wp_hook filter wp_nav_menu_args
	 * @param   array  $args Not used.
	 *
	 * @return array
	 */
	public static function _add_menu_item_title_filter( $args ) {
		add_filter( 'the_title', array( __CLASS__, '_add_icon' ), 999, 2 );
		add_filter( 'megamenu_the_title', array( __CLASS__, '_add_icon' ), 999, 2 );
		add_filter( 'megamenu_nav_menu_css_class', array( __CLASS__, '_add_menu_item_class' ), 10, 3 );

		return $args;
	}


	/**
	 * Remove filter from 'the_title' hook
	 *
	 * Because we don't want to filter post titles, we need to remove our
	 * filter when `wp_nav_menu()` exits.
	 *
	 * @since   0.1.0
	 * @wp_hook filter wp_nav_menu
	 * @param   array  $nav_menu Not used.
	 * @return  array
	 */
	public static function _remove_menu_item_title_filter( $nav_menu ) {
		remove_filter( 'the_title', array( __CLASS__, '_add_icon' ), 999, 2 );
		remove_filter( 'megamenu_the_title', array( __CLASS__, '_add_icon' ), 999, 2 );
		remove_filter( 'megamenu_nav_menu_css_class', array( __CLASS__, '_add_menu_item_class' ), 10, 3 );
		return $nav_menu;
	}


	/**
	 * Add icon to menu item title
	 *
	 * @since   0.1.0
	 * @since   0.9.0   Renamed the method to `add_icon()`.
	 * @wp_hook filter  the_title
	 * @param   string  $title     Menu item title.
	 * @param   int     $id        Menu item ID.
	 *
	 * @return string
	 */
	public static function _add_icon( $title, $id ) {
		$meta = Menu_Icons_Meta::get( $id );
		$icon = self::get_icon( $meta );

		if ( empty( $icon ) ) {
			return $title;
		}
		$menu_id           = Menu_Icons_Settings::get_current_menu_id();
		$menu_key          = sprintf( 'menu_%d', $menu_id );
		$global_hide_label = Menu_Icons_Settings::get( $menu_key, 'hide_label' );
		$title_class       = ! empty( $global_hide_label ) || ! empty( $meta['hide_label'] ) ? self::$hidden_label_class : '';
		$title_wrapped     = sprintf(
			'<span%s>%s</span>',
			( ! empty( $title_class ) ) ? sprintf( ' class="%s"', esc_attr( $title_class ) ) : '',
			$title
		);

		if ( 'after' === $meta['position'] ) {
			$title_with_icon = "{$title_wrapped}{$icon}";
		} else {
			$title_with_icon = "{$icon}{$title_wrapped}";
		}

		/**
		 * Allow plugins/themes to override menu item markup
		 *
		 * @since 0.8.0
		 *
		 * @param string  $title_with_icon Menu item markup after the icon is added.
		 * @param integer $id              Menu item ID.
		 * @param array   $meta            Menu item metadata values.
		 * @param string  $title           Original menu item title.
		 *
		 * @return string
		 */
		$title_with_icon = apply_filters( 'menu_icons_item_title', $title_with_icon, $id, $meta, $title );

		return $title_with_icon;
	}


	/**
	 * Get icon
	 *
	 * @since  0.9.0
	 * @param  array  $meta Menu item meta value.
	 * @return string
	 */
	public static function get_icon( $meta ) {
		$icon = '';

		// Icon type is not set.
		if ( empty( $meta['type'] ) ) {
			return $icon;
		}

		// Icon is not set.
		if ( empty( $meta['icon'] ) ) {
			return $icon;
		}

		// Icon type is not registered/enabled.
		if ( ! isset( self::$icon_types[ $meta['type'] ] ) ) {
			return $icon;
		}

		$type = self::$icon_types[ $meta['type'] ];

		$callbacks = array(
			array( $type, 'get_icon' ),
			array( __CLASS__, "get_{$type->id}_icon" ),
			array( __CLASS__, "get_{$type->template_id}_icon" ),
		);

		foreach ( $callbacks as $callback ) {
			if ( is_callable( $callback ) ) {
				$icon = call_user_func( $callback, $meta );
				break;
			}
		}

		return $icon;
	}


	/**
	 * Get icon style
	 *
	 * @since  0.9.0
	 * @param  array   $meta         Menu item meta value.
	 * @param  array   $keys         Style properties.
	 * @param  bool    $as_attribute Optional. Whether to output the style as HTML attribute or value only.
	 *                               Defaults to TRUE.
	 * @return string
	 */
	public static function get_icon_style( $meta, $keys, $as_attribute = true ) {
		$style_a = array();
		$style_s = '';

		foreach ( $keys as $key ) {
			if ( ! isset( self::$default_style[ $key ] ) ) {
				continue;
			}

			$rule = self::$default_style[ $key ];

			if ( ! isset( $meta[ $key ] ) || $meta[ $key ] === $rule['value'] ) {
				continue;
			}

			$value = $meta[ $key ];
			if ( ! empty( $rule['unit'] ) ) {
				$value .= $rule['unit'];
			}

			$style_a[ $rule['property'] ] = $value;
		}

		if ( empty( $style_a ) ) {
			return $style_s;
		}

		foreach ( $style_a as $key => $value ) {
			$style_s .= "{$key}:{$value};";
		}

		$style_s = esc_attr( $style_s );

		if ( $as_attribute  ) {
			$style_s = sprintf( ' style="%s"', $style_s );
		}

		return $style_s;
	}


	/**
	 * Get icon classes
	 *
	 * @since  0.9.0
	 * @param  array         $meta    Menu item meta value.
	 * @param  string        $output  Whether to output the classes as string or array. Defaults to string.
	 * @return string|array
	 */
	public static function get_icon_classes( $meta, $output = 'string' ) {
		$classes = array( '_mi' );

		if ( empty( $meta['hide_label'] ) ) {
			$classes[] = "_{$meta['position']}";
		}

		if ( 'string' === $output ) {
			$classes = implode( ' ', $classes );
		}

		return $classes;
	}


	/**
	 * Get font icon
	 *
	 * @since  0.9.0
	 * @param  array  $meta Menu item meta value.
	 * @return string
	 */
	public static function get_font_icon( $meta ) {
		$type = $meta['type'];
		$icon = $meta['icon'];

		$font_awesome5 = font_awesome_backward_compatible();
		if ( ! empty( $type ) && 'fa' === $type ) {
			$icon    = explode( ' ', $icon );
			$type    = reset( $icon );
			$icon    = end( $icon );
			$fa_icon = sprintf( '%s-%s', $type, $icon );
			if ( array_key_exists( $fa_icon, $font_awesome5 ) ) {
				$fa5_icon  = $font_awesome5[ $fa_icon ];
				$fa5_class = explode( ' ', $fa5_icon );
				$type      = reset( $fa5_class );
				$icon      = end( $fa5_class );
			}
		}
		$classes = sprintf( '%s %s %s', self::get_icon_classes( $meta ), $type, $icon );
		$style   = self::get_icon_style( $meta, array( 'font_size', 'vertical_align' ) );
		return sprintf( '<i class="%s" aria-hidden="true"%s></i>', esc_attr( $classes ), $style );
	}


	/**
	 * Get image icon
	 *
	 * @since  0.9.0
	 * @param  array  $meta Menu item meta value.
	 * @return string
	 */
	public static function get_image_icon( $meta ) {
		$args = array(
			'class'       => sprintf( '%s _image', self::get_icon_classes( $meta ) ),
			'aria-hidden' => 'true',
		);

		$style = self::get_icon_style( $meta, array( 'vertical_align' ), false );
		if ( ! empty( $style ) ) {
			$args['style'] = $style;
		}

		return wp_get_attachment_image( $meta['icon'], $meta['image_size'], false, $args );
	}


	/**
	 * Get SVG icon
	 *
	 * @since  0.9.0
	 * @param  array  $meta Menu item meta value.
	 * @return string
	 */
	public static function get_svg_icon( $meta ) {
		$classes = sprintf( '%s _svg', self::get_icon_classes( $meta ) );
		$style   = self::get_icon_style( $meta, array( 'svg_width', 'vertical_align' ) );

		$svg_icon = esc_url( wp_get_attachment_url( $meta['icon'] ) );
		$width  = '';
		$height = '';
		if ( 'image/svg+xml' === get_post_mime_type( $meta['icon'] ) ) {

			// Check `WP_Filesystem` function exists OR not.
			require_once ABSPATH . '/wp-admin/includes/file.php';
			\WP_Filesystem();
			global $wp_filesystem;

			$svg_icon = get_attached_file( $meta['icon'] );
			$svg_icon_content = $wp_filesystem->get_contents( $svg_icon );
			if ( $svg_icon_content ) {
				$xmlget = simplexml_load_string( $svg_icon_content );
				$xmlattributes = $xmlget->attributes();
				$width  = (string) $xmlattributes->width;
				$width  = (int) filter_var( $xmlattributes->width, FILTER_SANITIZE_NUMBER_INT );
				$height = (string) $xmlattributes->height;
				$height = (int) filter_var( $xmlattributes->height, FILTER_SANITIZE_NUMBER_INT );
			}
		} else {
			$attachment_meta = wp_get_attachment_metadata( $meta['icon'] );
			if ( $attachment_meta ) {
				$width = isset( $attachment_meta['width'] ) ? $attachment_meta['width'] : $width;
				$height = isset( $attachment_meta['height'] ) ? $attachment_meta['height'] : $height;
			}
		}
		if ( ! empty( $width ) ) {
			$width = sprintf( ' width="%d"', $width );
		}
		if ( ! empty( $height ) ) {
			$height = sprintf( ' height="%d"', $height );
		}
		$image_alt = get_post_meta( $meta['icon'], '_wp_attachment_image_alt', true );
		$image_alt = $image_alt ? wp_strip_all_tags( $image_alt ) : '';
		return sprintf(
			'<img src="%s" class="%s" aria-hidden="true" alt="%s"%s%s%s/>',
			esc_url( wp_get_attachment_url( $meta['icon'] ) ),
			esc_attr( $classes ),
			$image_alt,
			$width,
			$height,
			$style
		);
	}

	/**
	 * Add menu item class in `Max Mega Menu` item.
	 *
	 * @param array  $classes Item classes.
	 * @param array  $item WP menu item.
	 * @param object $args Menu object.
	 * @return array
	 */
	public static function _add_menu_item_class( $classes, $item, $args ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$classes[] = 'menu-item';
		return $classes;
	}
}
