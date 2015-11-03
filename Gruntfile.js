/* jshint node:true */
module.exports = function( grunt ) {
	grunt.initConfig( {
		browserify: {
			media: {
				files: {
					'js/src/media.js': 'js/src/media/manifest.js'
				}
			}
		},
		jshint: {
			grunt: {
				src: [ 'Gruntfile.js' ]
			},
			settings: {
				src: [ 'js/settings.js' ]
			},
			media: {
				options: {
					browserify: true
				},
				src: [ 'js/src/media/**/*.js' ]
			},
			util: {
				src: [ 'js/src/util.js' ]
			},
			options: grunt.file.readJSON( '.jshintrc' )
		},
		concat: {
			options: {
				separator: '\n'
			},
			dist: {
				src: [ 'js/src/media.js', 'js/src/util.js' ],
				dest: 'js/picker.js'
			}
		},
		uglify: {
			all: {
				files: {
					'js/settings.min.js': [ 'js/settings.js' ],
					'js/picker.min.js': [ 'js/picker.js' ]
				}
			}
		},
		cssmin: {
			all: {
				files: [ {
					expand: true,
					cwd: 'css/',
					src: [ '*.css', '!*.min.css' ],
					dest: 'css/',
					ext: '.min.css'
				} ]
			}
		},
		_watch:  {
			styles: {
				files: [ 'css/*.css', '!css/*.css' ],
				tasks: [ 'cssmin' ],
				options: {
					debounceDelay: 500,
					interval:      2000
				}
			},
			scripts: {
				files: [
					'js/media/**/*.js',
					'js/util.js',
					'js/settings.js'
				],
				tasks: [ 'js' ],
				options: {
					debounceDelay: 500,
					interval:      2000
				}
			}
		},
		clean: {
			main: [ 'release/<%= pkg.version %>' ]
		},
		copy: {

			// Copy the plugin to a versioned release directory
			main: {
				src:  [
					'**',
					'!node_modules/**',
					'!release/**',
					'!.git/**',
					'!.sass-cache/**',
					'!Gruntfile.js',
					'!package.json',
					'!.gitattributes',
					'!.gitignore',
					'!.gitmodules',
					'!readme.md'
				],
				dest: 'release/<%= pkg.version %>/'
			}
		},
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './release/menu-icons.<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'release/<%= pkg.version %>/',
				src: [ '**/*' ],
				dest: 'menu-icons/'
			}
		},
		makepot: {
			target: {
				options: {
					mainFile: 'menu-icons.php',
					type: 'wp-plugin'
				}
			}
		}
	} );

	// Tasks
	grunt.loadNpmTasks( 'grunt-browserify' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );

	grunt.renameTask( 'watch', '_watch' );
	grunt.registerTask( 'watch', function() {
		if ( ! this.args.length || this.args.indexOf( 'browserify' ) > -1 ) {
			grunt.config( 'browserify.options', {
				browserifyOptions: {
					debug: true
				},
				watch: true
			} );

			grunt.task.run( 'browserify' );
		}

		grunt.task.run( '_' + this.nameArgs );
	} );

	grunt.registerTask( 'css', [ 'cssmin' ] );
	grunt.registerTask( 'js', [ 'browserify', 'jshint', 'concat', 'uglify' ] );
	grunt.registerTask( 'i18n', [ 'makepot' ] );
	grunt.registerTask( 'default', [ 'css', 'js' ] );
	grunt.registerTask( 'build', [ 'default', 'clean', 'copy', 'compress' ] );

	grunt.util.linefeed = '\n';
};
