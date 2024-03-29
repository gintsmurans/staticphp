const path = require('path');
const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');
const ESLintPlugin = require('eslint-webpack-plugin');
// const dotenv = require('dotenv');

const config = {
    target: 'browserslist',
    mode: 'production',
    context: path.resolve(__dirname, 'src/Application/Public'),
    entry: {
        defaults: './assets/src/index.ts',
    },
    output: {
        devtoolModuleFilenameTemplate: '[resource-path]?[loaders]',
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'src/Application/Public/assets/dist/js/'),
        libraryTarget: 'var',
        library: '[name]Module',
    },
    resolve: {
        extensions: ['.ts', '.tsx', '.js', '.jsx'],
        alias: {
            base: path.resolve(
                __dirname,
                'src/Application/Public/assets/src/base/ts'
            ),
        },
    },
    module: {
        rules: [
            {
                test: /\.(ts|tsx|js|jsx)$/,
                use: [
                    {
                        loader: 'esbuild-loader',
                        options: {
                            format: 'esm',
                            target: 'ES2022',
                        },
                    },
                ],
                exclude: /node_modules/,
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
    plugins: [new ESLintPlugin()],
    devtool: 'source-map',
    watchOptions: {
        ignored: /node_modules/,
    },
};

module.exports = (env, argv) => {
    // // Load .env file
    // if (argv.mode === 'production') {
    //     dotenv.config({ path: './src/Application/.env.prod' });
    // } else {
    //     dotenv.config({ path: './src/Application/.env' });
    // }
    config.plugins.push(
        new webpack.DefinePlugin({
            APP_ENV: JSON.stringify(argv.mode),
        })
    );

    if (argv.mode === 'production') {
        config.output.filename = '[name].min.js';
        config.optimization.minimize = true;
    }

    return config;
};
