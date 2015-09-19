<?php
/**
 * Genericons
 *
 * @package Menu_Icons
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */


require_once dirname( __FILE__ ) . '/type-fonts.php';

/**
 * Icon type: Genericons
 *
 * @since 0.1.0
 */
class Menu_Icons_Type_Genericons extends Menu_Icons_Type_Fonts {

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
	protected $version = '3.4';


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
		$this->stylesheet = sprintf(
			'%scss/genericons%s.css',
			Menu_Icons::get( 'url' ),
			Menu_Icons::get_script_suffix()
		);

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
				'key'   => 'actions',
				'label' => __( 'Actions', 'menu-icons' ),
				'items' => array(
					'genericon-checkmark'      => __( 'Checkmark', 'menu-icons' ),
					'genericon-close'          => __( 'Close', 'menu-icons' ),
					'genericon-close-alt'      => __( 'Close', 'menu-icons' ),
					'genericon-dropdown'       => __( 'Dropdown', 'menu-icons' ),
					'genericon-dropdown-left'  => __( 'Dropdown left', 'menu-icons' ),
					'genericon-collapse'       => __( 'Collapse', 'menu-icons' ),
					'genericon-expand'         => __( 'Expand', 'menu-icons' ),
					'genericon-help'           => __( 'Help', 'menu-icons' ),
					'genericon-info'           => __( 'Info', 'menu-icons' ),
					'genericon-lock'           => __( 'Lock', 'menu-icons' ),
					'genericon-maximize'       => __( 'Maximize', 'menu-icons' ),
					'genericon-minimize'       => __( 'Minimize', 'menu-icons' ),
					'genericon-plus'           => __( 'Plus', 'menu-icons' ),
					'genericon-minus'          => __( 'Minus', 'menu-icons' ),
					'genericon-previous'       => __( 'Previous', 'menu-icons' ),
					'genericon-next'           => __( 'Next', 'menu-icons' ),
					'genericon-move'           => __( 'Move', 'menu-icons' ),
					'genericon-hide'           => __( 'Hide', 'menu-icons' ),
					'genericon-show'           => __( 'Show', 'menu-icons' ),
					'genericon-print'          => __( 'Print', 'menu-icons' ),
					'genericon-rating-empty'   => __( 'Rating: Empty', 'menu-icons' ),
					'genericon-rating-half'    => __( 'Rating: Half', 'menu-icons' ),
					'genericon-rating-full'    => __( 'Rating: Full', 'menu-icons' ),
					'genericon-refresh'        => __( 'Refresh', 'menu-icons' ),
					'genericon-reply'          => __( 'Reply', 'menu-icons' ),
					'genericon-reply-alt'      => __( 'Reply alt', 'menu-icons' ),
					'genericon-reply-single'   => __( 'Reply single', 'menu-icons' ),
					'genericon-search'         => __( 'Search', 'menu-icons' ),
					'genericon-send-to-phone'  => __( 'Send to', 'menu-icons' ),
					'genericon-send-to-tablet' => __( 'Send to', 'menu-icons' ),
					'genericon-share'          => __( 'Share', 'menu-icons' ),
					'genericon-shuffle'        => __( 'Shuffle', 'menu-icons' ),
					'genericon-spam'           => __( 'Spam', 'menu-icons' ),
					'genericon-subscribe'      => __( 'Subscribe', 'menu-icons' ),
					'genericon-subscribed'     => __( 'Subscribed', 'menu-icons' ),
					'genericon-unsubscribe'    => __( 'Unsubscribe', 'menu-icons' ),
					'genericon-top'            => __( 'Top', 'menu-icons' ),
					'genericon-unapprove'      => __( 'Unapprove', 'menu-icons' ),
					'genericon-zoom'           => __( 'Zoom', 'menu-icons' ),
					'genericon-unzoom'         => __( 'Unzoom', 'menu-icons' ),
					'genericon-xpost'          => __( 'X-Post', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'media-player',
				'label' => __( 'Media Player', 'menu-icons' ),
				'items' => array(
					'genericon-skip-back'   => __( 'Skip back', 'menu-icons' ),
					'genericon-rewind'      => __( 'Rewind', 'menu-icons' ),
					'genericon-play'        => __( 'Play', 'menu-icons' ),
					'genericon-pause'       => __( 'Pause', 'menu-icons' ),
					'genericon-stop'        => __( 'Stop', 'menu-icons' ),
					'genericon-fastforward' => __( 'Fast Forward', 'menu-icons' ),
					'genericon-skip-ahead'  => __( 'Skip ahead', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'meta',
				'label' => __( 'Meta', 'menu-icons' ),
				'items' => array(
					'genericon-comment'   => __( 'Comment', 'menu-icons' ),
					'genericon-category'  => __( 'Category', 'menu-icons' ),
					'genericon-hierarchy' => __( 'Hierarchy', 'menu-icons' ),
					'genericon-tag'       => __( 'Tag', 'menu-icons' ),
					'genericon-time'      => __( 'Time', 'menu-icons' ),
					'genericon-user'      => __( 'User', 'menu-icons' ),
					'genericon-day'       => __( 'Day', 'menu-icons' ),
					'genericon-week'      => __( 'Week', 'menu-icons' ),
					'genericon-month'     => __( 'Month', 'menu-icons' ),
					'genericon-pinned'    => __( 'Pinned', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'misc',
				'label' => __( 'Misc.', 'menu-icons' ),
				'items' => array(
					'genericon-uparrow'        => __( 'Arrow Up', 'menu-icons' ),
					'genericon-downarrow'      => __( 'Arrow Down', 'menu-icons' ),
					'genericon-leftarrow'      => __( 'Arrow Left', 'menu-icons' ),
					'genericon-rightarrow'     => __( 'Arrow Right', 'menu-icons' ),
					'genericon-activity'       => __( 'Activity', 'menu-icons' ),
					'genericon-bug'            => __( 'Bug', 'menu-icons' ),
					'genericon-book'           => __( 'Book', 'menu-icons' ),
					'genericon-cart'           => __( 'Cart', 'menu-icons' ),
					'genericon-cloud-download' => __( 'Cloud Download', 'menu-icons' ),
					'genericon-cloud-upload'   => __( 'Cloud Upload', 'menu-icons' ),
					'genericon-cog'            => __( 'Cog', 'menu-icons' ),
					'genericon-document'       => __( 'Document', 'menu-icons' ),
					'genericon-dot'            => __( 'Dot', 'menu-icons' ),
					'genericon-download'       => __( 'Download', 'menu-icons' ),
					'genericon-draggable'      => __( 'Draggable', 'menu-icons' ),
					'genericon-ellipsis'       => __( 'Ellipsis', 'menu-icons' ),
					'genericon-external'       => __( 'External', 'menu-icons' ),
					'genericon-feed'           => __( 'Feed', 'menu-icons' ),
					'genericon-flag'           => __( 'Flag', 'menu-icons' ),
					'genericon-fullscreen'     => __( 'Fullscreen', 'menu-icons' ),
					'genericon-handset'        => __( 'Handset', 'menu-icons' ),
					'genericon-heart'          => __( 'Heart', 'menu-icons' ),
					'genericon-key'            => __( 'Key', 'menu-icons' ),
					'genericon-mail'           => __( 'Mail', 'menu-icons' ),
					'genericon-menu'           => __( 'Menu', 'menu-icons' ),
					'genericon-microphone'     => __( 'Microphone', 'menu-icons' ),
					'genericon-notice'         => __( 'Notice', 'menu-icons' ),
					'genericon-paintbrush'     => __( 'Paint Brush', 'menu-icons' ),
					'genericon-phone'          => __( 'Phone', 'menu-icons' ),
					'genericon-picture'        => __( 'Picture', 'menu-icons' ),
					'genericon-plugin'         => __( 'Plugin', 'menu-icons' ),
					'genericon-portfolio'      => __( 'Portfolio', 'menu-icons' ),
					'genericon-star'           => __( 'Star', 'menu-icons' ),
					'genericon-summary'        => __( 'Summary', 'menu-icons' ),
					'genericon-tablet'         => __( 'Tablet', 'menu-icons' ),
					'genericon-videocamera'    => __( 'Video Camera', 'menu-icons' ),
					'genericon-warning'        => __( 'Warning', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'places',
				'label' => __( 'Places', 'menu-icons' ),
				'items' => array(
					'genericon-404'      => __( '404', 'menu-icons' ),
					'genericon-trash'    => __( 'Trash', 'menu-icons' ),
					'genericon-cloud'    => __( 'Cloud', 'menu-icons' ),
					'genericon-home'     => __( 'Home', 'menu-icons' ),
					'genericon-location' => __( 'Location', 'menu-icons' ),
					'genericon-sitemap'  => __( 'Sitemap', 'menu-icons' ),
					'genericon-website'  => __( 'Website', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'post-formats',
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
				'key'   => 'text-editor',
				'label' => __( 'Text Editor', 'menu-icons' ),
				'items' => array(
					'genericon-anchor'     => __( 'Anchor', 'menu-icons' ),
					'genericon-attachment' => __( 'Attachment', 'menu-icons' ),
					'genericon-edit'       => __( 'Edit', 'menu-icons' ),
					'genericon-code'       => __( 'Code', 'menu-icons' ),
					'genericon-bold'       => __( 'Bold', 'menu-icons' ),
					'genericon-italic'     => __( 'Italic', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'social',
				'label' => __( 'Social', 'menu-icons' ),
				'items' => array(
					'genericon-codepen'        => 'CodePen',
					'genericon-digg'           => 'Digg',
					'genericon-dribbble'       => 'Dribbble',
					'genericon-dropbox'        => 'DropBox',
					'genericon-facebook'       => 'Facebook',
					'genericon-facebook-alt'   => 'Facebook',
					'genericon-flickr'         => 'Flickr',
					'genericon-foursquare'     => 'Foursquare',
					'genericon-github'         => 'GitHub',
					'genericon-googleplus'     => 'Google+',
					'genericon-googleplus-alt' => 'Google+',
					'genericon-instagram'      => 'Instagram',
					'genericon-linkedin'       => 'LinkedIn',
					'genericon-linkedin-alt'   => 'LinkedIn',
					'genericon-path'           => 'Path',
					'genericon-pinterest'      => 'Pinterest',
					'genericon-pinterest-alt'  => 'Pinterest',
					'genericon-pocket'         => 'Pocket',
					'genericon-polldaddy'      => 'PollDaddy',
					'genericon-reddit'         => 'Reddit',
					'genericon-skype'          => 'Skype',
					'genericon-spotify'        => 'Spotify',
					'genericon-stumbleupon'    => 'StumbleUpon',
					'genericon-tumblr'         => 'Tumblr',
					'genericon-twitch'         => 'Twitch',
					'genericon-twitter'        => 'Twitter',
					'genericon-vimeo'          => 'Vimeo',
					'genericon-wordpress'      => 'WordPress',
					'genericon-youtube'        => 'Youtube',
				),
			),
		);
	}
}
