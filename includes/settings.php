<?php

/**
 * Settings
 *
 * @package Menu_Icons
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */

/**
 * Menu Icons Settings module
 */
final class Menu_Icons_Settings {

	const UPDATE_KEY = 'menu-icons-settings-update';

	const RESET_KEY = 'menu-icons-settings-reset';

	const TRANSIENT_KEY = 'menu_icons_message';

	/**
	 * Default setting values
	 *
	 * @since 0.3.0
	 * @var   array
	 * @acess protected
	 */
	protected static $defaults = array(
		'global' => array(
			'icon_types' => array(),
		),
	);

	/**
	 * Setting values
	 *
	 * @since 0.3.0
	 * @var   array
	 * @acess protected
	 */
	protected static $settings = array();


	/**
	 * Get setting value
	 *
	 * @since  0.3.0
	 * @return mixed
	 */
	public static function get() {
		$args = func_get_args();

		return kucrut_get_array_value_deep( self::$settings, $args );
	}


	/**
	 * Get setting values and apply sanitation
	 *
	 * @since 0.3.0
	 * @acess private
	 */
	private static function _get() {
		$settings = get_option( 'menu-icons', null );

		if ( is_null( $settings ) ) {
			$settings['global'] = self::$defaults['global'];
		}

		/**
		 * Check icon types
		 *
		 * A type could be enabled in the settings but disabled by a filter,
		 * so we need to 'fix' it here.
		 */
		if ( ! empty( $settings['global']['icon_types'] ) ) {
			$active_types = array();
			$icon_types   = Menu_Icons::get( 'icon_types' );

			foreach ( (array) $settings['global']['icon_types'] as $index => $id ) {
				if ( isset( $icon_types[ $id ] ) ) {
					$active_types[] = $id;
				}
			}

			if ( $settings['global']['icon_types'] !== $active_types ) {
				$settings['global']['icon_types'] = $active_types;
				update_option( 'menu-icons', $settings );
			}
		}

		self::$settings = $settings;
	}


	/**
	 * Get menu settings
	 *
	 * @since  0.3.0
	 * @param  int   $menu_id
	 * @return array
	 */
	public static function get_menu_settings( $menu_id ) {
		$menu_settings = self::get( sprintf( 'menu_%d', $menu_id ) );
		$menu_settings = apply_filters( 'menu_icons_menu_settings', $menu_settings, $menu_id );

		if ( ! is_array( $menu_settings ) ) {
			$menu_settings = array();
		}

		return $menu_settings;
	}


	/**
	 * Settings init
	 *
	 * @since 0.3.0
	 */
	public static function init() {
		self::$defaults['global']['icon_types'] = array_keys( Menu_Icons::get( 'icon_types' ) );
		self::_get();

		require_once Menu_Icons::get( 'dir' ) . 'includes/admin.php';
		Menu_Icons_Admin_Nav_Menus::init();

		add_action( 'load-nav-menus.php', array( __CLASS__, '_load_nav_menus' ), 1 );
		add_action( 'wp_ajax_menu_icons_update_settings', array( __CLASS__, '_ajax_menu_icons_update_settings' ) );
	}


