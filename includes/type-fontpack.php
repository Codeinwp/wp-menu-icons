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
	 * @since  0.3.2
	 * @access protected
	 * @var    string
	 */
	protected $path = '';

	/**
	 * Holds config array
	 *
	 * @since  0.3.2
	 * @access protected
	 * @var    array
	 */
	protected $config = array();
	
	/**
	 * Class constructor
	 *
	 * We need to override the parent's to set our stylesheet URL
	 *
	 * @since 0.1.0
	 * @param array $types Icon Types
	 * @return array
	 */
	public function __construct($pack_folder_name) {
		$this->path = dirname(dirname( __FILE__ )) . '/fontpacks/' . $pack_folder_name;
		
		//read in config.json
		$this->read_config();
		$this->type = $this->config['name'];
		$this->label = $this->config['name'];
		$this->version = '0.0.1'; //need to be able to pull version from somewhere... possibly fontello later?
		//read_config();
		$this->stylesheet = sprintf(
			'%sfontpacks/%s/css/%s.css',
			Menu_Icons::get( 'url' ),
			$pack_folder_name,
			$this->config['name']
		);

		parent::__construct();
	}

	/**
	 * Read in config and store for later.
	 * @since 0.3.3
	 * @return boolean
	 */
	private function read_config() {
		$config_path = $this->path . '/config.json';
		$config_json = file_get_contents($config_path);
		$this->config = json_decode($config_json, TRUE);
	}
	/*
	 * Read fontpacks directory for config.json's and find icons names
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function get_names() {
		$response = array();
		$pack_glyphs = $this->config['glyphs'];
		$fontpackItem = array(
			'key'   => __( $pack_name, 'menu-icons' ),
			'label' => __( $pack_name, 'menu-icons' ),
			'items' => array(),
		);

		foreach ($pack_glyphs as $key => $val) {
			$icon_class_name = $this->config['css_prefix_text'] . $val['css'];
			$icon_label = $val['css'];
			$fontpackItem['items'][$icon_class_name] = $icon_label;
		}

		$response[] = $fontpackItem;

		return $response;
	}
}
