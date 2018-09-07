var gulp = require("gulp"),
    util = require("gulp-util"),
    sass = require("gulp-sass"),
    autoprefixer = require('gulp-autoprefixer'),
    minifycss = require('gulp-minify-css'),
    rename = require('gulp-rename'),
    log = util.log;

var sassFiles = './assets/css/*.scss';

gulp.task("sass", function () {

    var getDestFilepath = function (sourceFilepath) {
        return './public' + sourceFilepath.split('assets')[1];
    };

    log("Generate CSS files " + (new Date()).toString());
    gulp.src(sassFiles)
        .pipe(sass({style: 'expanded'}))
        .pipe(autoprefixer("last 3 version"))
        .pipe(gulp.dest(function (file) {
            return getDestFilepath(file.base);
        }))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss())
        .pipe(gulp.dest(function (file) {
            return file.base;
        }));
});

gulp.task("watch", function () {
    gulp.watch(sassFiles, ["sass"]);
});

gulp.task('default', ['watch']);