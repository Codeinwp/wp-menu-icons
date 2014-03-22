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
			esc_attr( $this->type ),
			esc_attr( $this->key )
		) ?>
			<label for="<?php echo esc_attr( $input_id ) ?>"><?php echo esc_html( $this->label ); ?></label>
			<?php printf(
				'<select id="%s" name="%s" data-key="%s">',
				esc_attr( $input_id ),
				esc_attr( esc_attr( $input_name ) ),
				esc_attr( $this->key )
			) ?>
				<?php printf(
					'<option value=""%s>%s</option>',
					selected( empty( $current ), true, false ),
					esc_html__( '&mdash; Select &mdash;', 'menu-icons' )
				) ?>
				<?php foreach ( $this->get_names() as $group ) : ?>
					<optgroup label="<?php echo esc_attr( $group['label'] ) ?>">
						<?php foreach ( $group['items'] as $value => $label ) : ?>
							<?php printf(
								'<option value="%s"%s>%s</option>',
								esc_attr( $value ),
								selected( $meta_value[ $this->key ], $value, false ),
								esc_html( $label )
							) ?>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}


	/**
	 * Media frame data
	 *
	 * @since 0.2.0
	 * @param  string $id Icon type ID
	 * @return array
	 */
	/*
	public function frame_cb( $id ) {
		$data = array(
			'frameType' => 'font',
			'tabs'      => array(),
			'contents'  => array(),
		);

		foreach ( $this->get_names() as $group ) {
			$key = strtolower( str_replace( ' ', '-', $group['label'] ) );

			$data['tabs'][ $key ]     = $group['label'];
			$data['contents'][ $key ] = $group['items'];
		}

		return $data;
	}
	*/


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
		$title = sprintf( '<i class="%s %s"></i>%s', $values['type'], $values[ $this->key ], $title );

		return $title;
	}
}
