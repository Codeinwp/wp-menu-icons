/**
 * Version File for Grunt
 *
 * @package feedzy-rss-feeds-pro
 */
/* jshint node:true */
// https://github.com/kswedberg/grunt-version
module.exports = {
	options: {
		pkg: {
			version: '<%= package.version %>'
		}
	},
	project: {
		src: [
			'package.json'
		]
	},
	style: {
		options: {
			prefix: 'Version\\:\.*\\s'
		},
		src: [
			'menu-icons.php'
		]
	},
	class: {
		options: {
			prefix: '\\.*VERSION\.*\\s=\.*\\s\''
		},
		src: [
			'menu-icons.php',
		]
	}
};
