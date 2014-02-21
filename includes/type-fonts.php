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
abstract class Menu_Icons_Fonts {

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
	 * @access private
	 * @var    string
	 */
	private $_key;


	/**
	 * Get icon names
	 *
	 * @since  0.1.0
	 * @return array
	 */
	abstract function get_names();


	/**
	 * Class constructor
	 *
	 * This simply sets $key
	 *
	 * @since  0.1.0
	 */
	function __construct() {
		$this->_key = $this->type . '-icon';

		if ( is_null( $this->version ) ) {
			$this->version = get_bloginfo( 'version' );
		}
	}


	/**
	 * Register our type
	 *
	 * @since 0.1.0
	 * @param array $types Icon Types
	 * @return array
	 */
	public function register( $types ) {
		$props = array(
			'label'      => $this->label,
			'field_cb'   => array( $this, 'the_field' ),
			'front_cb'   => array( $this, 'front' ),
			'stylesheet' => $this->stylesheet,
			'version'    => $this->version,
		);

		$types[ $this->type ] = $props;

		return $types;
	}


	/**
	 * Print field for icons selection
	 *
	 * @since 0.1.0
	 * @param int   $id         Menu item ID
	 * @param array $meta_value Current value of 'menu-icons' metadata
	 */
	public function the_field( $id, $meta_value ) {
		$current    = isset( $meta_value[ $this->_key ] ) ? $meta_value[ $this->_key ] : '';
		$input_id   = sprintf( 'menu-icons-%d-%s', $id, $this->_key );
		$input_name = sprintf( 'menu-icons[%d][%s]', $id, $this->_key );
		?>
		<?php printf(
			'<p class="field-icon-child description menu-icon-type-%1$s" data-dep-on="%1$s">',
			esc_attr( $this->type )
		) ?>
			<label for="<?php echo esc_attr( $input_id ) ?>"><?php echo esc_html( $this->label ); ?></label>
			<select id="<?php echo esc_attr( $input_id ) ?>" name="<?php echo esc_attr( $input_name ) ?>">
				<?php printf(
					'<option value=""%s>%s</option>',
					selected( empty( $current ), true, false ),
					esc_html__( '&mdash; Select &mdash;', 'menu-icons' )
				) ?>
				<?php foreach ( $this->get_names() as $group ) : ?>
					<optgroup label="<?php echo esc_attr( $group['label'] ) ?>">
						<?php foreach ( $group['items'] as $value => $label ) : ?>
							<?php printf(
								'<option value="%s"%s>%s</option>',
								esc_attr( $value ),
								selected( $meta_value[ $this->_key ], $value, false ),
								esc_html( $label )
							) ?>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
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
		add_filter( 'the_title', array( $this, '_filter_menu_item_title' ), 999, 2 );

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
		$values = array_filter( (array) get_post_meta( $id, 'menu-icons', true ) );

		if ( empty( $values['type'] ) ) {
			return $title;
		}

		if ( $values['type'] !== $this->type ) {
			return $title;
		}

		if ( empty( $values[ $this->_key ] ) ) {
			return $title;
		}

		$title = $this->add_icon( $title, $values );

		return $title;
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
	protected function add_icon( $title, $values ) {
		$title = sprintf( '<i class="%s %s"></i>%s', $values['type'], $values[ $this->_key ], $title );

		return $title;
	}
}
