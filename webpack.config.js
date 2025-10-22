const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';

    return {
        entry: {
            'admin': './assets/src/js/admin/index.js',
            'tooltips': './assets/src/js/admin/tooltips.js',
            'admin-styles': './assets/src/scss/admin.scss'
        },
        output: {
            path: path.resolve(__dirname, 'assets/dist'),
            filename: 'js/[name].js',
            clean: true
        },
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env']
                        }
                    }
                },
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'sass-loader'
                    ]
                }
            ]
        },
        plugins: [
            new MiniCssExtractPlugin({
                filename: 'css/[name].css'
            })
        ],
        optimization: {
            minimizer: isProduction ? [
                new TerserPlugin({
                    extractComments: false,
                    terserOptions: {
                        format: {
                            comments: false
                        }
                    }
                }),
                new CssMinimizerPlugin()
            ] : []
        },
        devtool: isProduction ? false : 'source-map',
        externals: {
            jquery: 'jQuery'
        }
    };
};
