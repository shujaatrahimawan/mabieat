const mix = require('laravel-mix');
const WebpackNotifierPlugin = require('webpack-notifier');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');

mix.js('resources/src/main.js', 'public')
   .js('resources/src/login.js', 'public')
   .vue();

mix.webpackConfig({
    output: {
        filename: 'js/[name].min.js',
        chunkFilename: 'js/bundle/[name].[hash].js',
    },
    plugins: [
        new MomentLocalesPlugin(),
        new WebpackNotifierPlugin({ alwaysNotify: true }),
    ]
});

mix.version();
mix.disableNotifications(false);