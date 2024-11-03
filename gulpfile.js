/**
 * Начиная с этой версии мы используем наиболее отлаженную конфигурацию системы сборки GULP
 * @version FAE '2024-09-24'
 */
'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const browserSync = require('browser-sync');
const concat = require('gulp-concat');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const minify = require('gulp-minify'); // https://github.com/hustxiaoc/gulp-minify/issues/54
const rename = require('gulp-rename');
const googleWebFonts = require('gulp-google-webfonts');
const htmlmin = require('gulp-htmlmin');
const fileinclude = require('gulp-file-include');
const gulpif = require('gulp-if');
const gutil = require('gulp-util');
const path = require("path");
const fs = require("fs");
const uglify = require("gulp-uglify"); //@todo: ВМЕСТО 'gulp-minify', который может не работать почему-то с какими-то файлами
const cssmin = require('gulp-cssmin'); // https://www.npmjs.com/package/gulp-cssmin для минификации CSS, есть альтернатива - https://www.npmjs.com/package/gulp-clean-css (неясно отличие)

const argv = require('minimist')(process.argv.slice(2));

// Configuration
const configuration = {
    is_production: argv.production || 'true',
    public: './public',
    paths: {
        js: {
            src: [
                // './public/frontend/jquery/jquery-3.2.1_min.js',
                './public/frontend/leaflet/leaflet.js',
                './public/frontend/colorbox/jquery.colorbox-min.js',
                './public/frontend/leaflet/L.Icon.FontAwesome.js',
                './public/frontend/leaflet/leaflet.markercluster.js',
                './public/frontend/leaflet/L.Control.Zoomslider.js',
                './public/frontend/jquery/jquery.notifyBar.js',
                './public/frontend/MapBoxes.js',
                './public/frontend/MapActions.js',
                './public/frontend/helper.dataActionRedirect.js',
                './public/frontend/helper.notifyBar.js',
                './public/frontend/index.js',
            ],
            dest: './public/',
        },
        jquery: {
            src: './public/frontend/jquery/jquery-3.2.1_min.js',
            dest: './public/',
        },
        css: {
            src: [
                './public/frontend/leaflet/leaflet.css',
                './public/frontend/colorbox/colorbox.css',
                './public/frontend/leaflet/L.Icon.FontAwesome.css',
                // markers
                './public/frontend/leaflet/MarkerCluster.css',
                './public/frontend/leaflet/MarkerCluster.Default.css',
                './public/frontend/leaflet/L.Control.Zoomslider.css',
                './public/frontend/jquery/jquery.notifyBar.css',
                //
                './public/frontend/styles.css'
            ],
            dest: './public/'
        },
        csstables: {
            src: [
                './public/frontend/jquery/bootstrap.min.css',
                './public/frontend/jquery/dataTables.bootstrap.min.css',
                './public/frontend/jquery/jquery.dataTables.min.css'
            ],
            dest: './public/'
        },
    },
};

gulp.task('js', function () {
    return gulp.src(configuration.paths.js.src, { removeBOM: true, allowEmpty: true })
        .pipe(concat('scripts.js'))
        .pipe(uglify())
        .pipe(gulpif(
            configuration.is_production,
            uglify(),
            rename(function (path) {
                // path.basename = path.basename + '.min';
            } )
        ))
        .pipe(gulp.dest(configuration.paths.js.dest))
});

gulp.task('jquery', function () {
    return gulp.src(configuration.paths.jquery.src)
        .pipe(
            rename(function (path) {
                path.basename = 'jquery.min';
            })
        )
        .pipe(gulp.dest(configuration.paths.jquery.dest))
        ;
});

gulp.task('scss', function (){
    return gulp.src(configuration.paths.css.src)
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(gulpif(
            configuration.is_production,
            cleanCSS()
        ))
        .pipe(
            concat('styles.css')
        )
        .pipe(
            cssmin()
        )
        .pipe(
            gulp.dest(configuration.paths.css.dest)
        )
});

gulp.task('csstables', function (){
    return gulp.src(configuration.paths.csstables.src)
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(gulpif(
            configuration.is_production,
            cleanCSS()
        ))
        .pipe(
            concat('styles_tables.css')
        )
        .pipe(
            cssmin()
        )
        .pipe(
            gulp.dest(configuration.paths.csstables.dest)
        )
});

/**
 * Задача: скачивает гуглошрифты. Использовали его в экспериментальных целях.
 */
gulp.task("download:font:google", () => {
    return gulp.src('./fonts.list')
        .pipe(googleWebFonts(configuration.googleFontsOptions))
        .pipe(gulp.dest(configuration.paths.fonts.dest))
        ;
});

// алиасы для комплексных задач
gulp.task('build', gulp.series('js', 'jquery', 'csstables','scss'));


