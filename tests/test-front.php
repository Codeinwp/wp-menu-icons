<?php
/**
 * Front end tests.
 *
 * @package Menu_Icons
 */

/**
 * Menu Icons front end output.
 */
class Test_Menu_Icons_Front_End extends WP_UnitTestCase {

	/**
	 * Menu printed in the first location, with labels shown.
	 *
	 * @var int
	 */
	private $menu_shown = 0;

	/**
	 * Menu printed in the second location, set to hide labels.
	 *
	 * @var int
	 */
	private $menu_hidden = 0;

	/**
	 * Create two menus in their own locations, each with one item that has an icon.
	 */
	public function set_up(): void {
		parent::set_up();

		register_nav_menus(
			array(
				'mi-shown'  => 'Shown',
				'mi-hidden' => 'Hidden',
			)
		);

		$this->menu_shown  = $this->create_menu_with_icon_item( 'Labels shown' );
		$this->menu_hidden = $this->create_menu_with_icon_item( 'Labels hidden' );

		set_theme_mod(
			'nav_menu_locations',
			array(
				'mi-shown'  => $this->menu_shown,
				'mi-hidden' => $this->menu_hidden,
			)
		);

		update_option(
			'menu-icons',
			array(
				'global'                                   => array( 'icon_types' => array( 'dashicons' ) ),
				sprintf( 'menu_%d', $this->menu_hidden ) => array( 'hide_label' => '1' ),
			)
		);
		Menu_Icons_Settings::init();
	}

	/**
	 * Restore settings, locations and the current user.
	 */
	public function tear_down(): void {
		delete_option( 'menu-icons' );
		Menu_Icons_Settings::init();
		remove_theme_mod( 'nav_menu_locations' );
		unregister_nav_menu( 'mi-shown' );
		unregister_nav_menu( 'mi-hidden' );
		wp_set_current_user( 0 );

		parent::tear_down();
	}

	/**
	 * A menu's Hide Label setting applies to visitors, and only to that menu.
	 */
	public function test_menu_hide_label_applies_for_visitors(): void {
		wp_set_current_user( 0 );

		$this->assertStringContainsString( 'class="visuallyhidden"', $this->render( 'mi-hidden' ) );
		$this->assertStringNotContainsString( 'visuallyhidden', $this->render( 'mi-shown' ) );
	}

	/**
	 * The menu an admin last edited does not decide which menu hides its labels.
	 */
	public function test_menu_hide_label_ignores_last_edited_menu(): void {
		$admin = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin );

		update_user_option( $admin, 'nav_menu_recently_edited', $this->menu_shown );
		$this->assertStringContainsString( 'class="visuallyhidden"', $this->render( 'mi-hidden' ) );

		update_user_option( $admin, 'nav_menu_recently_edited', $this->menu_hidden );
		$this->assertStringNotContainsString( 'visuallyhidden', $this->render( 'mi-shown' ) );
	}

	/**
	 * Create a menu with one custom link that has a Dashicons icon.
	 *
	 * @param string $name Menu name.
	 *
	 * @return int Menu term ID.
	 */
	private function create_menu_with_icon_item( string $name ): int {
		$menu_id = wp_create_nav_menu( $name );
		$item_id = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $name . ' item',
				'menu-item-url'    => 'https://example.org/',
				'menu-item-status' => 'publish',
			)
		);

		Menu_Icons_Meta::update(
			$item_id,
			array(
				'type'           => 'dashicons',
				'icon'           => 'dashicons-admin-home',
				'hide_label'     => '',
				'position'       => 'before',
				'font_size'      => '1.2',
				'vertical_align' => 'middle',
			)
		);

		return $menu_id;
	}

	/**
	 * Render the menu assigned to a location.
	 *
	 * @param string $location Theme location.
	 *
	 * @return string Menu markup.
	 */
	private function render( string $location ): string {
		return (string) wp_nav_menu(
			array(
				'theme_location' => $location,
				'echo'           => false,
				'fallback_cb'    => false,
			)
		);
	}
}
