// jshint ignore: start

const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    externals: {
        'lodash': 'lodash'
    },
    entry: {
        questions: ['./assets/js/src/questions.js', './assets/scss/questions.scss'],
        quiz: ['./assets/js/src/quiz.js', './assets/scss/quiz.scss'],
        lesson: ['./assets/js/src/lesson.js', './assets/scss/lesson.scss'],
    },
    output: {
        path: path.resolve(__dirname, './assets'),
        filename: 'js/build/[name].js',
    },
    watch: 'production' === process.env.NODE_ENV ? false : true,
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                },
            },
            {
                test: /\.scss$/,
                use: [ MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    [
                                        "autoprefixer",
                                        {
                                            // Options
                                        },
                                    ],
                                ],
                            },
                        },
                    },
                    {
                        loader: 'sass-loader',
                    }
                ],
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css'
        })
    ]
};
