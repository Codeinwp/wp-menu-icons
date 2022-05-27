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
		},
		cssmin: { 
			sitecss: { 
				options: { 
					banner: '' 
				},
				files: {
					'css/admin.min.css': 'css/admin.css',
					'css/extra.min.css': 'css/extra.css',
					'css/dashboard-notice.min.css': 'css/dashboard-notice.css'
				}
			}
		},
		uglify: {
			options: { 
				compress: true 
			},
			dist: {
				files: {
					'js/admin.min.js': 'js/admin.js'
				}
			}
		}
	});
	grunt.loadNpmTasks('grunt-version');
	grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-qunit');
	
	grunt.registerTask('default', ['uglify', 'cssmin']);
};
