/* jshint node:true */
module.exports = function( grunt ) {

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),
		jshint: {
			all: [
				'Gruntfile.js',
				'js/*.js',
				'!js/*.min.js',
			],
			options: {
				curly:   true,
				eqeqeq:  true,
				immed:   true,
				latedef: true,
				newcap:  true,
				noarg:   true,
				sub:     true,
				undef:   true,
				boss:    true,
				eqnull:  true,
				globals: {
					exports: true,
					module:  false
				}
			}		
		},

		uglify: {
			all: {
				files: {
					'js/admin.min.js': ['js/admin.js'],
					'js/input-dependencies.min.js': ['js/input-dependencies.js']
				}
			}
		},
		cssmin: {
			minify: {
				expand: true,
				
				cwd: 'css/',
				src: [
					'admin.css',
					'elusive.css',
					'extra.css',
					'font-awesome.css',
					'foundation-icons.css',
					'genericons.css'
				],
				
				dest: 'css/',
				ext: '.min.css'
			}
		},
		watch:  {
			
			styles: {
				files: [
					'css/admin.css',
					'css/elusive.css',
					'css/extra.css',
					'css/font-awesome.css',
					'css/genericons.css'
				],
				tasks: ['cssmin'],
				options: {
					debounceDelay: 500
				}
			},
			
			scripts: {
				files: [
					'js/admin.js',
					'js/input-dependencies.js'
				],
				tasks: ['jshint', 'uglify'],
				options: {
					debounceDelay: 500
				}
			}
		},
		clean: {
			main: ['release/<%= pkg.version %>']
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
				src: ['**/*'],
				dest: 'menu-icons/'
			}
		}
	} );
	
	// Load other tasks
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-compress');
	
	// Default task.
	
	grunt.registerTask( 'default', ['jshint', 'uglify', 'cssmin'] );
	
	
	grunt.registerTask( 'build', ['default', 'clean', 'copy', 'compress'] );

	grunt.util.linefeed = '\n';
};
