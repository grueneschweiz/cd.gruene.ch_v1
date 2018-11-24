const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const webpack = require('webpack');


const extractPlugin = new ExtractTextPlugin('./../css/bundle.css');

module.exports = {
    entry: ['./src/Assets/js/app.js'],
    output: {
        path: path.resolve(__dirname, 'webroot/js'),
        filename: 'bundle.js'
    },
    //watch: true,
    module: {
        rules: [
            {
                test: /\.s?css$/,
                use: extractPlugin.extract({
                    use: ['css-loader', 'sass-loader']
                })
            },
            {
                test: /\.(ttf|otf|eot|woff(2)?)(\?[a-z0-9]+)?$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: '../font/',
                            publicPath: '../font/'
                        }
                    }
                ],
            },
            {
                test: /\.(gif|png|jpe?g|svg|webp)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: '../img/',
                            publicPath: '../img/'
                        }
                    }
                ]
            }
        ]
    },
    resolve: {
        alias: {
            'jquery-ui': 'jquery-ui/ui/widgets',
            'jquery-ui-css': 'jquery-ui/../../themes/base'
        }
    },
    plugins: [
        new webpack.ProvidePlugin({
            jQuery: 'jquery',
            $: 'jquery'
        }),
        extractPlugin
    ],
    //devtool: 'eval-source-map'
};