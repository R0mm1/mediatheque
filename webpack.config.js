const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

console.log(path.resolve(__dirname, 'public/js/'));

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
            }
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

module.exports = [
    bookBookConfig
];