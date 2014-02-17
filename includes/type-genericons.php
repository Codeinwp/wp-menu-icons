<?php
/**
 * Genericons
 *
 * @package Menu_Icons
 * @version 0.1.0
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */

/**
 * Icon type: Genericons
 *
 * @since 0.1.0
 */
class Menu_Items_Genericons {

	/**
	 * Initialize
	 *
	 * @since 0.1.0
	 */
	public static function init() {
		add_filter( 'menu_icons_types', array( __CLASS__, '_register' ) );
		add_action( 'get_header', array( __CLASS__, '_load_front_end' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_scripts_styles' ), 5 );
	}


	/**
	 * Register our type
	 *
	 * @since 0.1.0
	 * @param array $types Icon Types
	 * @return array
	 */
	public static function _register( $types ) {
		$types['genericon'] = array(
			'label'    => 'Genericons',
			'field_cb' => array( __CLASS__, 'the_field' ),
		);

		return $types;
	}


	/**
	 * Genericons's icons names
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function get_names() {
		return array(
			array(
				'label' => __( 'Post Formats', 'menu-icons' ),
				'items' => array(
					'genericon-standard' => __( 'Standard', 'menu-icons' ),
					'genericon-aside'    => __( 'Aside', 'menu-icons' ),
					'genericon-image'    => __( 'Image', 'menu-icons' ),
					'genericon-gallery'  => __( 'Gallery', 'menu-icons' ),
					'genericon-video'    => __( 'Video', 'menu-icons' ),
					'genericon-status'   => __( 'Status', 'menu-icons' ),
					'genericon-quote'    => __( 'Quote', 'menu-icons' ),
					'genericon-link'     => __( 'Link', 'menu-icons' ),
					'genericon-chat'     => __( 'Chat', 'menu-icons' ),
					'genericon-audio'    => __( 'Audio', 'menu-icons' ),
				),
			),
			array(
				'label' => __( 'Meta', 'menu-icons' ),
				'items' => array(
					'genericon-comment'  => __( 'Comment', 'menu-icons' ),
					'genericon-category' => __( 'Category', 'menu-icons' ),
					'genericon-tag'      => __( 'Tag', 'menu-icons' ),
					'genericon-time'     => __( 'Time', 'menu-icons' ),
					'genericon-user'     => __( 'User', 'menu-icons' ),
					'genericon-day'      => __( 'Day', 'menu-icons' ),
					'genericon-week'     => __( 'Week', 'menu-icons' ),
					'genericon-month'    => __( 'Month', 'menu-icons' ),
					'genericon-pinned'   => __( 'Pinned', 'menu-icons' ),
				),
			),
			array(
				'label' => __( 'Social', 'menu-icons' ),
				'items' => array(
					'genericon-codepen'        => 'CodePen',
					'genericon-digg'           => 'Digg',
					'genericon-dribbble'       => 'Dribbble',
					'genericon-dropbox'        => 'DropBox',
					'genericon-facebook'       => 'Facebook',
					'genericon-facebook-alt'   => sprintf( __( '%s (alt.)', 'menu-icons' ), 'Facebook' ),
					'genericon-flickr'         => 'Flickr',
					'genericon-github'         => 'GitHub',
					'genericon-googleplus'     => 'Google+',
					'genericon-googleplus-alt' => sprintf( __( '%s (alt.)', 'menu-icons' ), 'Google+' ),
					'genericon-instagram'      => 'Instagram',
					'genericon-linkedin'       => 'LinkedIn',
					'genericon-linkedin-alt'   => sprintf( __( '%s (alt.)', 'menu-icons' ), 'LinkedIn' ),
					'genericon-path'           => 'Path',
					'genericon-pinterest'      => 'Pinterest',
					'genericon-pinterest-alt'  => sprintf( __( '%s (alt.)', 'menu-icons' ), 'Pinterest' ),
					'genericon-pocket'         => 'Pocket',
					'genericon-polldaddy'      => 'PollDaddy',
					'genericon-reddit'         => 'Reddit',
					'genericon-skype'          => 'Skype',
					'genericon-stumbleupon'    => 'StumbleUpon',
					'genericon-tumblr'         => 'Tumblr',
					'genericon-twitter'        => 'Twitter',
					'genericon-vimeo'          => 'Vimeo',
					'genericon-wordpress'      => 'WordPress',
					'genericon-youtube'        => 'Youtube',
				),
			),
			array(
				'label' => __( 'Misc.', 'menu-icons' ),
				'items' => array(
					'genericon-404'            => __( '404', 'menu-icons' ),
					'genericon-attachment'     => __( 'Attachment', 'menu-icons' ),
					'genericon-bold'           => __( 'Bold', 'menu-icons' ),
					'genericon-book'           => __( 'Book', 'menu-icons' ),
					'genericon-cart'           => __( 'Cart', 'menu-icons' ),
					'genericon-checkmark'      => __( 'Checkmark', 'menu-icons' ),
					'genericon-close'          => __( 'Close', 'menu-icons' ),
					'genericon-close-alt'      => __( 'Close alt', 'menu-icons' ),
					'genericon-cloud'          => __( 'Cloud', 'menu-icons' ),
					'genericon-cloud-download' => __( 'Cloud download', 'menu-icons' ),
					'genericon-cloud-upload'   => __( 'Cloud upload', 'menu-icons' ),
					'genericon-code'           => __( 'Code', 'menu-icons' ),
					'genericon-cog'            => __( 'Cog', 'menu-icons' ),
					'genericon-collapse'       => __( 'Collapse', 'menu-icons' ),
					'genericon-document'       => __( 'Document', 'menu-icons' ),
					'genericon-dot'            => __( 'Dot', 'menu-icons' ),
					'genericon-downarrow'      => __( 'Downarrow', 'menu-icons' ),
					'genericon-draggable'      => __( 'Draggable', 'menu-icons' ),
					'genericon-dropdown'       => __( 'Dropdown', 'menu-icons' ),
					'genericon-dropdown-left'  => __( 'Dropdown left', 'menu-icons' ),
					'genericon-edit'           => __( 'Edit', 'menu-icons' ),
					'genericon-expand'         => __( 'Expand', 'menu-icons' ),
					'genericon-external'       => __( 'External', 'menu-icons' ),
					'genericon-fastforward'    => __( 'Fastforward', 'menu-icons' ),
					'genericon-feed'           => __( 'Feed', 'menu-icons' ),
					'genericon-flag'           => __( 'Flag', 'menu-icons' ),
					'genericon-fullscreen'     => __( 'Fullscreen', 'menu-icons' ),
					'genericon-heart'          => __( 'Heart', 'menu-icons' ),
					'genericon-help'           => __( 'Help', 'menu-icons' ),
					'genericon-hide'           => __( 'Hide', 'menu-icons' ),
					'genericon-home'           => __( 'Home', 'menu-icons' ),
					'genericon-info'           => __( 'Info', 'menu-icons' ),
					'genericon-italic'         => __( 'Italic', 'menu-icons' ),
					'genericon-key'            => __( 'Key', 'menu-icons' ),
					'genericon-leftarrow'      => __( 'Leftarrow', 'menu-icons' ),
					'genericon-location'       => __( 'Location', 'menu-icons' ),
					'genericon-lock'           => __( 'Lock', 'menu-icons' ),
					'genericon-mail'           => __( 'Mail', 'menu-icons' ),
					'genericon-maximize'       => __( 'Maximize', 'menu-icons' ),
					'genericon-menu'           => __( 'Menu', 'menu-icons' ),
					'genericon-minimize'       => __( 'Minimize', 'menu-icons' ),
					'genericon-next'           => __( 'Next', 'menu-icons' ),
					'genericon-notice'         => __( 'Notice', 'menu-icons' ),
					'genericon-pause'          => __( 'Pause', 'menu-icons' ),
					'genericon-phone'          => __( 'Phone', 'menu-icons' ),
					'genericon-picture'        => __( 'Picture', 'menu-icons' ),
					'genericon-play'           => __( 'Play', 'menu-icons' ),
					'genericon-plugin'         => __( 'Plugin', 'menu-icons' ),
					'genericon-portfolio'      => __( 'Portfolio', 'menu-icons' ),
					'genericon-previous'       => __( 'Previous', 'menu-icons' ),
					'genericon-print'          => __( 'Print', 'menu-icons' ),
					'genericon-refresh'        => __( 'Refresh', 'menu-icons' ),
					'genericon-reply'          => __( 'Reply', 'menu-icons' ),
					'genericon-reply-alt'      => __( 'Reply alt', 'menu-icons' ),
					'genericon-reply-single'   => __( 'Reply single', 'menu-icons' ),
					'genericon-rewind'         => __( 'Rewind', 'menu-icons' ),
					'genericon-rightarrow'     => __( 'Rightarrow', 'menu-icons' ),
					'genericon-search'         => __( 'Search', 'menu-icons' ),
					'genericon-send-to-phone'  => __( 'Send to', 'menu-icons' ),
					'genericon-send-to-tablet' => __( 'Send to', 'menu-icons' ),
					'genericon-share'          => __( 'Share', 'menu-icons' ),
					'genericon-show'           => __( 'Show', 'menu-icons' ),
					'genericon-skip-ahead'     => __( 'Skip ahead', 'menu-icons' ),
					'genericon-skip-back'      => __( 'Skip back', 'menu-icons' ),
					'genericon-spam'           => __( 'Spam', 'menu-icons' ),
					'genericon-star'           => __( 'Star', 'menu-icons' ),
					'genericon-stop'           => __( 'Stop', 'menu-icons' ),
					'genericon-subscribe'      => __( 'Subscribe', 'menu-icons' ),
					'genericon-subscribed'     => __( 'Subscribed', 'menu-icons' ),
					'genericon-summary'        => __( 'Summary', 'menu-icons' ),
					'genericon-tablet'         => __( 'Tablet', 'menu-icons' ),
					'genericon-top'            => __( 'Top', 'menu-icons' ),
					'genericon-trash'          => __( 'Trash', 'menu-icons' ),
					'genericon-unapprove'      => __( 'Unapprove', 'menu-icons' ),
					'genericon-unsubscribe'    => __( 'Unsubscribe', 'menu-icons' ),
					'genericon-unzoom'         => __( 'Unzoom', 'menu-icons' ),
					'genericon-uparrow'        => __( 'Uparrow', 'menu-icons' ),
					'genericon-warning'        => __( 'Warning', 'menu-icons' ),
					'genericon-zoom'           => __( 'Zoom', 'menu-icons' ),
				),
			),
		);
	}


	/**
	 * Print field for genericons selection
	 *
	 * @since 0.1.0
	 * @param int   $id      Menu item ID
	 * @param array $current Current values of 'menu-icons' metadata
	 */
	public static function the_field( $id, $current ) {
		$input_id   = sprintf( 'menu-icons-%d-genericon-icon', $id );
		$input_name = sprintf( 'menu-icons[%d][genericon-icon]', $id );
		?>
		<p class="description menu-icon-type-font-awesome">
			<label for="<?php echo esc_attr( $input_id ) ?>"><?php esc_html_e( 'Icon', 'menu-icons' ); ?></label>
			<select id="<?php echo esc_attr( $input_id ) ?>" name="<?php echo esc_attr( $input_name ) ?>">
				<?php printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $value ),
					selected( empty( $current['genericon-icon'] ), true, false ),
					esc_html__( '&mdash; Select &mdash;', 'menu-icons' )
				) ?>
				<?php foreach ( self::get_names() as $group ) : ?>
					<optgroup label="<?php echo esc_attr( $group['label'] ) ?>">
						<?php foreach ( $group['items'] as $value => $label ) : ?>
							<?php printf(
								'<option value="%s"%s>%s</option>',
								esc_attr( $value ),
								selected( ( isset( $current['genericon-icon'] ) && $value === $current['genericon-icon'] ), true, false ),
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
	 * Load front-end handler
	 *
	 * @since   0.1.0
	 * @wp_hook action get_header
	 */
	public static function _load_front_end() {
		require_once dirname( __FILE__ ) . '/type-fonts.php';
		$front = new Menu_Icons_Fonts( 'genericon' );
	}


	/**
	 * Enqueue genericons stylesheet
	 *
	 * @since 0.1.0
	 * @wp_hook action wp_enqueue_scripts
	 */
	public static function _enqueue_scripts_styles() {
		wp_enqueue_style(
			'genericons',
			Menu_Icons::get( 'url' ) . '/css/genericons.css',
			false,
			'3.0.3'
		);
	}
}

Menu_Items_Genericons::init();