	/**
	 * Prepare wp-admin/nav-menus.php page
	 *
	 * @since   0.3.0
	 * @wp_hook load-nav-menus.php
	 */
	public static function _load_nav_menus() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, '_enqueue_assets' ), 99 );

		/**
		 * Allow settings meta box to be disabled.
		 *
		 * @since 0.4.0
		 * @param bool $disabled Defaults to FALSE
		 */
		$settings_disabled = apply_filters( 'menu_icons_disable_settings', false );
		if ( true === $settings_disabled ) {
			return;
		}

		self::_maybe_update_settings();
		self::_add_settings_meta_box();

		add_action( 'admin_notices', array( __CLASS__, '_admin_notices' ) );
	}


	/**
	 * Update settings
	 *
	 * @since   0.3.0
	 * @access  private
	 * @wp_hook load-nav-menus.php
	 */
	public static function _maybe_update_settings() {
		if ( ! empty( $_POST['menu-icons']['settings'] ) ) {
			check_admin_referer( self::UPDATE_KEY, self::UPDATE_KEY );

			$redirect_url = self::_update_settings( $_POST['menu-icons']['settings'] );
			wp_redirect( $redirect );
		} elseif ( ! empty( $_REQUEST[ self::RESET_KEY ] ) ) {
			check_admin_referer( self::RESET_KEY, self::RESET_KEY );
			wp_redirect( self::_reset_settings() );
		}
	}


	/**
	 * Update settings
	 *
	 * @since  0.7.0
	 * @access protected
	 * @param  array     $values Settings values
	 * @return string    Redirect URL
	 */
	protected static function _update_settings( $values ) {
		update_option(
			'menu-icons',
			wp_parse_args(
				kucrut_validate( $values ),
				self::$settings
			)
		);
		set_transient( self::TRANSIENT_KEY, 'updated', 30 );

		$redirect_url = remove_query_arg(
			array( 'menu-icons-reset' ),
			wp_get_referer()
		);

		return $redirect_url;
	}


	/**
	 * Reset settings
	 *
	 * @since  0.7.0
	 * @access protected
	 * @return string    Redirect URL
	 */
	protected static function _reset_settings() {
		delete_option( 'menu-icons' );
		set_transient( self::TRANSIENT_KEY, 'reset', 30 );

		$redirect_url = remove_query_arg(
			array( self::RESET_KEY, 'menu-icons-updated' ),
			wp_get_referer()
		);

		return $redirect_url;
	}


	/**
	 * Update settings via ajax
	 *
	 * @since   0.7.0
	 * @wp_hook action _ajax_menu_icons_update_settings
	 */
	public static function _ajax_menu_icons_update_settings() {
		check_ajax_referer( self::UPDATE_KEY, self::UPDATE_KEY );

		if ( empty( $_POST['menu-icons']['settings'] ) ) {
			wp_send_json_error();
		}

		$redirect_url = self::_update_settings( $_POST['menu-icons']['settings'] );
		wp_send_json_success( array( 'redirectUrl' => $redirect_url ) );
	}


	/**
	 * Print admin notices
	 *
	 * @since   0.3.0
	 * @wp_hook admin_notices
	 */
	public static function _admin_notices() {
		$messages = array(
			'updated' => __( '<strong>Menu Icons Settings</strong> have been successfully updated.', 'menu-icons' ),
			'reset'   => __( '<strong>Menu Icons Settings</strong> have been successfully reset.', 'menu-icons' ),
		);

		$message_type = get_transient( self::TRANSIENT_KEY );
		if ( ! empty( $message_type ) && ! empty( $messages[ $message_type ] ) ) {
			printf(
				'<div class="updated"><p>%s</p></div>',
				wp_kses( $messages[ $message_type ], array( 'strong' => true ) )
			);
		}
	}


	/**
	 * Settings meta box
	 *
	 * @since  0.3.0
	 * @access private
	 */
	private static function _add_settings_meta_box() {
		add_meta_box(
			'menu-icons-settings',
			__( 'Menu Icons Settings', 'menu-icons' ),
			array( __CLASS__, '_meta_box' ),
			'nav-menus',
			'side',
			'low',
			array()
		);
	}


	/**
	 * Get ID of nav menu being edited
	 *
	 * @since  %ver
	 * @return int
	 */
	public static function get_current_menu_id() {
		global $nav_menu_selected_id;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST['menu'] ) ) {
			$menu_id = absint( $_POST['menu'] );
		} else {
			$menu_id = $nav_menu_selected_id;
		}

		return $menu_id;
	}


	/**
	 * Get settings fields
	 *
	 * @since  0.4.0
	 * @param  array $values Values to be applied to each field
	 * @uses   apply_filters() Calls 'menu_icons_settings_fields'.
	 * @return array
	 */
	public static function get_settings_fields( Array $values = array() ) {
		$fields = array(
			'hide_label' => array(
				'id'      => 'hide_label',
				'type'    => 'select',
				'label'   => __( 'Hide Label', 'menu-icons' ),
				'default' => '',
				'choices' => array(
					array(
						'value' => '',
						'label' => __( 'No', 'menu-icons' ),
					),
					array(
						'value' => '1',
						'label' => __( 'Yes', 'menu-icons' ),
					),
				),
			),
			'position'   => array(
				'id'      => 'position',
				'type'    => 'select',
				'label'   => __( 'Position', 'menu-icons' ),
				'default' => 'before',
				'choices' => array(
					array(
						'value' => 'before',
						'label' => __( 'Before', 'menu-icons' ),
					),
					array(
						'value' => 'after',
						'label' => __( 'After', 'menu-icons' ),
					),
				),
			),
		);

		$fields = apply_filters( 'menu_icons_settings_fields', $fields );

		foreach ( $fields as &$field ) {
			if ( isset( $values[ $field['id'] ] ) ) {
				$field['value'] = $values[ $field['id'] ];
			}

			if ( ! isset( $field['value'] ) && isset( $field['default'] ) ) {
				$field['value'] = $field['default'];
			}
		}

		unset( $field );

		return $fields;
	}


	/**
	 * Get settings sections
	 *
	 * @since  0.3.0
	 * @uses   apply_filters() Calls 'menu_icons_settings_sections'.
	 * @return array
	 */
	public static function get_fields() {
		$menu_id    = self::get_current_menu_id();
		$icon_types = array();
		foreach ( Menu_Icons::get( 'icon_types' ) as $id => $props ) {
			$icon_types[ $id ] = $props['label'];
		}

		$sections = array(
			'global' => array(
				'id'          => 'global',
				'title'       => __( 'Global', 'menu-icons' ),
				'description' => __( 'Global settings', 'menu-icons' ),
				'fields'      => array(
					array(
						'id'      => 'icon_types',
						'type'    => 'checkbox',
						'label'   => __( 'Icon Types', 'menu-icons' ),
						'choices' => $icon_types,
						'value'   => self::get( 'global', 'icon_types' ),
					),
				),
				'args'  => array(),
			),
		);

		if ( ! empty( $menu_id ) ) {
			$menu_term      = get_term( $menu_id, 'nav_menu' );
			$menu_key       = sprintf( 'menu_%d', $menu_id );
			$menu_settings  = self::get_menu_settings( $menu_id );

			$sections['menu'] = array(
				'id'          => $menu_key,
				'title'       => __( 'Current Menu', 'menu-icons' ),
				'description' => sprintf(
					__( '"%s" menu settings', 'menu-icons' ),
					apply_filters( 'single_term_title', $menu_term->name )
				),
				'fields'      => self::get_settings_fields( $menu_settings ),
				'args'        => array( 'inline_description' => true ),
			);
		}

		return apply_filters( 'menu_icons_settings_sections', $sections, $menu_id );
	}


	/**
	 * Get processed settings fields
	 *
	 * @since  0.3.0
	 * @access private
	 * @return array
	 */
	private static function _get_fields() {
		if ( ! class_exists( 'Kucrut_Form_Field' ) ) {
			require_once Menu_Icons::get( 'dir' ) . 'includes/library/form-fields.php';
		}

		$keys     = array( 'menu-icons', 'settings' );
		$sections = self::get_fields();

		foreach ( $sections as &$section ) {
			$_keys = array_merge( $keys, array( $section['id'] ) );
			$_args = array_merge( array( 'keys' => $_keys ), $section['args'] );

			foreach ( $section['fields'] as &$field ) {
				$field = Kucrut_Form_Field::create( $field, $_args );
			}

			unset( $field );
		}

		unset( $section );

		return $sections;
	}


	/**
	 * Settings meta box
	 *
	 * @since 0.3.0
	 */
	public static function _meta_box() {
		?>
			<div class="taxonomydiv">
				<ul id="menu-icons-settings-tabs" class="taxonomy-tabs add-menu-item-tabs hide-if-no-js">
					<?php foreach ( self::get_fields() as $section ) : ?>
						<?php
							printf(
								'<li><a href="#" title="%s" class="mi-settings-nav-tab" data-type="menu-icons-settings-%s">%s</a></li>',
								esc_attr( $section['description'] ),
								esc_attr( $section['id'] ),
								esc_html( $section['title'] )
							);
						?>
					<?php endforeach; ?>
					<?php
						printf(
							'<li><a href="#" class="mi-settings-nav-tab" data-type="menu-icons-settings-extensions">%s</a></li>',
							esc_html__( 'Extensions', 'menu-icons' )
						);
					?>
				</ul>
				<?php foreach ( self::_get_fields() as $section_index => $section ) : ?>
					<div id="menu-icons-settings-<?php echo esc_attr( $section['id'] ) ?>" class="tabs-panel _<?php echo esc_attr( $section_index ) ?>">
						<h4 class="hide-if-js"><?php echo esc_html( $section['title'] ) ?></h4>
						<?php foreach ( $section['fields'] as $field ) : ?>
							<div class="_field">
								<?php
									printf(
										'<label for="%s" class="_main">%s</label>',
										esc_attr( $field->id ),
										esc_html( $field->label )
									);
								?>
								<?php $field->render() ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				<div id="menu-icons-settings-extensions" class="tabs-panel _extensions">
					<h4 class="hide-if-js"><?php echo esc_html__( 'Extensions', 'menu-icons' ) ?></h4>
					<ul>
						<li><a target="_blank" href="http://wordpress.org/plugins/menu-icons-icomoon/">IcoMoon</a></li>
					</ul>
				</div>
			</div>
			<p class="submitbox button-controls">
				<?php wp_nonce_field( self::UPDATE_KEY, self::UPDATE_KEY ) ?>
				<span class="list-controls">
					<?php
						printf(
							'<a href="%s" title="%s" class="select-all submitdelete">%s</a>',
							esc_url(
								wp_nonce_url(
									admin_url( '/nav-menus.php' ),
									self::RESET_KEY,
									self::RESET_KEY
								)
							),
							esc_attr__( 'Discard all changes and reset to default state', 'menu-icons' ),
							esc_html__( 'Reset', 'menu-icons' )
						);
					?>
				</span>

				<span class="add-to-menu">
					<span class="spinner"></span>
					<?php
						submit_button(
							__( 'Save Settings', 'menu-icons' ),
							'secondary',
							'menu-item-settings-save',
							false
						);
					?>
				</span>
			</p>
		<?php
	}


	/**
	 * Enqueue scripts & styles for admin page
	 *
	 * @since   0.3.0
	 * @wp_hook action admin_enqueue_scripts
	 */
	public static function _enqueue_assets() {
		$suffix = Menu_Icons::get_script_suffix();

		wp_enqueue_style(
			'menu-icons',
			Menu_Icons::get( 'url' ) . 'css/admin' . $suffix . '.css',
			false,
			Menu_Icons::VERSION
		);
		wp_register_script(
			'kucrut-jquery-input-dependencies',
			Menu_Icons::get( 'url' ) . 'js/input-dependencies' . $suffix . '.js',
			array( 'jquery' ),
			'0.1.0',
			true
		);

		if ( ! empty( self::$settings['global']['icon_types'] ) ) {
			wp_enqueue_media();
		}

		wp_enqueue_script(
			'menu-icons',
			Menu_Icons::get( 'url' ) . 'js/admin' . $suffix . '.js',
			array( 'kucrut-jquery-input-dependencies' ),
			Menu_Icons::VERSION,
			true
		);
	}
}
