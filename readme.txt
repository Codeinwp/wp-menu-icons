=== Menu Icons by ThemeIsle ===
Contributors: codeinwp, themeisle
Tags: menu, nav-menu, icons, navigation
Requires at least: 4.7
Tested up to: 6.5
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Spice up your navigation menus with pretty icons, easily.


== Description ==

This plugin gives you the ability to add icons to your menu items, similar to the look of the latest dashboard menu.

https://www.youtube.com/watch?v=YcSotWXIczI

= Usage =
1. After the plugin is activated, go to *Appearance* > *Menus* to edit your menus
1. Enable/Disable icon types in "Menu Icons Settings" meta box
1. Set default settings for current nav menu; these settings will be inherited by the newly added menu items
1. Select icon by clicking on the "Select icon" link
1. Save the menu

= Supported icon types =
- Dashicons (WordPress core icons)
- [Elusive Icons](http://shoestrap.org/downloads/elusive-icons-webfont/) by [Aristeides Stathopoulos](http://shoestrap.org/blog/author/aristath/)
- [Font Awesome](http://fontawesome.io/) by [Dave Gandy](http://twitter.com/davegandy)
- [Foundation Icons](http://zurb.com/playground/foundation-icon-fonts-3/) by [Zurb](http://zurb.com/)
- [Genericons](http://genericons.com/) by [Automattic](http://automattic.com/)
- [Fontello](http://fontello.com/) icon packs
- [TI Icons](http://themeisle.com/free-icons/) icon pack by [ThemeIsle](http://twitter.com/themeisle)
- Image (attachments)
- SVG (attachments)

= Planned supported icon types =
- Image (URL)

= Extensions =
- [IcoMoon](http://wordpress.org/plugins/menu-icons-icomoon/) by [IcoMoon.io](http://icomoon.io/)

= Compatible themes =
Menu icons works with most of the themes out there, especially with popular ones like Twenty Seventeen or [Hestia](https://themeisle.com/themes/hestia/).

Development of this plugin is done on [GitHub](https://github.com/codeinwp/wp-menu-icons). **Pull requests welcome**. Please see [issues reported](https://github.com/codeinwp/wp-menu-icons/issues) there before going to the plugin forum.

== If you like this plugin, then consider checking out our other projects: ==

[CodeinWP Blog](https://www.codeinwp.com/blog/) – Designer's Guide To WordPress
[Revive.Social](https://revive.social) – Social Media Tools
[JustFreeThemes](https://justfreethemes.com) - Free WordPress Themes directory




== Screenshots ==
1. Menu Editor
2. Icon selection
3. Twenty Fourteen with Dashicons
4. Twenty Fourteen with Genericons
5. Twenty Thirteen with Dashicons
6. Twenty Thirteen with Genericons
7. Settings Meta Box (Global)
8. Settings Meta Box (Menu)


== Installation ==

1. Upload `menu-icons` to the `/wp-content/plugins/` directory
1. Activate the plugin through the *Plugins* menu in WordPress


== Frequently Asked Questions ==

= The icons are not showing! =
Make sure that your active theme is using the default walker for displaying the nav menu. If it's using its own custom walker, make sure that the menu item titles are filterable (please consult your theme author about this).

= The icon positions don't look right =
If you're comfortable with editing your theme stylesheet, then you can override the styles from there.
If you have [Jetpack](http://wordpress.org/plugins/jetpack) installed, you can also use its **Custom CSS** module.
Otherwise, I recommend you to use the [Advanced CSS plugin](https://wordpress.org/plugins/advanced-css-editor/).

= Some font icons are not rendering correctly =
This is a bug with the font icon itself. When the font is updated, this plugin will update its font too.

= How do I use css file from CDN? =
You can use the `icon_picker_icon_type_stylesheet_uri` filter, eg:
`
/**
 * Load Font Awesome's CSS from CDN
 *
 * @param  string                $stylesheet_uri Icon type's stylesheet URI.
 * @param  string                $icon_type_id   Icon type's ID.
 * @param  Icon_Picker_Type_Font $icon_type      Icon type's instance.
 *
 * @return string
 */
function myprefix_font_awesome_css_from_cdn( $stylesheet_uri, $icon_type_id, $icon_type ) {
	if ( 'fa' === $icon_type_id ) {
		$stylesheet_uri = sprintf(
			'https://maxcdn.bootstrapcdn.com/font-awesome/%s/css/font-awesome.min.css',
			$icon_type->version
		);
	}

	return $stylesheet_uri;
}
add_filter( 'icon_picker_icon_type_stylesheet_uri', 'myprefix_font_awesome_css_from_cdn', 10, 3 );
`

= Is this plugin extendable? =
**Certainly!** Here's how you can remove an icon type from your plugin/theme:
`
/**
 * Remove one or more icon types
 *
 * Uncomment one or more line to remove icon types
 *
 * @param  array $types Registered icon types.
 * @return array
 */
function my_remove_menu_icons_type( $types ) {
	// Dashicons
	//unset( $types['dashicons'] );

	// Elusive
	//unset( $types['elusive'] );

	// Font Awesome
	//unset( $types['fa'] );

	// Foundation
	//unset( $types['foundation-icons'] );

	// Genericons
	//unset( $types['genericon'] );

	// Image
	//unset( $types['image'] );

	return $types;
}
add_filter( 'menu_icons_types', 'my_remove_menu_icons_type' );
`

To add a new icon type, take a look at the files inside the `includes/library/icon-picker/includes/types` directory of this plugin.

= I don't want the settings meta box. How do I remove/disable it? =
Add this block of code to your [mu-plugin file](http://codex.wordpress.org/Must_Use_Plugins):
`
add_filter( 'menu_icons_disable_settings', '__return_true' );
`

= How can I change the CSS class for hiding the menu item labels? =
Add this block of code to your [mu-plugin file](http://codex.wordpress.org/Must_Use_Plugins):
`
/**
 * Override hidden label class
 *
 * @param  string $class Hidden label class.
 * @return string
 */
function my_menu_icons_hidden_label_class( $class ) {
	$class = 'hidden';

	return $class;
}
add_filter( 'menu_icons_hidden_label_class', 'my_menu_icons_hidden_label_class' );
`

= How can I modify the markup the menu items? =
Add this block of code to your [mu-plugin file](http://codex.wordpress.org/Must_Use_Plugins):
`
/**
 * Override menu item markup
 *
 * @param string  $markup  Menu item title markup.
 * @param integer $id      Menu item ID.
 * @param array   $meta    Menu item meta values.
 * @param string  $title   Menu item title.
 *
 * @return string
 */
function my_menu_icons_override_markup( $markup, $id, $meta, $title ) {
	// Do your thing.

	return $markup;
}
add_filter( 'menu_icons_item_title', 'my_menu_icons_override_markup', 10, 4 );
`

= Can you please add X icon font? =
Let me know via [GitHub issues](https://github.com/codeinw/wp-menu-icons/issues) and I'll see what I can do.

= How do I disable menu icons for a certain menu? =
Add this block of code to your [mu-plugin file](http://codex.wordpress.org/Must_Use_Plugins):
`
/**
 * Disable menu icons for a menu
 *
 * @param array $menu_settings Menu Settings.
 * @param int   $menu_id       Menu ID.
 *
 * @return array
 */
function my_menu_icons_menu_settings( $menu_settings, $menu_id ) {
	if ( 13 === $menu_id ) {
		$menu_settings['disabled'] = true;
	}

	return $menu_settings;
}
add_filter( 'menu_icons_menu_settings', 'my_menu_icons_menu_settings', 10, 2 );
`

= How do I add an icon pack from Fontello? =
1. Create a new directory called `fontpacks` in `wp-content`.
1. Grab the zip of the pack, extract, and upload it to the newly created directory.
1. Enable the icon type from the Settings meta box.


= I can't select a custom image size from the *Image Size* dropdown =
Read [this blog post](http://kucrut.org/add-custom-image-sizes-right-way/).

== Changelog ==

##### [Version 0.13.14](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.13...v0.13.14) (2024-05-14)

- Enhanced security




##### [Version 0.13.13](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.12...v0.13.13) (2024-04-18)

### Improvements
​- **Updated internal dependencies:​​** Enhanced performance and security.




##### [Version 0.13.12](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.11...v0.13.12) (2024-04-01)

### Improvements
- **Updated internal dependencies**




##### [Version 0.13.11](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.10...v0.13.11) (2024-03-29)

### Fixes
- Updated internal dependencies
- Enhanced security




##### [Version 0.13.10](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.9...v0.13.10) (2024-03-26)

### Improvements
- Updated internal dependencies
- Improved readme to link to the public source files
- Filter promotions




##### [Version 0.13.9](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.8...v0.13.9) (2024-02-23)

### Fixes
- Updated dependencies
- Harden security




##### [Version 0.13.8](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.7...v0.13.8) (2023-12-19)

- SDK Updates
- Fixed global hide label issue




##### [Version 0.13.7](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.6...v0.13.7) (2023-08-17)

- Updated dependencies
- Fixed broken button layout issues in other languages




##### [Version 0.13.6](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.5...v0.13.6) (2023-07-07)

- Updated composer dependencies to address warning in the widgets section




##### [Version 0.13.5](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.4...v0.13.5) (2023-03-30)

- Updated Dependencies and WordPress core tested up to version 6.2




##### [Version 0.13.4](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.3...v0.13.4) (2023-03-01)

Update dependencies




##### [Version 0.13.3](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.2...v0.13.3) (2023-02-25)

- Fix img width/height value, props @Htbaa 
- Update dependencies




##### [Version 0.13.2](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.1...v0.13.2) (2022-11-24)

* Fix - update dependencies




##### [Version 0.13.1](https://github.com/codeinwp/wp-menu-icons/compare/v0.13.0...v0.13.1) (2022-11-04)

Tested with the WordPress 6.1 version




#### [Version 0.13.0](https://github.com/codeinwp/wp-menu-icons/compare/v0.12.12...v0.13.0) (2022-08-23)

* Fix Neve upsells showing up inconsistently on edge cases
* Fix inconsistency with Font Awesome 5 and adds compatibility with 6th version
* Fix compatibility with JupiterX 
* Update dependencies




##### [Version 0.12.12](https://github.com/codeinwp/wp-menu-icons/compare/v0.12.11...v0.12.12) (2022-05-27)

- Fix the style handler conflict issue which breaks the arrow icon of the submenus on some themes
- Fix dismiss dashboard notice issue on some edge cases
- Fix compatibility with the Max Mega Menu plugin




##### [Version 0.12.11](https://github.com/codeinwp/wp-menu-icons/compare/v0.12.10...v0.12.11) (2022-03-16)

Add font awesome 5 support
Enhance compatibility with Otter/Neve




##### [Version 0.12.10](https://github.com/codeinwp/wp-menu-icons/compare/v0.12.9...v0.12.10) (2022-02-07)

- [Fix] Add support for alt attribute for SVG icons
- Tested up with WordPress 5.9




##### [Version 0.12.9](https://github.com/codeinwp/wp-menu-icons/compare/v0.12.8...v0.12.9) (2021-08-04)

* Tested compatibility with WordPress 5.8




##### [Version 0.12.8](https://github.com/codeinwp/wp-menu-icons/compare/v0.12.7...v0.12.8) (2021-05-12)

* Fix issue when the image is not accessible to fetch the width/height metadata.




##### [Version 0.12.7](https://github.com/codeinwp/wp-menu-icons/compare/v0.12.6...v0.12.7) (2021-05-07)

Fix PHP fatal error when uploading SVG with the image uploader




##### [Version 0.12.6](https://github.com/codeinwp/wp-menu-icons/compare/v0.12.5...v0.12.6) (2021-05-05)

* Adds explicit width/height to icons to prevent layout shifts issues


= 0.12.4 - 2020-07-13  =

* Fix Font Awesome not loading


= 0.12.3 - 2020-07-13  =

* Fixed Menu Icons in Block Editor not working
* Fixed CWP links.


= 0.12.2 - 2019-11-15  =



= 0.12.1 - 2019-11-15  =

* Improve legacy compatibility


= 0.12.0 - 2019-11-15  =

* Fix issues with WordPress 5.3.


= 0.11.5 - 2019-05-23  =

* Sync composer dependencies with the latest version


= 0.11.4 - 2018-12-10  =

* fix issue with composer libraries.


= 0.11.3 - 2018-12-10  =

* Tested with WP 5.0


= 0.11.2 - 2018-03-05  =

* Improve popup sidebar layout.


= 0.11.1 - 2018-02-24  =

* Add recommendation boxes in the menu icon popup.


= 0.11.0 - 2018-01-05  =

* Change ownership to ThemeIsle.
* Improves compatibility with various ThemeIsle products.


= 0.10.2 =
* Compatibility with WordPress 4.7.0, props [Aaron K](https://github.com/aaronkirkham).

= 0.10.1 =
* Support RTL, props [ybspost](https://wordpress.org/support/profile/ybspost).

= 0.10.0 =
* Icon Picker 0.4.0
  * Font Awesome 4.6.1
  * Introduce `icon_picker_icon_type_stylesheet_uri` filter hook.
* Add `aria-hidden="true"` attribute to icon element

= 0.9.3 =
* Fix CSS conflicts

= 0.9.2 =
* Update Icon Picker to [0.1.1](https://github.com/kucrut/wp-icon-picker/releases/tag/v0.1.1).

= 0.9.1 =
* Fix support for Composer.

= 0.9.0 =
* Performance optimization.
* Modularisation. Developers: Take a look at the [Icon Picker](https://github.com/kucrut/wp-icon-picker) library.
* Bug fixes.
* Removed `menu_icons_{type_id}_props` filter.

= 0.8.1 =
* Fix disappearing icons from front-end when not logged-in, props [jj9617](http://profiles.wordpress.org/jj9617/)

= 0.8.0 =
* Update Dashicons
* Update Genericons to 3.4
* Update Font Awesome to 4.4.0
* Allow the plugin to be disabled for a certain menu
* Add new icon type: SVG, props [Ethan Clevenger](https://github.com/ethanclevenger91)
* Add new filter: `menu_icons_hidden_label_class`
* Add new filter: `menu_icons_item_title`

= 0.7.0 =
* Update Dashicons
* Fix annoying browser popup when navigating away from Nav Menus screen
* Work-around settings update with ajax

= 0.6.0 =
* Update Genericons to [3.2](http://genericons.com/2014/10/03/3-2/)
* Update Font Awesome to [4.2.0](http://fontawesome.io/whats-new/)

= 0.5.1 =
* Update Menu Item Custom Fields to play nice with other plugins.
* Add missing Foundation Icons stylesheet, props [John](http://wordpress.org/support/profile/dsl225)
* JS & CSS fixes

= 0.5.0 =
* New Icon type: Foundation Icons
* Add new Dashicons icons
* Various fixes & enhancements

= 0.4.0 =
* Fontello icon packs support
* New icon type: Image (attachments)

= 0.3.2 =
* Add missing minified CSS for Elusive font icon, props [zazou83](http://profiles.wordpress.org/zazou83)

= 0.3.1 =
* Fix fatal error on outdated PHP versions, props [dellos](http://profiles.wordpress.org/dellos)

= 0.3.0 =
* Add Settings meta box on Menu screen
* New feature: Settings inheritance (nav menu > menu items)
* New feature: Hide menu item labels
* New Icon type: Elusive Icons
* Update Font Awesome to 4.1.0

= 0.2.3 =
* Add new group for Dashicons: Media

= 0.2.1 =
* Fix icon selector compatibility with WP 3.9

= 0.2.0 =
* Media frame for icon selection
* New font icon: Font Awesome

= 0.1.5 =
* Invisible, but important fixes and improvements

= 0.1.4 =
* Fix menu saving

= 0.1.3 =
* Provide icon selection fields on newly added menu items

= 0.1.2 =
* Improve extra stylesheet

= 0.1.1 =
* Improve icon selection UX

= 0.1.0 =
* Initial public release
