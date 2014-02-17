<?php
/**
 * Menu editor handler
 * @package Menu_Icons
 * @version 0.1.0
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
		add_action( 'menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
	}


	/**
	 * Save menu item's icons values
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @uses    apply_filters() Calls 'menu_icons_values' on returned array.
	 * @wp_hook action          wp_update_nav_menu_item
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


	/**
	 * Print fields
	 *
	 * @since  0.1.0
	 * @access protected
	 * @uses   add_action() Calls 'menu_icons_before_fields' hook
	 * @uses   add_action() Calls 'menu_icons_after_fields' hook
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
					 * Allow plugin/themes to inject HTML before menu icons' fields
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
					<select id="<?php echo esc_attr( $input_id ) ?>" name="<?php echo esc_attr( $input_name ) ?>">
						<?php foreach ( self::_get_types() as $value => $props ) : ?>
							<?php printf(
								'<option value="%s"%s>%s</option>',
								esc_attr( $value ),
								selected( ( isset( $current['type'] ) && $value === $current['type'] ), true, false ),
								esc_html( $props['label'] )
							) ?>
						<?php endforeach; ?>
					</select>
				</p>
				<?php foreach ( self::_get_types() as $value => $props ) : ?>
					<?php if ( ! empty( $props['require'] ) && is_readable( $props['require'] ) ) : ?>
						<?php require_once $props['require']; ?>
					<?php endif; ?>
					<?php if ( ! empty( $props['field_cb'] ) && is_callable( $props['field_cb'] ) ) : ?>
						<?php call_user_func_array( $props['field_cb'], array( $item->ID, $current ) ); ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php
					/**
					 * Allow plugin/themes to inject HTML after menu icons' fields
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
			array( '' => array( 'label' => __( '&mdash; Select &mdash;', 'menu-icons' ) ) ),
			Menu_Icons::get_icon_types()
		);

		return $types;
	}


	/**
	 * Add our field to the screen options toggle
	 *
	 * @since   0.1.0
	 * @access  private
	 * @wp_hook action manage_nav-menus_columns
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public static function _columns( $columns ) {
		$columns['icon'] = __( 'Icon', 'my-plugin' );

		return $columns;
	}
}
