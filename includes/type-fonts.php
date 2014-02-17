<?php
/**
 * Icon fonts handler
 *
 * @package Menu_Icons
 * @version 0.1.0
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */


/**
 * Generic handler for icons fonts
 *
 * @since 0.1.0
 */
class Menu_Icons_Fonts {

	/**
	 * Holds icon type
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string
	 */
	protected $type;

	/**
	 * Holds array key for icon value
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string
	 */
	protected $key;


	/**
	 * Class constructor
	 *
	 * @since 0.1.0
	 * @param string $type Icon type
	 */
	public function __construct( $type ) {
		$this->type = $type;
		$this->key  = $type . '-icon';

		add_filter( 'wp_nav_menu_args', array( $this, '_add_menu_item_title_filter' ) );
		add_filter( 'wp_nav_menu', array( $this, '_remove_menu_item_title_filter' ) );
	}


	/**
	 * Add filter to 'the_title' hook
	 *
	 * We need to filter the menu item title and we don't to filter post titles.
	 * Thus, we're adding the filter when wp_nav_menu() is called.
	 *
	 * @since 0.1.0
	 * @wp_hook filter wp_nav_menu_args
	 * @param array $args Not used
	 *
	 * @return array
	 */
	public function _add_menu_item_title_filter( $args ) {
		add_filter( 'the_title', array( $this, '_filter_menu_item_title' ), 999, 2 );

		return $args;
	}


	/**
	 * Remove filter from 'the_title' hook
	 *
	 * Because we don't want to filter post titles, we need to remove our
	 * filter when wp_nav_menu() is done.
	 *
	 * @since 0.1.0
	 * @wp_hook filter wp_nav_menu_args
	 * @param array $nav_menu Not used
	 *
	 * @return array
	 */
	public function _remove_menu_item_title_filter( $nav_menu ) {
		remove_filter( 'the_title', array( $this, '_filter_menu_item_title' ), 999, 2 );

		return $nav_menu;
	}


	/**
	 * Filter menu item titles
	 *
	 * @since 0.1.0
	 * @wp_hook filter the_title
	 *
	 * @param string $title Menu item title
	 * @param int    $id    Menu item ID
	 *
	 * @return string
	 */
	public function _filter_menu_item_title( $title, $id ) {
		$values = array_filter( (array) get_post_meta( $id, 'menu-icons', true ) );

		if ( empty( $values['type'] ) ) {
			return $title;
		}

		if ( $values['type'] !== $this->type ) {
			return $title;
		}

		if ( empty( $values[ $this->key ] ) ) {
			return $title;
		}

		$title = $this->add_icon( $title, $values );

		return $title;
	}


	/**
	 * Add icon to menu title
	 *
	 * Icon types should extend this class and override this method if they want to provide
	 * different markup.
	 *
	 * @since 0.1.0
	 * @param string $title  Menu item title
	 * @param array  $values Menu item metadata value
	 *
	 * @return string
	 */
	protected function add_icon( $title, $values ) {
		$title = sprintf( '<i class="%s %s"></i>%s', $values['type'], $values[ $this->key ], $title );

		return $title;
	}
}

