<?php
/**
 * Image
 *
 * @package Menu_Icons
 * @version 0.1.0
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */


require_once dirname( __FILE__ ) . '/type.php';

/**
 * Icon type: Image
 *
 * @version 0.1.0
 */
class Menu_Icons_Type_Image extends Menu_Icons_Type {

	/**
	 * Holds icon type
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $type = 'image';

	/**
	 * Holds icon stylesheet URL
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $stylesheet = 'dashicons';


	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->label = __( 'Image', 'menu-icons' );
		parent::__construct();
	}


	/**
	 * Print field for icons selection
	 *
	 * @since 0.1.0
	 * @param int   $id         Menu item ID
	 * @param array $meta_value Current value of 'menu-icons' metadata
	 */
	public function the_field( $id, $meta_value ) {
		$current    = isset( $meta_value[ $this->key ] ) ? $meta_value[ $this->key ] : '';
		$input_id   = sprintf( 'menu-icons-%d-%s', $id, $this->key );
		$input_name = sprintf( 'menu-icons[%d][%s]', $id, $this->key );
		?>
		<?php printf(
			'<p class="field-icon-child description menu-icon-type-%1$s" data-dep-on="%1$s">',
			esc_attr( $this->type )
		) ?>
			<label for="<?php echo esc_attr( $input_id ) ?>"><?php echo esc_html( $this->label ); ?></label>
			<?php printf(
				'<input type="text" id="%s" name="%s" value="%s" />',
				$input_id,
				$input_name,
				$current
			) ?>
		</p>
		<?php
	}


	/**
	 * Preview
	 *
	 * @since 0.2.0
	 * @param  string $id Icon type ID
	 * @return array
	 */
	public function preview_cb( $id, $meta_value ) {
		return $this->get_image( $meta_value );
	}


	/**
	 * Media frame data
	 *
	 * @since 0.2.0
	 * @param  string $id Icon type ID
	 * @return array
	 */
	public function frame_cb( $id ) {
		return array(
			'frameType' => 'image',
		);
	}


	/**
	 * Media frame data
	 *
	 * @since 0.2.0
	 * @param  string $id Icon type ID
	 * @return array
	 */
	public function templates() {
		$templates = array(
			'preview' => '<img src="{{ data.url }}" alt="" />',
		);

		return $templates;
	}

	/**
	 * Get image icon
	 *
	 * @since 0.2.0
	 * @param array $values Menu icon meta values
	 * @return bool|string  Image HTML on success or FALSE on failure
	 */
	public function get_image( $values ) {
		$src = wp_get_attachment_image_src( $values['image-icon'], 'full' );
		if ( ! empty( $src ) ) {
			$image = sprintf(
				'<img src="%s" alt="" />',
				esc_url( $src[0] )
			);
		}
		else {
			$image = '';
		}

		return $image;
	}

	/**
	 * Add icon to menu title
	 *
	 * Icon types should override this method if they want to provide different markup.
	 *
	 * @since 0.1.0
	 * @param string $title  Menu item title
	 * @param array  $values Menu item metadata value
	 *
	 * @return string
	 */
	protected function add_icon( $title, $values ) {
		$image = $this->get_image( $values );
		if ( ! empty( $image ) ) {
			$title = sprintf( '%s %s', $image, $title );
		}

		return $title;
	}
}
