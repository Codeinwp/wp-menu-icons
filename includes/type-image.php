<?php
/**
 * Image icon handler
 *
 * @package Menu_Icons
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */

require_once dirname( __FILE__ ) . '/type.php';

/**
 * Image icons
 *
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
	 * Class constructor
	 *
	 * This simply sets $label
	 *
	 * @since  0.1.0
	 */
	function __construct() {
		$this->label = __( 'Image', 'menu-icons' );
		parent::__construct();
	}

	/**
	 * Get image sizes
	 *
	 * TODO: Include custom image sizes
	 *
	 * @since  %ver%
	 * @return array
	 */
	protected function get_image_sizes() {
		$_sizes = array(
			'thumbnail' => __( 'Thumbnail', 'menu-icons' ),
			'medium'    => __( 'Medium', 'menu-icons' ),
			'large'     => __( 'Large', 'menu-icons' ),
			'full'      => __( 'Full Size', 'menu-icons' ),
		);

		$_sizes = apply_filters( 'image_size_names_choose', $_sizes );

		$sizes = array();
		foreach ( $_sizes as $value => $label ) {
			$sizes[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		return $sizes;
	}

	/**
	 * Settings fields
	 *
	 * @since  %ver%
	 * @param  array $fields
	 * @uses   apply_filters() Calls 'menu_icons_{type}_settings_sections'.
	 * @return array
	 */
	public function _settings_fields( $fields ) {
		$_fields = array(
			'image_size'     => array(
				'id'      => 'image_size',
				'type'    => 'select',
				'label'   => __( 'Image Size', 'menu-icons' ),
				'default' => 'full',
				'choices' => self::get_image_sizes(),
			),
			'vertical_align' => array(
				'id'      => 'vertical_align',
				'type'    => 'select',
				'label'   => __( 'Vertical Align', 'menu-icons' ),
				'default' => 'middle',
				'choices' => array(
					array(
						'value' => 'super',
						'label' => __( 'Super', 'menu-icons' ),
					),
					array(
						'value' => 'top',
						'label' => __( 'Top', 'menu-icons' ),
					),
					array(
						'value' => 'text-top',
						'label' => __( 'Text Top', 'menu-icons' ),
					),
					array(
						'value' => 'middle',
						'label' => __( 'Middle', 'menu-icons' ),
					),
					array(
						'value' => 'baseline',
						'label' => __( 'Baseline', 'menu-icons' ),
					),
					array(
						'value' => 'text-bottom',
						'label' => __( 'Text Bottom', 'menu-icons' ),
					),
					array(
						'value' => 'bottom',
						'label' => __( 'Bottom', 'menu-icons' ),
					),
					array(
						'value' => 'sub',
						'label' => __( 'Sub', 'menu-icons' ),
					),
				),
			),
		);

		$_fields = apply_filters( sprintf( 'menu_icons_%s_settings_fields', $this->type ), $_fields );
		$fields  = wp_parse_args( $_fields, $fields );

		return $fields;
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
				'<input type="text" id="%s" name="%s" data-key="%s" value="%s" />',
				esc_attr( $input_id ),
				esc_attr( $input_name ),
				esc_attr( $this->key ),
				esc_attr( $current )
			) ?>
		</p>
		<?php
	}


	/**
	 * Preview
	 *
	 * @since  0.2.0
	 * @param  string $id         Menu item ID
	 * @param  array  $meta_value Menu item metadata value
	 * @return array
	 */
	public function preview_cb( $id, $meta_value ) {
		if ( empty( $meta_value['image-icon'] ) ) {
			return null;
		}

		return wp_get_attachment_image(
			$meta_value['image-icon'],
			$meta_value['image_size'],
			false,
			array( 'class' => '_icon' )
		);
	}


	/**
	 * Media frame data
	 *
	 * @since 0.2.0
	 * @param  string $id Icon type ID
	 * @return array
	 */
	public function frame_cb( $id ) {
		$data = array(
			'controller' => 'miImage',
		);

		return $data;
	}


	/**
	 * Media frame templates
	 *
	 * @since 0.2.0
	 * @return array
	 */
	public function templates() {
		$icon = '<i class="_icon {{ data.type }} {{ data.icon }} _{{ data.position }}" style="font-size:{{ data.font_size }}em; vertical-align:{{ data.vertical_align }};"></i>';

		$templates = array(
			'field' => '<img src="{{ data.sizes.full.url }}" alt="{{ data.alt }}" class="_icon" />',
			'preview-before'     => sprintf( '<a href="#">%s <span>{{ data.title }}</span></a>', $icon ),
			'preview-after'      => sprintf( '<a href="#"><span>{{ data.title }}</span> %s</a>', $icon ),
			'preview-hide_label' => sprintf( '<a href="#">%s</a>', $icon ),
		);

		return $templates;
	}


	/**
	 * Add icon to menu title
	 *
	 * @since 0.1.0
	 * @param string $title  Menu item title
	 * @param array  $values Menu item metadata value
	 *
	 * @return string
	 */
	protected function add_icon( $title, $values ) {
		$class = ! empty( $values['hide_label'] ) ? 'visuallyhidden' : '';
		$title = sprintf(
			'<span%s>%s</span>',
			( ! empty( $class ) ) ? sprintf( ' class="%s"', esc_attr( $class ) ) : '',
			$title
		);

		$title = sprintf(
			'%s<i class="_mi%s %s %s"%s></i>%s',
			'before' === $values['position'] ? '' : $title,
			( empty( $values['hide_label'] ) ) ? esc_attr( " _{$values['position']}" ) : '',
			esc_attr( $this->type ),
			esc_attr( $values[ $this->key ] ),
			$this->get_style( $values ),
			'after' === $values['position'] ? '' : $title
		);

		return $title;
	}


	/**
	 * Inline style for icon size, etc
	 *
	 * TODO: Move this to Menu_Icons_Type?
	 *
	 * @since  0.2.0
	 * @param  array  $values Menu item metadata value
	 * @return string
	 */
	protected function get_style( $values ) {
		$style_d = Menu_Icons::get( 'default_style' );
		$style_a = array();
		$style_s = '';

		if ( ! empty( $values['vertical_align'] ) ) {
			$style_a['vertical-align'] = $values['vertical_align'];
		}

		$style_a = array_diff_assoc( $style_a, $style_d );

		if ( ! empty( $style_a ) ) {
			foreach ( $style_a as $key => $value ) {
				$style_s .= sprintf( '%s:%s;', esc_attr( $key ), esc_attr( $value ) );
			}
			$style_s = sprintf( ' style="%s"', $style_s );
		}

		return $style_s;
	}
}
