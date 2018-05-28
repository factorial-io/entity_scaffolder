/* eslint-disable */

import babel from 'babelify';
import browserify from 'browserify';
import buffer from 'vinyl-buffer';
import connect from 'gulp-connect';
import del from 'del';
import eslint from 'gulp-eslint';
import exit from 'gulp-exit';
import gulp from 'gulp';
import plumber from 'gulp-plumber';
import postcss from 'gulp-postcss';
import source from 'vinyl-source-stream';
import sourcemaps from 'gulp-sourcemaps';
import stylelint from 'gulp-stylelint';
import svgmin from 'gulp-svgmin';
import svgSprite from 'gulp-svg-sprite';
import watchify from 'watchify';

// PostCSS processors

import postcssImport from 'postcss-import';
import postcssUrl from 'postcss-url';
import postcssCustomProperties from 'postcss-custom-properties';
import postcssCalc from 'postcss-calc';
import postcssColorFunction from 'postcss-color-function';
import postcssCustomMedia from 'postcss-custom-media';
import postcssPseudoElements from 'postcss-pseudoelements';
import postcssNesting from 'postcss-nesting';
import autoprefixer from 'autoprefixer';

import path from 'path';

const postcssUrlOptions = [
  {
    // filter: /^..\/flags\/\dx\d\/[a-z\-]*.svg$/,
    filter: /..flags/, // NB: Match against a broad pattern for now.
    url: 'copy',
    basePath: path.resolve('node_modules/flag-icon-css/css'),
    assetsPath: 'images',
    useHash: true,
  },
];

gulp.task('build:css', () => {
  gulp.src('source/index.css')
    .pipe(plumber())
    .pipe(sourcemaps.init({ loadMaps: true }))
    .pipe(postcss([
      postcssImport,
      postcssUrl(postcssUrlOptions),
      postcssCustomProperties,
      postcssCalc,
      postcssColorFunction,
      postcssCustomMedia,
      postcssPseudoElements,
      postcssNesting,
      autoprefixer({
        browsers: ['last 2 versions', 'IE 10']
      }),
    ], {
      from: './source/index.css',
      to: './source/css/index.css',
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('source/css'))
    .pipe(gulp.dest('public/css/'))
    .pipe(connect.reload());
});

function compileJS(flag) {
  const bundler = watchify(browserify('./source/index.js', { debug: true }).transform(babel));

  function rebundle() {
    return bundler
      .bundle()
      .on('error', (err) => {
        console.error(err);
      })
      .pipe(plumber())
      .pipe(source('index.js'))
      .pipe(buffer())
      .pipe(sourcemaps.init({ loadMaps: true }))
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest('./source/js'))
      .pipe(gulp.dest('./public/js'))
      .pipe(connect.reload());
      // .pipe(exit()); // REVIEW
  }

  if (flag) {
    bundler.on('update', (ids) => {
      console.log(`-> bundling... ${ids}`);
      rebundle();
    });

    rebundle();
  } else {
    rebundle().pipe(exit()); // REVIEW
  }
}

gulp.task('build:js', () => compileJS());

gulp.task('lint:css', () => gulp.src([
  './source/*.css',
  './source/_patterns/**/*.css'])
  .pipe(plumber())
  .pipe(stylelint(
    {
      reporters: [
        {
          formatter: 'string',
          console: true,
        },
      ],
    }
  )
));

gulp.task('lint:js', () => gulp.src([
  'source/*.js',
  'source/_patterns/**/*.js',
  'gulpfile.babel.js'])
  .pipe(plumber())
  .pipe(eslint())
  .pipe(eslint.format())
  .pipe(eslint.failAfterError()));

gulp.task('server', () => {
  connect.server({
    root: './public',
    port: 4567,
  });
});

gulp.task('watch:css', () => {
  gulp.watch([
    'source/*.css',
    'source/_patterns/**/*.css',
  ], ['build:css', 'lint:css']);
});

gulp.task('watch:js', () => compileJS(true));

gulp.task('watch:pattern-lab', () => {
  gulp.watch([
    'source/**/*.json',
    'source/**/*.yaml',
    'source/**/*.twig',
  ], ['build:pattern-lab']);
});

gulp.task('watch:lint:js', () => {
  gulp.watch([
    'source/*.js',
    'source/_patterns/**/*.js',
    'gulpfile.babel.js',
  ], ['lint:js']);
});

gulp.task('default', [
  'watch:css',
  'watch:lint:js',
  'watch:js',
  'watch:pattern-lab',
  'server',
  'build:pattern-lab',
]);

gulp.task('svgdel', () =>
  del([
    './source/css/svg/**/*',
    './source/css/svg-sprite/**/*',
  ], {
    force: true,
  })
);

gulp.task('svgmin', () =>
  gulp.src('**/*.svg', { cwd: './source/svg' })
    .pipe(plumber())
    .pipe(svgmin({
      plugins: [
        {
          removeTitle: true,
        },
        {
          removeAttrs: {
            attrs: 'fill',
          },
        },
      ],
    }))
    .on('error', error => { console.log(error); })
    .pipe(gulp.dest('./source/css/svg/'))
);

gulp.task('build:svg-sprite', ['svgmin', 'svgdel'], () => {
  const config = {
    mode: {
      symbol: {
        render: {
          css: {
            template: '.svg-sprite.css',
          },
        },
        prefix: '.Icon--%s',
        dimensions: '%s',
        example: true,
      },
    },
  };

  return gulp.src('**/*.svg', { cwd: './source/css/svg/' })
    .pipe(plumber())
    .pipe(svgSprite(config))
    .on('error', error => { console.log(error); })
    .pipe(gulp.dest('./public/css/svg-sprite/'))
    .pipe(gulp.dest('./source/css/svg-sprite/'));
});

const exec = require('child_process').exec;

gulp.task('build:pattern-lab', (cb) => {
  gulp.src([
    'source/**/*.json',
    'source/**/*.yaml',
    'source/**/*.twig',
  ]).pipe(connect.reload());
  exec('php core/console --generate', (err, stdout, stderr) => {
    console.log(stdout);
    console.log(stderr);
    cb(err);
  });
});
