<?php
/**
 * Font Packs
 *
 * @package Menu_Icons
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 * @author Joshua F. Rountree <joshua@swodev.com>
 */


require_once dirname( __FILE__ ) . '/type-fonts.php';

/**
 * Icon type: Font Packs
 *
 * @version 0.1.0
 */
class Menu_Icons_Type_Fontpack extends Menu_Icons_Type_Fonts {

	/**
	 * Holds icon type
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $type = '';

	/**
	 * Holds icon label
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $label = '';

	/**
	 * Holds icon version
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '';

	/**
	 * Holds fontpack dir
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $dir = '';

	/**
	 * Holds fontpack url path
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $url = '';

	/**
	 * Holds error messages
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $messages = array();

	/**
	 * Holds config array
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $config = array();

	/**
	 * Holds config validation status
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    bool
	 */
	protected $is_config_valid = false;

	/**
	 * Holds icon names
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $icons = array();

	/**
	 * Class constructor
	 *
	 * We need to override the parent's to set our stylesheet URL
	 *
	 * @since 0.1.0
	 * @param array $types Icon Types
	 * @return array
	 */
	public function __construct( $pack ) {
		$this->messages = array(
			'no_config' => __( 'Menu Icons: %1$s was not found in %2$s.', 'menu-icons' ),
			'invalid'   => __( 'Menu Icons: %1$s is not set or invalid in %2$s.', 'menu-icons' ),
			'duplicate' => __( 'Menu Icons: %1$s is already registered. Please check your font pack config file: %2$s.', 'menu-icons' ),
		);

		$this->dir = sprintf( '%s/%s', Menu_Icons::get( 'fontpacks_dir_path' ), $pack );
		$this->url = sprintf( '%s/%s', Menu_Icons::get( 'fontpacks_dir_url' ), $pack );

		if ( ! is_readable( $this->dir . '/config.json' ) ) {
			trigger_error(
				sprintf(
					esc_html( $this->messages['no_config'] ),
					'<code><em>config.json</em></code>',
					sprintf( '<code>%s</code>', esc_html( $this->dir ) )
				)
			);

			return;
		}

		$this->read_config();
		$this->validate();

		if ( false === $this->is_config_valid ) {
			return;
		}

		$this->set_properties();

		parent::__construct();
	}


	/**
	 * Read in config and store for later.
	 *
	 * @since 0.1.0
	 * @access protected
	 */
	protected function read_config() {
		$config_path  = $this->dir . '/config.json';
		$config_json  = file_get_contents( $config_path );
		$this->config = json_decode( $config_json, true );
	}


	/**
	 * Validate config file
	 *
	 * @since 0.1.0
	 * @access protected
	 */
	protected function validate() {
		$keys = array( 'name', 'glyphs', 'css_prefix_text' );

		foreach ( $keys as $key ) {
			if ( empty( $this->config[ $key ] ) ) {
				trigger_error(
					sprintf(
						esc_html( $this->messages['invalid'] ),
						sprintf( '<code><em>%s</em></code>', esc_html( $key ) ),
						sprintf( '<code>%s/config.json</code>', esc_html( $this->dir ) )
					)
				);

				return;
			}
		}

		// Validate & get all glyphs
		if ( ! is_array( $this->config['glyphs'] ) ) {
			return;
		}

		$icons = array();
		foreach ( $this->config['glyphs'] as $glyph ) {
			if ( ! empty( $glyph['css'] ) ) {
				$class = $this->config['css_prefix_text'] . $glyph['css'];
				$label = $glyph['css'];

				$icons[ $class ] = $label;
			}
		}

		if ( empty( $icons ) ) {
			return;
		}

		$this->icons           = $icons;
		$this->is_config_valid = true;
	}


	/**
	 * Set class properties
	 *
	 * @since 0.1.0
	 * @access protected
	 */
	protected function set_properties() {
		$this->type       = sprintf( 'pack-%s', $this->config['name'] );
		$this->label      = sprintf( __( 'Pack: %s', 'menu-icons' ), $this->config['name'] );
		$this->stylesheet = sprintf( '%s/css/%s.css', $this->url, $this->config['name'] );

		if ( ! empty( $this->config['version'] ) ) {
			$this->version = $this->config['version'];
		} else {
			$this->version = filemtime( sprintf( '%s/css/%s.css', $this->dir, $this->config['name'] ) );
		}
	}


	/**
	 * Register our type
	 *
	 * @since  0.1.0
	 * @param  array $types Icon Types
	 * @return array
	 */
	public function register( $icon_types ) {
		if ( true !== $this->is_config_valid ) {
			return $icon_types;
		}

		// Check for duplicate packs
		if ( isset( $icon_types[ $this->type ] ) ) {
			trigger_error(
				sprintf(
					esc_html( $this->messages['duplicate'] ),
					sprintf( '<strong>%s</strong>', esc_html( $this->config['name'] ) ),
					sprintf( '<code><em>%s/config.json</em></code>', esc_html( $this->dir ) )
				)
			);

			return $icon_types;
		}

		$icon_types = parent::register( $icon_types );

		return $icon_types;
	}


	/**
	 * Read fontpacks directory for config.json's and find icons names
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function get_names() {
		$glyphs = $this->config['glyphs'];
		$names  = array(
			'key'   => 'all',
			'label' => __( 'All', 'menu-icons' ),
			'items' => $this->icons,
		);

		return array( $names );
	}
}
