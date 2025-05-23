<?php

/**
 * Form Fields
 *
 * A prototype for the new "Form Fields" plugin, a standalone plugin and
 * extension for the upcoming "Settings" plugin, a rewrite of KC Settings.
 *
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */

/**
 * Form Fields
 */
abstract class Kucrut_Form_Field {

	/**
	 * Holds field & argument defaults
	 *
	 * @since  0.1.0
	 * @var    array
	 * @access protected
	 */
	protected static $defaults = array(
		'field' => array(
			'id'          => '',
			'type'        => 'text',
			'value'       => null,
			'default'     => null,
			'attributes'  => array(),
			'description' => '',
			'choices'     => array(),
		),
		'args'  => array(
			'keys'               => array(),
			'inline_description' => false,
		),
	);

	/**
	 * Holds field attributes
	 *
	 * @since  0.1.0
	 * @var    array
	 * @access protected
	 */
	protected static $types = array(
		'text'            => 'Kucrut_Form_Field_Text',
		'number'          => 'Kucrut_Form_Field_Text',
		'url'             => 'Kucrut_Form_Field_Text',
		'color'           => 'Kucrut_Form_Field_Text',
		'date'            => 'Kucrut_Form_Field_Text',
		'hidden'          => 'Kucrut_Form_Field_Text',
		'checkbox'        => 'Kucrut_Form_Field_Checkbox',
		'radio'           => 'Kucrut_Form_Field_Radio',
		'textarea'        => 'Kucrut_Form_Field_Textarea',
		'select'          => 'Kucrut_Form_Field_Select',
		'select_multiple' => 'Kucrut_Form_Field_Select_Multiple',
		'select_pages'    => 'Kucrut_Form_Field_Select_Pages',
		'special'         => 'Kucrut_Form_Field_Special',
	);

	/**
	 * Holds forbidden attributes
	 *
	 * @since  0.1.0
	 * @var    array
	 * @access protected
	 */
	protected static $forbidden_attributes = array(
		'id',
		'name',
		'value',
		'checked',
		'multiple',
	);

	/**
	 * Holds allowed html tags
	 *
	 * @since  0.1.0
	 * @var    array
	 * @access protected
	 */
	protected $allowed_html = array(
		'a'      => array(
			'href'   => true,
			'target' => true,
			'title'  => true,
		),
		'code'   => true,
		'em'     => true,
		'p'      => array( 'class' => true ),
		'span'   => array( 'class' => true ),
		'strong' => true,
	);

	/**
	 * Holds constructed field
	 *
	 * @since  0.1.0
	 * @var    array
	 * @access protected
	 */
	protected $field;


	/**
	 * Holds field attributes
	 *
	 * @since  0.1.0
	 * @var    array
	 * @access protected
	 */
	protected $attributes = array();


	/**
	 * Loader
	 *
	 * @param string URL path to this directory
	 */
	final public static function load( $url_path = null ) {
		// Set URL path for assets
		if ( ! is_null( $url_path ) ) {
			self::$url_path = $url_path;
		} else {
			self::$url_path = plugin_dir_url( __FILE__ );
		}

		// Supported field types
		self::$types = apply_filters(
			'form_field_types',
			self::$types
		);
	}


	/**
	 * Create field
	 *
	 * @param array $field Field array
	 * @param array $args  Extra field arguments
	 */
	final public static function create( array $field, $args = array() ) {
		$field = wp_parse_args( $field, self::$defaults['field'] );
		if ( ! isset( self::$types[ $field['type'] ] )
			|| ! is_subclass_of( self::$types[ $field['type'] ], __CLASS__ )
		) {
			trigger_error(
				sprintf(
					// translators: %1$s - the name of the class, %2$s - the type of the field.
					esc_html__( '%1$s: Type %2$s is not supported, reverting to text.', 'menu-icons' ),
					__CLASS__,
					esc_html( $field['type'] )
				),
				E_USER_WARNING
			);
			$field['type'] = 'text';
		}

		if ( is_null( $field['value'] ) && ! is_null( $field['default'] ) ) {
			$field['value'] = $field['default'];
		}

		foreach ( self::$forbidden_attributes as $key ) {
			unset( $field['attributes'][ $key ] );
		}

		$args  = (object) wp_parse_args( $args, self::$defaults['args'] );
		$class = self::$types[ $field['type'] ];

		return new $class( $field, $args );
	}


	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 * @param array  $field Field array
	 * @param object $args  Extra field arguments
	 */
	public function __construct( $field, $args ) {
		$this->field = $field;
		$this->args  = $args;

		if ( ! is_array( $this->args->keys ) ) {
			$this->args->keys = array();
		}
		$this->args->keys[] = $field['id'];

		$this->attributes['id']   = $this->create_id();
		$this->attributes['name'] = $this->create_name();

		$this->attributes = wp_parse_args(
			$this->attributes,
			(array) $field['attributes']
		);

		$this->set_properties();
	}


