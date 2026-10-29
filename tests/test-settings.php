<?php
/**
 * Settings tests.
 *
 * @package Menu_Icons
 */

/**
 * Menu Icons settings initialization.
 */
class Test_Menu_Icons_Settings extends WP_UnitTestCase {

	/**
	 * Restore the default settings for the following tests.
	 */
	public function tear_down(): void {
		delete_option( 'menu-icons' );
		Menu_Icons_Settings::init();

		parent::tear_down();
	}

	/**
	 * Global settings saved with every icon type unchecked load as no icon types.
	 */
	public function test_init_without_icon_types(): void {
		// Unchecked checkboxes post nothing, so only the text field is saved.
		update_option( 'menu-icons', array( 'global' => array( 'fa5_extra_icons' => '' ) ) );

		Menu_Icons_Settings::init();

		$this->assertSame( array(), Menu_Icons_Settings::get( 'global', 'icon_types' ) );
		$this->assertSame( '', Menu_Icons_Settings::get( 'global', 'fa5_extra_icons' ) );
	}
}
