const path = require('path');
const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');
const ESLintPlugin = require('eslint-webpack-plugin');


const config = {
    target: 'browserslist',
    mode: 'production',
    context: path.resolve(__dirname, 'src/Application/Public'),
    entry: {
        defaults: './assets/src/index.js',
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'src/Application/Public/assets/dist/js/'),
        libraryTarget: 'var',
        library: '[name]Module',
    },
    resolve: {
        alias: {
            utils: path.resolve(__dirname, 'src/Application/Public/assets/src/base/js/utils.js'),
            customPolyfill: path.resolve(__dirname, 'src/Application/Public/assets/src/base/js/customPolyfill.js'),
        },
    },
    module: {
        rules: [
            {
                test: /\.js*$/,
                use: {
                    loader: 'strip-trailing-space-loader',
                    options: {
                        line_endings: 'unix',
                    },
                },
            },
        ],
    },
    optimization: {
        splitChunks: {
            cacheGroups: {
                commons: {
                    test: /[\\/]node_modules[\\/]|src\/Application\/Public\/assets\/base\/js/,
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
                terserOptions: {
                    output: {
                        ecma: 6,
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
        new ESLintPlugin(),
    ],
    devtool: 'source-map',
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
