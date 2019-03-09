var gulp = require("gulp"),
    util = require("gulp-util"),
    gulpSass = require("gulp-sass"),
    autoprefixer = require('gulp-autoprefixer'),
    minifycss = require('gulp-minify-css'),
    rename = require('gulp-rename'),
    log = util.log;

var sassFiles = './assets/css/*.scss';

function sass() {
    var getDestFilepath = function (sourceFilepath) {
        return './public' + sourceFilepath.split('assets')[1];
    };

    log("Generate CSS files " + (new Date()).toString());
    gulp.src(sassFiles)
        .pipe(gulpSass({style: 'expanded'}))
        .pipe(autoprefixer("last 3 version"))
        .pipe(gulp.dest(function (file) {
            return getDestFilepath(file.base);
        }))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss())
        .pipe(gulp.dest(function (file) {
            return file.base;
        }));
};

function watch(){
    gulp.watch(sassFiles, gulp.series(sass));
}

gulp.task('default', gulp.series(watch, sass));