const {mix} = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.sass('resources/assets/sass/bootstrap.scss', 'public/css').options({
    processCssUrls: false,
    postCss: [
        require('postcss-discard-comments')({
            removeAll: true
        })
    ],
   // purifyCss: true
});

mix.sass('resources/assets/sass/main.scss', 'public/css').options({processCssUrls: false});
mix.sass('resources/assets/sass/admin-main.scss', 'public/css').options({processCssUrls: false});

mix.styles([
    'resources/assets/bower_components/blueimp-gallery/css/blueimp-gallery.css',
    'resources/assets/bower_components/blueimp-file-upload/css/jquery.fileupload.css',
    'resources/assets/bower_components/blueimp-file-upload/css/jquery.fileupload-ui.css',
    'resources/assets/bower_components/jquery-ui/themes/redmond/jquery-ui.css',
    'resources/assets/taghandler/css/jquery.taghandler.css',
    'public/css/font-awesome.css'
], 'public/css/lib.css');

mix.copy('resources/assets/bower_components/jquery-ui/themes/redmond/images/', 'public/css/images');

mix.js('resources/assets/js/app.js', 'public/js');
mix.js('resources/assets/js/admin-app.js', 'public/js');

mix.scripts([
    'resources/assets/js/jquery-1.11.1.min.js',
    'resources/assets/js/jquery-ui.1.10.4.min.js',
    'resources/assets/bower_components/jquery-lazy/jquery.lazy.js',
    'resources/assets/bower_components/bootstrap-sass/assets/javascripts/bootstrap.js',
    'resources/assets/taghandler/js/jquery.taghandler.js',
    'resources/assets/bower_components/jquery-sticky/jquery.sticky.js',
    'resources/assets/js/jquery.blueimp-gallery.js',
    'resources/assets/js/jquery.noconflict.js',
    'resources/assets/js/theme-scripts.js',
    //'resources/assets/js/theme-scripts-tjq.js',
    'resources/assets/js/app.js'
], 'public/js/main.js');

mix.scripts([
    'resources/assets/js/jquery-1.11.1.min.js',
    'resources/assets/js/jquery-ui.1.10.4.min.js',
    'resources/assets/bower_components/bootstrap-sass/assets/javascripts/bootstrap.js',
    'resources/assets/taghandler/js/jquery.taghandler.js',
    'resources/assets/bower_components/jquery-modal/jquery.modal.js',
    'public/ckeditor/ckeditor.js',

    'resources/assets/bower_components/blueimp-tmpl/js/tmpl.js',
    'resources/assets/bower_components/blueimp-load-image/js/load-image.all.min.js',
    'resources/assets/bower_components/blueimp-canvas-to-blob/js/canvas-to-blob.js',
    //'resources/assets/bower_components/blueimp-gallery/js/jquery.blueimp-gallery.js',
    'resources/assets/js/jquery.blueimp-gallery.js',
    'resources/assets/bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
    'resources/assets/bower_components/blueimp-file-upload/js/jquery.iframe-transport.js',
    'resources/assets/bower_components/blueimp-file-upload/js/jquery.fileupload.js',
    'resources/assets/bower_components/blueimp-file-upload/js/jquery.fileupload-process.js',
    'resources/assets/bower_components/blueimp-file-upload/js/jquery.fileupload-image.js',
    'resources/assets/bower_components/blueimp-file-upload/js/jquery.fileupload-audio.js',
    'resources/assets/bower_components/blueimp-file-upload/js/jquery.fileupload-video.js',
    'resources/assets/bower_components/blueimp-file-upload/js/jquery.fileupload-validate.js',
    'resources/assets/bower_components/blueimp-file-upload/js/jquery.fileupload-ui.js',

    'resources/assets/js/jquery.noconflict.js',
    'resources/assets/js/theme-scripts.js',
    //'resources/assets/js/theme-scripts-tjq.js',
    'resources/assets/js/admin-app.js'
], 'public/js/admin-main.js');

mix.scripts([
    'resources/assets/bower_components/semantic-ui-transition/transition.js',
    'resources/assets/bower_components/semantic-ui-dropdown/dropdown.js',
], 'public/js/ui-dropdown.js');

mix.styles([
    'resources/assets/bower_components/semantic-ui-transition/transition.css',
    'resources/assets/bower_components/semantic-ui-dropdown/dropdown.css'
], 'public/css/ui-dropdown.css');

mix.copy('vendor/techlab/smartwizard/src/', 'public/smartwizard');
mix.copy('node_modules/bootstrap-validator/js/validator.js', 'public/js/validator.js');

mix.version();
