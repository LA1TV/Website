var path = require('path');
var generateConfig = require('../app/assets/webpack/generate-config');

var baseDir = path.resolve(__dirname, "../");
var buildDir = path.resolve(baseDir, 'app/assets/builds/admin');
var entryPointsBaseDir = path.resolve(baseDir, 'app/assets/src/entry/admin');
var publicPath = "/assets/built/admin/";

module.exports = generateConfig(baseDir, buildDir, entryPointsBaseDir, publicPath);