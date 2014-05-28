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
	 * @since %ver%
	 * @var   array
	 * @acess protected
	 */
	protected static $defaults = array(
		'global' => array(
			'icon_types' => array(),
			'extra_css'  => '1',
		),
		'menu'   => array(
			'position'       => 'before',
			'vertical-align' => 'middle',
			'font-size'      => '1.2',
			'misc'           => array(),
		)
	);

	/**
	 * Setting values
	 *
	 * @since %ver%
	 * @var   array
	 * @acess protected
	 */
	protected static $settings = array();


	/**
	 * Get setting value
	 *
	 * @since %ver%
	 */
	public static function get() {
		return kucrut_get_array_value_deep( self::$settings, func_get_args() );
	}


	/**
	 * Get setting values and apply sanitation
	 *
	 * @since %ver%
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
	 * @since  %ver%
	 * @param  int   $menu_id
	 * @return array
	 */
	public static function get_menu_settings( $menu_id ) {
		$defaults      = self::$defaults['menu'];
		$menu_settings = self::get( sprintf( 'menu_%d', $menu_id ) );

		if ( is_null( $menu_settings ) ) {
			$menu_settings = $defaults;
		}
		else {
			$menu_settings = wp_parse_args( $menu_settings, $defaults );
		}

		return apply_filters( 'menu_icons_menu_settings', $menu_settings, $menu_id );
	}


	/**
	 * Settings init
	 *
	 * @since %ver%
	 */
	public static function init() {
		self::$defaults['global']['icon_types'] = array_keys( Menu_Icons::get( 'icon_types' ) );
		self::_get();

		require_once Menu_Icons::get( 'dir' ) . 'includes/admin.php';
		Menu_Icons_Admin_Nav_Menus::init();

		add_action( 'load-nav-menus.php', array( __CLASS__, '_load_nav_menus' ), 1 );
	}


	/**
	 * Prepare wp-admin/nav-menus.php page
	 *
	 * @since   %ver%
	 * @wp_hook load-nav-menus.php
	 */
	public static function _load_nav_menus() {
		self::_maybe_update_settings();
		self::_add_settings_meta_box();

		add_action( 'admin_notices', array( __CLASS__, '_admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, '_enqueue_assets' ), 99 );
	}


	/**
	 * Update settings
	 *
	 * @since   %ver%
	 * @access  private
	 * @wp_hook load-nav-menus.php
	 */
	public static function _maybe_update_settings() {
		if ( ! empty( $_POST['menu-icons']['settings'] ) ) {
			check_admin_referer( self::UPDATE_KEY, self::UPDATE_KEY );

			update_option(
				'menu-icons',
				wp_parse_args(
					kucrut_validate( $_POST['menu-icons']['settings'] ),
					self::$settings
				)
			);
			set_transient( self::TRANSIENT_KEY, 'updated', 30 );
			wp_redirect(
				remove_query_arg(
					array( 'menu-icons-reset' ),
					wp_get_referer()
				)
			);
		}
		elseif ( ! empty( $_REQUEST[ self::RESET_KEY ] ) ) {
			check_admin_referer( self::RESET_KEY, self::RESET_KEY );

			delete_option( 'menu-icons' );
			set_transient( self::TRANSIENT_KEY, 'reset', 30 );
			wp_redirect(
				remove_query_arg(
					array( self::RESET_KEY, 'menu-icons-updated' ),
					wp_get_referer()
				)
			);
		}
	}


	/**
	 * Print admin notices
	 *
	 * @since   %ver%
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
	 * @since  %ver%
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
	 * Get settings fields
	 *
	 * @since  %ver%
	 * @uses   apply_filters() Calls 'menu_icons_settings_fields'.
	 * @return array
	 */
	public static function get_fields() {
		global $nav_menu_selected_id;

		$icon_types = array();
		foreach ( Menu_Icons::get( 'icon_types' ) as $id => $props ) {
			$icon_types[ $id ] = $props['label'];
		}

		$fields = array(
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
					array(
						'id'      => 'extra_css',
						'type'    => 'select',
						'label'   => __( 'Extra Stylesheet', 'menu-icons' ),
						'choices' => array(
							'1' => __( 'Enable', 'menu-icons' ),
							'0' => __( 'Disable', 'menu-icons' ),
						),
						'value'   => self::get( 'global', 'extra_css' ),
					),
				),
				'args'  => array(),
			),
		);

		if ( ! empty( $nav_menu_selected_id ) ) {
			$menu_term      = get_term( $nav_menu_selected_id, 'nav_menu' );
			$menu_key       = sprintf( 'menu_%d', $nav_menu_selected_id );
			$menu_settings  = self::get_menu_settings( $nav_menu_selected_id );
			$fields['menu'] = array(
				'id'          => $menu_key,
				'title'       => __( 'Current Menu', 'menu-icons' ),
				'description' => sprintf(
					__( '"%s" menu settings', 'menu-icons' ),
					apply_filters( 'single_term_title', $menu_term->name )
				),
				'fields'      => array(
					array(
						'id'      => 'position',
						'type'    => 'select',
						'label'   => __( 'Position', 'menu-icons' ),
						'choices' => array(
							'before' => __( 'Before', 'menu-icons' ),
							'after'  => __( 'After', 'menu-icons' ),
						),
						'value'   => $menu_settings['position'],
					),
					array(
						'id'      => 'vertical-align',
						'type'    => 'select',
						'label'   => __( 'Vertical Align', 'menu-icons' ),
						'choices' => array(
							'super'       => __( 'Super', 'menu-icons' ),
							'top'         => __( 'Top', 'menu-icons' ),
							'text-top'    => __( 'Text Top', 'menu-icons' ),
							'middle'      => __( 'Middle', 'menu-icons' ),
							'baseline'    => __( 'Baseline', 'menu-icons' ),
							'text-bottom' => __( 'Text Bottom', 'menu-icons' ),
							'bottom'      => __( 'Bottom', 'menu-icons' ),
							'sub'         => __( 'Sub', 'menu-icons' ),
						),
						'value'   => $menu_settings['vertical-align'],
					),
					array(
						'id'          => 'font-size',
						'type'        => 'number',
						'label'       => __( 'Font Size', 'menu-icons' ),
						'description' => 'em',
						'attributes'  => array(
							'min'  => '0.1',
							'step' => '0.1',
						),
						'value'   => $menu_settings['font-size'],
					),
					array(
						'id'      => 'misc',
						'type'    => 'checkbox',
						'label'   => __( 'Misc.', 'menu-icons' ),
						'choices' => array(
							'hide-label' => __( 'Hide Label', 'menu-icons' ),
						),
						'value'   => $menu_settings['misc'],
					),
				),
				'args' => array(
					'inline_description' => true,
				),
			);
		}

		return apply_filters( 'menu_icons_settings_fields', $fields, $nav_menu_selected_id );
	}


	/**
	 * Get processed settings fields
	 *
	 * @since  %ver%
	 * @access private
	 * @return array
	 */
	private static function _get_fields() {
		require_once Menu_Icons::get( 'dir' ) . 'includes/library/form-fields.php';

		$keys     = array( 'menu-icons', 'settings' );
		$sections = self::get_fields();

		foreach ( $sections as &$section ) {
			$_keys = array_merge( $keys, array( $section['id'] ) );
			$_args = array_merge( array( 'keys' => $_keys ), $section['args'] );

			foreach ( $section['fields'] as &$field ) {
				$field = Kucrut_Form_Field::create( $field, $_args );
			}
		}

		return $sections;
	}


	/**
	 * Settings meta box
	 *
	 * @since %ver%
	 */
	public static function _meta_box() {
		?>
			<div class="taxonomydiv">
				<ul id="menu-icons-settings-tabs" class="taxonomy-tabs add-menu-item-tabs hide-if-no-js">
					<?php foreach ( self::get_fields() as $section ) : ?>
						<?php printf(
							'<li><a href="#" title="%s" class="mi-settings-nav-tab" data-type="menu-icons-settings-%s">%s</a></li>',
							esc_attr( $section['description'] ),
							esc_attr( $section['id'] ),
							esc_html( $section['title'] )
						) ?>
					<?php endforeach ?>
				</ul>
				<?php foreach ( self::_get_fields() as $section_index => $section ) : ?>
					<div id="menu-icons-settings-<?php echo esc_attr( $section['id'] ) ?>" class="tabs-panel _<?php echo esc_attr( $section_index ) ?>">
						<h4 class="hide-if-js"><?php echo esc_html( $section['title'] ) ?></h4>
						<?php foreach ( $section['fields'] as $field ) : ?>
							<div class="_field">
								<?php printf(
									'<label for="%s" class="_main">%s</label>',
									esc_attr( $field->id ),
									esc_html( $field->label )
								) ?>
								<?php $field->render() ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="submitbox button-controls">
				<?php wp_nonce_field( self::UPDATE_KEY, self::UPDATE_KEY ) ?>
				<span class="list-controls">
					<?php printf(
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
					) ?>
				</span>

				<span class="add-to-menu">
					<?php submit_button(
						__( 'Save Settings', 'menu-icons' ),
						'secondary',
						'menu-item-settings-save',
						false
					) ?>
				</span>
			</p>
		<?php
	}


	/**
	 * Enqueue scripts & styles for admin page
	 *
	 * @since   %ver%
	 * @wp_hook action admin_enqueue_scripts
	 */
	public static function _enqueue_assets() {
		wp_enqueue_style(
			'menu-icons',
			Menu_Icons::get( 'url' ) . 'css/admin' . Menu_Icons::get_script_suffix() . '.css',
			false,
			Menu_Icons::VERSION
		);
		wp_register_script(
			'kucrut-jquery-input-dependencies',
			Menu_Icons::get( 'url' ) . 'js/input-dependencies' . Menu_Icons::get_script_suffix() . '.js',
			array( 'jquery' ),
			'0.1.0',
			true
		);

		if ( ! empty( self::$settings['global']['icon_types'] ) ) {
			wp_enqueue_media();
		}

		// TODO: WHY U NO WANT MINIFY?
		wp_enqueue_script(
			'menu-icons',
			Menu_Icons::get( 'url' ) . 'js/admin.js',
			array( 'kucrut-jquery-input-dependencies' ),
			Menu_Icons::VERSION,
			true
		);
	}
}
