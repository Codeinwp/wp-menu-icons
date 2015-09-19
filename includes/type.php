<?php
/**
 * Icon type handler
 *
 * @package Menu_Icons
 * @version 0.1.0
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */


/**
 * Generic handler for icon type
 *
 * @since 0.1.0
 */
abstract class Menu_Icons_Type {

	/**
	 * Holds icon type
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $type;

	/**
	 * Holds icon label
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $label;

	/**
	 * Holds icon stylesheet URL
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $stylesheet;

	/**
	 * Custom stylesheet ID
	 *
	 * @since  0.8.0
	 * @access protected
	 * @var    string
	 */
	protected $stylesheet_id;

	/**
	 * Holds icon version
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version;

	/**
	 * Holds array key for icon value
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $key;

	/**
	 * Holds menu settings
	 *
	 * @since  0.3.0
	 * @access protected
	 * @var    array
	 */
	protected $menu_setttings = array();


	/**
	 * Class constructor
	 *
	 * This simply sets $key
	 *
	 * @since  0.1.0
	 */
	function __construct() {
		$this->key = $this->type . '-icon';

		if ( empty( $this->stylesheet_id ) ) {
			$this->stylesheet_id = $this->type;
		}

		if ( is_null( $this->version ) ) {
			$this->version = get_bloginfo( 'version' );
		}

		add_filter( 'menu_icons_settings_fields', array( $this, '_settings_fields' ) );
	}


	/**
	 * __get() Magic method
	 *
	 * @since  0.5.0
	 * @param  string $name Property name
	 * @return mixed  NULL if attribute doesn't exist
	 */
	public function __get( $name ) {
		$props = get_object_vars( $this );

		if ( array_key_exists( $name, $props ) ) {
			return $props[ $name ];
		}

		return null;
	}


	/**
	 * Register our type
	 *
	 * @since  0.1.0
	 * @param  array $types Icon Types
	 * @uses   apply_filters() Calls 'menu_icons_{type}_props' on type properties.
	 * @return array
	 */
	public function register( $types ) {
		$props = array(
			'label'         => $this->label,
			'field_cb'      => array( $this, 'the_field' ),
			'front_cb'      => array( $this, 'front' ),
			'stylesheet_id' => $this->stylesheet_id,
			'stylesheet'    => $this->stylesheet,
			'version'       => $this->version,
		);

		if ( method_exists( $this, 'frame_cb' ) ) {
			$props['frame_cb'] = array( $this, 'frame_cb' );
			if ( method_exists( $this, 'templates' ) ) {
				$props['templates'] = $this->templates();
			}
			if ( method_exists( $this, 'preview_cb' ) ) {
				$props['preview_cb'] = array( $this, 'preview_cb' );
			}
		}

		/**
		 * Allow plugins/themes to filter icon type properties
		 *
		 * @since  0.5.0
		 * @param  array  $props Icon type properties
		 * @param  object $this  Icon type class object
		 * @return array
		 */
		$types[ $this->type ] = apply_filters(
			sprintf( 'menu_icons_%s_props', $this->type ),
			$props,
			$this
		);

		return $types;
	}


	/**
	 * Print field for icons selection
	 *
	 * @since 0.1.0
	 * @param int   $id         Menu item ID
	 * @param array $meta_value Current value of 'menu-icons' metadata
	 */
	abstract public function the_field( $id, $meta_value );


	/**
	 * Settings fields
	 *
	 * @since  0.4.0
	 * @param  array $fields
	 * @return array
	 */
	public function _settings_fields( $fields ) {
		return $fields;
	}


	/**
	 * Front-end tasks
	 *
	 * @since 0.1.0
	 * @param string $type Icon type
	 */
	public function front() {
		add_filter( 'wp_nav_menu_args', array( $this, '_add_menu_item_title_filter' ) );
		add_filter( 'wp_nav_menu', array( $this, '_remove_menu_item_title_filter' ) );
	}


	/**
	 * Add filter to 'the_title' hook
	 *
	 * We need to filter the menu item title but **not** regular post titles.
	 * Thus, we're adding the filter when `wp_nav_menu()` is called.
	 *
	 * @since 0.1.0
	 * @link  http://codex.wordpress.org/Plugin_API/Action_Reference/wp_nav_menu_args Filter: wp_nav_menu_args/999/2
	 * @param array $args Not used
	 *
	 * @return array
	 */
	public function _add_menu_item_title_filter( $args ) {
		$menu_id = Menu_Icons::get_nav_menu_id( $args );

		if ( false !== $menu_id ) {
			$this->menu_settings = Menu_Icons_Settings::get_menu_settings( $menu_id );
			add_filter( 'the_title', array( $this, '_filter_menu_item_title' ), 999, 2 );
		}

		return $args;
	}


	/**
	 * Remove filter from 'the_title' hook
	 *
	 * Because we don't want to filter post titles, we need to remove our
	 * filter when `wp_nav_menu()` exits.
	 *
	 * @since  0.1.0
	 * @link   http://codex.wordpress.org/Plugin_API/Action_Reference/wp_nav_menu Filter: wp_nav_menu/999/2
	 * @param  array $nav_menu Not used
	 * @return array
	 */
	public function _remove_menu_item_title_filter( $nav_menu ) {
		$this->menu_settings = array();
		remove_filter( 'the_title', array( $this, '_filter_menu_item_title' ), 999, 2 );

		return $nav_menu;
	}


	/**
	 * Filter menu item titles
	 *
	 * @since 0.1.0
	 * @link  http://codex.wordpress.org/Plugin_API/Action_Reference/the_title Filter: the_title/999/2
	 *
	 * @param string $title Menu item title
	 * @param int    $id    Menu item ID
	 *
	 * @return string
	 */
	public function _filter_menu_item_title( $title, $id ) {
		$values = wp_parse_args( Menu_Icons::get_meta( $id ), $this->menu_settings );

		if ( empty( $values['type'] ) ) {
			return $title;
		}

		if ( $values['type'] !== $this->type ) {
			return $title;
		}

		if ( empty( $values[ $this->key ] ) ) {
			return $title;
		}

		$title_with_icon = $this->add_icon( $title, $values );

		/**
		 * Allow plugins/themes to override menu item markup
		 *
		 * @since 0.8.0
		 *
		 * @param string  $title_with_icon Menu item markup after the icon is added.
		 * @param integer $id              Menu item ID.
		 * @param array   $values          Menu item metadata values.
		 * @param string  $title           Original menu item title.
		 *
		 * @return string
		 */
		$title_with_icon = apply_filters(
			'menu_icons_item_title',
			$title_with_icon,
			$id,
			$values,
			$title
		);

		return $title_with_icon;
	}


	/**
	 * Add icon to menu title
	 *
	 * Icon types should override this method if they want to provide different markup.
	 *
	 * @since 0.1.0
	 * @param string $title  Menu item title
	 * @param array  $values Menu item metadata value
	 *
	 * @return string
	 */
	abstract protected function add_icon( $title, $values );
}
