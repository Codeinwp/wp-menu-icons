<?php
/**
 * Menu editor handler
 * @package Menu_Icons
 * @version 0.1.1
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */


/**
 * Menu item metadata
 *
 * @since 0.1.0
 */
class Menu_Icons_Admin_Nav_Menus {

	/**
	 * Initialize class
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, '_scripts_styles' ) );
		add_action( 'menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
	}


	/**
	 * Get icon types
	 *
	 * @since  0.1.0
	 * @access protected
	 * @uses   apply_filters() Calls 'menu_icons_types' on returned array.
	 *
	 * @return array
	 */
	protected static function _get_types() {
		$types = array_merge(
			array(
				'' => array(
					'id'    => '',
					'label' => __( '&mdash; Select &mdash;', 'menu-icons' )
				),
			),
			Menu_Icons::get( 'icon_types' )
		);

		return $types;
	}


	/**
	 * Enqueue scripts & styles on wp-admin/nav-menus.php
	 *
	 * @since   0.1.1
	 * @access  protected
	 * @wp_hook admin_enqueue_scripts
	 */
	public static function _scripts_styles() {
		wp_register_script(
			'kucrut-jquery-input-dependencies',
			Menu_Icons::get( 'url' ) . 'js/input-dependencies.js',
			array( 'jquery' ),
			'0.1.0',
			true
		);
		wp_enqueue_script(
			'menu-icons',
			Menu_Icons::get( 'url' ) . 'js/admin.js',
			array( 'kucrut-jquery-input-dependencies' ),
			Menu_Icons::VERSION,
			true
		);

		wp_enqueue_style(
			'menu-icons',
			Menu_Icons::get( 'url' ) . 'css/admin.css',
			false,
			Menu_Icons::VERSION
		);
	}


	/**
	 * Print fields
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @uses    add_action() Calls 'menu_icons_before_fields' hook
	 * @uses    add_action() Calls 'menu_icons_after_fields' hook
	 * @wp_hook action       menu_item_custom_fields/10/3
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth Nav menu depth.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $item, $depth, $args = array(), $id = 0 ) {
		$current = array_filter( (array) get_post_meta( $item->ID, 'menu-icons', true ) );
		?>
			<div class="field-icon description-wide menu-icons-wrap">
				<?php
					/**
					 * Allow plugins/themes to inject HTML before menu icons' fields
					 *
					 * @param object $item  Menu item data object.
					 * @param int    $depth Nav menu depth.
					 * @param array  $args  Menu item args.
					 * @param int    $id    Nav menu ID.
					 *
					 */
					do_action( 'menu_icons_before_fields', $item, $depth, $args, $id );
				?>
				<?php
					$input_id   = sprintf( 'menu-icons-%d-type', $item->ID );
					$input_name = sprintf( 'menu-icons[%d][type]', $item->ID );
				?>
				<p class="description">
					<label for="<?php echo esc_attr( $input_id ) ?>"><?php esc_html_e( 'Icon type', 'menu-icons' ); ?></label>
					<?php printf(
						'<select id="%s" name="%s" class="hasdep" data-dep-scope="div.menu-icons-wrap" data-dep-children=".field-icon-child">',
						esc_attr( $input_id ),
						esc_attr( $input_name )
					) ?>
						<?php foreach ( self::_get_types() as $id => $props ) : ?>
							<?php printf(
								'<option value="%s"%s>%s</option>',
								esc_attr( $id ),
								selected( ( isset( $current['type'] ) && $id === $current['type'] ), true, false ),
								esc_html( $props['label'] )
							) ?>
						<?php endforeach; ?>
					</select>
				</p>
				<?php foreach ( self::_get_types() as $props ) : ?>
					<?php if ( ! empty( $props['field_cb'] ) && is_callable( $props['field_cb'] ) ) : ?>
						<?php call_user_func_array( $props['field_cb'], array( $item->ID, $current ) ); ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php
					/**
					 * Allow plugins/themes to inject HTML after menu icons' fields
					 *
					 * @param object $item  Menu item data object.
					 * @param int    $depth Nav menu depth.
					 * @param array  $args  Menu item args.
					 * @param int    $id    Nav menu ID.
					 *
					 */
					do_action( 'menu_icons_after_fields', $item, $depth, $args, $id );
				?>
			</div>
		<?php
	}


	/**
	 * Add our field to the screen options toggle
	 *
	 * @since   0.1.0
	 * @access  private
	 * @wp_hook action manage_nav-menus_columns
	 * @link    http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_posts_columns Action: manage_nav-menus_columns/99
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public static function _columns( $columns ) {
		$columns['icon'] = __( 'Icon', 'menu-icons' );

		return $columns;
	}


	/**
	 * Save menu item's icons values
	 *
	 * @since  0.1.0
	 * @access protected
	 * @uses   apply_filters() Calls 'menu_icons_values' on returned array.
	 * @link   http://codex.wordpress.org/Plugin_API/Action_Reference/wp_update_nav_menu_item Action: wp_update_nav_menu_item/10/2
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Sanitize
		if ( ! empty( $_POST['menu-icons'][ $menu_item_db_id ] ) ) {
			$value = array_filter( (array) $_POST['menu-icons'][ $menu_item_db_id ] );
		}
		else {
			$value = array();
		}

		/**
		 * Allow plugins/themes to filter the values
		 *
		 * @since 0.1.0
		 * @param array $value Metadata value
		 */
		$value = apply_filters( 'menu_icons_values', $value, $menu_item_db_id );

		// Update
		if ( ! empty( $value ) ) {
			update_post_meta( $menu_item_db_id, 'menu-icons', $value );
		}
		else {
			delete_post_meta( $menu_item_db_id, 'menu-icons' );
		}
	}
}
