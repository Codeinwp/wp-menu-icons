/* jshint node:true */
/* global require */

module.exports = function (grunt) {
	'use strict';
	grunt.initConfig({
		wp_readme_to_markdown: {
			files: {
				'readme.md': 'readme.txt'
			},
		},
		version: {

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
		}
	});
	grunt.loadNpmTasks('grunt-version');
	grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
};
