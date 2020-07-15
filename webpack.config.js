const path = require('path');
const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');


const config = {
    mode: 'production',
    context: path.resolve(__dirname, './Application/Public'),
    entry: {
        defaults: './assets/index.js',
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'Application/Public/assets/dist/js/'),
        libraryTarget: 'var',
        library: '[name]Module',
    },
    resolve: {
        alias: {
            utils: path.resolve(__dirname, 'Application/Public/assets/base/js/utils.js'),
        },
    },
    module: {
        rules: [
            {
                enforce: 'pre',
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'eslint-loader',
            },
            {
                test: /\.m?js$/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: {
                            presets: [
                                [
                                    '@babel/preset-env',
                                    {
                                        targets: 'last 2 versions, ie >= 10',
                                        modules: false,
                                        useBuiltIns: 'entry',
                                        corejs: 3,
                                    },
                                ],
                            ],
                        },
                    },
                ],
            },
            {
                test: /\.js*$/,
                use: [
                    {
                        loader: 'strip-trailing-space-loader',
                        options: {
                            line_endings: 'unix',
                        },
                    },
                ],
            },
        ],
    },
    optimization: {
        splitChunks: {
            cacheGroups: {
                commons: {
                    test: /[\\/]node_modules[\\/]|Application\/Public\/assets\/base\/js/,
                    name: 'vendors',
                    chunks: 'all',
                },
            },
        },
        usedExports: true,
        sideEffects: true,
        minimize: false,
        minimizer: [
            new TerserPlugin({
                sourceMap: true,
                terserOptions: {
                    output: {
                        comments: false,
                    },
                },
            }),
        ],
    },
    stats: {
        colors: true,
    },
    plugins: [
        new webpack.SourceMapDevToolPlugin({
            filename: '[file].map',
            fallbackModuleFilenameTemplate: '[absolute-resource-path]',
            moduleFilenameTemplate: '[absolute-resource-path]',
        }),
    ],
    devtool: false,
};

module.exports = (env, argv) => {
    config.plugins.push(new webpack.DefinePlugin({
        APP_ENV: JSON.stringify(argv.mode),
    }));

    if (argv.mode === 'production') {
        config.output.filename = '[name].min.js';
        config.optimization.minimize = true;
    }

    return config;
};
