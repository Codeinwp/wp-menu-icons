<?php
/**
 * Dashicons
 *
 * @package Menu_Icons
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */


require_once dirname( __FILE__ ) . '/type-fonts.php';

/**
 * Icon type: Elusive
 *
 * @version 2.0
 */
class Menu_Icons_Type_Elusive extends Menu_Icons_Type_Fonts {

	/**
	 * Holds icon type
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $type = 'elusive';

	/**
	 * Holds icon label
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $label = 'Elusive';

	/**
	 * Holds icon version
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '2.0';


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
			'%scss/elusive%s.css',
			Menu_Icons::get( 'url' ),
			Menu_Icons::get_script_suffix()
		);

		parent::__construct();
	}


	/**
	 * Eusive's icon names
	 *
	 * @since  0.1.0
	 * @return array
	 */
	public function get_names() {
		return array(
			array(
				'key'   => 'actions',
				'label' => __( 'Actions', 'menu-icons' ),
				'items' => array(
					'el-icon-adjust'             => __( 'Adjust', 'menu-icons' ),
					'el-icon-adjust-alt'         => __( 'Adjust', 'menu-icons' ),
					'el-icon-align-left'         => __( 'Align Left', 'menu-icons' ),
					'el-icon-align-center'       => __( 'Align Center', 'menu-icons' ),
					'el-icon-align-right'        => __( 'Align Right', 'menu-icons' ),
					'el-icon-align-justify'      => __( 'Justify', 'menu-icons' ),
					'el-icon-arrow-up'           => __( 'Arrow Up', 'menu-icons' ),
					'el-icon-arrow-down'         => __( 'Arrow Down', 'menu-icons' ),
					'el-icon-arrow-left'         => __( 'Arrow Left', 'menu-icons' ),
					'el-icon-arrow-right'        => __( 'Arrow Right', 'menu-icons' ),
					'el-icon-fast-backward'      => __( 'Fast Backward', 'menu-icons' ),
					'el-icon-step-backward'      => __( 'Step Backward', 'menu-icons' ),
					'el-icon-backward'           => __( 'Backward', 'menu-icons' ),
					'el-icon-forward'            => __( 'Forward', 'menu-icons' ),
					'el-icon-forward-alt'        => __( 'Forward', 'menu-icons' ),
					'el-icon-step-forward'       => __( 'Step Forward', 'menu-icons' ),
					'el-icon-fast-forward'       => __( 'Fast Forward', 'menu-icons' ),
					'el-icon-bold'               => __( 'Bold', 'menu-icons' ),
					'el-icon-italic'             => __( 'Italic', 'menu-icons' ),
					'el-icon-link'               => __( 'Link', 'menu-icons' ),
					'el-icon-caret-up'           => __( 'Caret Up', 'menu-icons' ),
					'el-icon-caret-down'         => __( 'Caret Down', 'menu-icons' ),
					'el-icon-caret-left'         => __( 'Caret Left', 'menu-icons' ),
					'el-icon-caret-right'        => __( 'Caret Right', 'menu-icons' ),
					'el-icon-check'              => __( 'Check', 'menu-icons' ),
					'el-icon-check-empty'        => __( 'Check Empty', 'menu-icons' ),
					'el-icon-chevron-up'         => __( 'Chevron Up', 'menu-icons' ),
					'el-icon-chevron-down'       => __( 'Chevron Down', 'menu-icons' ),
					'el-icon-chevron-left'       => __( 'Chevron Left', 'menu-icons' ),
					'el-icon-chevron-right'      => __( 'Chevron Right', 'menu-icons' ),
					'el-icon-circle-arrow-up'    => __( 'Circle Arrow Up', 'menu-icons' ),
					'el-icon-circle-arrow-down'  => __( 'Circle Arrow Down', 'menu-icons' ),
					'el-icon-circle-arrow-left'  => __( 'Circle Arrow Left', 'menu-icons' ),
					'el-icon-circle-arrow-right' => __( 'Circle Arrow Right', 'menu-icons' ),
					'el-icon-download'           => __( 'Download', 'menu-icons' ),
					'el-icon-download-alt'       => __( 'Download', 'menu-icons' ),
					'el-icon-edit'               => __( 'Edit', 'menu-icons' ),
					'el-icon-eject'              => __( 'Eject', 'menu-icons' ),
					'el-icon-file-new'           => __( 'File New', 'menu-icons' ),
					'el-icon-file-new-alt'       => __( 'File New', 'menu-icons' ),
					'el-icon-file-edit'          => __( 'File Edit', 'menu-icons' ),
					'el-icon-file-edit-alt'      => __( 'File Edit', 'menu-icons' ),
					'el-icon-fork'               => __( 'Fork', 'menu-icons' ),
					'el-icon-fullscreen'         => __( 'Fullscreen', 'menu-icons' ),
					'el-icon-indent-left'        => __( 'Indent Left', 'menu-icons' ),
					'el-icon-indent-right'       => __( 'Indent Right', 'menu-icons' ),
					'el-icon-list'               => __( 'List', 'menu-icons' ),
					'el-icon-list-alt'           => __( 'List', 'menu-icons' ),
					'el-icon-lock'               => __( 'Lock', 'menu-icons' ),
					'el-icon-lock-alt'           => __( 'Lock', 'menu-icons' ),
					'el-icon-unlock'             => __( 'Unlock', 'menu-icons' ),
					'el-icon-unlock-alt'         => __( 'Unlock', 'menu-icons' ),
					'el-icon-map-marker'         => __( 'Map Marker', 'menu-icons' ),
					'el-icon-map-marker-alt'     => __( 'Map Marker', 'menu-icons' ),
					'el-icon-minus'              => __( 'Minus', 'menu-icons' ),
					'el-icon-minus-sign'         => __( 'Minus Sign', 'menu-icons' ),
					'el-icon-move'               => __( 'Move', 'menu-icons' ),
					'el-icon-off'                => __( 'Off', 'menu-icons' ),
					'el-icon-ok'                 => __( 'OK', 'menu-icons' ),
					'el-icon-ok-circle'          => __( 'OK Circle', 'menu-icons' ),
					'el-icon-ok-sign'            => __( 'OK Sign', 'menu-icons' ),
					'el-icon-play'               => __( 'Play', 'menu-icons' ),
					'el-icon-play-alt'           => __( 'Play', 'menu-icons' ),
					'el-icon-pause'              => __( 'Pause', 'menu-icons' ),
					'el-icon-pause-alt'          => __( 'Pause', 'menu-icons' ),
					'el-icon-stop'               => __( 'Stop', 'menu-icons' ),
					'el-icon-stop-alt'           => __( 'Stop', 'menu-icons' ),
					'el-icon-plus'               => __( 'Plus', 'menu-icons' ),
					'el-icon-plus-sign'          => __( 'Plus Sign', 'menu-icons' ),
					'el-icon-print'              => __( 'Print', 'menu-icons' ),
					'el-icon-question'           => __( 'Question', 'menu-icons' ),
					'el-icon-question-sign'      => __( 'Question Sign', 'menu-icons' ),
					'el-icon-record'             => __( 'Record', 'menu-icons' ),
					'el-icon-refresh'            => __( 'Refresh', 'menu-icons' ),
					'el-icon-remove'             => __( 'Remove', 'menu-icons' ),
					'el-icon-repeat'             => __( 'Repeat', 'menu-icons' ),
					'el-icon-repeat-alt'         => __( 'Repeat', 'menu-icons' ),
					'el-icon-resize-vertical'    => __( 'Resize Vertical', 'menu-icons' ),
					'el-icon-resize-horizontal'  => __( 'Resize Horizontal', 'menu-icons' ),
					'el-icon-resize-full'        => __( 'Resize Full', 'menu-icons' ),
					'el-icon-resize-small'       => __( 'Resize Small', 'menu-icons' ),
					'el-icon-return-key'         => __( 'Return', 'menu-icons' ),
					'el-icon-retweet'            => __( 'Retweet', 'menu-icons' ),
					'el-icon-reverse-alt'        => __( 'Reverse', 'menu-icons' ),
					'el-icon-search'             => __( 'Search', 'menu-icons' ),
					'el-icon-search-alt'         => __( 'Search', 'menu-icons' ),
					'el-icon-share'              => __( 'Share', 'menu-icons' ),
					'el-icon-share-alt'          => __( 'Share', 'menu-icons' ),
					'el-icon-tag'                => __( 'Tag', 'menu-icons' ),
					'el-icon-tasks'              => __( 'Tasks', 'menu-icons' ),
					'el-icon-text-height'        => __( 'Text Height', 'menu-icons' ),
					'el-icon-text-width'         => __( 'Text Width', 'menu-icons' ),
					'el-icon-thumbs-up'          => __( 'Thumbs Up', 'menu-icons' ),
					'el-icon-thumbs-down'        => __( 'Thumbs Down', 'menu-icons' ),
					'el-icon-tint'               => __( 'Tint', 'menu-icons' ),
					'el-icon-trash'              => __( 'Trash', 'menu-icons' ),
					'el-icon-trash-alt'          => __( 'Trash', 'menu-icons' ),
					'el-icon-upload'             => __( 'Upload', 'menu-icons' ),
					'el-icon-view-mode'          => __( 'View Mode', 'menu-icons' ),
					'el-icon-volume-up'          => __( 'Volume Up', 'menu-icons' ),
					'el-icon-volume-down'        => __( 'Volume Down', 'menu-icons' ),
					'el-icon-volume-off'         => __( 'Mute', 'menu-icons' ),
					'el-icon-warning-sign'       => __( 'Warning Sign', 'menu-icons' ),
					'el-icon-zoom-in'            => __( 'Zoom In', 'menu-icons' ),
					'el-icon-zoom-out'           => __( 'Zoom Out', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'currency',
				'label' => __( 'Currency', 'menu-icons' ),
				'items' => array(
					'el-icon-eur' => 'EUR',
					'el-icon-gbp' => 'GBP',
					'el-icon-usd' => 'USD',
				),
			),
			array(
				'key'   => 'media',
				'label' => __( 'Media', 'menu-icons' ),
				'items' => array(
					'el-icon-video'     => __( 'Video', 'menu-icons' ),
					'el-icon-video-alt' => __( 'Video', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'misc',
				'label' => __( 'Misc.', 'menu-icons' ),
				'items' => array(
					'el-icon-adult'              => __( 'Adult', 'menu-icons' ),
					'el-icon-address-book'       => __( 'Address Book', 'menu-icons' ),
					'el-icon-address-book-alt'   => __( 'Address Book', 'menu-icons' ),
					'el-icon-asl'                => __( 'ASL', 'menu-icons' ),
					'el-icon-asterisk'           => __( 'Asterisk', 'menu-icons' ),
					'el-icon-ban-circle'         => __( 'Ban Circle', 'menu-icons' ),
					'el-icon-barcode'            => __( 'Barcode', 'menu-icons' ),
					'el-icon-bell'               => __( 'Bell', 'menu-icons' ),
					'el-icon-blind'              => __( 'Blind', 'menu-icons' ),
					'el-icon-book'               => __( 'Book', 'menu-icons' ),
					'el-icon-braille'            => __( 'Braille', 'menu-icons' ),
					'el-icon-briefcase'          => __( 'Briefcase', 'menu-icons' ),
					'el-icon-broom'              => __( 'Broom', 'menu-icons' ),
					'el-icon-brush'              => __( 'Brush', 'menu-icons' ),
					'el-icon-bulb'               => __( 'Bulb', 'menu-icons' ),
					'el-icon-bullhorn'           => __( 'Bullhorn', 'menu-icons' ),
					'el-icon-calendar'           => __( 'Calendar', 'menu-icons' ),
					'el-icon-calendar-sign'      => __( 'Calendar Sign', 'menu-icons' ),
					'el-icon-camera'             => __( 'Camera', 'menu-icons' ),
					'el-icon-car'                => __( 'Car', 'menu-icons' ),
					'el-icon-cc'                 => __( 'CC', 'menu-icons' ),
					'el-icon-certificate'        => __( 'Certificate', 'menu-icons' ),
					'el-icon-child'              => __( 'Child', 'menu-icons' ),
					'el-icon-cog'                => __( 'Cog', 'menu-icons' ),
					'el-icon-cog-alt'            => __( 'Cog', 'menu-icons' ),
					'el-icon-cogs'               => __( 'Cogs', 'menu-icons' ),
					'el-icon-comment'            => __( 'Comment', 'menu-icons' ),
					'el-icon-comment-alt'        => __( 'Comment', 'menu-icons' ),
					'el-icon-compass'            => __( 'Compass', 'menu-icons' ),
					'el-icon-compass-alt'        => __( 'Compass', 'menu-icons' ),
					'el-icon-credit-card'        => __( 'Credit Card', 'menu-icons' ),
					'el-icon-css'                => 'CSS',
					'el-icon-envelope'           => __( 'Envelope', 'menu-icons' ),
					'el-icon-envelope-alt'       => __( 'Envelope', 'menu-icons' ),
					'el-icon-error'              => __( 'Error', 'menu-icons' ),
					'el-icon-error-alt'          => __( 'Error', 'menu-icons' ),
					'el-icon-exclamation-sign'   => __( 'Exclamation Sign', 'menu-icons' ),
					'el-icon-eye-close'          => __( 'Eye Close', 'menu-icons' ),
					'el-icon-eye-open'           => __( 'Eye Open', 'menu-icons' ),
					'el-icon-male'               => __( 'Male', 'menu-icons' ),
					'el-icon-female'             => __( 'Female', 'menu-icons' ),
					'el-icon-file'               => __( 'File', 'menu-icons' ),
					'el-icon-file-alt'           => __( 'File', 'menu-icons' ),
					'el-icon-film'               => __( 'Film', 'menu-icons' ),
					'el-icon-filter'             => __( 'Filter', 'menu-icons' ),
					'el-icon-fire'               => __( 'Fire', 'menu-icons' ),
					'el-icon-flag'               => __( 'Flag', 'menu-icons' ),
					'el-icon-flag-alt'           => __( 'Flag', 'menu-icons' ),
					'el-icon-folder'             => __( 'Folder', 'menu-icons' ),
					'el-icon-folder-open'        => __( 'Folder Open', 'menu-icons' ),
					'el-icon-folder-close'       => __( 'Folder Close', 'menu-icons' ),
					'el-icon-folder-sign'        => __( 'Folder Sign', 'menu-icons' ),
					'el-icon-font'               => __( 'Font', 'menu-icons' ),
					'el-icon-fontsize'           => __( 'Font Size', 'menu-icons' ),
					'el-icon-gift'               => __( 'Gift', 'menu-icons' ),
					'el-icon-glass'              => __( 'Glass', 'menu-icons' ),
					'el-icon-glasses'            => __( 'Glasses', 'menu-icons' ),
					'el-icon-globe'              => __( 'Globe', 'menu-icons' ),
					'el-icon-globe-alt'          => __( 'Globe', 'menu-icons' ),
					'el-icon-graph'              => __( 'Graph', 'menu-icons' ),
					'el-icon-graph-alt'          => __( 'Graph', 'menu-icons' ),
					'el-icon-group'              => __( 'Group', 'menu-icons' ),
					'el-icon-group-alt'          => __( 'Group', 'menu-icons' ),
					'el-icon-guidedog'           => __( 'Guide Dog', 'menu-icons' ),
					'el-icon-hand-up'            => __( 'Hand Up', 'menu-icons' ),
					'el-icon-hand-down'          => __( 'Hand Down', 'menu-icons' ),
					'el-icon-hand-left'          => __( 'Hand Left', 'menu-icons' ),
					'el-icon-hand-right'         => __( 'Hand Right', 'menu-icons' ),
					'el-icon-hdd'                => __( 'HDD', 'menu-icons' ),
					'el-icon-headphones'         => __( 'Headphones', 'menu-icons' ),
					'el-icon-hearing-impaired'   => __( 'Hearing Impaired', 'menu-icons' ),
					'el-icon-heart'              => __( 'Heart', 'menu-icons' ),
					'el-icon-heart-alt'          => __( 'Heart', 'menu-icons' ),
					'el-icon-heart-empty'        => __( 'Heart Empty', 'menu-icons' ),
					'el-icon-hourglass'          => __( 'Hourglass', 'menu-icons' ),
					'el-icon-idea'               => __( 'Idea', 'menu-icons' ),
					'el-icon-idea-alt'           => __( 'Idea', 'menu-icons' ),
					'el-icon-inbox'              => __( 'Inbox', 'menu-icons' ),
					'el-icon-inbox-alt'          => __( 'Inbox', 'menu-icons' ),
					'el-icon-inbox-box'          => __( 'Inbox', 'menu-icons' ),
					'el-icon-info-sign'          => __( 'Info', 'menu-icons' ),
					'el-icon-key'                => __( 'Key', 'menu-icons' ),
					'el-icon-laptop'             => __( 'Laptop', 'menu-icons' ),
					'el-icon-laptop-alt'         => __( 'Laptop', 'menu-icons' ),
					'el-icon-leaf'               => __( 'Leaf', 'menu-icons' ),
					'el-icon-lines'              => __( 'Lines', 'menu-icons' ),
					'el-icon-magic'              => __( 'Magic', 'menu-icons' ),
					'el-icon-magnet'             => __( 'Magnet', 'menu-icons' ),
					'el-icon-mic'                => __( 'Mic', 'menu-icons' ),
					'el-icon-music'              => __( 'Music', 'menu-icons' ),
					'el-icon-paper-clip'         => __( 'Paper Clip', 'menu-icons' ),
					'el-icon-paper-clip-alt'     => __( 'Paper Clip', 'menu-icons' ),
					'el-icon-pencil'             => __( 'Pencil', 'menu-icons' ),
					'el-icon-pencil-alt'         => __( 'Pencil', 'menu-icons' ),
					'el-icon-person'             => __( 'Person', 'menu-icons' ),
					'el-icon-phone'              => __( 'Phone', 'menu-icons' ),
					'el-icon-phone-alt'          => __( 'Phone', 'menu-icons' ),
					'el-icon-photo'              => __( 'Photo', 'menu-icons' ),
					'el-icon-photo-alt'          => __( 'Photo', 'menu-icons' ),
					'el-icon-picture'            => __( 'Picture', 'menu-icons' ),
					'el-icon-plane'              => __( 'Plane', 'menu-icons' ),
					'el-icon-podcast'            => __( 'Podcast', 'menu-icons' ),
					'el-icon-puzzle'             => __( 'Puzzle', 'menu-icons' ),
					'el-icon-qrcode'             => __( 'QR Code', 'menu-icons' ),
					'el-icon-quotes'             => __( 'Quotes', 'menu-icons' ),
					'el-icon-quotes-alt'         => __( 'Quotes', 'menu-icons' ),
					'el-icon-random'             => __( 'Random', 'menu-icons' ),
					'el-icon-scissors'           => __( 'Scissors', 'menu-icons' ),
					'el-icon-screen'             => __( 'Screen', 'menu-icons' ),
					'el-icon-screen-alt'         => __( 'Screen', 'menu-icons' ),
					'el-icon-screenshot'         => __( 'Screenshot', 'menu-icons' ),
					'el-icon-shopping-cart'      => __( 'Shopping Cart', 'menu-icons' ),
					'el-icon-shopping-cart-sign' => __( 'Shopping Cart Sign', 'menu-icons' ),
					'el-icon-signal'             => __( 'Signal', 'menu-icons' ),
					'el-icon-smiley'             => __( 'Smiley', 'menu-icons' ),
					'el-icon-smiley-alt'         => __( 'Smiley', 'menu-icons' ),
					'el-icon-speaker'            => __( 'Speaker', 'menu-icons' ),
					'el-icon-user'               => __( 'User', 'menu-icons' ),
					'el-icon-th'                 => __( 'Thumbnails', 'menu-icons' ),
					'el-icon-th-large'           => __( 'Thumbnails (Large)', 'menu-icons' ),
					'el-icon-th-list'            => __( 'Thumbnails (List)', 'menu-icons' ),
					'el-icon-time'               => __( 'Time', 'menu-icons' ),
					'el-icon-time-alt'           => __( 'Time', 'menu-icons' ),
					'el-icon-torso'              => __( 'Torso', 'menu-icons' ),
					'el-icon-wheelchair'         => __( 'Wheelchair', 'menu-icons' ),
					'el-icon-wrench'             => __( 'Wrench', 'menu-icons' ),
					'el-icon-wrench-alt'         => __( 'Wrench', 'menu-icons' ),
					'el-icon-universal-access'   => __( 'Universal Access', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'places',
				'label' => __( 'Places', 'menu-icons' ),
				'items' => array(
					'el-icon-bookmark'       => __( 'Bookmark', 'menu-icons' ),
					'el-icon-bookmark-empty' => __( 'Bookmark Empty', 'menu-icons' ),
					'el-icon-dashboard'      => __( 'Dashboard', 'menu-icons' ),
					'el-icon-home'           => __( 'Home', 'menu-icons' ),
					'el-icon-home-alt'       => __( 'Home', 'menu-icons' ),
					'el-icon-iphone-home'    => __( 'Home (iPhone)', 'menu-icons' ),
					'el-icon-network'        => __( 'Network', 'menu-icons' ),
					'el-icon-tags'           => __( 'Tags', 'menu-icons' ),
					'el-icon-website'        => __( 'Website', 'menu-icons' ),
					'el-icon-website-alt'    => __( 'Website', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'social',
				'label' => __( 'Social', 'menu-icons' ),
				'items' => array(
					'el-icon-behance'         => 'Behance',
					'el-icon-blogger'         => 'Blogger',
					'el-icon-cloud'           => __( 'Cloud', 'menu-icons' ),
					'el-icon-cloud-alt'       => __( 'Cloud', 'menu-icons' ),
					'el-icon-delicious'       => 'Delicious',
					'el-icon-deviantart'      => 'DeviantArt',
					'el-icon-digg'            => 'Digg',
					'el-icon-dribbble'        => 'Dribbble',
					'el-icon-facebook'        => 'Facebook',
					'el-icon-facetime-video'  => 'Facetime Video',
					'el-icon-flickr'          => 'Flickr',
					'el-icon-foursquare'      => 'Foursquare',
					'el-icon-friendfeed'      => 'FriendFeed',
					'el-icon-friendfeed-rect' => 'FriendFeed',
					'el-icon-github'          => 'GitHub',
					'el-icon-github-text'     => 'GitHub',
					'el-icon-googleplus'      => 'Google+',
					'el-icon-instagram'       => 'Instagram',
					'el-icon-lastfm'          => 'Last.fm',
					'el-icon-linkedin'        => 'LinkedIn',
					'el-icon-livejournal'     => 'LiveJournal',
					'el-icon-myspace'         => 'MySpace',
					'el-icon-opensource'      => __( 'Open Source', 'menu-icons' ),
					'el-icon-path'            => 'path',
					'el-icon-picasa'          => 'Picasa',
					'el-icon-pinterest'       => 'Pinterest',
					'el-icon-rss'             => 'RSS',
					'el-icon-reddit'          => 'Reddit',
					'el-icon-skype'           => 'Skype',
					'el-icon-slideshare'      => 'Slideshare',
					'el-icon-soundcloud'      => 'SoundCloud',
					'el-icon-spotify'         => 'Spotify',
					'el-icon-stackoverflow'   => 'Stack Overflow',
					'el-icon-stumbleupon'     => 'StumbleUpon',
					'el-icon-twitter'         => 'Twitter',
					'el-icon-tumblr'          => 'Tumblr',
					'el-icon-viadeo'          => 'Viadeo',
					'el-icon-vimeo'           => 'Vimeo',
					'el-icon-vkontakte'       => 'VKontakte',
					'el-icon-w3c'             => 'W3C',
					'el-icon-wordpress'       => 'WordPress',
					'el-icon-youtube'         => 'YouTube',
				),
			),
		);
	}
}
