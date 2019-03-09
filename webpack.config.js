const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');


var config = {
    module: {
        rules: [
            {test: /\.vue$/, loader: 'vue-loader'},
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader"
                }
            },
            {test: /\.scss$/, loader: ['vue-style-loader', 'css-loader', 'sass-loader']},
            {test: /\.css$/, use: ['style-loader', 'css-loader']}
        ]
    },
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js'
        },
        extensions: ['*', '.js', '.vue', '.json']
    },
    plugins: [
        new VueLoaderPlugin()
    ]
};

var bookBookConfig = Object.assign({}, config, {
    name: 'bookBook',
    entry: './assets/js/book/book.js',
    output: {
        path: path.resolve(__dirname, 'public/js/book/'),
        filename: 'book.js'
    }
});

var bookAuthorConfig = Object.assign({}, config, {
    name: 'bookAuthor',
    entry: './assets/js/book/author.js',
    output: {
        path: path.resolve(__dirname, 'public/js/book/'),
        filename: 'author.js'
    }
});

var mediathequeHeaderConfig = Object.assign({}, config, {
    name: 'mediathequeHeader',
    entry: './assets/js/mediatheque/header.js',
    output: {
        path: path.resolve(__dirname, 'public/js/mediatheque/'),
        filename: 'header.js'
    }
});

var mediathequeLoginConfig = Object.assign({}, config, {
    name: 'mediathequeLogin',
    entry: './assets/js/mediatheque/login.js',
    output: {
        path: path.resolve(__dirname, 'public/js/mediatheque/'),
        filename: 'login.js'
    }
});

var mediathequeInstallConfig = Object.assign({}, config, {
    name: 'mediathequeInstall',
    entry: './assets/js/mediatheque/install.js',
    output: {
        path: path.resolve(__dirname, 'public/js/mediatheque/'),
        filename: 'install.js'
    }
});

module.exports = [
    bookBookConfig, bookAuthorConfig, mediathequeHeaderConfig, mediathequeLoginConfig, mediathequeInstallConfig
];