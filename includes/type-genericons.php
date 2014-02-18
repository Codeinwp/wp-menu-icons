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
class Menu_Icons_Genericons extends Menu_Icons_Fonts {

	/**
	 * Holds icon type
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $type = 'genericon';

	/**
	 * Holds icon label
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $label = 'Genericons';

	/**
	 * Holds icon version
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '3.0.3';


	/**
	 * Class constructor
	 *
	 * We need to override the parent's to set our stylesheet URL
	 *
	 * @since 0.1.0
	 * @param array $types Icon Types
	 * @return array
	 */
	public function __construct() {
		$this->stylesheet = Menu_Icons::get( 'url' ) . '/css/genericons.css';
		parent::__construct();
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
}


/**
 * Register Genericons
 *
 * @since   0.1.0
 * @wp_hook filter menu_icons_types/10/1
 * @param   array  $types Icon Types
 * @return  array
 */
function _menu_icons_genericons( $types ) {
	$dashicons = new Menu_Icons_Genericons();
	return $dashicons->register( $types );
}
add_filter( 'menu_icons_types', '_menu_icons_genericons' );
