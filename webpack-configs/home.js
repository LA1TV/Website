var path = require('path');
var generateConfig = require('../app/assets/webpack/generate-config');

var baseDir = path.resolve(__dirname, "../");
var buildDir = path.resolve(baseDir, 'app/assets/builds/home');
var entryPointsBaseDir = path.resolve(baseDir, 'app/assets/src/entry/home');
var publicPath = "/assets/built/home/";

module.exports = generateConfig(baseDir, buildDir, entryPointsBaseDir, publicPath);