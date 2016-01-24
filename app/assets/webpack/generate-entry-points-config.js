var fs = require('fs');
var path = require('path');
var deasync = require('deasync');

// generate entry points
// these are names as the path from the src/entry folder, with "/" replaced with "_"
module.exports = function(entryPointsBase) {
	var result = null;
	walk(entryPointsBase, function(err, results) {
		if (err) {
			error = true;
			throw "Something went wrong generating entry points.";
		}
		var entryPoints = {};
		results.forEach(function(location) {
			var key = location.substring(entryPointsBase.length+1).replace(/\.[^/.]+$/, "").replace(path.sep, "_"); 
			entryPoints[key] = location;
			console.log(key+" => "+location);
		});
		result = entryPoints;
	});

	deasync.loopWhile(function() {
		return !result;
	});
	return result;
};

// http://stackoverflow.com/a/5827895/1048589
function walk(dir, done) {
	var results = [];
	fs.readdir(dir, function(err, list) {
		if (err) return done(err);
		var pending = list.length;
		if (!pending) return done(null, results);
		list.forEach(function(file) {
			file = path.resolve(dir, file);
			fs.stat(file, function(err, stat) {
				if (stat && stat.isDirectory()) {
					walk(file, function(err, res) {
						results = results.concat(res);
						if (!--pending) done(null, results);
					});
				} else {
					results.push(file);
					if (!--pending) done(null, results);
				}
			});
		});
	});
};