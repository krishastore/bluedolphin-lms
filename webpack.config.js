// jshint ignore: start

const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    resolve: {
        alias: {
            "@countdowntimer": path.resolve(__dirname, 'node_modules/countdowntimer'),
        }
    },
    externals: {
        'lodash': 'lodash',
        "jquery": "jQuery"
    },
    entry: {
        questions: ['./assets/js/src/questions.js', './assets/scss/questions.scss'],
        quiz: ['./assets/js/src/quiz.js', './assets/scss/quiz.scss'],
        lesson: ['./assets/js/src/lesson.js', './assets/scss/lesson.scss'],
        course: ['./assets/js/src/course.js', './assets/scss/course.scss'],
        settings: ['./assets/js/src/settings.js', './assets/scss/settings.scss'],
        frontend: ['./assets/js/src/frontend.js', './assets/scss/frontend/style.scss'],
        plyr: ['./assets/js/src/plyr.js', './assets/scss/plyr.scss'],
        smartwizard: ['./assets/js/src/smartwizard.js', './assets/scss/smartwizard.scss'],
        countdowntimer: ['./assets/js/src/countdowntimer.js'],
        swiper: ['./assets/js/src/swiper.js', './assets/scss/swiper.scss'],
        result: './assets/scss/result.scss',

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