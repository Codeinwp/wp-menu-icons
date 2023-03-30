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
 * Version:     0.13.5
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

	const VERSION = '0.13.5';

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

		// Font awesome backward compatible functionalities.
		require_once self::$data['dir'] . 'includes/library/font-awesome/backward-compatible-icons.php';
		require_once self::$data['dir'] . 'includes/library/font-awesome/font-awesome.php';
		Menu_Icons_Font_Awesome::init();

		add_action( 'icon_picker_init', array( __CLASS__, '_init' ), 9 );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, '_admin_enqueue_scripts' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, '_wp_menu_icons_dashboard_notice' ) );
		add_action( 'admin_action_menu_icon_hide_notice', array( __CLASS__, 'wp_menu_icons_dismiss_dashboard_notice' ) );

		add_filter(
			'menu_icons_load_promotions',
			function() {
				return array( 'otter' );
			}
		);
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

		wp_register_style(
			'menu-icons-dashboard',
			"{$url}css/dashboard-notice{$suffix}.css",
			false,
			self::VERSION
		);
	}

	/**
	 * Render dashboard notice.
	 */
	public static function _wp_menu_icons_dashboard_notice() {
		$show_notice = true;
		if ( ! empty( get_option( self::DISMISS_NOTICE, false ) ) ) {
			$show_notice = false;
		}
		if ( ! empty( get_transient( self::DISMISS_NOTICE ) ) ) {
			$show_notice = false;
		}
		if ( $show_notice ) {
			wp_enqueue_style( 'menu-icons-dashboard' );
			add_action( 'admin_notices', array( __CLASS__, '_upsell_admin_notice' ) );
		}
	}

	/**
	 * Ajax request handle for dissmiss dashboard notice.
	 */
	public static function wp_menu_icons_dismiss_dashboard_notice() {
		// Verify WP nonce and store hide notice flag.
		if ( isset( $_GET['_wp_notice_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wp_notice_nonce'] ) ), self::DISMISS_NOTICE ) ) {
			update_option( self::DISMISS_NOTICE, 1 );
		}

		if ( ! headers_sent() ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
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

		$action_url = add_query_arg(
			array(
				'action'           => 'menu_icon_hide_notice',
				'_wp_notice_nonce' => wp_create_nonce( self::DISMISS_NOTICE ),
			),
			admin_url( 'index.php' )
		);
		?>
		<div class="notice notice-info menu-icon-dashboard-notice">
			<h2><?php esc_html_e( 'Thank you for installing Menu Icons!', 'menu-icons' ); ?></h2>
			<p><?php esc_html_e( 'Have you heard about our latest FREE theme - Neve? Using a mobile-first approach, compatibility with AMP and popular page-builders, Neve makes website building accessible for everyone.', 'menu-icons' ); ?></p>
			<a href="<?php echo esc_url( $neve_theme_url ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Preview Neve', 'menu-icons' ); ?></a>
			<a href="<?php echo esc_url( $action_url ); ?>" class="notice-dismiss"></a>
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
