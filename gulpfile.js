'use strict';

require('es6-promise').polyfill();
let gulp = require('gulp'),
		plumber = require('gulp-plumber'),
		notify = require('gulp-notify'),
		sassLint = require('gulp-sass-lint'),
		sass = require('gulp-sass'),
		autoprefixer = require('gulp-autoprefixer'),
		cssmin = require('gulp-cssmin');

/**
 * Style
 */
gulp.task('style', () => {
	gulp.src(['sass/**/*.scss', '!sass/**/_*.scss'])
		.pipe(plumber({
			errorHandler: notify.onError('<%= error.message %>')
		}))
		.pipe(sassLint())
		.pipe(sassLint.format())
		.pipe(sassLint.failOnError())
		.pipe(sass( {
			errLogToConsole: true,
			outputStyle: 'compressed',
			sourcemap: true,
			souceComments: 'normal',
		}))
		.pipe(autoprefixer({
			browsers: [
				'last 3 version',
				'ie 10',
				'Android 4.2'
			]
		}))
		.pipe(cssmin())
		.pipe(gulp.dest('css'));
});


/**
 * default タスク
 */
gulp.task('default', () => {
	gulp.watch('sass/**/*.scss', ['style']);
});