<?php
/**
 * SVG icon handler
 *
 * @package Menu_Icons
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 * @author Ethan Clevenger <ethan.c.clevenger@gmail.com>
 */

require_once dirname( __FILE__ ) . '/type-image.php';

/**
 * SVG icons
 *
 */
class Menu_Icons_Type_SVG extends Menu_Icons_Type_Image {

	/**
	 * Holds icon type
	 *
	 * @since  0.8.0
	 * @access protected
	 * @var    string
	 */
	protected $type = 'svg';


	/**
	 * Class constructor
	 *
	 * This simply sets $label
	 *
	 * @since 0.4.0
	 */
	function __construct() {
		$this->label = __( 'SVG', 'menu-icons' );

		add_action( 'menu_icons_loaded', array( $this, '_menu_icons_loaded' ) );
		parent::__construct();
	}


	/**
	 * Perform actions after Menu Icons is fully loaded
	 *
	 * @since   0.8.0
	 * @wp_hook action menu_icons_loaded
	 * @return  void
	 */
	public function _menu_icons_loaded() {
		$active_types = Menu_Icons_Settings::get( 'global', 'icon_types' );

		if ( in_array( $this->type, $active_types ) ) {
			add_filter( 'upload_mimes', array( $this, '_add_mime_type' ) );
		}
	}


	/**
	 * Add SVG support
	 *
	 * @since   0.8.0
	 * @access  protected
	 * @wp_hook action          upload_mimes/10
	 * @link    https://codex.wordpress.org/Plugin_API/Filter_Reference/upload_mimes Action: upload_mimes/10
	 * @by      Ethan Clevenger (GitHub: ethanclevenger91; email: ethan.c.clevenger@gmail.com)
	 * @return  array
	 */
	public function _add_mime_type( array $mimes ) {
		if ( ! isset( $mimes['svg'] ) ) {
			$mimes['svg'] = 'image/svg+xml';
		}

		return $mimes;
	}


	/**
	 * Get icon markup
	 *
	 * @since 0.8.0
	 *
	 * @param integer $attachment_id Attachment ID.
	 * @param array   $meta_values   Menu item meta values.
	 * @param array   $args          Extra arguments.
	 *
	 * @return string
	 */
	protected function get_icon_markup( $attachment_id, array $meta_values, array $args = array() ) {
		return file_get_contents( get_attached_file( $attachment_id ) );
	}


	/**
	 * Media frame templates
	 *
	 * @since 0.4.0
	 * @return array
	 */
	public function templates() {
		$templates = parent::templates();

		$templates['field'] = '<img src="{{ data.url }}" alt="{{ data.alt }}" class="_icon" />';

		return $templates;
	}


	/**
	 * Media frame data
	 *
	 * @since  0.4.0
	 * @param  string $id Icon type ID
	 * @return array
	 */
	public function frame_cb( $id ) {
		$data = array(
			'controller' => 'miSvg',
			'library'    => array(
				'type' => array( 'image/svg', 'image/svg+xml' ),
			),
		);

		return $data;
	}
}