	/**
	 * Attribute
	 *
	 * @since  0.1.0
	 * @param  string $key Attribute key
	 * @return mixed  NULL if attribute doesn't exist
	 */
	public function __get( $key ) {
		foreach ( array( 'attributes', 'field' ) as $group ) {
			if ( isset( $this->{$group}[ $key ] ) ) {
				return $this->{$group}[ $key ];
			}
		}

		return null;
	}


	/**
	 * Create id/name attribute
	 *
	 * @since 0.1.0
	 * @param string $format Attribute format
	 */
	protected function create_id_name( $format ) {
		return call_user_func_array(
			'sprintf',
			array_merge(
				array( $format ),
				$this->args->keys
			)
		);
	}


	/**
	 * Create id attribute
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return string
	 */
	protected function create_id() {
		$format = implode( '-', $this->args->keys );

		return $this->create_id_name( $format );
	}


	/**
	 * Create name attribute
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return string
	 */
	protected function create_name() {
		$format  = '%s';
		$format .= str_repeat( '[%s]', ( count( $this->args->keys ) - 1 ) );

		return $this->create_id_name( $format );
	}


	/**
	 * Set field properties
	 *
	 * @since 0.1.0
	 */
	protected function set_properties() {}


	/**
	 * Build field attributes
	 *
	 * @since  0.1.0
	 * @param  array  $excludes Attributes to be excluded
	 * @return string
	 */
	protected function build_attributes( $excludes = array() ) {
		$excludes   = array_filter( (array) $excludes );
		$attributes = '';

		foreach ( $this->attributes as $key => $value ) {
			if ( in_array( $key, $excludes, true ) ) {
				continue;
			}

			if ( 'class' === $key ) {
				$value = implode( ' ', (array) $value );
			}

			$attributes .= sprintf(
				' %s="%s"',
				esc_attr( $key ),
				esc_attr( $value )
			);
		}

		return $attributes;
	}


	/**
	 * Print field
	 *
	 * @since 0.1.0
	 */
	abstract public function render();


	/**
	 * Print field description
	 *
	 * @since 0.1.0
	 */
	public function description() {
		if ( ! empty( $this->field['description'] ) ) {
			$tag = ( ! empty( $this->args->inline_description ) ) ? 'span' : 'p';

			printf( // WPCS: XSS ok.
				'<%1$s class="description">%2$s</%1$s>',
				$tag,
				wp_kses( $this->field['description'], $this->allowed_html )
			);
		}
	}
}


/**
 * Field: text
 */
class Kucrut_Form_Field_Text extends Kucrut_Form_Field {

	protected $template = '<input type="%s" value="%s"%s />';


	protected function set_properties() {
		if ( ! is_string( $this->field['value'] ) ) {
			$this->field['value'] = '';
		}

		if ( in_array( $this->field['type'], array( 'text', 'url' ), true ) ) {
			if ( ! isset( $this->attributes['class'] ) ) {
				$this->attributes['class'] = array();
			}
			$this->attributes['class'] = array_unique(
				array_merge(
					array( 'regular-text' ),
					$this->attributes['class']
				)
			);
		}
	}


	public function render() {
		printf(  // WPCS: xss ok
			$this->template,
			esc_attr( $this->field['type'] ),
			esc_attr( $this->field['value'] ),
			$this->build_attributes()
		);
		$this->description();
	}
}


/**
 * Field: Textarea
 */
class Kucrut_Form_Field_Textarea extends Kucrut_Form_Field {

	protected $template = '<textarea%s>%s</textarea>';

	protected $attributes = array(
		'class' => 'widefat',
		'cols'  => 50,
		'rows'  => 5,
	);


