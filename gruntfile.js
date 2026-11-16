module.exports = function (grunt) {
	// Load all grunt tasks matching the ['grunt-*', '@*/grunt-*'] patterns.
	require( 'load-grunt-tasks' )( grunt, { pattern: ['grunt-*', '@*/grunt-*'] } );

	// Project configuration.
	grunt.initConfig(
		{
			pkg: grunt.file.readJSON( 'package.json' ),

			// Task for checking text domain.
			checktextdomain: {
				options: {
					text_domain: 'bp-redirect', // Change this to your text domain.
					keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
					],
				},
				files: {
					src: [
					'*.php',
					'**/*.php',
					'!node_modules/**',
					'!options/framework/**',
					'!tests/**'
					],
					expand: true
				},
			},

			// Task for CSS minification.
			cssmin: {
				options: {
					keepSpecialComments: 0,
					rebase: false
				},
				admin: {
					files: [{
						expand: true,
						cwd: 'admin/assets/css/', // Admin CSS sources and their -rtl copies.
						src: ['*.css', '!*.min.css'],
						dest: 'admin/assets/css/',
						ext: '.min.css',
						extDot: 'last',
					}],
				},
			},

			// Task for JavaScript minification.
			uglify: {
				admin: {
					options: {
						mangle: false, // Prevents variable name mangling.
					},
					files: [{
						expand: true,
						cwd: 'admin/assets/js/', // Source directory for admin JS files.
						src: ['*.js', '!*.min.js', '!vendor/*.js'], // Minify all admin JS files except already minified ones.
						dest: 'admin/assets/js/', // Destination directory for minified admin JS.
						ext: '.min.js', // Extension for minified files.
					}],
				},
			},

			// Task for watching file changes.
			watch: {
				adminCss: {
					files: ['admin/assets/css/*.css', '!admin/assets/css/*.min.css', '!admin/assets/css/*-rtl.css'], // Watch for changes in admin CSS files.
					tasks: ['rtlcss', 'cssmin:admin'], // Run admin CSS minification task.
				},
				adminJs: {
					files: ['admin/assets/js/*.js', '!admin/assets/js/*.min.js'], // Watch for changes in admin JS files.
					tasks: ['uglify:admin'], // Run admin JS minification task.
				},
				php: {
					files: ['**/*.php'], // Watch for changes in PHP files.
					tasks: ['checktextdomain'], // Run text domain check.
				},
			},

			// Task for generating RTL CSS.
			rtlcss: {
				myTask: {
					options: {
						// Generate source maps.
						map: { inline: false },
						// RTL CSS options.
						opts: {
							clean: false
						},
						// RTL CSS plugins.
						plugins: [],
						// Save unmodified files.
						saveUnmodified: true,
					},
					files: [
					{
						expand: true,
						cwd: 'admin/assets/css/', // Source directory for admin CSS.
						src: ['**/*.css', '!**/*.min.css', '!**/*-rtl.css', '!vendor/**/*.css'], // Source files, excluding vendor CSS.
						dest: 'admin/assets/css/', // Destination directory for admin RTL CSS.
						ext: '-rtl.css',
						flatten: true // Prevents creating subdirectories.
					}
					]
				}
			},
			shell: {
				wpcli: {
					command: 'wp i18n make-pot . languages/bp-redirect.pot || echo "WP-CLI not available, skipping POT generation"',
				}
			},

			// Task for cleaning dist directory.
			clean: {
				dist: ['dist/']
			},

			// Task for copying files to dist.
			copy: {
				dist: {
					files: [{
						expand: true,
						src: [
						'**',
						'!node_modules/**',
						'!dist/**',
						'!docs/**',
						'!marketing/**',
						'!.git/**',
						'!.gitignore',
						'!.editorconfig',
						'!.eslintrc',
						'!gruntfile.js',
						'!package.json',
						'!package-lock.json',
						'!readme.md',
						'!QA-TESTING.md',
						'!composer.json',
						'!composer.lock',
						'!phpcs.xml',
						'!phpunit.xml',
						'!tests/**',
						'!*.log',
						'!*.map',
						'!*.csv',
						'!**/*.md',
						'!audit/**',
						'!.phpcs.xml.dist',
						'!.gitmodules',
						'!vendor/**',
						'vendor/easy-digital-downloads/**',
						// Excludes after the re-include above, or the SDK's dev files ship.
						// Its vendor/autoload.php and vendor/composer stay: edd-sl-sdk.php needs them.
						'!vendor/easy-digital-downloads/**/.git',
						'!vendor/easy-digital-downloads/**/{package.json,package-lock.json,composer.json,composer.lock,phpunit.xml,webpack.config.js}',
						'!vendor/easy-digital-downloads/**/*.md',
						'!**/*.map'
						],
						dest: 'dist/<%= pkg.name %>/'
					}]
				}
			},

			// Task for creating zip.
			compress: {
				dist: {
					options: {
						archive: 'dist/<%= pkg.name %>-<%= pkg.version %>.zip',
						mode: 'zip'
					},
					files: [{
						expand: true,
						cwd: 'dist/',
						src: ['<%= pkg.name %>/**'],
						dest: ''
					}]
				}
			},

			// WordPress internationalization.
			makepot: {
				target: {
					options: {
						domainPath: '/languages',
						mainFile: 'bp-redirect.php',
						potFilename: 'bp-redirect.pot',
						potHeaders: {
							poedit: true,
							'x-poedit-keywordslist': true
						},
						type: 'wp-plugin',
						updateTimestamp: true,
						processPot: function (pot, options) {
							pot.headers['report-msgid-bugs-to'] = 'https://wbcomdesigns.com/contact/';
							pot.headers['language-team']        = 'LANGUAGE <support@wbcomdesigns.com>';
							return pot;
						}
					}
				}
			}
		}
	);

	// Note: All plugins are loaded automatically by load-grunt-tasks at the top.

	// Register tasks.
	grunt.registerTask( 'default', ['build'] );
	grunt.registerTask( 'build', ['rtlcss', 'cssmin', 'uglify'] );
	grunt.registerTask( 'dev', ['build', 'watch'] );
	grunt.registerTask( 'release', ['checktextdomain', 'rtlcss', 'cssmin', 'uglify', 'shell'] );
	grunt.registerTask( 'i18n', ['checktextdomain', 'makepot'] );
	// A fresh clone has an empty SDK submodule; a zip built from it ships with no updater.
	grunt.registerTask( 'sdk-check', function () {
		if ( ! grunt.file.exists( 'vendor/easy-digital-downloads/edd-sl-sdk/edd-sl-sdk.php' ) ) {
			grunt.fail.fatal( 'EDD SL SDK missing. Run: git submodule update --init' );
		}
	} );
	// grunt-contrib-clean breaks under the rimraf 6 override; Grunt's own delete needs no rimraf.
	grunt.registerTask( 'clean-dist', function () {
		if ( grunt.file.exists( 'dist' ) ) {
			grunt.file.delete( 'dist' );
		}
	} );
	grunt.registerTask( 'dist', ['sdk-check', 'build', 'shell', 'clean-dist', 'copy:dist', 'compress:dist'] );
};