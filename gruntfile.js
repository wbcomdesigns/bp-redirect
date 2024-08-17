'use strict';

module.exports = function (grunt) {

	// Load all grunt tasks matching the `grunt-*` pattern
	// Reference: https://npmjs.org/package/load-grunt-tasks
	require('load-grunt-tasks')(grunt);

	// Initialize configuration
	grunt.initConfig({

		// Check text domain
		checktextdomain: {
			options: {
				text_domain: ['bp-redirect'], // Specify allowed text domain(s)
				keywords: [ // List of gettext function specifications
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
				]
			},
			target: {
				files: [{
					expand: true,
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**',
						'!options/framework/**',
						'!tests/**'
					]
				}]
			}
		},

		// Generate POT files
		makepot: {
			target: {
				options: {
					cwd: '.', // Directory of files to internationalize
					domainPath: 'languages/', // Where to save the POT file
					exclude: ['node_modules/*', 'options/framework/*'], // Files or directories to ignore
					mainFile: 'index.php', // Main project file
					potFilename: 'bp-redirect.pot', // Name of the POT file
					potHeaders: { // Headers to add to the generated POT file
						poedit: true, // Include common Poedit headers
						'Last-Translator': 'Varun Dubey',
						'Language-Team': 'Wbcom Designs',
						'report-msgid-bugs-to': '',
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions
					},
					type: 'wp-plugin', // Type of project (wp-plugin or wp-theme)
					updateTimestamp: true // Whether to update the POT-Creation-Date even without other changes
				}
			}
		}
	});

	// Register default tasks
	grunt.registerTask('default', ['checktextdomain', 'makepot']);
};
