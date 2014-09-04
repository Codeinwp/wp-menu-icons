<?php
/**
 * Foundation Icons
 *
 * @package Menu_Icons
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */


require_once dirname( __FILE__ ) . '/type-fonts.php';

/**
 * Icon type: Foundation Icons
 *
 * @since   0.5.0
 * @version 0.1.0
 */
class Menu_Icons_Type_Foundation extends Menu_Icons_Type_Fonts {

	/**
	 * Holds icon type
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $type = 'foundation-icons';

	/**
	 * Holds icon label
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $label = 'Foundation';

	/**
	 * Holds icon version
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '3.0';


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
			'%scss/%s%s.css',
			Menu_Icons::get( 'url' ),
			$this->type,
			Menu_Icons::get_script_suffix()
		);

		parent::__construct();
	}


	/**
	 * Foundation's icons names
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function get_names() {
		return array(
			array(
				'key'   => 'accessibility',
				'label' => __( 'Accessibility', 'menu-icons' ),
				'items' => array(
					'fi-asl'              => __( 'ASL', 'menu-icons' ),
					'fi-blind'            => __( 'Blind', 'menu-icons' ),
					'fi-braille'          => __( 'Braille', 'menu-icons' ),
					'fi-closed-caption'   => __( 'Closed Caption', 'menu-icons' ),
					'fi-elevator'         => __( 'Elevator', 'menu-icons' ),
					'fi-guide-dog'        => __( 'Guide Dog', 'menu-icons' ),
					'fi-hearing-aid'      => __( 'Hearing Aid', 'menu-icons' ),
					'fi-universal-access' => __( 'Universal Access', 'menu-icons' ),
					'fi-male'             => __( 'Male', 'menu-icons' ),
					'fi-female'           => __( 'Female', 'menu-icons' ),
					'fi-male-female'      => __( 'Male & Female', 'menu-icons' ),
					'fi-male-symbol'      => __( 'Male Symbol', 'menu-icons' ),
					'fi-female-symbol'    => __( 'Female Symbol', 'menu-icons' ),
					'fi-wheelchair'       => __( 'Wheelchair', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'arrows',
				'label' => __( 'Arrows', 'menu-icons' ),
				'items' => array(
					'fi-arrow-up'        => __( 'Arrow: Up', 'menu-icons' ),
					'fi-arrow-down'      => __( 'Arrow: Down', 'menu-icons' ),
					'fi-arrow-left'      => __( 'Arrow: Left', 'menu-icons' ),
					'fi-arrow-right'     => __( 'Arrow: Right', 'menu-icons' ),
					'fi-arrows-out'      => __( 'Arrows: Out', 'menu-icons' ),
					'fi-arrows-in'       => __( 'Arrows: In', 'menu-icons' ),
					'fi-arrows-expand'   => __( 'Arrows: Expand', 'menu-icons' ),
					'fi-arrows-compress' => __( 'Arrows: Compress', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'devices',
				'label' => __( 'Devices', 'menu-icons' ),
				'items' => array(
					'fi-bluetooth'        => __( 'Bluetooth', 'menu-icons' ),
					'fi-camera'           => __( 'Camera', 'menu-icons' ),
					'fi-compass'          => __( 'Compass', 'menu-icons' ),
					'fi-laptop'           => __( 'Laptop', 'menu-icons' ),
					'fi-megaphone'        => __( 'Megaphone', 'menu-icons' ),
					'fi-microphone'       => __( 'Microphone', 'menu-icons' ),
					'fi-mobile'           => __( 'Mobile', 'menu-icons' ),
					'fi-mobile-signal'    => __( 'Mobile Signal', 'menu-icons' ),
					'fi-monitor'          => __( 'Monitor', 'menu-icons' ),
					'fi-tablet-portrait'  => __( 'Tablet: Portrait', 'menu-icons' ),
					'fi-tablet-landscape' => __( 'Tablet: Landscape', 'menu-icons' ),
					'fi-telephone'        => __( 'Telephone', 'menu-icons' ),
					'fi-usb'              => __( 'USB', 'menu-icons' ),
					'fi-video'              => __( 'Video', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'ecommerce',
				'label' => __( 'Ecommerce', 'menu-icons' ),
				'items' => array(
					'fi-bitcoin'           => __( 'Bitcoin', 'menu-icons' ),
					'fi-bitcoin-circle'    => __( 'Bitcoin', 'menu-icons' ),
					'fi-dollar'            => __( 'Dollar', 'menu-icons' ),
					'fi-euro'              => __( 'EURO', 'menu-icons' ),
					'fi-pound'             => __( 'Pound', 'menu-icons' ),
					'fi-yen'               => __( 'Yen', 'menu-icons' ),
					'fi-burst'             => __( 'Burst', 'menu-icons' ),
					'fi-burst-new'         => __( 'Burst: New', 'menu-icons' ),
					'fi-burst-sale'        => __( 'Burst: Sale', 'menu-icons' ),
					'fi-credit-card'       => __( 'Credit Card', 'menu-icons' ),
					'fi-dollar-bill'       => __( 'Dollar Bill', 'menu-icons' ),
					'fi-paypal'            => 'PayPal',
					'fi-price-tag'         => __( 'Price Tag', 'menu-icons' ),
					'fi-pricetag-multiple' => __( 'Price Tag: Multiple', 'menu-icons' ),
					'fi-shopping-bag'      => __( 'Shopping Bag', 'menu-icons' ),
					'fi-shopping-cart'     => __( 'Shopping Cart', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'editor',
				'label' => __( 'Editor', 'menu-icons' ),
				'items' => array(
					'fi-bold'             => __( 'Bold', 'menu-icons' ),
					'fi-italic'           => __( 'Italic', 'menu-icons' ),
					'fi-underline'        => __( 'Underline', 'menu-icons' ),
					'fi-strikethrough'    => __( 'Strikethrough', 'menu-icons' ),
					'fi-text-color'       => __( 'Text Color', 'menu-icons' ),
					'fi-background-color' => __( 'Background Color', 'menu-icons' ),
					'fi-superscript'      => __( 'Superscript', 'menu-icons' ),
					'fi-subscript'        => __( 'Subscript', 'menu-icons' ),
					'fi-align-left'       => __( 'Align Left', 'menu-icons' ),
					'fi-align-center'     => __( 'Align Center', 'menu-icons' ),
					'fi-align-right'      => __( 'Align Right', 'menu-icons' ),
					'fi-align-justify'    => __( 'Justify', 'menu-icons' ),
					'fi-list-number'      => __( 'List: Number', 'menu-icons' ),
					'fi-list-bullet'      => __( 'List: Bullet', 'menu-icons' ),
					'fi-indent-more'      => __( 'Indent', 'menu-icons' ),
					'fi-indent-less'      => __( 'Outdent', 'menu-icons' ),
					'fi-page-add'         => __( 'Add Page', 'menu-icons' ),
					'fi-page-copy'        => __( 'Copy Page', 'menu-icons' ),
					'fi-page-multiple'    => __( 'Duplicate Page', 'menu-icons' ),
					'fi-page-delete'      => __( 'Delete Page', 'menu-icons' ),
					'fi-page-remove'      => __( 'Remove Page', 'menu-icons' ),
					'fi-page-edit'        => __( 'Edit Page', 'menu-icons' ),
					'fi-page-export'      => __( 'Export', 'menu-icons' ),
					'fi-page-export-csv'  => __( 'Export to CSV', 'menu-icons' ),
					'fi-page-export-pdf'  => __( 'Export to PDF', 'menu-icons' ),
					'fi-page-filled'      => __( 'Fill Page', 'menu-icons' ),
					'fi-crop'             => __( 'Crop', 'menu-icons' ),
					'fi-filter'           => __( 'Filter', 'menu-icons' ),
					'fi-paint-bucket'     => __( 'Paint Bucket', 'menu-icons' ),
					'fi-photo'            => __( 'Photo', 'menu-icons' ),
					'fi-print'            => __( 'Print', 'menu-icons' ),
					'fi-save'             => __( 'Save', 'menu-icons' ),
					'fi-link'             => __( 'Link', 'menu-icons' ),
					'fi-unlink'           => __( 'Unlink', 'menu-icons' ),
					'fi-quote'            => __( 'Quote', 'menu-icons' ),
					'fi-page-search'      => __( 'Search in Page', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'file-types',
				'label' => __( 'File Types', 'menu-icons' ),
				'items' => array(
					'fi-page'     => __( 'File', 'menu-icons' ),
					'fi-page-csv' => __( 'CSV', 'menu-icons' ),
					'fi-page-doc' => __( 'Doc', 'menu-icons' ),
					'fi-page-pdf' => __( 'PDF', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'general',
				'label' => __( 'General', 'menu-icons' ),
				'items' => array(
					'fi-address-book'         => __( 'Addressbook', 'menu-icons' ),
					'fi-alert'                => __( 'Alert', 'menu-icons' ),
					'fi-annotate'             => __( 'Annotate', 'menu-icons' ),
					'fi-archive'              => __( 'Archive', 'menu-icons' ),
					'fi-bookmark'             => __( 'Bookmark', 'menu-icons' ),
					'fi-calendar'             => __( 'Calendar', 'menu-icons' ),
					'fi-clock'                => __( 'Clock', 'menu-icons' ),
					'fi-cloud'                => __( 'Cloud', 'menu-icons' ),
					'fi-comment'              => __( 'Comment', 'menu-icons' ),
					'fi-comment-minus'        => __( 'Comment: Minus', 'menu-icons' ),
					'fi-comment-quotes'       => __( 'Comment: Quotes', 'menu-icons' ),
					'fi-comment-video'        => __( 'Comment: Video', 'menu-icons' ),
					'fi-comments'             => __( 'Comments', 'menu-icons' ),
					'fi-contrast'             => __( 'Contrast', 'menu-icons' ),
					'fi-database'             => __( 'Database', 'menu-icons' ),
					'fi-folder'               => __( 'Folder', 'menu-icons' ),
					'fi-folder-add'           => __( 'Folder: Add', 'menu-icons' ),
					'fi-folder-lock'          => __( 'Folder: Lock', 'menu-icons' ),
					'fi-eye'                  => __( 'Eye', 'menu-icons' ),
					'fi-heart'                => __( 'Heart', 'menu-icons' ),
					'fi-plus'                 => __( 'Plus', 'menu-icons' ),
					'fi-minus'                => __( 'Minus', 'menu-icons' ),
					'fi-minus-circle'         => __( 'Minus', 'menu-icons' ),
					'fi-x'                    => __( 'X', 'menu-icons' ),
					'fi-x-circle'             => __( 'X', 'menu-icons' ),
					'fi-check'                => __( 'Check', 'menu-icons' ),
					'fi-checkbox'             => __( 'Check', 'menu-icons' ),
					'fi-download'             => __( 'Download', 'menu-icons' ),
					'fi-upload'               => __( 'Upload', 'menu-icons' ),
					'fi-upload-cloud'         => __( 'Upload to Cloud', 'menu-icons' ),
					'fi-flag'                 => __( 'Flag', 'menu-icons' ),
					'fi-foundation'           => __( 'Foundation', 'menu-icons' ),
					'fi-graph-bar'            => __( 'Graph: Bar', 'menu-icons' ),
					'fi-graph-horizontal'     => __( 'Graph: Horizontal', 'menu-icons' ),
					'fi-graph-pie'            => __( 'Graph: Pie', 'menu-icons' ),
					'fi-graph-trend'          => __( 'Graph: Trend', 'menu-icons' ),
					'fi-home'                 => __( 'Home', 'menu-icons' ),
					'fi-layout'               => __( 'Layout', 'menu-icons' ),
					'fi-like'                 => __( 'Like', 'menu-icons' ),
					'fi-dislike'              => __( 'Dislike', 'menu-icons' ),
					'fi-list'                 => __( 'List', 'menu-icons' ),
					'fi-list-thumbnails'      => __( 'List: Thumbnails', 'menu-icons' ),
					'fi-lock'                 => __( 'Lock', 'menu-icons' ),
					'fi-unlock'               => __( 'Unlock', 'menu-icons' ),
					'fi-marker'               => __( 'Marker', 'menu-icons' ),
					'fi-magnifying-glass'     => __( 'Magnifying Glass', 'menu-icons' ),
					'fi-refresh'              => __( 'Refresh', 'menu-icons' ),
					'fi-paperclip'            => __( 'Paperclip', 'menu-icons' ),
					'fi-pencil'               => __( 'Pencil', 'menu-icons' ),
					'fi-play-video'           => __( 'Play Video', 'menu-icons' ),
					'fi-results'              => __( 'Results', 'menu-icons' ),
					'fi-results-demographics' => __( 'Results: Demographics', 'menu-icons' ),
					'fi-rss'                  => __( 'RSS', 'menu-icons' ),
					'fi-share'                => __( 'Share', 'menu-icons' ),
					'fi-sound'                => __( 'Sound', 'menu-icons' ),
					'fi-star'                 => __( 'Star', 'menu-icons' ),
					'fi-thumbnails'           => __( 'Thumbnails', 'menu-icons' ),
					'fi-trash'                => __( 'Trash', 'menu-icons' ),
					'fi-web'                  => __( 'Web', 'menu-icons' ),
					'fi-widget'               => __( 'Widget', 'menu-icons' ),
					'fi-wrench'               => __( 'Wrench', 'menu-icons' ),
					'fi-zoom-out'             => __( 'Zoom Out', 'menu-icons' ),
					'fi-zoom-in'              => __( 'Zoom In', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'media-control',
				'label' => __( 'Media Controls', 'menu-icons' ),
				'items' => array(
					'fi-record'        => __( 'Record', 'menu-icons' ),
					'fi-play-circle'   => __( 'Play', 'menu-icons' ),
					'fi-play'          => __( 'Play', 'menu-icons' ),
					'fi-pause'         => __( 'Pause', 'menu-icons' ),
					'fi-stop'          => __( 'Stop', 'menu-icons' ),
					'fi-previous'      => __( 'Previous', 'menu-icons' ),
					'fi-rewind'        => __( 'Rewind', 'menu-icons' ),
					'fi-fast-forward'  => __( 'Fast Forward', 'menu-icons' ),
					'fi-next'          => __( 'Next', 'menu-icons' ),
					'fi-next'          => __( 'Next', 'menu-icons' ),
					'fi-volume'        => __( 'Volume', 'menu-icons' ),
					'fi-volume-none'   => __( 'Volume: Low', 'menu-icons' ),
					'fi-volume-strike' => __( 'Volume: Mute', 'menu-icons' ),
					'fi-loop'          => __( 'Loop', 'menu-icons' ),
					'fi-shuffle'       => __( 'Shuffle', 'menu-icons' ),
					'fi-eject'         => __( 'Eject', 'menu-icons' ),
					'fi-rewind-ten'    => __( 'Rewind 10', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'misc',
				'label' => __( 'Miscellaneous', 'menu-icons' ),
				'items' => array(
					'fi-anchor'            => __( 'Anchor', 'menu-icons' ),
					'fi-asterisk'          => __( 'Asterisk', 'menu-icons' ),
					'fi-at-sign'           => __( '@', 'menu-icons' ),
					'fi-battery-full'      => __( 'Battery: Full', 'menu-icons' ),
					'fi-battery-half'      => __( 'Battery: Half', 'menu-icons' ),
					'fi-battery-empty'     => __( 'Battery: Empty', 'menu-icons' ),
					'fi-book'              => __( 'Book', 'menu-icons' ),
					'fi-book-bookmark'     => __( 'Bookmark', 'menu-icons' ),
					'fi-clipboard'         => __( 'Clipboard', 'menu-icons' ),
					'fi-clipboard-pencil'  => __( 'Clipboard: Pencil', 'menu-icons' ),
					'fi-clipboard-notes'   => __( 'Clipboard: Notes', 'menu-icons' ),
					'fi-clipboard'         => __( 'Clipboard', 'menu-icons' ),
					'fi-crown'             => __( 'Crown', 'menu-icons' ),
					'fi-die-one'           => __( 'Dice: 1', 'menu-icons' ),
					'fi-die-two'           => __( 'Dice: 2', 'menu-icons' ),
					'fi-die-three'         => __( 'Dice: 3', 'menu-icons' ),
					'fi-die-four'          => __( 'Dice: 4', 'menu-icons' ),
					'fi-die-five'          => __( 'Dice: 5', 'menu-icons' ),
					'fi-die-six'           => __( 'Dice: 6', 'menu-icons' ),
					'fi-safety-cone'       => __( 'Cone', 'menu-icons' ),
					'fi-first-aid'         => __( 'Firs Aid', 'menu-icons' ),
					'fi-foot'              => __( 'Foot', 'menu-icons' ),
					'fi-info'              => __( 'Info', 'menu-icons' ),
					'fi-key'               => __( 'Key', 'menu-icons' ),
					'fi-lightbulb'         => __( 'Lightbulb', 'menu-icons' ),
					'fi-map'               => __( 'Map', 'menu-icons' ),
					'fi-mountains'         => __( 'Mountains', 'menu-icons' ),
					'fi-music'             => __( 'Music', 'menu-icons' ),
					'fi-no-dogs'           => __( 'No Dogs', 'menu-icons' ),
					'fi-no-smoking'        => __( 'No Smoking', 'menu-icons' ),
					'fi-paw'               => __( 'Paw', 'menu-icons' ),
					'fi-power'             => __( 'Power', 'menu-icons' ),
					'fi-prohibited'        => __( 'Prohibited', 'menu-icons' ),
					'fi-projection-screen' => __( 'Projection Screen', 'menu-icons' ),
					'fi-puzzle'            => __( 'Puzzle', 'menu-icons' ),
					'fi-sheriff-badge'     => __( 'Sheriff Badge', 'menu-icons' ),
					'fi-shield'            => __( 'Shield', 'menu-icons' ),
					'fi-skull'             => __( 'Skull', 'menu-icons' ),
					'fi-target'            => __( 'Target', 'menu-icons' ),
					'fi-target-two'        => __( 'Target', 'menu-icons' ),
					'fi-ticket'            => __( 'Ticket', 'menu-icons' ),
					'fi-trees'             => __( 'Trees', 'menu-icons' ),
					'fi-trophy'            => __( 'Trophy', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'people',
				'label' => __( 'People', 'menu-icons' ),
				'items' => array(
					'fi-torso'              => __( 'Torso', 'menu-icons' ),
					'fi-torso-business'     => __( 'Torso: Business', 'menu-icons' ),
					'fi-torso-female'       => __( 'Torso: Female', 'menu-icons' ),
					'fi-torsos'             => __( 'Torsos', 'menu-icons' ),
					'fi-torsos-all'         => __( 'Torsos: All', 'menu-icons' ),
					'fi-torsos-all-female'  => __( 'Torsos: All Female', 'menu-icons' ),
					'fi-torsos-male-female' => __( 'Torsos: Male & Female', 'menu-icons' ),
					'fi-torsos-female-male' => __( 'Torsos: Female & Male', 'menu-icons' ),
				),
			),
			array(
				'key'   => 'social',
				'label' => __( 'Social/Brand', 'menu-icons' ),
				'items' => array(
					'fi-social-500px'          => '500px',
					'fi-social-adobe'          => 'Adobe',
					'fi-social-amazon'         => 'Amazon',
					'fi-social-android'        => 'Android',
					'fi-social-apple'          => 'Apple',
					'fi-social-behance'        => 'Behance',
					'fi-social-bing'           => 'bing',
					'fi-social-blogger'        => 'Blogger',
					'fi-css3'                  => 'CSS3',
					'fi-social-delicious'      => 'Delicious',
					'fi-social-designer-news'  => 'Designer News',
					'fi-social-deviant-art'    => 'deviantArt',
					'fi-social-deviant-art'    => 'deviantArt',
					'fi-social-digg'           => 'Digg',
					'fi-social-dribbble'       => 'dribbble',
					'fi-social-drive'          => 'Drive',
					'fi-social-dropbox'        => 'DropBox',
					'fi-social-evernote'       => 'Evernote',
					'fi-social-facebook'       => 'Facebook',
					'fi-social-flickr'         => 'flickr',
					'fi-social-forrst'         => 'forrst',
					'fi-social-foursquare'     => 'Foursquare',
					'fi-social-game-center'    => 'Game Center',
					'fi-social-github'         => 'GitHub',
					'fi-social-google-plus'    => 'Google+',
					'fi-social-hacker-news'    => 'Hacker News',
					'fi-social-hi5'            => 'hi5',
					'fi-html5'                 => 'HTML5',
					'fi-social-instagram'      => 'Instagram',
					'fi-social-joomla'         => 'Joomla!',
					'fi-social-lastfm'         => 'last.fm',
					'fi-social-linkedin'       => 'LinkedIn',
					'fi-social-medium'         => 'Medium',
					'fi-social-myspace'        => 'My Space',
					'fi-social-orkut'          => 'Orkut',
					'fi-social-path'           => 'path',
					'fi-social-picasa'         => 'Picasa',
					'fi-social-pinterest'      => 'Pinterest',
					'fi-social-rdio'           => 'rdio',
					'fi-social-reddit'         => 'reddit',
					'fi-social-skype'          => 'Skype',
					'fi-social-skillshare'     => 'SkillShare',
					'fi-social-smashing-mag'   => 'Smashing Mag.',
					'fi-social-snapchat'       => 'Snapchat',
					'fi-social-spotify'        => 'Spotify',
					'fi-social-squidoo'        => 'Squidoo',
					'fi-social-stack-overflow' => 'StackOverflow',
					'fi-social-steam'          => 'Steam',
					'fi-social-stumbleupon'    => 'StumbleUpon',
					'fi-social-treehouse'      => 'TreeHouse',
					'fi-social-tumblr'         => 'Tumblr',
					'fi-social-twitter'        => 'Twitter',
					'fi-social-windows'        => 'Windows',
					'fi-social-xbox'           => 'XBox',
					'fi-social-yahoo'          => 'Yahoo!',
					'fi-social-yelp'           => 'Yelp',
					'fi-social-youtube'        => 'YouTube',
					'fi-social-zerply'         => 'Zerply',
					'fi-social-zurb'           => 'Zurb',
				),
			),
		);
	}
}
