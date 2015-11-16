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
	 * @since  0.9.0 Deprecated.
	 */
	function __construct() {
		_deprecated_function( __CLASS__, '0.9.0', 'Icon_Picker_Type' );
	}


	/**
	 * Register our type
	 *
	 * @since  0.1.0
	 * @since  0.9.0 Deprecated. This simply returns the $types.
	 * @param  array $types Icon Types
	 * @uses   apply_filters() Calls 'menu_icons_{type}_props' on type properties.
	 * @return array
	 */
	public function register( $types ) {
		return $types;
	}
}
