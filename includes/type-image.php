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
	 * @since  0.4.0
	 * @access protected
	 * @var    string
	 */
	protected $type = 'image';

	/**
	 * Class constructor
	 *
	 * This simply sets $label
	 *
	 * @since 0.4.0
	 */
	function __construct() {
		$this->label = __( 'Image', 'menu-icons' );
		parent::__construct();
	}

	/**
	 * Get image sizes
	 *
	 * @since  0.4.0
	 * @access protected
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
	 * @since  0.4.0
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
	 * @since 0.4.0
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
	 * @since  0.4.0
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
	 * @since  0.4.0
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
	 * @since 0.4.0
	 * @return array
	 */
	public function templates() {
		$icon = '<img src="{{ data.url }}" alt="{{ data.alt }}" class="_icon {{ data.type }} _{{ data.position }}" style="vertical-align:{{ data.vertical_align }};" />';

		$templates = array(
			'field'              => '<img src="{{ data.sizes[data._settings.image_size].url }}" alt="{{ data.alt }}" class="_icon" />',
			'preview-before'     => sprintf( '<a href="#">%s <span>{{ data.title }}</span></a>', $icon ),
			'preview-after'      => sprintf( '<a href="#"><span>{{ data.title }}</span> %s</a>', $icon ),
			'preview-hide_label' => sprintf( '<a href="#">%s</a>', $icon ),
		);

		return $templates;
	}


	/**
	 * Add icon to menu title
	 *
	 * @since  0.4.0
	 * @access protected
	 * @param  string $title  Menu item title
	 * @param  array  $values Menu item metadata value
	 *
	 * @return string
	 */
	protected function add_icon( $title, $values ) {
		if ( empty( $values['image-icon'] ) ) {
			return $title;
		}

		$icon = get_post( $values['image-icon'] );
		if ( ! ( $icon instanceof WP_Post ) || 'attachment' !== $icon->post_type ) {
			return $title;
		}

		$t_class = ! empty( $values['hide_label'] ) ? 'visuallyhidden' : '';
		$title   = sprintf(
			'<span%s>%s</span>',
			( ! empty( $t_class ) ) ? sprintf( ' class="%s"', esc_attr( $t_class ) ) : '',
			$title
		);

		$i_class  = '_mi';
		$i_class .= empty( $values['hide_label'] ) ? " _{$values['position']}" : '';
		$i_style  = $this->get_style( $values );
		$i_attrs  = array( 'class' => $i_class );

		if ( ! empty( $i_style ) ) {
			$i_attrs['style'] = $i_style;
		}

		$title = sprintf(
			'%s%s%s',
			'before' === $values['position'] ? '' : $title,
			wp_get_attachment_image(
				$icon->ID,
				$values['image_size'],
				false,
				$i_attrs
			),
			'after' === $values['position'] ? '' : $title
		);

		return $title;
	}


	/**
	 * Inline style for icon size, etc
	 *
	 * @since  0.4.0
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
		}

		return $style_s;
	}
}
