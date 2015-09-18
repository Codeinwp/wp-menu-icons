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
	 * @since 0.8.0
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
	 * @param boolean $is_preview    For preview or front-end, default false.
	 *
	 * @return string
	 */
	protected function get_icon_markup( $attachment_id, array $meta_values, array $args = array(), $is_preview = false ) {
		if ( $is_preview ) {
			$style = 'width:1em;';
		} else {
			$style = sprintf(
				'width:%sem;vertical-align:%s',
				esc_attr( $meta_values['width'] ),
				esc_attr( $meta_values['vertical_align'] )
			);
		}

		return sprintf(
			'<img src="%s" class="%s" style="%s" />',
			esc_url( wp_get_attachment_url( $attachment_id ) ),
			esc_attr( $args['class'] ),
			$style
		);
	}


	/**
	 * Media frame templates
	 *
	 * @since 0.8.0
	 * @return array
	 */
	public function templates() {
		$icon         = '<img src="{{ data.url }}" alt="{{ data.alt }}" class="_icon _{{data.type}}"%s />';
		$icon_item    = sprintf( $icon, '' );
		$icon_preview = sprintf( $icon, ' style="width:{{data.width}}em;vertical-align:{{ data.vertical_align }}"' );
		$templates    = array(
			'field'              => sprintf( $icon, ' style="width:1em"' ),
			'item'               => sprintf(
				'<div class="attachment-preview js--select-attachment svg-icon">
					<div class="thumbnail">
						<div class="centered">%s</div>
					</div>
				</div>
				<a class="check" href="#" title="%s"><div class="media-modal-icon"></div></a>',
				$icon_item,
				esc_attr__( 'Deselect', 'menu-icons' )
			),
			'preview-before'     => sprintf( '<a href="#">%s <span>{{ data.title }}</span></a>', $icon_preview ),
			'preview-after'      => sprintf( '<a href="#"><span>{{ data.title }}</span> %s</a>', $icon_preview ),
			'preview-hide_label' => sprintf( '<a href="#">%s</a>', $icon_preview ),
		);

		return $templates;
	}


	/**
	 * Media frame data
	 *
	 * @since  0.8.0
	 * @param  string $id Icon type.
	 * @return array
	 */
	public function frame_cb( $id ) {
		$data = array(
			'controller'  => 'miSvg',
			'library'     => array(
				'type' => array( 'image/svg', 'image/svg+xml' ),
			),
		);

		return $data;
	}


	/**
	 * Settings fields
	 *
	 * @since  0.4.0
	 * @param  array $fields Settings fields.
	 * @uses   apply_filters() Calls 'menu_icons_{type}_settings_fields'.
	 * @return array
	 */
	public function _settings_fields( $fields ) {
		$_fields = array(
			'width' => array(
				'id'          => 'width',
				'type'        => 'number',
				'label'       => __( 'Width', 'menu-icons' ),
				'default'     => '1',
				'description' => 'em',
				'attributes'  => array(
					'min'  => '.5',
					'step' => '.1',
				),
			),
		);

		$_fields = apply_filters( sprintf( 'menu_icons_%s_settings_fields', $this->type ), $_fields );
		$fields  = wp_parse_args( $_fields, $fields );

		return $fields;
	}
}
