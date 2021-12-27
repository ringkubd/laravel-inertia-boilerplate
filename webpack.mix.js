const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js")
    .vue()
    .postCss("resources/css/app.css", "public/css", [
        require("postcss-import"),
        require("tailwindcss"),
        require("autoprefixer"),
    ])
    .sass('resources/sass/chat.sass', 'public/css')
    .css('resources/css/custom_print.css', 'public/css')
    .css('resources/css/attendance_print.css', 'public/css')
    .webpackConfig(require("./webpack.config"));

if (mix.inProduction()) {
    mix.version();
}
mix.browserSync(process.env.APP_URL);
