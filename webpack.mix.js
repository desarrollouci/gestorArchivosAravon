const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
    .webpackConfig({
        module: {
            rules: [{
                test: /\.jsx?$/,
                exclude: /node_modules(?!\/foundation-sites)|bower_components/,
                use: [{
                    loader: 'babel-loader',
                    options: Config.babel()
                }]
            },

            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            },
            {
                test: /\.(png|jpg)$/,
                loader: 'file-loader',
                options: {
                    name: 'images/[name].[ext]'
                }
            },
            {
                test: /\.(woff|woff2|ttf|otf|eot)$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]',
                        outputPath: 'fonts',
                    },
                }, ],
            },

            ]
        },
        resolve: {
            alias: {
                '@': path.resolve('resources/assets/sass')
            }
        },
        resolve: {
            extensions: ['.js', '.vue', '.json'],
            alias: {
                '@': path.resolve(__dirname, '..')
            }
        }
    })
    .js('node_modules/popper.js/dist/popper.js', 'public/js').sourceMaps()
    .styles([
        'public/css/custom.css'
    ], 'public/css/all.css');

