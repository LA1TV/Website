var path = require('path');
var generateConfig = require('../app/assets/webpack/generate-config');

var baseDir = path.resolve(__dirname, "../");
var buildDir = path.resolve(baseDir, 'app/assets/builds/embed');
var entryPointsBaseDir = path.resolve(baseDir, 'app/assets/src/entry/embed');
var publicPath = "/assets/built/embed/";

module.exports = generateConfig(baseDir, buildDir, entryPointsBaseDir, publicPath);