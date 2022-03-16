<?php

/**
 * Menu Icons
 *
 * @package Menu_Icons
 * @version 0.10.2
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 *
 *
 * Plugin name: Menu Icons
 * Plugin URI:  https://github.com/Codeinwp/wp-menu-icons
 * Description: Spice up your navigation menus with pretty icons, easily.
 * Version:     0.12.11
 * Author:      ThemeIsle
 * Author URI:  https://themeisle.com
 * License:     GPLv2
 * Text Domain: menu-icons
 * Domain Path: /languages
 * WordPress Available:  yes
 * Requires License:    no
 */


/**
 * Main plugin class
 */
final class Menu_Icons {

	const DISMISS_NOTICE = 'menu-icons-dismiss-notice';

	const VERSION = '0.12.11';

	/**
	 * Holds plugin data
	 *
	 * @access protected
	 * @since  0.1.0
	 * @var    array
	 */
	protected static $data;


	/**
	 * Get plugin data
	 *
	 * @since  0.1.0
	 * @since  0.9.0  Return NULL if $name is not set in $data.
	 * @param  string $name
	 *
	 * @return mixed
	 */
	public static function get( $name = null ) {
		if ( is_null( $name ) ) {
			return self::$data;
		}

		if ( isset( self::$data[ $name ] ) ) {
			return self::$data[ $name ];
		}

		return null;
	}


	/**
	 * Load plugin
	 *
	 * 1. Load translation
	 * 2. Set plugin data (directory and URL paths)
	 * 3. Attach plugin initialization at icon_picker_init hook
	 *
	 * @since   0.1.0
	 * @wp_hook action plugins_loaded
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded
	 */
	public static function _load() {
		load_plugin_textdomain( 'menu-icons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		self::$data = array(
			'dir'   => plugin_dir_path( __FILE__ ),
			'url'   => plugin_dir_url( __FILE__ ),
			'types' => array(),
		);

		Icon_Picker::instance();

		require_once self::$data['dir'] . 'includes/library/compat.php';
		require_once self::$data['dir'] . 'includes/library/functions.php';
		require_once self::$data['dir'] . 'includes/meta.php';

		Menu_Icons_Meta::init();

		// Font awesome 5 backward compatible functionalities.
		require_once self::$data['dir'] . 'includes/library/font-awesome5/backward-compatible-icons.php';
		require_once self::$data['dir'] . 'includes/library/font-awesome5/font-awesome.php';
		Menu_Icons_Font_Awesome::init();

		add_action( 'icon_picker_init', array( __CLASS__, '_init' ), 9 );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, '_admin_enqueue_scripts' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, '_wp_menu_icons_dashboard_notice' ) );
		add_action( 'wp_ajax_wp_menu_icons_dismiss_dashboard_notice', array( __CLASS__, 'wp_menu_icons_dismiss_dashboard_notice' ) );
	}


	/**
	 * Initialize
	 *
	 * 1. Get registered types from Icon Picker
	 * 2. Load settings
	 * 3. Load front-end functionalities
	 *
	 * @since   0.1.0
	 * @since   0.9.0  Hook into `icon_picker_init`.
	 * @wp_hook action icon_picker_init
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference
	 */
	public static function _init() {
		/**
		 * Allow themes/plugins to add/remove icon types
		 *
		 * @since 0.1.0
		 * @param array $types Icon types
		 */
		self::$data['types'] = apply_filters(
			'menu_icons_types',
			Icon_Picker_Types_Registry::instance()->types
		);

		// Nothing to do if there are no icon types registered.
		if ( empty( self::$data['types'] ) ) {
			if ( WP_DEBUG ) {
				trigger_error( esc_html__( 'Menu Icons: No registered icon types found.', 'menu-icons' ) );
			}

			return;
		}

		// Load settings.
		require_once self::$data['dir'] . 'includes/settings.php';
		Menu_Icons_Settings::init();

		// Load front-end functionalities.
		if ( ! is_admin() ) {
			require_once self::$data['dir'] . '/includes/front.php';
			Menu_Icons_Front_End::init();
		}

		do_action( 'menu_icons_loaded' );
	}


	/**
	 * Display notice about missing Icon Picker
	 *
	 * @since   0.9.1
	 * @wp_hook action admin_notice
	 */
	public static function _notice_missing_icon_picker() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'Looks like Menu Icons was installed via Composer. Please activate Icon Picker first.', 'menu-icons' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Register assets.
	 */
	public static function _admin_enqueue_scripts() {
		$url    = self::get( 'url' );
		$suffix = kucrut_get_script_suffix();

		if ( defined( 'MENU_ICONS_SCRIPT_DEBUG' ) && MENU_ICONS_SCRIPT_DEBUG ) {
			$script_url = '//localhost:8081/';
		} else {
			$script_url = $url;
		}

		wp_register_style(
			'menu-icons-dashboard',
			"{$url}css/dashboard-notice{$suffix}.css",
			false,
			self::VERSION
		);

		wp_register_script(
			'menu-icons-dashboard',
			"{$script_url}js/dashboard-notice{$suffix}.js",
			array( 'jquery' ),
			self::VERSION,
			true
		);

		wp_localize_script(
			'menu-icons-dashboard',
			'menuIcons',
			array(
				'ajaxUrls' => admin_url( 'admin-ajax.php' ),
				'_nonce'   => wp_create_nonce( self::DISMISS_NOTICE ),
			)
		);
	}

	/**
	 * Render dashboard notice.
	 */
	public static function _wp_menu_icons_dashboard_notice() {
		if ( false === get_transient( self::DISMISS_NOTICE ) ) {
			wp_enqueue_style( 'menu-icons-dashboard' );
			wp_enqueue_script( 'menu-icons-dashboard' );
			add_action( 'admin_notices', array( __CLASS__, '_upsell_admin_notice' ) );
		}
	}

	/**
	 * Ajax request handle for dissmiss dashboard notice.
	 */
	public static function wp_menu_icons_dismiss_dashboard_notice() {
		check_ajax_referer( self::DISMISS_NOTICE, '_nonce' );

		$dismiss = ! empty( $_POST['dismiss'] ) ? intval( $_POST['dismiss'] ) : 0;
		set_transient( self::DISMISS_NOTICE, $dismiss, 365 * DAY_IN_SECONDS );

		wp_send_json_success(
			array(
				'status' => 0,
			)
		);
		die();
	}

	/**
	 * Upsell admin notice.
	 */
	public static function _upsell_admin_notice() {
		$neve_theme_url = add_query_arg(
			array(
				'theme' => 'neve',
			),
			admin_url( 'theme-install.php' )
		);
		?>
		<div class="notice notice-info is-dismissible menu-icon-dashboard-notice">
			<h2><?php esc_html_e( 'Thank you for installing Menu Icons!', 'menu-icons' ); ?></h2>
			<p><?php esc_html_e( 'Have you heard about our latest FREE theme - Neve? Using a mobile-first approach, compatibility with AMP and popular page-builders, Neve makes website building accessible for everyone.', 'menu-icons' ); ?></p>
			<a href="<?php echo esc_url( $neve_theme_url ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Preview Neve', 'menu-icons' ); ?></a>
		</div>
		<?php
	}
}
add_action( 'plugins_loaded', array( 'Menu_Icons', '_load' ) );

$vendor_file = dirname(__FILE__) . '/vendor/autoload.php';

if ( is_readable( $vendor_file ) ) {
	require_once $vendor_file;
}

add_filter( 'themeisle_sdk_products', 'kucrut_register_sdk', 10, 1 );
function kucrut_register_sdk( $products ) {

	$products[] = __FILE__;
	return $products;
}