	public function render() {
		printf( // WPCS: XSS ok.
			$this->template,
			$this->build_attributes(),
			esc_textarea( $this->field['value'] )
		);
	}
}


/**
 * Field: Checkbox
 */
class Kucrut_Form_Field_Checkbox extends Kucrut_Form_Field {

	protected $template = '<label><input type="%s" value="%s"%s%s /> %s</label><br />';


	protected function set_properties() {
		$this->field['value'] = array_filter( (array) $this->field['value'] );
		$this->attributes['name'] .= '[]';
	}


	protected function checked( $value ) {
		return checked( in_array( $value, $this->field['value'], true ), true, false );
	}


	public function render() {
		foreach ( $this->field['choices'] as $value => $label ) {
			printf( // WPCS: XSS ok.
				$this->template,
				$this->field['type'],
				esc_attr( $value ),
				$this->checked( $value ),
				$this->build_attributes( 'id' ),
				esc_html( $label )
			);
		}
	}
}


/**
 * Field: Radio
 */
class Kucrut_Form_Field_Radio extends Kucrut_Form_Field_Checkbox {

	protected function set_properties() {
		if ( ! is_string( $this->field['value'] ) ) {
			$this->field['value'] = '';
		}
	}


	protected function checked( $value ) {
		return checked( $value, $this->field['value'], false );
	}
}


/**
 * Field: Select
 */
class Kucrut_Form_Field_Select extends Kucrut_Form_Field {

	protected $template = '<option value="%s"%s>%s</option>';


	protected function set_properties() {
		if ( ! is_string( $this->field['value'] ) ) {
			$this->field['value'] = '';
		}
	}


	protected function selected( $value ) {
		return selected( ( $value === $this->field['value'] ), true, false );
	}


	public function render() {
		?>
		<select<?php echo $this->build_attributes() // xss ok ?>>
			<?php foreach ( $this->field['choices'] as $index => $choice ) : ?>
				<?php
				if ( is_array( $choice ) ) {
					$value = $choice['value'];
					$label = $choice['label'];
				} else {
					$value = $index;
					$label = $choice;
				}
				?>
				<?php
					printf( // WPCS: XSS ok.
						$this->template,
						esc_attr( $value ),
						$this->selected( $value ),
						esc_html( $label )
					);
				?>
			<?php endforeach; ?>
		</select>
		<?php
	}
}


/**
 * Field: Multiple Select
 */
class Kucrut_Form_Field_Select_Multiple extends Kucrut_Form_Field_Select {

	protected function set_properties() {
		$this->field['value']         = array_filter( (array) $this->field['value'] );
		$this->attributes['name']    .= '[]';
		$this->attributes['multiple'] = 'multiple';
	}


	protected function selected( $value ) {
		return selected( in_array( $value, $this->field['value'], true ), true, false );
	}
}


/**
 * Field: Select Pages
 */
class Kucrut_Form_Field_Select_Pages extends Kucrut_Form_Field_Select {

	protected $wp_dropdown_pages_args = array(
		'depth'             => 0,
		'child_of'          => 0,
		'option_none_value' => '',
	);


	public function __construct( $field, $args ) {
		$this->wp_dropdown_pages_args['show_option_none'] = __( '&mdash; Select &mdash;', 'menu-icons' );
		parent::__construct( $field, $args );
	}


	public function set_properties() {
		parent::set_properties();

		if ( empty( $this->args->wp_dropdown_pages_args ) ) {
			$this->args->wp_dropdown_pages_args = array();
		}

		// Apply defeaults
		$this->args->wp_dropdown_pages_args = wp_parse_args(
			$this->args->wp_dropdown_pages_args,
			$this->wp_dropdown_pages_args
		);

		// Force some args
		$this->args->wp_dropdown_pages_args = array_merge(
			$this->args->wp_dropdown_pages_args,
			array(
				'echo'     => true,
				'name'     => $this->attributes['name'],
				'id'       => $this->attributes['id'],
				'selected' => $this->field['value'],
			)
		);
	}


	public function render() {
		wp_dropdown_pages( $this->args->wp_dropdown_pages_args ); // WPCS: XSS ok.
	}
}


/**
 * Field: Special (Callback)
 */
class Kucrut_Form_Field_Special extends Kucrut_Form_Field {
	public function render() {
		call_user_func_array(
			$this->field['render_cb'],
			array( $this )
		);
	}
}
