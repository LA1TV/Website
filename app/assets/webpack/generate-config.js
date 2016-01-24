var path = require('path');
var Clean = require('clean-webpack-plugin');
var CommonsChunkPlugin = require("webpack/lib/optimize/CommonsChunkPlugin");
var generateEntryPoints = require('./generate-entry-points-config');
//var autoprefixer = require('autoprefixer');

module.exports = function(baseDir, buildDir, entryPointsbaseDirDir, publicPath) {
    // generate entry points
    // these are names as the path from the src/entry folder, with "/" replaced with "_"
    var entryConfig = generateEntryPoints(entryPointsbaseDirDir);
    return {
        plugins: [
            new Clean(buildDir)
        ],
        entry: entryConfig,
        resolve: {
            root: path.resolve(baseDir, 'app/assets/src'),
            alias: {
                jquery: path.resolve(baseDir, 'app/assets/src/lib/jquery.js'),
                Clappr: path.resolve(baseDir, 'app/assets/src/lib/clappr.js')
            }
        },
        module: {
            loaders: [
                {
                    test: /\.js$/,
                    loader: 'babel-loader?compact=true',
                    exclude: /node_modules/
                },
                {
                    test: /\.css$/,
                    loaders: ["style-loader", "css-loader"],
                    exclude: /node_modules/
                },
                {
                    test: /\.(eot|svg|ttf|woff|woff2|png)/, loader: 'file-loader'
                }
                
            ]
        },
        plugins: [
            new CommonsChunkPlugin("commons.chunk.js")
        ],
        /*
        postcss: [
            autoprefixer({browsers: ['last 2 versions']})
        ],
        */
        externals: {
            // should be an object defined in an inline script (form the server)
            "serverData": "LA1TV_GLOBAL.SERVER_DATA"
        },
        output: {
            path: buildDir,
            filename: '[name].js',
            chunkFilename: "[chunkhash].bundle.js",
            publicPath: publicPath
        }
    };
}