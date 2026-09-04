<?php

/**
 * Test class ThemeIsle MenuIcons.
 *
 * @package     ThemeIsle
 * @subpackage  Tests
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */
class Test_MenuIcons extends WP_Ajax_UnitTestCase {

	/**
	 * Generic test.
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 */
	public function test_generic() {
		$this->assertTrue( true );
	}

	/**
	 * Custom SVG icons expose padding and render it as a pixel style.
	 */
	public function test_svg_padding_setting_and_style() {
		if ( ! class_exists( 'Menu_Icons_Picker' ) ) {
			require_once dirname( __DIR__ ) . '/includes/picker.php';
		}

		$fields = Menu_Icons_Settings::get_settings_fields();

		$this->assertArrayHasKey( 'svg_padding', $fields );
		$this->assertSame( '', $fields['svg_padding']['default'] );
		$this->assertSame( '0', $fields['svg_padding']['placeholder'] );
		$this->assertSame( 'px', $fields['svg_padding']['description'] );

		$props = Menu_Icons_Picker::_add_extra_type_props_data(
			array(
				'controller' => 'Svg',
				'data'       => array(),
			),
			'svg',
			null
		);

		$this->assertContains( 'svg_padding', $props['data']['settingsFields'] );
		$this->assertSame(
			' style="padding:4px;box-sizing:border-box;"',
			Menu_Icons_Front_End::get_icon_style( array( 'svg_padding' => '4' ), array( 'svg_padding' ) )
		);
		$this->assertSame(
			'',
			Menu_Icons_Front_End::get_icon_style( array(), array( 'svg_padding' ) )
		);
		$this->assertSame(
			' style="padding:0px;box-sizing:border-box;"',
			Menu_Icons_Front_End::get_icon_style( array( 'svg_padding' => '0' ), array( 'svg_padding' ) )
		);
		$this->assertSame(
			'',
			Menu_Icons_Front_End::get_icon_style( array( 'svg_padding' => '' ), array( 'svg_padding' ) )
		);
	}

	/**
	 * Stored menu metadata cannot add arbitrary CSS declarations.
	 */
	public function test_rejects_unsafe_icon_style_values() {
		$unsafe_value = '0;position:fixed;background:url(https://example.com/tracker.png)';

		$this->assertSame(
			'',
			Menu_Icons_Front_End::get_icon_style(
				array(
					'font_size'      => $unsafe_value,
					'svg_width'      => $unsafe_value,
					'svg_padding'    => $unsafe_value,
					'vertical_align' => $unsafe_value,
				),
				array( 'font_size', 'svg_width', 'svg_padding', 'vertical_align' )
			)
		);
	}
}
