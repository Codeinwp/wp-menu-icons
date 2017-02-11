/* jshint node:true */
module.exports = function( grunt ) {
	grunt.initConfig({
		phpcs: {
			'default': {
				cmd: './vendor/bin/phpcs',
				args: [ '--standard=./phpcs.ruleset.xml', '-p', '-s', '-v', '--extensions=php', '.' ]
			}
		},
		uglify: {
			all: {
				files: {
					'js/admin.min.js': ['js/admin.js']
				}
			}
		},
		cssmin: {
			all: {
				files: [{
					expand: true,
					cwd: 'css/',
					src: [ '*.css', '!*.min.css' ],
					dest: 'css/',
					ext: '.min.css'
				}]
			}
		},
		makepot: {
			target: {
				options: {
					mainFile: 'menu-icons.php',
					type: 'wp-plugin',
					exclude: ['includes/library']
				}
			}
		}
	});

	// Tasks
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );

	grunt.registerMultiTask( 'phpcs', 'Runs PHP code sniffs.', function() {
		grunt.util.spawn({
			cmd: this.data.cmd,
			args: this.data.args,
			opts: { stdio: 'inherit' }
		}, this.async() );
	});

	grunt.registerTask( 'css', ['cssmin']);
	grunt.registerTask( 'js', ['uglify' ]);
	grunt.registerTask( 'i18n', ['makepot']);
	grunt.registerTask( 'default', [ 'css', 'js', 'i18n' ]);

	grunt.util.linefeed = '\n';
};
