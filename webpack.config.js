const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');


var config = {
    mode: 'production',
    entry: ['./Application/Public/assets/index.js'],
    output: {
        filename: 'index.bundle.js',
        path: path.resolve(__dirname, 'Application/Public/assets/'),
        libraryTarget: 'var',
        library: 'App'
    },
    resolve: {
        alias: {
            utils: path.resolve(__dirname, 'Application/Public/assets/base/js/utils.js')
        }
    },
    module: {
        rules: [
            {
                test: /\.m?js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            }
        ]
    },
    optimization: {
        minimize: false,
        minimizer: [
            new TerserPlugin({
                terserOptions: {
                  output: {
                    comments: false,
                  },
                },
            }),
        ],
    },
    stats: {
        colors: true
    },
    devtool: false
};

module.exports = (env, argv) => {
    if (argv.mode == 'production') {
        config.output.filename = 'index.min.js';
        config.optimization.minimize = true;
        config.devtool = 'source-map';
    }
    return config;
};
