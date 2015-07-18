<?php
/**
 * Icon fonts handler
 *
 * @package Menu_Icons
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */

require_once dirname( __FILE__ ) . '/type.php';

/**
 * Generic handler for icon fonts
 *
 */
abstract class Menu_Icons_Type_Fonts extends Menu_Icons_Type {

	/**
	 * Get icon names
	 *
	 * @since  0.1.0
	 * @return array
	 */
	abstract function get_names();


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
			'font_size'      => array(
				'id'          => 'font_size',
				'type'        => 'number',
				'label'       => __( 'Font Size', 'menu-icons' ),
				'default'     => '1.2',
				'description' => 'em',
				'attributes'  => array(
					'min'  => '0.1',
					'step' => '0.1',
				),
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
		<?php
			printf(
				'<p class="field-icon-child description menu-icon-type-%1$s" data-dep-on="%1$s">',
				esc_attr( $this->type )
			);
		?>
			<label for="<?php echo esc_attr( $input_id ) ?>"><?php echo esc_html( $this->label ); ?></label>
			<?php
				printf(
					'<select id="%s" name="%s" data-key="%s">',
					esc_attr( $input_id ),
					esc_attr( esc_attr( $input_name ) ),
					esc_attr( $this->key )
				);
			?>
				<?php
					printf(
						'<option value=""%s>%s</option>',
						selected( empty( $current ), true, false ),
						esc_html__( '&mdash; Select &mdash;', 'menu-icons' )
					);
				?>
				<?php foreach ( $this->get_names() as $group ) : ?>
					<optgroup label="<?php echo esc_attr( $group['label'] ) ?>">
						<?php foreach ( $group['items'] as $value => $label ) : ?>
							<?php
								printf(
									'<option value="%s"%s>%s</option>',
									esc_attr( $value ),
									selected( $current, $value, false ),
									esc_html( $label )
								);
							?>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
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
		return sprintf(
			'<i class="_icon %s %s"></i>',
			esc_attr( $this->type ),
			esc_attr( $meta_value[ $this->key ] )
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
			'controller' => 'miFont',
		);

		foreach ( $this->get_names() as $group ) {
			$data['groups'][ $group['key'] ] = $group['label'];

			foreach ( $group['items'] as $id => $label ) {
				$data['items'][] = array(
					'type'  => $this->type,
					'group' => $group['key'],
					'id'    => $id,
					'label' => $label,
				);
			}
		}

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
			'field'              => '<i class="_icon {{ data.type }} {{ data.id }}"></i>',
			'item'               => sprintf(
				'<div class="attachment-preview js--select-attachment">
					<div class="thumbnail">
						<span class="_icon"><i class="{{ data.type }} {{ data.id }}"></i></span>
						<div class="filename"><div>{{ data.label }}</div></div>
					</div>
				</div>
				<a class="check" href="#" title="%s"><div class="media-modal-icon"></div></a>',
				esc_attr__( 'Deselect', 'menu-icons' )
			),
			'preview-before'     => sprintf( '<a href="#">%s <span>{{ data.title }}</span></a>', $icon ),
			'preview-after'      => sprintf( '<a href="#"><span>{{ data.title }}</span> %s</a>', $icon ),
			'preview-hide_label' => sprintf( '<a href="#">%s</a>', $icon ),
		);

		return $templates;
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
		if ( empty( $values['position'] ) ) {
			$values['position'] = 'before';
		}

		$class = ! empty( $values['hide_label'] ) ? Menu_Icons::get_hidden_label_class() : '';
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
	 * @since  0.2.0
	 * @param  array  $values Menu item metadata value
	 * @return string
	 */
	protected function get_style( $values ) {
		$style_d = Menu_Icons::get( 'default_style' );
		$style_a = array();
		$style_s = '';

		if ( ! empty( $values['font_size'] ) ) {
			$style_a['font-size'] = sprintf( '%sem', $values['font_size'] );
		}
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
